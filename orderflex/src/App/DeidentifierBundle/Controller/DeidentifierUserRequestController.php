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

namespace App\DeidentifierBundle\Controller;


//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\UserdirectoryBundle\Controller\UserRequestController;


class DeidentifierUserRequestController extends UserRequestController
{

    public function __construct() {
        $this->siteName = 'deidentifier';
        $this->siteNameShowuser = 'deidentifier';
        $this->siteNameStr = 'Deidentifier';
        $this->roleEditor = 'ROLE_DEIDENTIFICATOR_ADMIN';
    }


    /**
     * Displays a form to create a new UserRequest entity.
     *
     * @Template("AppUserdirectoryBundle/UserRequest/account_request.html.twig")
     */
    #[Route(path: '/account-requests/new', name: 'deidentifier_accountrequest_new', methods: ['GET'])]
    public function newAction()
    {
        return parent::newAction();
    }

    /**
     * Creates a new UserRequest entity.
     *
     * @Template("AppUserdirectoryBundle/UserRequest/account_request.html.twig")
     */
    #[Route(path: '/account-requests/new', name: 'deidentifier_accountrequest_create', methods: ['POST'])]
    public function createAction(Request $request)
    {
        return parent::createAction($request);
    }


    /**
     * Lists all UserRequest entities.
     *
     * @Template("AppUserdirectoryBundle/UserRequest/index.html.twig")
     */
    #[Route(path: '/account-requests', name: 'deidentifier_accountrequest', methods: ['GET'])]
    public function indexAction( Request $request )
    {
        return parent::indexAction($request);
    }


    /**
     * @Template("AppUserdirectoryBundle/UserRequest/index.html.twig")
     */
    #[Route(path: '/account-requests/{id}/{status}/status', name: 'deidentifier_accountrequest_status', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function statusAction($id, $status)
    {
        return parent::statusAction($id,$status);
    }

    /**
     * Update (Approve) a new UserRequest entity.
     *
     * @Template("AppUserdirectoryBundle/UserRequest/index.html.twig")
     */
    #[Route(path: '/account-requests-approve', name: 'deidentifier_accountrequest_approve', methods: ['POST'])]
    public function approveUserAccountRequestAction(Request $request)
    {
        return parent::approveUserAccountRequestAction($request);
    }

}