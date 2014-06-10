<?php

namespace Oleg\OrderformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oleg\OrderformBundle\Entity\SiteParameters;
use Oleg\OrderformBundle\Form\SiteParametersType;
use Oleg\OrderformBundle\Helper\UserUtil;
use Oleg\OrderformBundle\Helper\ErrorHelper;

/**
 * SiteParameters controller.
 *
 * @Route("/settings")
 */
class SiteParametersController extends Controller
{

    /**
     * Lists all SiteParameters entities.
     *
     * @Route("/", name="siteparameters")
     * @Method("GET")
     * @Template("OlegOrderformBundle:SiteParameters:index.html.twig")
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OlegOrderformBundle:SiteParameters')->findAll();

        if( count($entities) != 1 ) {
            throw new \Exception( 'Must have only one parameter object. Found '.count($entities).'object(s)' );
        }

        $entity = $entities[0];

        $disabled = true;

        $passw = "*******";
        if( $entity->getAperioeSlideManagerDBPassword() != '' )
            $entity->setAperioeSlideManagerDBPassword($passw);

        if( $entity->getCoPathDBAccountPassword() != '' )
            $entity->setCoPathDBAccountPassword($passw);

        if( $entity->getADLDAPServerAccountPassword() != '' )
            $entity->setADLDAPServerAccountPassword($passw);

        if( $entity->getDbServerAccountPassword() != '' )
            $entity->setDbServerAccountPassword($passw);

        $editForm = $this->createEditForm($entity,$disabled);

        $link = realpath($_SERVER['DOCUMENT_ROOT']).'\order\scanorder\Scanorders2\app\config\parameters.yml';
        //echo "link=".$link."<br>";

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'cicle' => 'show',
            'link' => $link
        );
    }

    /**
     * Displays a form to edit an existing SiteParameters entity.
     *
     * @Route("/{id}/edit", name="siteparameters_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $request = $this->get('request');
        $param = trim( $request->get('param') );

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegOrderformBundle:SiteParameters')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SiteParameters entity.');
        }

        $editForm = $this->createEditForm($entity,$param,false);
        //$deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'cicle' => 'edit',
            'param' => $param
            //'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing SiteParameters entity.
     *
     * @Route("/{id}", name="siteparameters_update")
     * @Method("PUT")
     * @Template("OlegOrderformBundle:SiteParameters:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegOrderformBundle:SiteParameters')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SiteParameters entity.');
        }

        $param = trim( $request->get('param') );
        //echo "param=".$param."<br>";

        $editForm = $this->createEditForm($entity,$param,false);

        $editForm->handleRequest($request);

        if( $editForm->isValid() ) {
            $em->flush();

            return $this->redirect($this->generateUrl('siteparameters'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'cicle' => 'edit',
            'param' => ''
        );
    }

    /**
     * Creates a form to edit a SiteParameters entity.
     *
     * @param SiteParameters $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(SiteParameters $entity, $param=null,$disabled=false)
    {

        $cycle = 'show';

        if( !$disabled ) {
            $cycle = 'edit';
        }
        $params = array('cicle'=>$cycle,'param'=>$param);

        $form = $this->createForm(new SiteParametersType($params), $entity, array(
            'action' => $this->generateUrl('siteparameters_update', array('id' => $entity->getId(), 'param' => $param )),
            'method' => 'PUT',
            'disabled' => $disabled
        ));

        if( $disabled === false ) {
            $form->add('submit', 'submit', array('label' => 'Update', 'attr'=>array('class'=>'btn btn-warning')));
        }

        return $form;
    }



    /**
     * Displays a admin email.
     *
     * @Route("/scan-order/admin-email", name="scan-order-admin-email")
     * @Method("GET")
     * @Template("OlegOrderformBundle:History:index.html.twig")
     */
    public function getAdminEmailAction()
    {

        $userutil = new UserUtil();
        $em = $this->getDoctrine()->getManager();
        $adminemail = $userutil->getSiteSetting($em,'siteEmail');

        $response = new Response();
        $response->setContent($adminemail);

        return $response;
    }






    //    /**
//     * Creates a new SiteParameters entity.
//     *
//     * @Route("/", name="siteparameters_create")
//     * @Method("POST")
//     * @Template("OlegOrderformBundle:SiteParameters:new.html.twig")
//     */
//    public function createAction(Request $request)
//    {
//        $entity = new SiteParameters();
//        $form = $this->createCreateForm($entity);
//        $form->handleRequest($request);
//
//        if( $form->isValid() ) {
//            //echo "par not valid!";
//            //exit();
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($entity);
//            $em->flush();
//
////            return $this->redirect($this->generateUrl('siteparameters_show', array('id' => $entity->getId())));
//            return $this->redirect($this->generateUrl('siteparameters'));
//        }
//
//        return array(
//            'entity' => $entity,
//            'form'   => $form->createView(),
//        );
//    }

//    /**
//    * Creates a form to create a SiteParameters entity.
//    *
//    * @param SiteParameters $entity The entity
//    *
//    * @return \Symfony\Component\Form\Form The form
//    */
//    private function createCreateForm(SiteParameters $entity)
//    {
//        $form = $this->createForm(new SiteParametersType(), $entity, array(
//            'action' => $this->generateUrl('siteparameters_create'),
//            'method' => 'POST',
//        ));
//
//        $form->add('submit', 'submit', array('label' => 'Create'));
//
//        return $form;
//    }

//    /**
//     * Displays a form to create a new SiteParameters entity.
//     *
//     * @Route("/new", name="siteparameters_new")
//     * @Method("GET")
//     * @Template()
//     */
//    public function newAction()
//    {
//        $entity = new SiteParameters();
//        $form   = $this->createCreateForm($entity);
//
//        return array(
//            'entity' => $entity,
//            'form'   => $form->createView(),
//        );
//    }

//    /**
//     * Finds and displays a SiteParameters entity.
//     *
//     * @Route("/{id}", name="siteparameters_show")
//     * @Method("GET")
//     * @Template()
//     */
//    public function showAction($id)
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('OlegOrderformBundle:SiteParameters')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find SiteParameters entity.');
//        }
//
//        $deleteForm = $this->createDeleteForm($id);
//
//        return array(
//            'entity'      => $entity,
//            'delete_form' => $deleteForm->createView(),
//        );
//    }

//    /**
//     * Deletes a SiteParameters entity.
//     *
//     * @Route("/{id}", name="siteparameters_delete")
//     * @Method("DELETE")
//     */
//    public function deleteAction(Request $request, $id)
//    {
//        $form = $this->createDeleteForm($id);
//        $form->handleRequest($request);
//
//        if ($form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $entity = $em->getRepository('OlegOrderformBundle:SiteParameters')->find($id);
//
//            if (!$entity) {
//                throw $this->createNotFoundException('Unable to find SiteParameters entity.');
//            }
//
//            $em->remove($entity);
//            $em->flush();
//        }
//
//        return $this->redirect($this->generateUrl('siteparameters'));
//    }

//    /**
//     * Creates a form to delete a SiteParameters entity by id.
//     *
//     * @param mixed $id The entity id
//     *
//     * @return \Symfony\Component\Form\Form The form
//     */
//    private function createDeleteForm($id)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('siteparameters_delete', array('id' => $id)))
//            ->setMethod('DELETE')
//            ->add('submit', 'submit', array('label' => 'Delete'))
//            ->getForm()
//        ;
//    }

}
