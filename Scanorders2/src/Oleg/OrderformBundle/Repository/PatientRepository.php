<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Oleg\OrderformBundle\Entity\PatientMrn;
use Symfony\Component\Serializer\Exception\LogicException;
use Oleg\OrderformBundle\Entity\Patient;


/**
 * PatientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PatientRepository extends ArrayFieldAbstractRepository
{

    //patient is a patient object found in DB
    //original is a patient object provided by submitted form
    public function setResult( $patient, $orderinfo = null, $original=null ) {
              
        $em = $this->_em;

        echo "patient id=".$patient->getId()."<br>";

        $em->persist($patient);

        if( $orderinfo == null ) {
            return $patient;
        }

        $part = $patient->getProcedure()->first()->getAccession()->first()->getPart()->first();
        $block = $patient->getProcedure()->first()->getAccession()->first()->getPart()->first()->getBlock()->first();
        echo "@@@@@ part name partname=".$part->getPartname()->first()."<br>";
        echo "@@@@@ part name provider=".$part->getPartname()->first()->getProvider()."<br>";
        echo "@@@@@ part name validity=".$part->getPartname()->first()->getValidity()."<br>";
        echo "@@@@@ block name=".$block->getName()."<br>";

        $patient = $this->processFieldArrays($patient,$orderinfo,$original);
        //echo "patient after mrn provider=".$patient->getMrn()->first()->getProvider()."<br>";

        $procedures = $patient->getProcedure();
        //echo "procedure count in patient=".count($procedures)."<br>";
        //echo "0 patient->procedures count=".count($patient->getProcedure())."<br>";
        foreach( $procedures as $procedure ) {   
                            
            if( $em->getRepository('OlegOrderformBundle:Procedure')->notExists($procedure, "Procedure") ) {     //procedure new
                $patient->removeProcedure( $procedure );
                //echo "procedure0: ".$procedure."<br>";
                $procedure = $em->getRepository('OlegOrderformBundle:Procedure')->processEntityProcedure( $procedure, $procedure->getAccession(), $orderinfo );
                //echo "procedure1: ".$procedure."<br>";
                $patient->addProcedure($procedure);
                $orderinfo->addProcedure($procedure);
            } else {         //procedure from DB     
                //echo "procedure from DB continue id=".$procedure->getId()."<br>";
                continue;
            }
            
        }

//        echo "patient=".$patient."<br>";
        //echo "count mrn=".count($patient->getMrn())."<br>";
//        echo "patient id=".$patient->getId()."<br>";
//        echo "<br>patient mrn=".$patient->getMrn()->first()."<br>";
        //echo "patient mrn provider=".$patient->getMrn()->first()->getProvider()."<br>";
        //echo "patient mrn validity=".$patient->getMrn()->first()->getValidity()."<br>";
        //echo "original mrn provider=".$original->getMrn()->first()->getProvider()."<br>";
//        echo "patient name count=".count($patient->getName())."<br>";
//        echo "patient name=".$patient->getName()->first()."<br>";
//        echo "patient sex count=".count($patient->getSex())."<br>";
//        echo "patient sex=".$patient->getSex()->first()."<br>";
//        echo "patient dob=".$patient->getDob()->first()."<br>";
//        echo "patient age=".$patient->getAge()->first()."<br>";
//        echo "patient age=".$patient->getAge()->first()."<br>";
//        echo "patient clinHist=".$patient->getClinicalHistory()->first()."<br>";
//        echo $patient."<br>";
        //echo "1 patient->procedures count=".count($patient->getProcedure())."<br>";
        //exit();

        return $patient;
    }

    //filter out duplicate virtual (in form, not in DB) patients
    //after js check form, theoretically we should not have duplicate entities submitted by the form, but let's have it just in case ...
    public function removeDuplicateEntities( $entity ) {

        $patients = $entity->getPatient();

        if( count($patients) == 1 ) {
            return $entity;
        }

        $mrns = array();

        foreach( $patients as $patient ) {

//            foreach( $patient->getClinicalHistory() as $hist ) {
//                echo "hist id=".$hist->getId()."<br>";
//            }

            $mrn = $patient->getMrn();

            if( $mrn != null && $mrn != "" ) {
                if( count($mrns) == 0 || !in_array($mrn, $mrns) ) {
                    $mrns[] = $mrn;
                    //persist the rest of entities, because they will be added to DB.
                    $em = $this->_em;
                    $em->persist($patient);
                } else {
                    //echo "remove pat:".$patient;
                    $entity->removePatient($patient);
                }
            }
        }

        return $entity;
    }
    
}
