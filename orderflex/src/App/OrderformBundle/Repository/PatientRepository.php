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

namespace App\OrderformBundle\Repository;



use App\OrderformBundle\Entity\MrnType; //process.py script: replaced namespace by ::class: added use line for classname=MrnType


use App\OrderformBundle\Entity\Encounter; //process.py script: replaced namespace by ::class: added use line for classname=Encounter
use App\OrderformBundle\Entity\Patient;
use App\OrderformBundle\Form\DataTransformer\MrnTypeTransformer;
use App\OrderformBundle\Util\SecurityUtil;
use App\UserdirectoryBundle\Util\UserSecurityUtil;

/**
 * PatientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PatientRepository extends ArrayFieldAbstractRepository
{

    public function changeKeytype( $entity ) {

        $em = $this->_em;

        $key = $entity->obtainValidKeyField();

        if( !$key->getKeytype() || $key->getKeytype() == "" ) {
            //this can happen when accession is generated by a user on the form
            //throw new \Exception( 'Patient does not have a valid keytype. keytype=' . $key->getKeytype() );
        //process.py script: replaced namespace by ::class: ['AppOrderformBundle:MrnType'] by [MrnType::class]
            $keytype = $em->getRepository(MrnType::class)->findOneByName("Auto-generated MRN");
            $key->setKeytype($keytype);
        }

        //echo "keytype=".$key->getKeytype()."<br>";
        $newkeytypeid = $this->getCorrectKeytypeId( $key->getKeytype()->getId() );
        //echo "newkeytypeid=".$newkeytypeid."<br>";
        if( $key == "" || $newkeytypeid != $key->getKeytype()->getId() ) {  //$key == "" is the same as $key->getName().""
        //process.py script: replaced namespace by ::class: ['AppOrderformBundle:MrnType'] by [MrnType::class]
            $newkeytypeEntity = $em->getRepository(MrnType::class)->findOneByName("Auto-generated MRN");
            $key->setKeytype($newkeytypeEntity);
        }

        ////0 should be maintained and not deleted out when the patient is registered
        //if(0) {
            //strip zeros and record original
            $originalKey = $key->getField();
            $stripedKey = ltrim((string)$originalKey, '0');
            $key->setField($stripedKey);
            $key->setOriginal($originalKey);
        //}

        return $entity;
    }

    public function getCorrectKeytypeId($keytypeid,$user=null) {
        $em = $this->_em;

        //if( is_numeric ( $keytypeid ) ) {
        if( strval($keytypeid) == strval(intval($keytypeid)) ) {
        //process.py script: replaced namespace by ::class: ['AppOrderformBundle:MrnType'] by [MrnType::class]
            $keytypeEntity = $em->getRepository(MrnType::class)->findOneById($keytypeid);
        } else {
            //create a new MrnType entity
            $mrnTypeTransformer = new MrnTypeTransformer($em,$user);
            $keytypeEntity = $mrnTypeTransformer->createNew($keytypeid);
        }

        if( $keytypeEntity->getName()."" == "Existing Auto-generated MRN" ) {
        //process.py script: replaced namespace by ::class: ['AppOrderformBundle:MrnType'] by [MrnType::class]
            $keytypeEntity = $em->getRepository(MrnType::class)->findOneByName("Auto-generated MRN");
        }
        return $keytypeEntity->getId();
    }

    public function getExtraEntityById( $extra ) {
        if( strval($extra["keytype"]) == strval(intval($extra["keytype"])) ) {
        //process.py script: replaced namespace by ::class: ['AppOrderformBundle:MrnType'] by [MrnType::class]
            return $this->_em->getRepository(MrnType::class)->findOneById($extra["keytype"]);
        } else {
            return null;
        }
    }

    //replace child of patient if duplicated
    //$parent: patient
    //$message: message
    public function replaceDuplicateEntities( $parent, $message ) {

        //echo "Patient replace duplicates: parent: ".$parent;
        //echo "Patient replace duplicates: message: ".$message;

        $encounters = $parent->getChildren(); //encounters

        if( !$encounters ) {
            return $parent;
        }

        $count = 0;
        foreach( $encounters as $encounter ) {    //child is Encounter object
            //echo $count.": Testing child(Encounter)=".$encounter."<br>";

            if( count($encounter->getChildren()) != 1 ) {
                throw new \Exception( 'This entity must have only one child. Number of children=' . count($encounter->getChildren()) );
            }

            //get procedure
            $procedure = $encounter->getChildren()->first(); //in scanorder, encounter has only one procedure
            //echo "must be procedure:".$procedure;

            //get accession
            $accessions = null;
            $accession = null;
            if( $procedure ) {
                $accessions = $procedure->getChildren();
                $accession = $accessions->first(); //in scanorder, procedure has only one accession
            }

            if( !$accession ) {
                continue;
            }

            //echo "must be accession:".$accession;
            //echo "0 accession slide count=".count($accession->getPart()->first()->getBlock()->first()->getSlide())."<br>";

            //$sameChild = $this->findSimilarChild($parent,$encounter->getChildren()->first());
            $em = $this->_em;
            //$sameChild = $em->getRepository('AppOrderformBundle:Encounter')->findSimilarChild( $parent, $encounter->getChildren()->first() );
            //$foundAccession = $em->getRepository('AppOrderformBundle:Accession')->findSimilarChild( $parent, $accession );
        //process.py script: replaced namespace by ::class: ['AppOrderformBundle:Encounter'] by [Encounter::class]
            $foundAccession = $em->getRepository(Encounter::class)->findSimilarChild( $parent, $accession );

            //echo "similar child=".$foundAccession."<br>";

            if( $foundAccession ) {  //accession
                //echo "Found similar child to:".$accession."<br>";

                //Note: assume that js will not submit two similar accession with different contest. JS must check for existing accession in DB and in the form!
                //Copy all children element from checked $accession to found accession $foundAccession

                $foundProcedure = $foundAccession->getParent();
                $foundEncounter = $foundAccession->getParent()->getParent();

                //copy accessions from checked $accession to found accession $foundAccession
                foreach( $accession->getChildren() as $accessionChild ) {
                    $foundAccession->addChildren($accessionChild);
                }

                //Copy Fields for Encounter
                //echo "<br>######################################## Process similar fields ########################################<br>";
                $foundEncounter = $this->processFieldArrays($foundEncounter,$message,$encounter);
                //echo "######################################## EOF Process similar fields ########################################<br>";

                //Copy Fields for Procedure
                //echo "<br>######################################## Process similar fields ########################################<br>";
                $foundProcedure = $this->processFieldArrays($foundProcedure,$message,$procedure);
                //echo "######################################## EOF Process similar fields ########################################<br>";

                //Copy Fields for Accession
                //echo "<br>######################################## Process similar fields ########################################<br>";
                $foundAccession = $this->processFieldArrays($foundAccession,$message,$accession);
                //echo "######################################## EOF Process similar fields ########################################<br>";

                //clear encounter-procedure-accession from patient (parent) and from message
                //$foundProcedure->removeAccession($accession);
                //$foundEncounter->removeProcedure($procedure);
                $parent->removeEncounter($encounter);

                $message->removeAccession($accession);
                $message->removeProcedure($procedure);
                $message->removeEncounter($encounter);

                //add foundEncounter to patient
                $parent->addEncounter($foundEncounter);
                $message->addEncounter($foundEncounter);

                //add $foundProcedure to message
                $message->addProcedure($foundProcedure);

                //add $foundAccession to message
                $message->addAccession($foundAccession);

            }

            $count++;
        }

        return $parent;
    }


    public function findByValidMrnAndMrntype($mrnStr, $mrntypeId=null) {
        //echo "mrnStr=".$mrnStr."; mrntypeId=".$mrntypeId."<br>";
        $queryParameters = array();

        $dql = $this->_em->createQueryBuilder()
            //->from('AppOrderformBundle:Patient', 'patient')
            ->from(Patient::class, 'patient')
            ->select("patient")
            ->leftJoin("patient.mrn", "mrn")
            ->leftJoin("mrn.keytype", "keytype")
            ->where("mrn.field = :mrnStr");

        $queryParameters['mrnStr'] = $mrnStr;

        //$mrntype=null;
        if( $mrntypeId ) {
            $dql->andWhere("keytype.id = :keytypeId");
            $queryParameters['keytypeId'] = $mrntypeId; //$mrntype->getId();
        }

        $query = $dql->getQuery();

        $query->setParameters($queryParameters);

        $patients = $query->getResult();
        //echo "patient count=".count($patients)."<br>";

        $patient = NULL;
        if( count($patients) > 0 ) {
            $patient = $patients[0];
        }

        return $patient;
    }


    public function findByMrntypeString($mrntypeStr) {

        $query = $this->_em->createQueryBuilder()
            ->from(Patient::class, 'patient')
            ->select("patient")
            ->leftJoin("patient.mrn", "mrn")
            ->leftJoin("mrn.keytype", "keytype")
            ->where("keytype.name = :keytypeStr")
            ->setParameters( array('keytypeStr'=>$mrntypeStr) )
            ->getQuery();

        $patients = $query->getResult();
        //echo "patient count=".count($patients)." ";

        return $patients;
    }

    //Used only by updatePatient in PatientController
    //Find the most recent updated encounter and use it to update patient's common fields (names, suffix and gender)
    // The latest encounter fields will be copy to the patient object. They can come from different encounters
    public function copyCommonLatestEncounterFieldsToPatient($patient,$user,$sitename)
    {

        if (!$patient || !$patient->getId()) {
            return null;
        }

        $securityUtil = new UserSecurityUtil($this->_em);
        $source = $securityUtil->getDefaultSourceSystem($sitename);

//        foreach( $patient->getEncounter() as $encounter ) {
//            $this->_em->getRepository('AppOrderformBundle:Encounter')->copyNewCommonFieldsToPatient($encounter,$user,$source);
//        }
//        return $patient;

        $testing = false;
        //$testing = true;

        //suffix
        $this->processLatestEncounterCommonPatientField( $patient, $user, $source, "Suffix", $testing );

        //lastname
        $this->processLatestEncounterCommonPatientField( $patient, $user, $source, "Lastname", $testing );

        //firstname
        $this->processLatestEncounterCommonPatientField( $patient, $user, $source, "Firstname", $testing );

        //middlename
        $this->processLatestEncounterCommonPatientField( $patient, $user, $source, "Middlename", $testing );

        //sex
        $this->processLatestEncounterCommonPatientField( $patient,$user, $source, "Sex", $testing );

        if( $testing ) {
            exit('exit copy common encounter fields to patient');
        }
    }

    public function processLatestEncounterCommonPatientField( $patient, $user, $source, $fieldStr, $testing=false  ) {
        if( !$patient || !$fieldStr ) {
            return null;
        }


        //$fieldStr = "Lastname";
        $fieldlc = strtolower($fieldStr);

        //include latest updated or created
        $query = $this->_em->createQueryBuilder()
            //->from('AppOrderformBundle:EncounterPat'.$fieldlc, 'commonfield')
            ->from('App\\OrderformBundle\\Entity\\EncounterPat'.$fieldlc, 'commonfield')
            ->select("commonfield")
            ->leftJoin("commonfield.encounter", "encounter")
            ->leftJoin("encounter.patient", "patient")
            ->where("patient.id = :patientId AND commonfield.status = 'valid' AND commonfield.field IS NOT NULL")
            ->orderBy("commonfield.updateDate","DESC")
            ->setParameters( array('patientId'=>$patient->getId()) )
            ->getQuery();

        $commonfields = $query->getResult();

        if( $testing ) {
            //testing
            foreach ($commonfields as $commonfield) {
                if ($commonfield->getUpdateDate()) {
                    $updateDate = $commonfield->getUpdateDate()->format('d-m-Y H:i:s');
                } else {
                    $updateDate = null;
                }
                echo $commonfield->getId() . ": $fieldStr=" . $updateDate . "; value=" . $commonfield->getField() . "<br>";
            }
        }

        $encounterField = null;
        if( count($commonfields) > 0 ) {
            $encounterField = $commonfields[0];
        }

        if( $encounterField ) {

            if( $testing ) {
                //testing
                if ($encounterField->getUpdateDate()) {
                    $updateDate = $encounterField->getUpdateDate()->format('d-m-Y H:i:s');
                } else {
                    $updateDate = null;
                }
                echo "Final: " . $encounterField->getId() . ": $fieldStr=" . $updateDate . "; value=" . $encounterField->getField() . "<br>";
                //EOF testing
            }

            //update corresponding patient's field
            $this->processEncounterCommonPatientField($patient,$encounterField,$user,$source,$fieldStr,$testing);
        }

        if( $testing ) {
            echo "<br><br>";
        }

        return $patient;
    }
    //create/update the patient field according to the provided encounter field value
    public function processEncounterCommonPatientField( $patient, $encounterField, $user, $source, $fieldStr, $testing=false ) {

        if( !$encounterField || !$encounterField->getField() || !$patient ) {
            return;
        }

        $validStatus = self::STATUS_VALID;
        $invalidStatus = self::STATUS_INVALID;
        $getterMethod = "get".$fieldStr;
        $adderMethod = "add".$fieldStr;
        $classname = "Patient".$fieldStr;
        $patientFieldObject = $patient->hasSimpleField($encounterField,$getterMethod); //"getLastname"
        if( $patientFieldObject ) {
            if( $testing ) {
                echo "### " . $fieldStr . " field exists: " . $patientFieldObject . "; status=" . $patientFieldObject->getStatus() . "<br>";
            }
            if( $patientFieldObject->getStatus() != $validStatus ) {
                $patient->setStatusAllFields($patient->$getterMethod(), $invalidStatus);
                $patientFieldObject->setStatus($validStatus);
                //echo $fieldStr." change status field : ".$patientFieldObject."; status=".$patientFieldObject->getStatus()."<br>";
            }
        } else {
            if( $testing ) {
                echo "!!! " . $fieldStr . " create new field : " . $classname . " with value " . $encounterField->getField() . "<br>";
            }
            $patient->setStatusAllFields($patient->$getterMethod(), $invalidStatus);
            $entityClass = "App\\OrderformBundle\\Entity\\".$classname;
            $patientFieldObject = new $entityClass($validStatus, $user, $source); //PatientLastName
            $patientFieldObject->setField($encounterField->getField());
            $patient->$adderMethod($patientFieldObject); //addLastname
        }
    }

}
