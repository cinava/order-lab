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


use Oleg\UserdirectoryBundle\Entity\SiteList;
use Oleg\UserdirectoryBundle\Entity\User;
use Oleg\UserdirectoryBundle\Form\LabelType;
use Oleg\UserdirectoryBundle\Security\Util\UserSecurityUtil;
use Oleg\UserdirectoryBundle\Util\UserUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class HomeController extends Controller {

    public function mainCommonHomeAction() {
        return $this->render('OlegUserdirectoryBundle:Default:main-common-home.html.twig');
    }

    /**
     * @Route("/maintanencemode", name="main_maintenance")
     */
    public function maintanenceModeAction() {

        //exit('maint controller');

        $em = $this->getDoctrine()->getManager();
        $params = $roles = $em->getRepository('OlegUserdirectoryBundle:SiteParameters')->findAll();

        if( count($params) != 1 ) {
            throw new \Exception( 'Must have only one parameter object. Found '.count($params).'object(s)' );
        }

        $param = $params[0];

        //$maintenanceLoginMsg = $param->getMaintenanceloginmsg();
        //$maintenance = $param->getMaintenance();
        //echo "maintenance=".$maintenance."<br>";

        return $this->render('OlegUserdirectoryBundle:Default:maintenance.html.twig',
            array(
                'param' => $param
            )
        );
    }

    /**
     * @Route("/under-construction", name="under_construction")
     */
    public function underConstructionAction() {
        return $this->render('OlegUserdirectoryBundle:Default:under_construction.html.twig');
    }



//    /**
//     * @Route("/admin/list-manager/", name="platformlistmanager-list")
//     */
//    public function listManagerAction() {
//        if( false === $this->get('security.context')->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
//            //exit('no access');
//            return $this->redirect( $this->generateUrl('employees-nopermission') );
//        }
//        return $this->getList($request);
//    }


    /**
     * Not used: use http://localhost/order/directory/admin/first-time-login-generation-init/ for the first time user generation login
     * @Route("/first-time-user-generation-init/", name="first-time-user-generation-init")
     */
    public function firstTimeUserGenerationAction() {
        exit("not used");
//        return $this->render('OlegUserdirectoryBundle:Default:under_construction.html.twig');

        //exit("firstTimeUserGenerationAction");

        $em = $this->getDoctrine()->getManager();

        $default_time_zone = null;
        $usernamePrefix = "local-user";
        //$username = "oli2002";
        //$user = $this->em->getRepository('OlegUserdirectoryBundle:User')->findOneByUsername( $username."_@_". $usernamePrefix);


        $userSecUtil = new UserSecurityUtil($em,null,null);
        $systemuser = $userSecUtil->findSystemUser();

        //$this->generateSitenameList($systemuser);

        if( !$systemuser ) {

            $usetUtil = new UserUtil();
            $usetUtil->generateUsernameTypes($em);
            //$userkeytype = $em->getRepository('OlegUserdirectoryBundle:UsernameType')->findOneByAbbreviation("local-user");

            $userSecUtil = $this->container->get('user_security_utility');
            $userkeytype = $userSecUtil->getUsernameType($usernamePrefix);

            $systemuser = $usetUtil->createSystemUser($em, $userkeytype, $default_time_zone);
            $this->generateSitenameList($systemuser);

            //set unique username
            $usernameUnique = $systemuser->createUniqueUsername();
            $systemuser->setUsername($usernameUnique);
            $systemuser->setUsernameCanonical($usernameUnique);

            //$systemuser->setUsername("system_@_local-user");
            //$systemuser->setUsernameCanonical("system_@_local-user");

            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($systemuser, "systemuserpass");

            $systemuser->setPassword($encoded);
            $systemuser->setLocked(false);

            $em->persist($systemuser);
            $em->flush();

            exit("system user created");
        }

        if( !$systemuser->getPassword() ) {
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($systemuser, "systemuserpass");
            $systemuser->setPassword($encoded);
            $em->persist($systemuser);
            $em->flush();
        }

        exit("system user is already existed");
    }
    public function generateSitenameList($systemuser) {

        $em = $this->getDoctrine()->getManager();
        $userSecUtil = $this->get('user_security_utility');

        $elements = array(
            'directory' => 'employees',
            'scan' => 'scan',
            'fellowship-applications' => 'fellapp',
            'deidentifier' => 'deidentifier',
            'vacation-request' => 'vacreq',
            'call-log-book' => 'calllog'
        );


        //$username = $this->get('security.context')->getToken()->getUser();

        $count = 10;
        foreach( $elements as $name => $abbreviation ) {

            $entity = $em->getRepository('OlegUserdirectoryBundle:SiteList')->findOneByName($name);
            if( $entity ) {
                continue;
            }

            $entity = new SiteList();
            $userSecUtil->setDefaultList($entity,$count,$systemuser,$name);

            $entity->setAbbreviation($abbreviation);

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);

    }


    /**
     * @Route("/label/user/preview/{id}", name="employees_user_label_preview")
     * @Method({"GET","POST"})
     * @Template("OlegUserdirectoryBundle:Labels:label_user_preview.html.twig")
     */
    public function averyUserPrintAction(Request $request, $id) {
        if( false === $this->get('security.context')->isGranted('ROLE_USERDIRECTORY_EDITOR') ) {
            return $this->redirect( $this->generateUrl('employees-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $userDownloadUtil = $this->container->get('user_download_utility');

        //get username
        $subjectUser = $em->getRepository('OlegUserdirectoryBundle:User')->find($id);

        //Title
        $administrativeTitleNameStr = $userDownloadUtil->getUniqueFirstTitleStr($subjectUser);

        //Room
        $locationStr = null;
        $location = $subjectUser->getMainLocation();
        if( $location ) {
            $locationStr = $location->getLocationNameNoType();
        }

        //$nl = "&#13;&#10;";
        //$nl = "\n";
        $nl = "<br>\n";

//        $userEl = array();
//        $userEl['name'] = $subjectUser->getUsernameOptimal();
//        $userEl['title'] = $administrativeTitleNameStr;
//        $userEl['room'] = $locationStr;
//        $userElStr = implode("\n",$userEl);

        $userElStr =    $subjectUser->getUsernameOptimal() . $nl .
                        $administrativeTitleNameStr . $nl .
                        $locationStr;

        $params = array('label'=>$userElStr);

        $form = $this->createForm(new LabelType($params),null);

        $form->handleRequest($request);

        if( $form->isSubmitted() ) {
            $userlabel = $form['userlabel']->getData();
            //echo "userlabel=".$userlabel."<br>";

            $dotborders = $form['dotborders']->getData();
            $labelmax = $form['labelcount']->getData();
            $startcolumn = $form['startcolumn']->getData();
            $startrow = $form['startrow']->getData();
            //$endrow = $form['endrow']->getData();

            //return $this->redirect($this->generateUrl('employees_user_avery_5160', array('id'=>$id, 'userlabel'=>$userlabel)));

            $usersArr = array();

            $startIndex = 0;
            //$num = 30; //3 x 10

            $num = 30;//$endrow * 3; //30

            if( $labelmax == 0 ) {
                $labelmax = 30;
            }

            //$startrow
            //$startrow=1 => $currentLabelCount=0 (1-1)*3 = 0
            //$startrow=2 => $currentLabelCount=3 (2-1)*3 = 3
            //$startrow=3 => $currentLabelCount=6 (3-1)*3 = 6
            $currentLabelCount = ($startrow-1)*3;

            $emptyLabelCount = ($startrow-1)*3 + ($startcolumn-1);

            $labelCount = 0;
            $labelUserCount = 0;
            for( $i=$startIndex; $i<$num; $i++ ) {
                //if( $labelUserCount < $labelmax && $labelCount >= $currentLabelCount ) {
                if( $labelUserCount < $labelmax && $labelCount >= $emptyLabelCount ) {
                    $usersArr[] = $userlabel;   //$userEl;
                    $labelUserCount++;
                } else {
                    $usersArr[] = null;
                }
                $labelCount++;
            }

            return $this->render('OlegUserdirectoryBundle:Labels:avery_5160.html.twig', array(
                'userlabels' => $usersArr,
                'labelperpage' => 30,    //30
                'dotborders' => $dotborders
            ));
        }

        return array(
            'form' => $form->createView(),
            //'userEl' => $userEl,
            'title' => "User Label Print Management and Preview"
        );
    }
    /**
     * @Route("/label/avery-5160/user/{id}", name="employees_user_avery_5160")
     * @Method({"GET","POST"})
     * @Template("OlegUserdirectoryBundle:Labels:avery_5160.html.twig")
     */
    public function averySingleUserPrintAction( Request $request, $id ) {
        if( false === $this->get('security.context')->isGranted('ROLE_USERDIRECTORY_EDITOR') ) {
            return $this->redirect( $this->generateUrl('employees-nopermission') );
        }

        $userlabel = $request->request->get('userlabel');
        exit('userlabel='.$userlabel);

        if(0) {
            $em = $this->getDoctrine()->getManager();
            $userDownloadUtil = $this->container->get('user_download_utility');

            //get username
            $subjectUser = $em->getRepository('OlegUserdirectoryBundle:User')->find($id);

            //Title
            $administrativeTitleNameStr = $userDownloadUtil->getUserTitleStr($subjectUser);

            //Room
            $locationStr = null;
            $location = $subjectUser->getMainLocation();
            if ($location) {
                $locationStr = $location->getLocationNameNoType();
            }

            $userEl = array();
            $userEl['name'] = $subjectUser->getUsernameOptimal();
            $userEl['title'] = $administrativeTitleNameStr;
            $userEl['room'] = $locationStr;
        }

        $usersArr = array();

        $num = 30; //3 x 10

        for( $i=0; $i<$num; $i++ ) {
            $usersArr[] = $userlabel;   //$userEl;
        }

        return array(
            'userlabels' => $usersArr,
        );
    }

}
