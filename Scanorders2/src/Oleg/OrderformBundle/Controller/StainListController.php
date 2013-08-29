<?php

namespace Oleg\OrderformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oleg\OrderformBundle\Entity\StainList;
use Oleg\OrderformBundle\Form\StainListType;

/**
 * StainList controller.
 *
 * @Route("/stainlist")
 */
class StainListController extends Controller
{

    /**
     * Lists all StainList entities.
     *
     * @Route("/", name="stainlist")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OlegOrderformBundle:StainList')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new StainList entity.
     *
     * @Route("/", name="stainlist_create")
     * @Method("POST")
     * @Template("OlegOrderformBundle:StainList:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new StainList();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('stainlist_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a StainList entity.
    *
    * @param StainList $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(StainList $entity)
    {
        $form = $this->createForm(new StainListType(), $entity, array(
            'action' => $this->generateUrl('stainlist_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new StainList entity.
     *
     * @Route("/new", name="stainlist_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new StainList();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a StainList entity.
     *
     * @Route("/{id}", name="stainlist_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegOrderformBundle:StainList')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StainList entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing StainList entity.
     *
     * @Route("/{id}/edit", name="stainlist_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegOrderformBundle:StainList')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StainList entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a StainList entity.
    *
    * @param StainList $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(StainList $entity)
    {
        $form = $this->createForm(new StainListType(), $entity, array(
            'action' => $this->generateUrl('stainlist_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing StainList entity.
     *
     * @Route("/{id}", name="stainlist_update")
     * @Method("PUT")
     * @Template("OlegOrderformBundle:StainList:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegOrderformBundle:StainList')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StainList entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('stainlist_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a StainList entity.
     *
     * @Route("/{id}", name="stainlist_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OlegOrderformBundle:StainList')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find StainList entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('stainlist'));
    }

    /**
     * Creates a form to delete a StainList entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stainlist_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
