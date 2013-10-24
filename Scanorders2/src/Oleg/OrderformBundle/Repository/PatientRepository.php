<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Exception\LogicException;
use Oleg\OrderformBundle\Entity\Patient;

/**
 * PatientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PatientRepository extends EntityRepository
{

    const STATUS_RESERVED = "reserved";
    const STATUS_VALID = "valid";

    //make sure the uniqueness entity. Make new or return id of existing.
    public function processEntity( $patient, $orderinfo = null ) {

        //echo "enter patient rep <br>";
//        echo "patient id=".$patient->getId();
//        exit();

        $em = $this->_em;

        $patient = $em->getRepository('OlegOrderformBundle:Specimen')->removeDuplicateEntities( $patient );

        $found = $this->isExisted($patient); //return: 1 - null, 2 - existed but STATUS_RESERVED, 3 - existed and STATUS_VALID

        if( !$found ) {                                             //Case 1 - User entered new MRN, not existed in DB

            $patient->setStatus(self::STATUS_VALID);
            return $this->setResult( $patient, $orderinfo );

        } elseif( $found->getStatus() == self::STATUS_RESERVED  ) { //case 2 - existed but empty with STATUS_RESERVED; User press check with empty MRN field => new MRN was generated

            $patient->setStatus(self::STATUS_VALID);
            return $this->setResult( $patient, $orderinfo );

        } elseif( $found->getStatus() == self::STATUS_VALID  ) {    //Case 3 - existed and STATUS_VALID; User entered existed MRN

            //copy all children from form's entity to existing entity from DB
            foreach( $patient->getSpecimen() as $specimen ) {
                $found->addSpecimen( $specimen );
            }
            //copy all array fields from form to existing patient
            $found = $this->copyFieldArrays($patient,$found);
            return $this->setResult( $found, $orderinfo );

        } elseif( $found == -1 ) {                                  //Case 4 - MRN is not provided. Theoretically, this case is not possible
            $patient = $this->createPatient(self::STATUS_VALID);
            return $this->setResult( $patient, $orderinfo );
        } else {                                                    //Case 5 - Theoretically, this case is not possible
            throw new LogicException('Logical Error: Patient status is undefined');
        }

    }
    
    public function setResult( $patient, $orderinfo = null ) {
              
        $em = $this->_em;
        $em->persist($patient);

        if( $orderinfo == null ) {
            return $patient;
        }

        $patient = $this->processFieldArrays($patient,$orderinfo);
             
        $specimens = $patient->getSpecimen();
        //echo "specimen count in patient=".count($specimens)."<br>";
             
        foreach( $specimens as $specimen ) {   
                            
            if( $em->getRepository('OlegOrderformBundle:Specimen')->notExists($specimen) ) {     //specimen new               
                $patient->removeSpecimen( $specimen );
                //echo "specimen0: ".$specimen."<br>";
                $specimen = $em->getRepository('OlegOrderformBundle:Specimen')->processEntity( $specimen, null, $specimen->getAccession(), $orderinfo );
                //echo "specimen1: ".$specimen."<br>";
                $patient->addSpecimen($specimen);
                $orderinfo->addSpecimen($specimen);
            } else {         //specimen from DB     
                //echo "specimen from DB continue id=".$specimen->getId()."<br>";
                continue;              
            }
            
        }

        //exit();
        //$em->flush($patient);
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

    //check by ID
    public function notExists($entity) {
        $id = $entity->getId();
        if( !$id ) {
            return true;
        }      
        $em = $this->_em;
        $found = $em->getRepository('OlegOrderformBundle:Patient')->findOneById($id);       
        if( null === $found ) {
            return true;
        } else {
            return false;
        }
    }

    //check the last NOMRNPROVIDED MRN in DB and construct next available MRN
    public function getNextMrn() {
        $em = $this->_em;
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
        return 'NOMRNPROVIDED-'.$paddedmrn;
    }

    //check if the STATUS_VALID patient is existed in DB
    //return: null - not existed, entity object if existed
    public function isExisted( $patient ) {

        if( $patient->getMrn() == "" || $patient->getMrn() == null ) {
            return -1;
        }

        return $this->findOneBy(array('mrn' => $patient->getMrn()));
    }

    public function createPatient( $status = null ) {
        if( !$status ) {
            $status = self::STATUS_RESERVED;
        }
        $em = $this->_em;
        $mrn = $this->getNextMrn();
        $patient = new Patient();
        $patient->setMrn($mrn);
        $patient->setStatus($status);
        $em->persist($patient);
        $em->flush();
        return $patient;
    }

    //assign user provider
    public function processFieldArrays($patient,$orderinfo) {

        if( !$orderinfo || count($orderinfo->getProvider()) == 0 ) {
            //return $patient;
        }

        $provider = $orderinfo->getProvider()[0]; //assume orderinfo has only one provider.
        //echo "mrn=".$patient->getMrn().", hist count=".count($patient->getClinicalHistory()).", provider=".$provider."<br>";

        $fields = $patient->getClinicalHistory();
        $validitySet = false;

        foreach( $fields as $field ) {
            //echo "hist id=".$hist->getId()."<br>";
            if( !$field->getProvider() || $field->getProvider() == "" ) {
                $field->setProvider($provider);
            }

            if( !$validitySet ) {
                if( !$patient->getId() || !$this->hasValidity($patient) ) { //set validity for the first added field
                    $field->setValidity(1);
                }
                $validitySet = true;
            }

        }
        //exit();
        return $patient;
    }

    //copy field entity if not existed from source object to destination object
    public function copyFieldArrays( $source, $dest) {
        $em = $this->_em;
        foreach( $source->getClinicalHistory() as $hist ) {
            $found = $em->getRepository('OlegOrderformBundle:ClinicalHistory')->findOneById($hist->getId());
            if( !$found ) {
                $dest->addClinicalHistory( $hist );
            }
        }
        return $dest;
    }

    public function hasValidity( $entity ) {
        $fields = $entity->getClinicalHistory();
        foreach( $fields as $field ) {
            if( $field->getValidity() == 1 ) {
                return true;
            }
        }
        return false;
    }
    
}
