<?php

namespace Oleg\TranslationalResearchBundle\Controller;

use Oleg\TranslationalResearchBundle\Entity\DefaultReviewer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defaultreviewer controller.
 *
 * @Route("default-reviewers")
 */
class DefaultReviewerController extends Controller
{

    /**
     * Lists defaultReviewer states: irb_review, committee_review, final_approval
     *
     * @Route("/", name="translationalresearch_default-reviewer_index")
     * @Template("OlegTranslationalResearchBundle:DefaultReviewer:index.html.twig")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {

        $states = array(
            'irb_review',
            'committee_review',
            'final_approval'
        );

        return array(
            'states' => $states,
            'title' => "Default Reviewers"
        );
    }

    /**
     * Lists all defaultReviewer entities for a particular state.
     *
     * @Route("/stage/{stateStr}/", name="translationalresearch_state-default-reviewer_index")
     * @Template("OlegTranslationalResearchBundle:DefaultReviewer:state-default-reviewer-index.html.twig")
     * @Method("GET")
     */
    public function stateDefaultReviewerIndexAction(Request $request, $stateStr)
    {
        $em = $this->getDoctrine()->getManager();

        //$defaultReviewers = $em->getRepository('OlegTranslationalResearchBundle:DefaultReviewer')->findAll();

        $repository = $em->getRepository('OlegTranslationalResearchBundle:DefaultReviewer');
        $dql =  $repository->createQueryBuilder("defaultReviewer");
        $dql->select('defaultReviewer');

        $dql->leftJoin('defaultReviewer.reviewer','reviewer');
        $dql->leftJoin('reviewer.infos','reviewerInfos');
        $dql->leftJoin('defaultReviewer.reviewerDelegate','reviewerDelegate');
        $dql->leftJoin('reviewerDelegate.infos','reviewerDelegateInfos');

        $dql->where('defaultReviewer.state=:state');

        $limit = 30;
        $query = $em->createQuery($dql);

        $query->setParameters(array(
            "state" => $stateStr
        ));

        $paginationParams = array(
            'defaultSortFieldName' => 'defaultReviewer.id',
            'defaultSortDirection' => 'DESC'
        );

        $paginator  = $this->get('knp_paginator');
        $defaultReviewers = $paginator->paginate(
            $query,
            $request->query->get('page', 1),   /*page number*/
            $limit,                                         /*limit per page*/
            $paginationParams
        );

        //get state string: irb_review=>IRB Review
        $transresUtil = $this->container->get('transres_util');
        $stateLabel = $transresUtil->getStateSimpleLabelByName($stateStr);


        return array(
            'defaultReviewers' => $defaultReviewers,
            'stateStr' => $stateStr,
            'title' => "Default Reviewers for ".$stateLabel
        );
    }

    /**
     * Creates a new defaultReviewer entity.
     *
     * @Route("/new/{stateStr}", name="translationalresearch_default-reviewer_new")
     * @Template("OlegTranslationalResearchBundle:DefaultReviewer:new.html.twig")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $stateStr)
    {
        //exit('new');
        $defaultReviewer = new Defaultreviewer();
        $form = $this->createForm('Oleg\TranslationalResearchBundle\Form\DefaultReviewerType', $defaultReviewer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $defaultReviewer->setState($stateStr);
            $em = $this->getDoctrine()->getManager();
            $em->persist($defaultReviewer);
            $em->flush();

            return $this->redirectToRoute('translationalresearch_default-reviewer_show', array('id' => $defaultReviewer->getId()));
        }

        //get state string: irb_review=>IRB Review
        $transresUtil = $this->container->get('transres_util');
        $stateLabel = $transresUtil->getStateSimpleLabelByName($stateStr);

        return array(
            'defaultReviewer' => $defaultReviewer,
            'form' => $form->createView(),
            'title' => "Create a new Default Reviewer for ".$stateLabel
        );
    }

    /**
     * Finds and displays a defaultReviewer entity.
     *
     * @Route("/{id}", name="translationalresearch_default-reviewer_show")
     * @Template("OlegTranslationalResearchBundle:DefaultReviewer:show.html.twig")
     * @Method("GET")
     */
    public function showAction(DefaultReviewer $defaultReviewer)
    {
        $deleteForm = $this->createDeleteForm($defaultReviewer);

        return array(
            'defaultReviewer' => $defaultReviewer,
            'delete_form' => $deleteForm->createView(),
        );
//        return $this->render('defaultreviewer/show.html.twig', array(
//            'defaultReviewer' => $defaultReviewer,
//            'delete_form' => $deleteForm->createView(),
//        ));
    }

    /**
     * Displays a form to edit an existing defaultReviewer entity.
     *
     * @Route("/{id}/edit", name="translationalresearch_default-reviewer_edit")
     * @Template("OlegTranslationalResearchBundle:DefaultReviewer:edit.html.twig")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, DefaultReviewer $defaultReviewer)
    {
        $deleteForm = $this->createDeleteForm($defaultReviewer);
        $editForm = $this->createForm('Oleg\TranslationalResearchBundle\Form\DefaultReviewerType', $defaultReviewer);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('translationalresearch_default-reviewer_edit', array('id' => $defaultReviewer->getId()));
        }

        return array(
            'defaultReviewer' => $defaultReviewer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a defaultReviewer entity.
     *
     * @Route("/{id}", name="translationalresearch_default-reviewer_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, DefaultReviewer $defaultReviewer)
    {
        $form = $this->createDeleteForm($defaultReviewer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($defaultReviewer);
            $em->flush();
        }

        return $this->redirectToRoute('translationalresearch_default-reviewer_index');
    }

    /**
     * Creates a form to delete a defaultReviewer entity.
     *
     * @param DefaultReviewer $defaultReviewer The defaultReviewer entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DefaultReviewer $defaultReviewer)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('translationalresearch_default-reviewer_delete', array('id' => $defaultReviewer->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
