<?php

namespace Oleg\CallLogBundle\Util;
use Doctrine\Common\Collections\ArrayCollection;
use Oleg\OrderformBundle\Entity\MrnType;
use Oleg\OrderformBundle\Entity\PatientMasterMergeRecord;
use Oleg\OrderformBundle\Entity\PatientMrn;

/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 6/10/2016
 * Time: 3:04 PM
 */
class CallLogUtil
{

    protected $em;
    protected $sc;
    protected $container;

    public function __construct( $em, $sc, $container ) {
        $this->em = $em;
        $this->sc = $sc;
        $this->container = $container;
    }


//    public function processMerge( $patientsArr ) {
//
//        foreach( $patientsArr as $patient ) {
//
//
//
//        }
//
//    }


    //auto-generating a unique MRN on Scan Order, but prepend a prefix "MERGE"
    public function autoGenerateMergeMrn( $patient ) {

        $keyTypeMergeID = $this->em->getRepository('OlegOrderformBundle:MrnType')->findOneByName("Merge ID");
        if( !$keyTypeMergeID ) {
            $msg = 'MrnType not found by name Merge ID';
            throw new \Exception($msg);
            //return $msg;
        }
        $extra = array( "keytype" => $keyTypeMergeID->getId() );
        //$extra = null;

        $nextKey = $this->em->getRepository('OlegOrderformBundle:Patient')->getNextNonProvided($patient,$extra,null,"MERGE-ID");

        //convert NOMRNPROVIDED-0000000002 to MERGE-ID-0000000002
        //$nextKey = str_replace("NOMRNPROVIDED","",$nextKey);
        //$nextKey = "MERGE-ID".$nextKey;
        //echo "nextKey=".$nextKey."<br>";
        //exit('1');

        return $nextKey;
    }

    public function addGenerateMergeMrnToPatient( $patient, $autoGeneratedMergeMrn, $provider ) {
        $newMrn = $this->createPatientMergeMrn($provider,$patient,$autoGeneratedMergeMrn);
        if( !($newMrn instanceof PatientMrn) ) {
            return $newMrn; //this is an error message
        }

        return $patient;
    }

    //Always create a new MRN
    public function createPatientMergeMrn( $provider, $patient, $mrnId ) {

        //Source System: ORDER Call Log Book
        $sourcesystem = $this->em->getRepository('OlegUserdirectoryBundle:SourceSystemList')->findOneByName("ORDER Call Log Book");
        if( !$sourcesystem ) {
            $msg = 'Source system not found by name ORDER Call Log Book';
            //throw new \Exception($msg);
            return $msg;
        }

        $keyTypeMergeID = $this->em->getRepository('OlegOrderformBundle:MrnType')->findOneByName("Merge ID");
        if( !$keyTypeMergeID ) {
            $msg = 'MrnType not found by name Merge ID';
            //throw new \Exception($msg);
            return $msg;
        }

        $newMrn = null;
        $status = 'valid';

        //Create a new MRN
        $newMrn = new PatientMrn($status,$provider,$sourcesystem);
        $newMrn->setKeytype($keyTypeMergeID);
        $newMrn->setField($mrnId);
        $patient->addMRn($newMrn);

        //exit('create Patient Merge Mrn exit; mrnId='.$mrnId);
        return $newMrn;
    }
    //NOT USED: check if invalid MRN already exists or create a new one
    public function createWithCheckPatientMergeMrn( $provider, $patient, $mrnId ) {

        //Source System: ORDER Call Log Book
        $sourcesystem = $this->em->getRepository('OlegUserdirectoryBundle:SourceSystemList')->findOneByName("ORDER Call Log Book");
        if( !$sourcesystem ) {
            $msg = 'Source system not found by name ORDER Call Log Book';
            //throw new \Exception($msg);
            return $msg;
        }

        $keyTypeMergeID = $this->em->getRepository('OlegOrderformBundle:MrnType')->findOneByName("Merge ID");
        if( !$keyTypeMergeID ) {
            $msg = 'MrnType not found by name Merge ID';
            //throw new \Exception($msg);
            return $msg;
        }

        //check if invalid PatientMrn already exists by field and keytype
//        $patientMrns = $this->em->getRepository('OlegOrderformBundle:PatientMrn')->findBy(
//            array(
//                'keytype' => $keyTypeMergeID->getId(),
//                'field' => $mrnId,
//                'patient' => $patient->getId(),
//            )
//        );
        $patientMrns = $patient->obtainMergeMrnArr();

        $newMrn = null;
        $status = 'valid';

        if( count($patientMrns) == 0 ) {
            //OK: create a new MRN
            $newMrn = new PatientMrn($status,$provider,$sourcesystem);
            $newMrn->setKeytype($keyTypeMergeID);
            $patient->addMRn($newMrn);
        }

        if( count($patientMrns) > 1 ) {
            foreach( $patientMrns as $patientMrn ) {
                if( $patientMrn->getField() == $mrnId && $patientMrn->getStatus() == 'invalid' ) {
                    $newMrn = $patientMrn;
                    $newMrn->setStatus($status);
                    $newMrn->setProvider($provider);
                    $newMrn->setCreationdate();
                    break;
                }
            }
        }

        if( count($patientMrns) == 1 ) {
            if( $patientMrns[0]->getField() == $mrnId && $patientMrns[0]->getStatus() == 'invalid' ) {
                $newMrn = $patientMrns[0];
                $newMrn->setStatus($status);
                $newMrn->setProvider($provider);
                $newMrn->setCreationdate();
                //return "Found 1 invalid Merged MRN ID=".$patientMrns[0]->getField()."; status=".$patientMrns[0]->getStatus().".<br>";
            }
        }

        if( !$newMrn ) {
            $msg = 'PatientMrn has not been created. Found patientMrns count='.count($patientMrns);
            return $msg;
        }

        return $newMrn;
    }

    public function getMergedPatients( $mergeId, $mergedPatients=null, $existingPatientIds=null ) {

        $keyTypeMergeID = $this->em->getRepository('OlegOrderformBundle:MrnType')->findOneByName("Merge ID");
        if( !$keyTypeMergeID ) {
            $msg = 'MrnType not found by name Merge ID';
            throw new \Exception($msg);
            //return $msg;
        }

        $parameters = array();

        $repository = $this->em->getRepository('OlegOrderformBundle:Patient');
        $dql = $repository->createQueryBuilder("patient");
        $dql->leftJoin("patient.mrn", "mrn");

        $dql->andWhere("mrn.status = 'valid'");

        $dql->andWhere("mrn.keytype = :keytype AND mrn.field = :mrn");
        $parameters['keytype'] = $keyTypeMergeID->getId();
        $parameters['mrn'] = $mergeId;

        if( $existingPatientIds ) {
            $dql->andWhere("patient.id NOT IN (" . implode(",", $existingPatientIds) . ")");
        }

        $query = $this->em->createQuery($dql);
        $query->setParameters($parameters);
        //echo $mergeId.":sql=".$query->getSql()."<br>";
        $patients = $query->getResult();
        //echo "merged patients = ".count($patients)."<br>";

        if( $mergedPatients == null ) {
            $mergedPatients = new ArrayCollection();
        }

        //make unique array of the merged patients
        foreach( $patients as $patient ) {
            //echo "tryingAddPatient=".$patient->getId()."<br>";
            //$mergedPatients[] = $patient;
            if( !$mergedPatients->contains($patient) ) {
                //echo "addedPatient=".$patient->getId()."<br>";
                $mergedPatients->add($patient);
            }
        }

        return $mergedPatients;
    }

    //FirstNameOfAuthorOfMRN LastNameofAuthorOfMRN on DateOfMergeIDAdditionToPatientOne /
    // DateOfMergeIDAdditionToPatientTwo via Merge ID [MergeID-MRN]
    public function obtainSameMergeMrnInfoStr( $mrn1, $mrn2 ) {
        if( $mrn1->getField() != $mrn2->getField() ) {
            return null;
        }
        //1) get earliest author and creationdate
        if( $mrn1->getCreationdate() > $mrn2->getCreationdate() ) {
            //$mrn2 is the earliest one
            $author = $mrn2->getProvider();
            $creationDate = $mrn2->getCreationdate();
            $creationDateTwo = $mrn1->getCreationdate();
        } else {
            //$mrn1 is the earliest one
            $author = $mrn1->getProvider();
            $creationDate = $mrn1->getCreationdate();
            $creationDateTwo = $mrn2->getCreationdate();
        }
        $resStr = $author." on ".$creationDate->format("m/d/Y");

        //DateOfMergeIDAdditionToPatientTwo
        $resStr .= " / ".$creationDateTwo->format("m/d/Y");

        //via Merge ID [MergeID-MRN]
        $resStr .= " via Merge ID ".$mrn1->getField();

        return $resStr;
    }

    public function hasSameID( $patient1, $patient2 ) {
        $status = 'valid';
        $mergedMrn1Arr = $patient1->obtainMergeMrnArr($status);
        $mergedMrn2Arr = $patient2->obtainMergeMrnArr($status);

        foreach( $mergedMrn1Arr as $mergedMrn1 ) {
            foreach( $mergedMrn2Arr as $mergedMrn2 ) {
                if( $mergedMrn1->getField() == $mergedMrn2->getField() ) {
                    return true;
                }
            }
        }

        return false;
    }

    //If not equal, copy the MRN with the oldest timestamp of the ones available from
    // one patient with the type of "Merge ID" to the second patient as a new MRN
    public function copyOldestMrnToSecondPatient( $user, $patient1, $mergedMrn1, $patient2, $mergedMrn2 ) {
        if( $mergedMrn1->getCreationdate() > $mergedMrn2->getCreationdate() ) {
            //$mergedMrn2 is the oldest (earliest) one
            $oldestMrnId = $mergedMrn2->getField();
            $secondPatient = $patient1;
        } else {
            //$mergedMrn1 is the oldest (earliest) one
            $oldestMrnId = $mergedMrn1->getField();
            $secondPatient = $patient2;
        }

        $newMrn = $this->createPatientMergeMrn($user,$secondPatient,$oldestMrnId);

        //if( $newMrn instanceof PatientMrn ) {
            //$newMrn->setField($oldestMrnId);
            //$secondPatient->addMrn($newMrn);
        //}

        return $newMrn;
    }

    public function getJsonEncodedPatient( $patient ) {

        $status = 'valid';
        $fieldnameArr = array('patlastname','patfirstname','patmiddlename','patsuffix','patsex');

        //to get a single field only use obtainStatusField
        $mrnRes = $patient->obtainStatusField('mrn', $status);
        $dobRes = $patient->obtainStatusField('dob', $status);

        //values: patient vs encounters
        //Show the "Valid" values for First Name, Last Name, etc from the encounter (not from patient object).
        // If there are multiple "Valid" values, show the ones with the most recent time stamp.

        $fieldnameResArr = $patient->obtainSingleEncounterValues($fieldnameArr,$status);

        $firstNameRes = $fieldnameResArr['patfirstname']; //$patient->obtainStatusField('firstname', $status);
        $middleNameRes = $fieldnameResArr['patmiddlename'];  //$patient->obtainStatusField('middlename', $status);
        $lastNameRes = $fieldnameResArr['patlastname']; //$patient->obtainStatusField('lastname', $status);
        $suffixRes = $fieldnameResArr['patsuffix'];   //$patient->obtainStatusField('suffix', $status);
        $sexRes = $fieldnameResArr['patsex'];    //$patient->obtainStatusField('sex', $status);

        $contactinfo = $patient->obtainPatientContactinfo("Patient's Primary Contact Information");

//        if( $patient->isMasterMergeRecord() ) {
//            $masterStr = "+";
//        } else {
//            $masterStr = "";
//        }

        $patientInfo = array(
            'id' => $patient->getId(),
            'mrntype' => $mrnRes->getKeytype()->getId(),
            'mrntypestr' => $mrnRes->getKeytype()->getName(),
            'mrn' => $mrnRes->getField(),
            'dob' => $dobRes."",

            'lastname' => (($lastNameRes) ? $lastNameRes->getField() : null),  //$lastNameRes->getField(),
            'lastnameStatus' => (($lastNameRes) ? $lastNameRes->getStatus() : null),
            //'lastnameStatus' => 'alias',

            'firstname' => (($firstNameRes) ? $firstNameRes->getField() : null),  //$firstNameStr,
            'firstnameStatus' => (($firstNameRes) ? $firstNameRes->getStatus() : null),

            'middlename' => (($middleNameRes) ? $middleNameRes->getField() : null), //$middleNameRes->getField(),
            'middlenameStatus' => (($middleNameRes) ? $middleNameRes->getStatus() : null),

            'suffix' => (($suffixRes) ? $suffixRes->getField() : null),   //$suffixRes->getField(),
            'suffixStatus' => (($suffixRes) ? $suffixRes->getStatus() : null),

            'sex' => (($sexRes) ? $sexRes->getId() : null),    //$sexRes->getId(),
            'sexstr' => $sexRes."",

            'contactinfo' => $contactinfo,

            'mergedPatientsInfo' => NULL,

            'masterPatientId' => NULL,

            'patientInfoStr' => "Patient ID# ".$patient->getId().": "    //.$masterStr.": "//testing
        );

        return $patientInfo;
    }

    //set master patient: create a new, valid masterMergeRecord and set all others to invalid
    public function setMasterPatientRecord( $patients, $masterMergeRecordId, $provider ) {

        $sourcesystem = $this->em->getRepository('OlegUserdirectoryBundle:SourceSystemList')->findOneByName("ORDER Call Log Book");
        if( !$sourcesystem ) {
            $msg = 'Source system not found by name ORDER Call Log Book';
            throw new \Exception($msg);
            //return $msg;
        }

        //add all merged patients
        $patients = $this->getAllMergedPatients($patients);

        $ids = array();

        foreach( $patients as $patient ) {

            $ids[] = $patient->getId();

            //invalidate all merge master records objects
            $patient->invalidateMasterMergeRecord('invalid');

            //create a new merge record object with new timestamp and creator
            if( $masterMergeRecordId == $patient->getId() ) {
                //$status = 'valid', $provider = null, $source = null
                $masterMergeRecord = new PatientMasterMergeRecord('valid',$provider,$sourcesystem);
                $masterMergeRecord->setField(true);
                $patient->addMasterMergeRecord($masterMergeRecord);
            }

            //$msg .= $patient->getId().": before patient mrn count=".count($patient->getMrn())."<br>";
            //testing
//                    foreach( $patient->getMrn() as $mrn ) {
//                        $msg .= $patient->getId().": before MRNID=".$mrn->getID()." mrn=".$mrn->obtainOptimalName()."; status=".$mrn->getStatus()."<br>";
//                    }

            //save patients to DB
            $this->em->persist($patient);
            //$msg .= $patient->getId().": after patient mrn count=".count($patient->getMrn())."<br>";
        }

        return $ids;
    }

    public function getAllMergedPatients( $patients, $mergeMrnsArr=array(), $masterFirst=true ) {

        $existingPatientIds = array();
        foreach( $patients as $patient ) {
            $existingPatientIds[] = $patient->getId();
        }
        //$existingPatientIds = null;

        $resPatients = new ArrayCollection();

        foreach( $patients as $patient ) {

            //echo "!!!checkPatient=".$patient->getId()."<br>";
            //continue;

            if( !$resPatients->contains($patient) ) {

                $resPatients->add($patient);

                //set master patient as the first record
                if( $masterFirst ) {
                    if( $patient->isMasterMergeRecord() ) {

                        //get current first element
                        $firstPatient = $resPatients->get(0);

                        //set master patient as the first element
                        $resPatients->set(0,$patient);

                        //add the original first element to the end
                        if( $firstPatient ) {
                            if( !$resPatients->contains($firstPatient) ) {
                                $resPatients->add($firstPatient);
                            }
                        }
                    }
                }

            }

            //get valid mrns
            $mergeMrns = $patient->obtainMergeMrnArr('valid');

            foreach( $mergeMrns as $mergeMrn ) {

                $mid = $mergeMrn->getField();

                if( in_array($mid,$mergeMrnsArr) ) {
                    //this MID has already processed => skip it
                } else {
                    //echo "process MID=".$mid."<br>";
                    $mergeMrnsArr[] = $mid;
                    $resPatients = $this->getMergedPatients($mid, $resPatients, $existingPatientIds);

                    //recursive call
                    $resPatients = $this->getAllMergedPatients($resPatients,$mergeMrnsArr);
                }

            }

        }//foreach

        //foreach( $resPatients as $resPatient ) {
        //    echo "###resPatient=".$resPatient->getId()."<br>";
        //}
        //echo "<br><br>";

        return $resPatients;
    }

    public function getMasterRecordPatients( $patients ) {
        foreach( $patients as $patient ) {
            if( $patient->isMasterMergeRecord() ) {
                return $patient;
            }
        }
        return null;
    }

    public function getMergeInfo( $patient ) {
        $mergedMrnArr = $patient->obtainMergeMrnArr("valid");
        $str = "";
        foreach( $mergedMrnArr as $mergedMrn ) {
            $str .= "Merge ID ".$mergedMrn->getField().", merged by " . $mergedMrn->getProvider() . " on " . $mergedMrn->getCreationdate()->format('m/d/Y');
        }
        return $str;
    }

    //check for orphans for the same MRN ID for each valid MRN ID for this patient.
    //If there is only one sibling with the same valid MRN ID, then this sibling is orphan.
    //un-merge this orphan patient:
    // 1) invalidate Merge MRN object
    // 2) invalidate all merge master records for this orphan patient
    // 3) if removed patient is the linked node (holding MID for different chains, i.e. 1,2,3)
    //
    // 1) A*-B(MID1) C*-D(MID2)
    // 2) Merge B and C (B - master record)
    // 3) Result: A-B*-C(MID1) and C-D(MID2); node - C(MID1,MID2)
    // 4) E*-F(MID3)
    // 5) Merge C and E (B - master record; find a way to display to choose master record)
    // 6) Result: A-B*-C-E(MID1) C-D(MID2) E-F(MID3); node - C(MID1,MID2), E(MID2,MID3)
    // 7) Un-merge C: Un-merging C in this case should merge A, B, D, E, F by copying the oldest time-stamped Merge ID to E and ...
    // 8) a) Find all patients with MID1,2
    // 8) b) Add not existing MID2 to the first node (D)
    public function processOrphans( $patient ) {

        //get valid mrns
        $mergeMrns = $patient->obtainMergeMrnArr('valid');

        foreach( $mergeMrns as $mergeMrn ) {
            echo "<br><br>Merge ID ".$mergeMrn->getField().":<br>";

            $patients = $this->getMergedPatients($mergeMrn->getField(), null, array($patient->getId()));

            foreach( $patients as $thisPatient ) {
                echo "Patient ID ".$thisPatient->getId().":<br>";
            }

        }

    }

}