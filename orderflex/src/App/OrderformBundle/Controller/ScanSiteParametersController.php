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

namespace App\OrderformBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Routing\Annotation\Route;
use App\UserdirectoryBundle\Controller\SiteParametersController;



/**
 * SiteParameters controller.
 */
#[Route(path: '/settings')]
class ScanSiteParametersController extends SiteParametersController
{

    /**
     * Lists all SiteParameters entities.
     */
    #[Route(path: '/', name: 'scan_siteparameters', methods: ['GET'])]
    #[Template('AppUserdirectoryBundle/SiteParameters/index.html.twig')]
    public function indexAction(Request $request)
    {
        if( false === $this->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            return $this->redirect( $this->generateUrl('scan-nopermission') );
        }
        return $this->indexParameters($request);
    }

    /**
     * Displays a form to edit an existing SiteParameters entity.
     */
    #[Route(path: '/{id}/edit', name: 'scan_siteparameters_edit', methods: ['GET'])]
    #[Template('AppUserdirectoryBundle/SiteParameters/edit.html.twig')]
    public function editAction(Request $request,$id)
    {
        return $this->editParameters($request,$id);
    }

    /**
     * Edits an existing SiteParameters entity.
     */
    #[Route(path: '/{id}', name: 'scan_siteparameters_update', methods: ['PUT'])]
    #[Template('AppUserdirectoryBundle/SiteParameters/edit.html.twig')]
    public function updateAction(Request $request, $id)
    {
        return $this->updateParameters($request, $id);
    }



}
