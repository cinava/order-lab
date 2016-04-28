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
        $user = $this->get('security.context')->getToken()->getUser();

        $entity = $em->getRepository('OlegVacReqBundle:VacReqRequest')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Vacation Request by id='.$id);
        }

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

                $entity->setApprover($user);
                $em->persist($entity);
                $em->flush();

                $status = $entity->getOverallStatus();
                $eventType = 'Business/Vacation Request '.ucwords($status);
                $action = $status;

                //send respond email
                $vacreqUtil = $this->get('vacreq_util');
                $requestName = null;
                $vacreqUtil->sendSingleRespondEmailToSubmitter( $entity, $user, $requestName, $status );

            } else { //update

                $entity->setUpdateUser($user);
                $em->persist($entity);
                $em->flush();

                $action = "updated";
                $eventType = 'Business/Vacation Request Updated';
            }

            //Event Log
            $event = "Request for ".$entity->getUser()." has been ".$action." by ".$user;
            $userSecUtil = $this->container->get('user_security_utility');
            $userSecUtil->createUserEditEvent($this->container->getParameter('vacreq.sitename'),$event,$user,$entity,$request,$eventType);

            //Flash
            $this->get('session')->getFlashBag()->add(
                'notice',
                $event
            );

            return $this->redirectToRoute('vacreq_show', array('id' => $entity->getId()));
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
            'review' => $review
            //'delete_form' => $deleteForm->createView(),
        );
    }



    /**
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
                    $statusSet = true;
                }
            }

            if( $requestName == 'vacation' ) {
                $businessRequest = $entity->getRequestBusiness();
                if( $businessRequest ) {
                    $businessRequest->setStatus($status);
                    $statusSet = true;
                }
            }

            if( $statusSet ) {
                $entity->setApprover($user);
                $em->persist($entity);
                $em->flush();

                //return $this->redirectToRoute('vacreq_home');

                //Flash
                if ($status == 'pending') {
                    $status = 'set to Pending';
                }
                $event = ucwords($requestName)." Request ID " . $entity->getId() . " for " . $entity->getUser() . " has been " . $status . " by " . $user;
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    $event
                );

                //Event Log
                $userSecUtil = $this->container->get('user_security_utility');
                $userSecUtil->createUserEditEvent($this->container->getParameter('vacreq.sitename'), $event, $user, $entity, $request, 'Business/Vacation Request Updated');

                //send respond confirmation email to a submitter
                $vacreqUtil = $this->get('vacreq_util');
                $vacreqUtil->sendSingleRespondEmailToSubmitter( $entity, $user, $requestName, $status );
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

        return $this->redirectToRoute('vacreq_show', array('id' => $entity->getId()));
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

        $params = array(
            'sc' => $this->get('security.context'),
            'em' => $em,
            'user' => $entity->getUser(),
            'cycle' => $cycle,
            'roleAdmin' => $admin,
            'roleApprover' => $roleApprover,
            'organizationalInstitutions' => $organizationalInstitutions
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
}
