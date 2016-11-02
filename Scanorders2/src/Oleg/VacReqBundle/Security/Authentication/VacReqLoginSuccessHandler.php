<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 10/7/13
 * Time: 11:24 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\VacReqBundle\Security\Authentication;

use Oleg\UserdirectoryBundle\Security\Authentication\LoginSuccessHandler;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;



class VacReqLoginSuccessHandler extends LoginSuccessHandler {


    public function __construct( $container, SecurityContext $security, $em )
    {
        $this->container = $container;
        $this->router = $container->get('router');
        $this->security = $security;
        $this->em = $em;
        $this->siteName = $container->getParameter('vacreq.sitename');
        $this->siteNameStr = 'Vacation Request System';
        $this->roleBanned = 'ROLE_VACREQ_BANNED';
        $this->roleUser = 'ROLE_VACREQ_USER';
        $this->roleUnapproved = 'ROLE_VACREQ_UNAPPROVED';
        $this->firewallName = 'ldap_vacreq_firewall';
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        //return parent::onAuthenticationSuccess($request,$token);

        $redirectResponse = parent::onAuthenticationSuccess($request,$token);

        if( $this->security->isGranted("ROLE_VACREQ_ADMIN") ) {
            return $redirectResponse;
        }

        $url = $redirectResponse->getTargetUrl();
        //echo "url=".$url."<br>";

        $em = $this->em;
        $user = $token->getUser();

        //check other user's vacreq roles
        //$user, $sitename, $rolePartialName, $institutionId=null
        $institutionId = null;
        $roles = $em->getRepository('OlegUserdirectoryBundle:User')->
            findUserRolesBySiteAndPartialRoleName($user,'vacreq',"ROLE_VACREQ",$institutionId);
        //echo "roles count=".count($roles)."<br>";

        foreach( $roles as $role ) {
            $roleStr = $role."";
            $findStr = "_OBSERVER_";
            //echo "roleStr = ".$roleStr."; findStr=".$findStr."<br>";
            if( strpos($roleStr,$findStr) === false ) {
                //echo "The string $findStr was not found in the string $roleStr <br>";
                return $redirectResponse;
            } else {
                //echo "this is observer role!<br>";
            }
        }

        //if this is the only role the user has on the Vacation Request Site, be instantly redirected to the Away Calendar page
        if( $url != "/order/vacation-request/away-calendar/" ) {
            $redirectResponse->setTargetUrl("/order/vacation-request/away-calendar/");
        }

        return $redirectResponse;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        return parent::onAuthenticationFailure($request,$exception);
    }

}