<?php

/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 5/6/2016
 * Time: 4:25 PM
 */

namespace Oleg\VacReqBundle\EventListener;


use ADesigns\CalendarBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;
use Doctrine\ORM\EntityManager;



class CalendarEventListener
{

    protected $em;
    protected $sc;
    protected $container;

    public function __construct( $em, $sc, $container ) {
        $this->em = $em;
        $this->sc = $sc;
        $this->container = $container;
    }

    public function loadEvents(CalendarEvent $calendarEvent)
    {
        //$vacreqUtil = $this->container->get('vacreq_util');
        //$dateformat = 'M d Y';

        $startDate = $calendarEvent->getStartDatetime();
        $endDate = $calendarEvent->getEndDatetime();

        // The original request so you can get filters from the calendar
        // Use the filter in your query for example

        $request = $calendarEvent->getRequest();
        $groupId = $request->get('groupId');
        //echo "filter:".$filter.";";

        $filter = array('groupId'=>$groupId);

        $this->setCalendar( $calendarEvent, "requestBusiness", $startDate, $endDate, $filter );
        $this->setCalendar( $calendarEvent, "requestVacation", $startDate, $endDate, $filter );

        return;
    }

    public function setCalendar( $calendarEvent, $requestTypeStr, $startDate, $endDate, $filter ) {
        //echo "ID";
        $dateformat = 'M d Y';
        $vacreqUtil = $this->container->get('vacreq_util');

        $user = $this->sc->getToken()->getUser();
        //echo "user=".$user."<br>";

        //$requests = $vacreqUtil->getApprovedRequestStartedBetweenDates( $requestTypeStr, $startDate, $endDate );

        $groupId = $filter['groupId'];

        $repository = $this->em->getRepository('OlegVacReqBundle:VacReqRequest');
        $dql = $repository->createQueryBuilder('request');

        if( $requestTypeStr == 'business' || $requestTypeStr == 'requestBusiness' ) {
            $dql->leftJoin("request.requestBusiness", "requestType");
        }

        if( $requestTypeStr == 'vacation' || $requestTypeStr == 'requestVacation' ) {
            $dql->leftJoin("request.requestVacation", "requestType");
        }

        $dql->where("requestType.id IS NOT NULL");
        //$dql->andWhere('requestType.status = :statusApproved');
        $dql->andWhere('requestType.status = :statusApproved OR requestType.status = :statusPending');
        $dql->andWhere('(requestType.startDate BETWEEN :startDate and :endDate)');

        //$dql->andWhere('request.institution = :groupId');
        if( $groupId ) {
            $dql->leftJoin("request.institution","institution");
            $institution = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->find($groupId);
            $instStr = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->selectNodesUnderParentNode($institution,"institution",false);
            //echo "instStr=".$instStr."<br>";
            $dql->andWhere($instStr);
        }

        //select user, distinct start, end dates
        $dql->groupBy('request.user,requestType.startDate,requestType.endDate');

        $query = $this->em->createQuery($dql);

        $query->setParameter('statusPending', 'pending');
        $query->setParameter('statusApproved', 'approved');
        $query->setParameter('startDate', $startDate->format('Y-m-d H:i:s'));
        $query->setParameter('endDate', $endDate->format('Y-m-d H:i:s'));

        $requests = $query->getResult();

        if( $requestTypeStr == 'business' || $requestTypeStr == 'requestBusiness' ) {
            $backgroundColor = "#bce8f1";
            $requestName = "Business Travel";
        }

        if( $requestTypeStr == 'vacation' || $requestTypeStr == 'requestVacation' ) {
            $backgroundColor = "#b2dba1";
            $requestName = "Vacation";
        }

        $getMethod = "get".$requestTypeStr;

        // $companyEvents and $companyEvent in this example
        // represent entities from your database, NOT instances of EventEntity
        // within this bundle.
        //
        // Create EventEntity instances and populate it's properties with data
        // from your own entities/database values.

        foreach( $requests as $requestFull ) {

            $request = $requestFull->$getMethod();
            //echo "ID=".$request->getId();

            if( $this->container->get('security.context')->isGranted("read", $requestFull) ) {
                $url = $this->container->get('router')->generate(
                    'vacreq_show',
                    array(
                        'id' => $requestFull->getId()
                    )
                    //UrlGeneratorInterface::ABSOLUTE_URL
                );
            } else {
                $url = $this->container->get('router')->generate(
                    'vacreq_showuser',
                    array(
                        'id' => $requestFull->getUser()->getId()
                    )
                    //UrlGeneratorInterface::ABSOLUTE_URL
                );
            }

            //$userNameLink = '<a href="'.$url.'">'.$requestFull->getUser().'</a>';

            // create an event with a start/end time, or an all day event
            $title = "";
            //$title .= "(ID ".$requestFull->getId().") ";
            //$title .= "(EID ".$requestFull->getExportId().") ";
            $title .= $requestFull->getUser() . " " . $requestName;
            //$title .= $userNameLink . " " . $requestName;

            //$finalStartEndDates = $request->getFinalStartEndDates();
            $startDate = $request->getStartDate();
            $endDate = $request->getEndDate();
            $title .= " (" . $startDate->format($dateformat) . " - " . $endDate->format($dateformat);
            //$title .= ", back on ".$requestFull->getFirstDayBackInOffice()->format($dateformat).")";
            $title .= ")";

            if( $request->getStatus() == 'pending' ) {
                $backgroundColorCalendar = "#fcf8e3";
                $title = $title." Pending Approval";
            } else {
                $backgroundColorCalendar = $backgroundColor;
            }

            $eventEntity = new EventEntity($title, $startDate, $endDate, true);

            //optional calendar event settings
            $eventEntity->setAllDay(true); // default is false, set to true if this is an all day event
            $eventEntity->setBgColor($backgroundColorCalendar); //set the background color of the event's label
            $eventEntity->setFgColor('#2F4F4F'); //set the foreground color of the event's label
            $eventEntity->setUrl($url); // url to send user to when event label is clicked
            //$eventEntity->setCssClass('my-custom-class'); // a custom class you may want to apply to event labels

            //finally, add the event to the CalendarEvent for displaying on the calendar
            $calendarEvent->addEvent($eventEntity);

        }
    }



}

