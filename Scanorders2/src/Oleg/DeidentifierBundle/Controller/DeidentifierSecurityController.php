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

namespace Oleg\DeidentifierBundle\Controller;


use Oleg\UserdirectoryBundle\Controller\SecurityController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

use Oleg\UserdirectoryBundle\Util\UserUtil;

class DeidentifierSecurityController extends SecurityController
{

    /**
     * @Route("/login", name="deidentifier_login")
     *
     * @Method("GET")
     * @Template()
     */
    public function loginAction( Request $request ) {
        return parent::loginAction($request);
    }


    /**
     * @Route("/setloginvisit/", name="deidentifier_setloginvisit")
     *
     * @Method("GET")
     */
    public function setAjaxLoginVisit( Request $request )
    {
        return parent::setAjaxLoginVisit($request);
    }


    /**
     * @Route("/no-permission", name="deidentifier-nopermission")
     * @Method("GET")
     * @Template("OlegDeidentifierBundle:Security:nopermission.html.twig")
     */
    public function actionNoPermission( Request $request )
    {
        //return parent::actionNoPermission($request);
        return array(
            //'returnpage' => '',
        );
    }



    /**
     * @Route("/idlelogout", name="deidentifier_idlelogout")
     * @Route("/idlelogout/{flag}", name="deidentifier_idlelogout-saveorder")
     * @Template()
     */
    public function idlelogoutAction( Request $request, $flag = null )
    {
        return parent::idlelogoutAction($request,$flag);
    }


//    /**
//     * @Route("/access-request-logout/", name="deidentifier_accreq_logout")
//     * @Template()
//     */
//    public function accreqLogoutAction( Request $request )
//    {
//        //echo "logout Action! <br>";
//        //exit();
//        //return parent::accreqLogoutAction($request);
//
//        return $this->accreqLogout($request,$this->container->getParameter('deidentifier.sitename'));
//    }



}

?>
