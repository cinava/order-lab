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
 *
 *  Created by Oleg Ivanov
 */

namespace App\UserdirectoryBundle\Controller;

use App\UserdirectoryBundle\Entity\EmploymentStatus;
use App\UserdirectoryBundle\Form\UserDatesFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\UserdirectoryBundle\Controller\OrderAbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserDatesController extends OrderAbstractController
{

    /**
     * @Route("/employment-dates/view", name="employees_user_dates_show", options={"expose"=true})
     * @Route("/employment-dates/edit", name="employees_user_dates_edit", options={"expose"=true})
     * @Template("AppUserdirectoryBundle/UserDates/user_dates.html.twig")
     */
    public function userDatesAction( Request $request ) {

        if( false === $this->isGranted('ROLE_USERDIRECTORY_EDITOR') ) {
            return $this->redirect( $this->generateUrl('employees-nopermission') );
        }

        $cycle = 'show';
        if( $request->get('_route') == 'employees_user_dates_edit' ) {
            $cycle = 'edit';
        }

        $params = array();
        $filterform = $this->createForm(UserDatesFilterType::class, null, array(
            'method' => 'GET',
            'form_custom_value' => $params
        ));

        $filterform->handleRequest($request);

        if( $filterform->isSubmitted() && $filterform->isValid() ) {
            $users = $filterform["users"]->getData();
            //$useridsArr = array();
            //foreach( $users as $thisUser ) {
            //    $useridsArr[] = $thisUser->getId();
            //}
            //$userids = implode("-",$useridsArr);
        }

        $users = array();

        return array(
            'title' => 'Employment dates',
            'entities' => $users,
            'filterform' => $filterform->createView(),
            'cycle' => $cycle
        );
    }

    /**
     * @Route("/users/api", name="employees_users_api", options={"expose"=true})
     */
    public function getUsersApiAction( Request $request ) {

        if( false === $this->isGranted('ROLE_USERDIRECTORY_EDITOR') ) {
            return $this->redirect( $this->generateUrl('employees-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository('AppUserdirectoryBundle:User');
        $dql =  $repository->createQueryBuilder("user");
        $dql->select('user');
        $dql->leftJoin("user.infos","infos");

        $dql->leftJoin("user.employmentStatus", "employmentStatus");
        $dql->leftJoin("employmentStatus.employmentType", "employmentType");
        $dql->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL");

        $params = array();
        $filterform = $this->createForm(UserDatesFilterType::class, null, array(
            'method' => 'GET',
            'form_custom_value' => $params
        ));

        $filterform->handleRequest($request);

        $queryParameters = array();
        if( $filterform->isSubmitted() && $filterform->isValid() ) {
            //echo "filterform=OK <br>";
            //dump($filterform);

            $users = $filterform["users"]->getData();
            if( $users && count($users) > 0 ) {
                //echo "users=OK <br>";
                $useridsArr = array();
                foreach ($users as $thisUser) {
                    $useridsArr[] = $thisUser->getId();
                }
                //$userids = implode(",",$useridsArr);
                $dql->andWhere('user.id IN (:userids)');
                $queryParameters['userids'] = $useridsArr;
            }

            $search = $filterform["search"]->getData();
            //echo "search=$search <br>";
            if( $search ) {
                $searchStr = "user.primaryPublicUserId LIKE :search";
                //$searchStr = $searchStr . " OR " . "user.id LIKE :search";
                $searchStr = $searchStr . " OR " . "LOWER(infos.email) LIKE :search";
                $searchStr = $searchStr . " OR " . "LOWER(infos.lastName) LIKE :search";
                $searchStr = $searchStr . " OR " . "LOWER(infos.firstName) LIKE :search";
                $searchStr = $searchStr . " OR " . "LOWER(infos.displayName) LIKE :search";
                $searchStr = $searchStr . " OR " . "infos.preferredPhone LIKE :search";
                $searchStr = $searchStr . " OR " . "LOWER(user.usernameCanonical) LIKE :search";

                $dql->andWhere($searchStr);
                //$queryParameters['search'] = $useridsArr;
                $queryParameters['search'] = "%" . strtolower($search) . "%";
            }

            $roles = $filterform["roles"]->getData();
            if( $roles && count($roles) > 0 ) {
                $rolesArr = array();
                foreach ($roles as $role) {
                    $rolesArr[] = "user.roles LIKE " . "'%" . $role->getName() . "%'";
                }
                $rolesStr = implode(" OR ", $rolesArr);
                $dql->andWhere($rolesStr);
            }

            $startdate = $filterform["startdate"]->getData();
            if( $startdate ) {
                //$dql->andWhere('employmentStatus.hireDate ');
                //$dql->andWhere("(employmentStatus.hireDate > :startdate AND :createDateEnd OR request.firstDayBackInOffice between :createDateStart AND :createDateEnd)");
                $dql->andWhere("(employmentStatus.hireDate > :startdate)");
                $startdate = $startdate->format('Y-m-d H:i:s');
                $queryParameters['startdate'] = $startdate;
            }

            $enddate = $filterform["enddate"]->getData();
            if( $enddate ) {
                $dql->andWhere("(employmentStatus.terminationDate < :enddate)");
                $enddate = $enddate->format('Y-m-d H:i:s');
                $queryParameters['enddate'] = $enddate;
            }

//            if( $startdate && $enddate ) {
//
//            }

            $status = $filterform["status"]->getData();
            if( $status ) {
                if( $status == 'locked' ) {
                    //$enabled = false;
                    $dql->andWhere("user.enabled = false");
                }
            }

            //exit('111');
        }
        //exit('111');

        $dql->orderBy("infos.lastName","ASC");
        $query = $em->createQuery($dql);

        $query->setParameters($queryParameters);

        //$query->setMaxResults(30);
        //$totalUsers = $query->getResult();

        $limit = 20; //20;

        $paginationParams = array(
            'defaultSortFieldName' => 'user.id',
            'defaultSortDirection' => 'DESC',
            'wrap-queries' => true
        );

        $page = $request->query->get('page', 1);
        $paginator  = $this->container->get('knp_paginator');
        $users = $paginator->paginate(
            $query,
            $page,   /*page number*/
            $limit,                            /*limit per page*/
            $paginationParams
        );
        //echo "page=".$page.", users=".count($users)."<br>";
        //exit('111');

        $jsonArray = array();
        foreach($users as $user) {

            $showLink = $this->container->get('router')->generate(
                'employees_showuser',
                array(
                    'id' => $user->getId()
                ),
                //UrlGeneratorInterface::ABSOLUTE_PATH
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            //$showLink = ' <a href="' . $showLink . '">'.$user->getId().'</a>';

            $editLink = $this->container->get('router')->generate(
                'employees_user_edit',
                array(
                    'id' => $user->getId()
                ),
                //UrlGeneratorInterface::ABSOLUTE_PATH
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $eventlogLink = $this->container->get('router')->generate(
                'employees_logger_user_all',
                array(
                    'id' => $user->getId()
                ),
                //UrlGeneratorInterface::ABSOLUTE_PATH
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $institutions = $user->getDeduplicatedInstitutions();
            $instNames = array();
            foreach( $institutions as $instRes ) {
                foreach( $instRes as $instId => $instArr ) {
                    $instNames[] = $instArr['instName'];
                }
            }
            $instNameStr = implode("; ", $instNames);

            $degreeTitle = $user->getDegreesTitles();

            $titles = $degreeTitle['title'];

            $degree = $user->getSingleSalutation();

            $startEndDate = $user->getEmploymentStartEndDates();
            $startDate = $startEndDate['startDate'];
            $endDate = $startEndDate['endDate'];

            $status = "Active";
            $terminationStr = $user->getEmploymentTerminatedStr();
            if( $terminationStr ) {
                $status = $terminationStr; //"terminated";
            }

            //get usernametype => local or ldap
            $userKeyTypeAbbreviation = "";
            $userKeyType = $user->getKeyType();
            if( $userKeyType ) {
                $userKeyTypeAbbreviation = $userKeyType->getAbbreviation(); //ldap-user, ldap2-user, local-user
            }

            $cwid = $user->getCleanUsername();

            $jsonArray[] = array(
                'id'            => $user->getId(),
                'cwid'          => $cwid,
                'showLink'      => $showLink,
                'editLink'      => $editLink,
                'eventlogLink'  => $eventlogLink,
                'LastName'      => $user->getSingleLastName(),
                'FirstName'     => $user->getSingleFirstName(),
                'Degree'        => $degree,
                'Email'         => $user->getSingleEmail(),
                'Institution'   => $instNameStr,
                'Title'         => $titles,
                'StartDate'     => $startDate,
                'EndDate'       => $endDate,
                'status'        => $status,
                'keytype'       => $userKeyTypeAbbreviation

            );
        }

        $totalCount = $users->getTotalItemCount();
        //echo "totalCount=$totalCount <br>";
        $totalPages = ceil($totalCount/$limit);
        //echo "totalPages=$totalPages <br>";
        //exit('111');

        $info = array(
            'seed' => "abc",
            'results' => $limit,
            'page' => $page,
            'version' => 1
        );

        $results = array(
            'results' => $jsonArray,
            'info'    => $info,
            'totalPages'   => $totalPages,
            'totalUsers' => $totalCount
        );

        //return new JsonResponse($results);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(200);
        //$response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($results));

        return $response;
    }

    /**
     * @Route("/update-users-dates/", name="employees_update_users_date", options={"expose"=true})
     */
    public function updateUsersDateAction( Request $request ) {
        if( false === $this->isGranted('ROLE_USERDIRECTORY_EDITOR') ) {
            return $this->redirect( $this->generateUrl('employees-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();
        $sitename = $this->getParameter('employees.sitename');
        $results = 'ok';

        $userServiceUtil = $this->container->get('user_service_utility');
        $userSecUtil = $this->container->get('user_security_utility');

        $datas = json_decode($request->getContent(), true);
        //dump($datas);

        $employmentType = $em->getRepository('AppUserdirectoryBundle:EmploymentType')->findOneByName("Full Time");
        if( !$employmentType ) {
            $results = 'Unable to find EmploymentType entity by name='."Full Time";
            //throw new EntityNotFoundException('Unable to find entity by name='."Full Time");
        }

        $eventArr = array();

        foreach($datas as $key=>$thisData) {
            foreach($thisData as $data) {
                //dump($data);
                //exit("111 $key");
                $userId = $data['userId'];
                $startDateStr = $data['startDate'];
                $endDateStr = $data['endDate'];
                //echo "userId=$userId, startDateStr=$startDateStr, endDateStr=$endDateStr <br>";

                if( !$userId ) {
                    $eventArr[] = "User id does not exist";
                    continue;
                }

                $user = $em->getRepository('AppUserdirectoryBundle:User')->find($userId);
                if( !$user ) {
                    $eventArr[] = "User not found by user id ".$userId;
                    continue;
                }

                $event = null;
                $changeArr = array();

                $origianlEnableStatus = $user->isEnabled();

                //Save the start and end dates into the existing array field in the user profile (we have one already - please don’t create a new one)
                //get latest employement status
                $latestEmploymentStatus = $user->getLatestEmploymentStatus();
                if( !$latestEmploymentStatus ) {
                    $eventArr[] = "Latest employment status not found for ".$user;
                    continue;
                }

                $originalStartDate = $latestEmploymentStatus->getHireDate();
                if( $originalStartDate ) {
                    $originalStartDate = $originalStartDate->format('m/d/Y');
                }
                $originalEndDate = $latestEmploymentStatus->getTerminationDate();
                if( $originalEndDate ) {
                    $originalEndDate = $originalEndDate->format('m/d/Y');
                }
                
                if( $startDateStr ) {
                    if( $originalStartDate != $startDateStr ) {
                        $changeArr[] = "Start date changed from $originalStartDate to $startDateStr";
                    }
                    $startDate = \DateTime::createFromFormat('m/d/Y H:i', $startDateStr." 00:00");
                    $startDate = $userServiceUtil->convertFromUserTimezonetoUTC($startDate,$user);
                    //echo "startDate=".$startDate->format('m/d/Y H:i')."<br>";
                    $latestEmploymentStatus->setHireDate($startDate);
                }
                if( $endDateStr ) {
                    if( $originalEndDate != $endDateStr ) {
                        $changeArr[] = "End date changed from $originalEndDate to $endDateStr";
                    }
                    $endDate = \DateTime::createFromFormat('m/d/Y H:i', $endDateStr." 00:00");
                    $endDate = $userServiceUtil->convertFromUserTimezonetoUTC($endDate,$user);
                    //echo "endDate=".$endDate->format('m/d/Y H:i')."<br>";
                    $latestEmploymentStatus->setTerminationDate($endDate);
                }

                //lock user account
                if( $origianlEnableStatus !== false ) {
                    $user->setEnabled(false);
                    $changeArr[] = "User $user is locked by $currentUser";
                }

                if( count($changeArr) > 0 ) {
                    //$user->addEmploymentStatus($employmentStatus);
                    $em->flush();

                    $event = "User profile of ".$user." has been changed by ".$currentUser." with bulk updates:"."<br>";
                    $changeStr = implode("; ", $changeArr);

                    $event = $event . $changeStr;

                    //Event Log
                    $userSecUtil->createUserEditEvent($sitename,$event,$currentUser,$user,$request,'User record updated');
                } else {
                    $event = "User profile of ".$user." has not been changed by ".$currentUser." with bulk updates";
                }

                $eventArr[] = $event . "<br>";
            }
        }

        $eventStr = implode("<br>",$eventArr);
        $this->addFlash(
            'notice',
            $eventStr
        );

        //exit('Not implemented');

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(200);
        //$response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($results));

        return $response;
    }

}