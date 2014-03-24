<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 3/24/14
 * Time: 11:59 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\OrderformBundle\Security\Util;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;


class SecurityUtil {

    protected $em;
    protected $sc;
    protected $session;

    public function __construct( $em, $sc, $session=null ) {
        $this->em = $em;
        $this->sc = $sc;
        $this->session = $session;
    }

    public function isCurrentUser( $id ) {

        $user = $this->sc->getToken()->getUser();

        $entity = $this->em->getRepository('OlegOrderformBundle:User')->find($id);

        //echo $entity->getId()." ?= ".$user->getId()."<br>";

        if( $entity && $entity->getId() === $user->getId() ) {

            return true;
        }

//        if( $this->session && false === $this->sc->isGranted('ROLE_PROCESSOR') ) {
//            $this->session->getFlashBag()->add(
//                'notice',
//                'You do not have permission to the previously requested page'
//            );
//        }

        return false;
    }


    //
    public function isCurrentUserAllow( $oid ) {

        $allow = false;

        if( $this->sc->isGranted('ROLE_PROCESSOR') || $this->sc->isGranted('ROLE_DIVISION_CHIEF') ) {

            $allow = true;

        } else {
            
            $user = $this->sc->getToken()->getUser();

            $entity = $this->em->getRepository('OlegOrderformBundle:OrderInfo')->find($oid);

            if( $entity ) {

                //echo "provider:".$entity->getProvider()->first()->getId()." ?= ".$user->getId()."<br>";

                if( $entity->getProvider()->first() && $entity->getProvider()->first()->getId() === $user->getId() ) {
                    $allow = true;
                }

                if( $entity->getProxyuser()->first()&& $entity->getProxyuser()->first()->getId() === $user->getId() ) {
                    //echo "proxy:".$entity->getProxyuser()->first()->getId()." ?= ".$user->getId()."<br>";
                    $allow = true;
                }

            }

        }

        return $allow;
    }

}