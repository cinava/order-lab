<?php

namespace Oleg\CallLogBundle\Controller;

use Oleg\CallLogBundle\Form\CalllogPatientType;
use Oleg\OrderformBundle\Entity\Encounter;
use Oleg\OrderformBundle\Entity\EncounterPatfirstname;
use Oleg\OrderformBundle\Entity\EncounterPatlastname;
use Oleg\OrderformBundle\Entity\EncounterPatmiddlename;
use Oleg\OrderformBundle\Entity\EncounterPatsex;
use Oleg\OrderformBundle\Entity\EncounterPatsuffix;
use Oleg\OrderformBundle\Entity\MrnType;
use Oleg\OrderformBundle\Entity\Patient;
use Oleg\OrderformBundle\Entity\PatientDob;
use Oleg\OrderformBundle\Entity\PatientFirstName;
use Oleg\OrderformBundle\Entity\PatientLastName;
use Oleg\OrderformBundle\Entity\PatientMiddleName;
use Oleg\OrderformBundle\Entity\PatientMrn;
use Oleg\OrderformBundle\Entity\PatientSex;
use Oleg\OrderformBundle\Entity\PatientSuffix;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DataQualityController extends CallEntryController
{


    /**
     * @Route("/merge-patient-records", name="calllog_merge_patient_records", options={"expose"=true})
     * @Template("OlegCallLogBundle:DataQuality:merge-records.html.twig")
     */
    public function mergePatientAction(Request $request)
    {

        $user = $this->get('security.context')->getToken()->getUser();
        $securityUtil = $this->get('order_security_utility');
        $em = $this->getDoctrine()->getManager();

        $title = "Merge Patient Records";

        $system = $securityUtil->getDefaultSourceSystem($this->container->getParameter('calllog.sitename'));
        $status = 'valid';
        $cycle = 'show';

        $patient1 = new Patient(true,$status,$user,$system);

        $triggerSearch = 0;
        $mrntype = trim($request->get('mrn-type'));
        $mrnid = trim($request->get('mrn'));
        if( $mrntype && $mrnid ) {
            $mrnPatient1 = $patient1->obtainStatusField('mrn', $status);
            $mrnPatient1->setKeytype($mrntype);
            $mrnPatient1->setField($mrnid);
            $triggerSearch = 1;
        }
        //echo "triggerSearch=".$triggerSearch."<br>";

        $encounter1 = new Encounter(true,$status,$user,$system);
        $patient1->addEncounter($encounter1);
        $form1 = $this->createPatientForm($patient1,$mrntype,$mrnid);


        $patient2 = new Patient(true,$status,$user,$system);
        $encounter2 = new Encounter(true,$status,$user,$system);
        $patient2->addEncounter($encounter2);
        $form2 = $this->createPatientForm($patient2,$mrntype,$mrnid);


        return array(
            //'entity' => $entity,
            'form1' => $form1->createView(),
            'form2' => $form2->createView(),
            'cycle' => $cycle,
            'title' => $title,
            'triggerSearch' => $triggerSearch,
            'mrntype' => $mrntype
        );
    }

    /**
     * @Route("/merge-patient-records-ajax", name="calllog_merge_patient_records_ajax", options={"expose"=true})
     */
    public function mergePatientAjaxAction(Request $request)
    {

        $user = $this->get('security.context')->getToken()->getUser();
        //$securityUtil = $this->get('order_security_utility');
        $calllogUtil = $this->get('calllog_util');
        $em = $this->getDoctrine()->getManager();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $id1 = trim($request->get('id1'));
        $id2 = trim($request->get('id2'));
        $masterMergeRecordId = trim($request->get('masterMergeRecordId'));
        echo "id1=$id1; id2=$id2 <br>";
        exit('exit');

        $msg = "";
        //$res = null;
        $merged = false;
        $error = false;
        $patient1 = null;
        $patient2 = null;
        $patientsArr = array();
        $status = 'valid';

        if( $id1 ) {
            $patient1 = $this->getDoctrine()->getRepository('OlegOrderformBundle:Patient')->find($id1);
            if( !$patient1 ) {
                $msg .= "Patient 1 not found by id=".$id1;
                $error = true;
            }
            //$res = $patient1->getId();
            $patientsArr[] = $patient1;
        } else {
            $msg .= "Patient 1 id is invalid";
        }

        if( $id2 ) {
            $patient2 = $this->getDoctrine()->getRepository('OlegOrderformBundle:Patient')->find($id2);
            if( !$patient2 ) {
                $msg .= "Patient 2 not found by id=".$id2;
                $error = true;
            }
            //$res = $patient2->getId();
            $patientsArr[] = $patient2;
        } else {
            $msg .= "Patient 2 id is invalid";
        }

        //testing
//        foreach( $patientsArr as $patient ) {
//            foreach( $patient->getMrn() as $mrn ) {
//                $msg .= $patient->getId().": init MRNID=".$mrn->getID()." mrn=".$mrn->obtainOptimalName()."; status=".$mrn->getStatus()."<br>";
//            }
//        }


        if( !$error && $patient1 && $patient2 ) {
            $mergedMrn1 = $patient1->obtainMergeMrn($status);
            $mergedMrn2 = $patient2->obtainMergeMrn($status);

            //a) If neither of the patients has an MRN of type="Merge ID"
            //Add the generated MRN to both patients with an MRN Type of "Merge ID"
            //MergeID: auto-generate unique, but prepend a prefix "MERGE" (ID MERGE123456)
            if( !$mergedMrn1 && !$mergedMrn2 ) {
                //$msg .= 'Case (a): neither of the patients has an MRN of type="Merge ID"<br>';

                $merged = true;
                $autoGeneratedMergeMrn = $calllogUtil->autoGenerateMergeMrn($patient1);

                $patRes = $calllogUtil->addGenerateMergeMrnToPatient($patient1,$autoGeneratedMergeMrn,$user);
                if( !($patRes instanceof Patient) ) {
                    $msg .= $patRes."<br>";
                    $error = true;
                }

                $patRes = $calllogUtil->addGenerateMergeMrnToPatient($patient2,$autoGeneratedMergeMrn,$user);
                if( !($patRes instanceof Patient) ) {
                    $msg .= $patRes."<br>";
                    $error = true;
                }
            }

            //b) If one of the patients has one MRN of type = "Merge ID",
            // copy that MRN with the type of "Merge ID" to the second patient as a new MRN.
            //(c) If one of the patients has more than one (two, three, etc) MRNs of type= "Merge ID",
            // copy the MRN with the oldest timestamp of the ones available with the type of "Merge ID" to the second patient as a new MRN
            if( (!$mergedMrn1 && $mergedMrn2) || ($mergedMrn1 && !$mergedMrn2) ) {
                //$msg .= 'Case (b,c): one of the patients has one MRN of type = "Merge ID".<br>';

                if( $mergedMrn1 ) {
                    $msg .= " Patient with ID ".$id1." has Merged MRN. "."<br>";

                    $newMrn = $calllogUtil->createPatientMergeMrn($user,$patient2,$mergedMrn1->getField());
                    if( $newMrn instanceof PatientMrn ) {
                        $merged = true;
                        //$newMrn->setField($mergedMrn1->getField());
                        //$patient2->addMrn($newMrn);
                    } else {
                        $msg .= $newMrn."<br>";
                    }
                }

                if( $mergedMrn2 ) {
                    $msg .= " Patient with ID ".$id2." has Merged MRN. "."<br>";

                    $newMrn = $calllogUtil->createPatientMergeMrn($user,$patient1,$mergedMrn2->getField());
                    if( $newMrn instanceof PatientMrn ) {
                        $merged = true;
                        //$newMrn->setField($mergedMrn2->getField());
                        //$patient1->addMrn($newMrn);
                    } else {
                        $msg .= $newMrn."<br>";
                    }
                }

            }

            //If both patients have at least one MRN of type = "Merge ID"
            if( $mergedMrn1 && $mergedMrn2 ) {
                //$msg .= 'Case (d,e,f): If both patients have at least one MRN of type = "Merge ID". ';

                //(d) If both patients have (only) one MRN of type = "Merge ID" each and they are equal to each other
                if( !$error && !$merged ) {
                    if ($patient1->hasOnlyOneMergeMrn($status) && $patient2->hasOnlyOneMergeMrn($status)) {
                        if ($mergedMrn1->getField() == $mergedMrn2->getField()) {
                            //"Patient Records have already been merged by FirstNameOfAuthorOfMRN LastNameofAuthorOfMRN on
                            // DateOfMergeIDAdditionToPatientOne / DateOfMergeIDAdditionToPatientTwo via Merge ID [MergeID-MRN]
                            $msg .= "Patient Records have already been merged by " . $calllogUtil->obtainSameMergeMrnInfoStr($mergedMrn1, $mergedMrn2)."<br>";
                            $error = true;
                        } else {
                            //If not equal, copy the MRN with the oldest (earliest) timestamp of the ones available from one
                            // patient with the type of "Merge ID" to the second patient as a new MRN
                            $newMrn = $calllogUtil->copyOldestMrnToSecondPatient($user, $patient1, $mergedMrn1, $patient2, $mergedMrn2);
                            if ($newMrn instanceof PatientMrn) {
                                $merged = true;
                            } else {
                                $msg .= $newMrn."<br>";
                            }
                        }
                    }//(d)
                }

                //(e) (one has 1 and the other 3), the Merge ID of one is equal to any of the Merge IDs of another
                //(f) (one has 4 and the other 3), any of the Merge IDs of one is equal to any of the Merge IDs of another
                //(e,f): check if MRNs have overlapped (the same) MRN ID.
                if( !$error && !$merged ) {
                    if( $calllogUtil->hasSameID($patient1, $patient2) ) {
                        $msg .= "Patient Records have already been merged by " . $calllogUtil->obtainSameMergeMrnInfoStr($mergedMrn1, $mergedMrn2)."<br>";
                        $error = true;
                    } else {
                        //If not equal, copy the MRN with the oldest timestamp of the ones available from
                        // one patient with the type of "Merge ID" to the second patient as a new MRN
                        $newMrn = $calllogUtil->copyOldestMrnToSecondPatient($user, $patient1, $mergedMrn1, $patient2, $mergedMrn2);
                        if( $newMrn instanceof PatientMrn ) {
                            $merged = true;
                        } else {
                            $msg .= $newMrn."<br>";
                        }
                    }
                }//(e,f)

            }

            if( !$error && $merged ) {

                //merge: set master patient
                $ids = $calllogUtil->setMasterPatientRecord($patientsArr,$masterMergeRecordId,$user);

                $em->flush();

                //testing
//                foreach( $ids as $patientId ) {
//                    $patient = $this->getDoctrine()->getRepository('OlegOrderformBundle:Patient')->find($patientId);
//                    foreach( $patient->getMrn() as $mrn ) {
//                        $msg .= $patient->getId().": after MRNID=".$mrn->getID()." mrn=".$mrn->obtainOptimalName()."; status=".$mrn->getStatus()."<br>";
//                    }
//                }

                //"You have successfully merged patient records: Master Patient ID #."
                $msg .= "You have successfully merged patient records (IDs ".implode(", ",$ids).") with Master Patient ID # $masterMergeRecordId."."<br>";
            }

            if( !$error && !$merged ) {
                $msg .= "No merged cases found."."<br>";
            }

            //$result['res'] = 'OK';
        }


        //get master record
        $masterMergeRecordPatient = null;
        if( $masterMergeRecordId == $id1 ) {
            $masterMergeRecordPatient = $patient1;
        }
        if( $masterMergeRecordId == $id2 ) {
            $masterMergeRecordPatient = $patient2;
        }
        //event log
        $userSecUtil = $this->container->get('user_security_utility');
        $eventType = "Merged Patient";
        $event = "Merged patients with ID#" . $id1 . " and ID# " . $id2 .":"."<br>";
        $event = $event . $msg;
        $userSecUtil->createUserEditEvent($this->container->getParameter('calllog.sitename'), $event, $user, $masterMergeRecordPatient, $request, $eventType);


        $result = array();
        $result['error'] = $error;
        $result['msg'] = $msg;

        $response->setContent(json_encode($result));
        return $response;
    }






    /**
     * @Route("/un-merge-patient-records", name="calllog_unmerge_patient_records", options={"expose"=true})
     * @Route("/set-master-patient-record", name="calllog_set_master_patient_record", options={"expose"=true})
     *
     * @Template("OlegCallLogBundle:DataQuality:un-merge-records.html.twig")
     */
    public function unmergePatientAction(Request $request)
    {

        $user = $this->get('security.context')->getToken()->getUser();
        $securityUtil = $this->get('order_security_utility');
        //$em = $this->getDoctrine()->getManager();

        $system = $securityUtil->getDefaultSourceSystem($this->container->getParameter('calllog.sitename'));
        $status = 'valid';
        $cycle = 'show';

        $route = $request->get('_route');

        if( $route == "calllog_unmerge_patient_records" ) {
            $title = "Un-merge Patient Records";
            $formtype = 'unmerge';
        } else {
            $title = "Set Master Patient Record";
            $formtype = 'set-master-record';
        }

        $patient1 = new Patient(true,$status,$user,$system);

        $triggerSearch = 0;
        $mrntype = trim($request->get('mrn-type'));
        $mrnid = trim($request->get('mrn'));
        if( $mrntype && $mrnid ) {
            $mrnPatient1 = $patient1->obtainStatusField('mrn', $status);
            $mrnPatient1->setKeytype($mrntype);
            $mrnPatient1->setField($mrnid);
            $triggerSearch = 1;
        }

        $encounter1 = new Encounter(true,$status,$user,$system);
        $patient1->addEncounter($encounter1);
        $form1 = $this->createPatientForm($patient1,$mrntype,$mrnid);

//        $patient2 = new Patient(true,$status,$user,$system);
//        $encounter2 = new Encounter(true,$status,$user,$system);
//        $patient2->addEncounter($encounter2);
//        $form2 = $this->createPatientForm($patient2);

        return array(
            //'entity' => $entity,
            'form1' => $form1->createView(),
            //'form2' => $form2->createView(),
            'cycle' => $cycle,
            'title' => $title,
            'formtype' => $formtype,
            'triggerSearch' => $triggerSearch,
            'mrntype' => $mrntype
        );
    }



    /**
     * @Route("/set-master-patient-record-ajax", name="calllog_set_master_patient_record_ajax", options={"expose"=true})
     */
    public function setMasterPatientAjaxAction(Request $request)
    {

        $user = $this->get('security.context')->getToken()->getUser();
        //$securityUtil = $this->get('order_security_utility');
        $calllogUtil = $this->get('calllog_util');
        $em = $this->getDoctrine()->getManager();

        //$system = $securityUtil->getDefaultSourceSystem(); //'scanorder';
        //$status = 'valid';
        //$cycle = 'new';

        $error = false;
        $msg = "";

        $patientId = trim($request->get('masterId'));
        //echo "patientId=".$patientId."<br>";

        //set master patient
        if( $patientId ) {
            $patientObject = $this->getDoctrine()->getRepository('OlegOrderformBundle:Patient')->find($patientId);
            $patients = $calllogUtil->getAllMergedPatients(array($patientObject));
            $ids = $calllogUtil->setMasterPatientRecord($patients, $patientId, $user);
            $em->flush();
            $msg .= "Patient with ID $patientId has been set as a Master Record Patient; Patients affected ids=".implode(", ",$ids);
        } else {
            $error = true;
            $msg .= "Patient ID is not provided; patientId=".$patientId;
        }

        $result = array();
        $result['error'] = $error;
        $result['msg'] = $msg;

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($result));
        return $response;
    }

    /**
     * @Route("/unmerge-patient-records-ajax", name="calllog_unmerge_patient_records_ajax", options={"expose"=true})
     */
    public function unmergePatientAjaxAction(Request $request)
    {

        $user = $this->get('security.context')->getToken()->getUser();
        //$securityUtil = $this->get('order_security_utility');
        $calllogUtil = $this->get('calllog_util');
        $em = $this->getDoctrine()->getManager();

        //$system = $securityUtil->getDefaultSourceSystem(); //'scanorder';
        //$status = 'valid';
        //$cycle = 'new';

        $error = false;
        $msg = "";

        $masterId = trim($request->get('masterId'));
        $patientIds = trim($request->get('patientIds'));
        //echo "masterId=".$masterId."<br>";
        //echo "patientIds=".$patientIds."<br>";
        //exit('1');

        $patientIdsArr = explode(",",$patientIds);

        $unmergedPatients = array();

        //1) get all patients as $unmergedPatients array
        foreach( $patientIdsArr as $patientId ) {

            //$patientIdStrArr = explode("-mergeid-",$patientIdStr);
            //$patientId = $patientIdStrArr[0];
            //$patientMergeId = $patientIdStrArr[1];
            //echo "patientId=".$patientId."<br>";
            //continue;

            //find patient object
            $patient = $this->getDoctrine()->getRepository('OlegOrderformBundle:Patient')->find($patientId);
            if( !$patient ) {
                $error = true;
                $msg .= ' Patient not found by ID# '.$patientId.'<br>';
                break;
            }

            $unmergedPatients[] = $patient;

        }//foreach

        //2) check and change (if required) the masterRecord
        $processMasterRes = $calllogUtil->processMasterRecordPatients($unmergedPatients,$masterId,$user);
        if( $processMasterRes['error'] ) {
            $error = true;
        }
        $msg .= $processMasterRes['msg']."<br>";

        //3) process each un-merged patient
        foreach( $unmergedPatients as $unmergedPatient ) {
            // A) if only one merged patient exists with this mergeId (except this patient) => orphan
            // B) if multiple patients found (except this patient) => copy all merged IDs to the master patient in the chain
            $processUnmergePatientRes = $calllogUtil->processUnmergedPatient($unmergedPatient,$masterId,$user);
            if( $processUnmergePatientRes['error'] ) {
                $error = true;
            } else {
                $em->persist($unmergedPatient);
                //$em->persist($mergeMrn);
                $em->flush(); //testing
            }
            $msg .= "Unmerged Patient ID# ".$unmergedPatient->getId()." ".$unmergedPatient->getFullPatientName() . "; " . $processUnmergePatientRes['msg']."<br>";
        }

        if( count($unmergedPatients) > 0 ) {
            $userSecUtil = $this->container->get('user_security_utility');
            $eventType = "Un-Merged Patient";
            $event = "Un-Merged " . count($unmergedPatients) . " Patient(s) with a master patient " . $masterId.":";
            $event = $event . $msg;
            $userSecUtil->createUserEditEvent($this->container->getParameter('calllog.sitename'), $event, $user, $unmergedPatients, $request, $eventType);
        }

        $result = array();
        $result['error'] = $error;
        $result['msg'] = $msg;
        //exit('exit:'.$msg);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($result));
        return $response;
    }

    /**
     * @Route("/edit-patient-record", name="calllog_edit_patient_record", options={"expose"=true})
     * @Template("OlegCallLogBundle:DataQuality:edit-patient-record.html.twig")
     */
    public function editPatientAction(Request $request)
    {

        $user = $this->get('security.context')->getToken()->getUser();
        $securityUtil = $this->get('order_security_utility');
        //$em = $this->getDoctrine()->getManager();

        $system = $securityUtil->getDefaultSourceSystem($this->container->getParameter('calllog.sitename'));
        $status = 'valid';
        $cycle = 'show';

        $title = "Edit Patient Info";
        $formtype = 'edit-patient';

        $patient1 = new Patient(true,$status,$user,$system);

        $triggerSearch = 0;
        $mrntype = trim($request->get('mrn-type'));
        $mrnid = trim($request->get('mrn'));
        if( $mrntype && $mrnid ) {
            $mrnPatient1 = $patient1->obtainStatusField('mrn', $status);
            $mrnPatient1->setKeytype($mrntype);
            $mrnPatient1->setField($mrnid);
            $triggerSearch = 1;
        }

        $encounter1 = new Encounter(true,$status,$user,$system);
        $patient1->addEncounter($encounter1);
        $form1 = $this->createPatientForm($patient1,$mrntype,$mrnid);

        return array(
            'form1' => $form1->createView(),
            'cycle' => $cycle,
            'title' => $title,
            'formtype' => $formtype,
            'triggerSearch' => $triggerSearch,
            'mrntype' => $mrntype
        );
    }

    /**
     * @Route("/edit-patient-record-ajax", name="calllog_edit_patient_record_ajax", options={"expose"=true})
     */
    public function editPatientAjaxAction(Request $request)
    {

        $result = array();
        $result['error'] = false;
        $result['msg'] = "";


        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $patientId = trim($request->get('patientId'));
        $mrn = trim($request->get('mrn'));
        $mrntype = trim($request->get('mrntype'));
        $dob = trim($request->get('dob'));
        $lastname = trim($request->get('lastname'));
        $firstname = trim($request->get('firstname'));
        $middlename = trim($request->get('middlename'));
        $suffix = trim($request->get('suffix'));
        $sex = trim($request->get('sex'));
        //print_r($allgets);
        echo "patientId=".$patientId."; mrn=".$mrn."<br>";





        $result['error'] = true;
        $result['msg'] = "Under construction.";

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($result));
        return $response;
    }


    public function createPatientForm($patient, $mrntype=null, $mrn=null) {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        ////////////////////////
//        $query = $em->createQueryBuilder()
//            ->from('OlegOrderformBundle:MrnType', 'list')
//            ->select("list.id as id, list.name as text")
//            ->orderBy("list.orderinlist","ASC");
//        $query->where("list.type = :type OR ( list.type = 'user-added' AND list.name != :autogen)");
//        $query->setParameters( array('type' => 'default','autogen' => 'Auto-generated MRN') );
//        //echo "query=".$query."<br>";
//
//        $mrntypes = $query->getQuery()->getResult();
//        foreach( $mrntypes as $mrntype ) {
//            echo "mrntype=".$mrntype['id'].":".$mrntype['text']."<br>";
//        }
        ///////////////////////

        if( !$mrntype ) {
            $mrntype = 1;
        }

        $params = array(
            'cycle' => 'new',
            'user' => $user,
            'em' => $em,
            'container' => $this->container,
            //'alias' => true
            'type' => null,
            'mrntype' => intval($mrntype),
            'mrn' => $mrn,
            'formtype' => 'call-entry',
            'complexLocation' => false,
            'alias' => false
        );

        $form = $this->createForm(new CalllogPatientType($params, $patient), $patient);

        return $form;
    }


}
