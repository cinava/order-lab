<?php

namespace Oleg\OrderformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oleg\OrderformBundle\Entity\Educational;
use Oleg\OrderformBundle\Form\EducationalType;
use Oleg\OrderformBundle\Entity\Research;
use Oleg\OrderformBundle\Form\ResearchType;
use Oleg\OrderformBundle\Entity\History;

/**
 * Educational and Research controller.
 */
class EducationalResearchController extends Controller {


    /**
     * Displays a form to edit an existing entity.
     *
     * @Route("/educational/{id}/edit", name="educational_edit")
     * @Route("/research/{id}/edit", name="research_edit")
     * @Method("GET")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $request = $this->container->get('request');
        $routeName = $request->get('_route');
        //echo "routeName=".$routeName; //mrntype

        $pieces = explode("_", $routeName);
        $type = $pieces[0];
        //echo "type=".$type."<br>";
        //exit();

        $entity = $em->getRepository('OlegOrderformBundle:'.$type)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find '.$type.' entity.');
        }

        $editForm = $this->createEditForm($entity);
        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('OlegOrderformBundle:'.$type.':edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'cicle' => 'show'
        ));
    }

    /**
     * Edits an existing entity.
     *
     * @Route("/educational/{id}/edit", name="educational_update")
     * @Route("/research/{id}/edit", name="research_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $request = $this->container->get('request');
        $routeName = $request->get('_route');
        $pieces = explode("_", $routeName);
        $type = $pieces[0];

        if( $type == 'research' ) {
            $className = 'PIList';
        }

        if( $type == 'educational' ) {
            $className = 'DirectorList';
        }

        //$entity = $em->getRepository('OlegOrderformBundle:'.$type)->find($id);
        $entity = $em->getRepository('OlegOrderformBundle:'.$className)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find '.$type.' entity.');
        }

        $entityHolder = $em->getRepository('OlegOrderformBundle:'.$type)->find($id);
        $editForm = $this->createEditForm($entityHolder);

        $editForm->bind($request);

        if ($editForm->isValid()) {
            //exit("form is valid!");

            echo "name=(".$entity->getName()."), user=(".$entity->getPrincipal().")<br>";

            $em->persist($entity);
            $em->flush();

            $orderoid = $entity->getOrderinfo()->getOid();

            if( $routeName == "educational_update" ) {
                $msg = "Values saved for Order ".$orderoid.": Course Director = ".$entity->getDirector();
                $url = $this->generateUrl( 'educational_edit', array('id' => $id) );
                $reviewLink = '<br> <a href="'.$url.'">Back to Educational Data Review</a>';
            }
            if( $routeName == "research_update" ) {
                $msg = "Values saved for Order ".$orderoid.": Principal Investigator = ".$entity->getPrincipal();
                $url = $this->generateUrl( 'research_edit', array('id' => $id) );
                $reviewLink = '<br> <a href="'.$url.'">Back to Research Data Review</a>';
            }

            $this->get('session')->getFlashBag()->add(
                'status-changed',
                $msg
            );

            //add event log to History
            $user = $this->get('security.context')->getToken()->getUser();
            $history = new History();
            $history->setEventtype('Data Reviewed');
            $history->setOrderinfo($entity->getOrderinfo());
            $history->setProvider($user);
            $history->setCurrentid($entity->getOrderinfo()->getOid());
            $history->setCurrentstatus($entity->getOrderinfo()->getStatus());
            $history->setChangedate( new \DateTime() );
            $history->setNote($msg.$reviewLink);
            $history->setRoles($user->getRoles());
            $em->persist($history);
            $em->flush();


            //return $this->redirect($this->generateUrl($type.'_show', array('id' => $id)));
            return $this->redirect($this->generateUrl('scan-order-data-review-full', array('id' => $orderoid)));
        } else {
            //exit("form is not valid ???");
        }

        return $this->render('OlegOrderformBundle:'.$type.':edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'cicle' => 'show'
        ));

    }

    private function createEditForm($entity, $cicle = null) {

        $params = array('type'=>'SingleObject');
        $disable = false;

        if( $cicle == 'show' ) {
            $disable = true;
        }

        if( $entity instanceof Educational ) {
            $typeform = new EducationalType($params,$entity);
            $type = "educational";
        }

        if( $entity instanceof Research ) {
            $typeform = new ResearchType($params,$entity);
            $type = "research";
        }

        $form = $this->createForm( $typeform, $entity, array(
            'action' => $this->generateUrl($type.'_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'disabled' => $disable
        ));

        if( $cicle != 'show' ) {
            $form->add('submit', 'submit', array('label' => 'Save '.$type, 'attr' => array('class' => 'btn btn-primary')));
        }

        return $form;
    }
}
