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

namespace Oleg\TranslationalResearchBundle\Util;


use Doctrine\Common\Collections\ArrayCollection;
use Oleg\TranslationalResearchBundle\Entity\CommitteeReview;
use Oleg\TranslationalResearchBundle\Entity\FinalReview;
use Oleg\TranslationalResearchBundle\Entity\IrbReview;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 8/25/2017
 * Time: 09:48 AM
 */
class TransResUtil
{

    protected $container;
    protected $em;
    protected $secToken;
    protected $secAuth;

    public function __construct( $em, $container ) {
        $this->container = $container;
        $this->em = $em;
        $this->secAuth = $container->get('security.authorization_checker'); //$this->secAuth->isGranted("ROLE_USER")
        $this->secToken = $container->get('security.token_storage')->getToken(); //$user = $this->secToken->getUser();
    }

    public function getEnabledLinkActions( $project, $user=null, $classEdit=null, $classTransition=null ) {
        $workflow = $this->container->get('state_machine.transres_project');
        $transitions = $workflow->getEnabledTransitions($project);

        $links = array();
        foreach( $transitions as $transition ) {
            //$this->printTransition($transition);
            $transitionName = $transition->getName();
            $tos = $transition->getTos();
            foreach( $tos as $to ) {
                //sent state $to
//                $thisUrl = $this->container->get('router')->generate(
//                    'translationalresearch_transition_state_action',
//                    array(
//                        'transitionName'=>$transitionName,
//                        'to'=>$to,
//                        'id'=>$project->getId()
//                    ),
//                    UrlGeneratorInterface::ABSOLUTE_URL
//                );

                //don't sent state $to (get it from transition object)
                $thisUrl = $this->container->get('router')->generate(
                    'translationalresearch_transition_action',
                    array(
                        'transitionName'=>$transitionName,
                        'id'=>$project->getId()
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                //$label = ucfirst($transitionName)." (mark as ".ucfirst($to);
                $label = $this->getTransitionLabelByName($transitionName);

                $thisLink = "<a ".
                    "general-data-confirm='Are you sure you want to $label?'".
                    "href=".$thisUrl." class='".$classTransition."'>".$label."</a>";
                $links[] = $thisLink;
            }

        }

        //add links to edit if the current state is "_rejected"
        $froms = $transition->getFroms();
        $fromState = $froms[0];
        if( strpos($fromState, '_rejected') !== false || $fromState == 'draft' || $fromState == 'complete' ) {
            $label = "Edit Project";
            $thisUrl = $this->container->get('router')->generate(
                'translationalresearch_project_edit',
                array(
                    'id'=>$project->getId()
                ),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $editLink = "<a ".
                //"general-data-confirm='Are you sure you want to $label?'".
                "href=".$thisUrl." class='".$classEdit."'>".$label."</a>";
            //$links[] = $editLink;
            array_unshift($links,$editLink);
        }

        //echo "count=".count($links)."<br>";

        return $links;
    }

    public function printTransition($transition) {
        echo $transition->getName().": ";
        $froms = $transition->getFroms();
        foreach( $froms as $from ) {
            echo "from=".$from.", ";
        }
        $tos = $transition->getTos();
        foreach( $tos as $to ) {
            echo "to=".$to.", ";
        }
        echo "<br>";
    }

    public function getTransitionByName( $project, $transitionName ) {
        $workflow = $this->container->get('state_machine.transres_project');
        $transitions = $workflow->getEnabledTransitions($project);
        foreach( $transitions as $transition ) {
            if( $transition->getName() == $transitionName ) {
                return $transition;
            }
        }
        return null;
    }

    //change transition (by the $transitionName) of the project
    public function setTransition( $project, $transitionName, $to=null ) {
        $transresUtil = $this->container->get('transres_util');
        $workflow = $this->container->get('state_machine.transres_project');

        if( !$to ) {
            //Get Transition and $to
            $transition = $transresUtil->getTransitionByName($project, $transitionName);
            $tos = $transition->getTos();
            if (count($tos) != 1) {
                throw $this->createNotFoundException('Available to state is not a single state; count=' . $tos . ": " . implode(",", $tos));
            }
            $to = $tos[0];
        }

        $label = $this->getTransitionLabelByName($transitionName);

        // Update the currentState on the post
        if( $workflow->can($project, $transitionName) ) {
            try {
                $workflow->apply($project, $transitionName);
                //change state
                $project->setState($to); //i.e. 'irb_review'

                //check and add reviewers for this state by role? Do it when project is created?
                $this->addStateReviewers($project);

                //write to DB
                $this->em->flush($project);

                //send confirmation Emails

                $this->container->get('session')->getFlashBag()->add(
                    'notice',
                    "Successful action: ".$label
                );
                return true;
            } catch (LogicException $e) {
                $this->container->get('session')->getFlashBag()->add(
                    'warning',
                    "Action failed: ".$label
                );
                return false;
            }//try
        }
    }

    //TODO: create default reviewers object: set reviewer and delegate reviewer for each review state.
    //add reviewers according to their roles and state
    //for example, state=irb_review => roles=ROLE_TRANSRES_IRB_REVIEWER, ROLE_TRANSRES_IRB_REVIEWER_DELEGATE
    public function addStateReviewers( $project ) {
        //echo "project state=".$project->getState()."<br>";
        switch( $project->getState() ) {

            case "irb_review":
                $defaultReviewers = $this->em->getRepository('OlegTranslationalResearchBundle:DefaultReviewer')->findByState($project->getState());
                //reviewer delegate should be added to the specific reviewer => no delegate role is required?
                foreach($defaultReviewers as $defaultReviewer) {
                    //1) create IrbReview entity
                    $reviewer = $defaultReviewer->getReviewer();
                    if( $reviewer ) {
                        $reviewEntity = new IrbReview($reviewer);
                        $reviewerDelegate = $defaultReviewer->getReviewerDelegate();
                        if( $reviewerDelegate ) {
                            $reviewEntity->setReviewerDelegate($reviewerDelegate);
                        }
                    }
                }
                break;

            case "committee_review":

                $defaultReviewers = $this->em->getRepository('OlegTranslationalResearchBundle:DefaultReviewer')->findByState($project->getState());
                //reviewer delegate should be added to the specific reviewer => no delegate role is required?
                foreach($defaultReviewers as $defaultReviewer) {
                    //1) create CommitteeReview entity
                    $reviewer = $defaultReviewer->getReviewer();
                    if( $reviewer ) {
                        $reviewEntity = new CommitteeReview($reviewer);
                        $reviewerDelegate = $defaultReviewer->getReviewerDelegate();
                        if( $reviewerDelegate ) {
                            $reviewEntity->setReviewerDelegate($reviewerDelegate);
                        }
                    }
                }

                break;

            case "final_approval":

                $defaultReviewers = $this->em->getRepository('OlegTranslationalResearchBundle:DefaultReviewer')->findByState($project->getState());
                //reviewer delegate should be added to the specific reviewer => no delegate role is required?
                foreach($defaultReviewers as $defaultReviewer) {
                    //1) create FinalReview entity
                    $reviewer = $defaultReviewer->getReviewer();
                    if( $reviewer ) {
                        $reviewEntity = new FinalReview($reviewer);
                        $reviewerDelegate = $defaultReviewer->getReviewerDelegate();
                        if( $reviewerDelegate ) {
                            $reviewEntity->setReviewerDelegate($reviewerDelegate);
                        }
                    }
                }

                break;

            default:
                //
        }

        return $project;
    }

    public function processDefaultReviewersRole( $defaultReviewer, $originalReviewer=null, $originalReviewerDelegate=null ) {

        $roles = $defaultReviewer->getRoleByState();
        $reviewerRole = $roles['reviewer'];
        $reviewerDelegateRole = $roles['reviewerDelegate'];

        $reviewer = $defaultReviewer->getReviewer();
        if( $reviewer ) {
            $reviewer->addRole($reviewerRole);
        }
        if( $originalReviewer && $originalReviewer != $reviewer ) {
            $originalReviewer->removeRole($reviewerRole);
        }

        $reviewerDelegate = $defaultReviewer->getReviewerDelegate();
        if( $reviewerDelegate ) {
            $reviewerDelegate->addRole($reviewerDelegateRole);
        }
        if( $originalReviewerDelegate && $originalReviewerDelegate != $reviewerDelegate ) {
            $originalReviewerDelegate->removeRole($reviewerDelegateRole);
        }

        return $defaultReviewer;
    }

    //get url to the review page according to the project's current state (i.e. IRB Review Page)
    public function getReviewLink( $project, $user=null ) {

        //$workflow = $this->container->get('state_machine.transres_project');
        //$transitions = $workflow->getEnabledTransitions($project);
        //foreach($transitions as $transition) {
        //    echo "transition=".$this->printTransition($transition)."<br>";
        //}

        $class = "btn btn-default";

        //echo "project state=".$project->getState()."<br>";

        switch( $project->getState() ) {
            case "irb_review":
                $thisUrl = $this->container->get('router')->generate(
                    'translationalresearch_review_new',
                    array(
                        //'id'=>$project->getId()
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                $link = "<a href=".$thisUrl." class='".$class."' target='_blank'>"."IRB Review"."</a>";
                break;
            default:
                $link = "Not Available for ".$project->getState();
        }

        return $link;
    }

    public function getTransitionLabelByName( $transitionName ) {

        switch ($transitionName) {
            case "draft":
                $label = "Save Project as Draft";
                break;
            case "submit":
                $label = "Complete Submission";
                break;
            case "edit":
                $label = "Edit Project";
                break;

            ///// Re-Submit after rejected /////
            case "resubmit_irb_rejected":
                $label = "Re-Submit to IRB Review";
                break;
            case "resubmit_admin_rejected":
                $label = "Re-Submit to Admin Review";
                break;
            case "resubmit_committee_rejected":
                $label = "Re-Submit to Committee Review";
                break;

            ///// Main Actions /////
            //IRB Review
            case "to_irb_review":
                $label = "Submit to IRB Review";
                break;
            case "irb_review_no":
                $label = "Reject IRB Review";
                break;
            //ADMIN Review
            case "to_admin_review":
                $label = "Submit to Admin Review";
                break;
            case "admin_review_no":
                $label = "Reject Admin Review";
                break;
            //COMMITTEE Review
            case "to_committee_review":
                $label = "Submit to Committee Review";
                break;
            case "committee_review_no":
                $label = "Reject Committee Review";
                break;
            //FINAL approval
            case "to_final_approval":
                $label = "Submit to Final Approval";
                break;
            case "final_approval_yes":
                $label = "Final Approve";
                break;
            case "final_approval_no":
                $label = "Reject Final Approval";
                break;

            case "approved_closed":
                $label = "Close Approved Project";
                break;
            case "closed_approved":
                $label = "Re-Open Approved Project";
                break;

            default:
                $label = "<$transitionName>";

        }
        return $label;
    }

    public function getStateLabelByProject( $project ) {
        return $this->getStateLabelByName($project->getState());
    }
    public function getStateLabelByName( $stateName ) {
        switch ($stateName) {
            case "start":
                $state = "Edit Project";
                break;
            case "draft":
                $state = "Draft";
                break;
            case "complete":
                $state = "Completed";
                break;
            case "submit":
                $state = "Completed";
                break;
            case "irb_review":
                $state = "In IRB Review";
                break;
            case "irb_rejected":
                $state = "IRB Review Rejected";
                break;
            case "admin_review":
                $state = "In Admin Review";
                break;
            case "admin_rejected":
                $state = "Admin Review Rejected";
                break;
            case "committee_review":
                $state = "In Committee Review";
                break;
            case "committee_rejected":
                $state = "Committee Review Rejected";
                break;

            case "final_approval":
                $state = "In Final Approval";
                break;
            case "approved":
                $state = "Approved";
                break;
            case "not_approved":
                $state = "Final Approval Rejected";
                break;

            case "closed":
                $state = "Closed";
                break;

            default:
                $state = "<$stateName>";

        }
        return $state;
    }

    public function getStateSimpleLabelByName( $stateName ) {
        switch ($stateName) {
            case "start":
                $state = "Edit Project";
                break;
            case "draft":
                $state = "Draft";
                break;
            case "complete":
                $state = "Completed";
                break;
            case "submit":
                $state = "Completed";
                break;
            case "irb_review":
                $state = "IRB Review";
                break;
            case "irb_rejected":
                $state = "IRB Review Rejected";
                break;
            case "admin_review":
                $state = "Admin Review";
                break;
            case "admin_rejected":
                $state = "Admin Review Rejected";
                break;
            case "committee_review":
                $state = "Committee Review";
                break;
            case "committee_rejected":
                $state = "Committee Review Rejected";
                break;

            case "final_approval":
                $state = "Final Approval";
                break;
            case "approved":
                $state = "Approved";
                break;
            case "not_approved":
                $state = "Final Approval Rejected";
                break;

            case "closed":
                $state = "Closed";
                break;

            default:
                $state = "<$stateName>";

        }
        return $state;
    }

    //create a review form (for example, IrbReview form if logged in user is a reviewer or reviewer delegate)
    //1) if project is in the review state: irb_review, admin_review, committee_review or final_approval
    //2) if the current user is added to this project as the reviewer for the state above
    public function getReviewForm($project, $user) {

        switch( $project->getState() ) {

            case "irb_review":
                $reviewEntityName = "IrbReview";
                $reviewObject = $this->findReviewObjectByAnyReviewer($reviewEntityName,$user);
                if( $reviewObject ) {
                    $reviewForm = $this->createForm(ReviewBaseType::class, $reviewObject, array(
                        //'form_custom_value' => $params,
                        'data_class' => 'Oleg\\TranslationalResearchBundle\\Entity\\'.$reviewEntityName
                    ));
                }
                break;

            case "admin_review":
                $reviewEntityName = "AdminReview";
                $reviewObject = $this->findReviewObjectByAnyReviewer($reviewEntityName,$user);
                if( $reviewObject ) {
                    $reviewForm = $this->createForm(ReviewBaseType::class, $reviewObject, array(
                        //'form_custom_value' => $params,
                        'data_class' => 'Oleg\\TranslationalResearchBundle\\Entity\\'.$reviewEntityName
                    ));
                }
                break;

            case "committee_review":
                $reviewEntityName = "CommitteeReview";
                $reviewObject = $this->findReviewObjectByAnyReviewer($reviewEntityName,$user);
                if( $reviewObject ) {
                    $reviewForm = $this->createForm(ReviewBaseType::class, $reviewObject, array(
                        //'form_custom_value' => $params,
                        'data_class' => 'Oleg\\TranslationalResearchBundle\\Entity\\'.$reviewEntityName
                    ));
                }
                break;

            case "final_approval":
                $reviewEntityName = "FinalReview";
                $reviewObject = $this->findReviewObjectByAnyReviewer($reviewEntityName,$user);
                if( $reviewObject ) {
                    $reviewForm = $this->createForm(ReviewBaseType::class, $reviewObject, array(
                        //'form_custom_value' => $params,
                        'data_class' => 'Oleg\\TranslationalResearchBundle\\Entity\\'.$reviewEntityName
                    ));
                }
                break;

            default:
                //
        }

        return $reviewForm;
    }
    //$reviewObjectClassName - review entity class name (i.e. "IrbReview")
    public function findReviewObjectByAnyReviewer( $reviewObjectClassName, $reviewer ) {
        $reviewObject = null;
        if( $reviewObjectClassName && $reviewer ) {
            $reviewObject = $this->em->getRepository('OlegTranslationalResearchBundle:' . $reviewObjectClassName)->findByReviewer($reviewer);
            if (!$reviewObject) {
                $reviewObject = $this->em->getRepository('OlegTranslationalResearchBundle:' . $reviewObjectClassName)->findByReviewerDelegate($reviewer);
            }
        }
        return $reviewObject;
    }
}