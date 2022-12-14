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

namespace App\TranslationalResearchBundle\Controller;

use App\UserdirectoryBundle\Controller\UserController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class TranslationalResearchUserController extends UserController
{

    /**
     * Optimized show user
     * @Route("/user/{id}", name="translationalresearch_showuser", requirements={"id" = "\d+"}, options={"expose"=true})
     * @Method("GET")
     * @Template("AppUserdirectoryBundle:Profile:show_user.html.twig")
     */
    public function showUserOptimizedAction( Request $request, $id ) {
        //exit("sitename=".$this->container->getParameter('translationalresearch.sitename')); //result:translationalresearch
        return $this->showUserOptimized($request, $id, $this->container->getParameter('translationalresearch.sitename'));
    }


    /**
     * @Route("/edit-user-profile/{id}", name="translationalresearch_user_edit", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template("AppUserdirectoryBundle:Profile:edit_user.html.twig")
     */
    public function editUserAction(Request $request, $id)
    {
        $secUtil = $this->get('user_security_utility');
        if( !$secUtil->isCurrentUser($id) && false === $this->get('security.authorization_checker')->isGranted('ROLE_USERDIRECTORY_EDITOR') ) {
            return $this->redirect( $this->generateUrl('translationalresearch-nopermission') );
        }

        $editUser = $this->editUser($request,$id, $this->container->getParameter('translationalresearch.sitename'));

        if( $editUser === false ) {
            return $this->redirect( $this->generateUrl('translationalresearch-nopermission') );
        }

        return $editUser;
    }

    /**
     * @Route("/edit-user-profile/{id}", name="translationalresearch_user_update")
     * @Method("PUT")
     * @Template("AppUserdirectoryBundle:Profile:edit_user.html.twig")
     */
    public function updateUserAction(Request $request, $id)
    {
        $secUtil = $this->get('user_security_utility');
        if( !$secUtil->isCurrentUser($id) && false === $this->get('security.authorization_checker')->isGranted('ROLE_USERDIRECTORY_EDITOR') ) {
            return $this->redirect( $this->generateUrl('translationalresearch-nopermission') );
        }

        return $this->updateUser( $request, $id, $this->container->getParameter('translationalresearch.sitename') );
    }

    /**
     * @Route("/add-new-user-ajax/", name="translationalresearch_add_new_user_ajax", options={"expose"=true})
     * @Method("POST")
     */
    public function addNewUserAjaxAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_USER')) {
            return $this->redirect($this->generateUrl('translationalresearch-nopermission'));
        }

        return $this->addNewUserAjax($request); //$this->container->getParameter('employees.sitename')
    }
    public function processOtherUserParam($user,$otherUserParam) {
        if( $otherUserParam == "hematopathology" ) {
            //$user->addRole("ROLE_TRANSRES_HEMATOPATHOLOGY");
            $user->addRole("ROLE_TRANSRES_REQUESTER_HEMATOPATHOLOGY");
        }
        if( $otherUserParam == "ap-cp" ) {
            //$user->addRole("ROLE_TRANSRES_APCP");
            $user->addRole("ROLE_TRANSRES_REQUESTER_APCP");
        }
        if( $otherUserParam == "hematopathology_ap-cp" ) {
            $user->addRole("ROLE_TRANSRES_REQUESTER_HEMATOPATHOLOGY");
            $user->addRole("ROLE_TRANSRES_REQUESTER_APCP");
        }

        //$user->addRole("ROLE_TRANSRES_REQUESTER");

        $userSecUtil = $this->container->get('user_security_utility');
        $environment = $userSecUtil->getSiteSettingParameter('environment');
        if( $environment != 'live' ) {
            $user->addRole('ROLE_TESTER');
        }

        $user->addRole('ROLE_USERDIRECTORY_OBSERVER');

        return true;
    }

}
