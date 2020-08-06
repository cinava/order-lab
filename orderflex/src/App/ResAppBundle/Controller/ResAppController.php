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

namespace App\ResAppBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use App\ResAppBundle\Entity\ResidencyApplication;
use App\ResAppBundle\Entity\Interview;
use App\ResAppBundle\Form\InterviewType;
use App\UserdirectoryBundle\Entity\User;
use App\OrderformBundle\Helper\ErrorHelper;
//use App\UserdirectoryBundle\Entity\AccessRequest;
use App\UserdirectoryBundle\Entity\Reference;
use App\ResAppBundle\Form\ResAppFilterType;
use App\ResAppBundle\Form\ResidencyApplicationType;
use App\UserdirectoryBundle\Util\UserUtil;
use App\UserdirectoryBundle\Controller\OrderAbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Validator\Constraints\DateTime;


class ResAppController extends OrderAbstractController {

    /**
     * Show home page
     *
     * @Route("/", name="resapp_home")
     * @Route("/my-interviewees/", name="resapp_myinterviewees")
     * @Route("/send-rejection-emails", name="resapp_send_rejection_emails")
     * @Route("/accepted-residents", name="resapp_accepted_residents")
     *
     * @Template("AppResAppBundle/Default/home.html.twig")
     */
    public function indexAction(Request $request) {
        //echo "resapp home <br>";

//        if(
//            false == $this->get('security.authorization_checker')->isGranted('ROLE_USER') ||              // authenticated (might be anonymous)
//            false == $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')    // authenticated (NON anonymous)
//        ){
//            return $this->redirect( $this->generateUrl('resapp_login') );
//        }

//        if(
//            false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_USER')    // authenticated (NON anonymous)
//        ){
//            return $this->redirect( $this->generateUrl('resapp_login') );
//        }

//        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_USER') ){
//            return $this->redirect( $this->generateUrl('resapp-nopermission') );
//        }
//        if( false == $this->get('security.authorization_checker')->isGranted("read","ResidencyApplication") ){
//            exit('no permission as interviewer: ResidencyApplication');
//            //return $this->redirect( $this->generateUrl('resapp-nopermission') );
//        }
//        if(
//            false == $this->get('security.authorization_checker')->isGranted("read","ResidencyApplication") &&
//            false == $this->get('security.authorization_checker')->isGranted("create","Interview")
//        ){
//            return $this->redirect( $this->generateUrl('resapp-nopermission') );
//        }

        //testing
        //$memory_limit = ini_get('memory_limit');
        //echo "before memory_limit=$memory_limit <br>";

        //ini_set('memory_limit', '7168M');

        //testing
        //$memory_limit = ini_get('memory_limit');
        //echo "before memory_limit=$memory_limit <br>";


        $route = $request->get('_route');
        //echo "route".$route."<br>";
        //exit();

        if( $route == "resapp_home" ) {
            if( false == $this->get('security.authorization_checker')->isGranted("read","ResidencyApplication") ){
                return $this->redirect( $this->generateUrl('resapp-nopermission') );
            }
        }

        if( $route == "resapp_myinterviewees" ) {
            if(
                false == $this->get('security.authorization_checker')->isGranted("read","ResidencyApplication") &&
                false == $this->get('security.authorization_checker')->isGranted("create","Interview")
            ){
                return $this->redirect( $this->generateUrl('resapp-nopermission') );
            }
        }

        if( $route == "resapp_send_rejection_emails" ) {
            if(
                false == $this->get('security.authorization_checker')->isGranted("ROLE_RESAPP_COORDINATOR") &&
                false == $this->get('security.authorization_checker')->isGranted("ROLE_RESAPP_DIRECTOR")
            ) {
                return $this->redirect( $this->generateUrl('resapp-nopermission') );
            }
            if( false == $this->get('security.authorization_checker')->isGranted("read","ResidencyApplication") ){
                return $this->redirect( $this->generateUrl('resapp-nopermission') );
            }
        }

        if( $route == "resapp_accepted_residents" ) {
            if( false == $this->get('security.authorization_checker')->isGranted("read","ResidencyApplication") ){
                return $this->redirect( $this->generateUrl('resapp-nopermission') );
            }
        }

        $em = $this->getDoctrine()->getManager();
        $userSecUtil = $this->get('user_security_utility');

        //echo "resapp user ok <br>";

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $resappUtil = $this->container->get('resapp_util');
        $userServiceUtil = $this->get('user_service_utility');

        $enableGoolge = false;
        $searchFlag = false;
        //$currentYear = date("Y")+2;
        $currentYear = date("Y")+1;
        $defaultStartDates = $currentYear;

        $applicationSeasonStartDate = date("Y");
        $defaultApplicationSeasonStartDates = $applicationSeasonStartDate;

        $residencyTypes = $resappUtil->getResidencyTypesByUser($user);
        //echo "residencyTypes count=".count($residencyTypes)."<br>";

        if( count($residencyTypes) == 0 ) {
//            $linkUrl = $this->generateUrl(
//                "residencysubspecialtys-list",
//                array(),
//                UrlGeneratorInterface::ABSOLUTE_URL
//            );
            //$warningMsg = "No residency types (subspecialties) are found for WCMC Pathology and Laboratory Medicine department.";
            //$warningMsg = $warningMsg." ".'<a href="'.$linkUrl.'" target="_blank">Please associate the department with the appropriate residency subspecialties.</a>';
            //$warningMsg = $warningMsg."<br>"."For example, choose an appropriate subspecialty and set the institution to 'Weill Cornell Medical College => Pathology and Laboratory Medicine'";
            $linkUrl = $this->generateUrl(
                "resapp_residencytype_settings",
                array(),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $warningMsg = "No residency tracks are found.";
            $warningMsg = $warningMsg."<br>".'<a href="'.$linkUrl.'" target="_blank">Please add a new residency application track.</a>';

            $this->get('session')->getFlashBag()->add(
                'warning',
                //'No Residency Types (Subspecialties) are found for WCMC Pathology and Laboratory Medicine department.
                // Please assign the WCMC department to the appropriate Residency Subspecialties'
                $warningMsg
            );
            //return $this->redirect( $this->generateUrl('resapp-nopermission') );
            return $this->redirect( $this->generateUrl('resapp-nopermission',array('empty'=>true)) );
        }

        if( $route == "resapp_accepted_residents" ) {
            //pre-set startDate:
            // Years: [Select2] allowing multiselect, dynamically listing years from 1900 to [current year + 4], with 12 years [current year - 10] through [current year + 2] pre-selected by default
            //https://bootstrap-datepicker.readthedocs.io/en/latest/options.html#multidate
            //https://bootstrap-datepicker.readthedocs.io/en/latest/markup.html#daterange
            $currentYearInt = date("Y");
            $currentYearInt = intval($currentYearInt);
            $defaultStartDates = array();
            for($x = $currentYearInt-9; $x <= $currentYearInt; $x++) {
                //echo "The number is: $x <br>";
                $defaultStartDates[] = $x;
            }
            for($x = $currentYearInt+1; $x <= $currentYearInt+2; $x++) {
                //echo "The number is: $x <br>";
                $defaultStartDates[] = $x;
            }
            $defaultStartDates = implode(",",$defaultStartDates);//"2012,2013,2014,2015";
        }
        //echo "currentYear=".$currentYear."<br>";
        //$currentYear1 = date("Y")+2;
        //$currentYear2 = date("Y")+3;
        //$currentYear3 = date("Y")+3;
        //$defaultStartDates = array($currentYear1,$currentYear3,$currentYear3);
        //$defaultStartDates = "2019,2020,2021";
        //$defaultStartDates = "2019 2020 2021";
        //$defaultStartDates = $currentYear;

        //create resapp filter
        $params = array(
            'resTypes' => $userServiceUtil->flipArrayLabelValue($residencyTypes), //flipped
            'defaultStartDates' => $defaultStartDates
        );
        $filterform = $this->createForm(ResAppFilterType::class, null,array(
            'method' => 'GET',
            'form_custom_value'=>$params
        ));

        //$filterform->submit($request);  //use bind instead of handleRequest. handleRequest does not get filter data
        $filterform->handleRequest($request);

        $filter = $filterform['filter']->getData();
        $search = $filterform['search']->getData();
        $startDates = $filterform['startDates']->getData();
        $applicationSeasonStartDates = $filterform['applicationSeasonStartDates']->getData();
        $hidden = $filterform['hidden']->getData();
        $archived = $filterform['archived']->getData();
        $complete = $filterform['complete']->getData();
        $interviewee = $filterform['interviewee']->getData();
        $active = $filterform['active']->getData();
        $reject = $filterform['reject']->getData();
        //$onhold = $filterform['onhold']->getData();
        $priority = $filterform['priority']->getData();

        $accepted = $filterform['accepted']->getData();
        $acceptedandnotified = $filterform['acceptedandnotified']->getData();
        $rejectedandnotified = $filterform['rejectedandnotified']->getData();

        //$page = $request->get('page');
        //echo "0startDates=".$startDates->format('Y-m-d')."<br>";
        //echo "active=".$active."<br>";
        //echo "filter=".$filter."<br>";
        //echo "<br>search=".$search."<br>";
        //exit('1');

        $filterParams = $request->query->all();

        if( $route == "resapp_accepted_residents" && count($filterParams) == 0 ) {
            $residencyTypeId = null;
            if( count($residencyTypes) == 1 ) {
                $firstResType = reset($residencyTypes);
                //echo "firstResType id=".key($residencyTypes)."";
                //exit();
                $residencyTypeId = key($residencyTypes);
            }
            return $this->redirect( $this->generateUrl($route,
                array(
                    'filter[startDates]' => $defaultStartDates, //$currentYear,
                    //'filter[applicationSeasonStartDates]' => $defaultApplicationSeasonStartDates,
                    'filter[accepted]' => 1,
                    'filter[acceptedandnotified]' => 1,
                    'filter[filter]' => $residencyTypeId,
                )
            ));
        }

        if( $route == "resapp_send_rejection_emails" && count($filterParams) == 0 ) {
            $residencyTypeId = null;
            if( count($residencyTypes) == 1 ) {
                $firstResType = reset($residencyTypes);
                //echo "firstResType id=".key($residencyTypes)."";
                //exit();
                $residencyTypeId = key($residencyTypes);
            }
            //Show only "Active", "Priority", "Complete", "Interviewee", "Rejected"
            //filter[startDate]=2021&
            //filter[active]=1&filter[priority]=1&filter[complete]=1&filter[interviewee]=1&filter[reject]=1
            return $this->redirect( $this->generateUrl($route,
                array(
                    'filter[startDates]' => $defaultStartDates, //$currentYear,
                    //'filter[applicationSeasonStartDates]' => $defaultApplicationSeasonStartDates,
                    'filter[active]' => 1,
                    'filter[complete]' => 1,
                    'filter[interviewee]' => 1,
                    'filter[priority]' => 1,
                    'filter[reject]' => 1,
                    'filter[filter]' => $residencyTypeId,
                )
            ));
        }

        if( count($filterParams) == 0 ) {
            $residencyTypeId = null;
            if( count($residencyTypes) == 1 ) {
                $firstResType = reset($residencyTypes);
                //echo "firstResType id=".key($residencyTypes)."";
                //exit();
                $residencyTypeId = key($residencyTypes);
            }
            return $this->redirect( $this->generateUrl($route, //'resapp_home',
                array(
                    'filter[startDates]' => $defaultStartDates, //$currentYear,
                    //'filter[applicationSeasonStartDates]' => $defaultApplicationSeasonStartDates,
                    'filter[active]' => 1,
                    'filter[complete]' => 1,
                    'filter[interviewee]' => 1,
                    //'filter[onhold]' => 1,
                    'filter[priority]' => 1,
                    'filter[accepted]' => 1,
                    'filter[acceptedandnotified]' => 1,
                    'filter[filter]' => $residencyTypeId,
                )
            ) );
        }

        //force check: check user role. Change filter according to the user roles
        if( $filter && $resappUtil->hasSameResidencyTypeId($user,$filter) == false ) {
            //exit('no permission');
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        //$resApps = $em->getRepository('AppUserdirectoryBundle:ResidencyApplication')->findAll();
        $repository = $this->getDoctrine()->getRepository('AppResAppBundle:ResidencyApplication');
        $dql =  $repository->createQueryBuilder("resapp");
        $dql->select('resapp');
        //$dql->groupBy('resapp');
        $dql->orderBy("resapp.id","DESC");
        $dql->leftJoin("resapp.appStatus", "appStatus");
        $dql->leftJoin("resapp.residencyTrack", "residencyTrack");
        $dql->leftJoin("resapp.user", "applicant");
        $dql->leftJoin("applicant.infos", "applicantinfos");
        //$dql->leftJoin("applicant.credentials", "credentials");
        $dql->leftJoin("resapp.examinations", "examinations");
        $dql->leftJoin("resapp.trainings", "trainings");
        $dql->leftJoin("resapp.rank", "rank");

        if( $search ) {
            //echo "<br>search=".$search."<br>";
            $dql->andWhere("LOWER(applicantinfos.firstName) LIKE LOWER('%".$search."%') OR LOWER(applicantinfos.lastName) LIKE LOWER('%".$search."%')");
            $searchFlag = true;
        }

        $resSubspecId = null;
        if( $filter ) { //&& $filter != "ALL"
            $dql->andWhere("residencyTrack.id = ".$filter);
            $searchFlag = true;
            $resSubspecId = $filter;
        }

        if( 0 && !$filter ) {
            $restypeArr = array();
            foreach( $residencyTypes as $residencyTypeID => $residencyTypeName ) {
                $restypeArr[] = "residencyTrack.id = ".$residencyTypeID;
            }
            $dql->andWhere( implode(" OR ", $restypeArr) );
            $searchFlag = true;
        }

        $orWhere = array();
        $orWhere[] = "appStatus.id IS NULL";

        if( $hidden ) {
            $orWhere[] = "appStatus.name = 'hide'";
            $searchFlag = true;
        } else {
            //$searchFlag = true;
        }

        if( $archived ) {
            $orWhere[] = "appStatus.name = 'archive'";
            $searchFlag = true;
        } else {
            //$searchFlag = true;
        }

        if( $complete ) {
            $orWhere[] = "appStatus.name = 'complete'";
            $searchFlag = true;
        } else {
            //$searchFlag = true;
        }

        if( $interviewee ) {
            $orWhere[] = "appStatus.name = 'interviewee'";
            $searchFlag = true;
        } else {
            //$searchFlag = true;
        }

        if( $active ) {
            $orWhere[] = "appStatus.name = 'active'";
            $searchFlag = true;
        } else {
            //$searchFlag = true;
        }

        if( $reject ) {
            $orWhere[] = "appStatus.name = 'reject'";
            $searchFlag = true;
        }

//        if( $onhold ) {
//            $orWhere[] = "appStatus.name = 'onhold'";
//            $searchFlag = true;
//        }

        if( $priority ) {
            $orWhere[] = "appStatus.name = 'priority'";
            $searchFlag = true;
        }

        if( $accepted ) {
            $orWhere[] = "appStatus.name = 'accepted'";
            $searchFlag = true;
        }
        if( $acceptedandnotified ) {
            $orWhere[] = "appStatus.name = 'acceptedandnotified'";
            $searchFlag = true;
        }
        if( $rejectedandnotified ) {
            $orWhere[] = "appStatus.name = 'rejectedandnotified'";
            $searchFlag = true;
        }

        if( count($orWhere) > 0 ) {
            $orWhereStr = implode(" OR ",$orWhere);
            $dql->andWhere("(".$orWhereStr.")");
        }

        if( $startDates ) {
            //echo "startDate=$startDates <br>";
            if(1) {
                //date as string
                $startDateCriterions = array();
                $startDatesArr = explode(",",$startDates);
                $startYearStr = $startDates;    //$startDatesArr[0];
                foreach ($startDatesArr as $startDate) {
                    //$startDatesArr = explode("-", $startDate); //2009-01-01 00:00:00.000000
                    //$startYearStr = $startDatesArr[0];
                    $bottomDate = $startDate . "-01-01";
                    $topDate = $startDate . "-12-31";
                    //echo "bottomDate=$bottomDate, topDate=$topDate <br>";
                    $startDateCriterions[] = "("."resapp.startDate BETWEEN '" . $bottomDate . "'" . " AND " . "'" . $topDate . "'".")";
                }
                $startDateCriterion = implode(" OR ",$startDateCriterions);
                $dql->andWhere($startDateCriterion);
                if ($startDates != $defaultStartDates) {
                    $searchFlag = true;
                }
            } else {
                //date as DateTime object
                $startYearStr = $startDates->format('Y');
                $bottomDate = $startYearStr."-01-01";
                $topDate = $startYearStr."-12-31";
                $dql->andWhere("resapp.startDate BETWEEN '" . $bottomDate . "'" . " AND " . "'" . $topDate . "'" );

                if( $startYearStr != $currentYear ) {
                    $searchFlag = true;
                }
            }
        } else {
            $startYearStr = $currentYear;
        }

        if( $applicationSeasonStartDates ) {
            //echo "applicationSeasonStartDates=$applicationSeasonStartDates <br>";
            if(1) {
                //date as string
                $applicationSeasonStartDateCriterions = array();
                $applicationSeasonStartDatesArr = explode(",",$applicationSeasonStartDates);
                $seasonStartYearStr = $applicationSeasonStartDates;
                foreach ($applicationSeasonStartDatesArr as $applicationSeasonStartDate) {
                    $bottomDate = $applicationSeasonStartDate . "-01-01";
                    $topDate = $applicationSeasonStartDate . "-12-31";
                    //echo "bottomDate=$bottomDate, topDate=$topDate <br>";
                    $applicationSeasonStartDateCriterions[] = "("."resapp.applicationSeasonStartDate BETWEEN '" . $bottomDate . "'" . " AND " . "'" . $topDate . "'".")";
                }
                $applicationSeasonStartDateCriterion = implode(" OR ",$applicationSeasonStartDateCriterions);
                $dql->andWhere($applicationSeasonStartDateCriterion);
                if ($applicationSeasonStartDates != $defaultApplicationSeasonStartDates) {
                    $searchFlag = true;
                }
            } else {
                //date as DateTime object
                $seasonStartYearStr = $applicationSeasonStartDates->format('Y');
                $bottomDate = $seasonStartYearStr."-01-01";
                $topDate = $seasonStartYearStr."-12-31";
                $dql->andWhere("resapp.applicationSeasonStartDate BETWEEN '" . $bottomDate . "'" . " AND " . "'" . $topDate . "'" );

                if( $seasonStartYearStr != $currentYear ) {
                    $searchFlag = true;
                }
            }
        } else {
            $seasonStartYearStr = $currentYear;
        }

        if( $route == "resapp_myinterviewees" ) {
            $dql->leftJoin("resapp.interviews", "interviews");
            $dql->andWhere("interviews.interviewer = " . $user->getId() );
        }

        //echo "dql=".$dql."<br>";

        $limit = 200;
        //$limit = 10; //testing
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $resApps = $paginator->paginate(
            $query,
            $request->query->get('page', 1), /*page number*/
            //$request->query->getInt('page', 1),
            $limit,      /*limit per page*/
            array('wrap-queries' => true)
        );


        $eventtype = $em->getRepository('AppUserdirectoryBundle:EventTypeList')->findOneByName("Import of Residency Applications Spreadsheet");
        $lastImportTimestamps = $this->getDoctrine()->getRepository('AppUserdirectoryBundle:Logger')->findBy(array('eventType'=>$eventtype),array('creationdate'=>'DESC'),1);
        if( count($lastImportTimestamps) != 1 ) {
            $lastImportTimestamp = null;
        } else {
            $lastImportTimestamp = $lastImportTimestamps[0]->getCreationdate();
        }

        $accessreqs = $resappUtil->getActiveAccessReq();
        $accessreqsCount = 0;
        if( is_array($accessreqs) ) {
            $accessreqsCount = count($accessreqs);
        }

        //use date from the filter ($seasonStartYearStr) instead of $currentYear

        $complete = $resappUtil->getResAppByStatusAndYear('complete',$resSubspecId,$seasonStartYearStr);
        $completeTotal = $resappUtil->getResAppByStatusAndYear('complete',$resSubspecId);

        $hidden = $resappUtil->getResAppByStatusAndYear('hide',$resSubspecId,$seasonStartYearStr);
        $hiddenTotal = $resappUtil->getResAppByStatusAndYear('hide',$resSubspecId);

        $archived = $resappUtil->getResAppByStatusAndYear('archive',$resSubspecId,$seasonStartYearStr);
        $archivedTotal = $resappUtil->getResAppByStatusAndYear('archive',$resSubspecId);

        $active = $resappUtil->getResAppByStatusAndYear('active',$resSubspecId,$seasonStartYearStr);
        $activeTotal = $resappUtil->getResAppByStatusAndYear('active',$resSubspecId);

        $interviewee = $resappUtil->getResAppByStatusAndYear('interviewee',$resSubspecId,$seasonStartYearStr);
        $intervieweeTotal = $resappUtil->getResAppByStatusAndYear('interviewee',$resSubspecId);

        $reject = $resappUtil->getResAppByStatusAndYear('reject',$resSubspecId,$seasonStartYearStr);
        $rejectTotal = $resappUtil->getResAppByStatusAndYear('reject',$resSubspecId);

        //$onhold = $resappUtil->getResAppByStatusAndYear('onhold',$resSubspecId,$seasonStartYearStr);
        //$onholdTotal = $resappUtil->getResAppByStatusAndYear('onhold',$resSubspecId);

        $priority = $resappUtil->getResAppByStatusAndYear('priority',$resSubspecId,$seasonStartYearStr);
        $priorityTotal = $resappUtil->getResAppByStatusAndYear('priority',$resSubspecId);

        $accepted = $resappUtil->getResAppByStatusAndYear('accepted',$resSubspecId,$seasonStartYearStr);
        $acceptedTotal = $resappUtil->getResAppByStatusAndYear('accepted',$resSubspecId);

        $acceptedandnotified = $resappUtil->getResAppByStatusAndYear('acceptedandnotified',$resSubspecId,$seasonStartYearStr);
        $acceptedandnotifiedTotal = $resappUtil->getResAppByStatusAndYear('acceptedandnotified',$resSubspecId);

        $rejectedandnotified = $resappUtil->getResAppByStatusAndYear('rejectedandnotified',$resSubspecId,$seasonStartYearStr);
        $rejectedandnotifiedTotal = $resappUtil->getResAppByStatusAndYear('rejectedandnotified',$resSubspecId);

        $idsArr = array();
        foreach( $resApps as $resApp ) {
            $idsArr[] = $resApp->getId();
        }

        //Showing applications of your interviewees: 25 evaluations received, 10 awaited
        $awaitedInterviews = null;
        $receivedInterviews = null;
        if( $route == "resapp_myinterviewees" ) {

            if( $resSubspecId ) {
                $resSubspecArg = $resSubspecId;
            } else {
                $resSubspecArg = $residencyTypes;
            }

            $awaitedInterviews = count($resappUtil->getResAppByStatusAndYear('interviewee-not',$resSubspecArg,$seasonStartYearStr,$user));
            $receivedInterviews = count($resappUtil->getResAppByStatusAndYear('interviewee',$resSubspecArg,$seasonStartYearStr,$user));
            //echo "awaitedInterviews=".$awaitedInterviews."<br>";
            //echo "receivedInterviews=".$receivedInterviews."<br>";
        }

        //allowPopulateResApp
        //$userUtil = new UserUtil();
        //$allowPopulateResApp = $userUtil->getSiteSetting($em,'AllowPopulateResApp');
        if( $enableGoolge ) {
            $allowPopulateResApp = $userSecUtil->getSiteSettingParameter('AllowPopulateResApp');
        }

        //At the top of the homepage, show either "Now accepting applications" if the
        // "accepting applications" status from json is enabled, or show "Not accepting applications now."
        if( $enableGoolge ) {
            $acceptingApplication = NULL;
            if ($route == "resapp_home") {
                $acceptingApplication = "Not accepting applications now";
                $googlesheetmanagement = $this->container->get('resapp_googlesheetmanagement');
                $configFileContent = $googlesheetmanagement->getConfigOnGoogleDrive();
                if ($configFileContent) {
                    $configFileContent = json_decode($configFileContent, true);
                    $acceptingSubmissions = $configFileContent['acceptingSubmissions'];
                    if ($acceptingSubmissions || $acceptingSubmissions == 'true') {
                        $acceptingApplication = "Now accepting applications";
                    }
                    //echo "<pre>";
                    //print_r($configFileContent);
                    //echo "</pre>";
                }
                $acceptingApplication = "- " . $acceptingApplication;
            }
        }

        //emailAcceptSubject emailAcceptBody
        $acceptedEmailSubject = $userSecUtil->getSiteSettingParameter('acceptedEmailSubject',$this->getParameter('resapp.sitename'));
        $acceptedEmailBody = $userSecUtil->getSiteSettingParameter('acceptedEmailBody',$this->getParameter('resapp.sitename'));
        $rejectedEmailSubject = $userSecUtil->getSiteSettingParameter('rejectedEmailSubject',$this->getParameter('resapp.sitename'));
        $rejectedEmailBody = $userSecUtil->getSiteSettingParameter('rejectedEmailBody',$this->getParameter('resapp.sitename'));


        return array(
            'entities' => $resApps,
            'pathbase' => 'resapp',
            'lastImportTimestamp' => $lastImportTimestamp,
            //'allowPopulateResApp' => $allowPopulateResApp,
            //'acceptingApplication' => $acceptingApplication,
            'resappfilter' => $filterform->createView(),
            //'startDate' => $startDate,
            'filter' => $resSubspecId,
            'accessreqs' => $accessreqsCount,
            'currentYear' => $startYearStr, //$currentYear, //TODO: adopt the currentYear to currentYears in controller and html
            'seasonStartYear' => $seasonStartYearStr,
            'hiddenTotal' => count($hiddenTotal),
            'archivedTotal' => count($archivedTotal),
            'hidden' => count($hidden),
            'archived' => count($archived),
            'active' => count($active),
            'activeTotal' => count($activeTotal),
            'reject' => count($reject),
            'rejectTotal' => count($rejectTotal),
            //'onhold' => count($onhold),
            //'onholdTotal' => count($onholdTotal),
            'priority' => count($priority),
            'priorityTotal' => count($priorityTotal),
            'complete' => count($complete),
            'completeTotal' => count($completeTotal),
            'interviewee' => count($interviewee),
            'intervieweeTotal' => count($intervieweeTotal),

            'accepted' => count($accepted),
            'acceptedTotal' => count($acceptedTotal),
            'acceptedandnotified' => count($acceptedandnotified),
            'acceptedandnotifiedTotal' => count($acceptedandnotifiedTotal),
            'rejectedandnotified' => count($rejectedandnotified),
            'rejectedandnotifiedTotal' => count($rejectedandnotifiedTotal),

            'acceptedEmailSubject' => $acceptedEmailSubject,
            'acceptedEmailBody' => $acceptedEmailBody,
            'rejectedEmailSubject' => $rejectedEmailSubject,
            'rejectedEmailBody' => $rejectedEmailBody,

            'awaitedInterviews' => $awaitedInterviews,
            'receivedInterviews' => $receivedInterviews,
            'searchFlag' => $searchFlag,
            'serverTimeZone' => "", //date_default_timezone_get(),
            'resappids' => implode("-",$idsArr),
            'route_path' => $route
        );
    }

//    //check for active access requests
//    public function getActiveAccessReq() {
//        if( !$this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_ADMIN') ) {
//            return null;
//        }
//        $userSecUtil = $this->get('user_security_utility');
//        $accessreqs = $userSecUtil->getUserAccessRequestsByStatus($this->getParameter('resapp.sitename'),AccessRequest::STATUS_ACTIVE);
//        return $accessreqs;
//    }




    //@Route("/edit/{id}", name="resapp_edit")
    //@Route("/edit-with-default-interviewers/{id}", name="resapp_edit_default_interviewers")
    /**
     * @Route("/show/{id}", name="resapp_show")
     * @Route("/download/{id}", name="resapp_download")
     * @Template("AppResAppBundle/Form/new.html.twig")
     */
    public function showAction(Request $request, $id) {

        //echo "clientip=".$request->getClientIp()."<br>";
        //$ip = $this->container->get('request')->getClientIp();
        //echo "ip=".$ip."<br>";

//        if( false == $this->get('security.authorization_checker')->isGranted("read","ResidencyApplication") ){
//            return $this->redirect( $this->generateUrl('resapp-nopermission') );
//        }

        //ini_set('memory_limit', '7168M');

        //error_reporting(E_ERROR | E_PARSE);

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $logger = $this->container->get('logger');
        $routeName = $request->get('_route');
        $userSecUtil = $this->container->get('user_security_utility');

        $actionStr = "viewed";
        $eventType = 'Residency Application Page Viewed';

        //admin can edit
        if( $routeName == "resapp_edit" ) {
            $actionStr = "viewed on edit page";
            $eventType = 'Residency Application Page Viewed';
        }

        //download: user or localhost
        if( $routeName == 'resapp_download' ) {
            //$user = $this->get('security.token_storage')->getToken()->getUser();
            //download link can be accessed by a console as localhost with role IS_AUTHENTICATED_ANONYMOUSLY, so simulate login manually           
            if( !($user instanceof User) ) {
                $firewall = 'ldap_resapp_firewall';               
                $systemUser = $userSecUtil->findSystemUser();
                if( $systemUser ) {
                    $token = new UsernamePasswordToken($systemUser, null, $firewall, $systemUser->getRoles());
                    $this->get('security.token_storage')->setToken($token);
                    //$this->get('security.token_storage')->setToken($token);
                }
                $logger->notice("Download view: Logged in as systemUser=".$systemUser);
            } else {
                $logger->notice("Download view: Token user is valid security.token_storage user=".$user);
            }
        }

        
        //echo "resapp download!!!!!!!!!!!!!!! <br>";       

        $entity = $em->getRepository('AppResAppBundle:ResidencyApplication')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Residency Application by id='.$id);
        }

        //testing
        //$resappRecLetterUtil = $this->container->get('resapp_rec_letter_util');
        //$resappRecLetterUtil->generateResappRecLetterId($entity);
        //exit('testing');

//        if( false == $this->get('security.authorization_checker')->isGranted("interview",$entity) ) {
//            exit('resapp interview permission not ok ID:'.$entity->getId());
//        }

        //user who has the same res type can view or edit
        //can use hasResappPermission or isGranted("read",$entity). isGranted("read",$entity) resapp voter contains hasResappPermission
        //$resappUtil = $this->container->get('resapp_util');
        //if( $resappUtil->hasResappPermission($user,$entity) == false ) {
        if( false == $this->get('security.authorization_checker')->isGranted("read",$entity) ) {
            //exit('resapp read permission not ok ID:'.$entity->getId());
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }
        //exit('resapp permission ok ID:'.$entity->getId());

        if( $routeName == "resapp_edit" ) {
            if( false == $this->get('security.authorization_checker')->isGranted("update",$entity) ) {
                return $this->redirect( $this->generateUrl('resapp-nopermission') );
            }
        }
//        else {
//            if( false == $this->get('security.authorization_checker')->isGranted("read",$entity) ) {
//                return $this->redirect( $this->generateUrl('resapp-nopermission') );
//            }
//        }

        //$args = $this->getShowParameters($routeName,$id);
        $args = $this->getShowParameters($routeName,$entity);

        if( $routeName == 'resapp_download' ) {
            return $this->render('AppResAppBundle/Form/download.html.twig', $args);
        }

        //event log
        //$event = "Residency Application with ID".$id." has been ".$actionStr." by ".$user;
        //$userSecUtil->createUserEditEvent($this->getParameter('resapp.sitename'),$event,$user,$entity,$request,$eventType);
        
        return $this->render('AppResAppBundle/Form/new.html.twig', $args);
    }

    /**
     * @Route("/new/", name="resapp_new", methods={"GET"})
     *
     * @Template("AppResAppBundle/Form/new.html.twig")
     */
    public function newAction(Request $request) {

        //coordinator and director can create
//        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_COORDINATOR') && false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_DIRECTOR') ){
//            return $this->redirect( $this->generateUrl('resapp-nopermission') );
//        }
        if( false == $this->get('security.authorization_checker')->isGranted("create","ResidencyApplication") ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        //$user = new User();
        $addobjects = true;
        $applicant = new User($addobjects);
        $applicant->setPassword("");
        $applicant->setCreatedby('manual');
        $applicant->setAuthor($user);

        $residencyApplication = new ResidencyApplication($user);
        $residencyApplication->setTimestamp(new \DateTime());

        $applicant->addResidencyApplication($residencyApplication);

        $routeName = $request->get('_route');
        //$args = $this->getShowParameters($routeName,null,$residencyApplication);
        $args = $this->getShowParameters($routeName,$residencyApplication);

        if( count($args) == 0 ) {
            $linkUrl = $this->generateUrl(
                "resapp_residencytype_settings",
                array(),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $warningMsg = "No residency tracks are found.";
            $warningMsg = $warningMsg."<br>".'<a href="'.$linkUrl.'" target="_blank">Please add a new residency application track.</a>';

            $this->get('session')->getFlashBag()->add(
                'warning',
                $warningMsg
            );
            //return $this->redirect( $this->generateUrl('resapp-nopermission') );
            return $this->redirect( $this->generateUrl('resapp-nopermission',array('empty'=>true)) );
        }

        return $this->render('AppResAppBundle/Form/new.html.twig', $args);
    }


    public function getShowParameters($routeName, $entity) {
             
        $user = $this->get('security.token_storage')->getToken()->getUser();

//        echo "user=".$user."<br>";
//        if( !($user instanceof User) ) {
//            echo "no user object <br>";
//            $userSecUtil = $this->container->get('user_security_utility');
//            $user = $userSecUtil->findSystemUser();
//        }               
        
        $em = $this->getDoctrine()->getManager();

//        if( $id ) {
//            //$resApps = $em->getRepository('AppResAppBundle:ResidencyApplication')->findAll();
//            $entity = $this->getDoctrine()->getRepository('AppResAppBundle:ResidencyApplication')->find($id);
//
//            if( !$entity ) {
//                throw $this->createNotFoundException('Unable to find Residency Application by id='.$id);
//            }
//        } else {
//            if( !$entity ) {
//                throw $this->createNotFoundException('Residency Application entity was not provided: id='.$id.", entity=".$entity);
//            }
//        }

        //add empty fields if they are not exist
        $resappUtil = $this->container->get('resapp_util');

        $resTypes = $resappUtil->getResidencyTypesByInstitution(true);
        if( count($resTypes) == 0 ) {
            return array();
        }

        $resappVisas = $resappUtil->getResidencyVisaStatuses(false,false);
        
        $resappUtil->addEmptyResAppFields($entity); //testing
//        foreach( $entity->getTrainings() as $training ) {
//            echo "training=".$training->getTrainingType()."<br>";
//        }

        if( $routeName == "resapp_show" ) {
            $cycle = 'show';
            $disabled = true;
            $method = "GET";
            $action = $this->generateUrl('resapp_edit', array('id' => $entity->getId()));
        }

        if( $routeName == "resapp_new" ) {
            $cycle = 'new';
            $disabled = false;
            $method = "POST";
            $action = $this->generateUrl('resapp_create_applicant');
            //exit('resapp_new, action='.$action);
        }

        if( $routeName == "resapp_create_applicant" ) {
            $cycle = 'new';
            $disabled = false;
            $method = null; //"GET";
            $action = null; //$this->generateUrl('resapp_show',array('id' => $entity->getId()));
            //exit('resapp_new, action='.$action);
        }

        if( $routeName == "resapp_edit" ) {
            $cycle = 'edit';
            $disabled = false;
            $method = "PUT";
            $action = $this->generateUrl('resapp_update', array('id' => $entity->getId()));
        }

        if( $routeName == "resapp_update" ) {
            $cycle = 'edit';
            $disabled = false;
            $method = "PUT";
            $action = $this->generateUrl('resapp_update', array('id' => $entity->getId()));
        }

        if( $routeName == "resapp_edit_default_interviewers" ) {
            $cycle = 'edit';
            $disabled = false;
            $method = "PUT";
            $action = $this->generateUrl('resapp_update', array('id' => $entity->getId()));
            $resappUtil->addDefaultInterviewers($entity);

            $this->get('session')->getFlashBag()->add(
                'pnotify',
                "Important Note: Please manually review added default interviewers in the 'Interviews' section and click 'Update' button to save the changes!"
            );
        }

        if( $routeName == "resapp_download" ) {
            $cycle = 'download';
            $disabled = true;
            $method = "GET";
            $action = null; //$this->generateUrl('resapp_update', array('id' => $entity->getId()));
        }

        $fullForm = false;

        $params = array(
            'cycle' => $cycle,
            'em' => $em,
            'user' => $entity->getUser(),
            'cloneuser' => null,
            'roles' => $user->getRoles(),
            'container' => $this->container,
            'resappTypes' => $resTypes,
            'resappVisas' => $resappVisas,
            'fullForm' => $fullForm,
            'entity' => $entity
        );

        //echo "routeName=$routeName;  action=$action; method=$method<br>";
        //exit('111');

//        $form = $this->createForm(
//            new ResidencyApplicationType($params),
//            $entity,
//            array(
//                'disabled' => $disabled,
//                'method' => $method,
//                'action' => $action
//            )
//        );
        $form = $this->createForm(
            ResidencyApplicationType::class, //method: get Show Parameters
            $entity,
            array(
                'disabled' => $disabled,
                'method' => $method,
                'action' => $action,
                'form_custom_value' => $params
            )
        );

        //clear em, because createUserEditEvent will flush em
        $em = $this->getDoctrine()->getManager();
        $em->clear();

        return array(
            'form_pure' => $form,
            'form' => $form->createView(),
            'entity' => $entity,
            'pathbase' => 'resapp',
            'cycle' => $cycle,
            'sitename' => $this->getParameter('resapp.sitename'),
            'route' => $routeName,
            'fullForm' => $fullForm
        );
    }


//    /**
//     * -NOT-USED
//     * @Route("/update-NOT-USED/{id}", name="resapp_update-NOT-USED", methods={"PUT"})
//     * @Template("AppResAppBundle/Form/new.html.twig")
//     */
//    public function updateNotUsedAction(Request $request, $id) {
//
////        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_COORDINATOR') && false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_DIRECTOR') ){
////            return $this->redirect( $this->generateUrl('resapp-nopermission') );
////        }
////        if( false == $this->get('security.authorization_checker')->isGranted("update","ResidencyApplication") ){
////            return $this->redirect( $this->generateUrl('resapp-nopermission') );
////        }
//
//        //echo "update <br>";
//        //exit('update');
//
//        //ini_set('memory_limit', '3072M'); //3072M
//
//        $userSecUtil = $this->get('user_security_utility');
//        $user = $this->get('security.token_storage')->getToken()->getUser();
//
//        $entity = $this->getDoctrine()->getRepository('AppResAppBundle:ResidencyApplication')->find($id);
//
//        if( !$entity ) {
//            throw $this->createNotFoundException('Unable to find Residency Application by id='.$id);
//        }
//
//        //user who has the same res type can view or edit
//        $resappUtil = $this->container->get('resapp_util');
//        if( $resappUtil->hasResappPermission($user,$entity) == false ) {
//            return $this->redirect( $this->generateUrl('resapp-nopermission') );
//        }
//
//        if( false == $this->get('security.authorization_checker')->isGranted("update",$entity) ){
//            return $this->redirect( $this->generateUrl('resapp-nopermission') );
//        }
//
//        // Create an ArrayCollection of the current interviews
//        $originalInterviews = new ArrayCollection();
//        foreach( $entity->getInterviews() as $interview) {
//            $originalInterviews->add($interview);
//        }
//
//        $originalReports = new ArrayCollection();
//        foreach( $entity->getReports() as $report ) {
//            $originalReports->add($report);
//        }
//
//        $cycle = 'edit';
//        $user = $this->get('security.token_storage')->getToken()->getUser();
//
//        $params = array(
//            'cycle' => $cycle,
//            'em' => $this->getDoctrine()->getManager(),
//            'user' => $entity->getUser(),
//            'cloneuser' => null,
//            'roles' => $user->getRoles(),
//            'container' => $this->container,
//            'cycle_type' => "update"
//        );
//        $form = $this->createForm( ResidencyApplicationType::class, $entity, array('form_custom_value' => $params) ); //update
//        //$routeName = $request->get('_route');
//        //$args = $this->getShowParameters($routeName,null,$entity);
//        //$form = $args['form_pure'];
//
//        $form->handleRequest($request);
//
//        if( !$form->isSubmitted() ) {
//            echo "form is not submitted<br>";
//            $form->submit($request);
//        }
//
//
////        if ($form->isDisabled()) {
////            echo "form is disabled<br>";
////            exit();
////        }
////        if (count($form->getErrors(true)) > 0) {
////            echo "form has errors<br>";
////        }
////        echo "errors:<br>";
////        $string = (string) $form->getErrors(true);
////        echo "string errors=".$string."<br>";
////        echo "getErrors count=".count($form->getErrors())."<br>";
//        //echo "getErrorsAsString()=".$form->getErrorsAsString()."<br>";
////        print_r($form->getErrors());
////        echo "<br>string errors:<br>";
////        print_r($form->getErrorsAsString());
////        echo "<br>";
////        exit();
//
//        if(0) {
//            $errorHelper = new ErrorHelper();
//            $errors = $errorHelper->getErrorMessages($form);
//            echo "<br>form errors:<br>";
//            print_r($errors);
//
//            //echo "<br><br>getErrors:<br>";
//            //var_dump($form->getErrors());die;
//        }
//
//
//
//        $force = false;
//        //$force = true;
//        if( $form->isValid() || $force ) {
//
//            //exit('form valid');
//
//            /////////////// Process Removed Collections ///////////////
//            $removedCollections = array();
//
//            $removedInfo = $this->removeCollection($originalInterviews,$entity->getInterviews(),$entity);
//            if( $removedInfo ) {
//                $removedCollections[] = $removedInfo;
//            }
//            /////////////// EOF Process Removed Collections ///////////////
//
//            $this->calculateScore($entity);
//
//            $this->processDocuments($entity);
//
//            $this->assignResAppAccessRoles($entity);
//
//            //set update author application
//            $em = $this->getDoctrine()->getManager();
//            $userUtil = new UserUtil();
//            $secTokenStorage = $this->get('security.token_storage');
//            $userUtil->setUpdateInfo($entity,$em,$secTokenStorage);
//
//
//            /////////////// Add event log on edit (edit or add collection) ///////////////
//            /////////////// Must run before flash DB. When DB is flashed getEntityChangeSet() will not work ///////////////
//            $changedInfoArr = $this->setEventLogChanges($entity);
//
//            //report (Complete Application PDF) diff
//            $reportsDiffInfoStr = $this->recordToEvenLogDiffCollection($originalReports,$entity->getReports(),"Report");
//            //echo "reportsDiffInfoStr=".$reportsDiffInfoStr."<br>";
//            //exit('report');
//
//            //set Edit event log for removed collection and changed fields or added collection
//            if( count($changedInfoArr) > 0 || count($removedCollections) > 0 || $reportsDiffInfoStr ) {
//                $event = "Residency Application ".$entity->getId()." information has been changed by ".$user.":"."<br>";
//                $event = $event . implode("<br>", $changedInfoArr);
//                $event = $event . "<br>" . implode("<br>", $removedCollections);
//                $event = $event . $reportsDiffInfoStr;
//                //echo "Diff event=".$event."<br>";
//                //$userSecUtil = $this->get('user_security_utility');
//                $userSecUtil->createUserEditEvent($this->getParameter('resapp.sitename'),$event,$user,$entity,$request,'Residency Application Updated');
//            }
//
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($entity);
//            $em->flush();
//
//            //don't regenerate report if it was added.
//            //Regenerate if: report does not exists (reports count == 0) or if original reports are the same as current reports
//            //echo "report count=".count($entity->getReports())."<br>";
//            //echo "reportsDiffInfoStr=".$reportsDiffInfoStr."<br>";
//            if( count($entity->getReports()) == 0 || $reportsDiffInfoStr == "" ) {
//                $resappRepGen = $this->container->get('resapp_reportgenerator');
//                $resappRepGen->addResAppReportToQueue( $id, 'overwrite' );
//                $this->get('session')->getFlashBag()->add(
//                    'notice',
//                    'A new Complete Residency Application PDF will be generated.'
//                );
//                //echo "Regenerate!!!! <br>";
//            } else {
//                //echo "NO Regenerate!!!! <br>";
//            }
//            //exit('report regen');
//
//            //set logger for update
//            //$logger = $this->container->get('logger');
//            //$logger->notice("update: timezone=".date_default_timezone_get());
//            //$userSecUtil = $this->container->get('user_security_utility');
//            //$user = $em->getRepository('AppUserdirectoryBundle:User')->find($user->getId()); //fetch user from DB otherwise keytype is null
//            $event = "Residency Application with ID " . $id . " has been updated by " . $user;
//            $userSecUtil->createUserEditEvent($this->getParameter('resapp.sitename'),$event,$user,$entity,$request,'Residency Application Updated');
//            //exit('event='.$event);
//
//            return $this->redirect($this->generateUrl('resapp_show',array('id' => $entity->getId())));
//        } else {
//            echo "getErrors count=".count($form->getErrors(true))."<br>";
//            $string = (string) $form->getErrors(true);
//            //echo "Error:<br>$string<br><br><pre>";
//            //print_r($form->getErrors());
//            //echo "</pre>";
//
//            $msg = 'Residency Form has an error (ID# '.$entity->getId().'): '.$form->getErrors(true);
//            //$userSecUtil = $this->container->get('user_security_utility');
//            //$userSecUtil->sendEmailToSystemEmail("Residency Form has an error (ID# ".$entity->getId().")", $msg);
//            exit($msg."<br>Notification email has been sent to the system administrator.");
//            //throw new \Exception($msg);
//        }
//
//        //echo 'form invalid <br>';
//        //exit('form invalid');
//
//        return array(
//            'form' => $form->createView(),
//            'entity' => $entity,
//            'pathbase' => 'resapp',
//            'cycle' => $cycle,
//            'sitename' => $this->getParameter('resapp.sitename')
//        );
//    }
    //EOF -NOT-USED

    /**
     * Separate edit/update controller action to insure csrf token is valid
     * Displays a form to edit an existing resapp entity.
     *
     * @Route("/edit/{id}", name="resapp_edit", methods={"GET","POST"})
     * @Route("/edit-with-default-interviewers/{id}", name="resapp_edit_default_interviewers", methods={"GET","POST"})
     * @Template("AppResAppBundle/Form/edit.html.twig")
     */
    public function editAction(Request $request, ResidencyApplication $entity)
    {
        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Residency Application');
        }
        $id = $entity->getId();

        $userSecUtil = $this->container->get('user_security_utility');
        //$resappRecLetterUtil = $this->container->get('resapp_rec_letter_util');
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $routeName = $request->get('_route');

        //user who has the same res type can view or edit
        $resappUtil = $this->container->get('resapp_util');
        if( $resappUtil->hasResappPermission($user,$entity) == false ) {
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        if( false == $this->get('security.authorization_checker')->isGranted("update",$entity) ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        //ini_set('memory_limit', '7168M');

        ////// PRE Update INFO //////
        // Create an ArrayCollection of the current interviews
        $originalInterviews = new ArrayCollection();
        foreach( $entity->getInterviews() as $interview) {
            $originalInterviews->add($interview);
        }

        $originalReports = new ArrayCollection();
        foreach( $entity->getReports() as $report ) {
            $originalReports->add($report);
        }
        ////// EOF PRE Update INFO //////

        if( $routeName == "resapp_edit_default_interviewers" ) {
            $resappUtil->addDefaultInterviewers($entity);
        }

        $cycle = "edit";

        $form = $this->createResAppEditForm($entity,$cycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {

            //$this->getDoctrine()->getManager()->flush();
            //return $this->redirect($this->generateUrl('resapp_show',array('id' => $entity->getId())));

            /////////////// Process Removed Collections ///////////////
            $removedCollections = array();

            $removedInfo = $this->removeCollection($originalInterviews,$entity->getInterviews(),$entity);
            if( $removedInfo ) {
                $removedCollections[] = $removedInfo;
            }
            /////////////// EOF Process Removed Collections ///////////////

            $this->calculateScore($entity); // /edit/{id}  /edit-with-default-interviewers/{id}

            $this->processDocuments($entity);

            $this->assignResAppAccessRoles($entity);

            //DO NOT update reference hash ID, once it's generated. This hash ID will be used to auto attach recommendation letter to the reference's application.
            //$resappRecLetterUtil->generateResappRecLetterId($entity);

            $entity->autoSetRecLetterReceived();

            //set update author application
            $em = $this->getDoctrine()->getManager();
            $userUtil = new UserUtil();
            $secTokenStorage = $this->get('security.token_storage');
            $userUtil->setUpdateInfo($entity,$em,$secTokenStorage);


            /////////////// Add event log on edit (edit or add collection) ///////////////
            /////////////// Must run before flash DB. When DB is flashed getEntityChangeSet() will not work ///////////////
            $changedInfoArr = $this->setEventLogChanges($entity);

            //report (Complete Application PDF) diff
            $reportsDiffInfoStr = $this->recordToEvenLogDiffCollection($originalReports,$entity->getReports(),"Report");
            //echo "reportsDiffInfoStr=".$reportsDiffInfoStr."<br>";
            //exit('report');

            //set Edit event log for removed collection and changed fields or added collection
            if( count($changedInfoArr) > 0 || count($removedCollections) > 0 || $reportsDiffInfoStr ) {
                $event = "Residency Application ".$entity->getId()." information has been changed by ".$user.":"."<br>";
                $event = $event . implode("<br>", $changedInfoArr);
                $event = $event . "<br>" . implode("<br>", $removedCollections);
                $event = $event . $reportsDiffInfoStr;
                //echo "Diff event=".$event."<br>";
                $userSecUtil->createUserEditEvent($this->getParameter('resapp.sitename'),$event,$user,$entity,$request,'Residency Application Updated');
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            //don't regenerate report if it was added.
            //Regenerate if: report does not exists (reports count == 0) or if original reports are the same as current reports
            //echo "report count=".count($entity->getReports())."<br>";
            //echo "reportsDiffInfoStr=".$reportsDiffInfoStr."<br>";
            if( count($entity->getReports()) == 0 || $reportsDiffInfoStr == "" ) {
                $resappRepGen = $this->container->get('resapp_reportgenerator');
                $resappRepGen->addResAppReportToQueue( $entity->getId(), 'overwrite' );
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'A new Complete Residency Application PDF will be generated.'
                );
                //echo "Regenerate!!!! <br>";
            } else {
                //echo "NO Regenerate!!!! <br>";
            }
            //exit('report regen');

            //set logger for update
            //$user = $em->getRepository('AppUserdirectoryBundle:User')->find($user->getId()); //fetch user from DB otherwise keytype is null
            $event = "Residency Application with ID " . $id . " has been updated by " . $user;
            $userSecUtil->createUserEditEvent($this->getParameter('resapp.sitename'),$event,$user,$entity,$request,'Residency Application Updated');
            //exit('event='.$event);

            //return $this->redirect($this->generateUrl('resapp_show',array('id' => $entity->getId())));

            //redirect to a simple confirmation page
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Residency Application with ID '.$id.' has been updated.'
            );
            return $this->redirect($this->generateUrl('resapp_simple_confirmation',array('id' => $id)));

        } else {
//            if( !$form->isSubmitted() ){
//                echo "form is not submitted<br>";
//            }
//            if( !$form->isValid() ){
//                echo "form is not valid<br>";
//            }

            if( $routeName == "resapp_edit_default_interviewers" ) {
                $this->get('session')->getFlashBag()->add(
                    'pnotify',
                    "Important Note: Please manually review added default interviewers in the 'Interviews' section and click 'Update' button to save the changes!"
                );
            }

            //event log
            $em = $this->getDoctrine()->getManager();
            $actionStr = "viewed on edit page";
            $eventType = 'Residency Application Page Viewed';
            //$user = $em->getRepository('AppUserdirectoryBundle:User')->find($user->getId()); //fetch user from DB otherwise keytype is null
            $event = "Residency Application with ID".$id." has been ".$actionStr." by ".$user;

            $userSecUtil->createUserEditEvent($this->getParameter('resapp.sitename'),$event,$user,$entity,$request,$eventType);
        }

        $fullForm = false;
        
        return array(
            'form' => $form->createView(),
            'entity' => $entity,
            'pathbase' => 'resapp',
            'cycle' => $cycle,
            'sitename' => $this->getParameter('resapp.sitename'),
            'fullForm' => $fullForm
        );
    }
    private function createResAppEditForm( ResidencyApplication $entity, $cycle )
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $resappUtil = $this->container->get('resapp_util');

        //$resappUtil = $this->container->get('resapp_util');
        $resappUtil->addEmptyResAppFields($entity);

        $resTypes = $resappUtil->getResidencyTypesByInstitution(true);
        if( count($resTypes) == 0 ) {
            return array();
        }

        $resappVisas = $resappUtil->getResidencyVisaStatuses(false,false);

        $fullForm = false;

        $params = array(
            'cycle' => $cycle,
            'em' => $this->getDoctrine()->getManager(),
            'user' => $entity->getUser(),
            'cloneuser' => null,
            'roles' => $user->getRoles(),
            'container' => $this->container,
            'cycle_type' => "update",
            'resappTypes' => $resTypes,
            'resappVisas' => $resappVisas,
            'fullForm' => $fullForm
        );
        //Edit Form
        $form = $this->createForm( ResidencyApplicationType::class, $entity, array(
            'form_custom_value' => $params
        )); //update

        return $form;
    }


    public function calculateScore($entity) {
        $count = 0;
        $score = 0;
        foreach( $entity->getInterviews() as $interview ) {
            $totalRank = $interview->getTotalRank();
            if( $totalRank ) {
                $score = $score + $totalRank;
                $count++;
            }
        }
        if( $count > 0 ) {
            $score = $score/$count;
            $score = round($score,1);
        }

        $entity->setInterviewScore($score);
    }

    public function setEventLogChanges($entity) {

        $em = $this->getDoctrine()->getManager();

        $uow = $em->getUnitOfWork();
        $uow->computeChangeSets(); // do not compute changes if inside a listener

        $eventArr = array();

        //log simple fields
        $changeset = $uow->getEntityChangeSet($entity);
        $eventArr = $this->addChangesToEventLog( $eventArr, $changeset );

        //interviews
        foreach( $entity->getInterviews() as $subentity ) {
            $changeset = $uow->getEntityChangeSet($subentity);
            $text = "("."interview ".$this->getEntityId($subentity).")";
            $eventArr = $this->addChangesToEventLog( $eventArr, $changeset, $text );
        }

        return $eventArr;
    }
    public function removeCollection($originalArr,$currentArr,$entity) {
        $em = $this->getDoctrine()->getManager();
        $removeArr = array();

        foreach( $originalArr as $element ) {
            if( false === $currentArr->contains($element) ) {
                $removeArr[] = "<strong>"."Removed: ".$element." ".$this->getEntityId($element)."</strong>";

                if( $element instanceof Interview ) {
                    $entity->removeInterview($element);
                    //$element->setInterviewer(NULL);
                    $em->remove($element);
                }
            }
        } //foreach

        return implode("<br>", $removeArr);
    }
    public function addChangesToEventLog( $eventArr, $changeset, $text="" ) {

        $changeArr = array();

        //process $changeset: author, subjectuser, oldvalue, newvalue
        foreach( $changeset as $key => $value ) {
            if( $value[0] != $value[1] ) {

                if( is_object($key) ) {
                    //if $key is object then skip it, because we don't want to have non-informative record such as: credentials(stateLicense New): old value=, new value=Credentials
                    continue;
                }

                $field = $key;

                $oldValue = $value[0];
                $newValue = $value[1];

                if( $oldValue instanceof \DateTime ) {
                    $oldValue = $this->convertDateTimeToStr($value[0]);
                }
                if( $newValue instanceof \DateTime ) {
                    $newValue = $this->convertDateTimeToStr($value[1]);
                }

                if( is_array($oldValue) ) {
                    $oldValue = implode(", ",$oldValue);
                }
                if( is_array($newValue) ) {
                    $newValue = implode(", ",$newValue);
                }

                $event = "<strong>".$field.$text."</strong>".": "."old value=".$oldValue.", new value=".$newValue;
                //echo "event =".$event."<br>";
                //exit();

                $changeArr[] = $event;
            }
        }

        if( count($changeArr) > 0 ) {
            $eventArr[] = implode("<br>", $changeArr);
        }

        return $eventArr;

    }

    //record diff
    public function recordToEvenLogDiffCollection($originalArr,$currentArr,$text) {
        $removeArr = array();

        $original = $this->listToArray($originalArr);
        $new = $this->listToArray($currentArr);

        $diff = array_diff($original, $new);

        if( count($original) != count($new) || count($diff) != 0 ) {
            $removeArr[] = "<strong>"."Original ".$text.": ".implode(", ",$original)."</strong>";
            $removeArr[] = "<strong>"."New ".$text.": ".implode(", ",$new)."</strong>";
        }

        return implode("<br>", $removeArr);
    }
    public function listToArray($collection) {
        $resArr = array();
        foreach( $collection as $item ) {
            $resArr[] = $item."";
        }
        return $resArr;
    }

    public function convertDateTimeToStr($datetime) {
        $transformer = new DateTimeToStringTransformer(null,null,'m/d/Y');
        $dateStr = $transformer->transform($datetime);
        return $dateStr;
    }
    public function getEntityId($entity) {
        if( $entity->getId() ) {
            return "ID=".$entity->getId();
        }
        return "New";
    }

    /**
     * @Route("/applicant/new", name="resapp_create_applicant", methods={"POST"})
     * @Template("AppResAppBundle/Form/new.html.twig")
     */
    public function createApplicantAction( Request $request )
    {
        //exit("create form");
        if( false == $this->get('security.authorization_checker')->isGranted("create","ResidencyApplication") ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        //$resappRecLetterUtil = $this->container->get('resapp_rec_letter_util');
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $residencyApplication = new ResidencyApplication($user);

        $activeStatus = $em->getRepository('AppResAppBundle:ResAppStatus')->findOneByName("active");
        if( !$activeStatus ) {
            throw new EntityNotFoundException('Unable to find ResAppStatus by name='."active");
        }
        $residencyApplication->setAppStatus($activeStatus);

        if( !$residencyApplication->getUser() ) {
            //new applicant
            $addobjects = false;
            $applicant = new User($addobjects);
            $applicant->setPassword("");
            $applicant->setCreatedby('manual');
            $applicant->setAuthor($user);
            $applicant->addResidencyApplication($residencyApplication);
        }

        //add empty fields if they are not exist
        $resappUtil = $this->container->get('resapp_util');
        $resappUtil->addEmptyResAppFields($residencyApplication);

        $resappVisas = $resappUtil->getResidencyVisaStatuses(false,false);

        $resTypes = $resappUtil->getResidencyTypesByInstitution(true);
        //$resTypes = $resappUtil->getResidencyTypes(true);
        if( count($resTypes) == 0 ) {
            return array();
        }

        $params = array(
            'cycle' => 'new',
            'em' => $this->getDoctrine()->getManager(),
            'user' => $residencyApplication->getUser(),
            'cloneuser' => null,
            'roles' => $user->getRoles(),
            'container' => $this->container,
            'resappTypes' => $resTypes,
            'resappVisas' => $resappVisas

        );
        //$form = $this->createForm( new ResidencyApplicationType($params), $residencyApplication );
        $form = $this->createForm( ResidencyApplicationType::class, $residencyApplication, array('form_custom_value' => $params) ); //create new

//        $routeName = $request->get('_route');
//        $args = $this->getShowParameters($routeName,$residencyApplication); //create new
//        $form = $args['form_pure'];

        $form->handleRequest($request);

        if( !$form->isSubmitted() ) {
            //echo "form is not submitted<br>";
            $form->submit($request);
        }

        $applicant = $residencyApplication->getUser();

        if( !$residencyApplication->getResidencyTrack() ) {
            //exit("ResidencySpecialty is null");
            $form['residencyTrack']->addError(new FormError('Please select in the Residency Track before uploading'));
        }
        if( !$applicant->getEmail() ) {
            $form['user']['infos'][0]['email']->addError(new FormError('Please fill in the email before uploading'));
        }
        if( !$applicant->getFirstName() ) {
            $form['user']['infos'][0]['firstName']->addError(new FormError('Please fill in the First Name before uploading'));
        }
        if( !$applicant->getLastName() ) {
            $form['user']['infos'][0]['lastName']->addError(new FormError('Please fill in the Last Name before uploading'));
        }

//        if( !$form->isValid() ) {
//            //$errors = $this->getErrorMessages($form);
//            $errors = $form->getErrorsAsString();
//            var_dump($errors);
//            exit('form invalid');
//            //throw new HttpException(Codes::HTTP_BAD_REQUEST, implode("\n", $message));
//        }

        if( $form->isValid() ) {
            //exit('form valid');

            //set user
            $userSecUtil = $this->container->get('user_security_utility');
            $userkeytype = $userSecUtil->getUsernameType('local-user');
            if( !$userkeytype ) {
                throw new EntityNotFoundException('Unable to find local user keytype');
            }
            $applicant->setKeytype($userkeytype);

            $currentDateTime = new \DateTime();
            $currentDateTimeStr = $currentDateTime->format('m-d-Y-h-i-s');

            //Last Name + First Name + Email
            $applicantname = $applicant->getLastName()."_".$applicant->getFirstName()."_".$applicant->getEmail()."_".$currentDateTimeStr;
            $applicant->setPrimaryPublicUserId($applicantname);

            //set unique username
            $applicantnameUnique = $applicant->createUniqueUsername();
            $applicant->setUsername($applicantnameUnique);
            $applicant->setUsernameCanonical($applicantnameUnique);

            $applicant->setEmailCanonical($applicant->getEmail());
            $applicant->setPassword("");
            $applicant->setCreatedby('manual');

            $default_time_zone = $this->getParameter('default_time_zone');
            $applicant->getPreferences()->setTimezone($default_time_zone);
            $applicant->setLocked(true);

            //exit('form valid');

            $this->calculateScore($residencyApplication); // /applicant/new

            $this->processDocuments($residencyApplication);

            $this->assignResAppAccessRoles($residencyApplication);

            //create reference hash ID
            //$resappRecLetterUtil->generateResappRecLetterId($residencyApplication);

            $residencyApplication->autoSetRecLetterReceived();

            //set update author application
//            $em = $this->getDoctrine()->getManager();
//            $userUtil = new UserUtil();
//            $sc = $this->get('security.context');
//            $userUtil->setUpdateInfo($residencyApplication,$em,$sc);

            //exit('eof new applicant');

            $em = $this->getDoctrine()->getManager();
            $em->persist($residencyApplication);
            $em->persist($applicant);
            $em->flush();

            //update report if report does not exists
            //if( count($entity->getReports()) == 0 ) {
            $resappRepGen = $this->container->get('resapp_reportgenerator');
            $resappRepGen->addResAppReportToQueue( $residencyApplication->getId(), 'overwrite' );
            $this->get('session')->getFlashBag()->add(
                'notice',
                'A new Complete Residency Application PDF will be generated.'
            );
            //}

            //set logger for update
            $userSecUtil = $this->container->get('user_security_utility');
            $event = "Residency Application with ID " . $residencyApplication->getId() . " has been created by " . $user;
            $userSecUtil->createUserEditEvent($this->getParameter('resapp.sitename'),$event,$user,$residencyApplication,$request,'Residency Application Updated');


            return $this->redirect($this->generateUrl('resapp_show',array('id' => $residencyApplication->getId())));
        }

        //echo 'form invalid <br>';
        //exit('form invalid');

        return array(
            'form' => $form->createView(),
            'entity' => $residencyApplication,
            'pathbase' => 'resapp',
            'cycle' => 'new',
            'sitename' => $this->getParameter('resapp.sitename')
        );

    }

//    private function getErrorMessages(\Symfony\Component\Form\Form $form) {
//        $errors = array();
//        foreach ($form->getErrors() as $key => $error) {
//            $template = $error->getMessageTemplate();
//            $parameters = $error->getMessageParameters();
//
//            foreach($parameters as $var => $value){
//                $template = str_replace($var, $value, $template);
//            }
//
//            $errors[$key] = $template;
//        }
////        if ($form->hasChildren()) {
////            foreach ($form->getChildren() as $child) {
////                if (!$child->isValid()) {
////                    $errors[$child->getName()] = $this->getErrorMessages($child);
////                }
////            }
////        }
//        return $errors;
//    }


    //assign ROLE_RESAPP_INTERVIEWER corresponding to application
    public function assignResAppAccessRoles($application) {

        $em = $this->getDoctrine()->getManager();

        $residencyTrack = $application->getResidencyTrack();

        //////////////////////// INTERVIEWER ///////////////////////////
        $interviewerRoleResType = null;
        //$interviewerResTypeRoles = $em->getRepository('AppUserdirectoryBundle:Roles')->findByResidencyTrack($residencyTrack);
        $interviewerResTypeRoles = $em->getRepository('AppUserdirectoryBundle:Roles')->findByResidencyTrack($residencyTrack);
        foreach( $interviewerResTypeRoles as $role ) {
            if( strpos($role,'INTERVIEWER') !== false ) {
                $interviewerRoleResType = $role;
                break;
            }
        }
        if( !$interviewerRoleResType ) {
            //throw new EntityNotFoundException('Unable to find role by ResidencyTrack='.$residencyTrack);
            $logger = $this->container->get('logger');
            $logger->warning('Unable to find role by ResidencyTrack='.$residencyTrack);
            return false;
        }

        foreach( $application->getInterviews() as $interview ) {
            $interviewer = $interview->getInterviewer();
            if( $interviewer ) {

                //add general interviewer role                
                //$interviewer->addRole('ROLE_RESAPP_USER');
                //$interviewer->addRole('ROLE_RESAPP_INTERVIEWER');

                //add specific interviewer role
                $interviewer->addRole($interviewerRoleResType->getName());

            }
        }
        //////////////////////// EOF INTERVIEWER ///////////////////////////


        //////////////////////// OBSERVER ///////////////////////////
        foreach( $application->getObservers() as $observer ) {
            if( $observer ) {
                //add general observer role
                //$observer->addRole('ROLE_RESAPP_USER');
                $observer->addRole('ROLE_RESAPP_OBSERVER');
            }
        }
        //////////////////////// EOF OBSERVER ///////////////////////////

    }


    //process upload documents: CurriculumVitae(documents), ResidencyApplication(coverLetters), Examination(scores), ResidencyApplication(lawsuitDocuments), ResidencyApplication(reprimandDocuments)
    public function processDocuments($application) {

        $em = $this->getDoctrine()->getManager();

        //Avatar
        $em->getRepository('AppUserdirectoryBundle:Document')->processDocuments( $application, 'avatar' );

        //CurriculumVitae
        $em->getRepository('AppUserdirectoryBundle:Document')->processDocuments( $application, 'cv' );

        //ResidencyApplication(coverLetters)
        $em->getRepository('AppUserdirectoryBundle:Document')->processDocuments( $application, 'coverLetter' );
        $em->getRepository('AppUserdirectoryBundle:Document')->processDocuments( $application, 'lawsuitDocument');
        $em->getRepository('AppUserdirectoryBundle:Document')->processDocuments( $application, 'reprimandDocument' );

        //Examination
        foreach( $application->getExaminations() as $examination ) {
            $em->getRepository('AppUserdirectoryBundle:Document')->processDocuments( $examination );
        }

        //Reference .documents
        foreach( $application->getReferences() as $reference ) {
            $em->getRepository('AppUserdirectoryBundle:Document')->processDocuments( $reference );
        }

        //Other .documents
        $em->getRepository('AppUserdirectoryBundle:Document')->processDocuments( $application );

        //.itinerarys
        $em->getRepository('AppUserdirectoryBundle:Document')->processDocuments( $application, 'itinerary' );

        $em->getRepository('AppUserdirectoryBundle:Document')->processDocuments( $application, 'report' );
        $em->getRepository('AppUserdirectoryBundle:Document')->processDocuments( $application, 'formReport' );
        $em->getRepository('AppUserdirectoryBundle:Document')->processDocuments( $application, 'oldReport' );
    }


    /**
     * @Route("/change-status/{id}/{status}", name="resapp_status", methods={"GET"})
     * @Route("/status/{id}/{status}", name="resapp_status_email", methods={"GET"})
     */
    public function statusAction( Request $request, $id, $status ) {

        //$logger = $this->container->get('logger');
        //$logger->notice('statusAction: status='.$status);

        $entity = $this->getDoctrine()->getRepository('AppResAppBundle:ResidencyApplication')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Residency Application by id='.$id);
        }

        if( false == $this->get('security.authorization_checker')->isGranted("update","ResidencyApplication") ) {
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        //echo "id=$id <br>";
        //echo "status=$status <br>";
        //exit('eof status changed');

        $event = $this->changeResAppStatus($entity, $status, $request);

        $this->get('session')->getFlashBag()->add(
            'notice',
            $event
        );

        if( $request->get('_route') == 'resapp_status_email' ) {
            return $this->redirect( $this->generateUrl('resapp_show',array('id' => $id)) );
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode("ok"));
        return $response;
    }
    
    public function changeResAppStatus($resapp, $status, $request) {

        $resappUtil = $this->container->get('resapp_util');
        $logger = $this->container->get('logger');
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        //$status might have "-noemail". In this case remove "-noemail" and do not send a notification email.
        $sendEmail = true;
        if( strpos($status, "-noemail") !== false ) {
            $sendEmail = false;
            $status = str_replace("-noemail","",$status);
        }

        //get status object
        $statusObj = $em->getRepository('AppResAppBundle:ResAppStatus')->findOneByName($status);
        if( !$statusObj ) {
            $logger->error('statusAction: Unable to find ResAppStatus by name='.$status);
            throw new EntityNotFoundException('Unable to find ResAppStatus by name='.$status);           
        }

        //change status
        $resapp->setAppStatus($statusObj);

        $em->persist($resapp);
        $em->flush();

        //Every time an application is marked as "Priority", send an email to the user(s) with the corresponding "Residency Prpgram Coordinator" role (Cytopathology, etc), - in our case it will be Jessica - saying:
        if( $status == 'priority' ) {
            //$break = "\r\n";
            $break = "<br>";
            $directorEmails = $resappUtil->getDirectorsOfResAppEmails($resapp);
            $coordinatorEmails = $resappUtil->getCoordinatorsOfResAppEmails($resapp);
            $responsibleEmails = array_unique (array_merge ($coordinatorEmails, $directorEmails));
            $logger->notice("Residency application ".$resapp->getId()." status has been marked as Priority to the directors and coordinators emails " . implode(", ",$responsibleEmails));

            //Subject: FirstName LastName has marked FirstName LastName's ResidencyType residency application (ID:id#) as "Priority"
            $emailSubject = $user." has marked ".$resapp->getUser()->getUsernameShortest()."'s ".$resapp->getResidencyTrack().
                " residency application (ID:".$resapp->getId().") as 'Priority'";

            //Body: FirstName LastName (CWID: xxx1234) has marked FirstName LastName's ResidencyType
            // residency application (ID:id#) as "Priority" on MM/DD/YYY at HH:MM.
            //Link to the application:
            //Clickable Link leading to the application web page
            //Download the Application PDF:
            //Clickable link to the PDF of the entire application
            $applicationLink = $this->container->get('router')->generate(
                'resapp_show',
                array(
                    'id' => $resapp->getId(),
                ),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $linkToGeneratedApplicantPDF = $this->container->get('router')->generate(
                'resapp_view_pdf',
                array(
                    'id' => $resapp->getId()
                ),
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $currentDate = new \DateTime("now", new \DateTimeZone('America/New_York') );
            $currentDateStr = $currentDate->format('m/d/Y h:i A T');
            $emailBody = $emailSubject." on ".$currentDateStr.".".$break.$break;
            $emailBody .= "Link to the application:".$break;
            $emailBody .= $applicationLink;
            $emailBody .= $break.$break."Download the Application PDF:".$break;
            $emailBody .= $linkToGeneratedApplicantPDF;
            $emailUtil = $this->container->get('user_mailer_utility');
            $emailUtil->sendEmail( $responsibleEmails, $emailSubject, $emailBody );
        }

        if( $sendEmail && $status == 'acceptedandnotified' ) {
            $resappUtil->sendAcceptedNotificationEmail($resapp);
        }

        if( $sendEmail && $status == 'rejectedandnotified' ) {
            $resappUtil->sendRejectedNotificationEmail($resapp);
        }

        $eventType = 'Residency Application Status changed to ' . $statusObj->getAction();

        $userSecUtil = $this->container->get('user_security_utility');
        $event = $eventType . '; application ID ' . $resapp->getID() . ' by user ' . $user;
        $userSecUtil->createUserEditEvent($this->getParameter('resapp.sitename'),$event,$user,$resapp,$request,$eventType);
        
        return $event;
    }


//    /**
//     * @Route("/status-sync/", name="resapp_sincstatus", methods={"GET"})
//     */
//    public function syncStatusAction( Request $request ) {
//
//        $em = $this->getDoctrine()->getManager();
//        $applications = $this->getDoctrine()->getRepository('AppResAppBundle:ResidencyApplication')->findAll();
//
//        foreach( $applications as $application ) {
//            $status = $application->getApplicationStatus();
//            $statusObj = $em->getRepository('AppResAppBundle:ResAppStatus')->findOneByName($status);
//            if( !$statusObj ) {
//                throw new EntityNotFoundException('Unable to find ResAppStatus by name='.$status);
//            }
//            $application->setAppStatus($statusObj);
//            //$application->setApplicationStatus(NULL);
//        }
//
//        $em->flush();
//
//        return $this->redirect( $this->generateUrl('resapp_home') );
//    }

    /**
     * @Route("/application-evaluation/show/{id}", name="resapp_application_show", methods={"GET"})
     * @Route("/application-evaluation/{id}", name="resapp_application_edit", methods={"GET"})
     * @Template("AppResAppBundle/Interview/interview_selector.html.twig")
     */
    public function applicationAction( Request $request, ResidencyApplication $resapp )
    {

        //echo "status <br>";

        if( false == $this->get('security.authorization_checker')->isGranted("create", "Interview") ) {
            return $this->redirect($this->generateUrl('resapp-nopermission'));
        }

        $resappUtil = $this->container->get('resapp_util');
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $routeName = $request->get('_route');
        $cycle = "show";

        if( $routeName == "resapp_application_edit" ) {
            $cycle = "edit";
        }

        //1) check if this user is an interviewer for this application
        $interviews = $resappUtil->findInterviewByResappAndUser($resapp,$user);
        if( count($interviews) > 0 ) {
            if( count($interviews) == 1 ) {
                $interview = $interviews[0];
                if ($routeName == "resapp_application_edit") {
                    return $this->redirect($this->generateUrl('resapp_interview_edit', array('id' => $interview->getId())));
                } else {
                    return $this->redirect($this->generateUrl('resapp_interview_show', array('id' => $interview->getId())));
                }
            } else {
                if( count($interviews) > 0 ) {
                    //show all interviews selector
                    return array(
                        'resapp' => $resapp,
                        'interviews' => $interviews,
                        'cycle' => $cycle,
                        'sitename' => $this->getParameter('resapp.sitename')
                    );
                }
            }

        } else {
            //this user is not interviewer for this application
            if ($this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_COORDINATOR') ||
                $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_DIRECTOR') ||
                $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_ADMIN')
            ) {
                //show all interviews selector
                $interviews = $resapp->getInterviews();
                return array(
                    'resapp' => $resapp,
                    'interviews' => $interviews,
                    'cycle' => $cycle,
                    'sitename' => $this->getParameter('resapp.sitename')
                );
            }
        }

        return $this->redirect($this->generateUrl('resapp-nopermission'));
    }

    /**
     * @Route("/interview-evaluation/show/{id}", name="resapp_interview_show", methods={"GET"})
     * @Route("/interview-evaluation/{id}", name="resapp_interview_edit", methods={"GET"})
     * @Template("AppResAppBundle/Interview/new.html.twig")
     */
    public function interviewAction( Request $request, $id ) {

        //echo "status <br>";

//        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_INTERVIEWER') ){
//            return $this->redirect( $this->generateUrl('resapp-nopermission') );
//        }
        if( false == $this->get('security.authorization_checker')->isGranted("create","Interview") ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $routeName = $request->get('_route');

        $interview = $em->getRepository('AppResAppBundle:Interview')->find($id);

        if( !$interview ) {
            throw $this->createNotFoundException('Unable to find Residency Application Interview by id='.$id);
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        //check if the interviewer is the same as current user
        $interviewer = $interview->getInterviewer();
        //echo "interviewer=".$interviewer."<br>";
        $interviewerId = null;
        if( $interviewer ) {
            $interviewerId = $interviewer->getId();
        } else {
            throw $this->createNotFoundException('Interviewer is undefined');
        }
        //echo $user->getId()."?=".$interviewerId."<br>";

        if( $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_COORDINATOR') ||
            $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_DIRECTOR') ||
            $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_ADMIN')
        ){
            //allow
        } else {
            if ($user->getId() != $interviewerId) {
                return $this->redirect($this->generateUrl('resapp-nopermission'));
            }
        }

        if( $routeName == "resapp_interview_edit" && $interview->getTotalRank() && $interview->getTotalRank() > 0 ) {
            return $this->redirect( $this->generateUrl('resapp_interview_show',array('id' => $interview->getId())) );
        }

        if( $routeName == "resapp_interview_show" ) {
            $cycle = "show";
            $method = "GET";
            $action = null;
            $disabled = true;
        }

        if( $routeName == "resapp_interview_edit" ) {
            $cycle = "edit";
            $method = "POST";
            $action = $this->generateUrl('resapp_interview_update', array('id' => $interview->getId()));
            $disabled = false;
        }

        $params = array(
            'cycle' => $cycle,
            'container' => $this->container,
            'em' => $em,
            'interviewer' => $interview->getInterviewer(),
            'showFull' => false
        );

        //InterviewType($params)
        $form = $this->createForm(
            //new InterviewType($params),
            InterviewType::class,
            $interview,
            array(
                'form_custom_value' => $params,
                'disabled' => $disabled,
                'method' => $method,
                'action' => $action
            )
        );

        return array(
            'form' => $form->createView(),
            'entity' => $interview,
            'pathbase' => 'resapp',
            'cycle' => $cycle,
            'sitename' => $this->getParameter('resapp.sitename')
        );

    }

    /**
     * @Route("/interview/update/{id}", name="resapp_interview_update", methods={"POST"})
     * @Template("AppResAppBundle/Interview/new.html.twig")
     */
    public function interviewUpdateAction( Request $request, $id ) {

        //echo "status <br>";

//        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_INTERVIEWER') ){
//            return $this->redirect( $this->generateUrl('resapp-nopermission') );
//        }
        if( false == $this->get('security.authorization_checker')->isGranted("create","Interview") ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();

        $interview = $em->getRepository('AppResAppBundle:Interview')->find($id);

        if( !$interview ) {
            throw $this->createNotFoundException('Unable to find Residency Application Interview by id='.$id);
        }

        //check if the interviewer is the same as current user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if( $user->getId() != $interview->getInterviewer()->getId() ) {
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        $cycle = 'edit';
        $method = "POST";
        $action = $this->generateUrl('resapp_interview_update', array('id' => $interview->getId()));
        $disabled = false;

        $params = array(
            'cycle' => $cycle,
            'container' => $this->container,
            'em' => $em,
            'interviewer' => $interview->getInterviewer(),
            'showFull' => false
        );
        $form = $this->createForm(
            //new InterviewType($params),
            InterviewType::class,
            $interview,
            array(
                'form_custom_value' => $params,
                'disabled' => $disabled,
                'method' => $method,
                'action' => $action
            )
        );

        $form->handleRequest($request);

        $formCompleted = false;
        if( $interview->getTotalRank() && $interview->getTotalRank() > 0 ) {
            $formCompleted = true;
        }

        if( $form->isValid() && $formCompleted ) {

//            $interviewer = $interview->getInterviewer();
//            echo "interviewer=".$interviewer."<br>";
//            if( !$interviewer ) {
//                exit('no interviewer');
//            }
//            exit('1');

            $resapp = $interview->getResapp();

            $this->calculateScore($resapp); // /interview/update/{id}
            
            //Upon submitting the first interview evaluation form for a given application, 
            //if the current application status is not "Interviewee", automatically switch it to "Interviewee".
            if( $resapp->getAppStatus()->getName()."" != "interviewee" ) {
                $this->changeResAppStatus($resapp, "interviewee", $request);
            }
            
            $em->persist($interview);
            $em->flush();


            $applicant = $resapp->getUser();
            $eventType = 'Residency Interview Evaluation Updated';
            $userSecUtil = $this->container->get('user_security_utility');
            $user = $this->get('security.token_storage')->getToken()->getUser();
            //$event = $eventType . '; application ID ' . $resapp->getId();
            $event = 'Residency Interview Evaluation for applicant '.$applicant->getUsernameOptimal().' (ID: '.$resapp->getId().') has been submitted by ' . $user->getUsernameOptimal();
            $userSecUtil->createUserEditEvent($this->getParameter('resapp.sitename'),$event,$user,$resapp,$request,$eventType);

            //return $this->redirect( $this->generateUrl('resapp_home'));

            $this->get('session')->getFlashBag()->add(
                'notice',
                $event
            );

            return $this->redirect( $this->generateUrl('resapp_interview_show',array('id' => $interview->getId())) );
        }


        return array(
            'form' => $form->createView(),
            'entity' => $interview,
            'pathbase' => 'resapp',
            'cycle' => $cycle,
            'sitename' => $this->getParameter('resapp.sitename')
        );

    }


//    /**
//     * @Route("/interview/new/{resappid}/{interviewid}", name="resapp_interview_new", methods={"GET"})
//     * @Route("/interview/new/{resappid}/{interviewid}", name="resapp_interview_new", methods={"GET"})
//     * @Template("AppResAppBundle/Interview/new.html.twig")
//     */
//    public function createInterviewAction( Request $request ) {
//
//        //echo "status <br>";
//
//        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_INTERVIEWER') ){
//            return $this->redirect( $this->generateUrl('resapp-nopermission') );
//        }
//
//        $em = $this->getDoctrine()->getManager();
//
//        $interview = $this->getDoctrine()->getRepository('AppResAppBundle:Interview')->find($id);
//
//        if( !$interview ) {
//            throw $this->createNotFoundException('Unable to find Residency Application Interview by id='.$id);
//        }
//
//        $cycle = "new";
//
//        $params = array(
//            'cycle' => $cycle,
//            'sc' => $this->get('security.context'),
//            'em' => $this->getDoctrine()->getManager(),
//        );
//        $form = $this->createForm( new InterviewType($params), $interview );
//
//        return array(
//            'form' => $form->createView(),
//            'entity' => $interview,
//            'pathbase' => 'resapp',
//            'cycle' => $cycle,
//            'sitename' => $this->getParameter('resapp.sitename')
//        );
//
//    }







    /**
     * @Route("/remove/{id}", name="resapp_remove")
     */
    public function removeAction($id) {

        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_ADMIN') ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        //echo "remove <br>";
        exit('remove not supported');

        return $this->redirect( $this->generateUrl('resapp_home') );
    }




    /**
     * Manually import and populate applicants from Google
     *
     * @Route("/populate-import", name="resapp_import_populate")
     */
    public function importAndPopulateAction(Request $request) {

        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_ADMIN') ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        $resappImportPopulateUtil = $this->container->get('resapp_importpopulate_util');

        $result = $resappImportPopulateUtil->processResAppFromGoogleDrive();

        $this->get('session')->getFlashBag()->add(
            'notice',
            $result
        );

        return $this->redirect( $this->generateUrl('resapp_home') );

//        //1) import
//        $fileDb = $resappUtil->importResApp();
//
//        if( $fileDb ) {
//            $event = "Residency Application Spreadsheet file has been successful downloaded to the server with id=" . $fileDb->getId().", title=".$fileDb->getUniquename();
//            $flashType = 'notice';
//        } else {
//            $event = "Residency Application Spreadsheet download failed!";
//            $flashType = 'warning';
//            $error = true;
//        }
//
//        $this->get('session')->getFlashBag()->add(
//            $flashType,
//            $event
//        );
//
//        if( $error ) {
//            return $this->redirect( $this->generateUrl('resapp_home') );
//        }
//
//        //2) populate
//        $populatedCount = $resappUtil->populateResApp();
//
//        if( $populatedCount >= 0 ) {
//            $event = "Populated ".$populatedCount." Residency Applicantions.";
//            $flashType = 'notice';
//        } else {
//            $event = "Google API service failed!";
//            $flashType = 'warning';
//        }
//
//        $this->get('session')->getFlashBag()->add(
//            $flashType,
//            $event
//        );
//
//        return $this->redirect( $this->generateUrl('resapp_home') );
    }

    /**
     * Manually import and populate recommendation letters from Google
     *
     * @Route("/populate-import-letters", name="resapp_import_populate_letters")
     */
    public function importAndPopulateLettersAction(Request $request) {

        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_ADMIN') ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        $resappRecLetterUtil = $this->get('resapp_rec_letter_util');

        $result = $resappRecLetterUtil->processResRecLetterFromGoogleDrive();

        $this->get('session')->getFlashBag()->add(
            'notice',
            $result
        );

        return $this->redirect( $this->generateUrl('resapp_home') );
    }

    /**
     * Show home page
     *
     * @Route("/populate", name="resapp_populate")
     */
    public function populateSpreadsheetAction(Request $request) {

        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_ADMIN') ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        $resappUtil = $this->container->get('resapp_util');
        $populatedCount = $resappUtil->populateResApp();

        if( $populatedCount >= 0 ) {
            $event = "Populated ".$populatedCount." Residency Applicantions.";
            $flashType = 'notice';
        } else {
            $event = "Google API service failed!";
            $flashType = 'warning';
        }

        $this->get('session')->getFlashBag()->add(
            $flashType,
            $event
        );

        return $this->redirect( $this->generateUrl('resapp_home') );
    }


    /**
     * Import spreadsheet to C:\Program Files (x86)\pacsvendor\pacsname\htdocs\order\scanorder\Scanorders2\web\Uploaded\resapp\Spreadsheets
     *
     * @Route("/import", name="resapp_import")
     */
    public function importAction(Request $request) {

        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_ADMIN') ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        $resappUtil = $this->container->get('resapp_util');
        $fileDb = $resappUtil->importResApp();

        if( $fileDb ) {
            $event = "Residency Application Spreadsheet file has been successful downloaded to the server with id=" . $fileDb->getId().", title=".$fileDb->getUniquename();
            $flashType = 'notice';
        } else {
            $event = "Residency Application Spreadsheet download failed!";
            $flashType = 'warning';
        }

        $this->get('session')->getFlashBag()->add(
            $flashType,
            $event
        );

        //exit('import event'.$event);

        return $this->redirect( $this->generateUrl('resapp_home') );

//        //$excelFile = $this->printFile($service, $excelId);
//
//        //$response = $this->downloadFile($service, $excelFile, 'excel');
//
//        //echo "response=".$response."<br>";
//
//        exit(1);
//
//
////        $files = $service->files->listFiles();
////        echo "count files=".count($files)."<br>";
////        //echo "<pre>"; print_r($files);
////        foreach( $files as $item ) {
////            echo "title=".$item['title']."<br>";
////        }
//
//        //https://drive.google.com/open?id=0B2FwyaXvFk1edWdMdTlFTUt1aVU
//        $folderId = "0B2FwyaXvFk1edWdMdTlFTUt1aVU";
//        //https://drive.google.com/open?id=0B2FwyaXvFk1efmc2VGVHUm5yYjJRWGFYYTF0Z2N6am9iUFVzcTc1OXdoWEl1Vmc0LWdZc0E
//        //$folderId = "0B2FwyaXvFk1efmc2VGVHUm5yYjJRWGFYYTF0Z2N6am9iUFVzcTc1OXdoWEl1Vmc0LWdZc0E";
//        //$files = $this->printFilesInFolder($service, $folderId);
//
//
//        $photoId = "0B2FwyaXvFk1eRnJVS1N0MWhkc0E";
//        $file = $this->printFile($service, $photoId);
//        $response = $this->downloadFile($service, $file);
//        echo "response=".$response."<br>";
//
//        exit('1');
//
//        // Exchange authorization code for access token
//        //$accessToken = $client->authenticate($authCode);
//        //$client->setAccessToken($accessToken);
//
//        $fileId = "1DN1BEbONKNmFpHU6xBo69YSLjXCnhRy0IbyXrwMzEzc";
//
//        $file = $this->printFile($service, $fileId);
//
//        echo "after file <br>";
//
//        $response = $this->downloadFile($service,$file);
//
//        print_r($response);
//
//        echo "response=".$response."<br>";
//        //exit();
//        return $response;
//
//        return $this->redirect( $this->generateUrl('resapp_home') );
    }




//    /**
//     * NOT USED NOW
//     * update report by js
//     *
//     * @Route("/update-report/", name="resapp_update_report", methods={"POST"}, options={"expose"=true})
//     */
//    public function updateReportAction(Request $request) {
//
//        $id = $request->get('id');
//
//        $em = $this->getDoctrine()->getManager();
//        $entity = $em->getRepository('AppResAppBundle:ResidencyApplication')->find($id);
//
//        if( !$entity ) {
//            throw $this->createNotFoundException('Unable to find Residency Application by id='.$id);
//        }
//
//        echo "reports = " . count($entity->getReports()) . "<br>";
//        exit();
//
//        //update report if report does not exists
//        if( count($entity->getReports()) == 0 ) {
//            $resappRepGen = $this->container->get('resapp_reportgenerator');
//            $resappRepGen->addResAppReportToQueue( $id, 'overwrite' );
//        }
//
//        $response = new Response();
//        $response->setContent('Sent to queue');
//        return $response;
//    }


    /**
     * Download application using
     * https://github.com/KnpLabs/KnpSnappyBundle
     * https://github.com/devandclick/EnseparHtml2pdfBundle
     *
     * @Route("/download-pdf/{id}", name="resapp_download_pdf", methods={"GET"})
     * @Route("/view-pdf/{id}", name="resapp_view_pdf", methods={"GET"})
     */
    public function downloadReportAction(Request $request, $id) {

//        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_USER') ){
//            return $this->redirect( $this->generateUrl('resapp-nopermission') );
//        }
//        if( false == $this->get('security.authorization_checker')->isGranted("read","ResidencyApplication") ){
//            return $this->redirect( $this->generateUrl('resapp-nopermission') );
//        }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppResAppBundle:ResidencyApplication')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Residency Application by id='.$id);
        }

        //user who has the same res type can view or edit
        $resappUtil = $this->container->get('resapp_util');
        if( $resappUtil->hasResappPermission($user,$entity) == false ) {
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }
        if(
            false == $this->get('security.authorization_checker')->isGranted("read",$entity) &&
            false == $this->get('security.authorization_checker')->isGranted("create","Interview")
        ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        //event log
        $userSecUtil = $this->container->get('user_security_utility');
        $event = "Report for Residency Application with ID".$id." has been downloaded by ".$user;
        $userSecUtil->createUserEditEvent($this->getParameter('resapp.sitename'),$event,$user,$entity,null,'Complete Residency Application PDF Downloaded');

        $reportDocument = $entity->getRecentReport();
        //echo "report=".$reportDocument."<br>";
        //exit();

        if( $reportDocument ) {

            $routeName = $request->get('_route');

            if( $routeName == "resapp_view_pdf" ) {
                return $this->redirect( $this->generateUrl('resapp_file_view',array('id' => $reportDocument->getId())) );
            } else {
                return $this->redirect( $this->generateUrl('resapp_file_download',array('id' => $reportDocument->getId())) );
            }

        } else {

            //create report
            $resappRepGen = $this->container->get('resapp_reportgenerator');
            $argument = 'asap';
            //if( $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_COORDINATOR') ) {
                //$argument = 'overwrite';
            //}
            $resappRepGen->addResAppReportToQueue( $id, $argument );

            //exit('resapp_download_pdf exit');

            $this->get('session')->getFlashBag()->add(
                'warning',
                'Complete Application PDF is not ready yet. Please try again later.'
            );

            return $this->redirect( $this->generateUrl('resapp_show',array('id' => $id)) );
        }

    }

    /**
     * Download itinerary
     * @Route("/download-itinerary-pdf/{id}", name="resapp_download_itinerary_pdf", methods={"GET"})
     */
    public function downloadItineraryAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppResAppBundle:ResidencyApplication')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Residency Application by id='.$id);
        }

        $scheduleDocument = $entity->getRecentItinerary();
        if( $scheduleDocument ) {
            return $this->redirect( $this->generateUrl('resapp_file_download',array('id' => $scheduleDocument->getId())) );
        }

        return null;
    }

    /**
     * @Route("/regenerate-all-complete-application-pdfs/", name="resapp_regenerate_reports")
     *
     * @Template("AppResAppBundle/Form/new.html.twig")
     */
    public function regenerateAllReportsAction(Request $request) {

        exit("This method is disabled for security reason.");

        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_ADMIN') ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        $resappRepGen = $this->container->get('resapp_reportgenerator');
        $numDeleted = $resappRepGen->regenerateAllReports();

        $em = $this->getDoctrine()->getManager();
        $resapps = $em->getRepository('AppResAppBundle:ResidencyApplication')->findAll();
        $estimatedTime = count($resapps)*5; //5 min for each report
        $this->get('session')->getFlashBag()->add(
            'notice',
            'All Application Reports will be regenerated. Estimated processing time for ' . count($resapps) . ' reports is ' . $estimatedTime . ' minutes. Number of deleted processes in queue ' . $numDeleted
        );

        return $this->redirect( $this->generateUrl('resapp_home') );
    }

    /**
     * @Route("/reset-queue-and-run/", name="resapp_reset_queue_run")
     *
     * @Template("AppResAppBundle/Form/new.html.twig")
     */
    public function resetQueueRunAction(Request $request) {

        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_ADMIN') ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

//        //testing
//        $resappRepGen = $this->container->get('resapp_reportgenerator');
//        $loc = "C:\\Users\\ch3\\Desktop\\";
//        $filepath = $loc."badpdf.pdf";
//        $filepath = $loc."goodpdf.pdf";
//        if( $resappRepGen->isPdfCorrupted($filepath) ) {
//            echo "corrupted<br>";
//        } else {
//            echo "not corrupted<br>";
//        }
//
//        $filepath = "E:\Program Files (x86)\Aperio\Spectrum\htdocs\order\scanorder\Scanorders2\web\Uploaded\resapp\documents\5ba3cc18bae60.pdf";
//        $userSecUtil = $this->container->get('user_security_utility');
//        $errorMsg = "convert To Pdf: PDF is corrupted; filePath=".$filepath;
//        $userSecUtil->sendEmailToSystemEmail("Convert to PDF failed", $errorMsg);
//        exit();

        $resappRepGen = $this->container->get('resapp_reportgenerator');
        $numUpdated = $resappRepGen->resetQueueRun();

        $em = $this->getDoctrine()->getManager();
        $processes = $em->getRepository('AppResAppBundle:Process')->findAll();
        $estimatedTime = count($processes)*5; //5 min for each report
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Queue with ' . count($processes) . ' will be re-run. Estimated processing time is ' . $estimatedTime . ' minutes. Number of reset processes in queue ' . $numUpdated
        );

        //return $this->redirect( $this->generateUrl('resapp_home') );
        return $this->redirect( $this->generateUrl('main_common_home') );
    }


    
    /**
     * @Route("/download-applicants-list-excel/{currentYear}/{resappTypeId}/{resappIds}", name="resapp_download_applicants_list_excel")
     */
    public function downloadApplicantListExcelAction(Request $request, $currentYear, $resappTypeId, $resappIds) {

//        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_COORDINATOR') &&
//            false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_DIRECTOR') &&
//            false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_INTERVIEWER') &&
//            false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_OBSERVER')
//        ){
//            return $this->redirect( $this->generateUrl('resapp-nopermission') );
//        }
        if( false == $this->get('security.authorization_checker')->isGranted("read","ResidencyApplication") ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }
       
        $em = $this->getDoctrine()->getManager();
        $residencyTrack = null;
        $institutionNameResappName = "";
        
        if( $resappTypeId && $resappTypeId > 0 ) {
            $residencyTrack = $em->getRepository('AppUserdirectoryBundle:ResidencyTrackList')->find($resappTypeId);
        }
        
        if( $residencyTrack ) {
            $institution = $residencyTrack->getInstitution();
            $institutionNameResappName = $institution." ".$residencyTrack." ";
        }
        
        $resappUtil = $this->container->get('resapp_util');

        if(0) {
            //[YEAR] [WCMC (top level of actual institution)] [RESIDENCY-TYPE] Residency Candidate Data generated on [DATE] at [TIME] EST.xls
            $fileName = $currentYear." ".$institutionNameResappName."Residency Candidate Data generated on ".date('m/d/Y H:i').".xlsx";
            $fileName = str_replace("  ", " ", $fileName);
            $fileName = str_replace(" ", "-", $fileName);

            $excelBlob = $resappUtil->createApplicantListExcel($resappIds);

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excelBlob, 'Xlsx');
            //ob_end_clean();
            //$writer->setIncludeCharts(true);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            //header('Content-Disposition: attachment;filename="fileres.xlsx"');

            // Write file to the browser
            $writer->save('php://output');
            exit();
        }

        //Spout
        if(1) {

            //[YEAR] [WCMC (top level of actual institution)] [RESIDENCY-TYPE] Residency Candidate Data generated on [DATE] at [TIME] EST.xls
            $fileName = $currentYear." ".$institutionNameResappName."Residency Candidate Data generated on ".date('m-d-Y').".xlsx";
            $fileName = str_replace("  ", " ", $fileName);
            $fileName = str_replace(" ", "-", $fileName);
            $fileName = str_replace(",", "-", $fileName);

            $resappUtil->createApplicantListExcelSpout($resappIds,$fileName);
            exit();
        }

        exit();      
    }

    /**
     * @Route("/send-rejection-emails-action/", name="resapp_send_rejection_emails_action", methods={"POST"}, options={"expose"=true})
     * @Template("AppResAppBundle/Form/send-notification-emails.html.twig")
     */
    public function sendRejectionEmailsAction(Request $request) {

        if( false == $this->get('security.authorization_checker')->isGranted('ROLE_RESAPP_ADMIN') ){
            return $this->redirect( $this->generateUrl('resapp-nopermission') );
        }

        $logger = $this->container->get('logger');
        $em = $this->getDoctrine()->getManager();

        //show applications for current year (show the same list as home page)
        //$ids = $request->get('ids');
        //echo "ids=".$ids."<br>";
        //$logger->notice("Rejection ids=".$ids);

        //$idsArr = explode(",",$ids);

        $ids = $request->get('ids');

        foreach($ids as $id) {
            //$logger->notice("Rejection id=".$id);
            $resapp = $em->getRepository('AppResAppBundle:ResidencyApplication')->find($id);
            if( $resapp ) {
                $logger->notice("Rejection email id=".$id);
                //set status to Rejected and Notified
                //send rejection email
                //record to eventlog
                $status = "rejectedandnotified";
                $event = $this->changeResAppStatus($resapp, $status, $request);

                $this->get('session')->getFlashBag()->add(
                    'notice',
                    $event
                );

//                $this->get('session')->getFlashBag()->add(
//                    'notice',
//                    'Rejection notification email has been sent to '.$id
//                );
            }
        }

        //exit('111');
        //exit();

        //$url = $this->generateUrl('main_common_home');
        $url = $this->generateUrl('resapp_home');
        return new Response($url);
    }






    ///////////////////// un used methods //////////////////////////
    /**
     * Print files belonging to a folder.
     *
     * @param Google_Service_Drive $service Drive API service instance.
     * @param String $folderId ID of the folder to print files from.
     */
    function printFilesInFolder($service, $folderId) {
        $pageToken = NULL;

        do {
            try {
                $parameters = array();
                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }
                $children = $service->children->listChildren($folderId, $parameters);
                echo "count=".count($children->getItems())."<br>";

                foreach ($children->getItems() as $child) {
                    //print 'File Id: ' . $child->getId()."<br>";
                    //print_r($child);
                    $this->printFile($service,$child->getId());
                }
                $pageToken = $children->getNextPageToken();
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
                $pageToken = NULL;
            }
        } while ($pageToken);
    }

    function getFilesByAuthUrl() {
        $client_id = "1040591934373-hhm896qpgdaiiblaco9jdfvirkh5f65q.apps.googleusercontent.com";
        $client_secret = "RgXkEm2_1T8yKYa3Vw_tIhoO";
        $redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';    //"http://localhost";

        $res = $this->buildService($client_id,$client_secret,$redirect_uri);

        $service = $res['service'];
        $client = $res['client'];

        $authUrl = $client->createAuthUrl();
        echo "authUrl=".$authUrl."<br>";

        // Exchange authorization code for access token
        $accessToken = $client->authenticate('4/OrVeRdkw9eByckCs7Gtn0B4eUwhERny8AqFOAwy29fY');
        $client->setAccessToken($accessToken);

        $files = $service->files->listFiles();
        echo "count files=".count($files)."<br>";
        echo "<pre>"; print_r($files);
    }

    /**
     * Build a Drive service object.
     */
    function buildService($client_id,$client_secret,$redirect_uri) {
        $client = new \Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);

        //$client->addScope("https://www.googleapis.com/auth/drive");
        $client->setScopes(array('https://www.googleapis.com/auth/drive'));
        $client->setAccessType('offline');

        $service = new \Google_Service_Drive($client);

        $res = array(
            'client' => $client,
            'service' => $service
        );
        return $res;
    }

    /**
     * Print a file's metadata.
     *
     * @param apiDriveService $service Drive API service instance.
     * @param string $fileId ID of the file to print metadata for.
     */
    function printFile($service, $fileId) {
        $file = null;
        try {
            $file = $service->files->get($fileId);

            print "Title: " . $file->getTitle()."<br>";
            print "ID: " . $file->getId()."<br>";
            print "Size: " . $file->getFileSize()."<br>";
            //print "URL: " . $file->getDownloadUrl()."<br>";
            print "Description: " . $file->getDescription()."<br>";
            print "MIME type: " . $file->getMimeType()."<br>"."<br>";

        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
        return $file;
    }



    /**
     * Show home page
     *
     * @Route("/test", name="resapp_test", methods={"GET"})
     */
    public function testAction() {

        exit('tests');

        //test url on console
//        $resappUtil = $this->container->get('resapp_util');
//        $em = $this->getDoctrine()->getManager();
//        $residencyApplication = $em->getRepository('AppResAppBundle:ResidencyApplication')->find(162);
//        $resappUtil->sendConfirmationEmailsOnApplicationPopulation($residencyApplication,$residencyApplication->getUser());
//        return new Response("OK Test");
//        exit('email test');

        $googleSheetManagement = $this->container->get('resapp_googleSheetManagement');

        //$res = $googleSheetManagement->searchSheet();
        //exit('searchSheet res='.$res);

        $excelId = "156lKGi2cxSbHI3sMN8hiRZLZbLuSQVmisZYARxYWZsM";
        $rowId = "cinava7_yahoo.com_Doe_Linda_2016-03-15_17_59_53";

        //$res = $googleSheetManagement->deleteImportedApplicationAndUploadsFromGoogleDrive($excelId,$rowId);
        //exit('googleSheetManagement res='.$res);
        exit('no test');


        //include_once "vendor/google/apiclient/examples/simple-query.php";
        include_once "vendor/google/apiclient/examples/user-example.php";
        //include_once "vendor/google/apiclient/examples/idtoken.php";


        return new Response("OK Test");
    }

}
