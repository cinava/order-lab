<?php

namespace Oleg\TranslationalResearchBundle\Controller;

use Oleg\TranslationalResearchBundle\Entity\IrbReview;
use Oleg\TranslationalResearchBundle\Entity\Project;
use Oleg\TranslationalResearchBundle\Form\ReviewBaseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Irbreview controller.
 *
 * @Route("review")
 */
class ReviewBaseController extends Controller
{
    /**
     * Lists all irbReview entities.
     *
     * @Route("/{stateStr}", name="translationalresearch_review_index")
     * @Template("OlegTranslationalResearchBundle:Review:index.html.twig")
     * @Method("GET")
     */
    public function indexAction(Request $request, $stateStr)
    {
        $em = $this->getDoctrine()->getManager();

        $irbReviews = $em->getRepository('OlegTranslationalResearchBundle:IrbReview')->findAll();

        return array(
            'irbReviews' => $irbReviews,
        );
    }

    /**
     * Creates a new irbReview entity.
     *
     * @Route("/new", name="translationalresearch_review_new")
     * @Template("OlegTranslationalResearchBundle:Review:new.html.twig")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $irbReview = new Irbreview();
        $form = $this->createForm('Oleg\TranslationalResearchBundle\Form\IrbReviewType', $irbReview);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($irbReview);
            $em->flush();

            return $this->redirectToRoute('translationalresearch_review_show', array('id' => $irbReview->getId()));
        }

        return array(
            'irbReview' => $irbReview,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Review entity.
     *
     * @Route("/{stateStr}/{reviewId}/show", name="translationalresearch_review_show")
     * @Template("OlegTranslationalResearchBundle:Review:edit.html.twig")
     * @Method("GET")
     */
    public function showAction(Request $request, $stateStr, $reviewId)
    {

        $em = $this->getDoctrine()->getManager();
        //$user = $this->get('security.token_storage')->getToken()->getUser();
        $transresUtil = $this->container->get('transres_util');
        $cycle = "show";

        $reviewEntityName = $transresUtil->getReviewClassNameByState($stateStr);
        if( !$reviewEntityName ) {
            throw $this->createNotFoundException('Unable to find Review Entity Name by state='.$stateStr);
        }
        $review = $em->getRepository('OlegTranslationalResearchBundle:'.$reviewEntityName)->find($reviewId);
        if( !$review ) {
            throw $this->createNotFoundException('Unable to find '.$reviewEntityName.' by id='.$reviewId);
        }

        if( $transresUtil->isUserAllowedReview($review) === false ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('translationalresearch.sitename').'-nopermission') );
        }

        $form = $this->createReviewForm($request, $review, $cycle, $stateStr);

        return array(
            'review' => $review,
            'form' => $form->createView(),
            'stateStr' => $stateStr,
            'title' => $transresUtil->getStateSimpleLabelByName($stateStr),
            'cycle' => $cycle
            //'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing irbReview entity.
     *
     * @Route("/{stateStr}/{reviewId}/submit", name="translationalresearch_review_edit")
     * @Template("OlegTranslationalResearchBundle:Review:edit.html.twig")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $stateStr, $reviewId)
    {
        if( false === $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_USER') ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('translationalresearch.sitename').'-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $transresUtil = $this->container->get('transres_util');
        $cycle = "edit";

        $testing = false;
        //$testing = true;

        $reviewEntityName = $transresUtil->getReviewClassNameByState($stateStr);
        if( !$reviewEntityName ) {
            throw $this->createNotFoundException('Unable to find Review Entity Name by state='.$stateStr);
        }
        $review = $em->getRepository('OlegTranslationalResearchBundle:'.$reviewEntityName)->find($reviewId);
        if( !$review ) {
            throw $this->createNotFoundException('Unable to find '.$reviewEntityName.' by id='.$reviewId);
        }
        //echo "reviewID=".$review->getId();

        if( $transresUtil->isUserAllowedReview($review) === false || $transresUtil->isReviewable($review) === false ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('translationalresearch.sitename').'-nopermission') );
        }

//        $disabled = true;
//        if(
//            $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_ADMIN') ||
//            $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER') ||
//            $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER_DELEGATE')
//        ) {
//            $disabled = false;
//        }
//
//        //can be edited if the logged in user is a reviewer or reviewerDelegate for this review object
//        if( $user == $review->getReviewer() || $user == $review->getReviewerDelegate() ) {
//            $disabled = false;
//        }
        //$deleteForm = $this->createDeleteForm($review);
//        $form = $this->createForm('Oleg\TranslationalResearchBundle\Form\ReviewBaseType', $review, array(
//            'disabled' => $disabled
//        ));
        $form = $this->createReviewForm($request, $review, $cycle, $stateStr);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $review->setReviewedBy($user);

            if( !$testing ) {
                $this->getDoctrine()->getManager()->flush();
            }

            //set project next transit state depends on the decision
            //send notification emails
            //set eventLog
            $transresUtil->processProjectOnReviewUpdate($review,$stateStr,$request,$testing);

            if( $testing ) {
                exit("testing: exit submit review");
            }

            return $this->redirectToRoute('translationalresearch_review_show', array('stateStr'=>$stateStr,'reviewId' => $review->getId()));
        }

        return array(
            'review' => $review,
            'form' => $form->createView(),
            'stateStr' => $stateStr,
            'title' => $transresUtil->getStateSimpleLabelByName($stateStr),
            'cycle' => $cycle
            //'delete_form' => $deleteForm->createView(),
        );
    }

//    /**
//     * Deletes a irbReview entity.
//     *
//     * @Route("/{id}", name="translationalresearch_review_delete")
//     * @Method("DELETE")
//     */
//    public function deleteAction(Request $request, IrbReview $irbReview)
//    {
//        $form = $this->createDeleteForm($irbReview);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($irbReview);
//            $em->flush();
//        }
//
//        return $this->redirectToRoute('translationalresearch_review_index');
//    }

//    /**
//     * Creates a form to delete a irbReview entity.
//     *
//     * @param IrbReview $irbReview The irbReview entity
//     *
//     * @return \Symfony\Component\Form\Form The form
//     */
//    private function createDeleteForm(IrbReview $irbReview)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('translationalresearch_review_delete', array('id' => $irbReview->getId())))
//            ->setMethod('DELETE')
//            ->getForm()
//        ;
//    }

    private function createReviewForm( $request, $review, $cycle, $stateStr )
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $transresUtil = $this->container->get('transres_util');
        $routeName = $request->get('_route');

        $reviewEntityName = $transresUtil->getReviewClassNameByState($stateStr);
        if( !$reviewEntityName ) {
            throw $this->createNotFoundException('Unable to find Review Entity Name by state='.$stateStr);
        }

        $disabled = false;

        $params = array(
            'cycle' => $cycle,
            'em' => $em,
            'user' => $user,
            'SecurityAuthChecker' => $this->get('security.authorization_checker'),
            'review' => $review,
            'routeName' => $routeName,
            'stateStr' => $stateStr,
            'disabledReviewerFields' => false
        );

        if( $cycle == "show" ) {
            $disabled = true;
        }

        $params['admin'] = false;
        if(
            $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_ADMIN') ||
            $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER') ||
            $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER_DELEGATE')
        ) {
            $params['admin'] = true;
        }

        //check if reviewer
//        $params['reviewer'] = false;
//        if(  ) {
//
//        }

        $form = $this->createForm(ReviewBaseType::class, $review, array(
            'data_class' => 'Oleg\\TranslationalResearchBundle\\Entity\\'.$reviewEntityName,
            'form_custom_value' => $params,
            'disabled' => $disabled,
        ));

        return $form;
    }

}
