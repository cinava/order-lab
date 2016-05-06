<?php

namespace Oleg\VacReqBundle\Controller;

use Oleg\UserdirectoryBundle\Entity\AccessRequest;
use Oleg\VacReqBundle\Entity\VacReqRequest;
use Oleg\VacReqBundle\Form\VacReqRequestType;
use Oleg\VacReqBundle\Util\VacReqImportData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//vacreq site

class RequestController extends Controller
{


    /**
     * Creates a new VacReqRequest entity.
     *
     * @Route("/", name="vacreq_home")
     * @Route("/new", name="vacreq_new")
     * @Method({"GET", "POST"})
     * @Template("OlegVacReqBundle:Request:edit.html.twig")
     */
    public function newAction(Request $request)
    {

        $vacreqUtil = $this->get('vacreq_util');

        $user = $this->get('security.context')->getToken()->getUser();

        $entity = new VacReqRequest($user);

        if( false == $this->get('security.context')->isGranted("create", $entity) ) {
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }

        //set phone
        $phone = $vacreqUtil->getSubmitterPhone($user);
        $entity->setPhone($phone);

        $cycle = 'new';

        $form = $this->createRequestForm($entity,$cycle);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() ) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            //Event Log
            $eventType = "Business/Vacation Request Created";
            $event = "Request for ".$entity->getUser()." has been created";
            $userSecUtil = $this->container->get('user_security_utility');
            $userSecUtil->createUserEditEvent($this->container->getParameter('vacreq.sitename'),$event,$user,$entity,$request,$eventType);

            //Flash
            $this->get('session')->getFlashBag()->add(
                'notice',
                $event
            );

            $emailUtil = $this->get('user_mailer_utility');
            $break = "\r\n";

            //set confirmation email to submitter and approver and email users
            $subject = "Faculty Vacation/Business Request #".$entity->getId()." Confirmation";
            $message = "Dear ".$entity->getUser()->getUsernameOptimal().",".$break.$break;
            $message .= "You have successfully submitted the pathology faculty vacation/business travel request.";
            $message .= "The division approver will review your request soon.";
            $message .= $break.$break."**** PLEASE DON'T REPLY TO THIS EMAIL ****";
            $emailUtil->sendEmail( $user->getSingleEmail(), $subject, $message, null, null );

            //set confirmation email to approver and email users
//            $approvers = $vacreqUtil->getApprovers();
//            $subject = "Review Faculty Vacation/Business Request #".$entity->getId()." Confirmation";
//            $message = "Dear ".$entity->getUsernameOptimal().",".$break.$break;
//            $message .= "You have successfully submitted the pathology faculty vacation/business travel request.";
//            $message .= "The division approver will review your request soon.";
//            $message .= $break.$break."**** PLEASE DON'T REPLY TO THIS EMAIL ****";
//            $emailUtil->sendEmail( $user->getSingleEmail(), $subject, $message, null, null );
            $vacreqUtil->sendConfirmationEmailToApprovers( $entity );

            return $this->redirectToRoute('vacreq_show', array('id' => $entity->getId()));
        }

        //check for active access requests
        $accessreqs = $this->getActiveAccessReq();

        //calculate approved vacation days in total.
        $totalApprovedDaysString = $vacreqUtil->getApprovedDaysString($user);
        //echo "totalApprovedDaysString=".$totalApprovedDaysString."<br>";

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'cycle' => $cycle,
            'accessreqs' => count($accessreqs),
            'totalApprovedDaysString' => $totalApprovedDaysString,
        );
    }


    /**
     * Show: Finds and displays a VacReqRequest entity.
     *
     * @Route("/show/{id}", name="vacreq_show")
     * @Method("GET")
     * @Template("OlegVacReqBundle:Request:edit.html.twig")
     */
    public function showAction(Request $request, $id)
    {
        if( false == $this->get('security.context')->isGranted('ROLE_VACREQ_USER') ) {
            //exit('show: no permission');
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegVacReqBundle:VacReqRequest')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Vacation Request by id='.$id);
        }

        if( false == $this->get('security.context')->isGranted("read", $entity) ) {
            //exit('show: no permission');
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }
        //exit('show: ok permission');

        $cycle = 'show';

        $form = $this->createRequestForm($entity,$cycle);

        return array(
            'entity' => $entity,
            'cycle' => $cycle,
            'form' => $form->createView(),
            //'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edit: Displays a form to edit an existing VacReqRequest entity.
     *
     * @Route("/edit/{id}", name="vacreq_edit")
     * @Route("/review/{id}", name="vacreq_review")
     * @Method({"GET", "POST"})
     * @Template("OlegVacReqBundle:Request:edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        //$deleteForm = $this->createDeleteForm($vacReqRequest);
        //$editForm = $this->createForm('Oleg\VacReqBundle\Form\VacReqRequestType', $vacReqRequest);

        $em = $this->getDoctrine()->getManager();
        $vacreqUtil = $this->get('vacreq_util');
        $user = $this->get('security.context')->getToken()->getUser();

        $entity = $em->getRepository('OlegVacReqBundle:VacReqRequest')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Vacation Request by id='.$id);
        }

//        //can not edit if request is already processed by an approver: status == completed
//        if( $entity->getStatus() == 'completed' ) {
//            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
//        }

        //check permission
        $routName = $request->get('_route');
        if( $routName == 'vacreq_review' ) {
            if( false == $this->get('security.context')->isGranted("changestatus", $entity) ) {
                return $this->redirect( $this->generateUrl('vacreq-nopermission') );
            }
        } else {
            if( false == $this->get('security.context')->isGranted("update", $entity) ) {
                return $this->redirect( $this->generateUrl('vacreq-nopermission') );
            }
        }

        $cycle = 'edit';

        $form = $this->createRequestForm($entity,$cycle,$request);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if( $routName == 'vacreq_review' ) { //review

                $overallStatus = $entity->getOverallStatus();

                //set overall status
                $entity->setStatus($overallStatus);

                $entity->setApprover($user);
                $em->persist($entity);
                $em->flush();

                $eventType = 'Business/Vacation Request '.ucwords($overallStatus);
                $action = $overallStatus;

                //send respond email
                $requestName = null;
                $vacreqUtil->sendSingleRespondEmailToSubmitter( $entity, $user, $requestName, $overallStatus );

            } else { //update

                $entity->setUpdateUser($user);

                /////////////// Add event log on edit (edit or add collection) ///////////////
                /////////////// Must run before flash DB. When DB is flashed getEntityChangeSet() will not work ///////////////
                $changedInfoArr = $vacreqUtil->setEventLogChanges($entity);

                $em->persist($entity);
                $em->flush();

                $action = "updated";
                $eventType = 'Business/Vacation Request Updated';
            }

            if( $action == 'pending' ) {
                $action = 'set to Pending';
            }

            //Event Log
            $break = "\r\n";
            $event = "Request for ".$entity->getUser()." has been ".$action." by ".$user.$break.$break;
            $userSecUtil = $this->container->get('user_security_utility');

            //Flash
            $this->get('session')->getFlashBag()->add(
                'notice',
                $event
            );

            //set event log for objects
            if( count($changedInfoArr) > 0 ) {
                //$user = $this->get('security.context')->getToken()->getUser();
                $event .= "Updated Data:".$break;
                $event .= implode("<br>", $changedInfoArr);
            }

            $userSecUtil->createUserEditEvent($this->container->getParameter('vacreq.sitename'),$event,$user,$entity,$request,$eventType);

            if( $routName == 'vacreq_review' ) {
                return $this->redirectToRoute('vacreq_incomingrequests');
            } else {
                return $this->redirectToRoute('vacreq_show', array('id' => $entity->getId()));
            }

        }

        $review = false;
        if( $request ) {
            if( $request->get('_route') == 'vacreq_review' ) {
                $review = true;
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'cycle' => $cycle,
            'review' => $review,
            //'delete_form' => $deleteForm->createView(),
        );
    }



    /**
     * approved, rejected, pending, canceled
     * @Route("/status/{id}/{requestName}/{status}", name="vacreq_status_change")
     * @Method({"GET"})
     * @Template("OlegVacReqBundle:Request:edit.html.twig")
     */
    public function statusAction(Request $request, $id, $requestName, $status) {

        //if( false == $this->get('security.context')->isGranted('ROLE_VACREQ_APPROVER') ) {
        //    return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        //}

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $entity = $em->getRepository('OlegVacReqBundle:VacReqRequest')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Vacation Request by id='.$id);
        }

        if( false == $this->get('security.context')->isGranted("changestatus", $entity) ) {
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }

        if( $status ) {

            $statusSet = false;

            if( $requestName == 'business' ) {
                $businessRequest = $entity->getRequestBusiness();
                if( $businessRequest ) {
                    $businessRequest->setStatus($status);
                    //set overall status
                    if( $status == 'pending' ) {
                        $entity->setStatus('pending');
                    } else {
                        $entity->setStatus('completed');
                    }
                    $statusSet = true;
                }
            }

            if( $requestName == 'vacation' ) {
                $vacationRequest = $entity->getRequestVacation();
                if( $vacationRequest ) {
                    $vacationRequest->setStatus($status);
                    //set overall status
                    if( $status == 'pending' ) {
                        $entity->setStatus('pending');
                    } else {
                        $entity->setStatus('completed');
                    }
                    $statusSet = true;
                }
            }

            if( $requestName == 'entire' ) {

                $requestName = $entity->getRequestName(); //'business travel and vacation';

                $entity->setEntireStatus($status);

                if( $status != 'canceled' && $status != 'pending' ) {
                    $status = 'completed';
                }

                $entity->setStatus($status);
                $statusSet = true;
            }

            if( $statusSet ) {
                $entity->setApprover($user);
                $em->persist($entity);
                $em->flush();

                //return $this->redirectToRoute('vacreq_home');

                //Flash
                $statusStr = $status;
                if( $status == 'pending' ) {
                    $statusStr = 'set to Pending';
                }
                $event = ucwords($requestName)." Request ID " . $entity->getId() . " for " . $entity->getUser() . " has been " . $statusStr . " by " . $user;
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    $event
                );

                //Event Log
                $userSecUtil = $this->container->get('user_security_utility');
                $userSecUtil->createUserEditEvent($this->container->getParameter('vacreq.sitename'), $event, $user, $entity, $request, 'Business/Vacation Request Updated');

                //send respond confirmation email to a submitter
                $vacreqUtil = $this->get('vacreq_util');

                if( $status == 'canceled' ) {
                    //an email should be sent to approver saying
                    // "FirstName LastName canceled/withdrew their business travel / vacation request described below:"
                    // and list all variable names and values in the email.
                    $vacreqUtil->sendCancelEmailToApprovers( $entity, $user, $status );
                } else {
                    $vacreqUtil->sendSingleRespondEmailToSubmitter( $entity, $user, $requestName, $status );
                }

            }

        }

//        $url = $request->headers->get('referer');
//        //exit('url='.$url);
//
//        //return $this->redirectToRoute('vacreq_home');
//        //return $this->redirect($this->generateUrl('vacreq_home', $request->query->all()));
//
//        if( $url ) {
//            return $this->redirect($url);
//        } else {
//            //return $this->redirectToRoute('vacreq_approvers');
//            return $this->redirectToRoute('vacreq_show', array('id' => $entity->getId()));
//        }

        //return $this->redirectToRoute('vacreq_show', array('id' => $entity->getId()));
        return $this->redirectToRoute('vacreq_incomingrequests');
    }



//    /**
//     * @Route("/request/{id}", name="vacreq_request_show")
//     * @Route("/request/edit/{id}", name="vacreq_request_edit")
//     * @Method("GET")
//     * @Template("OlegVacReqBundle:Request:new.html.twig")
//     */
//    public function newRequestAction(Request $request, $id)
//    {
//
//        if( false == $this->get('security.context')->isGranted('ROLE_DEIDENTIFICATOR_USER') ) {
//            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
//        }
//
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('OlegVacReqBundle:VacReqRequestForm')->find($id);
//
//        if( !$entity ) {
//            throw $this->createNotFoundException('Unable to find Vacation Request by id='.$id);
//        }
//
//        $routeName = $request->get('_route');
//
//        $cycle = "show";
//        if( $routeName = 'vacreq_request_edit' ) {
//            $cycle = "edit";
//        }
//
//        //VacReqRequestType Form
//
//        $form = $this->createRequestForm($entity, $cycle);
//
//        if( 1 ) {
//            $admin = true;
//        } else {
//            $admin = false;
//        }
//
//        return array(
//            'form' => $form->createView(),
//            'admin' => $admin,
//            'cycle' => $cycle
//        );
//    }


    public function createRequestForm( $entity, $cycle, $request=null ) {

        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
//        if( !$entity ) {
//            $entity = new VacReqRequest($user);
//        }

        $admin = false;
        if( $this->get('security.context')->isGranted('ROLE_VACREQ_ADMIN') ) {
            $admin = true;
        }

        $roleApprover = false;
        if( $this->get('security.context')->isGranted("changestatus", $entity) ) {
            $roleApprover = true;
        }


        //organizationalInstitution
        //$organizationalInstitutions = $em->getRepository('OlegUserdirectoryBundle:User')->findVacReqOrganizationalInstitution($user);
        $organizationalInstitutions = $this->getVacReqOrganizationalInstitutions($user);

        //get holidays url
        $userSecUtil = $this->container->get('user_security_utility');
        $holidaysUrl = $userSecUtil->getSiteSettingParameter('holidaysUrl');
        if( !$holidaysUrl ) {
            throw new \InvalidArgumentException('holidaysUrl is not defined in Site Parameters.');
        }
        $holidaysUrl = '<a target="_blank" href="'.$holidaysUrl.'">holidays</a>';

        $params = array(
            'sc' => $this->get('security.context'),
            'em' => $em,
            'user' => $entity->getUser(),
            'cycle' => $cycle,
            'roleAdmin' => $admin,
            'roleApprover' => $roleApprover,
            'organizationalInstitutions' => $organizationalInstitutions,
            'holidaysUrl' => $holidaysUrl
        );

        $disabled = false;
        $method = 'GET';

        if( $cycle == 'show' ) {
            $disabled = true;
        }

        if( $cycle == 'new' ) {
            $method = 'POST';
        }

        if( $cycle == 'edit' ) {
            $method = 'POST';
        }

        $params['review'] = false;
        if( $request ) {
            if( $request->get('_route') == 'vacreq_review' ) {
                $params['review'] = true;
            }
        }

        $form = $this->createForm(
            new VacReqRequestType($params),
            $entity,
            array(
                'disabled' => $disabled,
                'method' => $method,
                //'action' => $action
            )
        );

        return $form;
    }

    //get institution from user submitter role
    public function getVacReqOrganizationalInstitutions( $user ) {

        $institutions = array();

        $em = $this->getDoctrine()->getManager();

        //get vacreq submitter role
        $submitterRoles = $em->getRepository('OlegUserdirectoryBundle:User')->findUserRolesByObjectAction( $user, "VacReqRequest", "create" );

        if( count($submitterRoles) == 0 ) {
            //find all submitter role's institution
            $submitterRoles = $em->getRepository('OlegUserdirectoryBundle:User')->findRolesByObjectAction("VacReqRequest", "create");
        }
        //echo "roles count=".count($submitterRoles)."<br>";

        foreach( $submitterRoles as $submitterRole ) {
            $institution = $submitterRole->getInstitution();
            if( $institution ) {

                //Clinical Pathology (for review by Firstname Lastname)
                //find approvers with the same institution
                $approverStr = $this->getApproversBySubmitterRole($submitterRole);
                if( $approverStr ) {
                    $orgName = $institution . " (for review by " . $approverStr . ")";
                } else {
                    $orgName = $institution;
                }

                //$institutions[] = array( $institution->getId() => $institution."-".$organizationalName . "-" . $approver);
                $institutions[$institution->getId()] = $orgName;
                //$institutions[] = $orgName;
                //$institutions[] = $institution;
            }
        }

        //add request institution
//        if( $entity->getInstitution() ) {
//            $orgName = $institution . " (for review by " . $approverStr . ")";
//            $institutions[$entity->getInstitution()->getId()] = $orgName;
//        }

        return $institutions;
    }

    //$role - string; for example "ROLE_VACREQ_APPROVER_CYTOPATHOLOGY"
    public function getApproversBySubmitterRole( $role ) {
        $em = $this->getDoctrine()->getManager();
        $roleApprover = str_replace("SUBMITTER","APPROVER",$role);
        $approvers = $em->getRepository('OlegUserdirectoryBundle:User')->findUserByRole($roleApprover);

        $approversArr = array();
        foreach( $approvers as $approver ) {
            $approversArr[] = $approver->getUsernameShortest();
        }

        return implode(", ",$approversArr);
    }

    //check for active access requests
    public function getActiveAccessReq() {
        if( !$this->get('security.context')->isGranted('ROLE_VACREQ_ADMIN') ) {
            return null;
        }
        $userSecUtil = $this->get('user_security_utility');
        $accessreqs = $userSecUtil->getUserAccessRequestsByStatus($this->container->getParameter('vacreq.sitename'),AccessRequest::STATUS_ACTIVE);
        return $accessreqs;
    }



    /**
     * @Route("/import-old-data/", name="vacreq_import_old_data")
     * @Method({"GET"})
     * @Template("OlegVacReqBundle:Request:edit.html.twig")
     */
    public function importOldDataAction(Request $request) {

        if( !$this->get('security.context')->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }

        $vacReqImportData = $this->get('vacreq_import_data');
        $res = $vacReqImportData->importOldData();

        //exit('Imported result: '.$res);

        //Flash
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Imported result: '.$res
        );

        return $this->redirectToRoute('vacreq_incomingrequests');
    }


    /**
     * @Route("/delete-imported-old-data/", name="vacreq_delete_imported_old_data")
     * @Method({"GET"})
     * @Template("OlegVacReqBundle:Request:edit.html.twig")
     */
    public function deleteImportedOldDataAction(Request $request) {

        if( !$this->get('security.context')->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository('OlegVacReqBundle:VacReqRequest');

        $dql =  $repository->createQueryBuilder("request");
        $dql->select('request');
        $dql->where("request.exportId != 0");

        $query = $em->createQuery($dql);

        $requests = $query->getResult();

        $batchSize = 20;
        $count = 0;
        foreach( $requests as $request ) {

//            echo "reqId=".$request->getId()."<br>";
//            if( $request->hasBusinessRequest() ) {
//                echo "businessId=" . $request->getRequestBusiness()->getID() . "<br>";
//            }
//            if( $request->hasVacationRequest() ) {
//                echo "vacationId=" . $request->getRequestVacation()->getID() . "<br>";
//            }

            $em->remove($request);

            $em->flush();
            //exit('removed');

            if( ($count % $batchSize) === 0 ) {
                $em->flush();
                //$em->clear(); // Detaches all objects from Doctrine!
            }

            $count++;
        }

        $em->flush(); //Persist objects that did not make up an entire batch
        $em->clear();

        //exit('Remove result: '.$count);

        //Flash
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Removed requests: '.count($requests)
        );

        return $this->redirectToRoute('vacreq_incomingrequests');
    }

}
