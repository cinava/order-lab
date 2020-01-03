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

/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 8/9/2017
 * Time: 10:10 AM
 */

namespace App\FellAppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use App\UserdirectoryBundle\Controller\UserRequestController;


class FellAppUserRequestController extends UserRequestController
{

    public function __construct() {
        $this->siteName = 'fellapp';
        $this->siteNameShowuser = 'fellapp';
        $this->siteNameStr = 'Fellowship Applications';
        $this->roleEditor = 'ROLE_FELLAPP_COORDINATOR';
    }


    /**
     * Displays a form to create a new UserRequest entity.
     *
     * @Route("/account-requests/new", name="fellapp_accountrequest_new")
     * @Method("GET")
     * @Template("AppUserdirectoryBundle:UserRequest:account_request.html.twig")
     */
    public function newAction()
    {
        return parent::newAction();
    }

    /**
     * Creates a new UserRequest entity.
     *
     * @Route("/account-requests/new", name="fellapp_accountrequest_create")
     * @Method("POST")
     * @Template("AppUserdirectoryBundle:UserRequest:account_request.html.twig")
     */
    public function createAction(Request $request)
    {
        return parent::createAction($request);
    }


    /**
     * Lists all UserRequest entities.
     *
     * @Route("/account-requests", name="fellapp_accountrequest")
     * @Method("GET")
     * @Template("AppUserdirectoryBundle:UserRequest:index.html.twig")
     */
    public function indexAction( Request $request )
    {
        return parent::indexAction($request);
    }


    /**
     * @Route("/account-requests/{id}/{status}/status", name="fellapp_accountrequest_status", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template("AppUserdirectoryBundle:UserRequest:index.html.twig")
     */
    public function statusAction($id, $status)
    {
        return parent::statusAction($id,$status);
    }

    /**
     * Update (Approve) a new UserRequest entity.
     *
     * @Route("/account-requests-approve", name="fellapp_accountrequest_approve")
     * @Method("POST")
     * @Template("AppUserdirectoryBundle:UserRequest:index.html.twig")
     */
    public function approveUserAccountRequestAction(Request $request)
    {
        return parent::approveUserAccountRequestAction($request);
    }

}