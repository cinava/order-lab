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
 * User: oli2002
 * Date: 10/16/14
 * Time: 9:55 AM
 */

namespace Oleg\UserdirectoryBundle\Services;

use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\CommentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FosCommentListener implements EventSubscriberInterface {


    private $container;
    private $em;
    protected $secTokenStorage;

    protected $secAuth;

    public function __construct( $container, $secTokenStorage, $em )
    {
        $this->container = $container;
        $this->em = $em;

        $this->secTokenStorage = $secTokenStorage;  //$container->get('security.token_storage'); //$user = $this->secTokenStorage->getToken()->getUser();
        $this->secAuth = $container->get('security.authorization_checker'); //$this->secAuth->isGranted("ROLE_USER")
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            //Events::COMMENT_PRE_PERSIST => 'onCommentPrePersistTest',
            //Events::COMMENT_POST_PERSIST => 'onCommentPostPersistTest',
        );
    }

    public function onCommentPrePersist(CommentEvent $event)
    {
        $comment = $event->getComment();
        $entity = $this->getEntityFromComment($comment);

        $authorTypeArr = $this->getAuthorType($entity);
        if( $authorTypeArr && count($authorTypeArr) > 0 ) {
            $comment->setAuthorType($authorTypeArr['type']);
            $comment->setAuthorTypeDescription($authorTypeArr['description']);
        }

        //$this->sendEmails($event,$comment,$entity);

        //set only eventlog
        //$this->setCommentEventLog($event,$comment,$entity);
    }

    public function onCommentPostPersist(CommentEvent $event)
    {
        $comment = $event->getComment();
        $entity = $this->getEntityFromComment($comment);

        //set only eventlog
        $resArr = $this->setCommentEventLog($event,$comment,$entity);

        //send only emails (Comment takes lots of time - couple seconds delay)
        //$this->sendCommentEmails($comment,$entity,$resArr);
    }



    public function setCommentEventLog(CommentEvent $event, $comment=null, $entity=null) {
        $transresUtil = $this->container->get('transres_util');

        if( !$comment ) {
            $comment = $event->getComment();
        }

        if( !$entity ) {
            $entity = $this->getEntityFromComment($comment);
        }

        $eventType = "Comment Posted";
        if( $entity->getEntityName() == "Project" ) {
            $eventType = "Project Comment Posted";
            $stateStr = $this->getStateStrFromComment($comment);
            $stateLabel = $transresUtil->getStateLabelByName($stateStr);
        }

        if( $entity->getEntityName() == "Request" ) {
            $eventType = "Request Comment Posted";
            $stateLabel = null;
        }

        $resArr = $this->getMsgSubjectAndBody($comment,$entity,$stateLabel);
        $body = $resArr['body'];
        $transresUtil->setEventLog($entity,$eventType,$body);

        return $resArr;
    }

    public function sendCommentEmails($comment, $entity, $resArr) {
        if( $entity->getEntityName() == "Project" ) {
            $this->sendCommentProjectEmails($comment, $entity, $resArr);
        }

        if( $entity->getEntityName() == "Request" ) {
            $this->sendCommentRequestEmails($comment, $entity, $resArr);
        }
    }

    public function sendCommentProjectEmails($comment=null, $entity=null, $resArr=null)
    {
        $transresUtil = $this->container->get('transres_util');

        if( !$entity ) {
            $entity = $this->getEntityFromComment($comment);
        }

        //send email to all entity related users: admin, primary, requesters, reviewers of this review type.
        $emails = array();

        $stateStr = $this->getStateStrFromComment($comment);
        $reviews = $transresUtil->getReviewsByProjectAndState($entity,$stateStr);

        //1) admins
        $adminEmails = $transresUtil->getTransResAdminEmails();
        $emails = array_merge($emails,$adminEmails);

        //2) reviewers of this review
        foreach($reviews as $review) {
            $reviewerEmails = $transresUtil->getCurrentReviewersEmails($review);
            $emails = array_merge($emails,$reviewerEmails);
        }

        //3) requesters
        $requesterEmails = $transresUtil->getRequesterEmails($entity);
        $emails = array_merge($emails,$requesterEmails);

        $emails = array_unique($emails);

        $senderEmail = null; //Admin email

        $break = "\r\n";

        if( !$resArr ) {
            $stateLabel = $transresUtil->getStateLabelByName($stateStr);
            $resArr = $this->getMsgSubjectAndBody($comment,$entity,$stateLabel);
        }

        $subject = $resArr['subject'];
        $body = $resArr['body'];

        //get entity url
        $entityUrl = $transresUtil->getProjectShowUrl($entity);
        $body = $body . $break . $break . "Please click on the URL below to view this ".$entity->getEntityName().":" . $break . $entityUrl;

        $emailUtil = $this->container->get('user_mailer_utility');
        $emailUtil->sendEmail( $emails, $subject, $body, null, $senderEmail );
    }

    public function sendCommentRequestEmails($comment, $entity, $resArr) {
        $transresUtil = $this->container->get('transres_util');

        if( !$entity ) {
            $entity = $this->getEntityFromComment($comment);
        }

        //send email to all entity related users: admin, primary, requesters, reviewers of this review type.
        $emails = array();

        //1) admins
        $adminEmails = $transresUtil->getTransResAdminEmails();
        $emails = array_merge($emails,$adminEmails);

        //2) contact
        $contact = $entity->getContact();
        if( $contact ) {
            $contactEmail = $contact->getSingleEmail();
            if( $contactEmail ) {
                $emails = array_merge($emails,array($emails));
            }
        }

        //3) principalInvestigators
        $piEmailArr = array();
        $pis = $entity->getPrincipalInvestigators();
        foreach( $pis as $pi ) {
            if( $pi ) {
                $piEmailArr[] = $pi->getSingleEmail();
            }
        }
        $emails = array_merge($emails,$piEmailArr);

        $emails = array_unique($emails);

        $senderEmail = null; //Admin email

        $break = "\r\n";

        if( !$resArr ) {
            //$stateStr = $this->getStateStrFromComment($comment);
            //$stateStr = $comment->getThread()->getId();
            //$stateLabel = $transresUtil->getStateLabelByName($stateStr);
            //$transresRequestUtil = $this->container->get('transres_request_util');
            //$stateLabel = $transresRequestUtil->getRequestLabelByStateMachineType();
            //$stateLabel = $transresRequestUtil->getProgressStateLabelByName($stateStr);
            $resArr = $this->getMsgSubjectAndBody($comment,$entity);
        }

        $subject = $resArr['subject'];
        $body = $resArr['body'];

        //get entity url
        $entityUrl = $transresUtil->getProjectShowUrl($entity);
        $body = $body . $break . $break . "Please click on the URL below to view this ".$entity->getEntityName().":" . $break . $entityUrl;

        $emailUtil = $this->container->get('user_mailer_utility');
        $emailUtil->sendEmail( $emails, $subject, $body, null, $senderEmail );
    }

    public function getMsgSubjectAndBody($comment,$entity,$stateLabel=null) {
        $break = "\r\n";
        if( $stateLabel ) {
            $stateLabel = " for the stage '".$stateLabel."'";
        }
        $subject = "New Comment for ".$entity->getEntityName()." ID ".$entity->getOid()." has been posted".$stateLabel;
        $body = $subject . ":" . $break . $comment->getBody();

        return array('subject'=>$subject, 'body'=>$body);
    }

    public function getAuthorType( $entity ) {

        if( !$this->secTokenStorage->getToken() ) {
            //not authenticated
            return null;
        }

        $authorTypeArr = array();

        if( $this->secAuth->isGranted('ROLE_TRANSRES_ADMIN') ) {
            //$authorType = "Administrator";
            $authorTypeArr['type'] = "Administrator";
            $authorTypeArr['description'] = "Administrator";
            return $authorTypeArr;
        }
        if( $this->secAuth->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER') ) {
            //$authorType = "Primary Reviewer";
            $authorTypeArr['type'] = "Administrator";
            $authorTypeArr['description'] = "Primary Reviewer";
            return $authorTypeArr;
        }
        if( $this->secAuth->isGranted('ROLE_TRANSRES_PRIMARY_REVIEWER_DELEGATE') ) {
            //$comment->setAuthorType("Primary Reviewer Delegate");
            //$authorType = "Primary Reviewer";
            $authorTypeArr['type'] = "Administrator";
            $authorTypeArr['description'] = "Primary Reviewer";
            return $authorTypeArr;
        }

        //if not found
        $transresUtil = $this->container->get('transres_util');
        $user = $this->secTokenStorage->getToken()->getUser();

        //1) check if the user is entity requester
        //$entity = $this->getEntityFromComment($comment);
        if( !$entity ) {
            return null;
        }

        //check if reviewer
        if( $transresUtil->isProjectReviewer($entity) ) {
            //return "Reviewer";
            $authorTypeArr['type'] = "Reviewer";
            $authorTypeArr['description'] = "Reviewer";
            return $authorTypeArr;
        }

        if( $entity->getEntityName() == "Project" ) {
            return $this->getProjectRequesterAuthorType($entity,$user);
        }

        if( $entity->getEntityName() == "Request" ) {
            return $this->getRequestRequesterAuthorType($entity,$user);
        }

        return null;
    }
    public function getProjectRequesterAuthorType( $entity, $user ) {

        //check if requester
        if( $entity->getSubmitter() && $entity->getSubmitter()->getId() == $user->getId() ) {
            //return "Submitter";
            $authorTypeArr['type'] = "Requester";
            $authorTypeArr['description'] = "Submitter";
            return $authorTypeArr;
        }
        if( $entity->getPrincipalInvestigators()->contains($user) ) {
            //return "Principal Investigator";
            $authorTypeArr['type'] = "Requester";
            $authorTypeArr['description'] = "Principal Investigator";
            return $authorTypeArr;
        }
        if( $entity->getCoInvestigators()->contains($user) ) {
            //return "Co-Investigator";
            $authorTypeArr['type'] = "Requester";
            $authorTypeArr['description'] = "Co-Investigator";
            return $authorTypeArr;
        }
        if( $entity->getPathologists()->contains($user) ) {
            //return "Pathologist";
            $authorTypeArr['type'] = "Requester";
            $authorTypeArr['description'] = "Pathologist";
            return $authorTypeArr;
        }
        if( $entity->getContacts()->contains($user) ) {
            //return "Contact";
            $authorTypeArr['type'] = "Requester";
            $authorTypeArr['description'] = "Contact";
            return $authorTypeArr;
        }
        if( $entity->getBillingContacts()->contains($user) ) {
            //return "Billing Contact";
            $authorTypeArr['type'] = "Requester";
            $authorTypeArr['description'] = "Billing Contact";
            return $authorTypeArr;
        }

        return null;
    }
    public function getRequestRequesterAuthorType( $entity, $user ) {
        //check if requester
        if( $entity->getSubmitter() && $entity->getSubmitter()->getId() == $user->getId() ) {
            //return "Submitter";
            $authorTypeArr['type'] = "Requester";
            $authorTypeArr['description'] = "Submitter";
            return $authorTypeArr;
        }
        if( $entity->getPrincipalInvestigators()->contains($user) ) {
            //return "Principal Investigator";
            $authorTypeArr['type'] = "Requester";
            $authorTypeArr['description'] = "Principal Investigator";
            return $authorTypeArr;
        }
        if( $entity->getContacts()->contains($user) ) {
            //return "Contact";
            $authorTypeArr['type'] = "Requester";
            $authorTypeArr['description'] = "Contact";
            return $authorTypeArr;
        }

        return null;
    }

    //http://localhost/order/api/threads/transres-request-20-billing/comments
    public function getEntityFromComment($comment) {
        $entity = null;
        //get entity from "transres-request-20-billing"
        $threadId = $comment->getThread()->getId();
        $idArr = explode("-",$threadId);

        $entityId = null;
        //$stateStr = null;
        if( count($idArr) >= 4 ) {
            $entitySitename = $idArr[0]; //sitename
            $entityName = $idArr[1]; //entity name
            $entityId = $idArr[2]; //entity id
            //$stateStr = $idArr[1];  //irb_review
        }

        if( $entitySitename == "transres" ) {
            $bundleName = 'OlegTranslationalResearchBundle';
        }

        if( $entityName == "Request" ) {
            $entityName = "TransResRequest";
        }
        //exit("Find entity by ID=".$entityId."; namespace=".$bundleName.':'.$entityName);

        if( $bundleName && $entityId ) {
            $entity = $this->em->getRepository($bundleName.':'.$entityName)->find($entityId);
        }

//        if( !$entity ) {
//            exit("No entity found by ID=".$entityId."; namespace=".$bundleName.':'.$entityName);
//        }

        return $entity;
    }

    public function getStateStrFromComment($comment) {
        //$logger = $this->container->get('logger');
        //$entity = null;
        //get state from "transres-request-20-billing" or "transres-Project-9-irb_review"
        $threadId = $comment->getThread()->getId();
        //echo "threadId=".$threadId."<br>";
        //$logger->notice("threadId=".$threadId);
        $idArr = explode("-",$threadId);

        $stateStr = null;
        if( count($idArr) >= 4 ) {
            $stateStr = $idArr[3];  //irb_review
        }

        if( !$stateStr ) {
            throw new \Exception('State not found by threadId='.$threadId);
        }

        return $stateStr;
    }


} 