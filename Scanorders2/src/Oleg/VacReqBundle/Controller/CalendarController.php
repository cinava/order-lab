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

namespace Oleg\VacReqBundle\Controller;


use Oleg\VacReqBundle\Form\VacReqCalendarFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use ADesigns\CalendarBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;

//vacreq site

class CalendarController extends Controller
{

    /**
     * show the names of people who are away that day (one name per "event"/line).
     *
     * @Route("/away-calendar/", name="vacreq_awaycalendar")
     * @Method({"GET"})
     * @Template("OlegVacReqBundle:Calendar:calendar.html.twig")
     */
    public function awayCalendarAction(Request $request) {

        if(
            false == $this->get('security.context')->isGranted('ROLE_VACREQ_OBSERVER') &&
            false == $this->get('security.context')->isGranted('ROLE_VACREQ_SUBMITTER') &&
            false == $this->get('security.context')->isGranted('ROLE_VACREQ_APPROVER') &&
            false == $this->get('security.context')->isGranted('ROLE_VACREQ_SUPERVISOR') &&
            false == $this->get('security.context')->isGranted('ROLE_VACREQ_OBSERVER')
        ) {
            return $this->redirect( $this->generateUrl('vacreq-nopermission') );
        }

        $vacreqUtil = $this->get('vacreq_util');
        $em = $this->getDoctrine()->getManager();

        $params = array();
        $params['em'] = $em;
        $params['supervisor'] = $this->get('security.context')->isGranted('ROLE_VACREQ_SUPERVISOR');

        //get submitter groups: VacReqRequest, create
        $groupParams = array();
        $groupParams['permissions'][] = array('objectStr'=>'VacReqRequest','actionStr'=>'changestatus');
        if( $this->get('security.context')->isGranted('ROLE_VACREQ_ADMIN') == false ) {
            $groupParams['exceptPermissions'][] = array('objectStr' => 'VacReqRequest', 'actionStr' => 'changestatus-carryover');
        }

        //to get the select filter with all groups under the supervisor group, find the first upper supervisor of this group.
        $user = $this->get('security.context')->getToken()->getUser();
        if( $this->get('security.context')->isGranted('ROLE_VACREQ_SUPERVISOR') ) {
            $subjectUser = $user;
        } else {
            $groupParams['asSupervisor'] = true;
            $subjectUser = $vacreqUtil->getClosestSupervisor( $user );
        }
        //echo "subjectUser=".$subjectUser."<br>";
        if( !$subjectUser ) {
            $subjectUser = $user;
        }
        $organizationalInstitutions = $vacreqUtil->getGroupsByPermission($subjectUser,$groupParams);

        $params['organizationalInstitutions'] = $organizationalInstitutions;

        $groupId = $request->query->get('group');
        //echo "groupId=".$groupId."<br>";

        $params['groupId'] = $groupId;

        $filterform = $this->createForm(new VacReqCalendarFilterType($params), null);


        return array(
            'vacreqfilter' => $filterform->createView(),
            'groupId' => $groupId
        );
    }

}
