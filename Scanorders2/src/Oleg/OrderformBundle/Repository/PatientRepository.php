<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Exception\LogicException;

/**
 * PatientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PatientRepository extends EntityRepository
{
    //make sure the uniqueness entity. Make new or return id of existing.
    public function processEntity( $in_entity, $orderinfo = null ) {

        //echo "enter patient rep <br>";

        $em = $this->_em;

        $in_entity = $em->getRepository('OlegOrderformBundle:Specimen')->removeDuplicateEntities( $in_entity );

        if( strpos( $in_entity->getMrn(), 'NOMRNPROVIDED' ) !== false ) {
            //throw new LogicException('MRN cannot contain NOMRNPROVIDED string');
        }

        //echo "patient rep 1<br>";

        //set up unknown patient
        if( $in_entity->getMrn() == "" || $in_entity->getMrn() == null ) {

            //check the last NOMRNPROVIDED MRN in DB
            $dql = "SELECT MAX(p.mrn) as maxmrn FROM OlegOrderformBundle:Patient p WHERE p.mrn LIKE '%NOMRNPROVIDED%'";
            $query = $em->createQuery($dql);

            $lastMrn =  $query->getResult();

            $lastMrnStr = $lastMrn[0]['maxmrn'];

            //echo $lastMrnStr;
            //exit();

            $mrnIndexArr = explode("-",$lastMrnStr);
            //echo "count=".count($mrnIndexArr)."<br>";
            if( count($mrnIndexArr) > 1 ) {
                $mrnIndex = $mrnIndexArr[1];
            } else {
                $mrnIndex = 0;
            }


            $mrnIndex = ltrim($mrnIndex,'0') + 1;

            $paddedmrn = str_pad($mrnIndex,10,'0',STR_PAD_LEFT);

            //echo "paddedmrn=".$paddedmrn."<br>";
            //exit();

            $in_entity->setMrn('NOMRNPROVIDED-'.$paddedmrn);

        }

        //exit();
        
        $entity = $this->findOneBy(array('mrn' => $in_entity->getMrn()));
        //$em = $this->_em;

        //create new, cause old entity was not found in db 
        if( null === $entity ) {                                        
            //$em->persist($in_entity);                            
            //return $in_entity;
            //echo "new patient<br>";
            return $this->setResult( $in_entity, $orderinfo );         
        } 

        //copy all children from form's entity to existing entity from DB
        foreach( $in_entity->getSpecimen() as $specimen ) {
            //$em->persist($specimen);
            $entity->addSpecimen( $specimen );
        }

        //$em->persist($entity);

        //return $entity;
        //echo "existing patient<br>";
        return $this->setResult( $entity, $orderinfo );     
    }
    
    public function setResult( $patient, $orderinfo = null ) {

        $em = $this->_em;
        $em->persist($patient);

        if( $orderinfo == null ) {
            return $patient;
        }

        //echo "specimen count in patient=".count($patient->getSpecimen())."<br>";
        
        $specimens = $patient->getSpecimen();
        foreach( $specimens as $specimen ) {
            if( !$specimen->getId() ) {          
                $patient->removeSpecimen( $specimen );
                //echo "specimen0: ".$specimen."<br>";
                $specimen = $em->getRepository('OlegOrderformBundle:Specimen')->processEntity( $specimen, $specimen->getAccession(), $orderinfo );
                //echo "specimen1: ".$specimen."<br>";
                $patient->addSpecimen($specimen);
                $orderinfo->addSpecimen($specimen);
            } else {
                continue;
            }
        }
         
        //$em->flush($patient);
        return $patient;
    }

    //filter out duplicate virtual (in form, not in DB) patients
    public function removeDuplicateEntities( $entity ) {

        $patients = $entity->getPatient();

        if( count($patients) == 1 ) {
            return $entity;
        }

        $mrns = array();

        foreach( $patients as $patient ) {

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
