<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 10/7/13
 * Time: 11:24 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\OrderformBundle\Security\Authentication;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

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
        $this->siteName = $container->getParameter('scan.sitename');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {

        $response = null;

        $user = $token->getUser();
        $options = array();
        $em = $this->em;
        $userUtil = new UserUtil();
        $secUtil = $this->container->get('user_security_utility');

        echo "onAuthenticationSuccess: Success. User=".$user.", setCreatedby=".$user->getCreatedby()."<br>";
        //exit;

        if( $this->security->isGranted('ROLE_SCANORDER_BANNED') ) {
            $options = array('event'=>'Banned User Login Attempt');
            $userUtil->setLoginAttempt($request,$this->security,$em,$options);

            return new RedirectResponse( $this->router->generate($this->siteName.'_access_request_new',array('id'=>$user->getId(),'sitename'=>$this->siteName)) );
        }

        //detect if the user was first time logged in by ldap: assign role ROLE_SCANORDER_UNAPPROVED_SUBMITTER
        //all users eneterd by ldap must have approved access request
        if( !$this->security->isGranted('ROLE_SCANORDER_SUBMITTER')  ) {
            //echo "assign role ROLE_SCANORDER_UNAPPROVED_SUBMITTER <br>";
            $user->addRole('ROLE_SCANORDER_UNAPPROVED_SUBMITTER');
        }

        if( $this->security->isGranted('ROLE_SCANORDER_UNAPPROVED_SUBMITTER') ) {
            $options = array('event'=>'Unapproved User Login Attempt');
            $userUtil->setLoginAttempt($request,$this->security,$em,$options);

            return new RedirectResponse( $this->router->generate($this->siteName.'_access_request_new',array('id'=>$user->getId(),'sitename'=>$this->siteName)) );
        }

        //if( $user->hasRole('ROLE_SCANORDER_PROCESSOR') ) {
        if( $this->security->isGranted('ROLE_SCANORDER_PROCESSOR') ) {

            //echo "ROLE SCANORDER PROCESSOR <br>";
            //exit();

            //$response = new RedirectResponse($this->router->generate('incoming-scan-orders',array('filter_search_box[filter]' => 'All Not Filled')));
            $response = new RedirectResponse($this->router->generate('scan-order-home'));
            $options['event'] = "Successful Login";

        }
        elseif(
            $this->security->isGranted('ROLE_SCANORDER_SUBMITTER') ||
            $this->security->isGranted('ROLE_SCANORDER_ORDERING_PROVIDER')
        ) {


            if( 1 ) {
                //redirect all users to the home page
                $response = new RedirectResponse($this->router->generate('scan-order-home'));
                $options['event'] = "Successful Login";

            } else {
                //redirect non-processor users to the previously requested page before authentication
                $indexLastRoute = '_security.aperio_ldap_firewall.target_path';   //'last_route';
                $lastRoute = $request->getSession()->get($indexLastRoute);
                //exit("lastRoute=".$lastRoute."<br>");

                $loginpos = strpos($lastRoute, '/login');
                $nopermpos = strpos($lastRoute, '/no-permission');
                $nocheck = strpos($lastRoute, '/check/');
                //setloginvisit

                if( $lastRoute && $lastRoute != '' && $lastRoute && $loginpos === false && $nopermpos === false && $nocheck === false ) {
                    //$referer_url = $this->router->generate( $lastRoute );
                    $referer_url = $lastRoute;
                } else {
                    $referer_url = $this->router->generate('scan-order-home');
                }

                //echo("referer_url=".$referer_url);
                //exit('<br>not processor');

                $response = new RedirectResponse($referer_url);

                $options['event'] = "Successful Login";

            }

        }
        else {

            //echo "user role not ok!";
            //exit();
            $response = new RedirectResponse( $this->router->generate($this->siteName.'_logout') );
            $options['event'] = "Unsuccessful Login Attempt. Wrong Role: user is not processor or submitter/ordering provider submitter";
            
        }

//        $lastRouteArr = $request->getSession()->get('last_route_arr');
//        echo "<br>lastRouteArr:<br>";
//        print_r($lastRouteArr);
//        $request->getSession()->set('last_route_arr', array());
//        echo "Session:<br>";
//        print_r($request->getSession());
//        exit("<br>");

        $userUtil->setLoginAttempt($request,$this->security,$em,$options);

        return $response;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        //error_log('You are out!');
        //echo "user is not ok!. Exception=<br>".$exception."<br>";
        //exit("user is not ok!");

        $options = array();
        $em = $this->em;
        $userUtil = new UserUtil();

        $options['event'] = "Bad Credentials";
        $options['serverresponse'] = $exception->getMessage();

        $userUtil->setLoginAttempt($request,$this->security,$em,$options);

        $request->getSession()->set(SecurityContextInterface::AUTHENTICATION_ERROR, $exception);

        $response = new RedirectResponse( $this->router->generate($this->siteName.'_login') );
        return $response;

    }

}