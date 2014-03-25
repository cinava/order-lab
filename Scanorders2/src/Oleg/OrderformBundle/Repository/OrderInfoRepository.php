<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Oleg\OrderformBundle\Helper\OrderUtil;
use Oleg\OrderformBundle\Entity\History;

/**
 * OrderInfoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderInfoRepository extends ArrayFieldAbstractRepository {
    
    //process orderinfo and all entities
    public function processOrderInfoEntity( $entity, $type=null ) {

        //echo "orderinfo: ".$entity."<br>";
//        echo "patients count=".count($entity->getPatient())."<br>";
//        $this->printTree( $entity->getPatient()->first() );

//        foreach( $entity->getPatient() as $patient ) {
//            echo "patient id=".$patient->getId()."<br>";
//            echo "patient mrn=".$patient->getMrn()->first()."<br>";
//            echo "patient name=".$patient->getName()->first()."<br>";
//            echo "patient oredreinfo count=".count($patient->getOrderinfo())."<br>";
//            echo "patient slide=".$patient->getProcedure()->first()->getAccession()->first()->getPart()->first()->getBlock()->first()->getSlide()->first()."<br>";
//              echo "patient accessions count =".count($patient->getProcedure()->first()->getAccession())."<br>";
//              echo "patient accession=".$patient->getProcedure()->first()->getAccession()->first()."<br>";
//
//            echo "patient count age=".count($patient->getAge())."<br>";
//            foreach( $patient->getAge() as $age ) {
//                echo "age: id=".$age->getId().", field=".$age."<br>";
//            }
//            echo "patient count clinicalHistory=".count($patient->getClinicalHistory())."<br>";
//            foreach( $patient->getClinicalHistory() as $ch ) {
//                echo "ch: id=".$ch->getId().", field=".$ch."<br>";
//            }
//        }
//        exit();

        $em = $this->_em;


        //one way to solve multi duplicate entities to filter the similar entities. But for complex entities such as Specimen or Block it is not easy to filter duplicates out.
        //$entity = $em->getRepository('OlegOrderformBundle:Patient')->removeDuplicateEntities( $entity );

        //set Status with Type and Group
        //$status = $em->getRepository('OlegOrderformBundle:Status')->findOneByAction('Submit');
        //$entity->setStatus($status);

        if( $type ) {

            $formtype = $em->getRepository('OlegOrderformBundle:FormType')->findOneByName( $type );

//            echo "formtype=".$formtype."<br>";
//            exit();

            $entity->setType($formtype);
        }
        
        if( $entity->getPriority() == "Routine" ) {      
            $entity->setScandeadline(NULL);
        }

        //return $entity;
        return $this->setOrderInfoResult( $entity );
    }
    
    public function setOrderInfoResult( $entity ) {

        $em = $this->_em;

        $patients = $entity->getPatient();
        //echo $patients->first();

//        echo "dataquality=".count($entity->getDataquality())."<br>";

        //process data quality
        foreach( $entity->getDataquality() as $dataquality) {

            //set correct mrntype
            $mrntype = $em->getRepository('OlegOrderformBundle:MrnType')->findOneById( $dataquality->getMrntype() );
            $dataquality->setMrntype($mrntype);

            //set correct accessiontype
            $accessiontype = $em->getRepository('OlegOrderformBundle:AccessionType')->findOneById( $dataquality->getAccessiontype() );
            $dataquality->setAccessiontype($accessiontype);

            $dataquality->setOrderinfo($entity);
            $dataquality->setProvider($entity->getProvider()->first());
            $dataquality->setStatus('active');

//            echo "dataquality: description=".$dataquality->getDescription()."<br>";
//            echo "dataquality: accession=".$dataquality->getAccession()."<br>";
//            echo "dataquality: accessionType=".$dataquality->getAccessiontype()."<br>";
//            echo "dataquality: mrn=".$dataquality->getMrn()."<br>";
//            echo "dataquality: mrn text=".$dataquality->getMrntype()."<br>";

        } //foreach

        //echo "patients count=".count($patients)."<br>";
//        echo "dataquality count=".count($entity->getDataquality())."<br>";
//        if( count($entity->getDataquality()) > 0 ) {
//            echo "dataquality: description=".$entity->getDataquality()->first()->getDescription()."<br>";
//            echo "dataquality: accession=".$entity->getDataquality()->first()->getAccession()."<br>";
//            echo "dataquality: mrn=".$entity->getDataquality()->first()->getMrn()."<br>";
//            echo "dataquality: mrn text=".$entity->getDataquality()->first()->getMrntype()."<br>";
//        }

        foreach( $patients as $patient ) {
            //echo "before patient oredreinfo count=".count($patient->getOrderinfo())."<br>";
            $entity->removePatient($patient);
            $patient = $em->getRepository('OlegOrderformBundle:Patient')->processEntity( $patient, $entity, "Patient", "mrn", "Procedure" );
            $entity->addPatient($patient);
        }

        //add slide's parnets recursevely to this orderinfo
        $slides = $entity->getSlide();
        foreach( $slides as $slide ) {
            $this->addOrderinfoToThisAndAllParents( $slide, $entity );
        }

        //********** take care of educational and research director and principal investigator ***********//
        if( $entity->getEducational() ) {
            $em->getRepository('OlegOrderformBundle:Educational')->processEntity( $entity->getEducational() );
        }

        if( $entity->getResearch() ) {
            $em->getRepository('OlegOrderformBundle:Research')->processEntity( $entity->getResearch() );
        }
        //********** end of educational and research processing ***********//

        //echo "<br><br>final patients count=".count($entity->getPatient())."<br>";
        //foreach( $entity->getPatient() as $patient ) {
//            echo 'patient nameCount='.count($patient->getName())." :".$patient->getName()->first().", status=".$patient->getName()->first()->getStatus()."<br>";
//////            echo 'patient orderinfo count='.count($patient->getOrderinfo())."<br>";
//////            //echo 'patient orderinfo='.$patient->getOrderinfo()->first()->getId()."<br>";
//            echo 'orderinfo patient ='.$entity->getPatient()->first()->getName()->first()."<br>";
            //echo $patient;
////            echo "patient accessions count =".count($patient->getProcedure()->first()->getAccession())."<br>";
////            echo "patient parts count =".count($patient->getProcedure()->first()->getAccession()->first()->getPart())."<br>";
////            //echo "patient accession=".$patient->getProcedure()->first()->getAccession()->first()."<br>";
//            echo "<br>--------------------------<br>";
//            $this->printTree( $patient );
//            echo "--------------------------<br>";
        //}

        //echo $entity;
//        echo "<br>Accession count=".count($patient->getProcedure()->first()->getAccession())."<br>";
//        $acc = $patient->getProcedure()->first()->getAccession()->first();
//        echo "number=".$acc->obtainValidKeyField()."<br>";
//        echo "original=".$acc->obtainValidKeyField()->getOriginal()."<br>";
//        echo "keytype=".$acc->obtainValidKeyField()->getKeytype()."<br>";
//
//        echo "<br>Patient count=".count($patients)."<br>";
//        echo "number=".$patient->obtainValidKeyField()."<br>";
//        echo "original=".$patient->obtainValidKeyField()->getOriginal()."<br>";
//        echo "keytype=".$patient->obtainValidKeyField()->getKeytype()."<br>";
//
        //exit('orderinfo repo exit');

        $originalStatus = $entity->getStatus();
        //echo "status=".$originalStatus."<br>";
        //exit();

        if( $originalStatus == 'Not Submitted' ) {
            $entity->setOid(null);
        }

        if( $originalStatus == 'Amended' ) {

            $originalId = $entity->getOid();
            //echo "originalId=".$originalId."<br>";
            
//            $newOrderinfo = clone $entity;
//            $newOrderinfo->setId(null);
//            $newOrderinfo->setOid($originalId);
//            $entity = $newOrderinfo;

            $entity->setId(null);
            $entity->setOid($originalId);
            
//            $orderUtil = new OrderUtil($em);
//            $message = $orderUtil->changeStatus($originalId, 'Amend');
            
            //$entity->setId(null);
                      
//            echo "orig id=".$entity->getId().",:".$entity."<br>";
//            echo "newOrderinfo id=".$entity->getId().",:".$entity."<br>";
//
//            echo "<br><br>final patients count=".count($entity->getPatient())."<br>";
//            echo "<br>--------------------------<br>";
//            $this->printTree( $entity->getPatient()->first() );
//            echo "--------------------------<br>";
            //exit('orderinfo repo exit');

        }

//        echo $entity;
//        //echo "proxy user=".$entity->getProxyuser()->first()."<br>";
//        echo "<br><br>final patients count=".count($entity->getPatient())."<br>";
//        echo $entity->getPatient()->first();
//        echo "<br>--------------------------<br>";
//        $this->printTree( $entity->getPatient()->first() );
//        echo "--------------------------<br>";

//        echo "<br><br>final slide count=".count($entity->getSlide())."<br>";
//        foreach( $entity->getSlide() as $elem ) {
//            echo $elem;
//        }

//        echo "patients=".count($entity->getPatient())."<br>";
//        echo "procedures=".count($entity->getProcedure())."<br>";
//        echo "accessions=".count($entity->getAccession())."<br>";
//        echo "parts=".count($entity->getPart())."<br>";
//        echo "blocks=".count($entity->getBlock())."<br>";
//        echo "slides=".count($entity->getSlide())."<br>";
        //exit('orderinfo repo exit');

        //create new orderinfo
        $em = $this->_em;
        $em->persist($entity);
        $em->flush();

        //insert oid to entity
        if( !$entity->getOid() ) {
            //echo "insert oid <br>";
            $entity->setOid($entity->getId());
            $em->flush();
        }

        //clean empty blocks
        $blocks = $entity->getBlock();
        foreach( $blocks as $block ) {
            if( count($block->getSlide()) == 0 ) {
                //echo "final remove block from orderinfo: ".$block;
                $em->remove($block);
                $em->flush();
            }
        }

        //final step for amend: swap newly created oid with Superseded order oid
        if( $originalStatus == 'Amended' ) {

            //exit('amended orderinfo repo exit');
            $newId = $entity->getId();

            //echo "originalId=".$originalId.", newId=".$newId."<br>";

//            $em->detach($entity);

            $orderUtil = new OrderUtil($em);
            $message = $orderUtil->changeStatus($originalId, 'Supersede', $entity->getProvider()->first(), $newId);

//            $entity = $em->getRepository('OlegOrderformBundle:OrderInfo')->findOneById($newId);
//            if( !$entity ) {
//                throw new \Exception( 'Unable to find OrderInfo entity by id'.$newId );
//            }
            $entity->setOid($originalId);   //swap oid

            //$em->persist($entity);
            $em->flush();
        }

        //*********** record history ***********//
        $history = new History();
        $history->setOrderinfo($entity);
        $history->setCurrentid($entity->getOid());
        $history->setCurrentstatus($entity->getStatus());
        $history->setProvider($entity->getProvider()->first());
        $history->setRoles($entity->getProvider()->first()->getRoles());
        $history->setCurrentstatus($entity->getStatus());

        if( $originalStatus == 'Amended' ) {
            $history->setEventtype('Amended Order Submission');
            $history->setNote('Previous order content saved as a Superseded ###'.$originalId.'###');
        } elseif( $entity->getStatus() == 'Not Submitted' ) {
            $systemUser = $this->em->getRepository('OlegOrderformBundle:User')->findOneByUsername('system');
            $history->setProvider( $systemUser );
            $history->setNote('Auto-Saved Draft; Submit this order to Process');
            $history->setEventtype('Auto-Saved Draft');
        } else {
            $history->setEventtype('Initial Order Submission');
        }

        $em->persist($history);
        $em->flush();
        //*********** EOF record history ***********//

        $em->clear();

        return $entity;
    }


    public function addOrderinfoToThisAndAllParents( $entity, $orderinfo ) {
        $className = new \ReflectionClass($entity);
        $shortClassName = $className->getShortName();
        $addClassMethod = "add".$shortClassName;    //"addPatient"

        if( $shortClassName == 'Patient' ) {    //don't add patient because it was added at the beginning on the controller
            return false;
        }

        //echo '<br>adding '.$shortClassName."<br>";

        //add if this object does not have yet this orderinfo (id=null)
        $orders = $entity->getOrderinfo();
        //echo "orders=".count($orders)."<br>";

        //if at least one order of this object does not have id => new order => this order => return true
        $thisOrderinfoCount = 0;
        foreach( $orders as $order ) {
            //echo "order id=".$order->getId()."<br>";
            if( $order->getId() && $order->getId() != '' ) {
                //echo "object has orderinfo with ID <br>";
            } else {
                $thisOrderinfoCount++;
                //echo "order no ID !!!!!!!!!!!!!!!!!!!!!!!!!!!!<br>";
            }
        }

        //echo $shortClassName.': thisOrderinfoCount='.$thisOrderinfoCount.'<br>';
        if( $thisOrderinfoCount == 0 ) {
            $orderinfo->$addClassMethod($entity);
        }

        $parent = $entity->getParent();
        //echo "parent = ".$parent;
        if( $parent ) {
            $this->addOrderinfoToThisAndAllParents( $parent, $orderinfo );
        }
    }

}
