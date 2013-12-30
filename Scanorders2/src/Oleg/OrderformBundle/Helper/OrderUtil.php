<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 10/4/13
 * Time: 12:56 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\OrderformBundle\Helper;


//use Oleg\OrderformBundle\Entity\OrderInfo;
//use Doctrine\Common\Collections\ArrayCollection;
use Oleg\OrderformBundle\Controller\MultyScanOrderController;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Oleg\OrderformBundle\Entity\History;

class OrderUtil {

    private $em;

    public function __construct( $em ) {
        $this->em = $em;
    }

    public function changeStatus( $id, $status ) {

        $em = $this->em;

        $entity = $em->getRepository('OlegOrderformBundle:OrderInfo')->findOneByOid($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderInfo entity.');
        }

        //check if user permission

        //$editForm = $this->createForm(new OrderInfoType(), $entity);
        //$deleteForm = $this->createDeleteForm($id);

        //$entity->setStatus($status);
        //echo "status=".$status."<br>";
        $status_entity = $em->getRepository('OlegOrderformBundle:Status')->findOneByAction($status);
        //echo "status_entity=".$status_entity->getName()."<br>";
        //exit();

        if( $status_entity ) {

            //record history
            $history = new History();
            $history->setCurrentid($entity->getOid());
            $history->setCurrentstatus($entity->getStatus());
            $history->setCurrentcicle($entity->getCicle());
            $history->setProvider($entity->getProvider()->first());

            //change status for all orderinfo children to "deleted-by-canceled-order"
            //IF their source is ="scanorder" AND there are no child objects with status == 'valid'
            //AND there are no fields that belong to this object that were added by another order
            if( $status == 'Cancel' ) {

                $fieldStatusStr = "deleted-by-canceled-order";
                $entity->setStatus($status_entity);
                $message = $this->processObjects( $entity, $status_entity, $fieldStatusStr );
                $entity->setOid($entity->getId()."-c");
                $entity->setCicle("superseded");

                //record history
                $history->setNewid($entity->getOid());
                $history->setNewstatus($entity->getStatus());
                $history->setNewcicle($entity->getCicle());

                $em->persist($entity);
                $em->persist($history);
                $em->flush();
                $em->clear();

            } else if( $status == 'Amend' ) {

                $fieldStatusStr = "deleted-by-amended-order";
                $entity->setStatus($status_entity);
                $message = $this->processObjects( $entity, $status_entity, $fieldStatusStr );
                $entity->setOid($entity->getId()."-a");
                $entity->setCicle("superseded");

                //record history
                $history->setNewid($entity->getOid());
                $history->setNewstatus($entity->getStatus());
                $history->setNewcicle($entity->getCicle());

                $em->persist($entity);
                $em->persist($history);
                $em->flush();
                //$em->clear();

            } else if( $status == 'Submit' ) {

                $statusStr = "valid";

                //1) clone orderinfo object
                //2) validate MRN-Accession
                //3) change status to 'valid' and 'submit'

//                echo "<br><br>newOrderinfo Patient's count=".count($newOrderinfo->getPatient())."<br>";
//                echo $newOrderinfo;
//                foreach( $newOrderinfo->getPatient() as $patient ) {
//                    echo "<br>--------------------------<br>";
//                    $em->getRepository('OlegOrderformBundle:OrderInfo')->printTree( $patient );
//                    echo "--------------------------<br>";
//                }

                //VALIDATION Accession-MRN
                foreach( $entity->getAccession() as $accession ) {
                    $patient = $accession->getParent()->getParent();

                    $patientKey = $patient->obtainValidKeyField();
                    if( !$patientKey ) {
                        throw new \Exception( 'Object does not have a valid key field. Object: '.$patient );
                    }

                    $accessionKey = $accession->obtainValidKeyField();
                    if( !$accessionKey ) {
                        throw new \Exception( 'Object does not have a valid key field. Object: '.$accession );
                    }

                    //echo "accessionKey=".$accessionKey."<br>";
                    $accessionDb = $em->getRepository('OlegOrderformBundle:Accession')->findOneByIdJoinedToField($accessionKey,"Accession","accession",true, true);

                    $mrn = $patientKey; //mrn
                    $mrnTypeId = $patientKey->getMrntype()->getId();
                    //$extra = $patientKey->obtainExtraKey();

                    if( $accessionDb ) {
                        //echo "similar accession found=".$accessionDb;
                        $patientDb = $accessionDb->getParent()->getParent();
                        if( $patientDb ) {
                            $mrnDb = $patientDb->obtainValidKeyField();
                            $mrnTypeIdDb = $mrnDb->getMrntype()->getId();

                            //echo $mrn . "?=". $mrnDb ." && ". $mrnTypeId . "==". $mrnTypeIdDb . "<br>";

                            if( $mrn == $mrnDb && $mrnTypeId == $mrnTypeIdDb ) {
                                //ok
                                //echo "no conflict <br>";
                            } else {
                                //echo "there is a conflict <br>";
                                //conflict => render the orderinfo in the amend view 'order_amend'
                                //exit('un-canceling order. id='.$newOrderinfo->getOid());

                                $res = array();
                                $res['result'] = 'conflict';
                                $res['oid'] = $entity->getOid();

                                return $res;

                            }
                        }
                    }

                }

                //CLONNING the orderinfo
                $res = $this->makeOrderInfoClone( $entity, $status_entity, $statusStr );

                $message = $res['message'];
                $newOrderinfo = $res['orderinfo'];

                //record new history
                $history->setNewid($newOrderinfo->getOid());
                $history->setNewstatus($newOrderinfo->getStatus());
                $history->setNewcicle($newOrderinfo->getCicle());
                $em->persist($history);

                $newOrderinfo = $em->getRepository('OlegOrderformBundle:OrderInfo')->processOrderInfoEntity( $newOrderinfo, null, "noform" );

            } else {

                throw new \Exception( 'Status '.$status.' can not be processed' );

            }

            $message = 'Status of Order #'.$id.' has been changed to "'.$status.'"'.$message;

        } else {
            $message = 'Status: "'.$status.'" is not found';
        }

        $res = array();
        $res['result'] = 'ok';
        $res['message'] = $message;

        return $res;
    }

    public function makeOrderInfoClone( $entity, $status_entity, $statusStr ) {

        $em = $this->em;

        if( !$status_entity  ) {
            $status_entity = $em->getRepository('OlegOrderformBundle:Status')->findOneByAction($statusStr);
        }

        //CLONING
        $oid = $entity->getOid();
        $oidArr = explode("-", $oid);
        $originalId = $oidArr[0];

        $newOrderinfo = clone $entity;

        $em->detach($entity);
        $em->detach($newOrderinfo);

        $newOrderinfo->setStatus($status_entity);
        $newOrderinfo->setCicle('submit');
        $newOrderinfo->setOid($originalId);

        //$newOrderinfo = $this->iterateOrderInfo( $newOrderinfo, $statusStr );

        //change status to valid
        $message = $this->processObjects( $newOrderinfo, $status_entity, $statusStr );

        $res = array();
        $res['message'] = $message;
        $res['orderinfo'] = $newOrderinfo;

        return $res;
    }

    public function processObjects( $entity, $status_entity, $statusStr ) {

        $patients = $entity->getPatient();
        $patCount = $this->iterateEntity( $entity, $patients, $status_entity, $statusStr );

        $procedures = $entity->getProcedure();
        $procCount = $this->iterateEntity( $entity, $procedures, $status_entity, $statusStr );

        $accessions = $entity->getAccession();
        $accCount = $this->iterateEntity( $entity, $accessions, $status_entity, $statusStr );

        $parts = $entity->getPart();
        $partCount = $this->iterateEntity( $entity, $parts, $status_entity, $statusStr );

        $blocks = $entity->getBlock();
        $blockCount = $this->iterateEntity( $entity, $blocks, $status_entity, $statusStr );

        $slides = $entity->getSlide();
        $slideCount = $this->iterateEntity( $entity, $slides, $status_entity, $statusStr );

        return " (changed children: patients ".$patCount.", procedures ".$procCount.", accessions ".$accCount.", parts ".$partCount.", blocks ".$blockCount." slides ".$slideCount.")";
    }

    public function iterateEntity( $orderinfo, $children, $status_entity, $statusStr ) {

        $em = $this->em;

        if( !$children->first() ) {
            return 0;
        }

        //echo "iterate children count=".count($children)."<br>";

        $class = new \ReflectionClass($children->first());
        $className = $class->getShortName();
        //echo "class name=".$className."<br>";

        $count = 0;

        foreach( $children as $child ) {

            $noOtherOrderinfo = true;

            //echo "orderinfo count=".count($child->getOrderinfo()).", order id=".$child->getOrderinfo()->first()->getId()."<br>";

            if( $statusStr != 'valid' ) {
                //check if this object is used by another orderinfo (for cancel and amend only)
                foreach( $child->getOrderinfo() as $order ) {
                    //echo "orderinfo id=".$order->getId().", oid=".$order->getOid()."<br>";
                    if( $orderinfo->getId() != $order->getId() && $order->getStatus()->getId() != $status_entity->getId()  ) {
                        $noOtherOrderinfo = false;
                        break;
                    }
                }
            }

            //echo "noOtherOrderinfo=".$noOtherOrderinfo."<br>";

            if( $child->getSource() == 'scanorder' && $noOtherOrderinfo ) {
                //echo "change status to (".$statusStr.") <br>";
                $child->setStatus($statusStr);
                $em->getRepository('OlegOrderformBundle:'.$className)->processFieldArrays($child,null,null,$statusStr);
                $count++;
            }

        }

        return $count;
    }

}