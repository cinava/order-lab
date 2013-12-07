<?php

namespace Oleg\OrderformBundle\Repository;


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
    public function setResult_OLD( $patient, $orderinfo = null, $original=null ) {
              
        $em = $this->_em;

        echo "patient id=".$patient->getId()."<br>";

        $em->persist($patient);

        if( $orderinfo == null ) {
            return $patient;
        }

        echo "patient=".$patient."<br>";
        echo "count mrn=".count($patient->getMrn())."<br>";
        echo "patient id=".$patient->getId()."<br>";
        echo "<br>patient mrn=".$patient->getMrn()->first()."<br>";
        echo "patient mrn provider=".$patient->getMrn()->first()->getProvider()."<br>";
        echo "patient mrn validity=".$patient->getMrn()->first()->getValidity()."<br>";
        echo "patient mrntype=".$patient->getMrn()->first()->getMrntype()."<br>";
        echo "patient mrn id=".$patient->getMrn()->first()->getId()."<br>";
        echo "@@@@@ procedure count=".count($patient->getProcedure())."<br>";
        foreach( $patient->getProcedure() as $proc ) {
            $part = $proc->getAccession()->first()->getPart()->first();
            $block = $proc->getAccession()->first()->getPart()->first()->getBlock()->first();
            echo "------------------<br>";
            echo "@@@@@ accession count=".count($proc->getAccession())."<br>";
            echo "@@@@@ accession=".$proc->getAccession()->first()."<br>";
            echo "@@@@@ part count=".count($proc->getAccession()->first()->getPart())."<br>";
            echo "@@@@@ block count=".count($proc->getAccession()->first()->getPart()->first()->getBlock())."<br>";
            echo "@@@@@ part name partname=".$part->getPartname()->first()."<br>";
            //echo "@@@@@ part name provider=".$part->getPartname()->first()->getProvider()."<br>";
            //echo "@@@@@ part name validity=".$part->getPartname()->first()->getValidity()."<br>";
            echo "@@@@@ block name=".$block->getBlockname()->first()."<br>";
        }
        echo "------------------<br>";
        exit();


        $patient = $this->processFieldArrays($patient,$orderinfo,$original);
        //echo "patient after mrn provider=".$patient->getMrn()->first()->getProvider()."<br>";

        $procedures = $patient->getProcedure();
        //echo "procedure count in patient=".count($procedures)."<br>";
        echo "0 patient->procedures count=".count($procedures)."<br>";
        foreach( $procedures as $procedure ) {
            echo $procedure;
        }

        foreach( $procedures as $procedure ) {   
                            
//            if( $em->getRepository('OlegOrderformBundle:Procedure')->notExists($procedure, "Procedure") ) {     //procedure new
            if(1) {
                echo "before process procedure <br>";
                $patient->removeProcedure( $procedure );
                //echo "procedure0: ".$procedure."<br>";
                //$procedure = $em->getRepository('OlegOrderformBundle:Procedure')->processEntityProcedure( $procedure, $procedure->getAccession(), $orderinfo );
                $procedure = $em->getRepository('OlegOrderformBundle:Procedure')->processEntity( $procedure, $orderinfo, "Procedure", "encounter", "Accession" );
                //echo "procedure1: ".$procedure."<br>";
                $patient->addProcedure($procedure);
                $orderinfo->addProcedure($procedure);
            } else {         //procedure from DB     
                echo "procedure from DB continue id=".$procedure->getId()."<br>";
                continue;
            }
            
        }

        //echo "patient=".$patient."<br>";
//        echo "count mrn=".count($patient->getMrn())."<br>";
//        echo "patient id=".$patient->getId()."<br>";
//        echo "<br>patient mrn=".$patient->getMrn()->first()."<br>";
//        echo "patient mrn provider=".$patient->getMrn()->first()->getProvider()."<br>";
//        echo "patient mrn validity=".$patient->getMrn()->first()->getValidity()."<br>";
//        echo "patient mrntype=".$patient->getMrn()->first()->getMrntype()."<br>";
//        echo "patient mrn id=".$patient->getMrn()->first()->getId()."<br>";
//        echo "original mrn provider=".$original->getMrn()->first()->getProvider()."<br>";
//        echo "patient name count=".count($patient->getName())."<br>";
//        echo "patient name=".$patient->getName()->first()."<br>";
//        echo "patient sex count=".count($patient->getSex())."<br>";
//        echo "patient sex=".$patient->getSex()->first()."<br>";
//        echo "patient dob=".$patient->getDob()->first()."<br>";
//        echo "patient age=".$patient->getAge()->first()."<br>";
//        echo "patient age=".$patient->getAge()->first()."<br>";
//        echo "patient clinHist=".$patient->getClinicalHistory()->first()."<br>";
//        echo $patient."<br>";
        echo "1 patient->procedures count=".count($patient->getProcedure()).": ".$patient->getProcedure()->first()."<br>";
        //exit();

        return $patient;
    }

    public function getExtraEntityById( $id ) {
        $em = $this->_em;
        return $em->getRepository('OlegOrderformBundle:MrnType')->findOneById($id);
    }


//    public function findOnePatientByIdJoinedToField( $mrn, $mrntype, $validity=null ) {
//        $onlyValid = "";
//        if( $validity ) {
//            //echo "Part check validity ";
//            $onlyValid = " AND pmrn.validity=1";
//        }
//
//        $query = $this->getEntityManager()
//            ->createQuery('
//            SELECT p FROM OlegOrderformBundle:Patient p
//            JOIN p.mrn pmrn
//            WHERE pmrn.field = :mrn AND pmrn.mrntype = :mrntype'.$onlyValid
//            )->setParameter('mrn', $mrn."")->setParameter('mrntype', $mrntype."");
//
//        $parts = $query->getResult();
//
//        if( $parts ) {
//            //echo "parts count=".count($parts)."<br>";
//            return $parts[0];
//        } else {
//            return null;
//        }
//    }

//    public function findNextMrnByMrntype( $mrntype ) {
//        if( !$mrntype || $mrntype == "" ) {
//            return null;
//        }
//
//        //echo "findNextPartnameByAccession: accessionNumber=".$accessionNumber."<br>";
//        $name = "NOMRNPROVIDED";
//
//        $query = $this->getEntityManager()
//            ->createQuery('
//            SELECT MAX(pmrn.field) as max'.'mrn'.' FROM OlegOrderformBundle:Patient p
//            JOIN p.mrn pmrn
//            WHERE ppartname.field LIKE :name AND aa.field = :accession'
//            )->setParameter('name', '%'.$name.'%')->setParameter('accession', $accessionNumber."");
//
//        $lastField = $query->getSingleResult();
//        $index = 'max'.'partname';
//        $lastFieldStr = $lastField[$index];
//        //echo "lastFieldStr=".$lastFieldStr."<br>";
//
//        return $this->getNextByMax($lastFieldStr, $name);
//    }


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
