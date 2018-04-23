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
use Doctrine\ORM\EntityRepository;
use Oleg\TranslationalResearchBundle\Entity\AdminReview;
use Oleg\TranslationalResearchBundle\Entity\CommitteeReview;
use Oleg\TranslationalResearchBundle\Entity\FinalReview;
use Oleg\TranslationalResearchBundle\Entity\IrbReview;
use Oleg\TranslationalResearchBundle\Entity\SpecialtyList;
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
    //protected $secToken;
    protected $secTokenStorage;
    protected $secAuth;

    public function __construct( $em, $container ) {
        $this->container = $container;
        $this->em = $em;
        $this->secAuth = $container->get('security.authorization_checker'); //$this->secAuth->isGranted("ROLE_USER")
        //$this->secToken = $container->get('security.token_storage')->getToken(); //$user = $this->secToken->getUser();
        $this->secTokenStorage = $container->get('security.token_storage'); //$user = $this->secTokenStorage->getToken()->getUser();
    }

    //NOT USED
    //get links to change states: Reject IRB Review and Approve IRB Review (translationalresearch_transition_action)
    public function getEnabledLinkActions( $project, $classEdit=null, $classTransition=null ) {
        $workflow = $this->container->get('state_machine.transres_project');
        $transitions = $workflow->getEnabledTransitions($project);

        $links = array();
        foreach( $transitions as $transition ) {

            //$this->printTransition($transition);
            $transitionName = $transition->getName();

            //$tos = $transition->getTos();
            $froms = $transition->getFroms();
            foreach( $froms as $from ) {
                //echo "from=".$from."<br>"; //irb_review

                //add user's validation: $from=irb_review => user has role _IRB_REVIEW_
                if( false === $this->isUserAllowedFromThisStateByRole($from) ) {
                    continue;
                }

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
                $label = $this->getTransitionLabelByName($transitionName); //not used

                $classTransition = $this->getHtmlClassTransition($transitionName);

                $thisLink = "<a ".
                    "general-data-confirm='Are you sure you want to $label?'".
                    "href=".$thisUrl." class='".$classTransition."'>".$label."</a>";
                $links[] = $thisLink;

            }//foreach

        }//foreach

        if(0) {
            ////////// add links to edit if the current state is "_rejected" //////////
            $froms = $transition->getFroms();
            $fromState = $froms[0];
            $showEditLink = false;
            $editLinkLabel = "Edit Project";
            if (strpos($fromState, '_rejected') !== false || $fromState == 'draft') {
                if ($this->secAuth->isGranted('ROLE_TRANSRES_REQUESTER')) {
                    $showEditLink = true;
                    $editLinkLabel = "Edit Project as Requester";
                }
            }
            if (
                $this->secAuth->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER') ||
                $this->secAuth->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER_DELEGATE')
            ) {
                $showEditLink = true;
                $editLinkLabel = "Edit Project as Primary Reviewer";
            }
            if ($this->secAuth->isGranted('ROLE_TRANSRES_ADMIN')) {
                $showEditLink = true;
                $editLinkLabel = "Edit Project as Administrator";
            }

            if ($showEditLink) {
                $thisUrl = $this->container->get('router')->generate(
                    'translationalresearch_project_edit',
                    array(
                        'id' => $project->getId()
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                $editLink = "<a " .
                    //"general-data-confirm='Are you sure you want to $label?'".
                    "href=" . $thisUrl . " class='" . $classEdit . "'>" . $editLinkLabel . "</a>";
                //$links[] = $editLink;
                array_unshift($links, $editLink);
            }
            ////////// EOF add links to edit if the current state is "_rejected" //////////
        }

        //echo "count=".count($links)."<br>";

        return $links;
    }

    //MAIN method to show allowed transition to state links
    //get links to change states: Reject IRB Review and Approve IRB Review (translationalresearch_transition_action)
    public function getReviewEnabledLinkActions( $review ) {
        //exit("get review links");
        $project = $review->getProject();
        $workflow = $this->container->get('state_machine.transres_project');
        $transitions = $workflow->getEnabledTransitions($project);
        $user = $this->secTokenStorage->getToken()->getUser();

        $links = array();

        //check if review is reviewable from this state
        if( $this->isReviewCorrespondsToState($review) === false ) {
            //echo "Review ".$review->getStateStr()." does not corresponds to state=".$project->getState()."<br>";
            return $links;
        }

        //if not admin - check if the logged in user is a reviewer for this review object (show committee review links according to the assigned reviewer)
        if( $this->isAdminOrPrimaryReviewer() === false ) {
            if( $this->isReviewsReviewer($user, array($review)) === false && $this->isProjectRequester($project) === false ) {
                //exit("return: is not reviewer or requester");
                return $links;
            }
        }

        //add current state of the review object
        //if( $review->getDecision() ) {
        //    $links[] = "<p>(Existing " . $review->getSubmittedReviewerInfo() . ")</p>";
        //}

//        $stateStr = $this->getAllowedFromState($project);
//
//        if( !$stateStr ) {
//            return $links;
//        }
//
//        if( $review->getStateStr() === "committee_review" ) {
//            if( strpos($stateStr, "missinginfo") !== false ) {
//                return $links;
//            }
//        }
//
//        if( false === $this->isUserAllowedFromThisStateByProjectOrReview($project,$review) ) {
//            return $links;
//        }

        foreach( $transitions as $transition ) {

            //$this->printTransition($transition);
            $transitionName = $transition->getName();
            //echo "transitionName=".$transitionName."<br>";

//            if( $this->isExceptionTransition($transitionName) === true ) {
//                continue;
//            }

            if( $review->getStateStr() === "committee_review" ) {
                if( strpos($transitionName, "missinginfo") !== false ) {
                    continue;
                }

                //don't show "Recommend..." buttons to primary reviewer for committee_review stage
                $finalReviewer = $this->isProjectStateReviewer($project,$user,"final_review",true);
                if( $review->getPrimaryReview() === false && $finalReviewer ) {
                    continue;
                }

                //show "Provide Final Approval" only if user is primary committee reviewer and final reviewer for this project
                if( $transitionName == "committee_finalreview_approved" ) {
                    //There should be only one Orange "Provide Final Approval" button for primary reviewer
                    if( method_exists($review, 'getPrimaryReview') ) {
                        if( $review->getPrimaryReview() === false ) {
                            continue;
                        }
                    }

                    //show "Provide Final Approval" only if user is primary committee reviewer and final reviewer for this project
                    $committeReviewer = $this->isProjectStateReviewer($project,$user,"committee_review",true);
                    $finalReviewer = $this->isProjectStateReviewer($project,$user,"final_review",true);
                    if( $committeReviewer && $finalReviewer ) {
                        //show link to committee_finalreview_approved
                    } else {
                        //echo "not primary or committee reviewer <br>";
                        if( !$this->secAuth->isGranted('ROLE_TRANSRES_ADMIN') ) {
                            continue;
                        }
                    }
                }
            }

            if( false === $this->isUserAllowedFromThisStateByProjectOrReview($project,$review) ) {
                continue;
            }

            //$tos = $transition->getTos();
            $froms = $transition->getFroms();
            foreach( $froms as $from ) {
                //echo "from=".$from."<br>"; //irb_review

                //add user's validation: $from=irb_review => user has role _IRB_REVIEW_
//                if( false === $this->isUserAllowedFromThisStateByRole($from) ) {
//                    continue;
//                }
//                if( false === $this->isUserAllowedFromThisStateByProjectOrReview($project,$review) ) {
//                    continue;
//                }

                //don't sent state $to (get it from transition object)
                $thisUrl = $this->container->get('router')->generate(
                    'translationalresearch_transition_action_by_review',
                    array(
                        'transitionName'=>$transitionName,
                        'id'=>$project->getId(),
                        'reviewId'=>$review->getId()
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                //$label = ucfirst($transitionName)." (mark as ".ucfirst($to);
                $label = $this->getTransitionLabelByName($transitionName,$review); //main

                $classTransition = $this->getHtmlClassTransition($transitionName);

                $generalDataConfirmation = "general-data-confirm='Are you sure you want to $label?'";

                //add class to distinguish irb_review update IRB expiration date
                if( $review->getStateStr() == "irb_review" ) {
                    $classTransition = $classTransition . " transres-irb_review";
                    //$generalDataConfirmation = "";
                }

                //don't show confirmation modal for missinginfo, because missinginfo has JS alert for empty comment
                //if( strpos($transitionName, "missinginfo") !== false ) {
                    //$generalDataConfirmation = "";
                //}

                $thisLink = "<a ".
                    //"general-data-confirm='Are you sure you want to $label?'".
                    $generalDataConfirmation.
                    "href=".$thisUrl." class='".$classTransition."'>".$label."</a>";
                $links[] = $thisLink;

            }//foreach

        }//foreach

        //echo "count=".count($links)."<br>";
        //exit();

        return $links;
    }

    //NOT USED
    //if status is missing and user is requester => add button "resubmit
    public function getResubmitButtons($review) {
        //exit("getResubmitButtons <br>");
        $project = $review->getProject();
        $workflow = $this->container->get('state_machine.transres_project');
        $transitions = $workflow->getEnabledTransitions($project);

        $links = array();
        foreach( $transitions as $transition ) {

            //$this->printTransition($transition);
            $transitionName = $transition->getName();
            //echo "transitionName=".$transitionName."<br>";

            //quick fix: only for missinginfo state
            if( strpos($transitionName, "missinginfo") !== false ) {
                return;
            }

            //$tos = $transition->getTos();
            $froms = $transition->getFroms();
            foreach( $froms as $from ) {
                //echo "from=".$from."<br>"; //irb_review

                //only if transitionName=irb_review_resubmit == irb class
                $statePrefixArr= explode("_", $transitionName); //irb
                $statePrefix = $statePrefixArr[0];
                if( strpos($review->getStateStr(), $statePrefix) === false ) {
                    continue;
                }

                //don't sent state $to (get it from transition object)
                $thisUrl = $this->container->get('router')->generate(
                    'translationalresearch_transition_action_by_review',
                    array(
                        'transitionName'=>$transitionName,
                        'id'=>$project->getId(),
                        'reviewId'=>$review->getId()
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                //$label = ucfirst($transitionName)." (mark as ".ucfirst($to);
                $label = $this->getTransitionLabelByName($transitionName); //not used

                $classTransition = $this->getHtmlClassTransition($transitionName);

                $thisLink = "<a ".
                    "general-data-confirm='Are you sure you want to $label?'".
                    "href=".$thisUrl." class='".$classTransition."'>".$label."</a>";
                $links[] = $thisLink;

            }//foreach

        }//foreach

        //echo "count=".count($links)."<br>";

        return $links;
    }

    //$transitionName - transition name, for example, committee_review_missinginfo or final_review_missinginfo
//    public function isExceptionTransition( $transitionName ) {
//        if( $transitionName == "committee_review_missinginfo" ) {
//            return true;
//        }
//        if( $transitionName == "final_review_missinginfo" ) {
//            return true;
//        }
//        return false;
//    }

    //NOT USED
    //get Review links for this user: irb_review => "IRB Review" or "IRB Review as Admin"
    //project/review/2/6 - project ID 2, review ID 6
    public function getProjectReviewLinks( $project, $user ) {
        //get project reviews for appropriate state (i.e. irb_review)
        $links = array();

        $state = $project->getState();

        $reviews = array();

        if( $state == "irb_review" ) {
            $reviews = $project->getIrbReviews();
            //$reviewEntityName = "IrbReview";
        }
        if( $state == "admin_review" ) {
            $reviews = $project->getAdminReviews();
            //$reviewEntityName = "AdminReview";
        }
        if( $state == "committee_review" ) {
            $reviews = $project->getCommitteeReviews();
            //$reviewEntityName = "CommitteeReview";
        }
        if( $state == "final_review" ) {
            $reviews = $project->getFinalReviews();
            //$reviewEntityName = "FinalReview";
        }

        //$reviewObjects = $this->findReviewObjectsByProjectAndAnyReviewers($reviewEntityName,$project,$user);

        foreach($reviews as $review) {
            $reviewer = $review->getReviewer();
            $reviewerDelegate = $review->getReviewerDelegate();
            if(
                $reviewer && $reviewer->getId() == $user->getId() ||
                $reviewerDelegate && $reviewerDelegate->getId() == $user->getId()
            ) {
                $thisUrl = $this->container->get('router')->generate(
                    'translationalresearch_review_edit',
                    array(
                        'stateStr' => $state,
                        'reviewId'=>$review->getId()
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                $label = $this->getStateSimpleLabelByName($state);
                $link = "<a ".
                    "href=".$thisUrl." target='_blank'>".$label."</a>";
                $links[] = $link;
            }
        }

        return $links;
    }

    public function isProjectEditableByRequester( $project ) {
        if( $project && $this->isUserAllowedSpecialtyObject($project->getProjectSpecialty()) === false ) {
            return false;
        }

        $state = $project->getState();
        if( strpos($state, '_rejected') !== false || $state == 'draft' ) { //|| strpos($state, "_missinginfo") !== false
            if( $this->isProjectRequester($project) === true ) {
                return true;
            }
        }
        if( $this->isProjectStateRequesterResubmit($project) ) {
            return true;
        }
        return false;
    }
    public function isProjectRequester( $project ) {
        if( $project && $this->isUserAllowedSpecialtyObject($project->getProjectSpecialty()) === false ) {
            return false;
        }

        $user = $this->secTokenStorage->getToken()->getUser();

        if( $project->getSubmitter() && $project->getSubmitter()->getId() == $user->getId() ) {
            return true;
        }
        if( $project->getPrincipalInvestigators()->contains($user) ) {
            return true;
        }
        if( $project->getCoInvestigators()->contains($user) ) {
            return true;
        }
        if( $project->getPathologists()->contains($user) ) {
            return true;
        }
        if( $project->getContacts()->contains($user) ) {
            return true;
        }
        if( $project->getBillingContacts()->contains($user) ) {
            return true;
        }
//        if( $project->getBillingContact() ) {
//            if( $project->getBillingContact()->getId() == $user->getId() ) {
//                return true;
//            }
//        }
        return false;
    }
    public function isRequesterOrAdmin( $project ) {
        if( $project && $this->isUserAllowedSpecialtyObject($project->getProjectSpecialty()) === false ) {
            return false;
        }
        if( $this->isProjectRequester($project) === true ) {
            return true;
        }
        if( $this->isAdminOrPrimaryReviewer() === true ) {
            return true;
        }

        return false;
    }
    public function isAdminOrPrimaryReviewer() {
        if(
            $this->secAuth->isGranted('ROLE_TRANSRES_ADMIN') ||
            $this->secAuth->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER') ||
            $this->secAuth->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER_DELEGATE')
        ) {
            return true;
        }
        return false;
    }
    public function hasReviewerRoles() {
        if( $this->secAuth->isGranted('ROLE_TRANSRES_IRB_REVIEWER') ) {
            return true;
        }
        if( $this->secAuth->isGranted('ROLE_TRANSRES_ADMIN') ) {
            return true;
        }
        if( $this->secAuth->isGranted('ROLE_TRANSRES_COMMITTEE_REVIEWER') ) {
            return true;
        }
        if( $this->secAuth->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER') ) {
            return true;
        }
        return false;
    }
    public function isProjectReviewer( $project ) {
        if( $project && $this->isUserAllowedSpecialtyObject($project->getProjectSpecialty()) === false ) {
            return false;
        }
        $user = $this->secTokenStorage->getToken()->getUser();
        if( $this->isReviewsReviewer($user,$project->getIrbReviews()) ) {
            return true;
        }
        if( $this->isReviewsReviewer($user,$project->getAdminReviews()) ) {
            return true;
        }
        if( $this->isReviewsReviewer($user,$project->getCommitteeReviews()) ) {
            return true;
        }
        if( $this->isReviewsReviewer($user,$project->getFinalReviews()) ) {
            return true;
        }
        return false;
    }
    public function isAdminOrPrimaryReviewerOrExecutive() {
        if(
            $this->secAuth->isGranted('ROLE_TRANSRES_ADMIN') ||
            $this->secAuth->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER') ||
            $this->secAuth->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER_DELEGATE') ||
            $this->secAuth->isGranted('ROLE_TRANSRES_EXECUTIVE_HEMATOPATHOLOGY') ||
            $this->secAuth->isGranted('ROLE_TRANSRES_EXECUTIVE_APCP')
        ) {
            return true;
        }
        return false;
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
        //echo $transitionName.": count=".count($transitions)."<br>";
        foreach( $transitions as $transition ) {
            //echo "transitionName:".$transition->getName()." == " . $transitionName;
            if( $transition->getName() == $transitionName ) {
                return $transition;
            }
        }
        return null;
    }

    //change transition (by the $transitionName) of the project
    public function setTransition( $project, $review, $transitionName, $to=null, $testing=false ) {

        if( !$review ) {
            throw new \Exception('Review object does not exist');
        }

        if( !$review->getId() ) {
            throw new \Exception('Review object ID is null');
        }

        //echo "transitionName=".$transitionName."<br>";
        $user = $this->secTokenStorage->getToken()->getUser();
        $transresUtil = $this->container->get('transres_util');
        $workflow = $this->container->get('state_machine.transres_project');
        $break = "\r\n";

        if( !$to ) {
            //Get Transition and $to
            $transition = $transresUtil->getTransitionByName($project, $transitionName);
            if( $transition ) {
                $tos = $transition->getTos();
                if (count($tos) != 1) {
                    throw new \Exception('Available to state is not a single state; count=' . $tos . ": " . implode(",", $tos));
                }
                $to = $tos[0];
            } else {
                $to = null;
                //exit("transition is null transitionName=".$transitionName);
            }
        }
        //echo "to=".$to."<br>";

        $originalStateStr = $project->getState();
        $originalStateLabel = $this->getStateLabelByName($originalStateStr);

        // Update the currentState on the post
        if( $workflow->can($project, $transitionName) ) {
            try {

                //$decision = $this->getDecisionByTransitionName($transitionName);
                //$review->setDecision($decision);
                $review->setDecisionByTransitionName($transitionName);

                //if committee_finalreview_approved => set final review decision to approve
                //because we search pending project assigned to me by decision == NULL
//                if( $transitionName == "committee_finalreview_approved" ) {
//                    $finalReviews = $project->getFinalReviews();
//                    foreach($finalReviews as $finalReview) {
//                        $finalReview->setDecisionByTransitionName($transitionName);
//                    }
//                }

                $review->setReviewedBy($user);

                //check if like/dislike
                if( $review->getStateStr() === "committee_review" ) {
                    if( $review->getPrimaryReview() !== true ) {

                        if( !$testing ) {
                            $this->em->flush($review);
                        }

                        //$recommended = true;
                        //$label = $this->getStateLabelByName($project->getState());
                        $subject = "Project ID ".$project->getOid(). " has been reviewed by a committee member with recommendation: ".$review->getDecision();
                        $body = $subject;

                        //get project url
                        $projectUrl = $transresUtil->getProjectShowUrl($project);
                        $emailBody = $body . $break.$break. "Please click on the URL below to view this project:".$break.$projectUrl;

                        //send notification emails
                        $this->sendNotificationEmails($project,$review,$subject,$emailBody,$testing);

                        //event log
                        //$this->setEventLog($project,$review,$transitionName,$originalStateStr,$body,$testing);
                        $emailBody = str_replace($break,"<br>",$emailBody);
                        $eventType = "Review Submitted";
                        $this->setEventLog($project,$eventType,$emailBody,$testing);

                        $this->container->get('session')->getFlashBag()->add(
                            'notice',
                            $subject
                        );

                        return true;
                    }
                }

                if( $to === "final_approved" ) {
                    $project->setApprovalDate(new \DateTime());
                }

                $workflow->apply($project, $transitionName);
                //change state
                $project->setState($to); //i.e. 'irb_review'

                //check and add reviewers for this state by role? Do it when project is created?
                //$this->addDefaultStateReviewers($project);

                $eventResetMsg = null;
//                if( $originalStateStr != $project->getState() ) {
//                    $eventResetMsg = $this->resetReviewDecision($project,$review);
//                }

                //write to DB
                if( !$testing ) {
                    $this->em->flush();
                }

                //$recommended = false;
                $label = $this->getTransitionLabelByName($transitionName,$review); //set transition
                $subject = "Project ID ".$project->getOid()." status has been changed from '$originalStateLabel' to '$label'";

                $statusChangeMsg = $this->getNotificationMsgByStates($originalStateStr,$to,$project);
                //$body = $statusChangeMsg;
                //get project url
                $projectUrl = $transresUtil->getProjectShowUrl($project);
                $emailBody = $statusChangeMsg . $break.$break. "Please click on the URL below to view this project:".$break.$projectUrl;

                //send confirmation email
                $this->sendNotificationEmails($project,$review,$subject,$emailBody,$testing);

                //event log
                //$this->setEventLog($project,$review,$transitionName,$originalStateStr,$body,$testing);
                $emailBody = $emailBody . $eventResetMsg;
                $emailBody = str_replace($break,"<br>",$emailBody);
                $eventType = "Review Submitted";
                $this->setEventLog($project,$eventType,$emailBody,$testing);

                $this->container->get('session')->getFlashBag()->add(
                    'notice',
                    $statusChangeMsg    //"Successful action: ".$label
                );
                return true;
            } catch (\LogicException $e) {
                //event log

                $this->container->get('session')->getFlashBag()->add(
                    'warning',
                    "Action failed: ".$this->getTransitionLabelByName($transitionName)
                );
                return false;
            }//try
        }
    }

    //TODO: create default reviewers object: set reviewer and delegate reviewer for each review state.
    //add reviewers according to their roles and state
    //for example, state=irb_review => roles=ROLE_TRANSRES_IRB_REVIEWER, ROLE_TRANSRES_IRB_REVIEWER_DELEGATE
    public function addDefaultStateReviewers( $project, $addForAllStates=true ) {

        $currentState = $project->getState();
        //echo "project state=".$currentState."<br>";

        $irbReviewState = "irb_review";
        if( $currentState == $irbReviewState || $addForAllStates ) {
            $defaultReviewers = $this->em->getRepository('OlegTranslationalResearchBundle:DefaultReviewer')->findBy(
                array(
                    "state" => $irbReviewState,
                    "projectSpecialty" => $project->getProjectSpecialty()
                )
            );
            //echo "defaultReviewers count=".count($defaultReviewers)."<br>";
            //reviewer delegate should be added to the specific reviewer => no delegate role is required?
            foreach ($defaultReviewers as $defaultReviewer) {
                //1) create IrbReview entity
                $reviewer = $defaultReviewer->getReviewer();
                if ($reviewer) {
                    if(false === $this->isReviewsReviewer($reviewer,$project->getIrbReviews()) ) {
                        //echo "reviewer=".$reviewer."<br>";
                        $reviewEntity = new IrbReview($reviewer);
                        $reviewerDelegate = $defaultReviewer->getReviewerDelegate();
                        if ($reviewerDelegate) {
                            $reviewEntity->setReviewerDelegate($reviewerDelegate);
                        }
                        $project->addIrbReview($reviewEntity);
                    }
                }
            }
        }

        $adminReviewState = "admin_review";
        if( $currentState == $adminReviewState || $addForAllStates ) {
            //$defaultReviewers = $this->em->getRepository('OlegTranslationalResearchBundle:DefaultReviewer')->findByState($adminReviewState);
            $defaultReviewers = $this->em->getRepository('OlegTranslationalResearchBundle:DefaultReviewer')->findBy(
                array(
                    "state" => $adminReviewState,
                    "projectSpecialty" => $project->getProjectSpecialty()
                )
            );
            //reviewer delegate should be added to the specific reviewer => no delegate role is required?
            foreach ($defaultReviewers as $defaultReviewer) {
                //1) create IrbReview entity
                $reviewer = $defaultReviewer->getReviewer();
                if ($reviewer) {
                    if(false === $this->isReviewsReviewer($reviewer,$project->getAdminReviews()) ) {
                        $reviewEntity = new AdminReview($reviewer);
                        $reviewerDelegate = $defaultReviewer->getReviewerDelegate();
                        if ($reviewerDelegate) {
                            $reviewEntity->setReviewerDelegate($reviewerDelegate);
                        }
                        $project->addAdminReview($reviewEntity);
                    }
                }
            }
        }

        $committeeReviewState = "committee_review";
        if( $currentState == $committeeReviewState || $addForAllStates ) {

            //$defaultReviewers = $this->em->getRepository('OlegTranslationalResearchBundle:DefaultReviewer')->findByState($committeeReviewState,array("primaryReview"=>"DESC"));
            $defaultReviewers = $this->em->getRepository('OlegTranslationalResearchBundle:DefaultReviewer')->findBy(
                array(
                    "state" => $committeeReviewState,
                    "projectSpecialty" => $project->getProjectSpecialty()
                )
            );
            //reviewer delegate should be added to the specific reviewer => no delegate role is required?
            foreach ($defaultReviewers as $defaultReviewer) {
                //1) create CommitteeReview entity
                $reviewer = $defaultReviewer->getReviewer();
                if ($reviewer) {
                    if(false === $this->isReviewsReviewer($reviewer,$project->getCommitteeReviews()) ) {
                        $reviewEntity = new CommitteeReview($reviewer);
                        $reviewerDelegate = $defaultReviewer->getReviewerDelegate();
                        if ($reviewerDelegate) {
                            $reviewEntity->setReviewerDelegate($reviewerDelegate);
                        }

                        //add primaryReview boolean
                        $reviewEntity->setPrimaryReview($defaultReviewer->getPrimaryReview());

                        $project->addCommitteeReview($reviewEntity);
                    }
                }
            }

        }

        $finalReviewState = "final_review";
        if( $currentState == $finalReviewState || $addForAllStates ) {

            //$defaultReviewers = $this->em->getRepository('OlegTranslationalResearchBundle:DefaultReviewer')->findByState($finalReviewState);
            $defaultReviewers = $this->em->getRepository('OlegTranslationalResearchBundle:DefaultReviewer')->findBy(
                array(
                    "state" => $finalReviewState,
                    "projectSpecialty" => $project->getProjectSpecialty()
                )
            );
            //reviewer delegate should be added to the specific reviewer => no delegate role is required?
            foreach ($defaultReviewers as $defaultReviewer) {
                //1) create FinalReview entity
                $reviewer = $defaultReviewer->getReviewer();
                if ($reviewer) {
                    if(false === $this->isReviewsReviewer($reviewer,$project->getFinalReviews()) ) {
                        $reviewEntity = new FinalReview($reviewer);
                        $reviewerDelegate = $defaultReviewer->getReviewerDelegate();
                        if ($reviewerDelegate) {
                            $reviewEntity->setReviewerDelegate($reviewerDelegate);
                        }
                        $project->addFinalReview($reviewEntity);
                    }
                }
            }

        }


        return $project;
    }

    public function getDefaultReviewerInfo( $state, $specialty ) {
        $infos = array();

        //$defaultReviewers = $this->em->getRepository('OlegTranslationalResearchBundle:DefaultReviewer')->findByState($state,array('primaryReview' => 'DESC'));
        $defaultReviewers = $this->em->getRepository('OlegTranslationalResearchBundle:DefaultReviewer')->findBy(
            array(
                'state'=>$state,
                'projectSpecialty'=>$specialty->getId()
            ),
            array('primaryReview' => 'DESC')
        );

        foreach ($defaultReviewers as $defaultReviewer) {
            $info = "";
            if( $defaultReviewer->getReviewer() ) {
                $reviewerUrl = $this->container->get('router')->generate(
                    'translationalresearch_showuser',
                    array(
                        'id'=>$defaultReviewer->getReviewer()->getId()
                    )
                );
                $reviewerLink = "<a ".
                    "href=".$reviewerUrl." target='_blank'>".$defaultReviewer->getReviewer()."</a>";
                $info .= "Reviewer: ".$reviewerLink;
            }
            if( $defaultReviewer->getReviewerDelegate() ) {
                $reviewerUrl = $this->container->get('router')->generate(
                    'translationalresearch_showuser',
                    array(
                        'id'=>$defaultReviewer->getReviewerDelegate()->getId()
                    )
                );
                $reviewerLink = "<a ".
                    "href=".$reviewerUrl." target='_blank'>".$defaultReviewer->getReviewerDelegate()."</a>";
                $info .= ", Reviewer Delegate: ".$reviewerLink;
            }
            if( $defaultReviewer->getState() == "committee_review" ) {
                if( $defaultReviewer->getPrimaryReview() === true ) {
                    $info .= " (<font color=\"#8063FF\">Primary Reviewer</font>)";
                }
            }
            if( $info ) {
                $info .= "<br>";
            }
            $infos[] = $info;
        }
        return $infos;
    }

    public function isReviewsReviewer($reviewerUser, $projectReviewers ) {
        if( !$reviewerUser || !$reviewerUser->getId() ) {
            return false;
        }
        foreach($projectReviewers as $projectReviewer ) {
            //echo "userID=".$reviewerUser->getId().": review ID=".$projectReviewer->getId()."<br>";
            if( $projectReviewer->getReviewer() && $projectReviewer->getReviewer()->getId() ) {
                //echo "userID=".$reviewerUser->getId().": reviewerID=".$projectReviewer->getReviewer()->getId()."<br>";
                if ($projectReviewer->getReviewer()->getId() == $reviewerUser->getId()) {
                    return true;
                }
            }
            if($projectReviewer->getReviewerDelegate() && $projectReviewer->getReviewerDelegate()->getId()) {
                //echo "userID=".$reviewerUser->getId().": ReviewerDelegateID=".$projectReviewer->getReviewerDelegate()->getId()."<br>";
                if ($projectReviewer->getReviewerDelegate()->getId() == $reviewerUser->getId()) {
                    return true;
                }
            }
        }
        //echo "not reviewer => return false<br>";
        return false;
    }

    public function isReviewer($reviewerUser, $review ) {
        if( !$reviewerUser || !$reviewerUser->getId() ) {
            return false;
        }
        //echo "reviewer ID=".$review->getId()."<br>";

        if ($review->getReviewer() && $review->getReviewer()->getId() ) {
            if ($review->getReviewer()->getId() == $reviewerUser->getId()) {
                return true;
            }
        }
        if ($review->getReviewerDelegate() && $review->getReviewerDelegate()->getId()) {
            if ($review->getReviewerDelegate()->getId() == $reviewerUser->getId()) {
                return true;
            }
        }

        //exit('not reviewer');
        return false;
    }

    public function removeReviewsFromProject($project, $originalReviews, $currentReviews) {
        foreach ($originalReviews as $originalReview) {
            if (false === $currentReviews->contains($originalReview)) {
                // remove the Task from the Tag
                $currentReviews->removeElement($originalReview);

                // if it was a many-to-one relationship, remove the relationship like this
                $originalReview->setProject(null);

                $this->em->persist($originalReview);

                // if you wanted to delete the Tag entirely, you can also do that
                $this->em->remove($originalReview);
            }
        }
        return $project;
    }

    //Change review's decision according to the current state, because we search pending project assigned to me by decision == NULL
    public function resetReviewDecision($project, $review=null)
    {
        $state = $project->getState();

        //echo "state=" . $state . "<br>";
        //if ($review) {
        //    echo "review getStateStr=" . $review->getStateStr() . "<br>";
        //}
        //exit();

        if( !$state ) {
            return null;
        }

        $pending = NULL;
        $approved = "approved";
        $reset = false;

        if( $state == "irb_review" || $state == "draft" ) { //strpos($state, "irb_") !== false
            //set before stages
            //set after stages
            $this->setProjectDecision($project->getIrbReviews(),$pending);
            $this->setProjectDecision($project->getAdminReviews(),$pending);
            $this->setProjectDecision($project->getCommitteeReviews(),$pending);
            $this->setProjectDecision($project->getFinalReviews(),$pending);
            $reset = true;
        }
        if( $state == "admin_review" ) {
            //set before stages
            $this->setProjectDecision($project->getIrbReviews(),$approved);
            //set after stages
            $this->setProjectDecision($project->getAdminReviews(),$pending);
            $this->setProjectDecision($project->getCommitteeReviews(),$pending);
            $this->setProjectDecision($project->getFinalReviews(),$pending);
            $reset = true;
        }
        if( $state == "committee_review" ) {
            //set before stages
            $this->setProjectDecision($project->getIrbReviews(),$approved);
            $this->setProjectDecision($project->getAdminReviews(),$approved);
            //set after stages
            $this->setProjectDecision($project->getCommitteeReviews(),$pending);
            $this->setProjectDecision($project->getFinalReviews(),$pending);
            $reset = true;
        }
        if( $state == "final_review" ) {
            //set before stages
            $this->setProjectDecision($project->getIrbReviews(),$approved);
            $this->setProjectDecision($project->getAdminReviews(),$approved);
            $this->setProjectDecision($project->getCommitteeReviews(),$approved);
            //set after stages
            $this->setProjectDecision($project->getFinalReviews(),$pending);
            $reset = true;
        }

        $msg = null;
        if( $reset ) {
            $msg = "Reset $state state's reviews decisions and all children reviews to pending";
        }

        $msg = "<br>".$msg;

        return $msg;
    }
    public function setProjectDecision($reviews,$decision) {
        foreach($reviews as $review) {
            if( $review->getStateStr() === "committee_review" ) {
                if( $review->getPrimaryReview() === true ) {
                    $review->setDecision($decision);
                }
            } else {
                $review->setDecision($decision);
            }
        }
    }

    public function processDefaultReviewersRole( $defaultReviewer, $originalReviewer=null, $originalReviewerDelegate=null ) {

        $roles = $defaultReviewer->getRoleByState();
        $reviewerRole = $roles['reviewer'];
        //$reviewerDelegateRole = $roles['reviewerDelegate'];

        $specialtyObject = $defaultReviewer->getProjectSpecialty();
        $reviewerSpecialtyRole = $this->getSpecialtyRole($specialtyObject);

        $reviewer = $defaultReviewer->getReviewer();
        if( $reviewer ) {
            $reviewer->addRole($reviewerRole);
            $reviewer->addRole($reviewerSpecialtyRole);
        }
        //remove role: make sure if the user is not a default reviewer in all other objects. Or don't remove role at all.
        //if( $originalReviewer && $originalReviewer != $reviewer ) {
            //$originalReviewer->removeRole($reviewerRole);
        //}

//        $reviewerDelegate = $defaultReviewer->getReviewerDelegate();
//        if( $reviewerDelegate && $reviewerDelegateRole ) {
//            $reviewerDelegate->addRole($reviewerDelegateRole);
//        }
        $reviewerDelegate = $defaultReviewer->getReviewerDelegate();
        if( $reviewerDelegate ) {
            $reviewerDelegate->addRole($reviewerRole);
            $reviewerDelegate->addRole($reviewerSpecialtyRole);
        }

        //remove role: make sure if the user is not a default reviewer in all other objects. Or don't remove role at all.
        //if( $originalReviewerDelegate && $originalReviewerDelegate != $reviewerDelegate && $reviewerDelegateRole ) {
            //$originalReviewerDelegate->removeRole($reviewerDelegateRole);
        //}

        return $defaultReviewer;
    }

    //get the review's form page according to the project's current state (i.e. IRB Review Page) and the logged in user
//    public function getReviewLink( $project, $user=null ) {
//
//        //$workflow = $this->container->get('state_machine.transres_project');
//        //$transitions = $workflow->getEnabledTransitions($project);
//        //foreach($transitions as $transition) {
//        //    echo "transition=".$this->printTransition($transition)."<br>";
//        //}
//
//        $class = "btn btn-default";
//
//        //echo "project state=".$project->getState()."<br>";
//
//        switch( $project->getState() ) {
//            case "irb_review":
//                $thisUrl = $this->container->get('router')->generate(
//                    'translationalresearch_review_new',
//                    array(
//                        //'id'=>$project->getId()
//                    ),
//                    UrlGeneratorInterface::ABSOLUTE_URL
//                );
//                $link = "<a href=".$thisUrl." class='".$class."' target='_blank'>"."IRB Review"."</a>";
//                break;
//            default:
//                $link = "Not Available for ".$project->getState();
//        }
//
//        return $link;
//    }

    //get all reviewers forms, starting with the user's review form
//    public function getReviewFormsHtml($project, $user) {
//        $html = null;
//        switch( $project->getState() ) {
//
//            case "irb_review":
//                $reviewEntityName = "IrbReview";
//                $reviewObjects = $this->findReviewObjectsByProjectAndAnyReviewers($reviewEntityName,$project,$user);
//                foreach($reviewObjects as $reviewObject) {
//                    $disabled = true;
//                    if( $reviewObject->getReviewer() == $user || $reviewObject->getReviewerDelegate() == $user ) {
//                        $disabled = false;
//                    }
//                    $reviewForm = $this->createForm(ReviewBaseType::class, $reviewObject, array(
//                        //'form_custom_value' => $params,
//                        'data_class' => 'Oleg\\TranslationalResearchBundle\\Entity\\'.$reviewEntityName,
//                        'disabled' => $disabled
//                    ));
//                    //$reviewHtml = $this->render('OlegTranslationalResearchBundle:ReviewBaseController:Some.html.twig', array())->getContent();
//                    //$reviewHtml = $this->redirectToRoute('translationalresearch_project_show', array('id' => $project->getId()));
//                    //TODO: use include form translationalresearch_review_edit in twig
//                }
//                break;
//
////            case "admin_review":
////                $reviewEntityName = "AdminReview";
////                $reviewObjects = $this->findReviewObjectsByProjectAndAnyReviewers($reviewEntityName,$project,$user);
////                foreach($reviewObjects as $reviewObject) {
////                    $reviewForm = $this->createForm(ReviewBaseType::class, $reviewObject, array(
////                        //'form_custom_value' => $params,
////                        'data_class' => 'Oleg\\TranslationalResearchBundle\\Entity\\'.$reviewEntityName
////                    ));
////                }
////                break;
////
////            case "committee_review":
////                $reviewEntityName = "CommitteeReview";
////                $reviewObjects = $this->findReviewObjectsByProjectAndAnyReviewers($reviewEntityName,$project,$user);
////                foreach($reviewObjects as $reviewObject) {
////                    $reviewForm = $this->createForm(ReviewBaseType::class, $reviewObject, array(
////                        //'form_custom_value' => $params,
////                        'data_class' => 'Oleg\\TranslationalResearchBundle\\Entity\\'.$reviewEntityName
////                    ));
////                }
////                break;
////
////            case "final_review":
////                $reviewEntityName = "FinalReview";
////                $reviewObjects = $this->findReviewObjectsByProjectAndAnyReviewers($reviewEntityName,$project,$user);
////                foreach($reviewObjects as $reviewObject) {
////                    $reviewForm = $this->createForm(ReviewBaseType::class, $reviewObject, array(
////                        //'form_custom_value' => $params,
////                        'data_class' => 'Oleg\\TranslationalResearchBundle\\Entity\\'.$reviewEntityName
////                    ));
////                }
////                break;
//
//            default:
//                //
//        }
//        return $html;
//    }

//    public function getReviewIds($project, $user) {
//        $reviewIds = array();
//        $reviewIds[] = 4;
//        switch( $project->getState() ) {
//
//            case "irb_review":
//                $reviewEntityName = "IrbReview";
//                //$reviewIds[] = 4;
//                break;
//
//            case "admin_review":
//                $reviewEntityName = "AdminReview";
//
//                break;
//
//            case "committee_review":
//                $reviewEntityName = "CommitteeReview";
//
//                break;
//
//            case "final_review":
//                $reviewEntityName = "FinalReview";
//
//                break;
//
//            default:
//                //
//        }
//        return $reviewIds;
//    }

    public function getTransitionLabelByName( $transitionName, $review=null ) {

        //$returnLabel = "<$transitionName>";

        switch ($transitionName) {
            //initial stages
            case "to_draft":
                $label = "Save Draft Project Request";
                $labeled = "Saved as Draft";
                break;
//            case "to_completed":
//                $label = "Complete Submission";
//                $labeled = "Completed Submission";
//                break;
            case "to_review":
                $label = "Submit Project Request to IRB Review";
                $labeled = "Submitted to IRB Review";
                break;
            //final stages
            case "approved_closed":
                $label = "Close Project Request";
                $labeled = "Closed Project Request";
                break;
            case "closed_approved":
                $label = "Re-Open previously Final Approved Project Request";
                $labeled = "Re-Opened (previously Final Approved Project Request)";
                break;

//            ///// Main Actions /////
            case "irb_review_approved":
                $label = "Approve Project Request as a Result of IRB Review";
                $labeled = "Approved Project Request as a Result of IRB Review";
                break;
            case "irb_review_rejected":
                $label = "Reject Project Request as a Result of IRB Review";
                $labeled = "Rejected Project Request as a Result of IRB Review";
                break;
            case "irb_review_missinginfo":
                $label = "Request additional information from submitter for IRB Review";
                $labeled = "Requested additional information from submitter for IRB Review";
                break;
            case "irb_review_resubmit":
                $label = "Resubmit Project Request to IRB Review";
                $labeled = "Resubmitted Project Request to IRB Review";
                break;

            case "admin_review_approved":
                $label = "Approve Project Request as a Result of Admin Review";
                $labeled = "Approved Project Request as a Result of Admin Review";
                break;
            case "admin_review_rejected":
                $label = "Reject Project Request as a Result of Admin Review";
                $labeled = "Rejected Project Request as a Result of Admin Review";
                break;
            case "admin_review_missinginfo":
                $label = "Request additional information from submitter for Admin Review";
                $labeled = "Requested additional information from submitter for Admin Review";
                break;
            case "admin_review_resubmit":
                $label = "Resubmit Project Request to Admin Review";
                $labeled = "Resubmitted Project Request to Admin Review";
                break;

            case "committee_review_approved":
                $label = "Approve Project Request as a Result of Committee Review";
                $labeled = "Approved Project Request as a Result of Committee Review";
                if( method_exists($review, 'getPrimaryReview') ) {
                    if ($review->getPrimaryReview() === true) {
                        //$label = $label . " as Primary Reviewer";
                        //$labeled = $labeled . " as Primary Reviewer";
                    } else {
                        $userInfo = $this->getReviewerInfo($review);
                        $label = "Recommend Approval as a Result of Committee Review" . $userInfo;
                        $labeled = "Recommended Approval as a Result of Committee Review" . $userInfo;
                    }
                }
                break;
            case "committee_review_rejected":
                $label = "Reject Project Request as a Result of Committee Review";
                $labeled = "Rejected Project Request as a Result of Committee Review";
                if( method_exists($review, 'getPrimaryReview') ) {
                    if ($review->getPrimaryReview() === true) {
                        //$label = $label . " as Primary Reviewer";
                        //$labeled = $labeled . " as Primary Reviewer";
                    } else {
                        $userInfo = $this->getReviewerInfo($review);
                        //$label = "Recommend Reject Committee Review".$userInfo;
                        $label = "Recommend Rejection as a Result of Committee Review" . $userInfo;
                        $labeled = "Recommended Rejection as a Result of Committee Review" . $userInfo;
                    }
                }
                break;
//            case "committee_review_missinginfo":
//                $label = "Request additional information from submitter for Committee Review";
//                $labeled = "Requested additional information from submitter for Committee Review";
//                if( method_exists($review, 'getPrimaryReview') ) {
//                    if ($review->getPrimaryReview() === true) {
//                        $label = $label . " as Primary Reviewer";
//                        $labeled = $labeled . " as Primary Reviewer";
//                    }
//                }
//                break;
            case "committee_review_resubmit":
                $label = "Resubmit Project Request to Committee Review";
                $labeled = "Resubmitted Project Request to Committee Review";
                break;

            case "final_review_approved":
                $label = "Approve Project Request as a Result of Final Review";
                $labeled = "Approved Project Request as a Result of Final Review";
                break;
            case "final_review_rejected":
                $label = "Reject Project Request as a Result of Final Review";
                $labeled = "Rejected Project Request as a Result of Final Review";
                break;
//            case "final_review_missinginfo":
//                $label = "Request additional information from submitter for Final Review";
//                $labeled = "Requested additional information from submitter for Final Review";
//                break;
            case "final_review_resubmit":
                $label = "Resubmit Project Request to Final Review";
                $labeled = "Resubmitted Project Request to Final Review";
                break;

            case "committee_finalreview_approved":
                $label = "Provide Final Approval";
                $labeled = "Provided Final Approval";
                break;

            default:
                $label = null;
                $labeled = null;
        }

        if( $label ) {
            $returnLabel = $label;
        } else {
            //irb_review_approved => IRB Review Approved
            //irb_review_rejected => IRB Review Rejected
            //irb_review_missinginfo => IRB Review Missinginfo
            //irb_review_resubmit => IRB Review Resubmit
            $label = str_replace("_"," ",$transitionName);
            $label = str_replace("missinginfo","missing information",$label);
            $returnLabel = ucwords($label);
        }

        return $returnLabel;
    }
    public function getReviewerInfo($review) {
        $userInfo = "";
        if( $this->secAuth->isGranted('ROLE_TRANSRES_ADMIN') ) {
            $reviewer = $review->getReviewer();
            if( $reviewer ) {
                $userInfo = " (as " . $reviewer->getDisplayName() . ")";
            }
        }
        return $userInfo;
    }

    public function getStateLabelByProject( $project ) {
        return $this->getStateLabelByName($project->getState());
    }
    public function getStateLabelByName( $stateName ) {
        switch ($stateName) {
            case "start":
                $state = "New Project";
                break;
            case "draft":
                $state = "Draft";
                break;
//            case "completed":
//                $state = "Completed";
//                break;

            case "irb_review":
                $state = "IRB Review";
                break;
            case "irb_rejected":
                $state = "IRB Review Rejected";
                break;
            case "irb_missinginfo":
                $state = "Pending additional information from submitter for IRB Review";
                break;

            case "admin_review":
                $state = "Admin Review";
                break;
            case "admin_rejected":
                $state = "Admin Review Rejected";
                break;
            case "admin_missinginfo":
                $state = "Pending additional information from submitter for Admin Review";
                break;

            case "committee_review":
                $state = "Committee Review";
                break;
            case "committee_rejected":
                $state = "Committee Review Rejected";
                break;
//            case "committee_missinginfo":
//                $state = "Pending additional information from submitter for Committee Review";
//                break;

            case "final_review":
                $state = "Final Review";
                break;
            case "final_approved":
                $state = "Approved";
                break;
            case "final_rejected":
                $state = "Final Review Rejected";
                break;
//            case "final_missinginfo":
//                $state = "Pending additional information from submitter for Final Review";
//                break;

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
//            case "completed":
//                $state = "Completed";
//                break;

            case "irb_review":
                $state = "IRB Review";
                break;
            case "irb_rejected":
                $state = "IRB Review Rejected";
                break;
            case "irb_missinginfo":
                $state = "Request additional information from submitter for IRB Review";
                break;

            case "admin_review":
                $state = "Admin Review";
                break;
            case "admin_rejected":
                $state = "Admin Review Rejected";
                break;
            case "admin_missinginfo":
                $state = "Request additional information from submitter for Admin Review";
                break;

            case "committee_review":
                $state = "Committee Review";
                break;
            case "committee_rejected":
                $state = "Committee Review Rejected";
                break;
//            case "committee_missinginfo":
//                $state = "Request additional information from submitter for Committee Review";
//                break;

            case "final_review":
                $state = "Final Review";
                break;
            case "final_approved":
                $state = "Approved";
                break;
            case "final_rejected":
                $state = "Final Review Rejected";
                break;
//            case "final_missinginfo":
//                $state = "Request additional information from submitter for Final Review";
//                break;

            case "closed":
                $state = "Closed";
                break;

            default:
                $state = "<$stateName>";

        }
        return $state;
    }

    public function getReviewClassNameByState($state) {
//        switch( $state ) {
//            case "irb_review":
//                $reviewEntityName = "IrbReview";
//                break;
//            case "admin_review":
//                $reviewEntityName = "AdminReview";
//                break;
//            case "committee_review":
//                $reviewEntityName = "CommitteeReview";
//                break;
//            case "final_review":
//                $reviewEntityName = "FinalReview";
//                break;
//            default:
//                $reviewEntityName = null;
//        }

        //echo "state=".$state."<br>";

        $reviewEntityName = null;

        if( strpos($state, "irb_") !== false ) {
            $reviewEntityName = "IrbReview";
        }
        if( strpos($state, "admin_") !== false ) {
            $reviewEntityName = "AdminReview";
        }
        if( strpos($state, "committee_") !== false ) {
            $reviewEntityName = "CommitteeReview";
        }
        if( strpos($state, "final_") !== false ) {
            $reviewEntityName = "FinalReview";
        }

        return $reviewEntityName;
    }

    public function getStateChoisesArr() {
        $stateArr = array(
            //'start', //Edit Project
            'draft',
//            'completed',

            'irb_review',
            'irb_rejected',
            'irb_missinginfo',

            'admin_review',
            'admin_rejected',
            'admin_missinginfo',

            'committee_review',
            'committee_rejected',
//            'committee_missinginfo',

            'final_review',
            'final_approved',
            'final_rejected',
//            'final_missinginfo',

            'closed'
        );

        $stateChoiceArr = array();

        foreach($stateArr as $state) {
            //$label = $state;
            $label = $this->getStateLabelByName($state);
            //$label = $label . " (" . $state . ")";
            $stateChoiceArr[$label] = $state;
        }

        return $stateChoiceArr;
    }
    public function getDefaultStatesArr() {
        $defaultStatesArr = array();
        $states = $this->getStateChoisesArr();
        foreach($states as $state) {
            if( $state != 'draft' ) {
                $defaultStatesArr[] = $state;
            }
        }

        return $defaultStatesArr;
    }

    public function getHtmlClassTransition( $transitionName ) {

        //irb_review_approved => IRB Review Approved
        //irb_review_rejected => IRB Review Rejected
        //irb_review_missinginfo => IRB Review Missinginfo
        //irb_review_resubmit => IRB Review Resubmit
        if( strpos($transitionName, "_approved") !== false ) {
            if( strpos($transitionName, "finalreview_approved") !== false ) {
                return "btn btn-warning transres-review-submit"; //btn-primary
            }
            return "btn btn-success transres-review-submit";
        }
        if( strpos($transitionName, "_missinginfo") !== false ) {
            return "btn btn-warning transres-review-submit transres-missinginfo";
        }
        if( strpos($transitionName, "_rejected") !== false ) {
            return "btn btn-danger transres-review-submit";
        }
        if( strpos($transitionName, "_resubmit") !== false ) {
            return "btn btn-success transres-review-submit";
        }

        return "btn btn-default";
    }

    //NOT USED
//    public function getDecisionByTransitionName( $transitionName ) {
//
//        //irb_review_approved => IRB Review Approved
//        //irb_review_rejected => IRB Review Rejected
//        //irb_review_missinginfo => IRB Review Missinginfo
//        //irb_review_resubmit => IRB Review Resubmit
//        if( strpos($transitionName, "_approved") !== false ) {
//            return "approved";
//        }
//        if( strpos($transitionName, "_missinginfo") !== false ) {
//            return "missinginfo";
//        }
//        if( strpos($transitionName, "_rejected") !== false ) {
//            return "rejected";
//        }
//        if( strpos($transitionName, "_resubmit") !== false ) {
//            return null;
//        }
//
//
//        return null;
//    }

    public function getReviewByReviewidAndState($reviewId, $state) {

        $reviewEntityName = $this->getReviewClassNameByState($state);
        if( !$reviewEntityName ) {
            throw new \Exception('Unable to find Review Entity Name by state='.$state);
        }
        //echo "reviewEntityName=".$reviewEntityName."<br>";

        $reviewObject = $this->em->getRepository('OlegTranslationalResearchBundle:'.$reviewEntityName)->find($reviewId);
        if( !$reviewObject ) {
            throw new \Exception('Unable to find '.$reviewEntityName.' by id='.$reviewId);
        }

        return $reviewObject;
    }

    public function getReviewsByProjectAndState($project,$state) {
        $reviewEntityName = $this->getReviewClassNameByState($state);
        if( !$reviewEntityName ) {
            throw new \Exception('Unable to find Review Entity Name by state='.$state);
        }

        $reviews = $this->findReviewObjectsByProjectAndAnyReviewers($reviewEntityName,$project);

        return $reviews;
    }

    //NOT USED
//    public function getReviewByProjectAndReviewidAndState($project, $reviewId, $state) {
//
//        $reviewEntityName = $this->getReviewClassNameByState($state);
//        if( !$reviewEntityName ) {
//            throw new \Exception('Unable to find Review Entity Name by state='.$state);
//        }
//        //echo "reviewEntityName=".$reviewEntityName."<br>";
//
//        if(1) {
//            $reviewObject = $this->em->getRepository('OlegTranslationalResearchBundle:'.$reviewEntityName)->find($reviewId);
//            if( !$reviewObject ) {
//                throw new \Exception('Unable to find '.$reviewEntityName.' by id='.$reviewId);
//            }
//            return $reviewObject;
//        } else {
//
//            $reviewObjects = $this->findReviewObjectsByProjectAndAnyReviewers($reviewEntityName, $project, null, $reviewId);
//            //echo "reviewObjects count=".count($reviewObjects)."<br>";
//
//            if (count($reviewObjects) == 1) {
//                return $reviewObjects[0];
//            }
//
//            if (count($reviewObjects) == 0) {
//                return null;
//            }
//
//            if (count($reviewObjects) > 1) {
//                //throw new \Exception("No single Review object $reviewEntityName founded: ID ".$reviewId);
//                return $reviewObjects[0];
//            }
//        }
//
//        return null;
//    }

    //create a review form (for example, IrbReview form if logged in user is a reviewer or reviewer delegate)
    //1) if project is in the review state: irb_review, admin_review, committee_review or final_review
    //2) if the current user is added to this project as the reviewer for the state above
    public function getReviewForm($project, $user) {

        switch( $project->getState() ) {

            case "irb_review":
                $reviewEntityName = "IrbReview";
                $reviewObjects = $this->findReviewObjectsByProjectAndAnyReviewers($reviewEntityName,$project,$user);
                if( count($reviewObjects) > 0 ) {
                    $reviewForm = $this->createForm(ReviewBaseType::class, $reviewObjects[0], array(
                        //'form_custom_value' => $params,
                        'data_class' => 'Oleg\\TranslationalResearchBundle\\Entity\\'.$reviewEntityName
                    ));
                }
                break;

            case "admin_review":
                $reviewEntityName = "AdminReview";
                $reviewObjects = $this->findReviewObjectsByProjectAndAnyReviewers($reviewEntityName,$project,$user);
                if( count($reviewObjects) > 0 ) {
                    $reviewForm = $this->createForm(ReviewBaseType::class, $reviewObjects[0], array(
                        //'form_custom_value' => $params,
                        'data_class' => 'Oleg\\TranslationalResearchBundle\\Entity\\'.$reviewEntityName
                    ));
                }
                break;

            case "committee_review":
                $reviewEntityName = "CommitteeReview";
                $reviewObjects = $this->findReviewObjectsByProjectAndAnyReviewers($reviewEntityName,$project,$user);
                if( count($reviewObjects) > 0 ) {
                    $reviewForm = $this->createForm(ReviewBaseType::class, $reviewObjects[0], array(
                        //'form_custom_value' => $params,
                        'data_class' => 'Oleg\\TranslationalResearchBundle\\Entity\\'.$reviewEntityName
                    ));
                }
                break;

            case "final_review":
                $reviewEntityName = "FinalReview";
                $reviewObjects = $this->findReviewObjectsByProjectAndAnyReviewers($reviewEntityName,$project,$user);
                if( count($reviewObjects) > 0 ) {
                    $reviewForm = $this->createForm(ReviewBaseType::class, $reviewObjects[0], array(
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
    public function findReviewObjectsByProjectAndAnyReviewers( $reviewObjectClassName, $project, $reviewer=null, $reviewId=null ) {
//        $reviewObject = null;
//        if( $reviewObjectClassName && $reviewer ) {
//            $reviewObject = $this->em->getRepository('OlegTranslationalResearchBundle:' . $reviewObjectClassName)->findBy(array(
//                'reviewer' => $reviewer->getId(),
//                'project' => $project->getId()
//            ));
//            if (!$reviewObject) {
//                $reviewObject = $this->em->getRepository('OlegTranslationalResearchBundle:' . $reviewObjectClassName)->findByReviewerDelegate($reviewer);
//            }
//        }
        $repository = $this->em->getRepository('OlegTranslationalResearchBundle:' . $reviewObjectClassName);
        $dql =  $repository->createQueryBuilder("review");
        $dql->select('review');
        $dql->GroupBy('review');
        $dql->leftJoin("review.project", "project");
        $dql->leftJoin("review.reviewer", "reviewer");
        $dql->leftJoin("review.reviewerDelegate", "reviewerDelegate");

        $dql->where("project.id=:projectId");

        $parameters = array("projectId"=>$project->getId());

        if( $reviewer ) {
            $dql->andWhere("reviewer.id=:reviewerId OR reviewerDelegate.id=:reviewerId");
            $parameters['reviewerId'] = $reviewer->getId();
        }

        if( $reviewId ) {
            $dql->andWhere("review.id=:reviewId");
            $parameters['reviewId'] = $reviewId;
        }

        $query = $dql->getQuery();

        //echo "projectId=".$project->getId()."<br>";
        //echo "reviewId=".$reviewId."<br>";
        //echo "query=".$query->getSql()."<br>";

        $query->setParameters($parameters);

        $reviewObjects = $query->getResult();

        return $reviewObjects;
    }

    //NOT USED: roles are not relable for each project
    //add user's validation (rely on Role): $from=irb_review => user has role _IRB_REVIEW_
    public function isUserAllowedFromThisStateByRole($from) {

        if(
            $this->secAuth->isGranted('ROLE_TRANSRES_ADMIN') ||
            $this->secAuth->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER') ||
            $this->secAuth->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER_DELEGATE')
        ) {
            return true;
        }

        $user = $this->secTokenStorage->getToken()->getUser();
        $sitename = $this->container->getParameter('translationalresearch.sitename');

        $role = null;
        if( $from == "irb_review" ) {
            $role = "_IRB_REVIEW_";
        }
        if( $from == "admin_review" ) {
            $role = "_ADMIN_REVIEW_";
        }
        if( $from == "committee_review" ) {
            $role = "_COMMITTEE_REVIEW_";
        }
        if( $from == "final_review" ) {
            $role = "_FINAL_REVIEW_";
        }

        if( $role && $this->em->getRepository('OlegUserdirectoryBundle:User')->isUserHasSiteAndPartialRoleName($user,$sitename,$role) ) {
            return true;
        }

        return false;
    }
    //check if the current logged in user has permission to make changes from the current project state
    public function isUserAllowedFromThisStateByProjectOrReview($project, $review=null) {

        if( !$project ) {
            $project = $review->getProject();
        }

        if( $project && $this->isUserAllowedSpecialtyObject($project->getProjectSpecialty()) === false ) {
            return false;
        }

//        $workflow = $this->container->get('state_machine.transres_project');
//        $transitions = $workflow->getEnabledTransitions($project);
//
//        if( count($transitions) != 1 ) {
//            throw new \Exception("Review with ID ".$review->getId()." does not have a project");
//        }
//
//        foreach($transitions as $transition) {
//            $this->printTransition($transition);
//        }
//        exit('1');

        $stateStr = $this->getAllowedFromState($project); //must be equal to the current project state
        if( !$stateStr ) {
            return false;
        }

        if( $this->isAdminOrPrimaryReviewer() ) {
            return true;
        }

        $user = $this->secTokenStorage->getToken()->getUser();

        //check if reviewer
        if( $this->isProjectStateReviewer($project,$user) ) {
            return true;
        }

        //check if submitter and project state has _missinginfo
//        if( strpos($stateStr, "_missinginfo") !== false ) {
//            if( $this->isProjectRequester($project) ) {
//                return true;
//            }
//        }
        if( $this->isProjectStateRequesterResubmit($project) ) {
            return true;
        }

        return false;
    }

    //return true if the project is in missinginfo state and logged in user is a requester or admin
    public function isProjectStateRequesterResubmit($project) {

        if( $project && $this->isUserAllowedSpecialtyObject($project->getProjectSpecialty()) === false ) {
            return false;
        }

        $stateStr = $this->getAllowedFromState($project);
        if( !$stateStr ) {
            return false;
        }

        if( strpos($stateStr, "_missinginfo") !== false ) {
            if ($this->isAdminOrPrimaryReviewer()) {
                return true;
            }
            if( $this->isProjectRequester($project) ) {
                return true;
            }
        }
        return false;
    }

    //return true if the project is in review state and logged in user is a reviewer or admin
    public function isProjectStateReviewer($project, $user, $stateStr=null, $onlyReviewer=false) {

        if( $project && $this->isUserAllowedSpecialtyObject($project->getProjectSpecialty()) === false ) {
            return false;
        }

        if( !$stateStr ) {
            $stateStr = $this->getAllowedFromState($project); //must be equal to the current project state
        }

        if( !$stateStr ) {
            return false;
        }

        $reviews = array();

        if( $stateStr == "irb_review" ) {
            $reviews = $project->getIrbReviews();
        }
        if( $stateStr == "admin_review" ) {
            $reviews = $project->getAdminReviews();
        }
        if( $stateStr == "committee_review" ) {
            $reviews = $project->getCommitteeReviews();
        }
        if( $stateStr == "final_review" ) {
            $reviews = $project->getFinalReviews();
        }

        //echo $stateStr.": reviews count=".count($reviews)."<br>";
        if( count($reviews) > 0 ) {
            if( $onlyReviewer == false ) {
                if( $this->isAdminOrPrimaryReviewer() ) {
                    return true;
                }
            }
            if( $this->isReviewsReviewer($user, $reviews) ) {
                return true;
            }
        }

        return false;
    }

    //only a single from state must exists, but a project can have multiple transitions and tos
    public function getAllowedFromState($project) {

        $stateStr = $project->getState();

        //double check this state by state machine
        $workflow = $this->container->get('state_machine.transres_project');

        $transitions = $workflow->getEnabledTransitions($project);

        foreach( $transitions as $transition ) {
            foreach( $transition->getFroms() as $fromStateStr ) {
                if( $fromStateStr == $stateStr ) {
                    return $stateStr;
                }
            }
        }

        return null;

//        if( count($transitions) == 0 ) {
//            //rejected => no states
//            return null;
//            //throw new \Exception("State Machine: Project does not have any transition. project state=".$project->getState());
//        }
//        //TODO: project can have 3 transitions => rewrite to get a single fromState
//        if( count($transitions) != 1 ) {
//            throw new \Exception("State Machine: Project must have only one transition. Number of transitions=".count($transitions));
//        }
//        $transition = $transitions[0];
//
//        $froms = $transition->getFroms();
//        if( count($froms) != 1 ) {
//            throw new \Exception("State Machine: Project must have only one from state. Number of from states=".count($froms));
//        }
//        $from = $froms[0];
//
//        return $from;
    }

    public function isUserAllowedReview( $review ) {
        //echo "reviewId=".$review->getId()."<br>";
        if(
            $this->secAuth->isGranted('ROLE_TRANSRES_ADMIN') ||
            $this->secAuth->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER') ||
            $this->secAuth->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER_DELEGATE')
        ) {
            //echo "isUserAllowedReview: admin ok <br>";
            return true;
        }

        $user = $this->secTokenStorage->getToken()->getUser();
        if( $this->isReviewer($user,$review) ) {
            return true;
        }

        return false;
    }

    public function isReviewCorrespondsToState($review) {
        if( !$review ) {
            return false;
        }
        $project = $review->getProject();
        if( !$project ) {
            return false;
        }

        //1) check if project state is reviewable
        $projectStateReviewable = false;
        $projectState = $project->getState();
        //echo "projectId=".$project->getId()."<br>";
        //echo "projectState=".$projectState."<br>";

        //check if the $review->getStateStr() has prefix (i.e. irb)
        //only if transitionName=irb_review_resubmit == irb class
        $statePrefixArr= explode("_", $projectState); //irb,review
        $statePrefix = $statePrefixArr[0]; //irb
        if( strpos($review->getStateStr(), $statePrefix) !== false ) {
            return true;
        }

        return false;

//        if( strpos($projectState, '_missinginfo') !== false ) {
//            $projectStateReviewable = true;
//            if( strpos($projectState, '_missinginfo') !== false ) {
//
//            }
//        }
//
//        if( $projectState == "irb_review" ) {
//            $projectStateReviewable = true;
//        }
//        if( $projectState == "admin_review" ) {
//            $projectStateReviewable = true;
//        }
//        if( $projectState == "committee_review" ) {
//            $projectStateReviewable = true;
//        }
//        if( $projectState == "final_review" ) {
//            $projectStateReviewable = true;
//        }
//
//        if( $projectStateReviewable === false ) {
//            return false;
//        }
//
//        //2) condition to allow edit only if project state is allow to edit this type of review (committee_review)
//        if( $projectState == $review->getStateStr() ) {
//            return true;
//        }
//
//        return false;
    }

    //NOT USED
    public function processProjectOnReviewUpdate( $review, $stateStr, $request, $testing=false ) {

        $project = $review->getProject();
        if( !$project ) {
            throw new \Exception("Review with ID ".$review->getId()." does not have a project");
            //return null;
        }

        $user = $this->secTokenStorage->getToken()->getUser();
        $userSecUtil = $this->container->get('user_security_utility');
        $transresUtil = $this->container->get('transres_util');
        $break = "\r\n";
        //echo "user=".$user."<br>";

        //$currentState = $project->getState();

        //set project next transit state depends on the decision
        $appliedTransition = $this->setProjectState($project,$review,$testing);
        //exit("exit appliedTransition=".$appliedTransition);

        if( $appliedTransition ) {
            //$recommended = false;
            $eventType = "Review Submitted";
            $label = $this->getTransitionLabelByName($appliedTransition,$review);//not used
            $subject = "Project ID ".$project->getOid()." has been sent to the status '$label'";
            $body = "Project ID ".$project->getOid()." has been sent to the status '$label'";
        } else {
            //$recommended = true;
            $eventType = "Review Submitted";
            $label = $this->getStateLabelByName($project->getState());
            $subject = "Project ID ".$project->getOid(). " (" .$label. "). Recommendation: ".$review->getDecision();
            $body = $subject;
        }

        //get project url
        $projectUrl = $transresUtil->getProjectShowUrl($project);
        $emailBody = $body . $break.$break. "Please click on the URL below to view this project:".$break.$projectUrl;

        //send notification emails
        $this->sendNotificationEmails($project,$review,$subject,$emailBody,$testing);

//        $workflow = $this->container->get('state_machine.transres_project');
//        $transitions = $workflow->getEnabledTransitions($project);
//        $transitionArr = array();
//        foreach ($transitions as $transition) {
//            echo "transition=" . $this->printTransition($transition) . "<br>";
//            $transitionArr[] = $this->printTransition($transition);
//        }
//        $projectTransition = "Project transition " . implode(";",$transitionArr);

//        //Event Log
//        if( $appliedTransition ) {
//            $eventType = "Review Submitted";
//            $event = "Project's (ID# " . $project->getId() . ") review has been successfully submitted. ".$review->getSubmittedReviewerInfo();
//
//            //testing
//            echo "appliedTransition=" . $appliedTransition . "<br>";
//            //echo "printTransition=".$this->printTransition($appliedTransition)."<br>";
//
//            $event .= ";<br> Project transitioned from '" . $this->getStateLabelByName($stateStr) . "'".
//                " to '" . $this->getStateLabelByName($project->getState()) . "'";
//            echo "event=".$event."<br>";
//
//            //exit('1');
//
//        } else {
//            $eventType = "Review Submitting Not Performed";
//            $event = "Project's (ID# " . $project->getId() . ") review submitting not performed. " . $review->getSubmittedReviewerInfo();
//            $event .= ";<br> Project transitioned from '" . $this->getStateLabelByName($stateStr) . "'" .
//                " to '" . $this->getStateLabelByName($project->getState()) . "'";
//            echo "event=".$event."<br>";
//
//            //exit('2');
//        }
//
//        $userSecUtil = $this->container->get('user_security_utility');
//        $userSecUtil->createUserEditEvent($this->container->getParameter('translationalresearch.sitename'),$event,$user,$review,$request,$eventType);

        //event log
        //$this->setEventLog($project,$review,$appliedTransition,$stateStr,$eventType,$body,$testing);
        $this->setEventLog($project,$eventType,$body,$testing);
    }
    //used by processProjectOnReviewUpdate
    public function setProjectState( $project, $review, $testing=false ) {

        $appliedTransition = null;

        //echo "decision=".$review->getDecision()."<br>";
        if( $review->getDecision() == null ) {
            return $appliedTransition;
        }

        //for not primary Committee review don't change the project state.
        //if( is_a($review,"CommitteeReview") ) {
        if( $review instanceof CommitteeReview ) {
            //echo "CommitteeReview <br>";
            if( $review->getPrimaryReview() === false ) {
                //exit("1");
                return $appliedTransition;
            }
        }

        $workflow = $this->container->get('state_machine.transres_project');
        $transitions = $workflow->getEnabledTransitions($project);

        //echo "<pre>";
        //print_r($transitions);
        //echo "</pre><br><br>";

        $transitionNameYes = null;
        $toYes = null;
        $transitionNameNo = null;
        $toNo = null;
        $transitionNameMissinginfo = null;
        $toMissinginfo = null;

        foreach($transitions as $transition) {
            $transitionName = $transition->getName();
            echo "transitionName=".$transitionName."<br>"; //"irb_review_no" or "to_admin_review"

            if( strpos($transitionName, '_rejected') !== false ) {
                echo "to: No<br>";
                $transitionNameNo = $transitionName;
                $tos = $transition->getTos();
                if( count($tos) > 1 ) {
                    throw new \Exception("State machine must have only one to state. To count=".count($tos));
                }
                $toNo = $tos[0];
            }

            if (strpos($transitionName, '_approved') !== false ) {
                echo "to: Yes<br>";
                $transitionNameYes = $transitionName;
                $tos = $transition->getTos();
                if (count($tos) > 1) {
                    throw new \Exception("State machine must have only one to state. To count=" . count($tos));
                }
                $toYes = $tos[0];
            }

            if (strpos($transitionName, '_missinginfo') !== false ) {
                echo "to: missinginfo<br>";
                $transitionNameMissinginfo = $transitionName;
                $tos = $transition->getTos();
                if (count($tos) > 1) {
                    throw new \Exception("State machine must have only one to state. To count=" . count($tos));
                }
                $toMissinginfo = $tos[0];
            }

        }//foreach

        if( $review->getDecision() == "rejected" && $toNo ) {
            echo "transit project to No: $toNo <br>";
            //$project->setState($toNo);
            $transitionNameFinal = $transitionNameNo;
            //$appliedTransition = true;
        }

        if( $review->getDecision() == "approved" && $toYes ) {
            echo "transit project to Yes: $toYes <br>";
            //$project->setState($toYes);
            $transitionNameFinal = $transitionNameYes;
            //$appliedTransition = true;
        }

        if( $review->getDecision() == "missinginfo" && $toMissinginfo ) {
            echo "transit project to missinginfo: $toMissinginfo <br>";
            //$project->setState($toMissinginfo);
            $transitionNameFinal = $transitionNameMissinginfo;
            //$appliedTransition = true;
        }

        if( $transitionNameFinal ) {
            try {
                echo "try apply transition=$transitionNameFinal <br>";
                $workflow->apply($project, $transitionNameFinal);
                $appliedTransition = $transitionNameFinal;

                //write to DB
                if( !$testing ) {
                    $this->em->flush($project);
                }

                $this->container->get('session')->getFlashBag()->add(
                    'notice',
                    "Successful transition: ".$transitionNameFinal."; Project request is in ".$this->getStateLabelByProject($project)." stage."
                );

            } catch (LogicException $e) {
                throw new \Exception("Can not change project's state: transitionNameFinal=" . $transitionNameFinal);
            }
        }

        echo "setProjectState: appliedTransition= $appliedTransition <br>";

        return $appliedTransition;
    }

    //Event Log
    public function setEventLog($project, $eventType, $event, $testing=false) {
        $user = $this->secTokenStorage->getToken()->getUser();
        $userSecUtil = $this->container->get('user_security_utility');

//        if( $appliedTransition ) {
//            $eventType = "Review Submitted";
//
//            if( !$event ) {
//                $event = "Project's (ID# " . $project->getId() . ") review has been successfully submitted. " . $review->getSubmittedReviewerInfo();
//
//                //testing
//                echo "appliedTransition=" . $appliedTransition . "<br>";
//                //echo "printTransition=".$this->printTransition($appliedTransition)."<br>";
//
//                $event .= ";<br> Project transitioned from '" . $this->getStateLabelByName($originalStateStr) . "'" .
//                    " to '" . $this->getStateLabelByName($project->getState()) . "'";
//                echo "event=" . $event . "<br>";
//
//                //exit('1');
//            }
//
//        } else {
//            $eventType = "Review Submitting Not Performed";
//            if( !$event ) {
//                $event = "Project's (ID# " . $project->getId() . ") review submitting not performed. " . $review->getSubmittedReviewerInfo();
//                $event .= ";<br> Project transitioned from '" . $this->getStateLabelByName($originalStateStr) . "'" .
//                    " to '" . $this->getStateLabelByName($project->getState()) . "'";
//                echo "event=" . $event . "<br>";
//
//                //exit('2');
//            }
//        }

        if( !$testing ) {
            $userSecUtil->createUserEditEvent($this->container->getParameter('translationalresearch.sitename'), $event, $user, $project, null, $eventType);
        }
    }

    public function sendNotificationEmails($project, $review, $subject, $body, $testing=false) {
        //if( !$appliedTransition ) {
        //    return null;
        //}

        $emailUtil = $this->container->get('user_mailer_utility');

        $senderEmail = null; //Admin email
        $emails = array();

//        $label = $this->getTransitionLabelByName($appliedTransition,$review);
//        if( $recommended ) {
//            $subject = "Project ID ".$project->getOid()." has been recommended to send to the status '$label'";
//            $body = "Project ID ".$project->getOid()." has been sent to the status '$label'";
//        } else {
//            $subject = "Project ID ".$project->getOid()." has been sent to the status '$label'";
//            $body = "Project ID ".$project->getOid()." has been sent to the status '$label'";
//        }

        //send to the
        // 1) admins and primary reviewers
        $admins = $this->getTransResAdminEmails(); //ok
        $emails = array_merge($emails,$admins);

        // 2) project's Requester (submitter, principalInvestigators, coInvestigators, pathologists)
        $requesterEmails = $this->getRequesterEmails($project); //ok
        $emails = array_merge($emails,$requesterEmails);

        if( $review ) {
            // 3) current project's reviewers
            $currentReviewerEmails = $this->getCurrentReviewersEmails($review); //ok
            $emails = array_merge($emails, $currentReviewerEmails);
        }

        // 4) next state project's reviewers
        $nextStateReviewerEmails = $this->getNextStateReviewersEmails($project,$project->getState());
        $emails = array_merge($emails,$nextStateReviewerEmails);

        $emails = array_unique($emails);

        //                    $emails, $subject, $message, $ccs=null, $fromEmail=null
        $emailUtil->sendEmail( $emails, $subject, $body, null, $senderEmail );

    }
    //get all users with admin and ROLE_TRANSRES_PRIMARY_REVIEWER, ROLE_TRANSRES_PRIMARY_REVIEWER_DELEGATE
    public function getTransResAdminEmails($asEmail=true, $onlyAdmin=false) {
        $users = array();

        $admins = $this->em->getRepository('OlegUserdirectoryBundle:User')->findUserByRole("ROLE_TRANSRES_ADMIN");
        foreach( $admins as $user ) {
            if( $user ) {
                if( $asEmail ) {
                    $users[] = $user->getSingleEmail();
                } else {
                    $users[] = $user;
                }
            }
        }

        if( $onlyAdmin ) {
            return $users;
        }

        $primarys = $this->em->getRepository('OlegUserdirectoryBundle:User')->findUserByRole("ROLE_TRANSRES_PRIMARY_REVIEWER");
        foreach( $primarys as $user ) {
            if( $user ) {
                if( $asEmail ) {
                    $users[] = $user->getSingleEmail();
                } else {
                    $users[] = $user;
                }
            }
        }

        return $users;
    }
    //project's Requester (submitter, principalInvestigators, coInvestigators, pathologists)
    public function getRequesterEmails($project, $asEmail=true) {
        $resArr = array();

        //1 submitter
        if( $project->getSubmitter() ) {
            if( $asEmail ) {
                $resArr[] = $project->getSubmitter()->getSingleEmail();
            } else {
                $resArr[] = $project->getSubmitter();
            }
        }

        //2 principalInvestigators
        $pis = $project->getPrincipalInvestigators();
        foreach( $pis as $pi ) {
            if( $pi ) {
                if( $asEmail ) {
                    $resArr[] = $pi->getSingleEmail();
                } else {
                    $resArr[] = $pi;
                }
            }
        }

        //3 coInvestigators
        $cois = $project->getCoInvestigators();
        foreach( $cois as $coi ) {
            if( $coi ) {
                if( $asEmail ) {
                    $resArr[] = $coi->getSingleEmail();
                } else {
                    $resArr[] = $coi;
                }
            }
        }

        //4 pathologists
        $pathologists = $project->getPathologists();
        foreach( $pathologists as $pathologist ) {
            if( $pathologist ) {
                if( $asEmail ) {
                    $resArr[] = $pathologist->getSingleEmail();
                } else {
                    $resArr[] = $pathologist;
                }
            }
        }

        //5 contacts
        $contacts = $project->getContacts();
        foreach( $contacts as $contact ) {
            if( $contact ) {
                if( $asEmail ) {
                    $resArr[] = $contact->getSingleEmail();
                } else {
                    $resArr[] = $contact;
                }
            }
        }

        //6 Billing contacts
        $billingContacts = $project->getBillingContacts();
        foreach( $billingContacts as $billingContact ) {
            if( $billingContact ) {
                if( $asEmail ) {
                    $resArr[] = $billingContact->getSingleEmail();
                } else {
                    $resArr[] = $billingContact;
                }
            }
        }

        return $resArr;
    }

    //current project's reviewers
    public function getCurrentReviewersEmails($review, $asEmail=true) {
        $resArr = array();

        //get reviewers
        $reviewer = $review->getReviewer();
        if( $reviewer ) {
            if( $asEmail ) {
                $resArr[] = $reviewer->getSingleEmail();
            } else {
                $resArr['reviewer'] = $reviewer;//->getUsernameOptimal();
            }
        }

        $reviewerDelegate = $review->getReviewerDelegate();
        if( $reviewerDelegate ) {
            if( $asEmail ) {
                $resArr[] = $reviewerDelegate->getSingleEmail();
            } else {
                $resArr['reviewerDelegate'] = $reviewerDelegate;//->getUsernameOptimal();
            }
        }

        return $resArr;
    }

    //next state project's reviewers
    public function getNextStateReviewersEmails($project, $nextStateStr, $asEmail=true) {
        $emails = array();

        //get next state
        $reviews = $this->getReviewsByProjectAndState($project,$nextStateStr);
        foreach($reviews as $review) {
            $currentReviewerEmails = $this->getCurrentReviewersEmails($review); //ok
            $emails = array_merge($emails,$currentReviewerEmails);
        }

        $emails = array_unique($emails);

        return $emails;
    }

    public function getTransResProjectSpecialties( $userAllowed=true ) {
        $user = $this->secTokenStorage->getToken()->getUser();

        $specialties = $this->em->getRepository('OlegTranslationalResearchBundle:SpecialtyList')->findBy(
            array(
                'type' => array("default","user-added")
            ),
            array('orderinlist' => 'ASC')
        );

        $allowedSpecialties = array();

        foreach($specialties as $specialty) {
            if( $userAllowed ) {
                if( $this->isUserAllowedSpecialtyObject($specialty, $user) ) {
                    $allowedSpecialties[] = $specialty;
                }
            } else {
                $allowedSpecialties[] = $specialty;
            }

        }

        return $allowedSpecialties;
    }

    public function getCommentAuthorNameByLoggedUser( $comment ) {

        $authorType = $comment->getAuthorTypeDescription();
        if( $authorType ) {
            $authorType = " (".$authorType.")";
        } else {
            $authorType = $comment->getAuthorType();
            if( $authorType ) {
                $authorType = " (".$authorType.")";
            }
        }

        if( !$comment->getAuthor() ) {
            return "Anonymous" . $authorType;
        }

        $user = $this->secTokenStorage->getToken()->getUser();

        if( $this->isAdminOrPrimaryReviewer() ) {
            return $comment->getAuthorName() . $authorType;
        }

        if( $user->getId() == $comment->getAuthor()->getId() ) {
            return $comment->getAuthorName() . $authorType;
        }

        return "Anonymous" . $authorType;
    }

    public function getProjectShowUrl($project) {
        $projectUrl = $this->container->get('router')->generate(
            'translationalresearch_project_show',
            array(
                'id' => $project->getId(),
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $projectUrl;
    }

    //$specialtyStr: hematopathology, ap-cp
    public function getSpecialtyObject($specialtyAbbreviation) {
        //echo "specialtyStr=".$specialtyStr."<br>";
        $specialty = $this->em->getRepository('OlegTranslationalResearchBundle:SpecialtyList')->findOneByAbbreviation($specialtyAbbreviation);
        if( !$specialty ) {
            throw new \Exception( "Project specialty is not found by name '".$specialtyAbbreviation."'" );
        }

        return $specialty;
    }

    //show it only to admin, reviewers and reviewedBy users
    public function showReviewedBy( $reviewObject ) {

        if( $this->isAdminOrPrimaryReviewer() ) {
            return true;
        }

        $user = $this->secTokenStorage->getToken()->getUser();
        if( $this->isReviewer($user,$reviewObject) ) {
            return true;
        }



        return false;
    }

    //get list of projects: 1) state final_approved, 2) irbExpirationDate, 3) logged in user is requester, 4) reviewer
    public function getAvailableProjects( $finalApproved=true, $notExpired=true, $requester=true, $reviewer=true ) {

        //$transresRequestUtil = $this->container->get('transres_request_util');

        $user = $this->secTokenStorage->getToken()->getUser();
        $repository = $this->em->getRepository('OlegTranslationalResearchBundle:Project');
        $dql =  $repository->createQueryBuilder("project");
        $dql->select('project');

        $dql->leftJoin('project.submitter','submitter');
        $dql->leftJoin('project.principalInvestigators','principalInvestigators');
        $dql->leftJoin('principalInvestigators.infos','principalInvestigatorsInfos');

        $dql->leftJoin('project.irbReviews','irbReviews');
        $dql->leftJoin('irbReviews.reviewer','irbReviewer');
        $dql->leftJoin('irbReviews.reviewerDelegate','irbReviewerDelegate');

        $dql->leftJoin('project.adminReviews','adminReviews');
        $dql->leftJoin('adminReviews.reviewer','adminReviewer');
        $dql->leftJoin('adminReviews.reviewerDelegate','adminReviewerDelegate');

        $dql->leftJoin('project.committeeReviews','committeeReviews');
        $dql->leftJoin('committeeReviews.reviewer','committeeReviewer');
        $dql->leftJoin('committeeReviews.reviewerDelegate','committeeReviewerDelegate');

        $dql->leftJoin('project.finalReviews','finalReviews');
        $dql->leftJoin('finalReviews.reviewer','finalReviewer');
        $dql->leftJoin('finalReviews.reviewerDelegate','finalReviewerDelegate');

        $dql->leftJoin('project.coInvestigators','coInvestigators');
        $dql->leftJoin('project.pathologists','pathologists');
        $dql->leftJoin('project.billingContact','billingContact');
        $dql->leftJoin('project.contacts','contacts');

        $dql->orderBy("project.id","DESC");

        $dqlParameters = array();

        //1) state final_approved
        if( $finalApproved ) {
            $dql->andWhere("project.state = 'final_approved'");
            //$dqlParameters = array("state" => "final_approved");
        }

        //2) irbExpirationDate
        if( $notExpired ) {
            $dql->andWhere("project.irbExpirationDate >= CURRENT_DATE()");
        }

        //3) logged in user is requester (only if not admin)
        if( $requester ) {
            if (!$this->secAuth->isGranted("ROLE_TRANSRES_ADMIN")) {
                $myRequestProjectsCriterion =
                    "principalInvestigators.id = :userId OR " .
                    "coInvestigators.id = :userId OR " .
                    "pathologists.id = :userId OR " .
                    "contacts.id = :userId OR " .
                    "billingContact.id = :userId OR " .
                    "submitter.id = :userId";

                $dqlParameters["userId"] = $user->getId();
                $dql->andWhere($myRequestProjectsCriterion);
            }
        }

        //4 logged in user is reviewer (only if not admin)
        if( $reviewer ) {
            $myReviewProjectsCriterion =
                "irbReviewer.id = :userId OR ".
                "irbReviewerDelegate.id = :userId OR ".

                "adminReviewer.id = :userId OR ".
                "adminReviewerDelegate.id = :userId OR ".

                "committeeReviewer.id = :userId OR ".
                "committeeReviewerDelegate.id = :userId OR ".

                "finalReviewer.id = :userId OR ".
                "finalReviewerDelegate.id = :userId"
            ;
            $dqlParameters["userId"] = $user->getId();
            $dql->andWhere($myReviewProjectsCriterion);
        }

        //user specialty, if not admin
        if( !$this->secAuth->isGranted("ROLE_TRANSRES_ADMIN") ) {
            $specialtyIds = array();
            $specialties = $this->getTransResProjectSpecialties();
            foreach ($specialties as $specialtyObject) {
                $hasRole = false;
//                $role = null;
//                if ($specialtyObject->getAbbreviation() == "hematopathology") {
//                    $role = "ROLE_TRANSRES_HEMATOPATHOLOGY";
//                }
//                if ($specialtyObject->getAbbreviation() == "ap-cp") {
//                    $role = "ROLE_TRANSRES_APCP";
//                }
                $role = $this->getSpecialtyRole($specialtyObject);
                if ($role) {
                    //check security context
                    if ($this->secAuth->isGranted($role)) {
                        $hasRole = true;
                    }

                    //check user's roles
                    if ($user && $user->hasRole($role)) {
                        $hasRole = true;
                    }
                    if( $hasRole ) {
                        //$specialtyIds[] = "'".$specialtyObject->getId()."'";
                        $specialtyIds[] = $specialtyObject->getId();
                    }
                }
            }

            if( count($specialtyIds) > 0 ) {
                $dql->leftJoin("project.projectSpecialty", "projectSpecialty");
                $specialtyStr = "projectSpecialty.id IN (".implode(",",$specialtyIds).")";
                //echo "specialtyStr=$specialtyStr<br>";
                $dql->andWhere($specialtyStr);
            } else {
                $dql->leftJoin("project.projectSpecialty", "projectSpecialty");
                $dql->andWhere("projectSpecialty.id IS NULL");
            }
        }

        $query = $dql->getQuery();

        //echo "projectId=".$project->getId()."<br>";
        //echo "reviewId=".$reviewId."<br>";
        //echo "query=".$query->getSql()."<br>";

        if( count($dqlParameters) > 0 ) {
            $query->setParameters($dqlParameters);
        }

        $projects = $query->getResult();

        return $projects;

//        //check for Request can not be submitted for the expired project
//        $finalProjects = array();
//        //TODO: this loop can cause delay for large number of projects. => duplicate IRB expiration date in the project
//        foreach($projects as $project) {
//            if( $transresRequestUtil->isRequestCanBeCreated($project) ) {
//                $finalProjects[] = $project;
//            }
//        }
//
//        return $finalProjects;
    }
    //logged in user requester or reviewer or submitter
    public function getAvailableRequesterOrReviewerProjects() {
        $user = $this->secTokenStorage->getToken()->getUser();
        $repository = $this->em->getRepository('OlegTranslationalResearchBundle:Project');
        $dql =  $repository->createQueryBuilder("project");
        $dql->select('project');

        $dql->leftJoin('project.submitter','submitter');
        $dql->leftJoin('project.principalInvestigators','principalInvestigators');
        $dql->leftJoin('principalInvestigators.infos','principalInvestigatorsInfos');

        $dql->leftJoin('project.irbReviews','irbReviews');
        $dql->leftJoin('irbReviews.reviewer','irbReviewer');
        $dql->leftJoin('irbReviews.reviewerDelegate','irbReviewerDelegate');

        $dql->leftJoin('project.adminReviews','adminReviews');
        $dql->leftJoin('adminReviews.reviewer','adminReviewer');
        $dql->leftJoin('adminReviews.reviewerDelegate','adminReviewerDelegate');

        $dql->leftJoin('project.committeeReviews','committeeReviews');
        $dql->leftJoin('committeeReviews.reviewer','committeeReviewer');
        $dql->leftJoin('committeeReviews.reviewerDelegate','committeeReviewerDelegate');

        $dql->leftJoin('project.finalReviews','finalReviews');
        $dql->leftJoin('finalReviews.reviewer','finalReviewer');
        $dql->leftJoin('finalReviews.reviewerDelegate','finalReviewerDelegate');

        $dql->leftJoin('project.coInvestigators','coInvestigators');
        $dql->leftJoin('project.pathologists','pathologists');
        $dql->leftJoin('project.billingContact','billingContact');
        $dql->leftJoin('project.contacts','contacts');

        $dql->leftJoin("project.projectSpecialty", "projectSpecialty");

        $dql->orderBy("project.id","DESC");

        $dqlParameters = array();

        //1) logged in user is requester (only if not admin)
        if(
            //!$this->secAuth->isGranted("ROLE_TRANSRES_ADMIN") &&
            //!$this->secAuth->isGranted("ROLE_TRANSRES_EXECUTIVE_HEMATOPATHOLOGY")  &&
            //!$this->secAuth->isGranted('ROLE_TRANSRES_EXECUTIVE_APCP')
            !$this->isAdminOrPrimaryReviewerOrExecutive() &&
            !$this->secAuth->isGranted("ROLE_TRANSRES_TECHNICIAN")
        ) {
            $myRequestProjectsCriterion =
                "principalInvestigators.id = :userId OR " .
                "coInvestigators.id = :userId OR " .
                "pathologists.id = :userId OR " .
                "contacts.id = :userId OR " .
                "billingContact.id = :userId OR " .
                "submitter.id = :userId";

            $dqlParameters["userId"] = $user->getId();

            //2) logged in user is reviewer (only if not admin)
            $myReviewProjectsCriterion =
                "irbReviewer.id = :userId OR " .
                "irbReviewerDelegate.id = :userId OR " .

                "adminReviewer.id = :userId OR " .
                "adminReviewerDelegate.id = :userId OR " .

                "committeeReviewer.id = :userId OR " .
                "committeeReviewerDelegate.id = :userId OR " .

                "finalReviewer.id = :userId OR " .
                "finalReviewerDelegate.id = :userId";
            $dqlParameters["userId"] = $user->getId();

            $dql->where($myRequestProjectsCriterion . " OR " . $myReviewProjectsCriterion);

            $specialtyIds = array();
            $specialties = $this->getTransResProjectSpecialties();
            foreach ($specialties as $specialtyObject) {
                $hasRole = false;
//                $role = null;
//                if ($specialtyObject->getAbbreviation() == "hematopathology") {
//                    $role = "ROLE_TRANSRES_HEMATOPATHOLOGY";
//                }
//                if ($specialtyObject->getAbbreviation() == "ap-cp") {
//                    $role = "ROLE_TRANSRES_APCP";
//                }
                $role = $this->getSpecialtyRole($specialtyObject);
                if ($role) {
                    //check security context
                    if ($this->secAuth->isGranted($role)) {
                        $hasRole = true;
                    }

                    //check user's roles
                    if ($user && $user->hasRole($role)) {
                        $hasRole = true;
                    }
                    if ($hasRole) {
                        //$specialtyIds[] = "'".$specialtyObject->getId()."'";
                        $specialtyIds[] = $specialtyObject->getId();
                    }
                }

                if (count($specialtyIds) > 0) {
                    $specialtyStr = "projectSpecialty.id IN (" . implode(",", $specialtyIds) . ")";
                    //echo "specialtyStr=$specialtyStr<br>";
                    $dql->andWhere($specialtyStr);
                } else {
                    $dql->andWhere("projectSpecialty.id IS NULL");
                }
            }
        } //if admin

        $query = $dql->getQuery();

        //echo "projectId=".$project->getId()."<br>";
        //echo "reviewId=".$reviewId."<br>";
        //echo "query=".$query->getSql()."<br>";

        if( count($dqlParameters) > 0 ) {
            $query->setParameters($dqlParameters);
        }

        $projects = $query->getResult();

        return $projects;
    }

    public function copyFormNodeFieldsToProject( $project, $flushDb=true ) {
        $transResFormNodeUtil = $this->container->get('transres_formnode_util');

        //update project's irbExpirationDate
        $projectIrbExpirationDate = $transResFormNodeUtil->getProjectFormNodeFieldByName($project,"IRB Expiration Date");
        if( $projectIrbExpirationDate ) {
            $expDate = date_create_from_format('m/d/Y', $projectIrbExpirationDate);
            $project->setIrbExpirationDate($expDate);
        } else {
            $project->setIrbExpirationDate(null);
        }

        //update project's fundedAccountNumber
        $projectFundedAccountNumber = $transResFormNodeUtil->getProjectFormNodeFieldByName($project,"If funded, please provide account number");
        $project->setFundedAccountNumber($projectFundedAccountNumber);

        //update project's title
        $projectTitle = $transResFormNodeUtil->getProjectFormNodeFieldByName($project,"Title");
        $project->setTitle($projectTitle);

        if( $flushDb ) {
            $this->em->flush($project);
        }
    }
    
    public function getProjectIdsFormNodeByFieldName( $search, $fieldName, $compareType="like" ) {
        $ids = array();
        if( !isset($search) ) {
            //echo "no search=".$search."<br>";
            return $ids;
        }
        //echo "search=".$search."<br>";

        $formNodeUtil = $this->container->get('user_formnode_utility');
        $transResFormNodeUtil = $this->container->get('transres_formnode_util');
        $ids = array();

        //1) get formnode by category type name "Category Type" under formnode "HemePath Translational Research Request"->"Request"
        $fieldFormNode = $transResFormNodeUtil->getFormNodeByFieldNameAndParents(
            $fieldName,
            "HemePath Translational Research",
            "HemePath Translational Research Project",
            "Project"
        );
        //echo "fieldFormNode=".$fieldFormNode->getId()."<br>";
        if( !$fieldFormNode ) {
            return array();
        }

        //2) get objectTypeDropdowns by:
        // value=$categoryType->getId(), entityNamespace="Oleg\TranslationalResearchBundle\Entity" , entityName="TransResRequest"
        $mapper = array(
            "entityName" => "Project",
            "entityNamespace" => "Oleg\\TranslationalResearchBundle\\Entity",
        );
        $objectTypeDropdowns = $formNodeUtil->getFormNodeListRecordsByReceivingObjectValue($fieldFormNode,$search,$mapper,$compareType);
        //echo "objectTypeDropdowns=".count($objectTypeDropdowns)."<br>";

        //3
        foreach($objectTypeDropdowns as $objectTypeDropdown) {
            //echo "id=".$objectTypeDropdown->getEntityId()."<br>";
            $ids[] = $objectTypeDropdown->getEntityId();
        }

        if( count($ids) == 0 ) {
            $ids[] = 0;
        }

        return $ids;
    }

    public function replaceTextByNamingConvention( $text, $project, $transresRequest, $invoice ) {
        if( $project )
            $text = str_replace("[[PROJECT ID]]", $project->getOid(), $text);

        if( $transresRequest )
            $text = str_replace("[[REQUEST ID]]", $transresRequest->getOid(), $text);

        if( $invoice )
            $text = str_replace("[[INVOICE ID]]", $invoice->getOid(), $text);

        return $text;
    }


    //get Issued Invoices
    public function getInvoicesInfosByProject($project) {
        $transresRequestUtil = $this->container->get('transres_request_util');
        $invoicesInfos = array();
        $count = 0;
        $total = 0.00;
        $paid = 0.00;
        $due = 0.00;
        $countRequest = 0;

        foreach($project->getRequests() as $request) {
            $res = $transresRequestUtil->getInvoicesInfosByRequest($request);
            $count = $count + $res['count'];
            $total = $total + $res['total'];
            $paid = $paid + $res['paid'];
            $due = $due + $res['due'];
            $countRequest++;
        }
        //echo $project->getOid().": countRequest=$countRequest: ";

        if( $count > 0 && $countRequest > 0 ) {
            if ($total > 0) {
                $total = $transresRequestUtil->toDecimal($total);
            }
            if ($paid > 0) {
                $paid = $transresRequestUtil->toDecimal($paid);
            }
            if ($due > 0) {
                $due = $transresRequestUtil->toDecimal($due);
            }
            //echo "value<br>";
        } else {
            //echo "total=$total<br>";
            $total = null;
            $paid = null;
            $due = null;
        }
        //echo "total=$total<br>";

        $invoicesInfos['count'] = $count;
        $invoicesInfos['total'] = $total;
        $invoicesInfos['paid'] = $paid;
        $invoicesInfos['due'] = $due;

        return $invoicesInfos;
    }

    public function createProjectListExcel($projectIdsArr) {

        $transresRequestUtil = $this->container->get('transres_request_util');
        $transResFormNodeUtil = $this->container->get('transres_formnode_util');

        $author = $this->container->get('security.token_storage')->getToken()->getUser();
        //$transformer = new DateTimeToStringTransformer(null,null,'d/m/Y');

        $ea = new \PHPExcel(); // ea is short for Excel Application

        $ea->getProperties()
            ->setCreator($author."")
            ->setTitle('Projects')
            ->setLastModifiedBy($author."")
            ->setDescription('Projects list in Excel format')
            ->setSubject('PHP Excel manipulation')
            ->setKeywords('excel php office phpexcel lakers')
            ->setCategory('programming')
        ;

        $ews = $ea->getSheet(0);
        $ews->setTitle('Projects');

        //align all cells to left
        $style = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            )
        );
        //$ews->getDefaultStyle()->applyFromArray($style);
        $ews->getParent()->getDefaultStyle()->applyFromArray($style);

        $styleBoldArray = array(
            'font' => array(
                'bold' => true
            )
        );

        $styleLastRow = array(
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'ebf1de')
            ),
            'borders' => array(
//                'bottom' => array(
//                    'style' => \PHPExcel_Style_Border::BORDER_THIN
//                ),
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $ews->setCellValue('A1', 'Project ID'); // Sets cell 'a1' to value 'ID
        $ews->setCellValue('B1', 'Submission Date');
        $ews->setCellValue('C1', 'Principal Investigator(s)');
        $ews->setCellValue('D1', 'Project Title');
        $ews->setCellValue('E1', 'Funding');
        $ews->setCellValue('F1', 'Status');
        $ews->setCellValue('G1', 'Approval Date');
        $ews->setCellValue('H1', 'IRB Expiration Date');

        $ews->setCellValue('I1', 'Request ID');

        $ews->setCellValue('J1', 'Funding Number');
        $ews->setCellValue('K1', 'Completion Status');

        $ews->setCellValue('L1', 'Invoice(s) Issued');
        $ews->setCellValue('M1', 'Latest Invoice Total($)');
        $ews->setCellValue('N1', 'Latest Invoice Paid($)');
        $ews->setCellValue('O1', 'Latest Invoice Due($)');
        $ews->setCellValue('P1', 'Latest Invoice Comment');

        $ews->getStyle('A1:P1')->applyFromArray($styleBoldArray);

        $totalRequests = 0;
        $totalInvoices = 0;
        $totalTotal = 0;
        $paidTotal = 0;
        $dueTotal = 0;

        $row = 2;
        foreach( $projectIdsArr as $projectId ) {

            $project = $this->em->getRepository('OlegTranslationalResearchBundle:Project')->find($projectId);
            if( !$project ) {
                continue;
            }

            if( $this->isUserAllowedSpecialtyObject($project->getProjectSpecialty()) === false ) {
                continue;
            }

//            $ews->setCellValue('A'.$row, $project->getOid());
//            $ews->setCellValue('B'.$row, $this->convertDateToStr($project->getCreateDate()) );
//
//            $piArr = array();
//            foreach( $project->getPrincipalInvestigators() as $pi) {
//                $piArr[] = $pi->getUsernameOptimal();
//            }
//            $ews->setCellValue('C'.$row, implode("\n",$piArr));
//            $ews->getStyle('C'.$row)->getAlignment()->setWrapText(true);
//
//            $ews->setCellValue('D'.$row, $transResFormNodeUtil->getProjectFormNodeFieldByName($project,"Title"));
//
//            //Funding
//            if( $transResFormNodeUtil->getProjectFormNodeFieldByName($project,"Funded") ) {
//                $funded = "Funded";
//            } else {
//                $funded = "Not Funded";
//            }
//            $ews->setCellValue('E'.$row, $funded);
//
//            //Status
//            $ews->setCellValue('F'.$row, $this->getStateLabelByName($project->getState()));
//
//            //Approval Date
//            $ews->setCellValue('G'.$row, $this->convertDateToStr($project->getApprovalDate()) );
//
//            //IRB Expiration Date
//            $ews->setCellValue('H'.$row, $transResFormNodeUtil->getProjectFormNodeFieldByName($project,"IRB Expiration Date"));
            $ews = $this->fillOutProjectCells($ews,$row,$project);

            $projectRequests = 0;
            $projectTotalInvoices = 0;
            $projectTotalTotal = 0;
            $projectTotalPaid = 0;
            $projectTotalDue = 0;

            foreach($project->getRequests() as $request) {

                $ews = $this->fillOutProjectCells($ews,$row,$project);

                $latestInvoice = $transresRequestUtil->getLatestInvoice($request);

                //Request ID
                $ews->setCellValue('I'.$row, $request->getOid());

                //Funding Number
                $ews->setCellValue('J'.$row, $request->getFundedAccountNumber());

                //Completion Status
                $ews->setCellValue('K'.$row, $transresRequestUtil->getProgressStateLabelByName($request->getProgressState()));

                //Invoice(s) Issued (Latest)
                $latestInvoice = $transresRequestUtil->getLatestInvoice($request);
                //$latestInvoicesCount = count($request->getInvoices());
                $latestInvoicesCount = 0;
                if( $latestInvoice ) {
                    $latestInvoicesCount = 1;
                    $totalInvoices++;
                    $projectTotalInvoices++;
                }
                $ews->setCellValue('L'.$row, $latestInvoicesCount);

                if( $latestInvoice ) {
                    //# Total($)
                    $total = $latestInvoice->getTotal();
                    $totalTotal = $totalTotal + $total;
                    $projectTotalTotal = $projectTotalTotal + $total;
                    if ($total) {
                        $ews->setCellValue('M' . $row, $total);
                    }

                    //# Paid($)
                    $paid = $latestInvoice->getPaid();
                    $paidTotal = $paidTotal + $paid;
                    $projectTotalPaid = $projectTotalPaid + $paid;
                    if ($paid) {
                        $ews->setCellValue('N' . $row, $paid);

                    }

                    //# Due($)
                    $due = $latestInvoice->getDue();
                    $dueTotal = $dueTotal + $due;
                    $projectTotalDue = $projectTotalDue + $due;
                    if ($due) {
                        $ews->setCellValue('O' . $row, $due);
                    }

                    //Comment
                    $comment = $latestInvoice->getComment();
                    if( $comment ) {
                        $ews->setCellValue('P' . $row, $comment);
                        $ews->getStyle('P' . $row)
                            ->getAlignment()->setWrapText(true);
                    }
                }

                $projectRequests = $projectRequests + 1;

                $row = $row + 1;
            }

            $totalRequests = $totalRequests + $projectRequests;

            $ews = $this->fillOutProjectCells($ews,$row,$project);

            //Request Total
            $ews->setCellValue('I'.$row, "Project Totals");
            $ews->getStyle('I'.$row)->applyFromArray($styleBoldArray);

            //This Project Total Invoices
            $ews->setCellValue('L'.$row, $projectTotalInvoices);
            $ews->getStyle('L'.$row)->applyFromArray($styleBoldArray);

            //This Project Total Total
            $ews->setCellValue('M'.$row, $projectTotalTotal);
            $ews->getStyle('M'.$row)->applyFromArray($styleBoldArray);

            //This Project Total Paid
            $ews->setCellValue('N'.$row, $projectTotalPaid);
            $ews->getStyle('N'.$row)->applyFromArray($styleBoldArray);

            //This Project Total Due
            $ews->setCellValue('O'.$row, $projectTotalDue);
            $ews->getStyle('O'.$row)->applyFromArray($styleBoldArray);

            //set color light green to the last Total row
            $ews->getStyle('A'.$row.':'.'P'.$row)->applyFromArray($styleLastRow);

            $row = $row + 1;
        }//project

        //Total
        //$row++;
        $ews->setCellValue('H'.$row, "Totals");
        $ews->getStyle('H'.$row)->applyFromArray($styleBoldArray);
        //Requests total
        $ews->setCellValue('I' . $row, $totalRequests);
        $ews->getStyle('I'.$row)->applyFromArray($styleBoldArray);
        //Invoices total
        $ews->setCellValue('L'.$row, $totalInvoices);
        $ews->getStyle('L'.$row)->applyFromArray($styleBoldArray);
        //Total total
        if( $totalTotal > 0 ) {
            $ews->setCellValue('M' . $row, $totalTotal);
            $ews->getStyle('M'.$row)->applyFromArray($styleBoldArray);
        }
        //Paid total
        if( $paidTotal > 0 ) {
            $ews->setCellValue('N' . $row, $paidTotal);
            $ews->getStyle('N'.$row)->applyFromArray($styleBoldArray);
        }
        //Due total
        if( $dueTotal > 0 ) {
            $ews->setCellValue('O' . $row, $dueTotal);
            $ews->getStyle('O'.$row)->applyFromArray($styleBoldArray);
        }

        //format columns to currency format 2:$row
        $ews->getStyle('M2:M'.$row)
            ->getNumberFormat()
            ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $ews->getStyle('N2:N'.$row)
            ->getNumberFormat()
            ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $ews->getStyle('O2:O'.$row)
            ->getNumberFormat()
            ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

        //exit("ids=".$fellappids);


        // Auto size columns for each worksheet
        \PHPExcel_Shared_Font::setAutoSizeMethod(\PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        foreach ($ea->getWorksheetIterator() as $worksheet) {

            $ea->setActiveSheetIndex($ea->getIndex($worksheet));

            $sheet = $ea->getActiveSheet();
            $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            /** @var PHPExcel_Cell $cell */
            foreach ($cellIterator as $cell) {
                $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
            }
        }


        return $ea;
    }
    public function convertDateToStr($datetime) {
        if( $datetime ) {
            return $datetime->format("m/d/Y");
        }
        return null;
    }
    public function fillOutProjectCells($ews, $row, $project) {
        $transResFormNodeUtil = $this->container->get('transres_formnode_util');

        $ews->setCellValue('A'.$row, $project->getOid());
        $ews->setCellValue('B'.$row, $this->convertDateToStr($project->getCreateDate()) );

        $piArr = array();
        foreach( $project->getPrincipalInvestigators() as $pi) {
            $piArr[] = $pi->getUsernameOptimal();
        }
        $ews->setCellValue('C'.$row, implode("\n",$piArr));
        $ews->getStyle('C'.$row)->getAlignment()->setWrapText(true);

        $projectTitle = $project->getTitle();
        if( !$projectTitle ) {
            //$projectTitle = $transResFormNodeUtil->getProjectFormNodeFieldByName($project,"Title");
            $projectTitle = $project->getTitle();
        }
        $ews->setCellValue('D'.$row, $projectTitle);

        //Funding
        //if( $transResFormNodeUtil->getProjectFormNodeFieldByName($project,"Funded") ) {
        if( $project->getFunded() ) {
            $funded = "Funded";
        } else {
            $funded = "Not Funded";
        }
        $ews->setCellValue('E'.$row, $funded);

        //Status
        $ews->setCellValue('F'.$row, $this->getStateLabelByName($project->getState()));

        //Approval Date
        $ews->setCellValue('G'.$row, $this->convertDateToStr($project->getApprovalDate()) );

        //IRB Expiration Date
        //$ews->setCellValue('H'.$row, $transResFormNodeUtil->getProjectFormNodeFieldByName($project,"IRB Expiration Date"));
        $expDateStr = null;
        if( $project->getIrbExpirationDate() ) {
            $expDateStr = $project->getIrbExpirationDate()->format('m/d/Y');
        }
        $ews->setCellValue('H'.$row, $expDateStr);

        return $ews;
    }

    public function getProjectsIdsStr($projects) {
        $idsArr = array();
        foreach($projects as $project) {
            if( $project->getId() ) {
                $idsArr[] = $project->getId();
            }
        }
        if( count($idsArr) > 0 ) {
            return implode(",",$idsArr);
        }
        return null;
    }

    public function isUserAllowedSpecialtyObject( $specialtyObject, $user=null ) {
        if( !$specialtyObject ) {
            return false;
        }

        if( !$user ) {
            $user = $this->secTokenStorage->getToken()->getUser();
        }

//        $role = null;
//        if( $specialtyObject->getAbbreviation() == "hematopathology" ) {
//            $role = "ROLE_TRANSRES_HEMATOPATHOLOGY";
//        }
//        if( $specialtyObject->getAbbreviation() == "ap-cp" ) {
//            $role = "ROLE_TRANSRES_APCP";
//        }
        $role = $this->getSpecialtyRole($specialtyObject);

        if( $role ) {
            //check security context
            if( $this->secAuth->isGranted($role) ) {
                return true;
            }

            //check user's roles
            if( $user && $user->hasRole($role) ) {
                return true;
            }
        }

        //exception for executive role: check by partial role name
        if( $role == "ROLE_TRANSRES_HEMATOPATHOLOGY" ) {
            $execRole = "ROLE_TRANSRES_EXECUTIVE_HEMATOPATHOLOGY";
            if( $this->secAuth->isGranted($execRole) ) {
                return true;
            }
            if( $user && $user->hasRole($execRole) ) {
                return true;
            }
        }
        if( $role == "ROLE_TRANSRES_APCP" ) {
            $execRole = "ROLE_TRANSRES_EXECUTIVE_APCP";
            if( $this->secAuth->isGranted($execRole) ) {
                return true;
            }
            if( $user && $user->hasRole($execRole) ) {
                return true;
            }
        }

        return false;
    }

    public function getSpecialtyRole($specialtyObject) {
        $role = null;
        if( $specialtyObject->getAbbreviation() == "hematopathology" ) {
            $role = "ROLE_TRANSRES_HEMATOPATHOLOGY";
        }
        if( $specialtyObject->getAbbreviation() == "ap-cp" ) {
            $role = "ROLE_TRANSRES_APCP";
        }
        return $role;
    }

    public function userQueryBuilder() {
        return function(EntityRepository $er) {
            return $er->createQueryBuilder('list')
                ->leftJoin("list.employmentStatus", "employmentStatus")
                ->leftJoin("employmentStatus.employmentType", "employmentType")
                ->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
                //->andWhere("list.roles LIKE '%ROLE_TRANSRES_%'")
                ->leftJoin("list.infos", "infos")
                ->orderBy("infos.displayName","ASC");
        };
    }

    public function getAllowedProjectSpecialty( $user )
    {
        $projectSpecialtyAllowedArr = new ArrayCollection();
        $projectSpecialtyDeniedArr = new ArrayCollection();

        //check is user is hematopathology user
        $specialtyHemaObject = $this->getSpecialtyObject("hematopathology");
        if( $this->isUserAllowedSpecialtyObject($specialtyHemaObject, $user) ) {
            $projectSpecialtyAllowedArr->add($specialtyHemaObject);
        } else {
            $projectSpecialtyDeniedArr->add($specialtyHemaObject);
        }

        //check is user is ap-cp user
        $specialtyAPCPObject = $this->getSpecialtyObject("ap-cp");
        if( $this->isUserAllowedSpecialtyObject($specialtyAPCPObject, $user) ) {
            $projectSpecialtyAllowedArr->add($specialtyAPCPObject);
        } else {
            $projectSpecialtyDeniedArr->add($specialtyAPCPObject);
        }

        $res = array(
            'projectSpecialtyAllowedArr' => $projectSpecialtyAllowedArr,
            'projectSpecialtyDeniedArr' => $projectSpecialtyDeniedArr
        );

        return $res;
    }
    public function getObjectDiff($first_array,$second_array) {
        $diff = array_udiff($first_array, $second_array,
            function ($obj_a, $obj_b) {
                if( $obj_a->getId() == $obj_b->getId() ) {
                    return 0;
                }
                return -1;
            }
        );
        return $diff;
    }
    public function getReturnIndexSpecialtyArray( $projectSpecialtyArr, $project=null, $filterType=null ) {
        $resArr = array();

        $count = 0;
        foreach($projectSpecialtyArr as $projectSpecialty) {
            //echo "spec=".$projectSpecialty."<br>";
            $resArr["filter[projectSpecialty][".$count."]"] = $projectSpecialty->getId();
            $count++;
        }

        if( $project && $project->getId() ) {
            $resArr["filter[project]"] = $project->getId();
        }

        if( $filterType ) {
            $resArr["type"] = $filterType;
            $resArr["title"] = $filterType;
        }

        //print_r($resArr);

        return $resArr;
    }

    //check if user does not have ROLE_TRANSRES_REQUESTER and specialty role
    public function addMinimumRolesToCreateProject( $specialtyObject=null ) {
        $userSecUtil = $this->container->get('user_security_utility');
        $user = $this->secTokenStorage->getToken()->getUser();
        $flushUser = false;
        $roleAddedArr = array();

        if( false == $this->secAuth->isGranted('ROLE_TRANSRES_REQUESTER') ) {
            $user->addRole('ROLE_TRANSRES_REQUESTER');
            $flushUser = true;
            $roleAddedArr[] = "ROLE_TRANSRES_REQUESTER";
        }

        if( $specialtyObject ) {
            $specialtyRole = $this->getSpecialtyRole($specialtyObject);
            if( false == $this->secAuth->isGranted($specialtyRole) ) {
                $user->addRole($specialtyRole);
                $flushUser = true;
                $roleAddedArr[] = $specialtyRole;
            }
            $specialtyStr = $specialtyObject."";
        } else {
            $specialtyStr = null;
        }

        $environment = $userSecUtil->getSiteSettingParameter('environment');
        if( $environment != 'live' ) {
            if( false == $this->secAuth->isGranted('ROLE_TESTER') ) {
                $user->addRole('ROLE_TESTER');
                $flushUser = true;
                $roleAddedArr[] = 'ROLE_TESTER';
            }
        }

        if( $flushUser ) {
            //exit('flush user');
            $this->em->flush($user);

//            $this->container->get('session')->getFlashBag()->add(
//                'warning',
//                "Permission to create a new $specialtyStr project has been automatically granted by the system. Your activities will be recorded."
//            );

            ///////////////// Event Log /////////////////
            $sitename = $this->container->getParameter('translationalresearch.sitename');
            $userSecUtil = $this->container->get('user_security_utility');
            $eventType = "User record updated";
            $eventMsg = "User information of " . $user . " has been automatically changed to be able to access a new $specialtyStr project page" . "<br>";

            if( count($roleAddedArr) > 0 ) {
                $eventMsg = $eventMsg . "Added roles:" . implode(", ", $roleAddedArr);
            }

            $userSecUtil->createUserEditEvent($sitename, $eventMsg, $user, $user, null, $eventType);
            ///////////////// EOF Event Log /////////////////

            return $roleAddedArr;
        }

        return null;
    }

    public function getNumberOfFundedRequests( $project ) {
        $repository = $this->em->getRepository('OlegTranslationalResearchBundle:TransResRequest');
        $dql =  $repository->createQueryBuilder("request");
        $dql->select('request');
        $dql->leftJoin("request.project", "project");

        $dql->where("project.id=:projectId");
        $dql->andWhere('request.fundedAccountNumber IS NOT NULL');

        $parameters = array("projectId"=>$project->getId());

        $query = $dql->getQuery();

        $query->setParameters($parameters);

        $requests = $query->getResult();

        $count = count($requests);

        return $count;
    }

    //$fromStateStr and $toStateStr - state string from workflow (i.e. 'irb_review')
    public function getNotificationMsgByStates( $fromStateStr, $toStateStr, $project ) {
        $transResFormNodeUtil = $this->container->get('transres_formnode_util');

        $msg = null;

        $id = $project->getOid();

        //$title = $transResFormNodeUtil->getProjectFormNodeFieldByName($project,"Title");
        $title = $project->getTitle();
        //if( !$title ) {
        //    $title = $transResFormNodeUtil->getProjectFormNodeFieldByName($project,"Title");
        //}

        $fromLabel = $this->getStateSimpleLabelByName($fromStateStr);
        $toLabel = $this->getStateSimpleLabelByName($toStateStr);

        //Case1: irb_review -> admin_review
        //Project ID [xxx] "[project title]" has successfully passed the "IRB review" stage and is now awaiting "Admin review".
        if( strpos($fromStateStr, "_review") !== false && strpos($toStateStr, "_review") !== false ) {
            $msg = "Project ID $id '".$title."' has successfully passed the '".$fromLabel."' stage and is now awaiting '".$toLabel."'.";
        }

        //Case2: final_review -> final_approved
        //Project ID [xxx] "[project title]" has successfully passed all stages of review and received final approval.
        if( strpos($fromStateStr, "_review") !== false && strpos($toStateStr, "_approved") !== false ) {
            $msg = "Project ID $id '".$title."' has successfully passed all stages of review and received final approval.";
        }

        //Case3: irb_review -> irb_rejected
        if( strpos($fromStateStr, "_review") !== false && strpos($toStateStr, "_rejected") !== false ) {
            $msg = "Project ID $id '".$title."' has been rejected as a result of '".$fromLabel."'.";
        }

        //Case4: irb_review -> irb_missinginfo
        if( strpos($fromStateStr, "_review") !== false && strpos($toStateStr, "_missinginfo") !== false ) {
            $msg = "Additional information has been requested for the project with ID $id '".$title."' for the '".$fromLabel."' stage.";
        }

        //Case5: irb_missinginfo -> irb_review
        if( strpos($fromStateStr, "_missinginfo") !== false && strpos($toStateStr, "_review") !== false ) {
            $msg = "Project ID $id '".$title."' has been re-submitted for '".$toLabel."' stage.";
        }

        if( !$msg ) {
            $msg = "The status of the project ID $id '".$title."' has been changed from '".$fromLabel."' to '".$toLabel."'.";
        }

        return $msg;
    }
}