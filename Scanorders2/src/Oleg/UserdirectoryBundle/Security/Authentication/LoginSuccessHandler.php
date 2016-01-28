<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 10/7/13
 * Time: 11:24 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\UserdirectoryBundle\Security\Authentication;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;

use Oleg\UserdirectoryBundle\Util\UserUtil;


class LoginSuccessHandler implements AuthenticationFailureHandlerInterface, AuthenticationSuccessHandlerInterface {

    protected $container;
    protected $security;
    protected $em;
    protected $router;
    protected $siteName;
    protected $siteNameStr;
    protected $roleBanned;
    protected $roleUser;
    protected $roleUnapproved;
    protected $firewallName;

    public function __construct( $container, SecurityContext $security, $em )
    {
        $this->container = $container;
        $this->router = $container->get('router');
        $this->security = $security;
        $this->em = $em;
        $this->siteName = $container->getParameter('employees.sitename');
        $this->siteNameStr = 'Employee Directory';
        $this->roleBanned = 'ROLE_USERDIRECTORY_BANNED';
        $this->roleUser = 'ROLE_USERDIRECTORY_OBSERVER';
        $this->roleUnapproved = 'ROLE_USERDIRECTORY_UNAPPROVED';
        $this->firewallName = 'ldap_employees_firewall';
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {

        $response = null;

        $user = $token->getUser();
        $options = array();
        $em = $this->em;
        $userUtil = new UserUtil();
        //$secUtil = $this->container->get('user_security_utility');

        $options['sitename'] = $this->siteName;

        //echo "userdirectory: employees authentication success: Success. User=".$user.", setCreatedby=".$user->getCreatedby()."<br>";
        //exit;

        //echo "roleBanned=".$this->roleBanned."<br>";
        //echo "siteName=".$this->siteName."<br>";
        
        ////////// set session variables: maxIdleTime ////////       
        $res = $userUtil->getMaxIdleTimeAndMaintenance($em,$this->security,$this->container);       
        $session = $request->getSession();
        
        //set max idle time
        $maxIdleTime = $res['maxIdleTime'];
        $session->set('maxIdleTime',$maxIdleTime);
        
        //set site email
        $siteEmail = $userUtil->getSiteSetting($em,'siteEmail');       
        $session->set('siteEmail',$siteEmail);
        //////// EOF session //////////////////////////////

        if( $this->security->isGranted($this->roleBanned) ) {
            $options['eventtype'] = 'Banned User Login Attempt';
            $options['event'] = 'Banned user login attempt to '.$this->siteNameStr.' site';
            $userUtil->setLoginAttempt($request,$this->security,$em,$options);
            //exit('banned user');
            return new RedirectResponse( $this->router->generate($this->siteName.'_access_request_new') );
        }

        //detect if the user was first time logged in by ldap: assign role UNAPPROVED user
        //all users must have at least an OBSERVER role
        if( !$this->security->isGranted($this->roleUser)  ) {
            //echo "assign role UNAPPROVED user <br>";
            //exit('UNAPPROVED user');
            $user->addRole($this->roleUnapproved);
        }

        if( $this->security->isGranted($this->roleUnapproved) ) {
            $options['eventtype'] = 'Unapproved User Login Attempt';
            $options['event'] = 'Unapproved user login attempt to '.$this->siteNameStr.' site';
            $userUtil->setLoginAttempt($request,$this->security,$em,$options);
            //exit('Unapproved user');
            return new RedirectResponse( $this->router->generate($this->siteName.'_access_request_new') );
        }

        //exit('user ok');
        $options['eventtype'] = "Successful Login";
        $options['event'] = 'Successful login to '.$this->siteNameStr.' site';

        $userUtil->setLoginAttempt($request,$this->security,$em,$options);

        //Issue #381: redirect non-processor users to the previously requested page before authentication

        //$response = new RedirectResponse($this->router->generate($this->siteName.'_home'));
        //return $response;

        //I should be redirected to the URL I was trying to visit after login.
        $indexLastRoute = '_security.'.$this->firewallName.'.target_path';
        $lastRoute = $request->getSession()->get($indexLastRoute);

        $loginpos = strpos($lastRoute, '/login');
        $nopermpos = strpos($lastRoute, '/no-permission');
        $nocheck = strpos($lastRoute, '/check/');
        $keepalive = strpos($lastRoute, '/keepalive');
        $idlelogout = strpos($lastRoute, '/idlelogout');
        $common = strpos($lastRoute, '/common/');

        $filedownload = strpos($lastRoute, '/file-download');
        if( $filedownload ) {
            $lastRouteArr = explode("/", $lastRoute);
            $fileid = $lastRouteArr[count($lastRouteArr)-1];
            $referer_url = $this->router->generate($this->siteName.'_thankfordownloading',array('id'=>$fileid,'sitename'=>$this->siteName));
            $response = new RedirectResponse($referer_url);
            //exit('thankfordownloading');
            return $response;
        }


        //echo "keepalive=".$keepalive."<br>";
        //echo "lastRoute=".$lastRoute."<br>";


        if( 
            $lastRoute && $lastRoute != '' && 
            $loginpos === false && $nopermpos === false && 
            $nocheck === false && $keepalive === false && 
            $idlelogout === false && $common === false 
        ) {
            $referer_url = $lastRoute;
        } else {
            $referer_url = $this->router->generate($this->siteName.'_home');
        }

        //echo("referer_url=".$referer_url);
        //exit();

        $response = new RedirectResponse($referer_url);
        return $response;

    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        //error_log('You are out!');
        //echo "user is not ok!. Exception=<br>".$exception."<br>";
        //exit("user is not ok!");
        //throw new \Exception( 'user is not ok!' );

        $options = array();
        $em = $this->em;
        $userUtil = new UserUtil();

        $options['sitename'] = $this->siteName;
        $options['eventtype'] = "Bad Credentials";
        $options['event'] = 'Bad credentials provided on login for '.$this->siteNameStr.' site';
        $options['serverresponse'] = $exception->getMessage();

        //testing
        $userUtil->setLoginAttempt($request,$this->security,$em,$options);

        $request->getSession()->set(SecurityContextInterface::AUTHENTICATION_ERROR, $exception);

        $response = new RedirectResponse( $this->router->generate($this->siteName.'_login') );
        return $response;

    }

}