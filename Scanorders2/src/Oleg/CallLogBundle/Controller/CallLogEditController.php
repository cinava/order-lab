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

namespace Oleg\CallLogBundle\Controller;

use Oleg\OrderformBundle\Entity\EncounterAttendingPhysician;
use Oleg\OrderformBundle\Entity\EncounterReferringProvider;
use Oleg\UserdirectoryBundle\Entity\ModifierInfo;
use Oleg\UserdirectoryBundle\Entity\Spot;
use Oleg\UserdirectoryBundle\Entity\Tracker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class CallLogEditController extends CallEntryController
{

    /**
     * @Route("/delete/{messageOid}/{messageVersion}", name="calllog_delete")
     * @Template("OlegUserdirectoryBundle:Default:about.html.twig")
     * @Method("GET")
     */
    public function deleteMessageAction(Request $request, $messageOid, $messageVersion)
    {
        if( false == $this->get('security.context')->isGranted("ROLE_CALLLOG_USER") ){
            return $this->redirect( $this->generateUrl('calllog-nopermission') );
        }

        //$userServiceUtil = $this->get('user_service_utility');
        //$user = $this->get('security.context')->getToken()->getUser();
        //$securityUtil = $this->get('order_security_utility');
        //$userSecUtil = $this->get('user_security_utility');
        //$orderUtil = $this->get('scanorder_utility');
        //$calllogUtil = $this->get('calllog_util');
        $em = $this->getDoctrine()->getManager();

        $message = $em->getRepository('OlegOrderformBundle:Message')->findByOidAndVersion($messageOid,$messageVersion);
        if( !$message ) {
            throw new \Exception( "Message is not found by oid ".$messageOid." and version ".$messageVersion );
        }

        $msg = $this->deleteMessage( $message, "delete link", $request );

        $this->get('session')->getFlashBag()->add(
            'pnotify',
            $msg
        );

        return $this->redirect($this->generateUrl('calllog_home'));
    }


    /**
     * @Route("/un-delete/{messageOid}/{messageVersion}", name="calllog_undelete")
     * @Template("OlegUserdirectoryBundle:Default:about.html.twig")
     * @Method("GET")
     */
    public function unDeleteMessageAction(Request $request, $messageOid, $messageVersion)
    {
        if( false == $this->get('security.context')->isGranted("ROLE_CALLLOG_USER") ){
            return $this->redirect( $this->generateUrl('calllog-nopermission') );
        }

        $userServiceUtil = $this->get('user_service_utility');
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $userSecUtil = $this->get('user_security_utility');

        $message = $em->getRepository('OlegOrderformBundle:Message')->findByOidAndVersion($messageOid,$messageVersion);
        if( !$message ) {
            throw new \Exception( "Message is not found by oid ".$messageOid." and version ".$messageVersion );
        }

        $messageStatusPrior = $message->getMessageStatusPrior();

        if( !$messageStatusPrior ) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Prior entry status is undefined, therefore, no modification has been performed.'
            );
            return $this->redirect($this->generateUrl('calllog_home'));
        }

        $message->setMessageStatus($messageStatusPrior);

        $em->flush($message);

        //Entry 123 for PatientFirstName PatientLastName (DOB: MM/DD/YYYY) submitted on
        // [submitted timestamp in MM/DD/YYYY HH:MM 24HR format] by SubmitterFirstName SubmitterLastName, MD successfully
        // un-deleted and status set to [name of status]
        $patientInfoStr = $message->getPatientNameMrnInfo();
        if( $patientInfoStr ) {
            $patientInfoStr = "for ".$patientInfoStr;
        }
        $msg = "Message Entry ID#".$message->getMessageOidVersion()." $patientInfoStr submitted on ".$userServiceUtil->getSubmitterInfo($message).
            " successfully un-deleted and status set to ".$messageStatusPrior;
//        $this->get('session')->getFlashBag()->add(
//            'notice',
//            $msg
//        );
        $this->get('session')->getFlashBag()->add(
            'pnotify',
            $msg
        );

        $eventType = "Call Log Book Entry Undeleted";
        $userSecUtil->createUserEditEvent($this->container->getParameter('calllog.sitename'), $msg, $user, $message, $request, $eventType);

        return $this->redirect($this->generateUrl('calllog_home'));
    }

    public function deleteMessage( $message, $actionStr, $request, $testing=false ) {
        $em = $this->getDoctrine()->getManager();
        $userServiceUtil = $this->get('user_service_utility');
        $userSecUtil = $this->get('user_security_utility');
        $user = $this->get('security.context')->getToken()->getUser();

        if( $message->getMessageStatus() != "Deleted" ) {
            $message->setMessageStatusPrior($message->getMessageStatus());
        }

        $messageStatus = $em->getRepository('OlegOrderformBundle:MessageStatusList')->findOneByName("Deleted");
        if( !$messageStatus ) {
            throw new \Exception( "Message Status is not found by name '"."Deleted"."'" );
        }

        $message->setMessageStatus($messageStatus);

        if( !$testing ) {
            $em->flush($message);
        }

        //"Entry 123 for PatientFirstName PatientLastName (DOB: MM/DD/YYYY) submitted on
        // [submitted timestamp in MM/DD/YYYY HH:MM 24HR format] by SubmitterFirstName SubmitterLastName, MD successfully deleted
        $patientInfoStr = $message->getPatientNameMrnInfo();
        if( $patientInfoStr ) {
            $patientInfoStr = "for ".$patientInfoStr;
        }
        $msg = "Message Entry ID#".$message->getMessageOidVersion()." $patientInfoStr submitted on ".$userServiceUtil->getSubmitterInfo($message)." successfully deleted by ".$actionStr;

        //testing
        //$msg = $msg . " DB ID#".$message->getID();

        //Event Log
        $eventType = "Call Log Book Entry Deleted";
        $userSecUtil->createUserEditEvent($this->container->getParameter('calllog.sitename'), $msg, $user, $message, $request, $eventType);

        return $msg;
    }


    /**
     * Get Call Log Entry Message Edit page
     * @Route("/entry/edit/{messageOid}/{messageVersion}", name="calllog_callentry_edit")
     * @Route("/entry/amend/{messageOid}/{messageVersion}", name="calllog_callentry_amend")
     * @Route("/entry/edit-latest-encounter/{messageOid}/{messageVersion}", name="calllog_callentry_edit_latest_encounter")
     * @Route("/entry/amend-latest-encounter/{messageOid}/{messageVersion}", name="calllog_callentry_amend_latest_encounter")
     * @Method("GET")
     * @Template("OlegCallLogBundle:CallLog:call-entry-edit.html.twig")
     */
    public function getCallLogEntryAction(Request $request, $messageOid, $messageVersion=null)
    {

        if (false == $this->get('security.context')->isGranted('ROLE_CALLLOG_USER')) {
            return $this->redirect($this->generateUrl('calllog-nopermission'));
        }

        //$userSecUtil = $this->get('user_security_utility');
        $calllogUtil = $this->get('calllog_util');
        $securityUtil = $this->get('order_security_utility');
        $userServiceUtil = $this->get('user_service_utility');
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        //$title = "Call Log Entry";
        $formtype = "call-entry";

        $route = $request->get('_route');
        if( strpos($route, "calllog_callentry_edit") !== false ) {
            $cycle = "edit";
        }
        if( strpos($route, "calllog_callentry_amend") !== false ) {
            $cycle = "amend";
        }

        //$patientId = trim($request->get('patientId'));
        //$nowStr = trim($request->get('nowStr'));
        //echo "patientId=".$patientId."<br>";
        //echo "nowStr=".$nowStr."<br>";
        //$messageId = 142; //154; //testing

        if( !is_numeric($messageVersion) || !$messageVersion ) {
            $messageLatest = $em->getRepository('OlegOrderformBundle:Message')->findByOidAndVersion($messageOid);

            if( !$messageLatest && !$messageVersion ) {
                //handle case with th real DB id: http://localhost/order/call-log-book/entry/view/267
                $messageLatest = $em->getRepository('OlegOrderformBundle:Message')->find($messageOid);
            }

            if( $messageLatest ) {
                return $this->redirect($this->generateUrl($route, array(
                    'messageOid' => $messageLatest->getOid(),
                    'messageVersion' => $messageLatest->getVersion()
                )));
            }

            throw new \Exception( "Latest Message is not found by oid ".$messageOid );
        }

        $message = $em->getRepository('OlegOrderformBundle:Message')->findByOidAndVersion($messageOid,$messageVersion);
        if( !$message ) {
            throw new \Exception( "Message is not found by oid ".$messageOid." and version ".$messageVersion );
        }

        //Replace encounter with the latest encounter.
        //Used replaced encounter for latest url only to show message's encounter, not patient's encounter!.
        if( strpos($route, "_latest_encounter") !== false ) {
            $encounter = $message->getEncounter()->first();
            if( !$calllogUtil->isLatestEncounterVersion($encounter) ) {
                $latestEncounter = $em->getRepository('OlegOrderformBundle:Encounter')->findLatestVersionEncounter($encounter);
                if( $latestEncounter ) {
                    echo "Original id=".$encounter->getId()."; version=".$encounter->getVersion()." => latestEncounter: id=".$latestEncounter->getId()."; version=".$latestEncounter->getVersion()."<br>";
                    //clear encounter
                    $message->clearEncounter();
                    //add encounter to the message
                    $message->addEncounter($latestEncounter);
                }
            }
        }

        $messageInfo = "Entry ID ".$message->getMessageOidVersion()." submitted on ".$userServiceUtil->getSubmitterInfo($message); // . " | Call Log Book";
        //echo "messageInfo=".$messageInfo."<br>";
        //exit('1');
        if (count($message->getPatient()) > 0 ) {
            $mrnRes = $message->getPatient()->first()->obtainStatusField('mrn', "valid");
            $mrntype = $mrnRes->getKeytype()->getId();
            $mrn = $mrnRes->getField();

            //LastName, FirstName, MiddleName | MRN Type: MRN | DOB: MM/DD/YY |
            // Entry ID XXX submitted on MM/DD/YYYY at HH:MM by SubmitterFirstName SubmitterLastName, MD | Call Log Book
            $title = $message->getPatient()->first()->obtainPatientInfoTitle('valid',null,false);
            $title = $title . " | ".$messageInfo;

        } else {
            $mrntype = null;
            $mrn = null;

            $title = $messageInfo;
        }

        ////////////////// add missing encounter fields //////////////////
        $system = $securityUtil->getDefaultSourceSystem($this->container->getParameter('calllog.sitename'));

//        $existingEncounter = null;
//        foreach( $message->getEncounter() as $encounter ) {
//            //echo "encounter ID=".$encounter->getId()."; status=".$encounter->getStatus()."<br>";
//            //if( !$encounter->getId() ) {
//                if( $encounter->getStatus() == 'valid' ) {
//                    $existingEncounter = $encounter;
//                    break;
//                }
//            //}
//        }

        //message should have only one attached encounter
        if( count($message->getEncounter()) > 1 ) {
            throw new \Exception('Message must have only one attached encounter. Number of attached encounters '.count($message->getEncounter()));
        }
        $existingEncounter = $message->getEncounter()->first();
        //echo "existingEncounter=".$existingEncounter->getId()."<br>";

        //ReferringProvider
        if( count($existingEncounter->getReferringProviders()) == 0 ) {
            $encounterReferringProvider = new EncounterReferringProvider('valid', $user, $system);
            $existingEncounter->addReferringProvider($encounterReferringProvider);
        }
        //AttendingPhysician
        if( count($existingEncounter->getAttendingPhysicians()) == 0 ) {
            $encounterAttendingPhysician = new EncounterAttendingPhysician('valid', $user, $system);
            $existingEncounter->addAttendingPhysician($encounterAttendingPhysician);
        }
        //Location: entity.tracker.spots
//        if( !$existingEncounter->getTracker() ) {
//            $tracker = new Tracker();
//            $existingEncounter->setTracker($tracker);
//        }
//        if( count($existingEncounter->getTracker()->getSpots()) == 0 ) {
//            $spotEntity = new Spot($user,$system);
//            $existingEncounter->getTracker()->addSpot($spotEntity);
//        }
        if( !$existingEncounter->getTracker() ) {
            $withdummyfields = true;
            //$locationTypePrimary = null;
            $encounterLocationType = $em->getRepository('OlegUserdirectoryBundle:LocationTypeList')->findOneByName("Encounter Location");
            if (!$encounterLocationType) {
                throw new \Exception('Location type is not found by name Encounter Location');
            }
            $locationName = null;   //""; //"Encounter's Location";
            $spotEntity = null;
            $removable = 0;
            $existingEncounter->addContactinfoByTypeAndName($user, $system, $encounterLocationType, $locationName, $spotEntity, $withdummyfields, $em, $removable);
        }
        ////////////////// EOF add missing encounter fields //////////////////

        //echo "patients=".count($message->getPatient())."<br>";
        $form = $this->createCalllogEntryForm($message,$mrntype,$mrn,$cycle);

        $complexPatientStr = null;
        //find record in the "Pathology Call Complex Patients" list by message object entityName, entityId
//        $mapper = array(
//            'prefix' => "Oleg",
//            'bundleName' => "CallLogBundle",
//            'className' => "PathologyCallComplexPatients",
//        );
//        $listRecord = $userSecUtil->getListByNameAndObject( $message, $mapper );
//        if( $listRecord ) {
//            //Patient was added to the "xxxxxxxx" list via this entry.
//            $complexPatientStr = "Patient was added to the Pathology Call Complex Patients list ID# ".$listRecord->getId()." via this entry:<br>".$listRecord->getName()."";
//        }
        //echo "complexStr=".$complexPatientStr."<br>";

        $class = new \ReflectionClass($message);
        $className = $class->getShortName();          //ObjectTypeText
        $classNamespace = $class->getNamespaceName(); //Oleg\UserdirectoryBundle\Entity

        //top message category id
        $formnodeTopHolderId = null;
        $categoryStr = "Pathology Call Log Entry";
        $messageCategory = $em->getRepository('OlegOrderformBundle:MessageCategory')->findOneByName($categoryStr);
        if( $messageCategory ) {
            $formnodeTopHolderId = $messageCategory->getId();
        }

        //View Previous Version(s)
        $allMessages = $em->getRepository('OlegOrderformBundle:Message')->findAllMessagesByOid($messageOid); //$messageVersion=null => all messages ordered by latest version first

        //find current (latest) message status
        $latestMessageStatus = null;
        $latestMessageLabel = null;
        $latestMessage = $em->getRepository('OlegOrderformBundle:Message')->findLatestMessageByOid(null,$allMessages);
        $latestMessageVersion = intval($latestMessage->getVersion()) + 1;
        if( $latestMessage && intval($messageVersion) != intval($latestMessage->getVersion()) ) {
            $latestMessageStatus = $latestMessage->getMessageStatus()->getName()."";
//            $latestMessageVersion = intval($latestMessage->getVersion()) + 1;
            //"Current Status of the Current Version of this message (Current Version is X, Displaying Version Y):"
            $latestMessageLabel = "Current Status of the Current Version of this message (Current Version is $messageVersion, New Displaying Version ".$latestMessageVersion."):";
        }
        //echo "messageLabel=".$latestMessageLabel."<br>";

        $latestEntryUrl = $this->generateUrl(
            $route,
            array('messageOid'=>$message->getOid(), 'messageVersion'=>'latest'),
            UrlGeneratorInterface::ABSOLUTE_URL // This guy right here
        );

        $maxEncounterVersion = $em->getRepository('OlegOrderformBundle:Encounter')->getMaxEncounterVersion($existingEncounter);
        $latestNextEncounterVersion = intval($maxEncounterVersion) + 1;

        return array(
            //'entity' => $entity,
            'form' => $form->createView(),
            'cycle' => $cycle,
            'title' => $title,
            'formtype' => $formtype,
            'triggerSearch' => 0,
            'mrn' => $mrn,
            'mrntype' => $mrntype,
            'message' => $message,
            'complexPatientStr' => $complexPatientStr,
            //'encounterid' => $encounterid
            'entityNamespace' => $classNamespace,
            'entityName' => $className,
            'entityId' => $message->getId(),
            'sitename' => $this->container->getParameter('calllog.sitename'),
            'titleheadroom' => $title,
            'formnodeTopHolderId' => $formnodeTopHolderId,
            'currentMessageStatus' => $latestMessageStatus,
            'currentMessageLabel' => $latestMessageLabel,
            'allMessages' => $allMessages,
            'currentMessageVersion' => $latestMessageVersion,
            'currentEncounterVersion' => $latestNextEncounterVersion,
            'latestEntryUrl' => $latestEntryUrl
        );
    }



    /**
     * Save/Update Call Log Entry
     * @Route("/entry/update/{messageId}/{cycle}", name="calllog_update_entry", options={"expose"=true})
     * @Template("OlegCallLogBundle:CallLog:call-entry.html.twig")
     * @Method("POST")
     */
    public function updateEntryAction(Request $request, $messageId, $cycle)
    {
        if( false == $this->get('security.context')->isGranted("ROLE_CALLLOG_USER") ){
            return $this->redirect( $this->generateUrl('calllog-nopermission') );
        }

        //exit('update entry');

        $user = $this->get('security.context')->getToken()->getUser();
        $securityUtil = $this->get('order_security_utility');
        $userSecUtil = $this->get('user_security_utility');
        $orderUtil = $this->get('scanorder_utility');
        $calllogUtil = $this->get('calllog_util');
        $em = $this->getDoctrine()->getManager();

        $testing = false;
        //$testing = true;

        //check if user has at least one institution
        $userSiteSettings = $securityUtil->getUserPerSiteSettings($user);
        if( !$userSiteSettings ) {
            $orderUtil->setWarningMessageNoInstitution($user);
            return $this->redirect( $this->generateUrl('calllog_home') );
        }
        $permittedInstitutions = $userSiteSettings->getPermittedInstitutionalPHIScope();
        if( count($permittedInstitutions) == 0 ) {
            $orderUtil->setWarningMessageNoInstitution($user);
            return $this->redirect( $this->generateUrl('calllog_home') );
        }

        $originalMessage = $em->getRepository('OlegOrderformBundle:Message')->find($messageId);
        if (!$originalMessage) {
            throw new \Exception('Original Message not found by ID ' . $messageId);
        }

        $mrn = null;
        $mrntype = null;

        $title = "Update Entry";

        $system = $securityUtil->getDefaultSourceSystem($this->container->getParameter('calllog.sitename'));
        $status = 'valid';
        $cycleForm = 'new';
        $formtype = 'call-entry';

        $message = $this->createCalllogEntryMessage($user,$permittedInstitutions,$system);

        $form = $this->createCalllogEntryForm($message,$mrntype,$mrn,$cycleForm);

        $form->handleRequest($request);

        if( $form->isSubmitted() ) {

            echo "message id=".$message->getId()."<br>";

            $msg = "No Case found. No action has been performed.";
            $institution = $userSecUtil->getCurrentUserInstitution($user);

            $patient = null;
            $encounterHolder = null;
            $patients = $message->getPatient();
            if( count($patients) > 0 ) {
                $patient = $patients->first();
                //$encounterHolder = $patient;
                echo "patient id=".$patient->getId()."<br>";
            } else {
                //$encounterHolder = $message;
            }
            $encounterHolder = $message;

//            $dummyEncounter = null;
//            $newEncounter = null;
//            //get an encounter
//            echo "encounter count=".count($encounterHolder->getEncounter())."<br>";
//            foreach( $encounterHolder->getEncounter() as $encounter ) {
//                echo "encounter ID=".$encounter->getId()."; status=".$encounter->getStatus()."; key=".$encounter->obtainValidField('number')."<br>";
//                //if( !$encounter->getId() ) {
//                    if( $encounter->getStatus() == 'valid' ) {
//                        $newEncounter = $encounter;
//                    }
//                    if( $encounter->getStatus() == 'invalid' ) {
//                        //this encounter is served only to find the patient:
//                        //copy all non-empty values from the $dummyEncounter to the $newEncounter
//                        //it must be removed from the patient
//                        $dummyEncounter = $encounter;
//                    }
//                //}
//            }

            //Use patient (if exists) or message to get edited encounter. On this stage, if patient exists, message does not have yet encounter =>
            // therefore, we can't use only message to get encounter. Use $encounterHolder.
            //For edit page we have only one encounter with patient info => use first encounter.
            // Another dummy encounter used to search patient does not exists on the edit page.
            if( $encounterHolder->getEncounter() == 1 ) {
                $newEncounter = $encounterHolder->getEncounter()->first();
            } else {
                throw new \Exception('Edit/Amend must contain only one encounter. Encounters count=' . count($encounterHolder->getEncounter()));
            }

//            //testing
//            if( $patient ) {
//                echo "###patient encounter counter=".count($patient->getEncounter())."<br>";
//                $patientEncounter = $patient->getEncounter()->first();
//                echo "### patient encounter ID=" . $patientEncounter->getId() . "<br>";
//                echo "### encounter message count=" . count($patientEncounter->getMessage()) . "<br>";
//            }
//            echo "###message encounter counter=".count($message->getEncounter())."<br>";
//            if( count($message->getEncounter()) > 0 ) {
//                $messageEncounter = $message->getEncounter()->first();
//                echo "### message encounter ID=" . $messageEncounter->getId() . "<br>";
//            }

            //set system source and user's default institution
            if( $newEncounter ) {

                $newEncounter->setSource($system);
                $newEncounter->setInstitution($institution);

                //assign generated encounter number ID
                //$key = $newEncounter->obtainAllKeyfield()->first();
                //echo $newEncounter->getId().": key=".$key."<br>";
                //$em->getRepository('OlegOrderformBundle:Encounter')->setEncounterKey($key, $newEncounter, $user);

                //increment encounter version for current encounter (keep status valid for all encounters, use version instead of status)
                $calllogUtil->incrementVersionEncounterFamily($newEncounter);

                //Remove tracker if spots/location is empty
                $tracker = $newEncounter->getTracker();
                if( $tracker ) {
                    $tracker->removeEmptySpots();
                    if ($tracker->isEmpty()) {
                        //echo "Tracker is empty! <br>";
                        $newEncounter->setTracker(null);
                    } else {
                        //echo "Tracker is not empty! <br>";
                        //check if location name is not empty
                        if ($newEncounter->getTracker()) {
                            $currentLocation = $newEncounter->getTracker()->getSpots()->first()->getCurrentLocation();
                            if (!$currentLocation->getName()) {
                                $currentLocation->setName('');
                            }
                            if (!$currentLocation->getCreator()) {
                                $currentLocation->setCreator($user);
                            }
                        }
                    }
                }
                //exit();

//                if( $patient && $dummyEncounter ) {
//                    //$dummyEncounter must be removed from the patient
//                    $patient->removeEncounter($dummyEncounter);
//                }

                //prevent creating a new location every time: if location id is provided => find location in DB and replace it with tracker->spot->location
                $calllogUtil->processTrackerLocation($newEncounter);

                //process EncounterReferringProvider: set Specialty, Phone and Email for a new userWrapper (getReferringProviders)
                $calllogUtil->processReferringProviders($newEncounter,$system);


                //clear encounter
                $message->clearEncounter();
                //add encounter to the message
                $message->addEncounter($newEncounter);

                //set message status from the form's name="messageStatus" field
                $data = $request->request->all();
                $buttonStatusObj = null;
                $buttonStatusForm = $data['messageStatusJs'];
                //echo "buttonStatusForm=".$buttonStatusForm."<br>";
                if( $buttonStatusForm ) {
                    $buttonStatusObj = $em->getRepository('OlegOrderformBundle:MessageStatusList')->findOneByName($buttonStatusForm);
                    if( $buttonStatusObj ) {

                        //if "Signed" set signed User, datetime, roles by signeeInfo
                        if( $buttonStatusObj->getName()."" == "Signed" ) {
                            if ($message->getSigneeInfo()) {
                                //echo "signee exist <br>";
                                $signeeInfo = $message->getSigneeInfo();
                                $signeeInfo->setInfo($user);
                            } else {
                                //echo "signee does exist <br>";
                                $message->setSigneeInfo(new ModifierInfo($user));
                            }
                        }

                        //if "Deleted" set signed User, datetime, roles by signeeInfo
                        if( $buttonStatusObj->getName()."" == "Deleted" ) {
                            //echo "deleted <br>";
                            $editorInfo = new ModifierInfo($user);
                            $message->addEditorInfo($editorInfo);
                        }

                        if( $buttonStatusObj->getName()."" == "Draft" ) {
                            echo "add editor: draft <br>";
                            $editorInfo = new ModifierInfo($user);
                            $message->addEditorInfo($editorInfo);
                        }

                    }
                }

                if( $message->getMessageCategory() ) {

                    //message title setMessageTitle: show the title of the form (not the message type) here, not just its ID
                    $messageTitle = $message->getMessageTitleStr();
                    $message->setMessageTitle($messageTitle);
                }

                //On the server side write in the "Versions" of the associated forms into this "Form Version" field in the same order as the Form titles+IDs
                $calllogUtil->setFormVersions($message);

                /////////////////////// Set edited message info /////////////////////
                //set OID from original message
                $message->setOid($originalMessage->getOid());
                //increment version: latest message + 1
                $latestMessage = $em->getRepository('OlegOrderformBundle:Message')->findLatestMessageByOid($originalMessage->getOid());
                $incrementedVersion = intval($latestMessage->getVersion()) + 1;
                //echo "incrementedVersion=".$incrementedVersion."<br>";
                $message->setVersion($incrementedVersion);

                if( $buttonStatusObj ) {
                    echo "set message status to " . $buttonStatusObj . "<br>";
                    //determine the new message status
                    $newMessageStatusObj = $calllogUtil->getNewMessageStatus($latestMessage->getMessageStatus(), $buttonStatusObj, $originalMessage->getOid());
                    $message->setMessageStatus($newMessageStatusObj);
                }

                //delete original message
                $this->deleteMessage( $originalMessage, $cycle." action", $request, $testing );
                /////////////////////// EOF Set edited message info /////////////////////

                if( $patient && $patient->getId() ) {
                    //CASE 1
                    echo "case 1: patient exists: create a new encounter to DB and add it to the existing patient <br>";
                    //get a new encounter without id $newEncounter
                    //                foreach( $encounter->getReferringProviders() as $referringProvider ) {
                    //                    echo "encounter referringProvider phone=".$referringProvider->getReferringProviderPhone()."<br>";
                    //                }

                    $patient = $em->getRepository('OlegOrderformBundle:Patient')->find($patient->getId());
                    $message->clearPatient();
                    $message->addPatient($patient);

                    //reset institution from the patient
                    $newEncounter->setInstitution($patient->getInstitution());

                    $patient->addEncounter($newEncounter);

                    $calllogUtil->updatePatientInfoFromEncounter($patient,$newEncounter,$user,$system );

                    if( !$testing ) {
                        $em->persist($newEncounter);
                        $em->persist($message);
                        $em->flush();
                    }

                    //add patient to the complex patient list specified by patientListTitle if the option addPatientToList is checked.
                    //do it after message is in DB and has ID
                    $calllogUtil->addToPatientLists($patient,$message,$testing);

                    //New Encounter (ID#" . $newEncounter->getId() . ")
                    $msg = " is created with Encounter number " . $newEncounter->obtainEncounterNumber() . " for the Patient with ID #" . $patient->getId();

                } else {
                    //CASE 2
                    echo "case 2: patient does not exists: create a new encounter to DB <br>";
                    //oleg_calllogbundle_patienttype[encounter][0][referringProviders][0][referringProviderPhone]

                    $newEncounter->setPatient(null);

                    //remove empty patient from message
                    if( $patient ) {
                        $message->removePatient($patient);
                    }

                    //exit('Exit Case 2');
                    if( !$testing ) {
                        $em->persist($newEncounter);
                        //$em->flush($newEncounter); //testing

                        $em->persist($message);
                        //$em->flush($message); //testing
                        $em->flush();
                    }

                    //New Encounter (ID#" . $newEncounter->getId() . ")
                    $msg = " is created with Encounter number " . $newEncounter->obtainEncounterNumber();
                }

                //set encounter as message's input
                //$message->addInputObject($newEncounter);
                //$em->persist($message);
                //$em->flush($message);

                //process form nodes
                $formNodeUtil = $this->get('user_formnode_utility');
                $formNodeUtil->processFormNodes($request,$message->getMessageCategory(),$message,$testing); //testing
                //exit('after formnode');

//                /////////////////////// Set edited message info /////////////////////
//                //set OID from original message
//                $message->setOid($originalMessage->getOid());
//                //increment version: latest message + 1
//                $latestMessage = $em->getRepository('OlegOrderformBundle:Message')->findLatestMessageByOid($messageId);
//                $incrementedVersion = intval($latestMessage->getVersion()) + 1;
//                echo "incrementedVersion=".$incrementedVersion."<br>";
//                $message->setVersion($incrementedVersion);
//
//                if( !$testing ) {
//                    $em->persist($message);
//                    $em->flush($message);
//                }
//                //delete original message
//                //$this->deleteMessage( $originalMessage, $cycle." action", $request );
//                /////////////////////// EOF Set edited message info /////////////////////

                //log search action
                $logger = $this->container->get('logger');
                $logger->notice("before check msg=".$msg);
                if( $msg ) {

                    if( $cycle == "edit" ) {
                        $msg = "Updated Message Entry ID#".$originalMessage->getMessageOidVersion() . " (new version " . $incrementedVersion .") ". $msg;
                        $eventType = "Call Log Book Entry Edited";
                    }
                    if( $cycle == "amend" ) {
                        $msg = "Amended Message Entry ID#".$originalMessage->getMessageOidVersion() . " (new version " . $incrementedVersion .") ". $msg;
                        $eventType = "Call Log Book Entry Amended";
                    }

                    $eventStr = $calllogUtil->getEventLogDescription($message,$patient,$newEncounter);
                    //exit('eventStr='.$eventStr);

                    if( !$testing ) {
                        $userSecUtil->createUserEditEvent($this->container->getParameter('calllog.sitename'), $eventStr, $user, $message, $request, $eventType);
                        $logger->notice("createUserEditEvent=".$msg);
                    }
                }

                if( !$testing ) {
                    //send an email to the Preferred Email of the "Attending:"
                    $calllogUtil->sendConfirmationEmail($message, $patient, $newEncounter);
                    $logger->notice("sendConfirmationEmail");
                }

            }//if $newEncounter

            if( $testing ) {
                echo "<br><br>message ID=" . $message->getId() . "; OID=". $message->getOid() . "<br>";
                foreach ($message->getPatient() as $patient) {
                    echo "patient id=" . $patient->getId() . "<br>";
                }
                foreach ($message->getEncounter() as $encounter) {
                    echo "encounter id=" . $encounter->getId() . "<br>";
                }
            }

            if( $testing ) {
                exit('form is submitted and finished, msg='.$msg);
            }

            $this->get('session')->getFlashBag()->add(
                'notice',
                $msg
            );

            //echo "return messageId=".$message->getId()."<br>";
            //exit('1');

            //return $this->redirect( $this->generateUrl('calllog_callentry') );
            if( $message->getId() ) {
                return $this->redirect($this->generateUrl('calllog_callentry_view', array('messageOid'=>$message->getOid(),'messageVersion'=>$message->getVersion())));
            } else {
                return $this->redirect($this->generateUrl('calllog_home'));
            }
        }
        //exit('form is not submitted');

        return array(
            //'entity' => $entity,
            'form' => $form->createView(),
            'cycle' => $cycle,
            'title' => $title,
            'formtype' => $formtype,
            'triggerSearch' => 0,
            'mrn' => $mrn,
            'mrntype' => $mrntype
        );
    }

    /**
     * Check if a new message/encounter version already exists for provided message/entry family.
     * @Route("/entry/check-message-version", name="calllog-check-message-version", options={"expose"=true})
     * @Method("GET")
     */
    public function checkMessageVersionAction(Request $request)
    {
        if (false == $this->get('security.context')->isGranted("ROLE_CALLLOG_USER")) {
            return $this->redirect($this->generateUrl('calllog-nopermission'));
        }

        $em = $this->getDoctrine()->getManager();
        $calllogUtil = $this->get('calllog_util');

        $messageId = trim($request->get('messageId'));
        $latestNextMessageVersion = trim($request->get('latestNextMessageVersion')); //next message version, that message will have after submit
        $latestNextEncounterVersion = trim($request->get('latestNextEncounterVersion'));
        //echo "latestNextMessageVersion=$latestNextMessageVersion<br>";

        $encounter = null;
        $encounterVersionOk = true;
        $result = "Not OK";

        $message = $em->getRepository('OlegOrderformBundle:Message')->find($messageId);
        if( !$message ) {
            throw new \Exception( "Message is not found by id ".$messageId );
        }

        $messageVersionOk = $calllogUtil->isMessageVersionMatch($message,$latestNextMessageVersion);
        //echo "messageVersionOk=$messageVersionOk<br>";

        if( count($message->getEncounter()) > 0 ) {
            $encounter = $message->getEncounter()->first();
        }

        if( $encounter ) {
            //echo "encounter exists: id=".$encounter->getId()."<br>";
            $encounterVersionOk = $calllogUtil->isEncounterVersionMatch($encounter,$latestNextEncounterVersion);
        }
        //echo "encounterVersionOk=$encounterVersionOk<br>";

        if( $messageVersionOk && $encounterVersionOk ) {
            $result = "OK";
            //echo "result OK!";
        } else {
            //not ok
        }
        //exit("res=".$result);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($result));
        return $response;
    }

}
