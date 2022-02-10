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

namespace App\VacReqBundle\Controller;


use App\UserdirectoryBundle\Controller\OrderAbstractController;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\VacReqBundle\Entity\VacReqRequest;
use App\VacReqBundle\Entity\VacReqRequestFloating;
use App\VacReqBundle\Form\VacReqFilterType;
use App\VacReqBundle\Form\VacReqFloatingDayType;
use App\VacReqBundle\Form\VacReqRequestFloatingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

//vacreq site

class FloatingDayController extends OrderAbstractController
{

    /**
     * @Route("/incoming-requests/floating-day", name="vacreq_floatingrequests", methods={"GET", "POST"})
     * @Template("AppVacReqBundle/FloatingDay/index.html.twig")
     */
    public function incomingFloatingRequestsAction(Request $request)
    {
        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_APPROVER') && false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_SUPERVISOR') ) {
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $params = array(
            'approver' => $user,
            'title' => "Incoming Floating Day Requests",
            'filterShowUser' => true
        );

        return $this->listRequests($params,$request);

//        //exit('incomingFloatingRequestsAction');
//        $em = $this->getDoctrine()->getManager();
//        $vacreqUtil = $this->get('vacreq_util');
//        $user = $this->get('security.token_storage')->getToken()->getUser();
//        $routeName = $request->get('_route');
//        $sitename = $this->getParameter('vacreq.sitename');
//        $filtered = false;
//        $indexTitle = "Floating Day Incoming Requests";
//        $pageTitle = $indexTitle;
//        $requestTypeAbbreviation = "floatingday";
//
//        //////////////// create vacreq filter ////////////////
//        $params = array(
//            //'cycle' => 'show'
//            'em' => $em,
//            'routeName' => $routeName,
//            'filterShowUser' => true,
//            'requestTypeAbbreviation' => $requestTypeAbbreviation,
//        );
//
//        $supervisorRole = false;
//        if( $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_SUPERVISOR') ||
//            $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN')
//        ) {
//            $supervisorRole = true;
//        }
//        $params['supervisor'] = $supervisorRole;
//
//        //////////////////// get list of users with "unknown" user ////////////////////
//        $em = $this->getDoctrine()->getManager();
//        $repository = $this->getDoctrine()->getRepository('AppUserdirectoryBundle:User');
//        $dqlFilterUser = $repository->createQueryBuilder('user');
//        $dqlFilterUser->select('user');
//        $dqlFilterUser->leftJoin("user.infos","infos");
//        $dqlFilterUser->leftJoin("user.employmentStatus", "employmentStatus");
//        $dqlFilterUser->leftJoin("employmentStatus.employmentType", "employmentType");
//        //filter out system user
//        $dqlFilterUser->andWhere("user.keytype IS NOT NULL AND user.primaryPublicUserId != 'system'");
//        //filter out Pathology Fellowship Applicants
//        $dqlFilterUser->andWhere("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL");
//        //$dqlFilterUser->where("user.keytype IS NOT NULL");
//        $dqlFilterUser->orderBy("infos.lastName","ASC");
//        $queryFilterUser = $em->createQuery($dqlFilterUser);
//        $filterUsers = $queryFilterUser->getResult();
//        //echo "count=".count($filterUsers)."<br>";
//        //add unknown dummy user
////        $unknown = new User();
////        $unknown->setDisplayName("unknown");
////        $em->persist($unknown);
//        //$filterUsers[] = $unknown;
////        array_unshift($filterUsers, $unknown);
//        $params['filterUsers'] = $filterUsers;
//        //////////////////// EOF get list of users with "unknown" user ////////////////////
//
//        //get submitter groups: VacReqRequest, create
//        $groupParams = array();
//        $groupParams['statusArr'] = array('default','user-added');
//        if( $request->get('_route') == "vacreq_myrequests" ) {
//            $groupParams['permissions'][] = array('objectStr'=>'VacReqRequest','actionStr'=>'create');
//        }
//        if( $request->get('_route') == "vacreq_incomingrequests" ) {
//            if( $params['requestTypeAbbreviation'] == "business-vacation" ) {
//                $groupParams['permissions'][] = array('objectStr'=>'VacReqRequest','actionStr'=>'changestatus');
//                if( $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN') == false ) {
//                    $groupParams['exceptPermissions'][] = array('objectStr' => 'VacReqRequest', 'actionStr' => 'changestatus-carryover');
//                }
//            } else {
//                $groupParams['permissions'][] = array('objectStr'=>'VacReqRequest','actionStr'=>'changestatus-carryover');
//            }
//        }
//        if( $request->get('_route') == "vacreq_floatingrequests" ) {
//            $groupParams['permissions'][] = array('objectStr'=>'VacReqRequest','actionStr'=>'changestatus');
//            if( $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN') == false ) {
//                $groupParams['exceptPermissions'][] = array('objectStr' => 'VacReqRequest', 'actionStr' => 'changestatus-carryover');
//            }
//        }
//        $user = $this->get('security.token_storage')->getToken()->getUser();
//        $organizationalInstitutions = $vacreqUtil->getGroupsByPermission($user,$groupParams);
//
//        $userServiceUtil = $this->get('user_service_utility');
//        $params['organizationalInstitutions'] = $userServiceUtil->flipArrayLabelValue($organizationalInstitutions); //flipped //$organizationalInstitutions;
//
//
//        $filterform = $this->createForm(VacReqFilterType::class, null,array(
//            'method' => 'GET',
//            'form_custom_value'=>$params
//        ));
//        //////////////// EOF create vacreq filter ////////////////
//
//        //$filterform->submit($request);  //use bind instead of handleRequest. handleRequest does not get filter data
//        $filterform->handleRequest($request);
//
//        $repository = $em->getRepository('AppVacReqBundle:VacReqRequestFloating');
//        $dql = $repository->createQueryBuilder("request");
//
//        $dql->select('request');
//
//        //COALESCE(requestBusiness.numberOfDays,0) replace NULL with 0 (similar to ISNULL)
//        //$dql->addSelect('(COALESCE(requestBusiness.numberOfDays,0) + COALESCE(requestVacation.numberOfDays,0)) as thisRequestTotalDays');
//
//        $dql->leftJoin("request.user", "user");
//        //$dql->leftJoin("request.submitter", "submitter");
//        $dql->leftJoin("user.infos", "infos");
//        $dql->leftJoin("request.institution", "institution");
//
//        $limit = 30;
//        $query = $em->createQuery($dql);
//
//        $paginationParams = array(
//            //'defaultSortFieldName' => 'request.firstDayAway', //createDate
//            'defaultSortDirection' => 'DESC',
//            'wrap-queries'=>true //use "doctrine/orm": "v2.4.8". ~2.5 causes error: Cannot select distinct identifiers from query with LIMIT and ORDER BY on a column from a fetch joined to-many association. Use walker.
//        );
//
//        $paginator  = $this->get('knp_paginator');
//        $pagination = $paginator->paginate(
//            $query,
//            $request->query->get('page', 1),   /*page number*/
//            $limit,                                         /*limit per page*/
//            $paginationParams
//        );
//
//
//        return array(
//            'filterform' => $filterform,
//            'vacreqfilter' => $filterform->createView(),
//            'pagination' => $pagination,
//            'sitename' => $sitename,
//            'filtered' => $filtered,
//            'routename' => $routeName,
//            'title' => $indexTitle,
//            'pageTitle' => $pageTitle,
//            'requestTypeAbbreviation' => $requestTypeAbbreviation,
//            //'totalApprovedDaysString' => $params['totalApprovedDaysString']
//        );
    }


    /**
     * @Route("/my-requests/floating-day", name="vacreq_myfloatingrequests", methods={"GET", "POST"})
     * @Template("AppVacReqBundle/FloatingDay/index.html.twig")
     */
    public function myFloatingRequestsAction(Request $request)
    {
        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_APPROVER') && false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_SUPERVISOR') ) {
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $params = array(
            'sitename' => $this->getParameter('vacreq.sitename'),
            'subjectUser' => $user,
            'title' => "My Floating Day Requests",
            'filterShowUser' => false,
        );
        return $this->listRequests($params, $request);
    }

    public function listRequests( $params, $request ) {

        $subjectUser = ( array_key_exists('subjectUser', $params) ? $params['subjectUser'] : null); //logged in user
        $approver = ( array_key_exists('approver', $params) ? $params['approver'] : null);
        $approver = ( array_key_exists('approver', $params) ? $params['approver'] : null);

        $indexTitle = $params['title'];
        $pageTitle = $indexTitle;

        //exit('incomingFloatingRequestsAction');
        $em = $this->getDoctrine()->getManager();
        $vacreqUtil = $this->get('vacreq_util');
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $routeName = $request->get('_route');
        $sitename = $this->getParameter('vacreq.sitename');
        //$filtered = false;
        //$indexTitle = "Floating Day Incoming Requests";
        //$pageTitle = $indexTitle;
        $requestTypeAbbreviation = "floatingday";

        //////////////// create vacreq filter ////////////////
//        $params = array(
//            //'cycle' => 'show'
//            'em' => $em,
//            'routeName' => $routeName,
//            'filterShowUser' => true,
//            'requestTypeAbbreviation' => $requestTypeAbbreviation,
//        );
        $params['em'] = $em;
        $params['routeName'] = $routeName;
        $params['requestTypeAbbreviation'] = $requestTypeAbbreviation;

        $supervisorRole = false;
        if( $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_SUPERVISOR') ||
            $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN')
        ) {
            $supervisorRole = true;
        }
        $params['supervisor'] = $supervisorRole;

//        //////////////////// get list of users with "unknown" user ////////////////////
//        $em = $this->getDoctrine()->getManager();
//        $repository = $this->getDoctrine()->getRepository('AppUserdirectoryBundle:User');
//        $dqlFilterUser = $repository->createQueryBuilder('user');
//        $dqlFilterUser->select('user');
//        $dqlFilterUser->leftJoin("user.infos","infos");
//        $dqlFilterUser->leftJoin("user.employmentStatus", "employmentStatus");
//        $dqlFilterUser->leftJoin("employmentStatus.employmentType", "employmentType");
//        //filter out system user
//        $dqlFilterUser->andWhere("user.keytype IS NOT NULL AND user.primaryPublicUserId != 'system'");
//        //filter out Pathology Fellowship Applicants
//        $dqlFilterUser->andWhere("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL");
//        //$dqlFilterUser->where("user.keytype IS NOT NULL");
//        $dqlFilterUser->orderBy("infos.lastName","ASC");
//        $queryFilterUser = $em->createQuery($dqlFilterUser);
//        $filterUsers = $queryFilterUser->getResult();
//        //echo "count=".count($filterUsers)."<br>";
//        //add unknown dummy user
////        $unknown = new User();
////        $unknown->setDisplayName("unknown");
////        $em->persist($unknown);
//        //$filterUsers[] = $unknown;
////        array_unshift($filterUsers, $unknown);
//        $params['filterUsers'] = $filterUsers;
//        //////////////////// EOF get list of users with "unknown" user ////////////////////
//
//        //get submitter groups: VacReqRequest, create
//        $groupParams = array();
//        $groupParams['statusArr'] = array('default','user-added');
//        if( $request->get('_route') == "vacreq_myrequests" ) {
//            $groupParams['permissions'][] = array('objectStr'=>'VacReqRequest','actionStr'=>'create');
//        }
//        if( $request->get('_route') == "vacreq_incomingrequests" ) {
//            if( $params['requestTypeAbbreviation'] == "business-vacation" ) {
//                $groupParams['permissions'][] = array('objectStr'=>'VacReqRequest','actionStr'=>'changestatus');
//                if( $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN') == false ) {
//                    $groupParams['exceptPermissions'][] = array('objectStr' => 'VacReqRequest', 'actionStr' => 'changestatus-carryover');
//                }
//            } else {
//                $groupParams['permissions'][] = array('objectStr'=>'VacReqRequest','actionStr'=>'changestatus-carryover');
//            }
//        }
//        if( $request->get('_route') == "vacreq_floatingrequests" ) {
//            $groupParams['permissions'][] = array('objectStr'=>'VacReqRequest','actionStr'=>'changestatus');
//            if( $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN') == false ) {
//                $groupParams['exceptPermissions'][] = array('objectStr' => 'VacReqRequest', 'actionStr' => 'changestatus-carryover');
//            }
//        }
//        $user = $this->get('security.token_storage')->getToken()->getUser();
//        $organizationalInstitutions = $vacreqUtil->getGroupsByPermission($user,$groupParams);
//
//        $userServiceUtil = $this->get('user_service_utility');
//        $params['organizationalInstitutions'] = $userServiceUtil->flipArrayLabelValue($organizationalInstitutions); //flipped //$organizationalInstitutions;
//
//
//        $filterform = $this->createForm(VacReqFilterType::class, null,array(
//            'method' => 'GET',
//            'form_custom_value'=>$params
//        ));
//        //////////////// EOF create vacreq filter ////////////////
//
//        //$filterform->submit($request);  //use bind instead of handleRequest. handleRequest does not get filter data
//        $filterform->handleRequest($request);

        $repository = $em->getRepository('AppVacReqBundle:VacReqRequestFloating');
        $dql = $repository->createQueryBuilder("request");

        $dql->select('request');

        //COALESCE(requestBusiness.numberOfDays,0) replace NULL with 0 (similar to ISNULL)
        //$dql->addSelect('(COALESCE(requestBusiness.numberOfDays,0) + COALESCE(requestVacation.numberOfDays,0)) as thisRequestTotalDays');

        $dql->leftJoin("request.user", "user");
        //$dql->leftJoin("request.submitter", "submitter");
        $dql->leftJoin("user.infos", "infos");
        $dql->leftJoin("request.institution", "institution");

        //my requests
        if( $subjectUser ) {
            $dql->andWhere("(request.user=".$subjectUser->getId()." OR request.submitter=".$subjectUser->getId().")");
        }

        //incoming requests: show all requests with institutions in vacreq roles institutions
        //filter by institutions for any user by using a general sub role name "ROLE_VACREQ_"
        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN') ) {
            if( $approver ) {
                $partialRoleName = "ROLE_VACREQ_";  //"ROLE_VACREQ_APPROVER"
                $vacreqRoles = $em->getRepository('AppUserdirectoryBundle:User')->
                    findUserRolesBySiteAndPartialRoleName($approver, "vacreq", $partialRoleName, null, false);

                //select all requests with institution is equal or under vacreqRole institution.
                if( count($vacreqRoles) > 0 ) {
                    $instCriterionArr = array();
                    $addedNodes = array();
                    foreach( $vacreqRoles as $vacreqRole ) {
                        $roleInst = $vacreqRole->getInstitution();
                        //echo "roleInst=".$roleInst."<br>";
                        if( !in_array($roleInst->getId(), $addedNodes) ) {
                            $addedNodes[] = $roleInst->getId();
                            //regular institution
                            $instCriterionArr[] = $em->getRepository('AppUserdirectoryBundle:Institution')->
                            selectNodesUnderParentNode($roleInst,"institution",false);
                            //regular tentativeInstitution
                            //$instCriterionArr[] = $em->getRepository('AppUserdirectoryBundle:Institution')->
                            //selectNodesUnderParentNode($roleInst,"tentativeInstitution",false);
                        }
                    }
                    if( count($instCriterionArr) > 0 ) {
                        $instCriteriaStr = implode(" OR ",$instCriterionArr);
                        $dql->andWhere($instCriteriaStr);
                    }
                }
            }
        }

        //process filter
        $filterRes = $this->processFilter( $dql, $request, $params );
        $filterform = $filterRes['form'];
        $dqlParameters = $filterRes['dqlParameters'];
        $filtered = $filterRes['filtered'];
        //$requestTypeAbbreviation = $filterRes['requestTypeAbbreviation'];

        $limit = 30;
        $query = $em->createQuery($dql);

        $paginationParams = array(
            //'defaultSortFieldName' => 'request.firstDayAway', //createDate
            'defaultSortDirection' => 'DESC',
            'wrap-queries'=>true //use "doctrine/orm": "v2.4.8". ~2.5 causes error: Cannot select distinct identifiers from query with LIMIT and ORDER BY on a column from a fetch joined to-many association. Use walker.
        );

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),   /*page number*/
            $limit,                                         /*limit per page*/
            $paginationParams
        );


        return array(
            'filterform' => $filterform,
            'vacreqfilter' => $filterform->createView(),
            'pagination' => $pagination,
            'sitename' => $sitename,
            'filtered' => $filtered,
            'routename' => $routeName,
            'title' => $indexTitle,
            'pageTitle' => $pageTitle,
            'requestTypeAbbreviation' => $requestTypeAbbreviation,
            //'totalApprovedDaysString' => $params['totalApprovedDaysString']
        );
    }

    public function processFilter( $dql, $request, $params ) {

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        $vacreqUtil = $this->get('vacreq_util');

        $dqlParameters = array();
        $filterRes = array();
        $filtered = false;

        //////////////////// get list of users with "unknown" user ////////////////////
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('AppUserdirectoryBundle:User');
        $dqlFilterUser = $repository->createQueryBuilder('user');
        $dqlFilterUser->select('user');
        $dqlFilterUser->leftJoin("user.infos","infos");
        $dqlFilterUser->leftJoin("user.employmentStatus", "employmentStatus");
        $dqlFilterUser->leftJoin("employmentStatus.employmentType", "employmentType");
        //filter out system user
        $dqlFilterUser->andWhere("user.keytype IS NOT NULL AND user.primaryPublicUserId != 'system'");
        //filter out Pathology Fellowship Applicants
        $dqlFilterUser->andWhere("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL");
        //$dqlFilterUser->where("user.keytype IS NOT NULL");
        $dqlFilterUser->orderBy("infos.lastName","ASC");
        $queryFilterUser = $em->createQuery($dqlFilterUser);
        $filterUsers = $queryFilterUser->getResult();
        //echo "count=".count($filterUsers)."<br>";
        //add unknown dummy user
//        $unknown = new User();
//        $unknown->setDisplayName("unknown");
//        $em->persist($unknown);
        //$filterUsers[] = $unknown;
//        array_unshift($filterUsers, $unknown);
        $params['filterUsers'] = $filterUsers;
        //////////////////// EOF get list of users with "unknown" user ////////////////////

        $params['em'] = $em;

        //get request type
        $params['requestTypeAbbreviation'] = "floatingday";
        $requestParams = $request->query->all();
        $requestTypeId = null;
        if( array_key_exists("filter", $requestParams) ) {
            if( array_key_exists("requestType", $requestParams["filter"]) ) {
                $requestTypeId = $requestParams["filter"]["requestType"];
            }
        }
//        //echo "requestTypeId=".$requestTypeId."<br>";
//        if( $requestTypeId ) {
//            $requestType = $em->getRepository('AppVacReqBundle:VacReqRequestTypeList')->find($requestTypeId);
//            if (!$requestType) {
//                throw $this->createNotFoundException('Unable to find Request Type by id=' . $requestTypeId);
//            }
//            //echo "requestTypeAbbreviation=".$requestType->getAbbreviation()."<br>";
//            $params['requestTypeAbbreviation'] = $requestType->getAbbreviation();
//        }

        //get submitter groups: VacReqRequest, create
        $groupParams = array();
        $groupParams['statusArr'] = array('default','user-added');
        if( $request->get('_route') == "vacreq_myfloatingrequests" ) {
            $groupParams['permissions'][] = array('objectStr'=>'VacReqRequest','actionStr'=>'create');
        }
        if( $request->get('_route') == "vacreq_floatingrequests" ) {
            $groupParams['permissions'][] = array('objectStr'=>'VacReqRequest','actionStr'=>'changestatus');
            if( $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN') == false ) {
                $groupParams['exceptPermissions'][] = array('objectStr' => 'VacReqRequest', 'actionStr' => 'changestatus-carryover');
            }
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $organizationalInstitutions = $vacreqUtil->getGroupsByPermission($user,$groupParams);

        //testing
        //foreach( $organizationalInstitutions as $organizationalInstitution ) {
        //echo "organizationalInstitution=".$organizationalInstitution."<br>";
        //}

        if( count($organizationalInstitutions) == 0 ) {
            if( $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN') ) {
                $groupPageUrl = $this->generateUrl(
                    "vacreq_approvers",
                    array(),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                $warningMsg = "No submitter/approver groups have been set up. Such groups can be set up " .
                    '<a href="' . $groupPageUrl . '" target="_blank">here</a>.' .
                    " Once they are set up, this page will show cumulative summary data.";
            } else {
                //regular user
                $adminUsers = $em->getRepository('AppUserdirectoryBundle:User')->findUserByRole("ROLE_VACREQ_ADMIN", "infos.lastName", true);
                $emails = array();
                foreach ($adminUsers as $adminUser) {
                    $singleEmail = $adminUser->getSingleEmail();
                    if ($singleEmail) {
                        $emails[] = $adminUser->getSingleEmail();
                    }
                }
                $emailStr = "";
                if (count($emails) > 0) {
                    $emailStr = " Administrator email(s): " . implode(", ", $emails);
                }

                $warningMsg = "No submitter/approver groups have been set up." .
                    " Please contact the site administrator to create a group and/or get a Submitter role for your account.".$emailStr;
                " Once they are set up, this page will show cumulative summary data.";
            }
            //Flash
            $this->get('session')->getFlashBag()->add(
                'warning',
                $warningMsg
            );
        }

        $userServiceUtil = $this->get('user_service_utility');
        $params['organizationalInstitutions'] = $userServiceUtil->flipArrayLabelValue($organizationalInstitutions); //flipped //$organizationalInstitutions;

//        //tentative institutions
//        $tentativeGroupParams = array(); //'asObject'=>true
//        $tentativeGroupParams['permissions'][] = array('objectStr'=>'VacReqRequest','actionStr'=>'changestatus');
//        if( $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN') == false ) {
//            $tentativeGroupParams['exceptPermissions'][] = array('objectStr' => 'VacReqRequest', 'actionStr' => 'changestatus-carryover');
//        }
//        $tentativeInstitutions = $vacreqUtil->getGroupsByPermission($user,$tentativeGroupParams);
        //testing
        //foreach( $tentativeInstitutions as $tentativeInstitution ) {
        //echo "tentativeInstitution=".$tentativeInstitution."<br>";
        //}

        //tooltip for Academic Year:
        //"Academic Year Start (for [Current Academic Year, show as 2015-2016], pick [first/starting year, show as 2015]"
        $previousYear = date("Y") - 1;
        $currentYear = date("Y");
        $yearRange = $previousYear."-".$currentYear;
        $academicYearTooltip = "Academic Year Start (for ".$yearRange.", pick ".$previousYear.")";
        $params['academicYearTooltip'] = $academicYearTooltip;

        $params['routeName'] = $request->get('_route');

        $approverRole = false;
        if( $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_APPROVER') ||
            $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN')
        ) {
            $approverRole = true;
        }
        $params['approverRole'] = $approverRole;

        $supervisorRole = false;
        if( $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_SUPERVISOR') ||
            $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN')
        ) {
            $supervisorRole = true;
        }
        $params['supervisor'] = $supervisorRole;

        //create filter form
        $filterform = $this->createForm(VacReqFilterType::class, null, array(
            'method' => 'GET',
            'form_custom_value' => $params
        ));

        //Use the Symfony\Component\Form\Form::handleRequest method instead. If you want to test whether the form was submitted separately, you can use the Symfony\Component\Form\Form::isSubmitted method
        //$filterform->submit($request);
        $filterform->handleRequest($request);

        //echo "<pre>";
        //print_r($filterform['startdate']);
        //echo "</pre>";

        $startdate = $filterform['startdate']->getData();
        $enddate = $filterform['enddate']->getData();

        ////////////// Optional filters //////////////

        //filter type. By default select only floatingday type requests.
//        $requestTypeAbbreviation = "floatingday";
//        if( $filterform->has('requestType') ) {
//            $requestType = $filterform['requestType']->getData();
//            //echo "requestType=".$requestType."<br>";
//            if( $requestType && $requestType->getAbbreviation() ) {
//                $requestTypeAbbreviation = $requestType->getAbbreviation();
//            }
//        }
        //echo "requestTypeAbbreviation=(".$requestTypeAbbreviation.")<br>";
        //$dql->andWhere("requestType.abbreviation = '".$requestTypeAbbreviation."'");
        //$dql->andWhere("requestType.abbreviation = :requestTypeAbbreviation");
        //$dqlParameters['requestTypeAbbreviation'] = $requestTypeAbbreviation;

        //$subjectUser = ( array_key_exists('user', $filterform) ? $filterform['user']->getData() : null);
        if( $filterform->has('user') ) {
            $subjectUser = $filterform['user']->getData();
        } else {
            $subjectUser = null;
        }
        //echo "user=".$subjectUser."<br>";

        if( $filterform->has('submitter') ) {
            $submitter = $filterform['submitter']->getData();
        } else {
            $submitter = null;
        }

        if( $filterform->has('organizationalInstitutions') ) {
            $groups = $filterform['organizationalInstitutions']->getData();
        } else {
            $groups = null;
        }

        if( $filterform->has('academicYear') ) {
            $academicYear = $filterform['academicYear']->getData();
        } else {
            $academicYear = null;
        }

//        if( $filterform->has('vacationRequest') ) {
//            $vacationRequest = $filterform['vacationRequest']->getData();
//        } else {
//            $vacationRequest = null;
//        }
//
//        if( $filterform->has('businessRequest') ) {
//            $businessRequest = $filterform['businessRequest']->getData();
//        } else {
//            $businessRequest = null;
//        }
        ////////////// EOF Optional filters //////////////


        //$completed = $filterform['completed']->getData();
        $completed = null;
        $pending = $filterform['pending']->getData();
        $approved = $filterform['approved']->getData();
        $rejected = $filterform['rejected']->getData();

        $cancellationRequest = $filterform['cancellationRequest']->getData();
        $cancellationRequestApproved = $filterform['cancellationRequestApproved']->getData();
        $cancellationRequestRejected = $filterform['cancellationRequestRejected']->getData();

        //$year = $filterform['year']->getData();
        //echo "userID=".$subjectUser."<br>";

        if( $subjectUser && $subjectUser->getId() ) {
            $where = "";
            if( $where != "" ) {
                $where .= " OR ";
            }
            if( $subjectUser ) {
                $where .= "request.user=".$subjectUser->getId();
            } else {
                $where .= "request.user IS NULL";
            }
            $dql->andWhere($where);

            $filtered = true;
        }

        if( $submitter && $submitter->getId() ) {
            $where = "";
            if( $where != "" ) {
                $where .= " OR ";
            }
            if( $submitter ) {
                $where .= "request.submitter=".$submitter->getId();
            } else {
                $where .= "request.submitter IS NULL";
            }
            $dql->andWhere($where);

            $filtered = true;
        }

        if( $groups && $groups->getId() ) {
            //echo "groupId=".$groups->getId()."<br>";
            $where = "";
            if( $where != "" ) {
                $where .= " OR ";
            }
            if( $groups ) {
                //add institution hierarchy: "Pathology and Laboratory Medicine" institution is under "WCM-NYP Collaboration" institution.
                //$where .= "institution=".$groups->getId();
                //$where .= $em->getRepository('AppUserdirectoryBundle:Institution')->selectNodesUnderParentNode($groups,"institution",false);
                $where .= $em->getRepository('AppUserdirectoryBundle:Institution')->getCriterionStrForCollaborationsByNode(
                    $groups,
                    "institution",
                    array("Union", "Intersection", "Untrusted Intersection"),
                    true,
                    false
                );
//                $where .= $em->getRepository('AppUserdirectoryBundle:Institution')->getCriterionStrUnderlyingCollaborationsByNode(
//                    $groups,
//                    "institution",
//                    array("Union", "Intersection", "Untrusted Intersection")
//                //,true
//                //,false
//                );
            } else {
                $where .= "institution IS NULL";
            }
            //echo "group where=".$where."<br>";
            $dql->andWhere($where);

            $filtered = true;
        }

        if( $groups == null && $request->get('_route') == "vacreq_floatingrequests" ) {

            $instWhereArr = array();

            $instArr = array();
            foreach( $organizationalInstitutions as $instId => $instNameStr ) {
                $instArr[] = $instId;
            }
            if( count($instArr) > 0 ) {
                $instWhereArr[] = "institution.id IN (" . implode(",", $instArr) . ")";
            }

//            $tentativeInstArr = array();
//            foreach( $tentativeInstitutions as $instId => $instNameStr ) {
//                //echo "tentativeInstitution: id=".$instId."; name=".$instNameStr."<br>";
//                $tentativeInstArr[] = $instId;
//            }
//            if( count($tentativeInstArr) > 0 ) {
//                $instWhereArr[] = "(tentativeInstitution.id IN (" . implode(",", $tentativeInstArr) . ") OR tentativeInstitution.id is NULL)";
//            }

            if( count($instWhereArr) > 0 ) {
                $dql->andWhere(implode(" AND ", $instWhereArr)); //OR
            }
        }

        if( $academicYear ) {

            $academicYear = $academicYear->format('Y');
            $academicYear = $academicYear + 1; //the user should pick the start of the academic year (2015) to see 2015-2016
            //echo "academicYear=".$academicYear."<br>";

            $startAcademicYearStr = $vacreqUtil->getEdgeAcademicYearDate( $academicYear, "Start" );
            $startAcademicYearDate = new \DateTime($startAcademicYearStr);
            $startAcademicYearDate = $this->convertFromUserTimezonetoUTC($startAcademicYearDate,$currentUser);
            $startAcademicYearDate->setTime(00, 00, 00);
            //echo "start year date:".$startAcademicYearDate->format('Y-m-d H:i:s')."<br>";

            $endAcademicYearStr = $vacreqUtil->getEdgeAcademicYearDate( $academicYear, "End" );
            $endAcademicYearDate = new \DateTime($endAcademicYearStr);
            $endAcademicYearDate = $this->convertFromUserTimezonetoUTC($endAcademicYearDate,$currentUser);
            $endAcademicYearDate->setTime(23, 59, 59);
            //echo "end year date:".$endAcademicYearDate->format('Y-m-d H:i:s')."<br>";

            //requests with firstDayAway or firstDayBackInOffice inside the academic year
            $dql->andWhere("(request.firstDayAway between :createDateStart AND :createDateEnd OR request.firstDayBackInOffice between :createDateStart AND :createDateEnd)");

            $dqlParameters['createDateStart'] = $startAcademicYearDate;
            $dqlParameters['createDateEnd'] = $endAcademicYearDate;

            $filtered = true;
        }

        //echo "startdate=".$startdate."<br>";
        if( $startdate ) {
            $dql->andWhere("request.createDate >= :startdate");

            $startdate = $this->convertFromUserTimezonetoUTC($startdate,$currentUser);
            $startdate->setTime(00, 00, 00);
            $dqlParameters['startdate'] = $startdate;

            $filtered = true;
        }

        if( $enddate ) {
            $dql->andWhere("request.createDate <= :enddate");

            $enddate = $this->convertFromUserTimezonetoUTC($enddate,$currentUser);
            $enddate->setTime(23, 59, 59);
            $dqlParameters['enddate'] = $enddate;

            $filtered = true;
        }

//        if( $vacationRequest || $businessRequest ) {
//            $requestStatusCriterionArr = array();
//            if( $businessRequest ) {
//                $requestStatusCriterionArr[] = "requestBusiness.startDate IS NOT NULL";
//            }
//            if( $vacationRequest ) {
//                $requestStatusCriterionArr[] = "requestVacation.startDate IS NOT NULL";
//            }
//
//            if( count($requestStatusCriterionArr) > 0 ) {
//                $dql->andWhere(implode(" OR ", $requestStatusCriterionArr));
//                $filtered = true;
//            }
//        }

        if( $completed || $pending || $rejected || $approved ) {
            $requestStatusCriterionArr = array();

            if ($completed) {
                $requestStatusCriterionArr[] = "request.status='rejected' OR request.status='approved'";
            }
            if ($pending) {
                $requestStatusCriterionArr[] = "request.status='pending'";
            }
            if ($rejected) {
                $requestStatusCriterionArr[] = "request.status='rejected'";
            }
            if ($approved) {
                $requestStatusCriterionArr[] = "request.status='approved'";
            }
            if ($cancellationRequestApproved) {
                $requestStatusCriterionArr[] = "request.status='canceled'";
            }

//            if( $requestTypeAbbreviation == "business-vacation" ) {
//                if ($completed) {
//                    $requestStatusCriterionArr[] = "requestBusiness.status='rejected' OR requestVacation.status='rejected' OR requestBusiness.status='approved' OR requestVacation.status='approved'";
//                }
//                if ($pending) {
//                    $requestStatusCriterionArr[] = "requestBusiness.status='pending' OR requestVacation.status='pending'";
//                }
//                if ($rejected) {
//                    $requestStatusCriterionArr[] = "requestBusiness.status='rejected' OR requestVacation.status='rejected'";
//                }
//                if ($approved) {
//                    $requestStatusCriterionArr[] = "requestBusiness.status='approved' OR requestVacation.status='approved'";
//                }
//                if ($cancellationRequest) {
//                    $requestStatusCriterionArr[] = "request.extraStatus = 'Cancellation Requested'";
//                }
//                if ($cancellationRequestApproved) {
//                    $requestStatusCriterionArr[] = "request.extraStatus = 'Cancellation Approved (Canceled)'";
//                }
//                if ($cancellationRequestRejected) {
//                    $requestStatusCriterionArr[] = "request.extraStatus = 'Cancellation Denied (Approved)'";
//                }
//            }
//            if( $requestTypeAbbreviation == "carryover" ) {
//                if ($completed) {
//                    $requestStatusCriterionArr[] = "request.status='rejected' OR request.status='approved'";
//                }
//                if ($pending) {
//                    $requestStatusCriterionArr[] = "request.status='pending'";
//                }
//                if ($rejected) {
//                    $requestStatusCriterionArr[] = "request.status='rejected'";
//                }
//                if ($approved) {
//                    $requestStatusCriterionArr[] = "request.status='approved'";
//                }
//                if ($cancellationRequestApproved) {
//                    $requestStatusCriterionArr[] = "request.status='canceled'";
//                }
//            }

            if( count($requestStatusCriterionArr) > 0 ) {
                $dql->andWhere(implode(" OR ", $requestStatusCriterionArr));
                $filtered = true;
            }
        }

//        if( $cancellationRequest || $cancellationRequestApproved || $cancellationRequestRejected ) {
//            $requestStatusCriterionArr = array();
//            if ($cancellationRequest) {
//                $requestStatusCriterionArr[] = "request.extraStatus = 'Cancellation Requested'";
//            }
//            if ($cancellationRequestApproved) {
//                $requestStatusCriterionArr[] = "request.extraStatus = 'Cancellation Approved (Canceled)'";
//            }
//            if ($cancellationRequestRejected) {
//                $requestStatusCriterionArr[] = "request.extraStatus = 'Cancellation Denied (Approved)'";
//            }
//
//            $dql->andWhere(implode(" OR ",$requestStatusCriterionArr));
//            $filtered = true;
//        }

        $filterRes['form'] = $filterform;
        $filterRes['dqlParameters'] = $dqlParameters;
        $filterRes['filtered'] = $filtered;
        //$filterRes['requestTypeAbbreviation'] = $requestTypeAbbreviation;


        return $filterRes;
    }
    //convert given datetime from user's timezone to UTC. Use UTC in DB query. 12:00 => 17:00 +5
    public function convertFromUserTimezonetoUTC($datetime,$user) {

        //$user_tz = 'America/New_York';
        $user_tz = $user->getPreferences()->getTimezone();

        //echo "input datetime=".$datetime->format('Y-m-d H:i')."<br>";
        $datetimeTz = new \DateTime($datetime->format('Y-m-d'), new \DateTimeZone($user_tz) );
        $datetimeUTC = $datetimeTz->setTimeZone(new \DateTimeZone('UTC'));
        //echo "output datetime=".$datetimeUTC->format('Y-m-d H:i')."<br>";

        return $datetimeUTC;
    }


    /**
     * @Route("/floating-day-request", name="vacreq_floating_day", methods={"GET","POST"})
     * @Template("AppVacReqBundle/FloatingDay/floating-day.html.twig")
     */
    public function FloatingDayAction(Request $request) {

        if(
            false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_OBSERVER') &&
            false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_SUBMITTER') &&
            false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_APPROVER') &&
            false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_SUPERVISOR')
        ) {
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }

        //$testing = true;
        $testing = false;

        $userSecUtil = $this->container->get('user_security_utility');
        $userServiceUtil = $this->get('user_service_utility');
        $vacreqUtil = $this->get('vacreq_util');
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $entity = new VacReqRequestFloating($user);
        
        $params = array();
        $params['em'] = $em;
        //$params['supervisor'] = $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_SUPERVISOR');

//        $floatingNote = "The Juneteenth Holiday may be used as a floating holiday
//        only if you have an NYPH appointment. You can request a floating holiday however,
//        it must be used in the same fiscal year ending June 30, 2022. It cannot be carried over";
        $floatingNote = $userSecUtil->getSiteSettingParameter('floatingDayNote','vacreq');

        //$title = "Floating Day (The page and functionality are under construction!)";
        $title = $userSecUtil->getSiteSettingParameter('floatingDayName','vacreq');
        $title = $title . " - The page and functionality are under construction!";

        $cycle = 'new';

        $form = $this->createRequestForm($entity,$cycle,$request);

        $form->handleRequest($request);


        if( $form->isSubmitted() && $form->isValid() ) { //new
            //exit("Submitted floating day request");

            if( $testing == false ) {
                $em->persist($entity);
                $em->flush();
            }


            $requestName = $entity->getRequestName(); //"Floating Day Request";
            $emailUtil = $this->get('user_mailer_utility');
            //$break = "\r\n";
            $break = "<br>";

            //set confirmation email to submitter and approver and email users
            $css = null;
            $personAway = $entity->getUser();
            $personAwayEmail = $personAway->getSingleEmail();

            if( $personAway->getId() != $user->getId() ) {
                //cc to submitter
                $css = $user->getSingleEmail();
            }

            if( !$personAwayEmail ) {
                //throw $this->createNotFoundException("Person email is null: personAway=".$personAway);
                $personAwayEmail = $css;
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    "Away person's email is not set. Sent confirmation email to $personAwayEmail instead."
                );
            }

            $subject = $requestName." ID #".$entity->getId()." Confirmation";
            $message = "Dear ".$entity->getUser()->getUsernameOptimal().",".$break.$break;

            $message .= "You have successfully submitted the ".$requestName." #".$entity->getId().".";
            $message .= $break.$break.$entity->printRequest($this->container)."".$break;

            $message .= $break."You will be notified once your request is reviewed and its status changes.";
            $message .= $break.$break."**** PLEASE DO NOT REPLY TO THIS EMAIL ****";
            $emailUtil->sendEmail( $personAwayEmail, $subject, $message, $css, null );

            //set confirmation email to approver and email users
            $approversNameStr = $this->sendConfirmationEmailToFloatingApprovers( $entity );

            //Event Log
            $event = $requestName . " for ".$entity->getUser()." has been submitted.".
                " Confirmation email has been sent to ".$approversNameStr;
            $event = $event . $break.$break. $entity->printRequest();

            if( $testing == false ) {
                $userSecUtil->createUserEditEvent($this->getParameter('vacreq.sitename'), $event, $user, $entity, $request, $eventType);
            }

            //exit('exit event='.$event);

            //Flash
            $this->get('session')->getFlashBag()->add(
                'notice',
                $event
            );

            //return $this->redirectToRoute('vacreq_show', array('id' => $entity->getId()));
            return $this->redirectToRoute('vacreq_floating_show');
        }
        
        

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'cycle' => $cycle,
            'title' => $title,
            'floatingNote' => $floatingNote
        );
    }

    /**
     * @Route("/show/floating/{id}", name="vacreq_floating_show", methods={"GET"})
     *
     * @Template("AppVacReqBundle/FloatingDay/floating-day.html.twig")
     */
    public function showAction(Request $request, $id)
    {
        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_USER') ) {
            //exit('show: no permission');
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppVacReqBundle:VacReqRequestFloating')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Floating Day Request by id='.$id);
        }

        //TODO: adjust the permission: AdminController->addVacReqPermission
        if( false == $this->get('security.authorization_checker')->isGranted("read", $entity) ) {
            //exit('show: no permission');
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }
        //exit('show: ok permission');

        //echo "req=".$entity->printRequest($this->container);
        //exit('1');

        $cycle = 'show';

        //get request type
        $title = "Floating Day Request";

        $form = $this->createRequestForm($entity,$cycle,$request);

        return array(
            'entity' => $entity,
            'cycle' => $cycle,
            'form' => $form->createView(),
            'title' => $title
            //'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edit: Displays a form to edit an existing VacReqRequest entity.
     *
     * @Route("/edit/floating/{id}", name="vacreq_floating_edit", methods={"GET", "POST"})
     * @Route("/review/floating/{id}", name="vacreq_floating_review", methods={"GET", "POST"})
     *
     * @Template("AppVacReqBundle/FloatingDay/edit.html.twig")
     */
    public function editAction(Request $request, $id) {
        $logger = $this->container->get('logger');
        $em = $this->getDoctrine()->getManager();
        $vacreqUtil = $this->get('vacreq_util');
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $entity = $em->getRepository('AppVacReqBundle:VacReqRequestFloating')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Floating Day Request by id='.$id);
        }

        //check permission
        $routName = $request->get('_route');
        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN') ) {
            if ($routName == 'vacreq_floating_review') {
                if (false == $this->get('security.authorization_checker')->isGranted("changestatus", $entity)) {
                    //exit("vacreq_floating_review: no permission to changestatus");
                    $this->get('session')->getFlashBag()->add(
                        'warning',
                        "no permission to review/change the status of this floating day request."
                    );
                    return $this->redirect($this->generateUrl('vacreq-nopermission'));
                }
            } else {
                if (false == $this->get('security.authorization_checker')->isGranted("update", $entity)) {
                    //exit('vacreq_edit: no permission to update');
                    $this->get('session')->getFlashBag()->add(
                        'warning',
                        "no permission to update this floating day request."
                    );
                    return $this->redirect($this->generateUrl('vacreq-nopermission'));
                }
            }
        }
        //exit('testing: approval of carry over request OK'); //testing

        $cycle = 'edit';
        if( $routName == 'vacreq_floating_review' ) {
            $cycle = 'review';
        }

        $changedInfoArr = array();

        $originalTentativeStatus = $entity->getTentativeStatus();
        $originalStatus = $entity->getStatus();
        //$originalCarryOverDays = $entity->getCarryOverDays();

//            $carryOverWarningMessage = null;
//            $carryOverWarningMessageLog = null;

        $form = $this->createRequestForm($entity,$cycle,$request);

        $form->handleRequest($request);

        //check for overlapped date range
        $overlappedRequests = $this->checkFloatingRequestForOverlapDates($entity->getUser(), $entity);    //check for editAction
        //echo 'overlappedRequests count='.count($overlappedRequests)."<br>";
        if (count($overlappedRequests) > 0) {
            //$errorMsg = 'This request has overlapped vacation date range with a previous approved vacation request(s) with ID #' . implode(',', $overlappedRequestIds);
            $errorMsg = $this->getOverlappedMessage( $entity, $overlappedRequests, true ); //edit, review
            $form->addError(new FormError($errorMsg));
        } else {
            //exit('no overlaps found');
        }

        if( $form->isSubmitted() && $form->isValid() ) { //edit, review

            //exit("Review request");

            /////////////// log status ////////////////////////
            $statusMsg = $entity->getId()." (".$routName.")".": set by user=".$user;
            $statusF = $entity->getStatus();
            $statusMsg = $statusMsg . " status=".$statusF;
            $logger->notice($statusMsg);
            /////////////// EOF log status ////////////////////////

            //exit('form is valid');
            if( $routName == 'vacreq_floating_review' ) { //review
                ///////////////// review //////////////////////////

                //set final (global) status according to sub-requests status:
                //only two possible actions: reject or approved
                $entity->setFinalStatus(); //vacreq_floating_review

                $overallStatus = $entity->getStatus();

                if( $overallStatus == "pending" ) {
                    $entity->setApprover(null);
                } else {
                    $entity->setApprover($user);
                }
                //exit('vacreq: overallStatus='.$overallStatus."; status=".$entity->getDetailedStatus());

                //$entity->setExtraStatus(NULL);
                $em->persist($entity);
                $em->flush();

                $eventType = 'Floating Day Request '.ucwords($overallStatus);
                $action = $overallStatus;

                //send respond email for the request changed by the form
                $vacreqUtil->sendSingleRespondEmailToSubmitter( $entity, $user, $overallStatus );

            }//$routName == 'vacreq_floating_review'
            else
            {
                ///////////////// update vacreq_edit (edit page does not allow to change status) //////////////////////////

                $entity->setUpdateUser($user);

                /////////////// Add event log on edit (edit or add collection) ///////////////
                /////////////// Must run before flash DB. When DB is flashed getEntityChangeSet() will not work ///////////////
                $changedInfoArr = $vacreqUtil->setEventLogChanges($entity);

                $em->persist($entity);
                $em->flush();
                //echo "1 business req=".$entity->getRequestBusiness()."<br>";
                //exit('1');

                $action = "updated";

                $eventType = 'Floating Day Request Updated';

            } //if else: review or update

            if( $action == 'pending' ) {
                $action = 'set to Pending';
            }

            //Event Log
            //$break = "\r\n";
            $break = "<br>";
            $event = "Request ID #".$entity->getID()." for ".$entity->getUser()." has been ".$action." by ".$user.$break;
            $event .= $entity->getDetailedStatus().$break.$break;
            $userSecUtil = $this->container->get('user_security_utility');

            //Flash
            $this->get('session')->getFlashBag()->add(
                'notice',
                $event
            );

            //set event log for objects
            if( count($changedInfoArr) > 0 ) {
                //$user = $this->get('security.token_storage')->getToken()->getUser();
                $event .= "Updated Data:".$break;
                $event .= implode("<br>", $changedInfoArr);
            }

            $userSecUtil->createUserEditEvent($this->getParameter('vacreq.sitename'),$event,$user,$entity,$request,$eventType);

            if( $routName == 'vacreq_floating_review' ) {
                return $this->redirectToRoute('vacreq_floatingrequests');
            } else {
                return $this->redirectToRoute('vacreq_floating_show', array('id' => $entity->getId()));
            }

        }

        $review = false;
        if( $request ) {
            if( $request->get('_route') == 'vacreq_floating_review' ) {
                $review = true;
            }
        }

        //get request type
        if( $entity->getRequestTypeAbbreviation() == "carryover" ) {
            $title = "Request carry over of vacation days";
        } else {
            $title = "Vacation/Business Travel Request";
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'cycle' => $cycle,
            'review' => $review,
            'title' => $title,
            'carryOverWarningMessage' => $carryOverWarningMessage
            //'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * approved, rejected, pending, canceled
     * @Route("/status/floating/{id}/{status}", name="vacreq_floating_status_change", methods={"GET"})
     * @Route("/estatus/floating/{id}/{status}", name="vacreq_floating_status_email_change", methods={"GET"})
     * @Template("AppVacReqBundle/FloatingDay/floating-day.html.twig")
     */
    public function statusAction(Request $request, $id, $status) {

        //if( false == $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_APPROVER') ) {
        //    return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        //}

        $logger = $this->container->get('logger');
        $em = $this->getDoctrine()->getManager();
        $routeName = $request->get('_route');
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $vacreqUtil = $this->get('vacreq_util');

        $entity = $em->getRepository('AppVacReqBundle:VacReqRequest')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Request by id='.$id);
        }

        $originalStatus = $entity->getStatus();

        /////////////// log status ////////////////////////
        $logger->notice("RequestController statusAction: ".$entity->getId()." (".$routeName.")".": status=".$status."; set by user=".$user);
        /////////////// EOF log status ////////////////////////

        if( $this->get('security.authorization_checker')->isGranted("changestatus", $entity) ) {
            //Approvers can change status to anything
        } elseif( $this->get('security.authorization_checker')->isGranted("update", $entity) ) {
            //Owner can only set status to: canceled, pending
            if( $status != "canceled" && $status != "pending" ) {
                //Flash
                $this->get('session')->getFlashBag()->add(
                    'warning',
                    "You can not change status of this ".$entity->getRequestName()." with ID #".$entity->getId()." to ".$status
                );
                $logger->error($user." has no permission to change status to ".$status." for request ID #".$entity->getId().". Reason: request is not pending or canceled");
                return $this->redirect($this->generateUrl('vacreq-nopermission'));
            }
        } else {
            $logger->error($user." has no permission to change status to ".$status." for request ID #".$entity->getId().". Reason: user does not have permission to changestatus or update for this request");
            return $this->redirect($this->generateUrl('vacreq-nopermission'));
        }

        //if not pending and vacreq_status_email_change => redirect to incoming request page
        if( $entity->getStatus() != "pending" && $routeName == 'vacreq_floating_status_email_change' ) {
            $modificationDate = "";
            $updateDate = $entity->getUpdateDate();
            if( $updateDate ) {
                $modificationDate = " on ".$updateDate->format('m/d/Y H:i:s');
            }
            //Flash
            $this->get('session')->getFlashBag()->add(
                'notice',
                //"This ".$entity->getRequestName()." ID #" . $entity->getId()." has already been completed by ".$entity->getApprover()
                "This ".$entity->getRequestName()." ID #" . $entity->getId()." is not pending anymore and has been modified by ".$entity->getUpdateUser().$modificationDate
            );
            return $this->redirectToRoute('vacreq_floatingrequests');
        }


        //check for overlapped date range if a new status is approved
        if( $status == "approved" ) {
            $overlappedRequests = $this->checkFloatingRequestForOverlapDates($entity->getUser(), $entity); //check for statusAction
            //exit("count=".count($overlappedRequests));
            if (count($overlappedRequests) > 0) {
                $errorMsg = $vacreqUtil->getOverlappedMessage( $entity, $overlappedRequests );  //change status: approved, rejected, pending, canceled
                $this->get('session')->getFlashBag()->add(
                    'warning',
                    $errorMsg
                );
                return $this->redirectToRoute('vacreq_show',array('id'=>$entity->getId()));
            } else {
                //exit('no overlaps found');
            }
        }
        
        if( $status ) {

            $statusSet = false;

            $entity->setStatus($status);
            
//            if( $status == 'pending' ) {
//                $entity->setStatus('pending');
//            } else {
//                $entity->setStatus('completed');
//            }
            $statusSet = true;

            if( $statusSet ) {

                if( $status == "pending" ) {
                    $entity->setApprover(null);
                } else {
                    $entity->setApprover($user);
                }

                //$entity->setExtraStatus(NULL);
                $em->persist($entity);
                $em->flush();
                
                //send respond confirmation email to a submitter
                if( $status == 'canceled' ) {
                    //an email should be sent to approver saying
                    // "FirstName LastName canceled/withdrew their business travel / vacation request described below:"
                    // and list all variable names and values in the email.
                    $approversNameStr = $vacreqUtil->sendCancelEmailToApprovers( $entity, $user, $status );
                } else {
                    $approversNameStr = null;
                    //send confirmation email by express link to change status (email or link in the list)
                    $vacreqUtil->sendSingleRespondEmailToSubmitter( $entity, $user, $status );
                }

                $removeCarryoverStr = "";
//                if( $entity->getRequestTypeAbbreviation() == "carryover" && $status == "canceled" && $originalStatus == "approved" ) {
//                    //TODO: reset user's VacReqUserCarryOver object? Take care of this case by syncVacReqCarryOverRequest
//                    //reset user's VacReqUserCarryOver object: remove VacReqCarryOver for this canceled request year
//                    $removeCarryoverStr = " ".$vacreqUtil->deleteCanceledVacReqCarryOverRequest($entity).".";
//                }
                //exit("test");

                //Flash
                $statusStr = $status;
                if( $status == 'pending' ) {
                    $statusStr = 'set to Pending';
                }

                //re-submit request
                if( $status == "pending" && $originalStatus == "canceled" ) {
                    //send a confirmation email to approver //sendConfirmationEmailToApprovers -> sendConfirmationEmailToFloatingApprovers
                    $approversNameStr = $vacreqUtil->sendConfirmationEmailToFloatingApprovers( $entity );
                    $statusStr = 're-submitted';
                }

                $event = ucwords($requestName)." ID #" . $entity->getId() . " for " . $entity->getUser() . " has been " . $statusStr . " by " . $user;
                $event .= ": ".$entity->getDetailedStatus().".";
                if( $approversNameStr ) {
                    $event .= " Confirmation email(s) have been sent to ".$approversNameStr.".";
                }

                $event .= $removeCarryoverStr;

                $this->get('session')->getFlashBag()->add(
                    'notice',
                    $event
                );

                $eventType = 'Business/Vacation Request Updated';

                //Event Log
                $userSecUtil = $this->container->get('user_security_utility');
                $userSecUtil->createUserEditEvent($this->getParameter('vacreq.sitename'), $event, $user, $entity, $request, $eventType);

            }

        }

        //redirect to myrequests for owner
        if( $entity->getUser()->getId() == $user->getId() ) {
            return $this->redirectToRoute("vacreq_myrequests",array('filter[requestType]'=>$entity->getRequestType()->getId()));
        }

        $url = $request->headers->get('referer');
        //exit('url='.$url);

        //when status is changed from email, then the url is a system home page
        if( $url && strpos($url, 'incoming-requests') !== false ) {
            return $this->redirect($url);
        }

        //return $this->redirectToRoute('vacreq_show', array('id' => $entity->getId()));
        return $this->redirectToRoute('vacreq_floatingrequests');
    }

    /**
     * @Route("/send-reminder-email/floating/{id}", name="vacreq_floating_send_reminder_email", methods={"GET"})
     */
    public function sendReminderEmailAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        //$user = $this->get('security.token_storage')->getToken()->getUser();

        $entity = $em->getRepository('AppVacReqBundle:VacReqRequestFloating')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Floating Request by id=' . $id);
        }

        //check permissions
//        if( false == $this->get('security.authorization_checker')->isGranted("update", $entity) && false === $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_SUPERVISOR')) {
//            return $this->redirect($this->generateUrl('vacreq-nopermission'));
//        }
        if(
            $this->get('security.authorization_checker')->isGranted("read", $entity) ||
            $this->get('security.authorization_checker')->isGranted("update", $entity) ||
            $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN') ||
            $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_SUPERVISOR')
        )
        {
            //OK send reminder email: read, supervisor
        } else {
            return $this->redirect($this->generateUrl('vacreq-nopermission'));
        }

        //set confirmation email to approver and email users
        $vacreqUtil = $this->get('vacreq_util');
        $approversNameStr = $vacreqUtil->sendConfirmationEmailToFloatingApprovers( $entity );

        $eventSubject = 'Reminder email(s) has been sent to '.$approversNameStr;

        //Flash
        $this->get('session')->getFlashBag()->add(
            'notice',
            $eventSubject
        );

        return $this->redirectToRoute("vacreq_myrequests",array('filter[requestType]'=>$entity->getRequestType()->getId()));
    }


    public function getOverlappedMessage( $subjectRequest, $overlappedRequests, $absolute=null, $short=false ) {
        //$errorMsg = 'This request ID #'.$entity->getId().' has overlapped vacation date range with a previous approved vacation request(s) with ID #' . implode(',', $overlappedRequestIds);
        $errorMsg = null;
        //Your request includes dates (MM/DD/YYYY, MM/DD/YYYY, MM/DD/YYYY) already covered by your previous request(s) (Request ID LINK #1, Request ID LINK #2, Request ID LINK #3). Please exclude these dates from this request before re-submitting.
        if( count($overlappedRequests) > 0 ) {
            $dates = array();
            $hrefs = array();
            foreach( $overlappedRequests as $overlappedRequest ) {
                //echo "overlapped re id=".$overlappedRequest->getId()."<br>";

                $finalDay = $overlappedRequest->getFloatingDay();
                $dates[] = $finalDay->format('m/d/Y');

                if( $absolute ) {
                    $absoluteFlag = UrlGeneratorInterface::ABSOLUTE_URL;
                } else {
                    $absoluteFlag = null;
                }
                $link = $this->container->get('router')->generate(
                    'vacreq_floating_show',
                    array(
                        'id' => $overlappedRequest->getId(),
                    ),
                    $absoluteFlag
                //UrlGeneratorInterface::ABSOLUTE_URL
                );
                if( $absolute ) {
                    $href = 'Request ID '.$overlappedRequest->getId()." ".$link;
                } else {
                    $href = '<a href="'.$link.'">Request ID '.$overlappedRequest->getId().'</a>';
                }

                $hrefs[] = $href;
            }

            $errorMsg = "This floating day request includes dates ".implode(", ",$dates)." already covered by your previous request(s) (".implode(", ",$hrefs).").";

            if( !$short ) {
                $errorMsg .= " Please exclude this day from this floating day request before submitting.";
            }
        }

        return $errorMsg;
    }

    public function createRequestForm( $entity, $cycle, $request ) {

        $em = $this->getDoctrine()->getManager();
        $userServiceUtil = $this->get('user_service_utility');
        $vacreqUtil = $this->get('vacreq_util');
        $routeName = $request->get('_route');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $admin = false;
        if( $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN') ) {
            $admin = true;
        }

        $roleApprover = false;
        if( $this->get('security.authorization_checker')->isGranted("changestatus", $entity) ) {
            $roleApprover = true;
        }

        $groupParams = array();

        $groupParams['permissions'][] = array('objectStr'=>'VacReqRequest','actionStr'=>'create');
        if( $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN') == false ) {
            $groupParams['exceptPermissions'][] = array('objectStr' => 'VacReqRequest', 'actionStr' => 'changestatus-carryover');
        }

        $tentativeInstitutions = null;

        $organizationalInstitutions = $vacreqUtil->getGroupsByPermission($user,$groupParams);
        //echo "1 organizationalInstitutions count=".count($organizationalInstitutions)."<br>";

        //include this request institution to the $organizationalInstitutions array
        $organizationalInstitutions = $vacreqUtil->addRequestInstitutionToOrgGroup( $entity, $organizationalInstitutions );
        //echo "2 organizationalInstitutions count=".count($organizationalInstitutions)."<br>";

        //include this request institution to the $tentativeInstitutions array
        //$tentativeInstitutions = $vacreqUtil->addRequestInstitutionToOrgGroup( $entity, $tentativeInstitutions, "tentativeInstitution" );

        if( count($organizationalInstitutions) == 0 ) {
            //If count($organizationalInstitutions) == 0 then try to run http://hosthame/order/directory/admin/sync-db/

            if( $this->get('security.authorization_checker')->isGranted('ROLE_VACREQ_ADMIN') ) {
                //admin user
                $groupPageUrl = $this->generateUrl(
                    "vacreq_approvers",
                    array(),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                $warningMsg = "You don't have any group and/or assigned Submitter roles for the Business/Vacation Request site.".
                    ' <a href="'.$groupPageUrl.'" target="_blank">Please create a group and/or assign a Submitter role to your user account.</a> ';
            } else {
                //regular user
                $adminUsers = $em->getRepository('AppUserdirectoryBundle:User')->findUserByRole("ROLE_VACREQ_ADMIN", "infos.lastName", true);
                $emails = array();
                foreach ($adminUsers as $adminUser) {
                    $singleEmail = $adminUser->getSingleEmail();
                    if ($singleEmail) {
                        $emails[] = $adminUser->getSingleEmail();
                    }
                }
                $emailStr = "";
                if (count($emails) > 0) {
                    $emailStr = " Administrator email(s): " . implode(", ", $emails);
                }

                $warningMsg = "You don't have any group and/or assigned Submitter roles for the Business/Vacation Request site.".
                    " Please contact the site administrator to create a group and/or get a Submitter role for your account.".$emailStr;
            }
            //Flash
            $this->get('session')->getFlashBag()->add(
                'warning',
                $warningMsg
            );
        }

        //get holidays url
        $userSecUtil = $this->container->get('user_security_utility');
        $holidaysUrl = $userSecUtil->getSiteSettingParameter('holidaysUrl','vacreq');
        if( !$holidaysUrl ) {
            throw new \InvalidArgumentException('holidaysUrl is not defined in Site Parameters.');
        }
        $holidaysUrl = '<a target="_blank" href="'.$holidaysUrl.'">holidays</a>';

        //echo "roleApprover=".$roleApprover."<br>";
        //echo "roleCarryOverApprover=".$roleCarryOverApprover."<br>";

        $params = array(
            'container' => $this->container,
            'em' => $em,
            'user' => $entity->getUser(),
            'cycle' => $cycle,
            'roleAdmin' => $admin,
            'roleApprover' => $roleApprover,
            'organizationalInstitutions' => $userServiceUtil->flipArrayLabelValue($organizationalInstitutions),
            'tentativeInstitutions' => $userServiceUtil->flipArrayLabelValue($tentativeInstitutions),
            'holidaysUrl' => $holidaysUrl,
            'maxCarryOverVacationDays' => $userSecUtil->getSiteSettingParameter('maxCarryOverVacationDays','vacreq'),
            'noteForCarryOverDays' => $userSecUtil->getSiteSettingParameter('noteForCarryOverDays','vacreq'),
            //'maxVacationDays' => $userSecUtil->getSiteSettingParameter('maxVacationDays','vacreq'),
            //'noteForVacationDays' => $userSecUtil->getSiteSettingParameter('noteForVacationDays','vacreq'),
        );
        
        //set default floating day
        $floatingDayType = NULL;
        $parameters = array();
        $repository = $em->getRepository('AppVacReqBundle:VacReqFloatingTypeList');
        $dql = $repository->createQueryBuilder('list');
        $dql->andWhere("(list.type = :typedef OR list.type = :typeadd)");
        $dql->orderBy("list.orderinlist","ASC");
        $parameters['typedef'] = 'default';
        $parameters['typeadd'] = 'user-added';
        $query = $em->createQuery($dql);
        if( count($parameters) > 0 ) {
            $query->setParameters($parameters);
        }
        $floatingDayTypes = $query->getResult();
        if( count($floatingDayTypes) > 0 ) {
            $floatingDayType = $floatingDayTypes[0];
        }

        $params['defaultFloatingType'] = $floatingDayType;

        $disabled = false;
        $method = 'GET';

        if( $cycle == 'show' ) {
            $disabled = true;
        }

        if( $cycle == 'new' ) {
            $method = 'POST';
        }

        if( $cycle == 'edit' ) {
            $method = 'POST';
        }

        if( $cycle == 'review' ) {
            $method = 'POST';
        }

        $params['review'] = false;
        if( $request ) {
            if( $routeName == 'vacreq_floating_review' ) {
                $params['review'] = true;
            }
        }

        $form = $this->createForm(
            VacReqRequestFloatingType::class,
            $entity,
            array(
                'form_custom_value' => $params,
                'disabled' => $disabled,
                'method' => $method,
                //'action' => $action
            )
        );

        return $form;
    }

    public function sendConfirmationEmailToFloatingApprovers( $entity, $sendCopy=true ) {
        $vacreqUtil = $this->get('vacreq_util');
        $subject = $entity->getEmailSubject();
        $message = $this->createFloatingEmailBody($entity);
        return $vacreqUtil->sendGeneralEmailToApproversAndEmailUsers($entity,$subject,$message,$sendCopy);
    }
    public function createFloatingEmailBody( $entity, $emailToUser=null, $addText=null, $withLinks=true ) {

        //$break = "\r\n";
        $break = "<br>";

        $submitter = $entity->getUser();

        //$message = "Dear " . $emailToUser->getUsernameOptimal() . "," . $break.$break;
        $message = "Dear ###emailuser###," . $break.$break;

        $requestName = $entity->getRequestName();

        $message .= $submitter->getUsernameOptimal()." has submitted the ".$requestName." ID #".$entity->getId()." and it is ready for review.";
        $message .= $break.$break.$entity->printRequest($this->container)."";

        $reviewRequestUrl = $this->container->get('router')->generate(
            'vacreq_floating_review',
            array(
                'id' => $entity->getId()
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $message .= $break . $break . "Please follow the link below to review ".$requestName." ID #".$entity->getId().":" . $break;
        $message .= $reviewRequestUrl . $break . $break;

        //$message .= $break . "Please click on the URLs below for quick actions to approve or reject ".$requestName." ID #".$entity->getId().".";

        //href="{{ path(vacreq_sitename~'_status_email_change', { 'id': entity.id,  'requestName':requestName, 'status': 'approved' }) }}
        //approved
        $actionRequestUrl = $this->container->get('router')->generate(
            'vacreq_floating_status_email_change',
            array(
                'id' => $entity->getId(),
                'status' => 'approved'
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $message .= $break . $break . "Please follow the link below to Approve the ".$requestName." ID #".$entity->getId().":" . $break;
        $message .= $actionRequestUrl;

        //rejected
        $actionRequestUrl = $this->container->get('router')->generate(
            'vacreq_floating_status_email_change',
            array(
                'id' => $entity->getId(),
                'status' => 'rejected'
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $message .= $break . $break . "Please follow the link below to Reject the ".$requestName." ID #".$entity->getId().":" . $break;
        $message .= $actionRequestUrl;

        $message .= $break.$break."To approve or reject requests, Division Approvers must be on site or using vpn when off site.";
        $message .= $break.$break."**** PLEASE DO NOT REPLY TO THIS EMAIL ****";

        if( $addText ) {
            $message = $addText.$break.$break.$message;
        }

        return $message;
    }

    public function checkFloatingRequestForOverlapDates( $user, $subjectRequest ) {
        //$logger = $this->container->get('logger');
        //get all user approved vacation requests
        $requestTypeStr = "requestVacation";

        $repository = $this->em->getRepository('AppVacReqBundle:VacReqRequestFloating');

        $dql =  $repository->createQueryBuilder("request");
        $dql->select('request');

        $dql->leftJoin("request.user", "user");
        
        $dql->andWhere("requestVacation.status = 'approved'");
        $dql->andWhere("user.id = :userId");
        $dql->andWhere("request.id != :requestId");

        $dql->orderBy('request.id');

        $query = $this->em->createQuery($dql);

        $query->setParameter('userId', $user->getId());
        $query->setParameter('requestId', $subjectRequest->getId());

        $requests = $query->getResult();
        //EOF get all user approved vacation requests

        $overlappedRequests = array();

        $subjectDay = $subjectRequest->getFloatingDay();
        //dump($subjectDateRange);
        if( !$subjectDay ) {
            return $overlappedRequests;
        }

        //$overlappedIds = array();
        foreach( $requests as $request ) {
            //echo 'check reqid='.$request->getId()."<br>";
            $thisDay = $request->getFloatingDay();
            if( $thisDay == $subjectDay ) {
                $overlappedRequests[] = $request;
            }
        }//foreach requests

        return $overlappedRequests;
    }

}
