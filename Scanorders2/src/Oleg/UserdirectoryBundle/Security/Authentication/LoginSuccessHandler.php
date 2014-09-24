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

    private $container;
    private $security;
    private $em;
    private $router;
    private $siteName;

    public function __construct( $container, SecurityContext $security, $em )
    {
        $this->container = $container;
        $this->router = $container->get('router');
        $this->security = $security;
        $this->em = $em;
        $this->siteName = $container->getParameter('employees.sitename');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {

        $response = null;

        $user = $token->getUser();
        $options = array();
        $em = $this->em;
        $userUtil = new UserUtil();
        //$secUtil = $this->container->get('user_security_utility');

        $options['sitename'] = $this->siteName;

        //echo "employees authentication success: Success. User=".$user.", setCreatedby=".$user->getCreatedby()."<br>";
        //exit;

        if( $this->security->isGranted('ROLE_USERDIRECTORY_BANNED') ) {
            $options['eventtype'] = 'Banned User Login Attempt';
            $options['event'] = 'Banned user login attempt to Employee Directory site';
            $userUtil->setLoginAttempt($request,$this->security,$em,$options);

            return new RedirectResponse( $this->router->generate($this->siteName.'_access_request_new',array('id'=>$user->getId(),'sitename'=>$this->siteName)) );
        }

        //detect if the user was first time logged in by ldap: assign role UNAPPROVED user
        //all users must have at least an OBSERVER role
        if( !$this->security->isGranted('ROLE_USERDIRECTORY_OBSERVER')  ) {
            //echo "assign role UNAPPROVED user <br>";
            $user->addRole('ROLE_USERDIRECTORY_UNAPPROVED');
        }

        if( $this->security->isGranted('ROLE_USERDIRECTORY_UNAPPROVED') ) {
            $options['eventtype'] = 'Unapproved User Login Attempt';
            $options['event'] = 'Unapproved user login attempt to Employee Directory site';
            $userUtil->setLoginAttempt($request,$this->security,$em,$options);

            return new RedirectResponse( $this->router->generate($this->siteName.'_access_request_new',array('id'=>$user->getId(),'sitename'=>$this->siteName)) );
        }

        $options['eventtype'] = "Successful Login";
        $options['event'] = 'Successful login to Employee Directory site';
        $response = new RedirectResponse($this->router->generate($this->siteName.'_home'));

        $userUtil->setLoginAttempt($request,$this->security,$em,$options);

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
        $options['event'] = 'Bad credentials provided on login for Employee Directory site';
        $options['serverresponse'] = $exception->getMessage();

        $userUtil->setLoginAttempt($request,$this->security,$em,$options);

        $request->getSession()->set(SecurityContextInterface::AUTHENTICATION_ERROR, $exception);

        $response = new RedirectResponse( $this->router->generate($this->siteName.'_login') );
        return $response;

    }

}