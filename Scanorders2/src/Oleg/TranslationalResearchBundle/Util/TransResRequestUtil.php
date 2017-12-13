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
use Oleg\TranslationalResearchBundle\Entity\InvoiceItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 8/25/2017
 * Time: 09:48 AM
 */
class TransResRequestUtil
{

    protected $container;
    protected $em;
    protected $secTokenStorage;
    protected $secAuth;

    public function __construct( $em, $container ) {
        $this->container = $container;
        $this->em = $em;
        $this->secAuth = $container->get('security.authorization_checker'); //$this->secAuth->isGranted("ROLE_USER")
        $this->secTokenStorage = $container->get('security.token_storage'); //$user = $this->secTokenStorage->getToken()->getUser();
    }


    public function getTransResRequestTotalFeeHtml( $project ) {

        //$transResFormNodeUtil = $this->container->get('transres_formnode_util');
        $repository = $this->em->getRepository('OlegTranslationalResearchBundle:TransResRequest');
        $dql =  $repository->createQueryBuilder("transresRequest");
        $dql->select('transresRequest');

        $dql->leftJoin('transresRequest.submitter','submitter');
        $dql->leftJoin('transresRequest.project','project');
        $dql->leftJoin('submitter.infos','submitterInfos');

        $dqlParameters = array();

        $dql->andWhere("project.id = :projectId");

        $dqlParameters["projectId"] = $project->getId();

        $query = $this->em->createQuery($dql);

        if( count($dqlParameters) > 0 ) {
            $query->setParameters($dqlParameters);
        }

        $requests = $query->getResult();

        $total = 0;

        foreach($requests as $request) {
            $subTotal = $this->getTransResRequestFeeHtml($request);
            if( $subTotal ) {
                $total = $total + $subTotal;
            }
        }

        //echo "total=".$total."<br>";
        if( $total ) {
            $res = "Total fees: $".$total;
            return $res;
        }

        return null;
    }

    public function getTransResRequestFeeHtml( $request ) {
        $subTotal = 0;

        foreach($request->getProducts() as $product) {
            $requested = $product->getRequested();
            $completed = $product->getCompleted();
            $category = $product->getCategory();
            //echo "requested=$requested <br>";
            $fee = 0;
            $units = 0;
            if( $category ) {
                $fee = $category->getFee();
            }
            if( $requested ) {
                $units = intval($requested);
            }
            if( $completed ) {
                $units = intval($completed);
            }
            //echo "units=$units; fee=$fee <br>";
            if( $fee && $units ) {
                $subTotal = $subTotal + ($units * intval($fee));
            }
        }

        return $subTotal;
    }

    //TODO: modify for multiple sections
    public function getTransResRequestFormnodeFeeHtml( $request ) {

        $transResFormNodeUtil = $this->container->get('transres_formnode_util');
        $formNodeUtil = $this->container->get('user_formnode_utility');

        $completedEntities = $transResFormNodeUtil->getProjectFormNodeFieldByName(
            $request,
            "Completed #",
            "HemePath Translational Research Request",
            "Request",
            "Product or Service",
            null,
            true
        );
        //echo "completedEntities=".count($completedEntities)."<br>";
//        $formNodeValues = $completedEntities['formNodeValue'];
//        foreach($formNodeValues as $resArr) {
//            $formNodeValue = $resArr['formNodeValue'];
//            echo "formNodeValue=".$formNodeValue."<br>";
//            $arraySectionIndex = $resArr['arraySectionIndex'];
//            echo "arraySectionIndex=" . $arraySectionIndex . "<br>";
//        }
//        return 1;

        $requestedEntities = $transResFormNodeUtil->getProjectFormNodeFieldByName(
            $request,
            "Requested #",
            "HemePath Translational Research Request",
            "Request",
            "Product or Service",
            null,
            true
        );
        //echo "requestedEntities=".count($requestedEntities)."<br>";

        $requestCategoryTypeComplexResults = $this->getMultipleProjectFormNodeFieldByName(
            $request,
            "Category Type",
            "HemePath Translational Research Request",
            "Request",
            "Product or Service"
        );
        //echo "requestCategoryTypeComplexResults=".count($requestCategoryTypeComplexResults)."<br>";
//        $res = array(
//            'formNodeValue' => $formNodeValue,
//            'formNodeId' => $formNode->getId(),
//            'arraySectionId' => $result->getArraySectionId(),
//            'arraySectionIndex' => $result->getArraySectionIndex(),
//        );
//        $resArr[] = $res;

        $subTotal = 0;

        //2) group by arraySectionIndex
        foreach($requestCategoryTypeComplexResults as $complexRes) {

            $arraySectionIndex = $complexRes['arraySectionIndex'];
            //echo "arraySectionIndex=".$arraySectionIndex."<br>";
            $dropdownObject = $complexRes['dropdownObject'];

            $requested = $this->findByArraySectionIndex($requestedEntities,$arraySectionIndex);
            //echo "requested=".$requested."<br>";
            $completed = $this->findByArraySectionIndex($completedEntities,$arraySectionIndex);
            //echo "completed=".$completed."<br>";
            //echo "###<br>";

            $fee = $dropdownObject->getFee();

            if( $fee ) {
                $subTotal = $subTotal + intval($completed) * intval($fee);
                //return $subTotal;
            }
        }

        return $subTotal;
    }
    public function findByArraySectionIndex($entities, $arraySectionIndex) {
//        foreach($entities as $entity) {
////            if( $entity->getArraySectionIndex() == $arraySectionIndex ) {
////                return $entity;
////            }
//        }
        $formNodeValues = $entities['formNodeValue'];
        if( !is_array($formNodeValues) ) {
            return null;
        }
        foreach($formNodeValues as $resArr) {
            $formNodeValue = $resArr['formNodeValue'];
            //echo "formNodeValue=".$formNodeValue."<br>";
            $thisArraySectionIndex = $resArr['arraySectionIndex'];
            //echo "arraySectionIndex=" . $arraySectionIndex . "<br>";
            if( $thisArraySectionIndex == $arraySectionIndex ) {
                return $formNodeValue;
            }
        }
        return null;
    }
    public function getMultipleProjectFormNodeFieldByName(
        $entity,
        $fieldName,
        $parentNameStr = "HemePath Translational Research",
        $formNameStr = "HemePath Translational Research Project",
        $entityFormNodeSectionStr = "Project"
    )
    {
        $transResFormNodeUtil = $this->container->get('transres_formnode_util');
        $formNodeUtil = $this->container->get('user_formnode_utility');

        $value = null;
        $receivingEntity = null;

        //1) get FormNode by fieldName
        //echo "getting formnode <br>";
        $fieldFormNode = $transResFormNodeUtil->getFormNodeByFieldNameAndParents($fieldName, $parentNameStr, $formNameStr, $entityFormNodeSectionStr);

        //2) get field for this particular project
        $class = new \ReflectionClass($entity);
        $className = $class->getShortName();
        $classNamespace = $class->getNamespaceName();
        $entityMapper = array(
            'entityNamespace' => $classNamespace,   //"Oleg\\TranslationalResearchBundle\\Entity",
            'entityName' => $className, //"Project",
            'entityId' => $entity->getId(),
        );

        $results = $formNodeUtil->getFormNodeValueByFormnodeAndReceivingmapper($fieldFormNode,$entityMapper,true);

        $resArr = array();
        foreach( $results as $result ) {
            $arraySectionIndex = $result->getArraySectionIndex();
            //echo "result ID= ".$result->getId()." <br>";
            //$formNodeValue = $formNodeUtil->processFormNodeValue($fieldFormNode,$result,$result->getValue(),true);
            //echo "formNodeValue= $formNodeValue <br>";
            //$dropdownObject = $formNodeUtil->getReceivingObject($fieldFormNode,$result->getId());
            //echo "dropdownObject ID= ".$dropdownObject->getId()." <br>";
            $dropdownObject = $this->em->getRepository('OlegTranslationalResearchBundle:RequestCategoryTypeList')->find($result->getValue());
            //echo "category=".$dropdownObject."<br>";
            $thisRes = array(
                'arraySectionIndex'=>$arraySectionIndex,
                'dropdownObject'=>$dropdownObject
            );
            $resArr[] = $thisRes;
        }

        return $resArr;
    }
    public function getSingleTransResRequestFeeHtml_OLD( $request ) {

        $transResFormNodeUtil = $this->container->get('transres_formnode_util');

        $completed = $transResFormNodeUtil->getProjectFormNodeFieldByName(
            $request,
            "Completed #",
            "HemePath Translational Research",
            "HemePath Translational Research Request",
            "Request",
            false
        );

        //
        $completed = str_replace(" ","",$completed);

        if( !$completed ) {
            $completed = $transResFormNodeUtil->getProjectFormNodeFieldByName(
                $request,
                "Requested #",
                "HemePath Translational Research",
                "HemePath Translational Research Request",
                "Request",
                false
            );
        }

        $completed = str_replace(" ","",$completed);
        //echo "completed=".$completed."<br>";

        $requestCategoryTypeDropdownObject = $transResFormNodeUtil->getProjectFormNodeFieldByName(
            $request,
            "Category Type",
            "HemePath Translational Research",
            "HemePath Translational Research Request",
            "Request",
            true
        );

        if( $completed && $requestCategoryTypeDropdownObject ) {
            //echo "requestCategoryTypeDropdownObject=".$requestCategoryTypeDropdownObject."<br>";
            //echo "requestCategoryType feeUnit=".$requestCategoryType->getFeeUnit()."<br>";
            //echo "requestCategoryType fee=".$requestCategoryType->getFee()."<br>";

            $fee = $requestCategoryTypeDropdownObject->getFee();

            if( $fee ) {
                $subTotal = intval($completed) * intval($fee);
                return $subTotal;
            }
        }

        return null;
    }

    public function getProgressStateArr() {
        $stateArr = array(
            'active',
            'canceled',
            'investigator',
            'histo',
            'ihc',
            'mol',
            'retrieval',
            'payment',
            'slidescanning',
            'block',
            'suspended',
            'other',
            'completed'
        );

        $stateChoiceArr = array();

        foreach($stateArr as $state) {
            //$label = $state;
            $label = $this->getProgressStateLabelByName($state);
            $label = $label . " (" . $state . ")";
            $stateChoiceArr[$label] = $state;
        }

        return $stateChoiceArr;
    }


    public function getBillingStateArr() {
        $stateArr = array(
            'active',
            'canceled',
            'missinginfo',
            'invoiced',
            'paid',
            'refunded',
            'partiallyRefunded',
        );

        $stateChoiceArr = array();

        foreach($stateArr as $state) {
            //$label = $state;
            $label = $this->getBillingStateLabelByName($state);
            $label = $label . " (" . $state . ")";
            $stateChoiceArr[$label] = $state;
        }

        return $stateChoiceArr;
    }


    public function getProgressStateLabelByName( $stateName ) {
        switch ($stateName) {
            case "draft":
                $state = "Draft";
                break;
            case "active":
                $state = "Active";
                break;
            case "canceled":
                $state = "Canceled";
                break;
            case "investigator":
                $state = "Investigator";
                break;
            case "histo":
                $state = "Histo";
                break;
            case "ihc":
                $state = "Ihc";
                break;
            case "mol":
                $state = "Mol";
                break;
            case "retrieval":
                $state = "Retrieval";
                break;
            case "payment":
                $state = "Payment";
                break;
            case "slidescanning":
                $state = "Slide Scanning";
                break;
            case "block":
                $state = "Block";
                break;
            case "suspended":
                $state = "Suspended";
                break;
            case "other":
                $state = "Other";
                break;
            case "completed":
                $state = "Completed";
                break;

            default:
                $state = "<$stateName>";

        }
        return $state;
    }
    public function getBillingStateLabelByName( $stateName ) {
        switch ($stateName) {
            case "draft":
                $state = "Draft";
                break;
            case "active":
                $state = "Active";
                break;
            case "canceled":
                $state = "Canceled";
                break;
            case "missinginfo":
                $state = "Pending additional information from submitter";
                break;
            case "invoiced":
                $state = "Invoiced";
                break;
            case "paid":
                $state = "Paid";
                break;
            case "refunded":
                $state = "Refunded";
                break;
            case "partiallyRefunded":
                $state = "Partially Refunded";
                break;

            default:
                $state = "<$stateName>";

        }
        return $state;
    }
    public function getRequestStateLabelByName( $stateName, $statMachineType ) {
        if( $statMachineType == 'progress' ) {
            return $this->getProgressStateLabelByName($stateName);
        }
        if( $statMachineType == 'billing' ) {
            return $this->getBillingStateLabelByName($stateName);
        }
        return "<".$stateName.">";
    }

    public function getHtmlClassTransition( $stateStr ) {
        return "btn btn-success transres-review-submit";
    }

    //get Request IDs for specified RequestCategoryTypeList
    public function getRequestIdsFormNodeByCategory( $categoryType ) {

        if( !$categoryType ) {
            return array();
        }
        //echo $categoryType->getId().": categoryType=".$categoryType->getOptimalAbbreviationName()."<br>";

        $formNodeUtil = $this->container->get('user_formnode_utility');
        $transResFormNodeUtil = $this->container->get('transres_formnode_util');
        $ids = array();

        //1) get formnode by category type name "Category Type" under formnode "HemePath Translational Research Request"->"Request"
//        $fieldFormNode = $transResFormNodeUtil->getFormNodeByFieldNameAndParents(
//            "Category Type",
//            "HemePath Translational Research",
//            "HemePath Translational Research Request",
//            "Request" //Product or Service
//        );
        $fieldFormNode = $transResFormNodeUtil->getFormNodeByFieldNameAndParents(
            "Category Type",
            "HemePath Translational Research Request",
            "Product or Service"
        );
        //echo "fieldFormNode=".$fieldFormNode->getId()."<br>";
        if( !$fieldFormNode ) {
            return array();
        }

        //2) get objectTypeDropdowns by:
        // value=$categoryType->getId(), entityNamespace="Oleg\TranslationalResearchBundle\Entity" , entityName="TransResRequest"
        $mapper = array(
            "entityName" => "TransResRequest",
            "entityNamespace" => "Oleg\\TranslationalResearchBundle\\Entity",
        );
        $objectTypeDropdowns = $formNodeUtil->getFormNodeListRecordsByReceivingObjectValue($fieldFormNode,$categoryType->getId(),$mapper,"exact");
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

    public function getRequestIdsFormNodeByComment( $commentStr ) {

        if( !$commentStr ) {
            return array();
        }
        //echo "commentStr=".$commentStr."<br>";

        $formNodeUtil = $this->container->get('user_formnode_utility');
        $transResFormNodeUtil = $this->container->get('transres_formnode_util');
        $ids = array();
        $objectTypeDropdowns = array();

        //1) get formnode by category type name "Category Type" under formnode "HemePath Translational Research Request"->"Request"
//        $fieldFormNode = $transResFormNodeUtil->getFormNodeByFieldNameAndParents(
//            "Comment",
//            "HemePath Translational Research",
//            "HemePath Translational Research Request",
//            "Request"
//        );
        $fieldFormNode = $transResFormNodeUtil->getFormNodeByFieldNameAndParents(
            "Comment",
            "HemePath Translational Research Request",
            "Product or Service"
        );
        //echo "fieldFormNode=".$fieldFormNode->getId()."<br>";
        if( !$fieldFormNode ) {
            return array();
        }

        //2) get objectTypeDropdowns by:
        // value=$categoryType->getId(), entityNamespace="Oleg\TranslationalResearchBundle\Entity" , entityName="TransResRequest"
        $mapper = array(
            "entityName" => "TransResRequest",
            "entityNamespace" => "Oleg\\TranslationalResearchBundle\\Entity",
        );
        $objectTypeDropdowns = $formNodeUtil->getFormNodeListRecordsByReceivingObjectValue($fieldFormNode,$commentStr,$mapper,"like");
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

    public function isRequestProgressReviewer($transresRequest) {

        return true;
    }

    public function isRequestBillingReviewer($transresRequest) {

        return true;
    }

    public function isRequestProgressReviewable($transresRequest) {
        return $this->isRequestReviewableByRequestAndStateMachineType($transresRequest,"progress");
    }
    public function isRequestBillingReviewable($transresRequest) {
        return $this->isRequestReviewableByRequestAndStateMachineType($transresRequest,"billing");
    }
    public function isRequestReviewableByRequestAndStateMachineType( $transresRequest, $statMachineType ) {
        $workflow = $this->getWorkflowByStateMachineType($statMachineType);
        $transitions = $workflow->getEnabledTransitions($transresRequest);
        foreach( $transitions as $transition ) {
            $tos = $transition->getTos();
            if( count($tos) > 0 ) {
                return true;
            }
        }
        return false;
    }

    public function isRequestCanBeCreated( $project ) {
        $transresUtil = $this->container->get('transres_util');
        $transResFormNodeUtil = $this->container->get('transres_formnode_util');

        //1) is_granted('ROLE_TRANSRES_REQUESTER')
        if( $this->secAuth->isGranted('ROLE_TRANSRES_REQUESTER') === false && $transresUtil->isAdminOrPrimaryReviewer() === false ) {
            return -1;
        }

        //2) project.state == "final_approved"
        if( $project->getState() != "final_approved" ) {
            return -2;
        }

        //3) Request can not be submitted for the expired project
        if( $project->getIrbExpirationDate() ) {
            //use simple project's field
            $expDate = $project->getIrbExpirationDate();
        } else {
            //use formnode project's field if the simple field is null
            $expirationDate = $transResFormNodeUtil->getProjectFormNodeFieldByName($project, "IRB Expiration Date");
            //echo "expirationDate=$expirationDate<br>";
            $expDate = date_create_from_format('m/d/Y', $expirationDate);
            //echo "exp_date=".$expDate->format("d-m-Y H:i:s")."<br>";
        }
        if( new \DateTime() > $expDate ) {
            //echo "expired<br>";
            return -3;
        }
        //echo "not expired<br>";

        return 1;
    }

    public function getReviewEnabledLinkActions( $transresRequest, $statMachineType ) {
        //exit("get review links");
        $transresUtil = $this->container->get('transres_util');
        $project = $transresRequest->getProject();
        $user = $this->secTokenStorage->getToken()->getUser();

        $links = array();

        ////////// Check permission //////////
        $verified = false;
        if( $statMachineType == 'progress' ) {
            if( $transresUtil->isAdminOrPrimaryReviewer() === false && $this->isRequestProgressReviewer($transresRequest) === false ) {
                exit("return: progress not allowed");
                return $links;
            }
            $workflow = $this->container->get('state_machine.transres_request_progress');
            $transitions = $workflow->getEnabledTransitions($transresRequest);
            $verified = true;
        }
        if( $statMachineType == 'billing' ) {
            if( $transresUtil->isAdminOrPrimaryReviewer() === false && $this->isRequestBillingReviewer($transresRequest) === false ) {
                exit("return: billing not allowed");
                return $links;
            }
            $workflow = $this->container->get('state_machine.transres_request_billing');
            $transitions = $workflow->getEnabledTransitions($transresRequest);
            $verified = true;
        }
        if( $verified == false ) {
            return $links;
        }
        ////////// EOF Check permission //////////

        foreach( $transitions as $transition ) {

            //$this->printTransition($transition);
            $transitionName = $transition->getName();
            //echo "transitionName=".$transitionName."<br>";

//            if( false === $this->isUserAllowedFromThisStateByProjectOrReview($project,$review) ) {
//                continue;
//            }

            $tos = $transition->getTos();
            //$froms = $transition->getFroms();
            foreach( $tos as $to ) {
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
                    'translationalresearch_request_transition_action_by_review',
                    array(
                        'transitionName'=>$transitionName,
                        'id'=>$transresRequest->getId(),
                        'statMachineType'=>$statMachineType
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                //$thisUrl = "#";

                //$label = ucfirst($transitionName)." (mark as ".ucfirst($to);
                $label = $this->getRequestStateLabelByName($to,$statMachineType);

                $classTransition = $this->getHtmlClassTransition($transitionName);

                $generalDataConfirmation = "general-data-confirm='Are you sure you want to $label?'";

                //don't show confirmation modal
//                if( strpos($transitionName, "missinginfo") !== false ) {
//                    $generalDataConfirmation = "";
//                }

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



    //change transition (by the $transitionName) of the project
    public function setRequestTransition( $transresRequest, $statMachineType, $transitionName, $to, $testing ) {

        if( !$transresRequest ) {
            throw $this->createNotFoundException('Request object does not exist');
        }

        if( !$transresRequest->getId() ) {
            throw $this->createNotFoundException('Request object ID is null');
        }

        //echo "transitionName=".$transitionName."<br>";
        $user = $this->secTokenStorage->getToken()->getUser();
        $transresUtil = $this->container->get('transres_util');
        $break = "\r\n";

        if( $statMachineType == 'progress' ) {
            $workflow = $this->container->get('state_machine.transres_request_progress');
            $transitions = $workflow->getEnabledTransitions($transresRequest);
            $originalStateStr = $transresRequest->getProgressState();
            $setState = "setProgressState";
        }
        if( $statMachineType == 'billing' ) {
            $workflow = $this->container->get('state_machine.transres_request_billing');
            $transitions = $workflow->getEnabledTransitions($transresRequest);
            $originalStateStr = $transresRequest->getBillingState();
            $setState = "setBillingState";
        }

        if( !$to ) {
            //Get Transition and $to
            $transition = $this->getTransitionByName($transresRequest,$transitionName,$statMachineType);
            if( !$transition ) {
                throw $this->createNotFoundException($statMachineType.' transition not found by name '.$transitionName);
            }
            $tos = $transition->getTos();
            if (count($tos) != 1) {
                throw $this->createNotFoundException('Available to state is not a single state; count=' . $tos . ": " . implode(",", $tos));
            }
            $to = $tos[0];
        }
        //echo "to=".$to."<br>";

        //$label = $this->getTransitionLabelByName($transitionName);
        //$label = $transitionName;
        //echo "label=".$label."<br>";

        $originalStateLabel = $this->getRequestStateLabelByName($originalStateStr,$statMachineType);

        // Update the currentState on the post
        if( $workflow->can($transresRequest, $transitionName) ) {
            try {

                //$review->setDecisionByTransitionName($transitionName);
                //$review->setReviewedBy($user);

                $workflow->apply($transresRequest, $transitionName);
                //change state
                $transresRequest->$setState($to); //i.e. 'irb_review'

                //check and add reviewers for this state by role? Do it when project is created?
                //$this->addDefaultStateReviewers($project);

                //write to DB
                if( !$testing ) {
                    $this->em->flush();
                }

                $label = $this->getRequestStateLabelByName($to,$statMachineType);
                $subject = "Project ID ".$transresRequest->getProject()->getOid().": Request ID ".$transresRequest->getId()." has been sent to the status '$label' from '".$originalStateLabel."'";
                $body = $subject;
                //get request url
                $requestUrl = $this->getRequestShowUrl($transresRequest);
                $emailBody = $body . $break.$break. "Please click on the URL below to view this project:".$break.$requestUrl;

                //send confirmation email
                //TODO: send confirmation email to who?
                $this->sendNotificationEmails($transresRequest,$statMachineType,$subject,$emailBody,$testing);

                //event log
                //$this->setEventLog($project,$review,$transitionName,$originalStateStr,$body,$testing);
                $eventType = "Request State Changed";
                $transresUtil->setEventLog($transresRequest,$eventType,$body,$testing);

                $this->container->get('session')->getFlashBag()->add(
                    'notice',
                    "Successful action: ".$label
                );
                return true;
            } catch (\LogicException $e) {

                //event log

                $this->container->get('session')->getFlashBag()->add(
                    'warning',
                    "Action failed: ".$label
                );
                return false;
            }//try
        }
    }

    public function getTransitionByName( $transresRequest, $transitionName, $statMachineType ) {
        $workflow = $this->getWorkflowByStateMachineType($statMachineType);
        $transitions = $workflow->getEnabledTransitions($transresRequest);
        foreach( $transitions as $transition ) {
            if( $transition->getName() == $transitionName ) {
                return $transition;
            }
        }
        return null;
    }

    public function getWorkflowByStateMachineType($statMachineType) {
        if( $statMachineType == 'progress' ) {
            return $this->container->get('state_machine.transres_request_progress');
        }
        if( $statMachineType == 'billing' ) {
            return $this->container->get('state_machine.transres_request_billing');
        }
        return null;
    }

    public function getRequestShowUrl($transresRequest) {
        $url = $this->container->get('router')->generate(
            'translationalresearch_request_show',
            array(
                'id' => $transresRequest->getId(),
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $url;
    }

    //set transresRequest's $fundedAccountNumber to the project's formnode (i.e. $fundedAccountNumber => "If funded, please provide account number")
    public function setValueToFormNodeProject( $project, $fieldName, $value ) {
        //echo "value=$value<br>";

        //if( $fieldName != "If funded, please provide account number" ) {
            //only supported and tested for the string formnode field
            //return;
        //}

        $formNodeUtil = $this->container->get('user_formnode_utility');
        $transResFormNodeUtil = $this->container->get('transres_formnode_util');
        //$transResFormNodeUtil->setProjectFormNodeFieldByName($project,$fieldName,$value);

        //1) get project's formnode
        $fieldFormNode = $transResFormNodeUtil->getFormNodeByFieldNameAndParents(
            $fieldName
        );
        //echo "fieldFormNode=".$fieldFormNode->getId()."<br>";
        if( !$fieldFormNode ) {
            return array();
        }

        //2) get objectTypeDropdowns by:
        $mapper = array(
            "entityName" => "Project",
            "entityNamespace" => "Oleg\\TranslationalResearchBundle\\Entity",
            "entityId" => $project->getId(),
        );
        $receivingValue = null;
        $compareType = null;
        $receivingObjects = $formNodeUtil->getFormNodeListRecordsByReceivingObjectValue($fieldFormNode,$receivingValue,$mapper,$compareType);

        //echo "receivingObjects count=".count($receivingObjects)."<br>";
        //foreach($receivingObjects as $receivingObject){
        //    echo "receivingObject ID=".$receivingObject->getId()."<br>";
        //}

        if( count($receivingObjects) == 0 ) {
            throw new \Exception("receivingObjects are not found for the project ID ".$project->getId()." and fieldName=".$fieldName." => "."failed to set value".$value);
        }

        $receivingObject = $receivingObjects[0];
        $receivingObject->setValue($value);

        //echo "receivingObject ID=".$receivingObject->getId().": updated value=".$value."<br>";

        //$this->em->flush($receivingObject);

        //exit('exit setValueToFormNodeProject');
        
        return $receivingObject;
    }

    public function getRequestItems($request) {
        $user = $this->secTokenStorage->getToken()->getUser();
        $invoiceItemsArr = new ArrayCollection();
        foreach( $request->getProducts() as $product ) {
            //Invoice's quantity field is pre-populated by the Request's "Requested #"
            $invoiceItem = new InvoiceItem($user);

            $invoiceItem->setProduct($product);

            $quantity = null;
            $requested = $product->getRequested();
            if( $requested ) {
                $quantity = $requested;
            } else {
                $completed = $product->getCompleted();
                if( $completed ) {
                    $quantity = $completed;
                }
            }
            $invoiceItem->setQuantity($quantity);

            $category = $product->getCategory();

            //ItemCode
            $itemCode = $category->getProductId();
            $invoiceItem->setItemCode($itemCode);

            //Description
            $name = $category->getName();
            $invoiceItem->setDescription($name);

            //UnitPrice
            $fee = $category->getFee();
            $invoiceItem->setUnitPrice($fee);

            //Total
            $total = intval($requested) * intval($fee);
            $invoiceItem->setTotal($total);

            $invoiceItemsArr->add($invoiceItem);
        }

        return $invoiceItemsArr;
    }
    public function getRequestItemsFormNode($request) {
        $user = $this->secTokenStorage->getToken()->getUser();
        //$user = null; //testing
        $invoiceItemsArr = new ArrayCollection();

        $transResFormNodeUtil = $this->container->get('transres_formnode_util');
        $formNodeUtil = $this->container->get('user_formnode_utility');

        $completedEntities = $transResFormNodeUtil->getProjectFormNodeFieldByName(
            $request,
            "Completed #",
            "HemePath Translational Research Request",
            "Request",
            "Product or Service",
            null,
            true
        );
        //echo "completedEntities=".count($completedEntities)."<br>";
//        $formNodeValues = $completedEntities['formNodeValue'];
//        foreach($formNodeValues as $resArr) {
//            $formNodeValue = $resArr['formNodeValue'];
//            echo "formNodeValue=".$formNodeValue."<br>";
//            $arraySectionIndex = $resArr['arraySectionIndex'];
//            echo "arraySectionIndex=" . $arraySectionIndex . "<br>";
//        }
//        return 1;

        $requestedEntities = $transResFormNodeUtil->getProjectFormNodeFieldByName(
            $request,
            "Requested #",
            "HemePath Translational Research Request",
            "Request",
            "Product or Service",
            null,
            true
        );
        //echo "requestedEntities=".count($requestedEntities)."<br>";

        $requestCategoryTypeComplexResults = $this->getMultipleProjectFormNodeFieldByName(
            $request,
            "Category Type",
            "HemePath Translational Research Request",
            "Request",
            "Product or Service"
        );
        //echo "requestCategoryTypeComplexResults=".count($requestCategoryTypeComplexResults)."<br>";

        //2) group by arraySectionIndex
        foreach($requestCategoryTypeComplexResults as $complexRes) {

            $arraySectionIndex = $complexRes['arraySectionIndex'];
            //echo "arraySectionIndex=".$arraySectionIndex."<br>";
            $dropdownObject = $complexRes['dropdownObject'];

            $requested = $this->findByArraySectionIndex($requestedEntities,$arraySectionIndex);
            //echo "requested=".$requested."<br>";
            $completed = $this->findByArraySectionIndex($completedEntities,$arraySectionIndex);
            //echo "completed=".$completed."<br>";
            //echo "###<br>";

            //$fee = $dropdownObject->getFee();

//            if( $fee ) {
//                $subTotal = $subTotal + intval($completed) * intval($fee);
//                //return $subTotal;
//            }

            $invoiceItem = new InvoiceItem($user);
            $invoiceItem->setQuantity($completed);

            //ItemCode
            $itemCode = $dropdownObject->getProductId();
            $invoiceItem->setItemCode($itemCode);

            //Description
            $name = $dropdownObject->getName();
            $invoiceItem->setDescription($name);

            //UnitPrice
            $fee = $dropdownObject->getFee();
            $invoiceItem->setUnitPrice($fee);

            //Total
            $total = intval($completed) * intval($fee);
            $invoiceItem->setTotal($total);

            $invoiceItemsArr->add($invoiceItem);
        }

        //$invoiceItemsArr->add(new InvoiceItem($user));
        //$invoiceItemsArr->add(new InvoiceItem($user));
        //$invoiceItemsArr->add(new InvoiceItem($user));
        return $invoiceItemsArr;
    }
    
    public function getInvoiceLogo() {
        //<img src="{{ asset(bundleFileName) }}" alt="{{ title }}"/>
        $filename = "wcmc_logo.jpg";
        $bundleFileName = "bundles\\olegtranslationalresearch\\images\\".$filename;
        return $bundleFileName;

        $title = "WCMC";
        $html = '<img src="'.$bundleFileName.'" alt="'.$title.'"/>';
        return $html;
    }

    public function sendNotificationEmails($transresRequest, $statMachineType, $subject, $body, $testing=false) {
        //if( !$appliedTransition ) {
        //    return null;
        //}

        $transresUtil = $this->container->get('transres_util');
        $emailUtil = $this->container->get('user_mailer_utility');

        $senderEmail = null; //Admin email
        $emails = array();

        //send to the
        // 1) admins and primary reviewers
        $admins = $transresUtil->getTransResAdminEmails(); //ok
        $emails = array_merge($emails,$admins);

        // 2) a) submitter, b) principalInvestigators, c) contact
        //a submitter
        if( $transresRequest->getSubmitter() ) {
            $submitterEmail = $transresRequest->getSubmitter()->getSingleEmail();
            if( $submitterEmail ) {
                $emails = array_merge($emails,array($submitterEmail));
            }
        }

        //b principalInvestigators
        $piEmailArr = array();
        $pis = $transresRequest->getPrincipalInvestigators();
        foreach( $pis as $pi ) {
            if( $pi ) {
                $piEmailArr[] = $pi->getSingleEmail();
            }
        }
        $emails = array_merge($emails,$piEmailArr);

        //c contact
        if( $transresRequest->getContact() ) {
            $contactEmail = $transresRequest->getContact()->getSingleEmail();
            if( $submitterEmail ) {
                $emails = array_merge($emails,array($contactEmail));
            }
        }

        $emails = array_unique($emails);

        //                    $emails, $subject, $message, $ccs=null, $fromEmail=null
        $emailUtil->sendEmail( $emails, $subject, $body, null, $senderEmail );

    }
    
}