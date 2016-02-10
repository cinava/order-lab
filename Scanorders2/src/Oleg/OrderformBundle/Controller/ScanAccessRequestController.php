<?php

namespace Oleg\OrderformBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oleg\UserdirectoryBundle\Entity\AccessRequest;
use Oleg\UserdirectoryBundle\Controller\AccessRequestController;

/**
 * AccessRequest controller.
 */
class ScanAccessRequestController extends AccessRequestController
{

    public function __construct() {
        $this->siteName = 'scan';
        $this->siteNameShowuser = 'scan';
        $this->siteNameStr = 'Scan Order';
        $this->roleBanned = 'ROLE_SCANORDER_BANNED';
        $this->roleUser = 'ROLE_SCANORDER_SUBMITTER';
        $this->roleUnapproved = 'ROLE_SCANORDER_UNAPPROVED';
        $this->roleEditor = 'ROLE_SCANORDER_PROCESSOR';
    }

    /**
     * @Route("/access-requests/new/create", name="scan_access_request_new_plain")
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request.html.twig")
     */
    public function accessRequestCreatePlainAction()
    {

        $userSecUtil = $this->get('user_security_utility');

        $user = $this->get('security.context')->getToken()->getUser();

        //the user might be authenticated by another site. If the user does not have lowest role => assign unapproved role to trigger access request
        if( false === $userSecUtil->hasGlobalUserRole('ROLE_SCANORDER_SUBMITTER',$user) ) {
            //exit('adding unapproved');
            $user->addRole('ROLE_SCANORDER_UNAPPROVED');
        }

//        if( true === $userSecUtil->hasGlobalUserRole('ROLE_SCANORDER_SUBMITTER',$user) ) {
//            return $this->redirect($this->generateUrl('scan-nopermission'));
//        }

        if( false === $userSecUtil->hasGlobalUserRole('ROLE_SCANORDER_UNAPPROVED',$user) ) {

            //relogin the user, because when admin approves accreq, the user must relogin to update the role in security context
            //return $this->redirect($this->generateUrl($this->container->getParameter('scan.sitename').'_login'));

            //exit('nopermission create scan access request for non ldap user');

            $this->get('session')->getFlashBag()->add(
                'warning',
                "You don't have permission to visit this page on Scan Order site."."<br>".
                "If you already applied for access, then try to " . "<a href=".$this->generateUrl($this->container->getParameter('scan.sitename').'_logout',true).">Re-Login</a>"
            );
            return $this->redirect( $this->generateUrl('main_common_home') );
        }

        $roles = array(
            "unnaproved" => "ROLE_SCANORDER_UNAPPROVED",
            "banned" => "ROLE_SCANORDER_BANNED",
        );

        return $this->accessRequestCreateNew($user->getId(),$this->container->getParameter('scan.sitename'),$roles);
    }

    /**
     * @Route("/access-requests/new", name="scan_access_request_new")
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request.html.twig")
     */
    public function accessRequestCreateAction()
    {

        $sitename = $this->container->getParameter('scan.sitename');

        $user = $this->get('security.context')->getToken()->getUser();

        $userSecUtil = $this->get('user_security_utility');
        if( false === $userSecUtil->hasGlobalUserRole('ROLE_SCANORDER_UNAPPROVED',$user) ) {
            return $this->redirect($this->generateUrl($sitename.'_login'));
        }

        $roles = array(
            "unnaproved" => "ROLE_SCANORDER_UNAPPROVED",
            "banned" => "ROLE_SCANORDER_BANNED",
        );

        return $this->accessRequestCreateNew($user->getId(),$sitename,$roles);
    }

    /**
     * @Route("/access-requests/new/pending", name="scan_access_request_create")
     * @Method("POST")
     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request.html.twig")
     */
    public function accessRequestAction()
    {

        $user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
        $sitename = $this->container->getParameter('scan.sitename');

        return $this->accessRequestCreate($id,$sitename);
    }


    /**
     * Lists all Access Request.
     *
     * @Route("/access-requests", name="scan_accessrequest_list")
     * @Method("GET")
     * @Template("OlegOrderformBundle:AccessRequest:access_request_list.html.twig")
     */
    public function accessRequestIndexAction()
    {
        if( false === $this->get('security.context')->isGranted('ROLE_SCANORDER_PROCESSOR') ) {
            return $this->redirect( $this->generateUrl('scan-nopermission') );
        }

        return $this->accessRequestIndexList($this->container->getParameter('scan.sitename'));
    }


    /**
     * @Route("/access-requests/change-status/{id}/{status}", name="scan_accessrequest_change", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function accessRequestChangeAction(Request $request, $id, $status)
    {

        return parent::accessRequestChangeAction($request, $id, $status);

//        ///////////////////////////
//        if( false === $this->get('security.context')->isGranted('ROLE_SCANORDER_PROCESSOR') ) {
//            return $this->redirect( $this->generateUrl('scan-nopermission') );
//        }
//
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('OlegUserdirectoryBundle:User')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find User entity.');
//        }
//
//        //$accReq = $em->getRepository('OlegUserdirectoryBundle:AccessRequest')->findOneByUser($id);
//        $userSecUtil = $this->get('user_security_utility');
//        $accReq = $userSecUtil->getUserAccessRequest($id,$this->container->getParameter('scan.sitename'));
//
//        if( !$accReq ) {
//            throw new \Exception( 'AccessRequest is not found by id=' . $id );
//        }
//
//        if( $status == "approved" ) {
//
//            $entity->removeRole('ROLE_SCANORDER_UNAPPROVED');
//            $entity->removeRole('ROLE_SCANORDER_BANNED');
//
//            $entity->addRole('ROLE_SCANORDER_SUBMITTER');
//            $entity->addRole('ROLE_SCANORDER_ORDERING_PROVIDER');
//
//            //add WCMC institional scope to Aperio created users
//            $creator = $this->get('security.context')->getToken()->getUser();
//            $orderSecUtil = $this->container->get('order_security_utility');
//            $orderSecUtil->addInstitutionalPhiScopeWCMC($entity,$creator);
//
//            if( $accReq )
//                $accReq->setStatus(AccessRequest::STATUS_APPROVED);
//        }
//
//        if( $status == "declined" ) {
//
//            $entity->removeRole('ROLE_SCANORDER_SUBMITTER');
//            $entity->removeRole('ROLE_SCANORDER_ORDERING_PROVIDER');
//
//            $entity->addRole('ROLE_SCANORDER_BANNED');
//
//            if( $accReq )
//                $accReq->setStatus(AccessRequest::STATUS_DECLINED);
//        }
//
//        if( $status == "active" ) {
//
//            $entity->removeRole('ROLE_SCANORDER_SUBMITTER');
//            $entity->removeRole('ROLE_SCANORDER_ORDERING_PROVIDER');
//
//            $entity->addRole('ROLE_SCANORDER_UNAPPROVED');
//            if( $accReq )
//                $accReq->setStatus(AccessRequest::STATUS_ACTIVE);
//        }
//
//        $em->persist($entity);
//        $em->persist($accReq);
//        $em->flush();
//
//        $this->createAccessRequestUserNotification( $entity, $status, $this->container->getParameter('scan.sitename') );
//
//        return $this->redirect($this->generateUrl($this->container->getParameter('scan.sitename').'_accessrequest_list'));
    }

    //overwrite parent class methods
    public function addOptionalApproveRoles($entity) {
        $entity->addRole('ROLE_SCANORDER_ORDERING_PROVIDER');

        //add WCMC institional scope to Aperio created users
        $creator = $this->get('security.context')->getToken()->getUser();
        $orderSecUtil = $this->container->get('order_security_utility');
        $orderSecUtil->addInstitutionalPhiScopeWCMC($entity,$creator);
    }
    public function removeOptionalDeclineRoles($entity) {
        $entity->removeRole('ROLE_SCANORDER_ORDERING_PROVIDER');
    }


    /**
     * @Route("/access-requests/{id}", name="scan_accessrequest_management", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request_management.html.twig")
     */
    public function accessRequestManagementAction($id )
    {
        return parent::accessRequestManagementAction($id);
    }

    /**
     * @Route("/access-requests/submit/{id}", name="scan_accessrequest_management_submit", requirements={"id" = "\d+"})
     * @Method("POST")
     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request_management.html.twig")
     */
    public function accessRequestManagementSubmitAction(Request $request, $id )
    {
        return parent::accessRequestManagementSubmitAction($request,$id);
    }

    /**
     * @Route("/deny-access-request/{userId}", name="scan_accessrequest_remove", requirements={"userId" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function accessRequestRemoveAction(Request $request, $userId )
    {
        return parent::accessRequestRemoveAction($request,$userId);
    }

    /**
     * @Route("/authorized-users/", name="scan_authorized_users")
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:AccessRequest:authorized_users.html.twig")
     */
    public function authorizedUsersAction(Request $request )
    {
        return parent::authorizedUsersAction($request);
    }

    /**
     * @Route("/authorization-user-manager/{id}", name="scan_authorization_user_management", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request_management.html.twig")
     */
    public function authorizationManagementAction( $id )
    {
        return parent::authorizationManagementAction($id);
    }

    /**
     * @Route("/authorization-user-manager/submit/{id}", name="scan_authorization_user_management_submit", requirements={"id" = "\d+"})
     * @Method("POST")
     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request_management.html.twig")
     */
    public function authorizationManagementSubmitAction( Request $request, $id )
    {
        return parent::authorizationManagementSubmitAction($request,$id);
    }

    /**
     * @Route("/revoke-access-authorization/{userId}", name="scan_authorization_remove", requirements={"userId" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function authorizationRemoveAction(Request $request,$userId)
    {
        return parent::authorizationRemoveAction($request,$userId);
    }

    /**
     * @Route("/add-authorized-user/", name="scan_add_authorized_user")
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:AccessRequest:add_authorized_user.html.twig")
     */
    public function addAuthorizedUserAction( Request $request )
    {
        return parent::addAuthorizedUserAction($request);
    }

//    /**
//     * @Route("/add-authorized-user/submit/", name="scan_add_authorized_user_submit")
//     * @Method("POST")
//     * @Template("OlegUserdirectoryBundle:AccessRequest:add_authorized_user.html.twig")
//     */
//    public function addAuthorizedUserSubmitAction( Request $request )
//    {
//        return parent::addAuthorizedUserSubmitAction($request);
//    }

}
