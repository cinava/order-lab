<?php

namespace Oleg\CallLogBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oleg\UserdirectoryBundle\Controller\SiteParametersController;



/**
 * SiteParameters controller.
 *
 * @Route("/settings")
 */
class CallLogSiteParametersController extends SiteParametersController
{

    /**
     * Lists all SiteParameters entities.
     *
     * @Route("/", name="calllog_siteparameters")
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:SiteParameters:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        return $this->indexParameters($request);
    }

    /**
     * Displays a form to edit an existing SiteParameters entity.
     *
     * @Route("/{id}/edit", name="calllog_siteparameters_edit")
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:SiteParameters:edit.html.twig")
     */
    public function editAction(Request $request,$id)
    {
        return $this->editParameters($request,$id);
    }

    /**
     * Edits an existing SiteParameters entity.
     *
     * @Route("/{id}", name="calllog_siteparameters_update")
     * @Method("PUT")
     * @Template("OlegUserdirectoryBundle:SiteParameters:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        return $this->updateParameters($request, $id);
    }



}
