<?php

namespace Oleg\OrderformBundle\Repository;


use Oleg\OrderformBundle\Security\Util\SecurityUtil;
use Oleg\OrderformBundle\Entity\PatientLastName;
use Oleg\OrderformBundle\Entity\PatientFirstName;
use Oleg\OrderformBundle\Entity\PatientMiddleName;
use Oleg\OrderformBundle\Entity\PatientSex;
use Oleg\OrderformBundle\Entity\DataQualityAge;
use Oleg\OrderformBundle\Entity\PatientSuffix;


/**
 * EncounterRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EncounterRepository extends ArrayFieldAbstractRepository
{

//    public function setEncounterKey($key, $entity, $orderinfo) {
//        $em = $this->_em;
//        $newkeytypeEntity = $em->getRepository('OlegOrderformBundle:EncounterType')->findOneByName("Auto-generated Encounter Number");
//        $key->setKeytype($newkeytypeEntity);
//
//        $nextKey = $this->getNextNonProvided($entity,null,$orderinfo);  //"NO".strtoupper($fieldName)."PROVIDED", $className, $fieldName);
//
//        //we should have only one key field !!!
//        $key->setField($nextKey);
//        $key->setStatus(self::STATUS_VALID);
//        $key->setProvider($orderinfo->getProvider());
//    }


    //add Encounter's name, sex to the corresponding patient fields
    public function copyCommonFieldsToPatient( $encounter, $user ) {

        $patient = $encounter->getParent();

        $securityUtil = new SecurityUtil($this->_em,null,null);
        $source = $securityUtil->getDefaultSourceSystem();  //'scanorder';
        $status = self::STATUS_VALID;

        //suffix
        if( count($encounter->getPatsuffix()) > 0 ) {
            $suffix = $this->validFieldIsSet( $patient->getSuffix() );
            if( $suffix ) {
                //$suffix->setStatus(self::STATUS_INVALID);
                $status = self::STATUS_INVALID;
            }
            $patientsuffix = new PatientSuffix($status,$user,$source);
            $patientsuffix->setField($encounter->getPatsuffix()->first()->getField());
            $patient->addSuffix($patientsuffix);
        }

        //lastname
        //echo "proc last name count=".count($encounter->getPatlastname())."<br>";
        if( count($encounter->getPatlastname()) > 0 ) {
            $lastname = $this->validFieldIsSet( $patient->getLastname() );
            if( $lastname ) {
                //$lastname->setStatus(self::STATUS_INVALID);
                $status = self::STATUS_INVALID;
            }
            $patientlastname = new PatientLastName($status,$user,$source);
            $patientlastname->setField($encounter->getPatlastname()->first()->getField());
            $patient->addLastname($patientlastname);
        }

        //firstname
        if( count($encounter->getPatfirstname()) > 0 ) {
            $firstname = $this->validFieldIsSet( $patient->getFirstname() );
            if( $firstname ) {
                //$firstname->setStatus(self::STATUS_INVALID);
                $status = self::STATUS_INVALID;
            }
            $patientfirstname = new PatientFirstName($status,$user,$source);
            $patientfirstname->setField($encounter->getPatfirstname()->first()->getField());
            $patient->addFirstname($patientfirstname);
        }

        //middlename
        if( count($encounter->getPatmiddlename()) > 0 ) {
            $middlename = $this->validFieldIsSet( $patient->getMiddlename() );
            if( $middlename ) {
                //$middlename->setStatus(self::STATUS_INVALID);
                $status = self::STATUS_INVALID;
            }
            $patientmiddlename = new PatientMiddleName($status,$user,$source);
            $patientmiddlename->setField($encounter->getPatmiddlename()->first()->getField());
            $patient->addMiddlename($patientmiddlename);
        }

        //sex
        if( count($encounter->getPatsex()) > 0 ) {
            $sex = $this->validFieldIsSet( $patient->getSex() );
            if( $sex ) {
                //$sex->setStatus(self::STATUS_INVALID);
                $status = self::STATUS_INVALID;
            }
            $patientsex = new PatientSex($status,$user,$source);
            //echo "encounter sex=".$encounter->getPatsex()->first()."<br>";
            $patientsex->setField($encounter->getPatsex()->first());
            $patient->addSex($patientsex);
        }


    }

    //age conflict is based on 3 values: dob, encounter date and encounter age
    public function checkAgeConflict( $encounter, $orderinfo, $original ) {

        $dataqualityObj = null;

        if( $original ) {
            $formEntity = $original;
        } else {
            $formEntity = $encounter;
        }

        $patient = $formEntity->getParent();

        $patientage = $patient->calculateAgeInt();
        $patientdob = $patient->obtainValidField('dob')->getField();
        $encounterage = $formEntity->obtainValidField('Patage')->getField();
        $encounterdate = $formEntity->obtainValidField('date')->getField();

//        if( $encounterdate == NULL )
//            echo "<br>encdate null<br>";
//        if( $encounterdate == "" )
//            echo "<br>encdate empty<br>";

        //Case 1a: if $patientage and $encounterdate are empty => no conflict
        if( $patientage == 0 && $encounterdate == NULL ) {
            return $dataqualityObj;
        }

        //Case 1b: if $encounterage is empty => no conflict
        if( $encounterage == NULL ) {
            return $dataqualityObj;
        }

        //Case 1c: if $patientage is empty => no conflict
        if( $patientage == 0 ) {
            return $dataqualityObj;
        }

        $msg = "";

        //echo "<br>encounterdate=".$encounterdate.", patientage=". $patientage . ", encounterage=".$encounterage."<br>";

        //Case 2: if encounter date is empty, but age and dob are set, verify encounter age with patient age by current date
        if( $encounterdate == NULL && $patientage > 0 && $encounterage > 0 ) {
            echo "case 2: ".$encounterage."?=".$patientage."<br>";
            if( $encounterage != $patientage ) {
                $msg = "The patient's age at the time of encounter does not correspond to the patient's date of birth (DOB) based on today's date. Please verify and correct the DOB and Patient's Age (at the time of encounter) field values.".
                       " Encounter age=".$encounterage.", patient age (based on DOB)=".$patientage;
            }
        }

        //Case 3: all 3 parameters are set: patient's dob, encounter date and age at the time of encounter => years diff between dob and encounter date should be equal to encounter age
        if( $encounterdate != NULL && $patientage > 0 && $encounterage > 0 ) {

            //calculate age based on encounter date and dob and compare with existing encounter age
            $interval = $encounterdate->diff($patientdob);
            $years = $interval->format('%y');

            echo "case 3: ".$years."?=".$encounterage."<br>";
            if( $years != $encounterage ) {
                $msg = "The patient's age at the time of encounter does not correspond the patient's date of birth (DOB). Please verify and correct the DOB, Encounter Date, and Patient's Age (at the time of encounter) field values.".
                       " Encounter age=".$encounterage.", expected encounter age (based on years difference bewteen DOB and encounter date)=".$years;
            }

        }

        if(  $msg != "" ) {

            $dataqualityObj = new DataQualityAge();
            $dataqualityObj->setOrderinfo($orderinfo);
            $dataqualityObj->setPatientdob($patientdob);
            $dataqualityObj->setEncounterdate($encounterdate);
            $dataqualityObj->setEncounterage($encounterage);
            $dataqualityObj->setProvider($orderinfo->getProvider());
            $dataqualityObj->setDescription($msg);
            $dataqualityObj->setStatus('active');

            if( $encounter && $encounter->getId() && $encounter->getId() != "" ) {
                $dataqualityObj->setEncounter($encounter);
            } else {
                $dataqualityObj->setEncounter($original);
            }

            $this->_em->persist($dataqualityObj);

        }

        echo "age conflict msg=".$msg."<br>";


        return $dataqualityObj;
    }


    //exception for encounter: encounter is linked to a single procedure => check if procedure is already existed in DB, if existed => don't create encounter, but use existing encounter
    public function findUniqueByKey( $entity ) {

        //echo "find Unique By Key: Encounter: ".$entity;

        if( count($entity->getChildren()) != 1 ) {
            throw new \Exception( 'This entity must have only one child. Number of children=' . count($entity->getChildren()) );
        }

        $em = $this->_em;
        $foundProcedure = $em->getRepository('OlegOrderformBundle:Procedure')->findUniqueByKey( $entity->getChildren()->first() );    //,"procedure","procedure");

        if( $foundProcedure ) {
            //echo "This entity alsready exists in DB ".$foundProcedure."<br>";
            //get existing encounter
            return $foundProcedure->getParent(); //Procedure->getEncounter => encounter

        } else {
            return null;
        }
    }

    //make sure encounter type is set to "Auto-generated Encounter Number"
    public function changeKeytype($entity) {

        $key = $entity->obtainValidKeyField();

        if( !$key->getKeytype() || $key->getKeytype() == "" ) {
            //throw new \Exception( 'Encounter does not have a valid keytype. keytype=' . $key->getKeytype() );
            $em = $this->_em;
            $newkeytypeEntity = $em->getRepository('OlegOrderformBundle:EncounterType')->findOneByName("Auto-generated Encounter Number");
            $key->setKeytype($newkeytypeEntity);
        }

        if( $key == "" || $key->getField() != "Auto-generated Encounter Number" ) {
            $em = $this->_em;
            $newkeytypeEntity = $em->getRepository('OlegOrderformBundle:EncounterType')->findOneByName("Auto-generated Encounter Number");
            $key->setKeytype($newkeytypeEntity);
        }

        //strip zeros and record original
        $originalKey = $key->getField();
        $key->setOriginal($originalKey);
        $stripedKey = ltrim($originalKey,'0');
        $key->setField($stripedKey);

        return $entity;
    }

    //replace child if duplicated
    //$parent: patient
    //Encounter has only one procedure
    public function replaceDuplicateEntities( $parent, $orderinfo ) {
        //echo "Encounter replace duplicates:".$parent;
        return $parent;
    }



    //find similar encounter in patient.
    //However, encounter is identified by encounter number
    //$parent: patient
    //$newChild: procedure
    //find similar child and return the first one
    //return false if no similar children are found
    public function findSimilarChild($parent,$newChild) {
        //echo "Encounter: find similar Child to: ".$newChild." <br>";

        $children = $parent->getChildren();

        //echo "<br>";
        //echo $newChild;
        //echo "newChild key=".$newChild->obtainValidKeyfield()."<br>";
        if( $newChild->obtainValidKeyfield()."" == "" ) {   //no name is provided, so can't compare => does not exist
            //echo "false: no name <br>";
            return false;
        }

        if( !$children || count($children) == 0 ) { //no children => does not exist
            //echo "false: no children <br>";
            return false;
        }

        foreach( $children as $child ) {
            //echo $child;

            if( count($child->getProcedure()) != 1 ) {
                throw new \Exception( 'This entity must have only one child. Number of children=' . count($child->getProcedure()) );
            }

            if( $child->getProcedure()->first() === $newChild ) {
                //echo "the same child: continue<br>";
                return false;
            }

            if( $this->entityEqualByComplexKey($child->getProcedure()->first(), $newChild) ) {
                //echo "MATCH!: ".$child." <br>";
                return $child;
            } else {
                //echo "NO MATCH! <br>";
            }

        }//foreach

        return false;
    }


    //process conflict if exists for procedure number. Replace conflicting procedure number by a new generated number.
    //This function redirects to the same overrided function by Procedure Repository
    public function processDuplicationKeyField( $encounter, $orderinfo ) {

        $procedures = $encounter->getChildren();

        if( count($procedures) != 1 ) {
            throw new \Exception( 'Encounter entity must have only one Procedure. Number of Procedure found is ' . count($procedures) );
        }

        $procedure = $procedures->first();

        $encounter->removeChildren($procedure);

        //process conflict if exists for procedure number. Replace conflicting procedure number by a new generated number.
        $procedure = $this->_em->getRepository('OlegOrderformBundle:Procedure')->processDuplicationKeyField($procedure,$orderinfo);

        $encounter->addChildren($procedure);

        return $encounter;
    }

}
