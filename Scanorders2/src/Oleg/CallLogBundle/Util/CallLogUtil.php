<?php

namespace Oleg\CallLogBundle\Util;
use Oleg\OrderformBundle\Entity\MrnType;
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

        $nextKey = $this->em->getRepository('OlegOrderformBundle:Patient')->getNextNonProvided($patient,null,null,"MERGE-ID");

        //convert NOMRNPROVIDED-0000000002 to MERGE-ID-0000000002
        //$nextKey = str_replace("NOMRNPROVIDED","",$nextKey);
        //$nextKey = "MERGE-ID".$nextKey;
        //echo "nextKey=".$nextKey."<br>";
        //exit('1');

        return $nextKey;
    }

    public function addGenerateMergeMrnToPatient( $patient, $autoGeneratedMergeMrn, $provider ) {
        //$securityUtil = $this->get('order_security_utility');
        //$this->addMrn( new PatientMrn($status,$provider,$sourcesystem) );

//        //Source System: ORDER Call Log Book
//        $sourcesystem = $this->em->getRepository('OlegUserdirectoryBundle:SourceSystemList')->findOneByName("ORDER Call Log Book");
//        if( !$sourcesystem ) {
//            $msg = 'Source system not found by name ORDER Call Log Book';
//            //throw new \Exception($msg);
//            return $msg;
//        }
//
//        $status = 'valid';
//        $newMrn = new PatientMrn($status,$provider,$sourcesystem);
//
//        //set ID
//        $newMrn->setField($autoGeneratedMergeMrn);
//
//        //set keytype MrnType "Merge ID"
//        $keyTypeMergeID = $this->em->getRepository('OlegOrderformBundle:MrnType')->findOneByName("Merge ID");
//        if( !$keyTypeMergeID ) {
//            $msg = 'MrnType not found by name Merge ID';
//            //throw new \Exception($msg);
//            return $msg;
//        }
//        $newMrn->setKeytype($keyTypeMergeID);

        $newMrn = $this->createPatientMergeMrn($provider,$patient,$autoGeneratedMergeMrn);
        if( !($newMrn instanceof PatientMrn) ) {
            return $newMrn; //this is an error message
        }

        //set ID
        //$newMrn->setField($autoGeneratedMergeMrn);

        //$patient->addMrn($newMrn);

        return $patient;
    }

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
            $this->em->persist($newMrn);
        }

        if( count($patientMrns) > 1 ) {
            foreach( $patientMrns as $patientMrn ) {
                if( $patientMrn->getField() == $mrnId && $patientMrn->getStatus() == 'invalid' ) {
                    $newMrn = $patientMrn;
                    $newMrn->setStatus($status);
                    $this->em->persist($newMrn);
                    break;
                }
            }
        }

        if( count($patientMrns) == 1 ) {
            if( $patientMrns[0]->getField() == $mrnId && $patientMrns[0]->getStatus() == 'invalid' ) {
                $newMrn = $patientMrns[0];
                $newMrn->setStatus($status);
                $this->em->persist($newMrn);
            }
        }

        if( !$newMrn ) {
            $msg = 'PatientMrn has not been created. Found patientMrns count='.count($patientMrns);
            return $msg;
        }

        return $newMrn;
    }

    public function getMergedPatients( $mergedPatients, $mergeId, $existingPatientIds=null ) {

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

        foreach( $patients as $patient ) {
            $mergedPatients[] = $patient;
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

            'mergedPatientsInfo' => NULL
        );

        return $patientInfo;
    }

}