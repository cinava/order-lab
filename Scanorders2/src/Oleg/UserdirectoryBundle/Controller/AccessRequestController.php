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

namespace Oleg\UserdirectoryBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Oleg\UserdirectoryBundle\Entity\PerSiteSettings;
use Oleg\UserdirectoryBundle\Form\AccessRequestType;
use Oleg\UserdirectoryBundle\Form\AuthorizedUserFilterType;
use Oleg\UserdirectoryBundle\Form\GeneratedUserType;
use Oleg\UserdirectoryBundle\Form\PerSiteSettingsType;
use Oleg\UserdirectoryBundle\Entity\User;
use Oleg\UserdirectoryBundle\Form\AuthorizitaionUserType;
use Oleg\UserdirectoryBundle\Form\SimpleUserType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

use Oleg\UserdirectoryBundle\Entity\AccessRequest;
use Oleg\UserdirectoryBundle\Util\EmailUtil;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


//When admin approves acc req, the user must logout and login.

/**
 * AccessRequest controller.
 */
class AccessRequestController extends Controller
{

    protected $router;
    protected $siteName;
    protected $siteNameShowuser;
    protected $siteNameStr;
    protected $roleBanned;
    protected $roleUser;
    protected $roleUnapproved;

    public function __construct() {
        $this->siteName = 'employees'; //controller is not setup yet, so we can't use $this->container->getParameter('employees.sitename');
        $this->siteNameShowuser = 'employees';
        $this->siteNameStr = 'Employee Directory';
        $this->roleBanned = 'ROLE_USERDIRECTORY_BANNED';
        $this->roleUser = 'ROLE_USERDIRECTORY_OBSERVER';
        $this->roleUnapproved = 'ROLE_USERDIRECTORY_UNAPPROVED';
        $this->roleEditor = 'ROLE_USERDIRECTORY_EDITOR';
    }

    /**
     * This url "/access-requests/new/create" is set in security.yml as access_denied_url parameter
     *
     * @Route("/access-requests/new/create", name="employees_access_request_new_plain")
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request.html.twig")
     */
    public function accessRequestCreatePlainAction(Request $request)
    {
        return $this->accessRequestCreatePlain($request);
    }
    public function accessRequestCreatePlain($request)
    {

        //exit('access Request Create Plain');

        $userSecUtil = $this->get('user_security_utility');
        //$userServiceUtil = $this->get('user_service_utility');
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();


        if( $userSecUtil->isSiteAccessible($this->siteName) === false ) {
            $systemEmail = $userSecUtil->getSiteSettingParameter('siteEmail');
            $this->get('session')->getFlashBag()->add(
                'notice',
                "This site is currently not accessible. Please try it later. If you have any questions, please contact the ".$systemEmail."."
            );
            return $this->redirect( $this->generateUrl($this->siteName.'_home') );
        }

        //TODO: If self-sign up is enabled, and a site is "LIVE"
        $testing = false;
        //$testing = true;
        //add minimum roles and redirect to the intended URL they were trying to access
        if( $userSecUtil->isSelfSignUp($this->siteName) ) {
            //echo "site is Self Sign Up<br>";

            //$environment = $userSecUtil->getSiteSettingParameter('environment');
            //if( $environment == "live" ) {
            if( $userSecUtil->isSiteAccessible($this->siteName) ) {
                //echo "site is Live<br>";

                $siteObject = $em->getRepository('OlegUserdirectoryBundle:SiteList')->findOneByAbbreviation($this->siteName);
                $lowestRoles = $siteObject->getLowestRoles();
                //1) Add Minimum Roles for this site
                if( count($lowestRoles) == 0 ) {
                    $lowestRoles = array($this->roleUser);
                }
                $roleStrArr = array();
                foreach($lowestRoles as $role) {
                    $user->addRole($role);
                    $roleStrArr[] = $role."";
                }

                //flush changes for user roles
                if( !$testing ) {
                    $em->flush($user);
                }

                //EventLog
                $event = "Auto-Grant Access by assigning the lowest roles:".implode(",",$roleStrArr);
                $userSecUtil->createUserEditEvent($this->siteName,$event,$user,$user,$request,'Auto-Grant Access');

                //////////////// 2) redirect to the intended URL they were trying to access //////////////
                $lastRoute = $this->siteName.'_home';
                if( 0 ) {
                    //TODO: find a way to get intended URL
                    //ldap_translationalresearch_firewall, ldap_employees_firewall ...
//                $firewallName = 'ldap_'.$this->siteName.'_firewall';
                    $firewallName = 'ldap_translationalresearch_firewall';
                    //$firewallName = 'ldap_employees_firewall';
                    $indexLastRoute = '_security.'.$firewallName.'.target_path';
                    $lastRoute = $request->getSession()->get($indexLastRoute);
                    echo "0 lastRoute=".$lastRoute."<br>";

                    $lastPath = $request->headers->get('referer');
                    echo "referer lastRoute=" . $lastPath . "<br>";

                    $lastRoute = $this->getRefererRoute($request);
                    echo "2 lastRoute=" . $lastRoute . "<br>";
                }//if(0)
                //////////////// EOF 2) redirect to the intended URL they were trying to access //////////////

                if( $testing ) {
                    exit('redirect to the requested system');
                }
                return $this->redirect( $this->generateUrl($lastRoute) );

            } else {
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    "This site has not gone live yet."
                );
            }
        }
        if( $testing ) {
            exit('continue with access request');
        }

        //the user might be authenticated by another site. If the user does not have lowest role => assign unapproved role to trigger access request
        if( false === $userSecUtil->hasGlobalUserRole($this->roleUser,$user) ) {
            //exit('no roleUser=' . $this->roleUser);
            $user->addRole($this->roleUnapproved);
        }

//        if( true === $userSecUtil->hasGlobalUserRole($this->roleUser,$user) ) {
//            return $this->redirect($this->generateUrl('employees-nopermission'));
//        }

        if( false === $userSecUtil->hasGlobalUserRole($this->roleUnapproved,$user) ) {

            //relogin the user, because when admin approves accreq, the user must relogin to update the role in security context. Or update security context (How?)
            //return $this->redirect($this->generateUrl($this->container->getParameter('employees.sitename').'_login'));

            $this->get('session')->getFlashBag()->add(
                'warning',
                "You don't have permission to visit this page on ".$this->siteNameStr." site."."<br>".
                "If you already applied for access, then try to " . "<a href=".$this->generateUrl($this->siteName.'_logout',UrlGeneratorInterface::ABSOLUTE_URL).">Re-Login</a>"
            );
            return $this->redirect( $this->generateUrl('main_common_home') );
        }

        $roles = array(
            "unnaproved" => $this->roleUnapproved,
            "banned" => $this->roleBanned,
        );

        return $this->accessRequestCreateNew($user->getId(),$this->siteName,$roles);
    }


    //NOT USED
    public function getRefererRoute($request)
    {
        //look for the referer route
        $referer = $request->headers->get('referer');
        $lastPath = substr($referer, strpos($referer, $request->getBaseUrl()));
        $lastPath = str_replace($request->getBaseUrl(), '', $lastPath);

        $matcher = $this->get('router')->getMatcher();
        $parameters = $matcher->match($lastPath);
        $route = $parameters['_route'];

        return $route;
    }


    /**
     * Used to reqdirect from LoginSuccessHandler if a user has role roleBanned or roleUnapproved
     *
     * @Route("/access-requests/new", name="employees_access_request_new")
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request.html.twig")
     */
    public function accessRequestCreateAction()
    {

        $sitename = $this->siteName;

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $userSecUtil = $this->get('user_security_utility');
        if( false === $userSecUtil->hasGlobalUserRole($this->roleUnapproved,$user) ) {
            return $this->redirect($this->generateUrl($sitename.'_login'));
        }

        $roles = array(
            "unnaproved" => $this->roleUnapproved,
            "banned" => $this->roleBanned,
        );

        return $this->accessRequestCreateNew($user->getId(),$sitename,$roles);
    }


    // check for cases:
    // 1) user has active accreq
    // 2) user has accreq but it was declined
    // 3) user has role banned
    // 4) user has approved accreq, but user has ROLE_UNAPPROVED
    public function accessRequestCreateNew($id,$sitename,$roles) {

        //echo "create new accreq <br>";

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('OlegUserdirectoryBundle:User')->find($id);

        if (!$user) {
            return $this->redirect($this->generateUrl($sitename.'_login'));
            //throw $this->createNotFoundException('Unable to find User.');
        }

        $secUtil = $this->get('order_security_utility');
        $userAccessReq = $secUtil->getUserAccessRequest($user,$sitename);

        $sitenameFull = $this->siteNameStr;

        // Case 1: user has active accreq
        if( $userAccessReq && $userAccessReq->getStatus() == AccessRequest::STATUS_ACTIVE ) {

            $transformer = new DateTimeToStringTransformer(null,null,'m/d/Y');
            $dateStr = $transformer->transform($userAccessReq->getCreatedate());

            $text = "You have requested access to ".$sitenameFull." on " . $dateStr . ". Your request has not been approved yet. Please contact the system administrator by emailing ".$this->container->getParameter('default_system_email')." if you have any questions.";

            //$this->get('security.context')->setToken(null);
            //$this->get('request')->getSession()->invalidate();

            return $this->render('OlegUserdirectoryBundle:AccessRequest:request_confirmation.html.twig',array('text'=>$text,'sitename'=>$sitename,'pendinguser'=>true));
        }

        // Case 2: user has accreq but it was declined
        if( $userAccessReq && $userAccessReq->getStatus() == AccessRequest::STATUS_DECLINED ) {

            $transformer = new DateTimeToStringTransformer(null,null,'m/d/Y');
            $dateStr = $transformer->transform($userAccessReq->getCreatedate());
            $text = 'You have requested access to '.$sitenameFull.' on '.$dateStr.'. Your request has been declined. Please contact the system administrator by emailing '.$this->container->getParameter('default_system_email').' if you have any questions.';

            return $this->render('OlegUserdirectoryBundle:AccessRequest:request_confirmation.html.twig',array('text'=>$text,'sitename'=>$sitename,'pendinguser'=>true));
        }

        // Case 3: user has role banned
        $userSecUtil = $this->get('user_security_utility');
        if( $userSecUtil->hasGlobalUserRole($roles['banned'],$user) ) {

            $this->get('session')->getFlashBag()->add(
                'warning',
                "You were banned to visit this site."."<br>".
                "You can try to " . "<a href=".$this->generateUrl($sitename.'_logout',UrlGeneratorInterface::ABSOLUTE_URL).">Re-Login</a>"
            );
            return $this->redirect( $this->generateUrl('main_common_home') );
        }

        // Case 4: user has approved accreq, but user has ROLE_UNAPPROVED
        if( $userAccessReq && $userAccessReq->getStatus() == AccessRequest::STATUS_APPROVED && $userSecUtil->hasGlobalUserRole($roles['unnaproved'],$user) ) {

            $this->get('session')->getFlashBag()->add(
                'warning',
                "You don't have permission to visit this site because you have UNAPPROVED role."."<br>".
                "Please contact site system administrator ".$this->container->getParameter('default_system_email')."<br>".
                "You can try to " . "<a href=".$this->generateUrl($sitename.'_logout',UrlGeneratorInterface::ABSOLUTE_URL).">Re-Login</a>"
            );
            return $this->redirect( $this->generateUrl('main_common_home') );
        }

        //echo "create new accreq, exit??? <br>";

        //create a form for AccessRequest entity:
        // when the user clicks "Yes" in answer to "would you like to request access to this site?",
        // show a page with the following fields
        // (all are from http://c.med.cornell.edu/order/scan/account-requests/new except
        // the name of the field "Reason for account request" is changed to "Reason for access request)
        $params = $this->getParams();
        $accReq = new AccessRequest();
        $accReq->setStatus(AccessRequest::STATUS_ACTIVE);
        $accReq->setUser($user);
        $accReq->setSiteName($sitename);

        //pre-populate the First Name, Last Name, Email, Phone Number, Job Title, and Organizational Group fields
        $userDetalsArr = $user->getDetailsArr();
        //firstName
        $accReq->setFirstName($user->getFirstName());
        //lastName
        $accReq->setLastName($user->getLastName());
        //email
        $accReq->setEmail($user->getSingleEmail());
        //phone
        $phonesArr = $user->getAllPhones();
        if( count($phonesArr)>0 && $phonesArr[0]['phone'] ) {
            $accReq->setPhone($phonesArr[0]['phone']);
        }
        //job title
        $accReq->setJob($userDetalsArr["title"]);
        //organizationalGroup
        $accReq->setOrganizationalGroup($userDetalsArr["institution"]);

        $form = $this->createForm(AccessRequestType::class, $accReq, array('form_custom_value'=>$params));

        return array(
            'user' => $user,
            'form' => $form->createView(),
            'sitename' => $sitename,
            'sitenamefull' => $sitenameFull,
            'pendinguser' => true
        );
    }
    public function getParams() {

        $params = array();

        $em = $this->getDoctrine()->getManager();
        $params['em'] = $em;

        //departments
        $department = $em->getRepository('OlegUserdirectoryBundle:Institution')->findOneByName('Pathology and Laboratory Medicine');

        $params['institution'] = $department;

        //$requestedInstitutionalPHIScope = $em->getRepository('OlegUserdirectoryBundle:Institution')->findBy(array('level'=>0));

        $repository = $em->getRepository('OlegUserdirectoryBundle:Institution');
        $dql =  $repository->createQueryBuilder("institution");
        $dql->select('institution');
        $dql->leftJoin("institution.types", "types");

        $dql->where("institution.type = 'default'");
        $dql->andWhere("types.name IS NULL OR types.name != 'Collaboration'");

        $query = $em->createQuery($dql);
        $requestedScanOrderInstitutionScope = $query->getResult();

        //$params['requestedInstitutionalPHIScope'] = $requestedInstitutionalPHIScope;
        $params['requestedScanOrderInstitutionScope'] = $requestedScanOrderInstitutionScope;


        return $params;
    }



//    /**
//     * when the user clicks "Yes" in answer to "would you like to request access to this site?",
//     * show a page with the following fields
//     * (all are from http://c.med.cornell.edu/order/scan/account-requests/new except
//     * the name of the field "Reason for account request" is changed to "Reason for access request)
//     *
//     * @Route("/access-requests/details/new", name="employees_access_request_details_new")
//     * @Method("GET")
//     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request.html.twig")
//     */
//    public function accessRequestDetailsAction()
//    {
//        $user = $this->get('security.token_storage')->getToken()->getUser();
//        $id = $user->getId();
//        $sitename = $this->siteName;
//
//        return $this->accessRequestCreate($id,$sitename);
//
//    }

     /**
      * On click button: Yes, please!
      *
      * @Route("/access-requests/new/pending", name="employees_access_request_create")
      * @Method("POST")
      * @Template("OlegUserdirectoryBundle:AccessRequest:access_request.html.twig")
      */
    public function accessRequestAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $id = $user->getId();
        $sitename = $this->siteName;

        return $this->accessRequestCreate($request,$id,$sitename);

    }
    public function accessRequestCreate($request,$id,$sitename) {

        //echo "create new accreq, post <br>";

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('OlegUserdirectoryBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        //$user->setAppliedforaccess('active');
        //$user->setAppliedforaccessdate( new \DateTime() );

        $secUtil = $this->get('order_security_utility');
        $userAccessReq = $secUtil->getUserAccessRequest($user,$sitename);

        $sitenameFull = $this->siteNameStr;

        //echo "sitename=".$sitename."<br>";

        if( $userAccessReq ) {

            if( $userAccessReq->getStatus() == AccessRequest::STATUS_APPROVED ) {
                $this->get('session')->getFlashBag()->add(
                    'warning',
                    "The status of your request is " . $userAccessReq->getStatusStr() . ". " .
                    "Please re-login to access this site " . "<a href=".$this->generateUrl($sitename.'_logout',UrlGeneratorInterface::ABSOLUTE_URL).">Re-Login</a>"
                );
                return $this->redirect( $this->generateUrl('main_common_home') );
            }

            //throw $this->createNotFoundException('AccessRequest is already created for this user');
            $transformer = new DateTimeToStringTransformer(null,null,'m/d/Y');
            $dateStr = $transformer->transform($userAccessReq->getCreatedate());

            $text = "You have requested access to ".$sitenameFull." on " . $dateStr . ". " .
                "The status of your request is " . $userAccessReq->getStatusStr() . "." .
                "Please contact the system administrator by emailing ".$this->container->getParameter('default_system_email')." if you have any questions.";

            return $this->render('OlegUserdirectoryBundle:AccessRequest:request_confirmation.html.twig',array('text'=>$text,'sitename'=>$sitename,'pendinguser'=>true));
        }

        //Create a new active AccessRequest
        $accReq = new AccessRequest();
        $accReq->setStatus(AccessRequest::STATUS_ACTIVE);
        $accReq->setUser($user);
        $accReq->setSiteName($sitename);

        $params = $this->getParams();
        $form = $this->createForm(AccessRequestType::class, $accReq, array('form_custom_value'=>$params));

        //$request = $this->get('request');
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() ) {
            //exit('new access request valid !!!');
            //echo "email=".$accReq->getEmail()."<br>";
        } else {
            //exit('Access Request form is not valid.');
        }

        //exit('new access request flush');
        $em->persist($accReq);
        $em->flush();

        $email = $user->getEmail();
        $emailStr = "";
        if( $email && $email != "" ) {
            $emailStr = "\r\nConfirmation email was sent to ".$email;
        }

        $emailUtil = $this->get('user_mailer_utility');

        $siteurl = $this->generateUrl( $sitename.'_home', array(), UrlGeneratorInterface::ABSOLUTE_URL );

        $emailBody = 'Your access request for the ' .
            //'<a href="'.$siteurl.'">'.$sitenameFull.'</a>' .
            $sitenameFull . ': ' . $siteurl . ' ' .
            ' was successfully submitted and and will be reviewed.'.$emailStr;

        $emailUtil->sendEmail( $email, "Access request confirmation for site: ".$sitenameFull, $emailBody );

        $text = 'Your access request was successfully submitted and will be reviewed.'.$emailStr;

        ///////////////// Send an email to the preferred emails of the users who have Administrator role for a given site and CC the users with Platform Administrator role when an access request is submitted
        //$incomingReqPage = $this->generateUrl( $sitename.'_home', array(), true );
        $subject = "[O R D E R] Access request for the ".$sitenameFull." site received from ".$user->getUsernameOptimal();
        $msg = $user->getUsernameOptimal()." submitted a request to access the ".$sitenameFull." site (".$siteurl.").";
        //$msg = $msg . " Please visit ".$incomingReqPage." to approve or deny it.";

        $approveDeclineMsg = "the access request from ".$user->getUsernameOptimal()." for the ".$sitenameFull." site, visit the following link:";

//        //add approve link
//        $approvedLink = $this->generateUrl( $sitename.'_accessrequest_change', array("id"=>$id,"status"=>"approve"), true );
//        $approvedMsg = "To approve " . $approveDeclineMsg . "\r\n" . $approvedLink;
//
//        //add decline link
//        $declinedLink = $this->generateUrl( $sitename.'_accessrequest_change', array("id"=>$id,"status"=>"decline"), true );
//        $declinedMsg = "To decline " . $approveDeclineMsg . "\r\n" . $declinedLink;
//
//        $msg = $msg . "\r\n"."\r\n" . $approvedMsg . "\r\n"."\r\n" . $declinedMsg;

        //add access request management link
        $managementLink = $this->generateUrl( $sitename.'_accessrequest_management', array("id"=>$accReq->getId()), UrlGeneratorInterface::ABSOLUTE_URL );
        $managementMsg = "To review, approve, or deny " . $approveDeclineMsg . "\r\n" . $managementLink;

        $msg = $msg . "\r\n"."\r\n" . $managementMsg;

        //Verena-Wilberth Sailer has supplied the following information:
        $msg .= "\r\n"."\r\n" . $user->getUsernameOptimal() . " has supplied the following information:"."\r\n";
        $msg .= "\r\n"."E-Mail: ".$accReq->getEmail();
        $msg .= "\r\n"."Phone Number: ".$accReq->getPhone();
        $msg .= "\r\n"."Job Title: ".$accReq->getJob();
        $msg .= "\r\n"."Organizational Group: ".$accReq->getOrganizationalGroup();
        $msg .= "\r\n"."Reason for Access Request: ".$accReq->getReason();
        $msg .= "\r\n"."Access permissions similar to (user name): ".$accReq->getSimilaruser();
        $msg .= "\r\n"."Reference Name: ".$accReq->getReferencename();
        $msg .= "\r\n"."Reference E-Mail: ".$accReq->getReferenceemail();
        $msg .= "\r\n"."Reference Phone Number: ".$accReq->getReferencephone();

        $userSecUtil = $this->get('user_security_utility');
        $emails = $userSecUtil->getUserEmailsByRole($sitename,"Administrator");
        $headers = $userSecUtil->getUserEmailsByRole($sitename,"Platform Administrator");

        if( !$emails ) {
            $emails = $headers;
            $headers = null;
        }

        //$emails = "oli2002@med.cornell.edu";
//        $logstr = "user emails=".implode(";",$emails)."<br>";
//        $logstr .= "user headers=".implode(";",$headers)."<br>";
//        echo $logstr;
//        $logger = $this->container->get('logger');
//        $logger->notice($logstr);
        //exit('1');

        $emailUtil->sendEmail( $emails, $subject, $msg, $headers );
        ///////////////// EOF /////////////////

        //auto-log out after submitting an access request as described in issue #478 (6)
        //Differentiate between the two situations:
        //A- User just logged in and requested access
        //B- User logged in to use another site, then clicked on a site they have no access to and "requested access"...
        //$request = $this->get('request');
        $session = $request->getSession();
        if( $session->get('sitename') == $sitename ) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                $text
            );
            //$this->get('security.context')->setToken(null);
            $this->get('security.token_storage')->setToken(null);
            return $this->redirect($this->generateUrl($sitename . '_login'));
        } else {
            return $this->render('OlegUserdirectoryBundle:AccessRequest:request_confirmation.html.twig', array('text' => $text, 'sitename' => $sitename, 'pendinguser' => true));
        }
    }

//    public function reLoginUser($sitename) {
//        echo "relogin sitename=".$sitename."<br>";
//        $this->get('session')->getFlashBag()->add(
//            'warning',
//            "You must re-login to access this site " . "<a href=".$this->generateUrl($sitename.'_logout',true).">Re-Login</a>"
//        );
//        return $this->redirect( $this->generateUrl('main_common_home') );
//    }

    /**
     * No, thanks.
     *
     * @Route("/no-thanks-access-requests/{sitename}", name="employees_no_thanks_accessrequest")
     * @Method("GET")
     * @Template()
     */
    public function noThanksAccessRequestAction( Request $request, $sitename )
    {
        $session = $request->getSession();
        if( $session->get('sitename') == $sitename ) {
            return $this->redirect($this->generateUrl($sitename . '_logout'));
        } else {
            return $this->redirect( $this->generateUrl("main_common_home") );
        }
    }


    /**
     * Lists all Access Request.
     *
     * @Route("/access-requests", name="employees_accessrequest_list")
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request_list.html.twig")
     */
    public function accessRequestIndexAction(Request $request)
    {
        if( false === $this->get('security.authorization_checker')->isGranted($this->roleEditor) ) {
            return $this->redirect( $this->generateUrl($this->siteName."-nopermission") );
        }

        return $this->accessRequestIndexList($request,$this->siteName);
    }
    public function accessRequestIndexList( $request, $sitename ) {

        $em = $this->getDoctrine()->getManager();

        $roles = $em->getRepository('OlegUserdirectoryBundle:Roles')->findAll();
        $rolesArr = array();
        foreach( $roles as $role ) {
            $rolesArr[$role->getName()] = $role->getAlias();
        }

        $repository = $this->getDoctrine()->getRepository('OlegUserdirectoryBundle:AccessRequest');
        $dql =  $repository->createQueryBuilder("accreq");
        $dql->select('accreq');
        $dql->leftJoin('accreq.user','user');
        $dql->leftJoin('user.infos','infos');
        $dql->leftJoin('user.keytype','keytype');
        $dql->leftJoin('accreq.updatedby','updatedby');
        $dql->leftJoin('updatedby.infos','updatedbyinfos');
        $dql->where("accreq.siteName = '" . $sitename . "'" );
        //$dql->where("accreq.status = ".AccessRequest::STATUS_ACTIVE." OR accreq.status = ".AccessRequest::STATUS_DECLINED." OR accreq.status = ".AccessRequest::STATUS_APPROVED);
        
		//$request = $this->get('request');
		$postData = $request->query->all();
		
		if( !isset($postData['sort']) ) { 
			$dql->orderBy("accreq.status","DESC");
		}
		
		//pass sorting parameters directly to query; Somehow, knp_paginator stoped correctly create pagination according to sorting parameters       
//		if( isset($postData['sort']) ) {
//            $dql = $dql . " ORDER BY $postData[sort] $postData[direction]";
//        }

        $limit = 30;
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1), /*page number*/
            $limit,     /*limit per page*/
            array(
                'defaultSortFieldName' => 'accreq.createdate',
                'defaultSortDirection' => 'DESC',
                'wrap-queries'=>true
            )
        );

        $sitenameFull = $this->siteNameStr;

        return array(
            'entities' => $pagination,
            'roles' => $rolesArr,
            'sitename' => $sitename,
            'sitenameshowuser' => $this->siteNameShowuser,
            'sitenamefull' => $sitenameFull
        );

    }


    /**
     * @Route("/access-requests/change-status/{id}/{status}", name="employees_accessrequest_change", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function accessRequestChangeAction(Request $request, $id, $status)
    {

        if (false === $this->get('security.authorization_checker')->isGranted($this->roleEditor)) {
            return $this->redirect( $this->generateUrl($this->siteName."-nopermission") );
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegUserdirectoryBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $userSecUtil = $this->get('user_security_utility');
        $accReq = $userSecUtil->getUserAccessRequest($id,$this->siteName);

        if( !$accReq ) {
            throw new \Exception( 'AccessRequest is not found by id=' . $id );
        }

        $this->changeStatus( $accReq, $status, $entity, $request );

        return $this->redirect($this->generateUrl($this->siteName.'_accessrequest_list'));
    }

    public function changeStatus( $accReq, $status, $entity, $request, $sendEmail=true ) {

        $em = $this->getDoctrine()->getManager();

        //save original institutions for log
        $originalInsts = new ArrayCollection();
        if( $entity->getPerSiteSettings() ) {
            foreach ($entity->getPerSiteSettings()->getPermittedInstitutionalPHIScope() as $item) {
                $originalInsts->add($item);
            }
        }

        //save original roles for log
        $originalRoles = array();
        foreach( $entity->getRoles() as $item) {
            $originalRoles[] = $item;
        }

        if( $status == "approved" || $status == "approve" ) {
            //$entity->setRoles(array());
            $entity->removeRole($this->roleUnapproved);
            $entity->removeRole($this->roleBanned);

            if( $em->getRepository('OlegUserdirectoryBundle:Roles')->findOneByName($this->roleUser) ) {
                $entity->addRole($this->roleUser);
            }

            $this->addOptionalApproveRoles($entity);

            if( $accReq )
                $accReq->setStatus(AccessRequest::STATUS_APPROVED);
        }

        if( $status == "declined" || $status == "decline" ) {
            //1 way) remove general user rolw and add Banned role
            //$entity->removeRole($this->roleUser);
            //$entity->addRole($this->roleBanned);

            //2 way) New: If "Yes" is pressed, all roles belonging to this user and attached to this site should be removed.
            $userSecUtil = $this->get('user_security_utility');
            $roles = $userSecUtil->getUserRolesBySite( $entity, $this->siteName );
            foreach( $roles as $role ) {
                //echo "role=".$role->getName()."<br>";
                $entity->removeRole($role->getName());
            }
            //exit('1');

            $this->removeOptionalDeclineRoles($entity);

            if( $accReq )
                $accReq->setStatus(AccessRequest::STATUS_DECLINED);
        }

        if( $status == "active" ) { //not used now
            //$entity->setRoles(array());
            $entity->removeRole($this->roleUser);

            $this->removeOptionalDeclineRoles($entity);

            $entity->addRole($this->roleUnapproved);
            if( $accReq )
                $accReq->setStatus(AccessRequest::STATUS_ACTIVE);
        }

        //set updated by and updated author roles
        if( $accReq ) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $accReq->setUpdatedby($user);
            $accReq->setUpdateAuthorRoles($user->getRoles());
            $em->persist($accReq);
        }

        $em->persist($entity);
        $em->flush();

        //////// When the user's Access Request has been approved, send an email to the user from the email address in Site Settings with... ////
        if( $sendEmail ) {
            //echo "emailNotification !";
            $this->createAccessRequestUserNotification( $entity, $status, $this->siteName );
        }
        //////////////////// EOF ////////////////////


        //save original for log
        $this->logAuthorizationChanges($request,$this->siteName,$entity,$originalInsts,$originalRoles);
    }

    public function addOptionalApproveRoles($entity) {
        return;
    }
    public function removeOptionalDeclineRoles($entity) {
        return;
    }

    public function createAccessRequestUserNotification( $subjectUser, $status, $sitename ) {

        $sitenameFull = $this->siteNameStr." site";

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $siteLink = $this->generateUrl( $sitename.'_home', array(), UrlGeneratorInterface::ABSOLUTE_URL );
        $newline = "\r\n";
        $msg = "";

        if( $user->getEmail() ) {
            $adminEmail = $user->getEmail();
            $adminEmailStr = " (".$adminEmail.")";
        } else {
            $adminEmailStr = "";
        }

        if( $status == "approved" || $status == "approve" ) {
            $subject = "Your request to access ".$sitenameFull." has been approved";  //"Access granted to site: ".$sitenameFull;

            $msg = $subjectUser->getUsernameOptimal().",".$newline.$newline.
                "Your request to access ".$sitenameFull.": ".$siteLink." has been approved. You can now log in using the user name ".
                $subjectUser->getPrimaryUseridKeytypeStr()." and your password.".$newline.$newline.
                $user->getUsernameOptimal().$adminEmailStr;
        }


        if( $status == "declined" || $status == "decline" ) {
            $subject = "Your request to access ".$sitenameFull." has been denied";

            $msg = $subjectUser->getUsernameOptimal().",".$newline.$newline.
                "Your request to access ".$sitenameFull.": ".$siteLink." has been denied. For additional details please email ".$user->getUsernameOptimal().$adminEmailStr.".".$newline.$newline.
                $user->getUsernameOptimal().$adminEmailStr;
        }

        if( $status == "updated" || $status == "update" ) {

            $userSecUtil = $this->get('user_security_utility');
            $roles = $userSecUtil->getUserRolesBySite( $subjectUser, $sitename );

            $subject = "Your access for ".$sitenameFull." has been updated";

            $msg = $subjectUser->getUsernameOptimal().",".$newline.$newline.
                "Your access for ".$sitenameFull.": ".$siteLink." has been updated.".$newline.
                "Current roles: " . implode(", ",$roles).$newline.
                "For additional details please email ".$user->getUsernameOptimal().$adminEmailStr.".".$newline.$newline.
                $user->getUsernameOptimal().$adminEmailStr;
        }

        if( $msg != "" ) {
            $email = $subjectUser->getEmail();
            $emailUtil = $this->get('user_mailer_utility');
            //                 $email, $subject, $message, $em, $ccs=null, $adminemail=null
            $emailUtil->sendEmail( $email, $subject, $msg, null, $adminEmail );
        }
    }


    //access request management page with the process to force the admin to select the "PHI Scope" Institution(s) and "Role(s)"
    /**
     * @Route("/access-requests/{id}", name="employees_accessrequest_management", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request_management.html.twig")
     */
    public function accessRequestManagementAction( Request $request, $id )
    {

        if (false === $this->get('security.authorization_checker')->isGranted($this->roleEditor)) {
            return $this->redirect( $this->generateUrl($this->siteName."-nopermission") );
        }

        $em = $this->getDoctrine()->getManager();

        //$entity = $em->getRepository('OlegUserdirectoryBundle:User')->find($id);
        $accReq = $em->getRepository('OlegUserdirectoryBundle:AccessRequest')->find($id);

        if (!$accReq) {
            throw $this->createNotFoundException('Unable to find Access Request entity with ID ' . $id);
        }

        //$userSecUtil = $this->get('user_security_utility');
        //$accReq = $userSecUtil->getUserAccessRequest($id,$this->siteName);

        $entity = $accReq->getUser();
        if( !$entity ) {
            throw new \Exception( 'User is not found in Access Request with ID' . $id );
        }

        ////////////////// lowest roles /////////////////////
//        //if a user does not have any siteroles => pre-populate the Role with a role that has the lowest permissions on this site
//        //user's roles associated with this site
//        $securityUtil = $this->get('order_security_utility');
//        $siteRoles = $securityUtil->getUserRolesBySite( $entity, $this->siteName );
//        if( count($siteRoles) == 0 ) {
//            //pre-populate the Role with a role that has the lowest permissions on this site
//            $lowestSiteRoles = $securityUtil->getLowestRolesBySite( $this->siteName );
//            foreach( $lowestSiteRoles as $lowestSiteRole ) {
//                $entity->addRole($lowestSiteRole);
//            }
//        }
        $this->addLowestRolesToUser( $entity, $this->siteName );
        ////////////////// EOF lowest roles /////////////////////

        //Roles
        $securityUtil = $this->get('order_security_utility');
        $rolesArr = $securityUtil->getSiteRolesKeyValue($this->siteName);

        $params = array(
            //'institutions' => $institutions,
            'sitename' => $this->siteNameStr,
            'roles' => $rolesArr,
            'simple-form' => true //show only permittedInstitutionalPHIScope
        );
        $form = $this->createForm(AuthorizitaionUserType::class, $entity, array('form_custom_value'=>$params));

        $userViewArr = array(
            'form' => $form->createView(),
            'accreq' => $accReq,
            'entity' => $entity,
            'sitename' => $this->siteName,
            'sitenameshowuser' => $this->siteNameShowuser,
            'sitenamefull'=>$this->siteNameStr,
            'routename' => $request->get('_route'),
            'routenameshort' => 'accessrequest_management'
        );


        return $userViewArr;
    }

    /**
     * @Route("/access-requests/submit/{id}", name="employees_accessrequest_management_submit", requirements={"id" = "\d+"})
     * @Method("POST")
     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request_management.html.twig")
     */
    public function accessRequestManagementSubmitAction( Request $request, $id )
    {

        if (false === $this->get('security.authorization_checker')->isGranted($this->roleEditor)) {
            return $this->redirect( $this->generateUrl($this->siteName."-nopermission") );
        }

        $em = $this->getDoctrine()->getManager();

        $accReq = $em->getRepository('OlegUserdirectoryBundle:AccessRequest')->find($id);

        if (!$accReq) {
            throw $this->createNotFoundException('Unable to find Access Request entity with ID ' . $id);
        }

        $entity = $accReq->getUser();
        if( !$entity ) {
            throw new \Exception( 'User is not found in Access Request with ID' . $id );
        }

        //Original Roles not associated with this site
        $securityUtil = $this->get('order_security_utility');
        $originalOtherRoles = $securityUtil->getUserRolesBySite( $entity, $this->siteName, false );

        $rolesArr = $securityUtil->getSiteRolesKeyValue($this->siteName);

        $params = array(
            //'institutions' => $institutions,
            'sitename' => $this->siteNameStr,
            'roles' => $rolesArr,
            'simple-form' => true //show only permittedInstitutionalPHIScope
        );
        $form = $this->createForm(AuthorizitaionUserType::class, $entity, array('form_custom_value'=>$params));

//        $userViewArr = array(
//            'form' => $form->createView(),
//            'accreq' => $accReq,
//            'entity' => $entity,
//            'sitename' => $this->siteName,
//            'sitenameshowuser' => $this->siteNameShowuser,
//            'sitenamefull'=>$this->siteNameStr
//        );

        //save original for log
        $originalInsts = new ArrayCollection();
        if( $entity->getPerSiteSettings() ) {
            foreach ($entity->getPerSiteSettings()->getPermittedInstitutionalPHIScope() as $item) {
                $originalInsts->add($item);
            }
        }
        $originalRoles = array();
        foreach( $entity->getRoles() as $item) {
            $originalRoles[] = $item;
        }

//        $form->submit($request);
        $form->handleRequest($request);

        if( $form->isValid() ) {

            $this->processUserAuthorization( $request, $entity, $originalOtherRoles );

            /////////////// update status //////////////////////
            $emailNotification = $form['emailNotification']->getData();
            if( $emailNotification ) {
                $sendEmail = true;
            } else {
                $sendEmail = false;
            }

            if( $request->request->has('accessrequest-approve') ) {
                $this->changeStatus( $accReq, "approve", $entity, $request, $sendEmail );
                $em->persist($accReq);
                $em->flush($accReq);
            }

            if( $request->request->has('accessrequest-decline') ) {
                $this->changeStatus( $accReq, "decline", $entity, $request, $sendEmail );
                $em->persist($accReq);
                $em->flush($accReq);
            }
            /////////////// EOF update status //////////////////////

            $this->logAuthorizationChanges($request,$this->siteName,$entity,$originalInsts,$originalRoles);

            //exit('valid');
        }
        //exit('not valid');

        //return $this->redirect($this->generateUrl($this->siteName.'_accessrequest_management',array('id'=>$id)));
        return $this->redirect($this->generateUrl($this->siteName.'_accessrequest_list'));
    }

    public function processUserAuthorization( $request, $entity, $originalOtherRoles ) {

        $em = $this->getDoctrine()->getManager();

        ///////////////// update roles /////////////////
        //add original roles not associated with this site
        foreach( $originalOtherRoles as $role ) {
            $entity->addRole($role);
        }

        $em->persist($entity);
        $em->flush($entity);
        ///////////////// EOF update roles /////////////////
    }


    /**
     * @Route("/deny-access-request/{userId}", name="employees_accessrequest_remove", requirements={"userId" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function accessRequestRemoveAction(Request $request,$userId)
    {

        if (false === $this->get('security.authorization_checker')->isGranted($this->roleEditor)) {
            return $this->redirect( $this->generateUrl($this->siteName."-nopermission") );
        }

        $this->authorizationRemove($request,$userId);

        return $this->redirect($this->generateUrl($this->siteName.'_accessrequest_list'));
    }

    /**
     * @Route("/revoke-access-authorization/{userId}", name="employees_authorization_remove", requirements={"userId" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function authorizationRemoveAction(Request $request, $userId)
    {

        if (false === $this->get('security.authorization_checker')->isGranted($this->roleEditor)) {
            return $this->redirect( $this->generateUrl($this->siteName."-nopermission") );
        }

        $this->authorizationRemove($request,$userId);

        return $this->redirect($this->generateUrl($this->siteName.'_authorized_users'));
    }

    public function authorizationRemove($request,$userId) {
        $em = $this->getDoctrine()->getManager();

        $subjectuser = $em->getRepository('OlegUserdirectoryBundle:User')->find($userId);
        if (!$subjectuser) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

//        //find all user's roles with sitename only and remove them from the user
//        foreach( $subjectuser->getRoles() as $role ) {
//            $roleObject = $em->getRepository('OlegUserdirectoryBundle:Roles')->findOneByName($role);
//            if( $roleObject && $roleObject->hasSite( $this->siteName ) ) {
//                $subjectuser->removeRole($role);
//            }
//        }
//        $userSecUtil = $this->get('user_security_utility');
//        $accReq = $userSecUtil->getUserAccessRequest($userId,$this->siteName);
//        $accReq->setStatus(AccessRequest::STATUS_DECLINED);
//        $em->persist($subjectuser);
//        $em->persist($accReq);
//        $em->flush();

        //Previously Remove access authorization was working by adding Banned role
        $userSecUtil = $this->get('user_security_utility');
        $accReq = $userSecUtil->getUserAccessRequest($userId,$this->siteName);
        $this->changeStatus( $accReq, "decline", $subjectuser, $request );

    }






    /**
     * @Route("/authorization-user-manager/{id}", name="employees_authorization_user_management", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request_management.html.twig")
     */
    public function authorizationManagementAction( Request $request, $id )
    {

        if (false === $this->get('security.authorization_checker')->isGranted($this->roleEditor)) {
            return $this->redirect( $this->generateUrl($this->siteName."-nopermission") );
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegUserdirectoryBundle:User')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Usert entity with ID ' . $id);
        }

        $this->addLowestRolesToUser( $entity, $this->siteName );

        $securityUtil = $this->get('order_security_utility');

        //Roles
        $rolesArr = $securityUtil->getSiteRolesKeyValue($this->siteName);

        $params = array(
            //'institutions' => $institutions,
            'sitename' => $this->siteNameStr,
            'roles' => $rolesArr,
            'simple-form' => true //show only permittedInstitutionalPHIScope
        );
        $form = $this->createForm(AuthorizitaionUserType::class, $entity, array('form_custom_value'=>$params));

        //user's roles associated with this site
        $siteRoles = $securityUtil->getUserRolesBySite( $entity, $this->siteName );

        //TODO: check if this user is already authorized to access this site
        $userSecUtil = $this->get('user_security_utility');
        $accReq = $userSecUtil->getUserAccessRequest($id,$this->siteName);

        $userViewArr = array(
            'form' => $form->createView(),
            'entity' => $entity,
            'sitename' => $this->siteName,
            'sitenameshowuser' => $this->siteNameShowuser,
            'sitenamefull'=>$this->siteNameStr,
            'siteRoles'=>$siteRoles,
            'accreq' => $accReq,
            'routename' => $request->get('_route'),
            'routenameshort' => 'authorization_user_management'
        );

        return $userViewArr;
    }

    /**
     * @Route("/authorization-user-manager/submit/{id}", name="employees_authorization_user_management_submit", requirements={"id" = "\d+"})
     * @Method("POST")
     * @Template("OlegUserdirectoryBundle:AccessRequest:access_request_management.html.twig")
     */
    public function authorizationManagementSubmitAction( Request $request, $id )
    {

        if (false === $this->get('security.authorization_checker')->isGranted($this->roleEditor)) {
            return $this->redirect( $this->generateUrl($this->siteName."-nopermission") );
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegUserdirectoryBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity with ID ' . $id);
        }

        //user's original not associated with this site
        $securityUtil = $this->get('order_security_utility');
        $originalOtherRoles = $securityUtil->getUserRolesBySite( $entity, $this->siteName, false );

        $rolesArr = $securityUtil->getSiteRolesKeyValue($this->siteName);

        $params = array(
            'sitename' => $this->siteNameStr,
            'roles' => $rolesArr,
            'simple-form' => true //show only permittedInstitutionalPHIScope
        );
        $form = $this->createForm(AuthorizitaionUserType::class, $entity, array('form_custom_value'=>$params));

//        $userViewArr = array(
//            'form' => $form->createView(),
//            //'accreq' => $accReq,
//            'entity' => $entity,
//            'sitename' => $this->siteName,
//            'sitenameshowuser' => $this->siteNameShowuser,
//            'sitenamefull'=>$this->siteNameStr
//        );

        //save original for log
        $originalInsts = new ArrayCollection();
        if( $entity->getPerSiteSettings() ) {
            foreach ($entity->getPerSiteSettings()->getPermittedInstitutionalPHIScope() as $item) {
                $originalInsts->add($item);
            }
        }
        $originalRoles = array();
        foreach( $entity->getRoles() as $item) {
            $originalRoles[] = $item;
        }

        $form->handleRequest($request);

        if( $form->isValid() ) {
            $this->processUserAuthorization( $request, $entity, $originalOtherRoles );
            $em->flush();

            $this->logAuthorizationChanges($request,$this->siteName,$entity,$originalInsts,$originalRoles);

            $emailNotification = $form['emailNotification']->getData();
            if( $emailNotification ) {
                //echo "emailNotification !";
                $this->createAccessRequestUserNotification( $entity, "updated", $this->siteName );
            } else {
                //echo "no emailNotification";
            }
            //exit('1');

        }

        return $this->redirect($this->generateUrl($this->siteName.'_authorized_users'));
    }


    /**
     * @Route("/authorized-users/", name="employees_authorized_users")
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:AccessRequest:authorized_users.html.twig")
     */
    public function authorizedUsersAction( Request $request )
    {

        if (false === $this->get('security.authorization_checker')->isGranted($this->roleEditor)) {
            return $this->redirect( $this->generateUrl($this->siteName."-nopermission") );
        }

        $em = $this->getDoctrine()->getManager();
        //echo "sitename=".$this->siteName."<br>";

        /////////// Filter ////////////
        $siteRoles = $em->getRepository('OlegUserdirectoryBundle:User')->findRolesBySiteAndPartialRoleName($this->siteName,"");
        $params = array(
            'roles'=>$siteRoles,
        );
        $filterform = $this->createForm(AuthorizedUserFilterType::class, null,array(
            'method' => 'GET',
            'form_custom_value'=>$params
        ));
        $filterform->handleRequest($request);
        $roles = $filterform['roles']->getData();
        $search = $filterform['search']->getData();
        $condition = $filterform['condition']->getData();
        //echo "roles=".implode(", ",$roles)."<br>";
        /////////// EOF Filter ////////////

        //new simple user form: user type, user id
        $params = array(
            'cycle' => 'create',
            'readonly' => false,
            //'sc' => $this->get('security.context')
        );
        $form = $this->createForm(SimpleUserType::class,null,array('form_custom_value'=>$params));

        $userSecUtil = $this->get('user_security_utility');
        //$query = $userSecUtil->getQueryUserBySite( $this->siteName );

        $dql = $userSecUtil->getDqlUserBySite($this->siteName);

        if( $roles && count($roles) > 0 ) {
            $rolesArr = array();
            foreach ($roles as $role) {
                $rolesArr[] = "user.roles LIKE " . "'%" . $role->getName() . "%'";
            }
            //$rolesStr = implode(" OR ",$rolesArr);
            $rolesStr = implode(" $condition ", $rolesArr);
            $dql->andWhere($rolesStr);
        }

        if( $search ) {
            $searchStr = "user.primaryPublicUserId LIKE " . "'%" . $search . "%'";
            $searchStr = $searchStr . " OR " . "infos.email LIKE " . "'%" . $search . "%'";
            $searchStr = $searchStr . " OR " . "infos.lastName LIKE " . "'%" . $search . "%'";
            $searchStr = $searchStr . " OR " . "infos.firstName LIKE " . "'%" . $search . "%'";
            $searchStr = $searchStr . " OR " . "infos.displayName LIKE " . "'%" . $search . "%'";
            $searchStr = $searchStr . " OR " . "infos.preferredPhone LIKE " . "'%" . $search . "%'";

            $dql->andWhere($searchStr);
        }

        $query = $em->createQuery($dql);

        //echo "query=".$query->getSql()."<br>";
        //$users = $query->getResult();
        //echo "users count=".count($users)."<br>";
        //exit('1');

        $limit = 20;
        $paginator  = $this->get('knp_paginator');
        $users = $paginator->paginate(
            $query,
            $request->query->get('page', 1),   /*page number*/
            $limit,                             /*limit per page*/
            array('wrap-queries'=>true)         //don't need it with "doctrine/orm": "v2.4.8"
            //array('defaultSortFieldName' => 'infos.displayName', 'defaultSortDirection' => 'asc')
        );


        return array(
            'form' => $form->createView(),
            'filterform' => $filterform->createView(),
            'users' => $users,
            'sitename' => $this->siteName,
            'sitenameshowuser' => $this->siteNameShowuser,
            'sitenamefull'=>$this->siteNameStr
        );
    }


    /**
     * @Route("/add-authorized-user/", name="employees_add_authorized_user")
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:AccessRequest:add_authorized_user.html.twig")
     */
    public function addAuthorizedUserAction( Request $request )
    {

        if (false === $this->get('security.authorization_checker')->isGranted($this->roleEditor)) {
            return $this->redirect( $this->generateUrl($this->siteName."-nopermission") );
        }

        $keytype = $request->query->get('keytype');
        $keytype = trim($keytype);

        $primaryPublicUserIdStr = $request->query->get('primaryPublicUserId');
        $primaryPublicUserIdStr = trim($primaryPublicUserIdStr);

        //the string should be silently cut at the @ character to get cwid in case email was entered with @
        list($primaryPublicUserId,$email) = explode('@', $primaryPublicUserIdStr);

        //echo "sitename=".$this->siteName."<br>";
        //echo "usertype=(".$keytype.")<br>";
        //echo "userid=(".$primaryPublicUserId.")<br>";

        $em = $this->getDoctrine()->getManager();

        //find user in DB
        $users = $em->getRepository('OlegUserdirectoryBundle:User')->findBy(array('keytype'=>$keytype,'primaryPublicUserId'=>$primaryPublicUserId));

        if( count($users) > 1 ) {
            throw $this->createNotFoundException('Unable to find a Single User. Found users ' . count($users) );
        }

        if( count($users) == 1 ) {
            $subjectUser = $users[0];
        }

        if( count($users) == 0 ) {
            $keytypeObj = $em->getRepository('OlegUserdirectoryBundle:UsernameType')->find($keytype);
            $this->get('session')->getFlashBag()->set(
                'notice',
                'User ' . $primaryPublicUserId . ' (' . $keytypeObj . ')' . ' not found.'." ".
                "Please use the 'Create New User' form to add a new user."
            );
            return $this->redirect( $this->generateUrl("employees_new_user",array("user-type"=>$keytype,"user-name"=>$primaryPublicUserId)) );
            //exit("User not found;");
            //$subjectUser = new User();
        }

        //echo "subjectUser=".$subjectUser."<br>";

        return $this->redirect( $this->generateUrl($this->siteName."_authorization_user_management", array('id'=>$subjectUser->getId())) );

    }

    public function logAuthorizationChanges($request,$sitename,$entity,$originalInsts,$originalRoles) {

        $removedInfo = null;
        if( $entity->getPerSiteSettings() ) {
            $removedInfo = $this->recordToEvenLogDiffCollection($originalInsts, $entity->getPerSiteSettings()->getPermittedInstitutionalPHIScope(), "PermittedInstitutionalPHIScope");
        }
        if( $removedInfo ) {
            $removedCollections[] = $removedInfo;
        }

        $removedInfo = $this->recordToEvenLogDiffCollection($originalRoles,$entity->getRoles(),"Roles");
        if( $removedInfo ) {
            $removedCollections[] = $removedInfo;
        }

        //set Edit event log for removed collection and changed fields or added collection
        if( count($removedCollections) > 0 ) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $event = "User information of ".$entity." has been changed by ".$user.":"."<br>";
            $event = $event . "<br>" . implode("<br>", $removedCollections);
            $userSecUtil = $this->get('user_security_utility');
            $userSecUtil->createUserEditEvent($sitename,$event,$user,$entity,$request,'User record updated');
        }
    }
    //record if different: old values, new values
    public function recordToEvenLogDiffCollection($originalArr,$currentArr,$text) {
        $removeArr = array();

        $original = $this->listToArray($originalArr);
        $new = $this->listToArray($currentArr);

        $diff = array_diff($original, $new);

        if( count($original) != count($new) || count($diff) != 0 ) {
            $removeArr[] = "<strong>"."Original ".$text.": ".implode(", ",$original)."</strong>";
            $removeArr[] = "<strong>"."New ".$text.": ".implode(", ",$new)."</strong>";
        }

        return implode("<br>", $removeArr);
    }
    public function listToArray($collection) {
        $resArr = array();
        foreach( $collection as $item ) {
            $resArr[] = $item."";
        }
        return $resArr;
    }

    public function addLowestRolesToUser( $user, $sitename ) {
        //if a user does not have any siteroles => pre-populate the Role with a role that has the lowest permissions on this site
        //user's roles associated with this site
        $securityUtil = $this->get('order_security_utility');
        $siteRoles = $securityUtil->getUserRolesBySite( $user, $sitename );
        if( count($siteRoles) == 0 ) {
            //pre-populate the Role with a role that has the lowest permissions on this site
            $lowestSiteRoles = $securityUtil->getLowestRolesBySite( $sitename );
            foreach( $lowestSiteRoles as $lowestSiteRole ) {
                $user->addRole($lowestSiteRole);
            }
        }
    }



//    /**
//     * @Route("/add-authorized-user/submit/", name="employees_add_authorized_user_submit")
//     * @Method("POST")
//     * @Template("OlegUserdirectoryBundle:AccessRequest:add_authorized_user.html.twig")
//     */
//    public function addAuthorizedUserSubmitAction( Request $request )
//    {
//
//        if (false === $this->get('security.authorization_checker')->isGranted($this->roleEditor)) {
//            return $this->redirect( $this->generateUrl($this->siteName."-nopermission") );
//        }
//
//        //echo "sitename=".$this->siteName."<br>";
//
//        exit("submit a new autorized user");
//
//    }


    /**
     * @Route("/generated-users/", name="employees_generated_users")
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:AccessRequest:generated_users.html.twig")
     */
    public function generatedUsersAction(Request $request)
    {
//        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_USERDIRECTORY_EDITOR')) {
//            return $this->redirect($this->generateUrl('employees-nopermission'));
//        }
        if (false === $this->get('security.authorization_checker')->isGranted($this->roleEditor)) {
            return $this->redirect( $this->generateUrl($this->siteName."-nopermission") );
        }

        //return $this->generatedUsers($request,$this->container->getParameter('employees.sitename'));

        //exit("Under implementation for " . $this->siteName);

        $em = $this->getDoctrine()->getManager();

        $createdby = "manual-".$this->siteName;

        $repository = $this->getDoctrine()->getRepository('OlegUserdirectoryBundle:User');
        $dql =  $repository->createQueryBuilder("user");
        $dql->select('user');
        $dql->leftJoin('user.infos','infos');
        $dql->leftJoin('user.keytype','keytype');
        $dql->where("user.createdby = '" . $createdby . "'" );

        //$request = $this->get('request');
        $postData = $request->query->all();
        if( !isset($postData['sort']) ) {
            $dql->orderBy("user.createDate","DESC");
        }

        //pass sorting parameters directly to query; Somehow, knp_paginator stoped correctly create pagination according to sorting parameters
//		if( isset($postData['sort']) ) {
//            $dql = $dql . " ORDER BY $postData[sort] $postData[direction]";
//        }

        $limit = 30;
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $users = $paginator->paginate(
            $query,
            $request->query->get('page', 1), /*page number*/
            $limit,     /*limit per page*/
            array(
                'defaultSortFieldName' => 'user.createDate',
                'defaultSortDirection' => 'DESC',
                'wrap-queries'=>true
            )
            //array('wrap-queries'=>true)         //don't need it with "doctrine/orm": "v2.4.8"
        );

        //echo "query=".$query->getSql()."<br>";
        //$users = $query->getResult();
        //echo "users count=".count($users)."<br>";
        //exit('1');

        return array(
            'users' => $users,
            'sitename' => $this->siteName,
            'sitenameshowuser' => $this->siteNameShowuser,
            'sitenamefull'=>$this->siteNameStr
        );
    }

    /**
     * @Route("/generated-user/{id}", name="employees_generated_user_management")
     * @Template("OlegUserdirectoryBundle:AccessRequest:generated_user_management.html.twig")
     * @Method({"GET", "POST"})
     */
    public function generatedUserManagementAction(Request $request, User $user)
    {
        if (false === $this->get('security.authorization_checker')->isGranted($this->roleEditor)) {
            return $this->redirect( $this->generateUrl($this->siteName."-nopermission") );
        }

        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        $securityUtil = $this->get('order_security_utility');
        $newline = "\r\n";

        //set minimum role
        //if( count($user->getRoles()) == 0 ) {
        //    $user->addRole($this->roleUser);
        //}

        $originalPrimaryPublicUserId = $user->getPrimaryPublicUserId();
        $originalKeytype = $user->getKeytype();

        $rolesArr = $securityUtil->getSiteRolesKeyValue($this->siteName);

        $params = array(
            'sitename' => $this->siteNameStr,
            'roles' => $rolesArr,
        );
        $form = $this->createForm(GeneratedUserType::class, $user, array('form_custom_value'=>$params));

        $form->handleRequest($request);

        //validate
        if( !$user->getKeytype() ) {
            $form->get('keytype')->addError(new FormError('Primary Public User ID Type is not set.'));
        }

        if( !$user->getPrimaryPublicUserId() ) {
            $form->get('primaryPublicUserId')->addError(new FormError('Primary Public User ID.'));
        }

        //validate and update username
        if( $originalKeytype != $user->getKeytype() && $originalPrimaryPublicUserId != $user->getPrimaryPublicUserId() ) {
            $newUsername = $user->createUniqueUsername();

            //check if user already exists in DB
            $userManager = $this->container->get('fos_user.user_manager');
            $newUserDb = $userManager->findUserByUsername($newUsername);
            if( $newUserDb ) {
                //Error
                $form->get('primaryPublicUserId')->addError(new FormError('The user with the current Primary Public User ID and Type is already exists.'));
            }

            $user->setUsernameForce($newUsername);
            $user->setUsernameCanonicalForce($newUsername);
        }

        if( !$user->getFirstName() ) {
            $form->get('infos')[0]->get('firstName')->addError(new FormError('First Name is not set.'));
        }

        if( !$user->getLastName() ) {
            $form->get('infos')[0]->get('lastName')->addError(new FormError('Last Name is not set.'));
        }

        if( !$user->getEmail() ) {
            $form->get('infos')[0]->get('email')->addError(new FormError('Email is not set.'));
        }

        //Validate role
        $originalUserSiteRoles = $securityUtil->getUserRolesBySite( $user, $this->siteName, true );
        if( count($originalUserSiteRoles) == 0 ) {
            //Error
            $form->get('roles')->addError(new FormError('At least one role must be assigned.'));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            //exit('new');

            $passwordNote = $this->setUserPassword($user);

            //add user observer role
            $user->addRole("ROLE_USERDIRECTORY_OBSERVER");

            //$em->persist($user);
            //$em->flush($user);

            //$em->persist();
            $em->flush();

            if( $user->getLocked() ) {
                $lockedStr = "Locked";
            } else {
                $lockedStr = "Unlocked";
            }

            /////////////////// send emailNotification ///////////////////
            $emailNotification = $form['emailNotification']->getData();
            if( $emailNotification ) {

                $userSecUtil = $this->get('user_security_utility');

                if( $currentUser->getEmail() ) {
                    $adminEmail = $currentUser->getEmail();
                    $adminEmailStr = " (".$adminEmail.")";
                } else {
                    $adminEmailStr = "";
                }

                $roles = $userSecUtil->getUserRolesBySite( $user, $this->siteName );
                $siteLink = $this->generateUrl( $this->siteName.'_home', array(), UrlGeneratorInterface::ABSOLUTE_URL );
                $sitenameFull = $this->siteNameStr." site";

                $subject = "Your manually created account for ".$sitenameFull." has been updated";

                $msg = $user->getUsernameOptimal().",".$newline.$newline.
                    "Your manually created account for ".$sitenameFull." (".$siteLink.") has been updated.".$newline.
                    "Current roles: " . implode(", ",$roles).$newline.
                    "Status: ".$lockedStr.$newline.
                    $passwordNote.
                    "For additional details please email ".$currentUser->getUsernameOptimal().$adminEmailStr.".".$newline.$newline.
                    $currentUser->getUsernameOptimal().$adminEmailStr;

                $email = $user->getEmail();
                $emailUtil = $this->get('user_mailer_utility');
                //                    $email, $subject, $message, $em, $ccs=null, $adminemail=null
                $emailUtil->sendEmail( $email, $subject, $msg, null, $adminEmail );
            }
            /////////////////// EOF send emailNotification ///////////////////

            $msg = "Manually created user $user has been reviewed. Status: ".$lockedStr;

            $this->get('session')->getFlashBag()->add(
                'notice',
                $msg
            );

            //Event Log
            $event = "Manually created account ".$user." for ".$sitenameFull." has been changed by ".$currentUser."; Status: $lockedStr"."<br>";
            //$event = $event. "<br>" . $passwordNote; //for testing only. Disable for prod to hide plain password in the event log.
            $userSecUtil = $this->get('user_security_utility');
            $userSecUtil->createUserEditEvent($this->siteName,$event,$currentUser,$user,$request,'User Record Approved');

            return $this->redirectToRoute($this->siteName.'_generated_users');
        }

        return array(
            'sitename' => $this->siteName,
            'sitenameshowuser' => $this->siteNameShowuser,
            'sitenamefull' => $this->siteNameStr,
            'user' => $user,
            'form' => $form->createView(),
            'title' => "Review Manually Created User ".$user,
        );
    }

    /**
     * @Route("/generated-user/approve/{id}", name="employees_generated_user_approve")
     * @Method({"GET", "POST"})
     */
    public function generatedUserApproveAction(Request $request, User $user)
    {
        if (false === $this->get('security.authorization_checker')->isGranted($this->roleEditor)) {
            return $this->redirect($this->generateUrl($this->siteName . "-nopermission"));
        }

        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        $securityUtil = $this->get('order_security_utility');
        $newline = "\r\n";

        //unlock the user
        $user->setLocked(false);

        ///////////////// Password ///////////////////
        $passwordNote = $this->setUserPassword($user);
        ///////////////// EOF Password //////////////////

        //add user observer role
        $user->addRole("ROLE_USERDIRECTORY_OBSERVER");

        $em->flush();

        $lockedStr = "Unlocked";

        /////////////////// send emailNotification ///////////////////
        $emailNotification = true;
        if( $emailNotification ) {

            $userSecUtil = $this->get('user_security_utility');

            if( $currentUser->getEmail() ) {
                $adminEmail = $currentUser->getEmail();
                $adminEmailStr = " (".$adminEmail.")";
            } else {
                $adminEmailStr = "";
            }

            $roles = $userSecUtil->getUserRolesBySite( $user, $this->siteName );
            $siteLink = $this->generateUrl( $this->siteName.'_home', array(), UrlGeneratorInterface::ABSOLUTE_URL );
            $sitenameFull = $this->siteNameStr." site";

            $subject = "Your manually created account for ".$sitenameFull." has been updated";

            $msg = $user->getUsernameOptimal().",".$newline.$newline.
                "Your manually created account for ".$sitenameFull." (".$siteLink.") has been updated.".$newline.
                "Current roles: " . implode(", ",$roles).$newline.
                "Status: ".$lockedStr.$newline.
                $passwordNote.
                "For additional details please email ".$currentUser->getUsernameOptimal().$adminEmailStr.".".$newline.$newline.
                $currentUser->getUsernameOptimal().$adminEmailStr;

            $email = $user->getEmail();
            $emailUtil = $this->get('user_mailer_utility');
            //                    $email, $subject, $message, $em, $ccs=null, $adminemail=null
            $emailUtil->sendEmail( $email, $subject, $msg, null, $adminEmail );
        }
        /////////////////// EOF send emailNotification ///////////////////

        $msg = "Manually created user $user has been approved. Status: ".$lockedStr;

        $this->get('session')->getFlashBag()->add(
            'notice',
            $msg
        );

        //Event Log
        $event = "Manually created account ".$user." for ".$sitenameFull." has been approved by ".$currentUser."; Status: $lockedStr"."<br>";
        //$event = $event. "<br>" . $passwordNote; //for testing only. Disable for prod to hide plain password in the event log.
        $userSecUtil = $this->get('user_security_utility');
        $userSecUtil->createUserEditEvent($this->siteName,$event,$currentUser,$user,$request,'User Record Approved');

        return $this->redirectToRoute($this->siteName.'_generated_users');
    }

    public function setUserPassword( $user ) {
        $newline = "\r\n";
        $passwordNote = "";
        if( !$user->getPassword() && $user->getKeytype()->getName() == "Local User" ) {
            //exit("set password");

            //check salt
            if( !$user->getSalt() ) {
                $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
                $user->setSalt($salt);
            }

            //generate random password
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $plainPassword = substr($tokenGenerator->generateToken(), 0, 8); // 8 chars
            //exit("set password=[".$plainPassword."]");

            //encode password
            $encoderService = $this->container->get('security.encoder_factory');
            $encoder = $encoderService->getEncoder($user);
            $encoded = $encoder->encodePassword($plainPassword, $user->getSalt());
            //exit("set encoded=[".$encoded."]");

            $user->setPassword($encoded);

            $passwordNote = "Temporary Password (without single quotes): '".$plainPassword."'".$newline;
            $passwordNote = $passwordNote . "Please change your password in 'My Profile' page".$newline;
        }

        return $passwordNote;
    }

}
