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


use App\VacReqBundle\Form\VacReqCalendarFilterType;
use App\UserdirectoryBundle\Controller\OrderAbstractController;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\VacReqBundle\Util\iCalEasyReader;
use App\VacReqBundle\Util\iCalendar;
use App\VacReqBundle\Util\ics;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//use ADesigns\CalendarBundle\Event\CalendarEvent;
//use ADesigns\CalendarBundle\Entity\EventEntity;

//vacreq site

class CalendarController extends OrderAbstractController
{

    /**
     * Template("AppVacReqBundle/Calendar/calendar.html.twig")
     * show the names of people who are away that day (one name per "event"/line).
     *
     * @Route("/away-calendar/", name="vacreq_awaycalendar", methods={"GET"})
     * @Template("AppVacReqBundle/Calendar/calendar-tattali.html.twig")
     */
    public function awayCalendarAction(Request $request) {

        if(
            false == $this->isGranted('ROLE_VACREQ_OBSERVER') &&
            false == $this->isGranted('ROLE_VACREQ_SUBMITTER') &&
            false == $this->isGranted('ROLE_VACREQ_PROXYSUBMITTER') &&
            false == $this->isGranted('ROLE_VACREQ_APPROVER') &&
            false == $this->isGranted('ROLE_VACREQ_SUPERVISOR')
        ) {
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }

        $userServiceUtil = $this->container->get('user_service_utility');
        $vacreqUtil = $this->container->get('vacreq_util');
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $params = array();
        $params['em'] = $em;
        $params['supervisor'] = $this->isGranted('ROLE_VACREQ_SUPERVISOR');

        ///// NOT USED /////
//        if(0) {
//            //get submitter groups: VacReqRequest, create
//            $groupParams = array();
//
//            $groupParams['permissions'][] = array('objectStr' => 'VacReqRequest', 'actionStr' => 'create');
//            $groupParams['permissions'][] = array('objectStr' => 'VacReqRequest', 'actionStr' => 'changestatus');
//            if ($this->isGranted('ROLE_VACREQ_ADMIN') == false) {
//                $groupParams['exceptPermissions'][] = array('objectStr' => 'VacReqRequest', 'actionStr' => 'changestatus-carryover');
//            }
//
//            //to get the select filter with all groups under the supervisor group, find the first upper supervisor of this group.
//            if ($this->isGranted('ROLE_VACREQ_SUPERVISOR')) {
//                $subjectUser = $user;
//            } else {
//                $groupParams['asSupervisor'] = true;
//                $subjectUser = $vacreqUtil->getClosestSupervisor($user);
//            }
//            //echo "subjectUser=".$subjectUser."<br>";
//            if (!$subjectUser) {
//                $subjectUser = $user;
//            }
//
//            $organizationalInstitutions = $vacreqUtil->getGroupsByPermission($subjectUser,$groupParams);
//        }
        ///// EOF NOT USED /////

        $organizationalInstitutions = $vacreqUtil->getAllGroupsByUser($user);
//        foreach($organizationalInstitutions as $id=>$organizationalInstitution) {
//            echo $id.": group=".$organizationalInstitution."<br>";
//        }

        //$params['organizationalInstitutions'] = $organizationalInstitutions;
        $params['organizationalInstitutions'] = $userServiceUtil->flipArrayLabelValue($organizationalInstitutions);   //flipped

        $groupId = $request->query->get('group');
        //echo "groupId=".$groupId."<br>";

        $params['groupId'] = $groupId;

        $filterform = $this->createForm(VacReqCalendarFilterType::class, null, array('form_custom_value'=>$params));


        return array(
            'vacreqfilter' => $filterform->createView(),
            'groupId' => $groupId
        );
    }


    /**
     * @Route("/vacreq-import-holiday-dates/", name="vacreq_import_holiday_dates", methods={"GET"}, options={"expose"=true})
     */
    public function importHolidayDatesAction(Request $request)
    {

        if ( false == $this->isGranted('ROLE_VACREQ_ADMIN') ) {
            return $this->redirect($this->generateUrl('vacreq-nopermission'));
        }

        $response = new Response();

        $holidayDatesUrl = $request->get('holidayDatesUrl');
        echo "holidayDatesUrl=$holidayDatesUrl <br>";

        if(0) {
            //https://www.phpclasses.org/browse/file/63450.html
            $ical = new iCalEasyReader();
            $lines = $ical->load(file_get_contents($holidayDatesUrl));
            //var_dump( $lines );
            dump($lines);
            exit();
        }

        if(0) {
            $ical = new iCalendar();
            $ical->parse($holidayDatesUrl);
            $ical_data = $ical->get_all_data();
            //var_dump( $lines );
            dump($ical_data);
            exit();
        }

        //https://www.apptha.com/blog/import-google-calendar-events-in-php/
        /* Getting events from isc file */
        $obj = new ics();
        $icsEvents = $obj->getIcsEventsAsArray( $holidayDatesUrl );
        //dump($icsEvents);

        $count = 0;

        foreach($icsEvents as $event) {
            //echo $event;
            if( isset($event['BEGIN']) ) {
                if( trim($event['BEGIN']) == 'VCALENDAR' ) {
                    continue;
                }
            } else {
                continue;
            }

            $valueBegin = trim($event['BEGIN']);
            //echo "valueBegin=[$valueBegin] <br>";
            if( $valueBegin != 'VEVENT' ) {
               continue;
            }

            $count++;

            $class = isset($event['CLASS']) ? trim($event['CLASS']) : NULL; //PUBLIC
            $summary = isset($event['SUMMARY']) ? trim($event['SUMMARY']) : NULL; //Thanksgiving Day
            $date = isset($event['DTSTART;VALUE=DATE']) ? trim($event['DTSTART;VALUE=DATE']) : NULL; //20221124

            echo $count . ": " . $date . ", " . $summary . "<br>";
        }

        exit("count=".$count);

        //parse the downloaded file and add the retrieved US holiday titles and dates
        // for the next 20 years from the downloaded file to the Platform List Manager
        // into a new Platform list manager list titled “Holidays”

        $response->setContent("OK");
        return $response;
    }

}
