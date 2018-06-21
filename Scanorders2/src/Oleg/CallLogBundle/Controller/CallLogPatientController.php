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

/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 8/30/2016
 * Time: 12:19 PM
 */

namespace Oleg\CallLogBundle\Controller;


use Oleg\CallLogBundle\Form\CalllogListPreviousEntriesFilterType;
use Oleg\CallLogBundle\Form\CalllogPatientType;
use Oleg\OrderformBundle\Entity\Encounter;
use Oleg\OrderformBundle\Entity\Patient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oleg\OrderformBundle\Controller\PatientController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


///**
// * CallLog Patient controller.
// *
// * @Route("/patient")
// */
class CallLogPatientController extends PatientController {

    /**
     * Finds and displays a Patient entity.
     *
     * @Route("/patient/info/{id}", name="calllog_patient_show", options={"expose"=true})
     * @Method("GET")
     * @Template("OlegOrderformBundle:Patient:new.html.twig")
     */
    public function showAction( Request $request, $id )
    {

        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_CALLLOG_USER') ){
            return $this->redirect( $this->generateUrl('calllog-nopermission') );
        }

        ini_set('memory_limit', '5120M');

        $showtreedepth = 2;

        $params = array(
            'sitename' => $this->container->getParameter('calllog.sitename'),
            'datastructure' => 'datastructure-patient',
            'tracker' => 'tracker',
            'editpath' => 'calllog_patient_edit',
            'show-tree-depth' => $showtreedepth
        );

        return $this->showPatient($request,$id,$params);
    }

    /**
     * Displays a form to view an existing Patient entity by mrn.
     * Test 'show-tree-depth': http://localhost/order/call-log-book/patient/view-patient-record?mrn=testmrn-1&mrntype=16&show-tree-depth=2
     *
     * @Route("/patient/view-patient-record", name="calllog_patient_view_by_mrn", options={"expose"=true})
     * @Method("GET")
     * @Template("OlegOrderformBundle:Patient:new.html.twig")
     */
    public function viewPatientByMrnAction( Request $request )
    {
        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_CALLLOG_USER') ){
            return $this->redirect( $this->generateUrl('calllog-nopermission') );
        }

        ini_set('memory_limit', '5120M');

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userSecUtil = $this->get('user_security_utility');
        $em = $this->getDoctrine()->getManager();

        $mrntype = trim($request->get('mrntype'));
        $mrn = trim($request->get('mrn'));
        $showtreedepth = trim($request->get('show-tree-depth'));

        $extra = array();
        $extra["keytype"] = $mrntype;
        $validity = array('valid','reserved');
        $single = false;

        $institution = $userSecUtil->getCurrentUserInstitution($user);
        $institutions = array();
        $institutions[] = $institution->getId();

        $patients = $em->getRepository('OlegOrderformBundle:Patient')->findOneByIdJoinedToField($institutions,$mrn,"Patient","mrn",$validity,$single,$extra);

        if( count($patients) > 1 ) {
            $patient = null;
            $patientArr = array();
            foreach( $patients as $thisPatient ) {
                if( $thisPatient->obtainValidKeyfield() ) {
                    //we should return a single result, but we got multiple entity, so return the first valid key one.
                    $patient = $thisPatient;
                }
                $patientArr[] = $patient->obtainPatientInfoSimple();
            }
            if( !$patient ) {
                $patient = $patients[0];
            }
            $this->get('session')->getFlashBag()->add(
                'pnotify-error',
                'Multiple patients found with mrn ' . $mrn . ". Displayed is the first patient with a valid mrn. Found " . count($patients) . " patients: <hr>" . implode("<hr>",$patientArr)
            );
        }

        if( count($patients) == 1 ) {
            $patient = $patients[0];
        }

        if( !$patient || !$patient->getId() ) {
            $this->get('session')->getFlashBag()->add(
                'pnotify-error',
                'No patient found with mrn ' . $mrn
            );
            return $this->redirect($this->generateUrl('calllog_home'));
        }


        if( !$showtreedepth ) {
            $showtreedepth = 2;
        }
        //echo "showtreedepth=".$showtreedepth."<br>";

        $params = array(
            'sitename' => $this->container->getParameter('calllog.sitename'),
            'datastructure' => 'datastructure-patient',
            //'datastructure' => 'datastructure', //images are shown only if the 'datastructure' parameters is set to 'datastructure'
            'tracker' => 'tracker',
            'editpath' => 'calllog_patient_edit',
            'show-tree-depth' => $showtreedepth
        );

        return $this->showPatient($request,$patient->getId(),$params);
    }


    /**
     * Displays a form to edit an existing Patient entity by id.
     *
     * @Route("/patient/{id}/edit", name="calllog_patient_edit", options={"expose"=true})
     * @Method("GET")
     * @Template("OlegOrderformBundle:Patient:new.html.twig")
     */
    public function editAction( Request $request, $id )
    {
        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_CALLLOG_USER') ){
            return $this->redirect( $this->generateUrl('calllog-nopermission') );
        }

        $showtreedepth = 2;

        $params = array(
            'sitename' => $this->container->getParameter('calllog.sitename'),
            'datastructure' => 'datastructure-patient',
            'tracker' => 'tracker',
            'updatepath' => 'calllog_patient_update',
            'showPlus' => 'showPlus',
            'show-tree-depth' => $showtreedepth
        );

        $formResArr = $this->editPatient($request,$id,$params);

        $formResArr['title'] = $formResArr['title'] . " | Call Log Book";

        return $formResArr;
    }

    /**
     * Displays a form to edit an existing Patient entity by mrn.
     *
     * ////Route("/patient/edit-by-mrn/{mrn}/{mrntype}", name="calllog_patient_edit_by_mrn", options={"expose"=true})
     *
     * @Route("/patient/edit-patient-record", name="calllog_patient_edit_by_mrn", options={"expose"=true})
     * @Method("GET")
     * @Template("OlegOrderformBundle:Patient:new.html.twig")
     */
    public function editPatientByMrnAction( Request $request )
    {
        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_CALLLOG_USER') ){
            return $this->redirect( $this->generateUrl('calllog-nopermission') );
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userSecUtil = $this->get('user_security_utility');
        $em = $this->getDoctrine()->getManager();

        $mrntype = trim($request->get('mrntype'));
        $mrn = trim($request->get('mrn'));
        $showtreedepth = trim($request->get('show-tree-depth'));

        $extra = array();
        $extra["keytype"] = $mrntype;
        $validity = array('valid','reserved');
        $single = false;

        //$institution = $em->getRepository('OlegUserdirectoryBundle:Institution')->findOneByName("All Institutions");
        //$institution = $em->getRepository('OlegUserdirectoryBundle:Institution')->findOneByName("Weill Cornell Medical College");
        //$institution = $em->getRepository('OlegUserdirectoryBundle:Institution')->findOneByName("New York-Presbyterian Hospital");
        $institution = $userSecUtil->getCurrentUserInstitution($user);
        $institutions = array();
        $institutions[] = $institution->getId();

        $patients = $em->getRepository('OlegOrderformBundle:Patient')->findOneByIdJoinedToField($institutions,$mrn,"Patient","mrn",$validity,$single,$extra);
        //echo "found patient=".$entity."<br>";
        //exit("edit patient by mrn $mrn $mrntype");
        //$patients = $em->getRepository('OlegOrderformBundle:Patient')->findAll(); //testing

        if( count($patients) > 1 ) {
            $patient = null;
            $patientArr = array();
            foreach( $patients as $thisPatient ) {
                if( $thisPatient->obtainValidKeyfield() ) {
                    //we should return a single result, but we got multiple entity, so return the first valid key one.
                    $patient = $thisPatient;
                }
                $patientArr[] = $patient->obtainPatientInfoSimple();
            }
            if( !$patient ) {
                $patient = $patients[0];
            }
            $this->get('session')->getFlashBag()->add(
                'pnotify-error',
                'Multiple patients found with mrn ' . $mrn . ". Displayed is the first patient with a valid mrn. Found " . count($patients) . " patients: <hr>" . implode("<hr>",$patientArr)
            );
        }

        if( count($patients) == 1 ) {
            $patient = $patients[0];
        }

        if( !$patient || !$patient->getId() ) {
            $this->get('session')->getFlashBag()->add(
                'pnotify-error',
                'No patient found with mrn ' . $mrn
            );
            return $this->redirect($this->generateUrl('calllog_home'));
        }

//        $this->get('session')->getFlashBag()->add(
//            'pnotify',
//            'Ok!'
//        );

        if( !$showtreedepth ) {
            $showtreedepth = 2;
        }
        //echo "showtreedepth=".$showtreedepth."<br>";

        $params = array(
            'sitename' => $this->container->getParameter('calllog.sitename'),
            'datastructure' => 'datastructure-patient',
            'tracker' => 'tracker',
            'updatepath' => 'calllog_patient_update',
            'showPlus' => 'showPlus',
            'show-tree-depth' => $showtreedepth
        );

        return $this->editPatient($request,$patient->getId(),$params);
    }

    /**
     * Edits an existing Patient entity.
     *
     * @Route("/patient/{id}/edit", name="calllog_patient_update", options={"expose"=true})
     * @Method("POST")
     * @Template("OlegOrderformBundle:Patient:new.html.twig")
     */
    public function updateAction( Request $request, $id )
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_SCANORDER_SUBMITTER') &&
            false === $this->get('security.authorization_checker')->isGranted('ROLE_SCANORDER_ORDERING_PROVIDER')
        ) {
            return $this->redirect($this->generateUrl('scan-nopermission'));
        }

        $params = array(
            'sitename' => $this->container->getParameter('calllog.sitename'),
            'datastructure' => 'datastructure-patient',
            'tracker' => 'tracker',
            'updatepath' => 'calllog_patient_update',
            'showpath' => 'calllog_patient_show',
        );

        return $this->updatePatient($request,$id,$params);  //$datastructure,$showpath,$updatepath);
    }


    /**
     * Complex Patient List
     * @Route("/patient-list/{listid}/{listname}", name="calllog_complex_patient_list")
     * @Template("OlegCallLogBundle:PatientList:complex-patient-list.html.twig")
     */
    public function complexPatientListAction(Request $request, $listid, $listname)
    {
        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_CALLLOG_USER') ){
            return $this->redirect( $this->generateUrl('calllog-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $securityUtil = $this->get('order_security_utility');
        $user = $this->get('security.token_storage')->getToken()->getUser();

        //$listname
        $listnameArr = explode('-',$listname);
        $listname = implode(' ',$listnameArr);
        $listname = ucwords($listname);
        //echo "list: name=$listname; id=$listid <br>";

        //get list name by $listname, convert it to the first char as Upper case and use it to find the list in DB
        //for now use the mock page complex-patient-list.html.twig

        //get list by id
        //$patientList = $em->getRepository('OlegOrderformBundle:PatientListHierarchy')->find($listid);
        //$patients = $patientList->getChildren();

        $patientGroup = $em->getRepository('OlegOrderformBundle:PatientListHierarchyGroupType')->findOneByName('Patient');

        $parameters = array();

        $repository = $em->getRepository('OlegOrderformBundle:PatientListHierarchy');
        $dql = $repository->createQueryBuilder("list");

        $dql->leftJoin("list.patient", "patient");
        $dql->leftJoin("patient.lastname", "lastname");
        $dql->leftJoin("patient.firstname", "firstname");
        $dql->leftJoin("patient.mrn", "mrn");

        $dql->where("list.parent = :parentId AND list.organizationalGroupType = :patientGroup");
        $parameters['parentId'] = $listid;
        $parameters['patientGroup'] = $patientGroup->getId();

        $dql->andWhere("list.type = 'user-added' OR list.type = 'default'");

        $query = $em->createQuery($dql);
        $query->setParameters($parameters);
        //echo "sql=".$query->getSql()."<br>";

        $limit = 30;
        $paginator  = $this->get('knp_paginator');
        $patients = $paginator->paginate(
            $query,
            $request->query->get('page', 1), /*page number*/
            //$request->query->getInt('page', 1),
            $limit,      /*limit per page*/
            array(
                'defaultSortFieldName' => 'patient.id',
                'defaultSortDirection' => 'DESC',
                'wrap-queries'=>true
            )
        );
        //$patients = $query->getResult();

        //echo "patients=".count($patients)."<br>";

        $patientListHierarchyObject = $em->getRepository('OlegUserdirectoryBundle:PlatformListManagerRootList')->findOneByName('Patient List Hierarchy');

        //create patient form for "Add Patient" section
        $status = 'invalid';
        $system = $securityUtil->getDefaultSourceSystem($this->container->getParameter('calllog.sitename'));
        $newPatient = new Patient(true,$status,$user,$system);
        $newEncounter = new Encounter(true,'dummy',$user,$system);
        $newPatient->addEncounter($newEncounter);
        $patientForm = $this->createPatientForm($newPatient);

        //src/Oleg/CallLogBundle/Resources/views/PatientList/complex-patient-list.html.twig
        return array(
            'patientListId' => $listid,
            'patientNodes' => $patients,
            'title' => $listname,   //"Complex Patient List",
            'platformListManagerRootListId' => $patientListHierarchyObject->getId(),
            'patientForm' => $patientForm->createView(),
            'cycle' => 'new',
            'formtype' => 'add-patient-to-list',
            'mrn' => null,
            'mrntype' => null
        );
    }

    /**
     * @Route("/patient/remove-patient-from-list/{patientId}/{patientListId}", name="calllog_remove_patient_from_list")
     */
    public function removePatientFromListAction(Request $request, $patientId, $patientListId) {
        if (false == $this->get('security.authorization_checker')->isGranted('ROLE_CALLLOG_USER')) {
            return $this->redirect($this->generateUrl('calllog-nopermission'));
        }

        $em = $this->getDoctrine()->getManager();

        $patientList = $em->getRepository('OlegOrderformBundle:PatientListHierarchy')->find($patientListId);
        if( !$patientList ) {
            throw new \Exception( "PatientListHierarchy not found by id $patientListId" );
        }

        //remove patient from the list
        $repository = $em->getRepository('OlegOrderformBundle:PatientListHierarchy');
        $dql = $repository->createQueryBuilder("list");

        $dql->leftJoin("list.patient", "patient");

        $dql->where("patient = :patientId");
        $parameters['patientId'] = $patientId;

        $query = $em->createQuery($dql);
        $query->setParameters($parameters);
        $patients = $query->getResult();

        $msgArr = array();
        foreach( $patients as $patientNode ) {
            $patientNode->setType('disabled');
            //TODO: remove this patient from all CalllogEntryMessage (addPatientToList, patientList): find all message with this patient where addPatientToList is true and set to false?
            $msgArr[] = $patientNode->getPatient()->obtainPatientInfoTitle();
        }
        $em->flush();

        $msg = implode('<br>',$msgArr);
        if( $msg ) {
            $msg = "Removed patient:<br>" . $msg;
        }

        $this->get('session')->getFlashBag()->add(
            'pnotify',
            $msg
        );

        $listName = $patientList->getName()."";
        $listNameLowerCase = str_replace(" ","-",$listName);
        $listNameLowerCase = strtolower($listNameLowerCase);

        return $this->redirect($this->generateUrl('calllog_complex_patient_list',array('listname'=>$listNameLowerCase,'listid'=>$patientListId)));
    }



    /**
     * @Route("/patient/add-patient-to-list/{patientListId}/{patientId}", name="calllog_add_patient_to_list")
     * @Route("/patient/add-patient-to-list-ajax/{patientListId}/{patientId}", name="calllog_add_patient_to_list_ajax", options={"expose"=true})
     *
     * @Template("OlegCallLogBundle:PatientList:complex-patient-list.html.twig")
     */
    public function addPatientToListAction(Request $request, $patientListId, $patientId) {
        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_CALLLOG_USER') ){
            return $this->redirect( $this->generateUrl('calllog-nopermission') );
        }

        $calllogUtil = $this->get('calllog_util');
        $em = $this->getDoctrine()->getManager();

        $patientList = $em->getRepository('OlegOrderformBundle:PatientListHierarchy')->find($patientListId);
        if( !$patientList ) {
            throw new \Exception( "PatientListHierarchy not found by id $patientListId" );
        }

        //add patient from the list
        $patient = $em->getRepository('OlegOrderformBundle:Patient')->find($patientId);
        if( !$patient ) {
            throw new \Exception( "Patient not found by id $patientId" );
        }

        //exit("before adding patient");
        $newListElement = $calllogUtil->addPatientToPatientList($patient,$patientList);

        if( $newListElement ) {
            //Patient added to the Pathology Call Complex Patients list
            $msg = "Patient " . $newListElement->getPatient()->obtainPatientInfoTitle() . " has been added to the " . $patientList->getName() . " list";
            $pnotify = 'pnotify';
        } else {
            $msg = "Patient " . $patient->obtainPatientInfoTitle() . " HAS NOT BEEN ADDED to the " . $patientList->getName() . " list. Probably, this patient already exists in this list.";
            $pnotify = 'pnotify-error';
        }

        $this->get('session')->getFlashBag()->add(
            $pnotify,
            $msg
        );

        //return OK
        if( $request->get('_route') == "calllog_add_patient_to_list_ajax" ) {
            $res = "OK";
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($res));
            return $response;
        }

        $listName = $patientList->getName()."";
        $listNameLowerCase = str_replace(" ","-",$listName);
        $listNameLowerCase = strtolower($listNameLowerCase);

        return $this->redirect($this->generateUrl('calllog_complex_patient_list',array('listname'=>$listNameLowerCase,'listid'=>$patientListId)));
    }


    //calllog-list-previous-entries
    /**
     * @Route("/patient/list-previous-entries/", name="calllog-list-previous-entries", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function listPatientPreviousEntriesAction(Request $request)
    {
        if( false === $this->get('security.authorization_checker')->isGranted('ROLE_USER') ) {
            return $this->redirect( $this->generateUrl('employees-nopermission') );
        }

        $calllogUtil = $this->get('calllog_util');
        $em = $this->getDoctrine()->getManager();

        $title = "Previous Entries";
        $template = null;
        $filterMessageCategory = null;

        $messageId = $request->query->get('messageid');

        $patientid = $request->query->get('patientid');
        //echo "patientid=".$patientid."<br>";

        $patient = $em->getRepository('OlegOrderformBundle:Patient')->find($patientid);
        if( !$patient ) {
            throw new \Exception( "Patient not found by id $patientid" );
        }

        //get linked patients
        $mergedPatients = $calllogUtil->getAllMergedPatients( array($patient) );

        //get master patient If the entered patient is linked to another
        if( count($mergedPatients) > 1 ) {
            $masterPatient = $calllogUtil->getMasterRecordPatients($mergedPatients);
            if ($masterPatient) {
                if ($masterPatient->getId() != $patientid) {
                    //not master record
                    //"Previous Entries for FirstNameOfMasterRecord LastNameOfMasterRecord (DOB: DateOfBirthOfMasterRecord, MRNTypeOfMasterRecord: MRNofMasterRecord)
                    $title = "Previous entries for all patients linked with the master patient record of ".$masterPatient->obtainPatientInfoSimple();
                }
            }
        }

        //get patient ids
        $patientIdArr = array();
        foreach( $mergedPatients as $mergedPatient ) {
            $patientIdArr[] = $mergedPatient->getId();
        }
        if( count($patientIdArr) > 0 ) {
            $patientIds = implode(",", $patientIdArr);
        } else {
            throw new \Exception( "Patient array does not have any patients. count=".count($patientIdArr) );
        }

        $messageCategoryId = $request->query->get('type');
        //if ( strval($messageCategoryId) != strval(intval($messageCategoryId)) ) {
            //echo "Your variable is not an integer";
            //$messageCategoryId = null;
        //} else {
            //$filterMessageCategory = $em->getRepository('OlegOrderformBundle:MessageCategory')->find($messageCategoryId);
            //echo "filter=".$filterMessageCategory."<br>";
        //}
        if( !$messageCategoryId || $messageCategoryId == "null" || $messageCategoryId == "undefined" ) {
            $messageCategoryId = null;
        }

        //echo "patientid=".$patientid."<br>";
        //echo "messageCategory=".$messageCategory."<br>";

        $testing = $request->query->get('testing');

        //$showUserArr = $this->showUser($userid,$this->container->getParameter('employees.sitename'),false);
        //$template = $this->render('OlegUserdirectoryBundle:Profile:edit_user_only.html.twig',$showUserArr)->getContent();

        //child nodes of "Pathology Call Log Entry"
        //$messageCategoriePathCall = $em->getRepository('OlegOrderformBundle:MessageCategory')->findOneByName("Pathology Call Log Entry");
        $messageCategoriePathCall = $calllogUtil->getDefaultMessageCategory();
        $messageCategories = array();
        if( $messageCategoriePathCall ) {
            //$messageCategories = $messageCategoriePathCall->printTreeSelectList();
            //#51: Show them in the same way as the "Message Type" dropdown menu on the homepage shows its values.
            $messageCategories = $messageCategoriePathCall->printTreeSelectListIncludingThis(true,array("default","user-added"));
        }
        //print_r($messageCategories);

        $filterform = null;
        if(0) {
            $params = array(
                'messageCategory' => $messageCategoryId,
                'messageCategories' => $messageCategories //for previous entries page
            );
            $filterform = $this->createForm(CalllogListPreviousEntriesFilterType::class, null, array(
                'method' => 'GET',
                'form_custom_value' => $params
            ));
            //$filterform->submit($request);
            $filterform->handleRequest($request);

            //$messageCategoryId = $filterform['messageCategory']->getData();
            //echo "messageCategoryId=".$messageCategoryId."<br>";
        }

        //////////////// find messages ////////////////
        //$this->testSelectMessagesWithMaxVersion($patientid);

        $queryParameters = array();
        $repository = $em->getRepository('OlegOrderformBundle:Message');
        $dql = $repository->createQueryBuilder('message');
        $dql->select('message');

        //$dql->select('message, MAX(message.version) AS HIDDEN max_version');
        //$dql->groupBy('message.oid');
        //$dql->addGroupBy('message.version');

        $dql->leftJoin("message.messageStatus","messageStatus");
        $dql->leftJoin("message.messageCategory","messageCategory");
        $dql->leftJoin("message.provider","provider");
        $dql->leftJoin("message.patient","patient");
        $dql->leftJoin("message.editorInfos","editorInfos");

        $dql->leftJoin("message.signeeInfo","signeeInfo");
        $dql->leftJoin("signeeInfo.modifiedBy","signee");

        $dql->leftJoin("message.encounter","encounter");
        $dql->leftJoin("encounter.referringProviders","referringProviders");
        $dql->leftJoin("referringProviders.field","referringProviderWrapper");
        $dql->leftJoin("encounter.attendingPhysicians","attendingPhysicians");
        $dql->leftJoin("attendingPhysicians.field","attendingPhysicianWrapper");

        $dql->orderBy("message.orderdate","DESC");
        $dql->addOrderBy("editorInfos.modifiedOn","DESC");

        //$dql->where("patient.id = :patientId");
        //$queryParameters['patientId'] = $patientid;

        $dql->where('patient.id IN (:patientIds)');
        $queryParameters['patientIds'] = $patientIds;

        //$dql->andWhere("(SELECT messages, MAX(messages.version) AS maxversion FROM OlegOrderformBundle:Message WHERE messages.id=message.id)");

        //We can use the fact that latest version messages have status not "Deleted"
        $dql->andWhere("messageStatus.name != :deletedMessageStatus");
        $queryParameters['deletedMessageStatus'] = "Deleted";

        if( $messageCategoryId ) {
            $dql->andWhere("messageCategory.name=:messageCategoryId");
            $queryParameters['messageCategoryId'] = $messageCategoryId;
        }

        //TODO: Show only the most recent version for each message (if a message has been edited/amended 5 times, show only the message with message version "6").

        //TODO: 7- If the entered patient is linked to another AND is NOT the master patient record,
        // change the title of the accordion to
        // "Previous Entries for FirstNameOfMasterRecord LastNameOfMasterRecord (DOB: DateOfBirthOfMasterRecord, MRNTypeOfMasterRecord: MRNofMasterRecord).
        // Clicking "Re-enter patient" in the Patient Info accordion should re-set the title of the accordion to "Previous Entries" (remove the patient name/info).

        $query = $em->createQuery($dql);
        $query->setParameters($queryParameters);

        $limit = 10;
        //$query->setMaxResults($limit);

        //echo "query=".$query->getSql()."<br>";

//        $paginator  = $this->get('knp_paginator');
//        $messages = $paginator->paginate(
//            $query,
//            $this->get('request')->query->get('page', 1), /*page number*/
//            //$request->query->getInt('page', 1),
//            $limit      /*limit per page*/
//        );

        $messages = $query->getResult();

        //echo "messages count=".count($messages)."<br>";
        //foreach( $messages as $message ) {
        //    echo "Message=".$message->getMessageOidVersion()."<br>";
        //}
        //exit('testing');
        //////////////// find messages ////////////////

        if( count($messages) > $limit ) {
            $mrnRes = $patient->obtainStatusField('mrn', "valid");
            $mrntype = $mrnRes->getKeytype()->getId();
            $mrn = $mrnRes->getField();
            $linkUrl = $this->generateUrl(
                "calllog_home",
                array(
                    'filter[mrntype]'=>$mrntype,
                    'filter[search]'=>$mrn,
                    'filter[messageStatus]'=>"All except deleted",
                ),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $showAllMsg = "showing the last $limit entries, click here to view all";
            $href = '<a href="'.$linkUrl.'" target="_blank">'.$showAllMsg.'</a>';
            $title = $title . " (" . $href . ")";
        }

        $params = array(
            'filterform' =>  ($filterform ? $filterform->createView() : null), //$filterform->createView(),
            'route_path' => $request->get('_route'),
            'messages' => $messages,
            'title' => $title,
            'limit' => $limit,
            'messageid' => $messageId
            //'testing' => true
        );
        $htmlPage = $this->render('OlegCallLogBundle:PatientList:patient_entries.html.twig',$params);

        //testing
        if( $testing ) {
            return $htmlPage;
        }

        $template = $htmlPage->getContent();

        $json = json_encode($template);
        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    //NOT USED
    public function testSelectMessagesWithMaxVersion( $patientid ) {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('OlegOrderformBundle:Message');
        $query = $repository->createQueryBuilder('s');
        $query->select('s, MAX(s.version)');
        $query->leftJoin("s.patient","patient");
        $query->where('patient.id = :patient')->setParameter('patient', $patientid);
        $query->groupBy('s');
        //$query->addGroupBy('s.version');
        //$query->setMaxResults($limit);
        $query->orderBy('s.oid', 'ASC');

        $messagesComplex = $query->getQuery()->getResult();
        //print_r($messagesComplex);
        echo "messagesComplex count=".count($messagesComplex)."<br>";

        $messages = $messagesComplex['s'];
        echo "messages=".$messages."<br>";
        echo "messages count=".count($messages)."<br>";

        foreach( $messages as $message ) {
            echo "Message=".$message->getMessageOidVersion()."<br>";
        }
        exit('testing');
    }
    //NOT USED
    public function testSelectMessagesWithMaxVersion_OLD($patientid) {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('
            SELECT message, message.version AS HIDDEN
            FROM OlegOrderformBundle:Message message
            INNER JOIN message.patient patient'.
            ' LEFT OUTER JOIN OlegOrderformBundle:Message b ON message.id = b.id AND message.version < b.version'.
            ' WHERE patient.id = :patient
            ORDER BY message.oid ASC'
        )->setParameter('patient', $patientid);

        echo "query=".$query->getSql()."<br>";

        $messages = $query->getResult();

        echo "messages count=".count($messages)."<br>";

        foreach( $messages as $message ) {
            echo "Message=".$message->getMessageOidVersion()."<br>";
        }

        exit("testing");
    }


    public function createPatientForm($patient, $mrntype=null, $mrn=null) {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $calllogUtil = $this->get('calllog_util');

        if( !$mrntype ) {
            //$mrntype = 1;
            $defaultMrnType = $calllogUtil->getDefaultMrnType();
            $mrntype = $defaultMrnType->getId();
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

        $form = $this->createForm(CalllogPatientType::class, $patient, array(
            'form_custom_value' => $params,
            'form_custom_value_entity' => $patient
        ));

        return $form;
    }
}