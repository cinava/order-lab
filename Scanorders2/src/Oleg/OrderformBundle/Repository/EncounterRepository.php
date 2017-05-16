<?php
/**
 * Copyright (c) 2017 Cornell University
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

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

    public function setEncounterKey($key, $entity, $provider=null, $message=null ) {
        $em = $this->_em;
        $newkeytypeEntity = $em->getRepository('OlegOrderformBundle:EncounterType')->findOneByName("Auto-generated Encounter Number");
        $key->setKeytype($newkeytypeEntity);

        $nextKey = $this->getNextNonProvided($entity,null,$message);  //"NO".strtoupper($fieldName)."IDPROVIDED", $className, $fieldName);
        //echo "nextKey=".$nextKey."<br>";

        //we should have only one key field !!!
        $key->setField($nextKey);
        $key->setStatus(self::STATUS_VALID);

        if( !$provider && $message ) {
            $provider = $message->getProvider();
        }
        $key->setProvider($provider);

        return $entity;
    }


    //add Encounter's name, sex to the corresponding patient fields
    public function copyCommonFieldsToPatient( $encounter, $user, $sitename=null ) {

        $patient = $encounter->getParent();

        $securityUtil = new SecurityUtil($this->_em,null,null);
        $source = $securityUtil->getDefaultSourceSystem($sitename);
        //$status = self::STATUS_VALID;

        //suffix
        if( count($encounter->getPatsuffix()) > 0 ) {
            $status = self::STATUS_VALID;
            $alias = $encounter->getPatsuffix()->first()->getAlias();
            if( $alias === true ) {
                $status = self::STATUS_ALIAS;
            } else {
                $name = $this->validFieldIsSet( $patient->getSuffix() );
                if( $name ) {
                    $status = self::STATUS_INVALID;
                }
            }
            //echo "encounter suffix status=".$status."<br>";
            $patientsuffix = new PatientSuffix($status,$user,$source);
            $patientsuffix->setField($encounter->getPatsuffix()->first()->getField());
            $patient->addSuffix($patientsuffix);
        }

        //lastname
        //echo "start: last name count=".count($encounter->getPatlastname())."<br>";
        //echo "start: last name count=".count($patient->getLastname())."<br>";
        if( count($encounter->getPatlastname()) > 0 ) {
            $status = self::STATUS_VALID;
            $alias = $encounter->getPatlastname()->first()->getAlias();
            if( $alias === true ) {
                $status = self::STATUS_ALIAS;
            } else {
                $name = $this->validFieldIsSet( $patient->getLastname() );
                if( $name ) {
                    $status = self::STATUS_INVALID;
                }
            }
            //echo "add lastname!!!!!!! <br>";
            $patientlastname = new PatientLastName($status,$user,$source);
            $patientlastname->setField($encounter->getPatlastname()->first()->getField());
            $patient->addLastname($patientlastname);
        }
        //echo "end: last name count=".count($patient->getLastname())."<br>";

        //firstname
        if( count($encounter->getPatfirstname()) > 0 ) {
            $status = self::STATUS_VALID;
            $alias = $encounter->getPatfirstname()->first()->getAlias();
            if( $alias === true ) {
                $status = self::STATUS_ALIAS;
            } else {
                $name = $this->validFieldIsSet( $patient->getFirstname() );
                if( $name ) {
                    $status = self::STATUS_INVALID;
                }
            }
            $patientfirstname = new PatientFirstName($status,$user,$source);
            $patientfirstname->setField($encounter->getPatfirstname()->first()->getField());
            $patient->addFirstname($patientfirstname);
        }

        //middlename
        if( count($encounter->getPatmiddlename()) > 0 ) {
            $status = self::STATUS_VALID;
            $alias = $encounter->getPatmiddlename()->first()->getAlias();
            if( $alias === true ) {
                $status = self::STATUS_ALIAS;
            } else {
                $name = $this->validFieldIsSet( $patient->getMiddlename() );
                if( $name ) {
                    $status = self::STATUS_INVALID;
                }
            }
            $patientmiddlename = new PatientMiddleName($status,$user,$source);
            $patientmiddlename->setField($encounter->getPatmiddlename()->first()->getField());
            $patient->addMiddlename($patientmiddlename);
        }

        //sex
        if( count($encounter->getPatsex()) > 0 ) {
            $status = self::STATUS_VALID;
            $sex = $this->validFieldIsSet( $patient->getSex() );
            if( $sex ) {
                //$sex->setStatus(self::STATUS_INVALID);
                $status = self::STATUS_INVALID;
            }
            $patientsex = new PatientSex($status,$user,$source);
            $encounterSex = $encounter->getPatsex()->first()->getField();
            //echo "encounter sex=".$encounterSex."<br>";
            $patientsex->setField($encounterSex);
            $patient->addSex($patientsex);
        }


        //echo "patient after copy encounter fields: ".$patient."<br>";
    }
    public function copyNewCommonFieldsToPatient( $encounter, $user, $source ) {
        $patient = $encounter->getParent();

        //suffix
        $this->processEncounterCommonPatientField( $patient, $encounter->getPatsuffix(), $user, $source, "Suffix" );

        //lastname
        $this->processEncounterCommonPatientField( $patient, $encounter->getPatlastname(), $user, $source, "Lastname" );

        //firstname
        $this->processEncounterCommonPatientField( $patient, $encounter->getPatfirstname(), $user, $source, "Firstname" );

        //middlename
        $this->processEncounterCommonPatientField( $patient, $encounter->getPatmiddlename(), $user, $source, "Middlename" );

        //sex
        $this->processEncounterCommonPatientField( $patient, $encounter->getPatsex(), $user, $source, "Sex" );

    }
    //$fieldStr - i.e. "Lastname"
    public function processEncounterCommonPatientField( $patient, $encounterFields, $user, $source, $fieldStr ) {
        $validStatus = self::STATUS_VALID;
        $invalidStatus = self::STATUS_INVALID;
        //lastname
        $validField = $this->validFieldIsSet( $encounterFields ); //$encounter->getPatlastname()
        //echo "valid encounter lastname=".$validField."<br>";
        if( $validField ) {
            $getterMethod = "get".$fieldStr;
            $adderMethod = "add".$fieldStr;
            $classname = "Patient".$fieldStr;
            $patientFieldObject = $patient->hasSimpleField($validField,$getterMethod); //"getLastname"
            if( $patientFieldObject ) {
                //echo $fieldStr." field exists: ".$patientFieldObject."; status=".$patientFieldObject->getStatus()."<br>";
                if( $patientFieldObject->getStatus() != $validStatus ) {
                    $patient->setStatusAllFields($patient->$getterMethod(), $invalidStatus);
                    $patientFieldObject->setStatus($validStatus);
                    //echo $fieldStr." change status field : ".$patientFieldObject."; status=".$patientFieldObject->getStatus()."<br>";
                }
            } else {
                $patient->setStatusAllFields($patient->$getterMethod(), $invalidStatus);
                $entityClass = "Oleg\\OrderformBundle\\Entity\\".$classname;
                $patientFieldObject = new $entityClass($validStatus, $user, $source); //PatientLastName
                $patientFieldObject->setField($validField->getField());
                $patient->$adderMethod($patientFieldObject); //addLastname
            }
        }
    }

    //age conflict is based on 3 values: dob, encounter date and encounter age
    public function checkAgeConflict( $encounter, $message, $original ) {

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
            $dataqualityObj->setMessage($message);
            $dataqualityObj->setPatientdob($patientdob);
            $dataqualityObj->setEncounterdate($encounterdate);
            $dataqualityObj->setEncounterage($encounterage);
            $dataqualityObj->setProvider($message->getProvider());
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
    public function replaceDuplicateEntities( $parent, $message ) {
        //echo "Encounter replace duplicates:".$parent;
        return $parent;
    }



    //find similar accession in patient by using encounter-procedure.
    //$parent: patient
    //$newChild: accession
    //find similar child and return the first one
    //return accession or false if no similar children are found
    public function findSimilarChild($parent,$newChild) {
        //echo "Encounter: find similar parent: ".$parent." <br>";
        //echo "Encounter: find similar Child to: ".$newChild." <br>";

        $encounters = $parent->getChildren();

        //echo "<br>";
        //echo $newChild;
        //echo "newChild key=".$newChild->obtainValidKeyfield()."<br>";
        if( $newChild->obtainValidKeyfield()."" == "" ) {   //no name is provided, so can't compare => does not exist
            //echo "false: no name <br>";
            return false;
        }

        if( !$encounters || count($encounters) == 0 ) { //no children => does not exist
            //echo "false: no children <br>";
            return false;
        }

        foreach( $encounters as $encounter ) {
            //echo $child;

            $procedures = $encounter->getProcedure();

            if( count($procedures) != 1 ) {
                throw new \Exception( 'Encounter must have only one Procedure child. Number of children=' . count($procedures) );
            }

            $procedure = $procedures->first();

            $accessions = $procedure->getAccession();

            if( count($accessions) != 1 ) {
                throw new \Exception( 'Procedure must have only one Accession child. Number of children=' . count($procedures) );
            }

            $accession = $accessions->first();

            if( $accession === $newChild ) {
                //echo "the same child: continue<br>";
                return false;
            }

            if( $this->entityEqualByComplexKey($accession, $newChild) ) {
                //echo "MATCH!: ".$child." <br>";
                return $accession;
            } else {
                //echo "NO MATCH! <br>";
            }

        }//foreach

        return false;
    }


    //process conflict if exists for procedure number. Replace conflicting procedure number by a new generated number.
    //This function redirects to the same overrided function by Procedure Repository
    public function processDuplicationKeyField( $encounter, $message ) {

        $procedures = $encounter->getChildren();

        if( count($procedures) != 1 ) {
            throw new \Exception( 'Encounter entity must have only one Procedure. Number of Procedure found is ' . count($procedures) );
        }

        $procedure = $procedures->first();

        $encounter->removeChildren($procedure);

        //process conflict if exists for procedure number. Replace conflicting procedure number by a new generated number.
        $procedure = $this->_em->getRepository('OlegOrderformBundle:Procedure')->processDuplicationKeyField($procedure,$message);

        $encounter->addChildren($procedure);

        return $encounter;
    }

    //used for call log entry
    public function findOneEncounterByNumberAndType( $encounterTypeId, $encounterNumber ) {

//        $repository = $this->_em->getRepository('OlegOrderformBundle:Encounter');
//        $dql = $repository->createQueryBuilder("encounter");
//        $dql->leftJoin("encounter.number", "number");
//
//        $dql->andWhere("number.field = :number AND number.keytype = :keytype AND encounter.status = 'valid'");
//
//        $parameters['number'] = $encounterNumber;
//        $parameters['keytype'] = $encounterTypeId;
//
//        $query = $this->_em->createQuery($dql);
//        $query->setParameters($parameters);
//        $encounters = $query->getResult();

        $encounters = $this->findEncountersByNumberAndType($encounterTypeId,$encounterNumber);

        if( count($encounters) > 0 ) {
            return $encounters[0];
        }

        return null;
    }

    public function findEncountersByNumberAndType( $encounterTypeId, $encounterNumber, $status='valid' ) {

        $repository = $this->_em->getRepository('OlegOrderformBundle:Encounter');
        $dql = $repository->createQueryBuilder("encounter");
        $dql->leftJoin("encounter.number", "number");

        if( $status ) {
            $dql->andWhere("number.field = :number AND number.keytype = :keytype AND encounter.status = '".$status."'");
        } else {
            $dql->andWhere("number.field = :number AND number.keytype = :keytype");
        }

        $parameters['number'] = $encounterNumber;
        $parameters['keytype'] = $encounterTypeId;

        $query = $this->_em->createQuery($dql);
        $query->setParameters($parameters);
        $encounters = $query->getResult();

        return $encounters;
    }

    public function findAllEncountersByEncounter( $encounter ) {

        $key = $encounter->obtainValidField('number');
        $encounterNumber = $key->getField();
        $encounterTypeId = $key->getKeytype();

        $encounters = $this->findEncountersByNumberAndType($encounterTypeId,$encounterNumber);

        return $encounters;
    }

    //used for call log entry
    public function isPatientEncounterMatch( $mrnNumber, $mrnTypeId, $encounterEntity ) {

        $patient = $encounterEntity->getPatient();
        if( $patient ) {
            $mrn = $patient->obtainValidField('mrn');
            if( $mrn && $mrn->getField() == $mrnNumber && $mrn->getKeytype() && $mrn->getKeytype()->getId() == $mrnTypeId ) {
                return true;
            }
        }

        return false;
    }
}
