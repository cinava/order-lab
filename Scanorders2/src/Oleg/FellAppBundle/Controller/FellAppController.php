<?php

namespace Oleg\FellAppBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Oleg\FellAppBundle\Entity\FellowshipApplication;
use Oleg\FellAppBundle\Entity\Interview;
use Oleg\FellAppBundle\Form\InterviewType;
use Oleg\UserdirectoryBundle\Entity\User;
use Oleg\OrderformBundle\Helper\ErrorHelper;
use Oleg\UserdirectoryBundle\Entity\AccessRequest;
use Oleg\UserdirectoryBundle\Entity\Reference;
use Oleg\FellAppBundle\Form\FellAppFilterType;
use Oleg\FellAppBundle\Form\FellowshipApplicationType;
use Oleg\UserdirectoryBundle\Util\UserUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;



class FellAppController extends Controller {

    /**
     * Show home page
     *
     * @Route("/", name="fellapp_home")
     * @Route("/my-interviewees/", name="fellapp_myinterviewees")
     *
     * @Template("OlegFellAppBundle:Default:home.html.twig")
     */
    public function indexAction(Request $request) {
        //echo "fellapp home <br>";

//        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_USER') ){
//            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
//        }
        if( false == $this->get('security.context')->isGranted("read","FellowshipApplication") ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();

        //echo "fellapp user ok <br>";

        $user = $this->get('security.context')->getToken()->getUser();
        $fellappUtil = $this->container->get('fellapp_util');

        $searchFlag = false;
        $currentYear = date("Y")+2;

        $fellowshipTypes = $fellappUtil->getFellowshipTypesByUser($user);
        //echo "fellowshipTypes count=".count($fellowshipTypes)."<br>";

        if( count($fellowshipTypes) == 0 ) {
            $this->get('session')->getFlashBag()->add(
                'warning',
                'No Fellowship Types (Subspecialties) are found for WCMC Pathology and Laboratory Medicine department.'
            );
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        //create fellapp filter
        $params = array(
            'fellTypes' => $fellowshipTypes,
        );
        $filterform = $this->createForm(new FellAppFilterType($params), null);

        $filterform->bind($request);  //use bind instead of handleRequest. handleRequest does not get filter data

        $filter = $filterform['filter']->getData();
        $search = $filterform['search']->getData();
        $startDate = $filterform['startDate']->getData();
        $hidden = $filterform['hidden']->getData();
        $archived = $filterform['archived']->getData();
        $complete = $filterform['complete']->getData();
        $interviewee = $filterform['interviewee']->getData();
        $active = $filterform['active']->getData();
        $reject = $filterform['reject']->getData();
        //$onhold = $filterform['onhold']->getData();
        $priority = $filterform['priority']->getData();
        //$page = $request->get('page');
        //echo "active=".$active."<br>";
        //echo "filter=".$filter."<br>";
        //echo "<br>search=".$search."<br>";
        //exit('1');

        $route = $request->get('_route');
        //echo "route".$route."<br>";
        //exit();

        $filterParams = $request->query->all();

        if( count($filterParams) == 0 ) {
            $fellowshipTypeId = null;
            if( count($fellowshipTypes) == 1 ) {
                $firstFellType = reset($fellowshipTypes);
                //echo "firstFellType id=".key($fellowshipTypes)."";
                //exit();
                $fellowshipTypeId = key($fellowshipTypes);
            }
            return $this->redirect( $this->generateUrl($route, //'fellapp_home',
                array(
                    'filter[startDate]' => $currentYear,
                    'filter[active]' => 1,
                    'filter[complete]' => 1,
                    'filter[interviewee]' => 1,
                    //'filter[onhold]' => 1,
                    'filter[priority]' => 1,
                    'filter[filter]' => $fellowshipTypeId,
                )
            ) );
        }

        //force check: check user role. Change filter according to the user roles
        if( $filter && $fellappUtil->hasSameFellowshipTypeId($user,$filter) == false ) {
            //exit('no permission');
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        //$fellApps = $em->getRepository('OlegUserdirectoryBundle:FellowshipApplication')->findAll();
        $repository = $this->getDoctrine()->getRepository('OlegFellAppBundle:FellowshipApplication');
        $dql =  $repository->createQueryBuilder("fellapp");
        $dql->select('fellapp');
        //$dql->groupBy('fellapp');
        $dql->orderBy("fellapp.id","DESC");
        $dql->leftJoin("fellapp.appStatus", "appStatus");
        $dql->leftJoin("fellapp.fellowshipSubspecialty", "fellowshipSubspecialty");
        $dql->leftJoin("fellapp.user", "applicant");
        $dql->leftJoin("applicant.infos", "applicantinfos");
        //$dql->leftJoin("applicant.credentials", "credentials");
        $dql->leftJoin("fellapp.examinations", "examinations");
        $dql->leftJoin("fellapp.trainings", "trainings");
        $dql->leftJoin("fellapp.rank", "rank");

        if( $search ) {
            $dql->leftJoin("applicant.infos", "userinfos");
            $dql->andWhere("userinfos.firstName LIKE '%".$search."%' OR userinfos.lastName LIKE '%".$search."%'");
            $searchFlag = true;
        }

        $fellSubspecId = null;
        if( $filter ) { //&& $filter != "ALL"
            $dql->andWhere("fellowshipSubspecialty.id = ".$filter);
            $searchFlag = true;
            $fellSubspecId = $filter;
        }

        //if( $filter == "ALL" ) {
        if( !$filter ) {
            $felltypeArr = array();
            foreach( $fellowshipTypes as $fellowshipTypeID => $fellowshipTypeName ) {
                //if( $fellowshipTypeID != "ALL" ) {
                    //echo "fellowshipType=".$fellowshipTypeID."<br>";
                    //$dql->orWhere("fellowshipSubspecialty.id = ".$fellowshipTypeID);
                $felltypeArr[] = "fellowshipSubspecialty.id = ".$fellowshipTypeID;
                //}
            }
            $dql->andWhere( implode(" OR ", $felltypeArr) );
            $searchFlag = true;
            //$fellSubspecId = $filter;
        }

        $orWhere = array();

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

        if( count($orWhere) > 0 ) {
            $orWhereStr = implode(" OR ",$orWhere);
            $dql->andWhere("(".$orWhereStr.")");
        }

        if( $startDate ) {
            //$transformer = new DateTimeToStringTransformer(null,null,'Y-m-d');
            //$dateStr = $transformer->transform($startDate);
            //$dql->andWhere("fellapp.startDate >= '".$startDate."'");
            //$dql->andWhere("year(fellapp.startDate) = '".$startDate->format('Y')."'");
            $startYearStr = $startDate->format('Y');
            $bottomDate = $startYearStr."-01-01";
            $topDate = $startYearStr."-12-31";
            $dql->andWhere("fellapp.startDate BETWEEN '" . $bottomDate . "'" . " AND " . "'" . $topDate . "'" );

            if( $startYearStr != $currentYear ) {
                $searchFlag = true;
            }
        } else {
            $startYearStr = $currentYear;
        }

        if( $route == "fellapp_myinterviewees" ) {
            $dql->leftJoin("fellapp.interviews", "interviews");
            $dql->andWhere("interviews.interviewer = " . $user->getId() );
        }

        //echo "dql=".$dql."<br>";

        $limit = 200;
        //$limit = 10; //testing
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $fellApps = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1), /*page number*/
            //$request->query->getInt('page', 1),
            $limit      /*limit per page*/
        );


        $em = $this->getDoctrine()->getManager();
        $eventtype = $em->getRepository('OlegUserdirectoryBundle:EventTypeList')->findOneByName("Import of Fellowship Applications Spreadsheet");
        $lastImportTimestamps = $this->getDoctrine()->getRepository('OlegUserdirectoryBundle:Logger')->findBy(array('eventType'=>$eventtype),array('creationdate'=>'DESC'),1);
        if( count($lastImportTimestamps) != 1 ) {
            $lastImportTimestamp = null;
        } else {
            $lastImportTimestamp = $lastImportTimestamps[0]->getCreationdate();
        }

        $accessreqs = $fellappUtil->getActiveAccessReq();

        //use date from the filter ($startYearStr) instead of $currentYear

        $complete = $fellappUtil->getFellAppByStatusAndYear('complete',$fellSubspecId,$startYearStr);
        $completeTotal = $fellappUtil->getFellAppByStatusAndYear('complete',$fellSubspecId);

        $hidden = $fellappUtil->getFellAppByStatusAndYear('hide',$fellSubspecId,$startYearStr);
        $hiddenTotal = $fellappUtil->getFellAppByStatusAndYear('hide',$fellSubspecId);

        $archived = $fellappUtil->getFellAppByStatusAndYear('archive',$fellSubspecId,$startYearStr);
        $archivedTotal = $fellappUtil->getFellAppByStatusAndYear('archive',$fellSubspecId);

        $active = $fellappUtil->getFellAppByStatusAndYear('active',$fellSubspecId,$startYearStr);
        $activeTotal = $fellappUtil->getFellAppByStatusAndYear('active',$fellSubspecId);

        $interviewee = $fellappUtil->getFellAppByStatusAndYear('interviewee',$fellSubspecId,$startYearStr);
        $intervieweeTotal = $fellappUtil->getFellAppByStatusAndYear('interviewee',$fellSubspecId);

        $reject = $fellappUtil->getFellAppByStatusAndYear('reject',$fellSubspecId,$startYearStr);
        $rejectTotal = $fellappUtil->getFellAppByStatusAndYear('reject',$fellSubspecId);

        //$onhold = $fellappUtil->getFellAppByStatusAndYear('onhold',$fellSubspecId,$startYearStr);
        //$onholdTotal = $fellappUtil->getFellAppByStatusAndYear('onhold',$fellSubspecId);

        $priority = $fellappUtil->getFellAppByStatusAndYear('priority',$fellSubspecId,$startYearStr);
        $priorityTotal = $fellappUtil->getFellAppByStatusAndYear('priority',$fellSubspecId);

        $idsArr = array();
        foreach( $fellApps as $fellApp ) {
            $idsArr[] = $fellApp->getId();
        }

        //Showing applications of your interviewees: 25 evaluations received, 10 awaited
        $awaitedInterviews = null;
        $receivedInterviews = null;
        if( $route == "fellapp_myinterviewees" ) {

            if( $fellSubspecId ) {
                $fellSubspecArg = $fellSubspecId;
            } else {
                $fellSubspecArg = $fellowshipTypes;
            }

            $awaitedInterviews = count($fellappUtil->getFellAppByStatusAndYear('interviewee-not',$fellSubspecArg,$startYearStr,$user));
            $receivedInterviews = count($fellappUtil->getFellAppByStatusAndYear('interviewee',$fellSubspecArg,$startYearStr,$user));
            //echo "awaitedInterviews=".$awaitedInterviews."<br>";
            //echo "receivedInterviews=".$receivedInterviews."<br>";
        }

        //allowPopulateFellApp
        $userUtil = new UserUtil();
        $allowPopulateFellApp = $userUtil->getSiteSetting($em,'AllowPopulateFellApp');
        
        return array(
            'entities' => $fellApps,
            'pathbase' => 'fellapp',
            'lastImportTimestamp' => $lastImportTimestamp,
            'allowPopulateFellApp' => $allowPopulateFellApp,
            'fellappfilter' => $filterform->createView(),
            'startDate' => $startDate,
            'filter' => $fellSubspecId,
            'accessreqs' => count($accessreqs),
            'currentYear' => $startYearStr, //$currentYear,
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
            'awaitedInterviews' => $awaitedInterviews,
            'receivedInterviews' => $receivedInterviews,
            'searchFlag' => $searchFlag,
            'serverTimeZone' => "", //date_default_timezone_get(),
            'fellappids' => implode("-",$idsArr),
            'route_path' => $route
        );
    }

//    //check for active access requests
//    public function getActiveAccessReq() {
//        if( !$this->get('security.context')->isGranted('ROLE_FELLAPP_ADMIN') ) {
//            return null;
//        }
//        $userSecUtil = $this->get('user_security_utility');
//        $accessreqs = $userSecUtil->getUserAccessRequestsByStatus($this->container->getParameter('fellapp.sitename'),AccessRequest::STATUS_ACTIVE);
//        return $accessreqs;
//    }





    /**
     * @Route("/show/{id}", name="fellapp_show")
     * @Route("/edit/{id}", name="fellapp_edit")
     * @Route("/edit-with-default-interviewers/{id}", name="fellapp_edit_default_interviewers")
     * @Route("/download/{id}", name="fellapp_download")
     *
     * @Template("OlegFellAppBundle:Form:new.html.twig")
     */
    public function showAction(Request $request, $id) {

        //echo "clientip=".$request->getClientIp()."<br>";
        //$ip = $this->container->get('request')->getClientIp();
        //echo "ip=".$ip."<br>";

//        if( false == $this->get('security.context')->isGranted("read","FellowshipApplication") ){
//            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
//        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $logger = $this->container->get('logger');
        $routeName = $request->get('_route');
        $userSecUtil = $this->container->get('user_security_utility');

        $actionStr = "viewed";
        $eventType = 'Fellowship Application Page Viewed';

        //admin can edit
        if( $routeName == "fellapp_edit" ) {
            $actionStr = "viewed on edit page";
            $eventType = 'Fellowship Application Page Viewed';
        }

        //download: user or localhost
        if( $routeName == 'fellapp_download' ) {
            //$user = $this->get('security.context')->getToken()->getUser();
            //download link can be accessed by a console as localhost with role IS_AUTHENTICATED_ANONYMOUSLY, so simulate login manually           
            if( !($user instanceof User) ) {
                $firewall = 'ldap_fellapp_firewall';               
                $systemUser = $userSecUtil->findSystemUser();
                if( $systemUser ) {
                    $token = new UsernamePasswordToken($systemUser, null, $firewall, $systemUser->getRoles());
                    $this->get('security.context')->setToken($token);
                    //$this->get('security.token_storage')->setToken($token);
                }
                $logger->notice("Download view: Logged in as systemUser=".$systemUser);
            } else {
                $logger->notice("Download view: Token user is valid security.context user=".$user);
            }
        }

        
        //echo "fellapp download!!!!!!!!!!!!!!! <br>";       

        $entity = $em->getRepository('OlegFellAppBundle:FellowshipApplication')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Fellowship Application by id='.$id);
        }

        //user who has the same fell type can view or edit
        //can use hasFellappPermission or isGranted("read",$entity). isGranted("read",$entity) fellapp voter contains hasFellappPermission
        //$fellappUtil = $this->container->get('fellapp_util');
        //if( $fellappUtil->hasFellappPermission($user,$entity) == false ) {
        if( false == $this->get('security.context')->isGranted("read",$entity) ) {
            //exit('fellapp permission not ok ID:'.$entity->getId());
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }
        //exit('fellapp permission ok ID:'.$entity->getId());

        if( $routeName == "fellapp_edit" ) {
            if( false == $this->get('security.context')->isGranted("update",$entity) ) {
                return $this->redirect( $this->generateUrl('fellapp-nopermission') );
            }
        } else {
            if( false == $this->get('security.context')->isGranted("read",$entity) ) {
                return $this->redirect( $this->generateUrl('fellapp-nopermission') );
            }
        }

        $args = $this->getShowParameters($routeName,$id);

        if( $routeName == 'fellapp_download' ) {
            return $this->render('OlegFellAppBundle:Form:download.html.twig', $args);
        }


        //event log
        //$logger = $this->container->get('logger');
        //$logger->notice("view: timezone=".date_default_timezone_get());
        $user = $em->getRepository('OlegUserdirectoryBundle:User')->find($user->getId()); //fetch user from DB otherwise keytype is null
        $event = "Fellowship Application with ID".$id." has been ".$actionStr." by ".$user;

        $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$user,$entity,$request,$eventType);
        
        return $this->render('OlegFellAppBundle:Form:new.html.twig', $args);
    }

    /**
     * @Route("/new/", name="fellapp_new")
     *
     * @Template("OlegFellAppBundle:Form:new.html.twig")
     */
    public function newAction(Request $request) {

        //coordinator and director can create
//        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_COORDINATOR') && false == $this->get('security.context')->isGranted('ROLE_FELLAPP_DIRECTOR') ){
//            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
//        }
        if( false == $this->get('security.context')->isGranted("create","FellowshipApplication") ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        $user = $this->get('security.context')->getToken()->getUser();

        //$user = new User();
        $addobjects = true;
        $applicant = new User($addobjects);
        $applicant->setPassword("");
        $applicant->setCreatedby('manual');

        $fellowshipApplication = new FellowshipApplication($user);
        $fellowshipApplication->setTimestamp(new \DateTime());

        $applicant->addFellowshipApplication($fellowshipApplication);

        $routeName = $request->get('_route');
        $args = $this->getShowParameters($routeName,null,$fellowshipApplication);

        return $this->render('OlegFellAppBundle:Form:new.html.twig', $args);
    }


    public function getShowParameters($routeName, $id=null, $entity=null) {
             
        $user = $this->get('security.context')->getToken()->getUser(); 

//        echo "user=".$user."<br>";
//        if( !($user instanceof User) ) {
//            echo "no user object <br>";
//            $userSecUtil = $this->container->get('user_security_utility');
//            $user = $userSecUtil->findSystemUser();
//        }               
        
        $em = $this->getDoctrine()->getManager();

        if( $id ) {
            //$fellApps = $em->getRepository('OlegFellAppBundle:FellowshipApplication')->findAll();
            $entity = $this->getDoctrine()->getRepository('OlegFellAppBundle:FellowshipApplication')->find($id);

            if( !$entity ) {
                throw $this->createNotFoundException('Unable to find Fellowship Application by id='.$id);
            }
        } else {
            if( !$entity ) {
                throw $this->createNotFoundException('Fellowship Application entity was not provided: id='.$id.", entity=".$entity);
            }
        }

        //add empty fields if they are not exist
        $fellappUtil = $this->container->get('fellapp_util');
        $fellappUtil->addEmptyFellAppFields($entity);

        if( $routeName == "fellapp_show" ) {
            $cycle = 'show';
            $disabled = true;
            $method = "GET";
            $action = $this->generateUrl('fellapp_edit', array('id' => $entity->getId()));
        }

        if( $routeName == "fellapp_new" ) {
            $cycle = 'new';
            $disabled = false;
            $method = "POST";
            $action = $this->generateUrl('fellapp_create_applicant');
        }

        if( $routeName == "fellapp_edit" ) {
            $cycle = 'edit';
            $disabled = false;
            $method = "PUT";
            $action = $this->generateUrl('fellapp_update', array('id' => $entity->getId()));
        }

        if( $routeName == "fellapp_edit_default_interviewers" ) {
            $cycle = 'edit';
            $disabled = false;
            $method = "PUT";
            $action = $this->generateUrl('fellapp_update', array('id' => $entity->getId()));
            $fellappUtil->addDefaultInterviewers($entity);
        }

        if( $routeName == "fellapp_download" ) {
            $cycle = 'download';
            $disabled = true;
            $method = "GET";
            $action = null; //$this->generateUrl('fellapp_update', array('id' => $entity->getId()));
        }


        $params = array(
            'cycle' => $cycle,
            'sc' => $this->get('security.context'),
            'em' => $em,
            'user' => $entity->getUser(),
            'cloneuser' => null,
            'roles' => $user->getRoles()
        );

        $form = $this->createForm(
            new FellowshipApplicationType($params),
            $entity,
            array(
                'disabled' => $disabled,
                'method' => $method,
                'action' => $action
            )
        );


        //clear em, because createUserEditEvent will flush em
        $em = $this->getDoctrine()->getManager();
        $em->clear();

        return array(
            'form' => $form->createView(),
            'entity' => $entity,
            'pathbase' => 'fellapp',
            'cycle' => $cycle,
            'sitename' => $this->container->getParameter('fellapp.sitename')
        );
    }


    /**
     * @Route("/update/{id}", name="fellapp_update")
     * @Method("PUT")
     * @Template("OlegFellAppBundle:Form:new.html.twig")
     */
    public function updateAction(Request $request, $id) {

//        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_COORDINATOR') && false == $this->get('security.context')->isGranted('ROLE_FELLAPP_DIRECTOR') ){
//            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
//        }
//        if( false == $this->get('security.context')->isGranted("update","FellowshipApplication") ){
//            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
//        }

        //echo "update <br>";
        //exit('update');

        $user = $this->get('security.context')->getToken()->getUser();

        $entity = $this->getDoctrine()->getRepository('OlegFellAppBundle:FellowshipApplication')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Fellowship Application by id='.$id);
        }

        //user who has the same fell type can view or edit
        $fellappUtil = $this->container->get('fellapp_util');
        if( $fellappUtil->hasFellappPermission($user,$entity) == false ) {
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        if( false == $this->get('security.context')->isGranted("update",$entity) ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        // Create an ArrayCollection of the current interviews
        $originalInterviews = new ArrayCollection();
        foreach( $entity->getInterviews() as $interview) {
            $originalInterviews->add($interview);
        }

        $originalReports = new ArrayCollection();
        foreach( $entity->getReports() as $report ) {
            $originalReports->add($report);
        }

        $cycle = 'edit';
        $user = $this->get('security.context')->getToken()->getUser();

        $params = array(
            'cycle' => $cycle,
            'sc' => $this->get('security.context'),
            'em' => $this->getDoctrine()->getManager(),
            'user' => $entity->getUser(),
            'cloneuser' => null,
            'roles' => $user->getRoles()
        );

        $form = $this->createForm( new FellowshipApplicationType($params), $entity );

        $form->handleRequest($request);

        if( !$form->isSubmitted() ) {
            //echo "form is not submitted<br>";
            $form->submit($request);
        }


//        if ($form->isDisabled()) {
//            echo "form is disabled<br>";
//        }
//        if (count($form->getErrors(true)) > 0) {
//            echo "form has errors<br>";
//        }
//        echo "errors:<br>";
//        $string = (string) $form->getErrors(true);
//        echo "string errors=".$string."<br>";
//        echo "getErrors count=".count($form->getErrors())."<br>";
//        echo "getErrorsAsString()=".$form->getErrorsAsString()."<br>";
//        print_r($form->getErrors());
//        echo "<br>string errors:<br>";
//        print_r($form->getErrorsAsString());
//        echo "<br>";
//        exit();

        if(0) {
            $errorHelper = new ErrorHelper();
            $errors = $errorHelper->getErrorMessages($form);
            echo "<br>form errors:<br>";
            print_r($errors);
        }

        if( $form->isValid() ) {

            //exit('form valid');

            /////////////// Process Removed Collections ///////////////
            $removedCollections = array();

            $removedInfo = $this->removeCollection($originalInterviews,$entity->getInterviews(),$entity);
            if( $removedInfo ) {
                $removedCollections[] = $removedInfo;
            }
            /////////////// EOF Process Removed Collections ///////////////

            $this->calculateScore($entity);

            $this->processDocuments($entity);

            $this->assignFellAppAccessRoles($entity);

            //set update author application
            $em = $this->getDoctrine()->getManager();
            $userUtil = new UserUtil();
            $sc = $this->get('security.context');
            $userUtil->setUpdateInfo($entity,$em,$sc);


            /////////////// Add event log on edit (edit or add collection) ///////////////
            /////////////// Must run before flash DB. When DB is flashed getEntityChangeSet() will not work ///////////////
            $changedInfoArr = $this->setEventLogChanges($entity);

            //report (Complete Application PDF) diff
            $reportsDiffInfoStr = $this->recordToEvenLogDiffCollection($originalReports,$entity->getReports(),"Report");
            //echo "reportsDiffInfoStr=".$reportsDiffInfoStr."<br>";
            //exit('report');

            //set Edit event log for removed collection and changed fields or added collection
            if( count($changedInfoArr) > 0 || count($removedCollections) > 0 || $reportsDiffInfoStr ) {
                $event = "Fellowship Application ".$entity->getId()." information has been changed by ".$user.":"."<br>";
                $event = $event . implode("<br>", $changedInfoArr);
                $event = $event . "<br>" . implode("<br>", $removedCollections);
                $event = $event . $reportsDiffInfoStr;
                //echo "Diff event=".$event."<br>";
                $userSecUtil = $this->get('user_security_utility');
                $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$user,$entity,$request,'Fellowship Application Updated');
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            //don't regenerate report if it was added.
            //Regenerate if: report does not exists (reports count == 0) or if original reports are the same as current reports
            //echo "report count=".count($entity->getReports())."<br>";
            //echo "reportsDiffInfoStr=".$reportsDiffInfoStr."<br>";
            if( count($entity->getReports()) == 0 || $reportsDiffInfoStr == "" ) {
                $fellappRepGen = $this->container->get('fellapp_reportgenerator');
                $fellappRepGen->addFellAppReportToQueue( $id, 'overwrite' );
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'A new Complete Fellowship Application PDF will be generated.'
                );
                //echo "Regenerate!!!! <br>";
            } else {
                //echo "NO Regenerate!!!! <br>";
            }
            //exit('report regen');

            //set logger for update
            //$logger = $this->container->get('logger');
            //$logger->notice("update: timezone=".date_default_timezone_get());
            $userSecUtil = $this->container->get('user_security_utility');
            $user = $em->getRepository('OlegUserdirectoryBundle:User')->find($user->getId()); //fetch user from DB otherwise keytype is null
            $event = "Fellowship Application with ID " . $id . " has been updated by " . $user;
            $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$user,$entity,$request,'Fellowship Application Updated');
            //exit('event='.$event);

            return $this->redirect($this->generateUrl('fellapp_show',array('id' => $entity->getId())));
        }

        //echo 'form invalid <br>';
        //exit('form invalid');

        return array(
            'form' => $form->createView(),
            'entity' => $entity,
            'pathbase' => 'fellapp',
            'cycle' => $cycle,
            'sitename' => $this->container->getParameter('fellapp.sitename')
        );
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
     * @Route("/applicant/new", name="fellapp_create_applicant")
     * @Method("POST")
     * @Template("OlegFellAppBundle:Form:new.html.twig")
     */
    public function createApplicantAction( Request $request )
    {

        if( false == $this->get('security.context')->isGranted("create","FellowshipApplication") ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $fellowshipApplication = new FellowshipApplication($user);

        $activeStatus = $em->getRepository('OlegFellAppBundle:FellAppStatus')->findOneByName("active");
        if( !$activeStatus ) {
            throw new EntityNotFoundException('Unable to find FellAppStatus by name='."active");
        }
        $fellowshipApplication->setAppStatus($activeStatus);

        if( !$fellowshipApplication->getUser() ) {
            //new applicant
            $addobjects = false;
            $applicant = new User($addobjects);
            $applicant->setPassword("");
            $applicant->setCreatedby('manual');
            $applicant->addFellowshipApplication($fellowshipApplication);
        }

        //add empty fields if they are not exist
        $fellappUtil = $this->container->get('fellapp_util');
        $fellappUtil->addEmptyFellAppFields($fellowshipApplication);

        $params = array(
            'cycle' => 'new',
            'sc' => $this->get('security.context'),
            'em' => $this->getDoctrine()->getManager(),
            'user' => $fellowshipApplication->getUser(),
            'cloneuser' => null,
            'roles' => $user->getRoles()
        );
        $form = $this->createForm( new FellowshipApplicationType($params), $fellowshipApplication );

        $form->handleRequest($request);

        if( !$form->isSubmitted() ) {
            //echo "form is not submitted<br>";
            $form->submit($request);
        }

        $applicant = $fellowshipApplication->getUser();

        if( !$fellowshipApplication->getFellowshipSubspecialty() ) {
            $form['fellowshipSubspecialty']->addError(new FormError('Please select in the Fellowship Type before uploading'));
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

        if( $form->isValid() ) {

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

            $default_time_zone = $this->container->getParameter('default_time_zone');
            $applicant->getPreferences()->setTimezone($default_time_zone);
            $applicant->setLocked(true);

            //exit('form valid');

            $this->calculateScore($fellowshipApplication);

            $this->processDocuments($fellowshipApplication);

            $this->assignFellAppAccessRoles($fellowshipApplication);


            //set update author application
//            $em = $this->getDoctrine()->getManager();
//            $userUtil = new UserUtil();
//            $sc = $this->get('security.context');
//            $userUtil->setUpdateInfo($fellowshipApplication,$em,$sc);

            //exit('eof new applicant');

            $em = $this->getDoctrine()->getManager();
            $em->persist($fellowshipApplication);
            $em->persist($applicant);
            $em->flush();

            //update report if report does not exists
            //if( count($entity->getReports()) == 0 ) {
            $fellappRepGen = $this->container->get('fellapp_reportgenerator');
            $fellappRepGen->addFellAppReportToQueue( $fellowshipApplication->getId(), 'overwrite' );
            $this->get('session')->getFlashBag()->add(
                'notice',
                'A new Complete Fellowship Application PDF will be generated.'
            );
            //}

            //set logger for update
            $userSecUtil = $this->container->get('user_security_utility');
            $event = "Fellowship Application with ID " . $fellowshipApplication->getId() . " has been created by " . $user;
            $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$user,$fellowshipApplication,$request,'Fellowship Application Updated');


            return $this->redirect($this->generateUrl('fellapp_show',array('id' => $fellowshipApplication->getId())));
        }

        //echo 'form invalid <br>';
        //exit('form invalid');

        return array(
            'form' => $form->createView(),
            'entity' => $fellowshipApplication,
            'pathbase' => 'fellapp',
            'cycle' => 'new',
            'sitename' => $this->container->getParameter('fellapp.sitename')
        );

    }


    //assign ROLE_FELLAPP_INTERVIEWER corresponding to application
    public function assignFellAppAccessRoles($application) {

        $em = $this->getDoctrine()->getManager();

        $fellowshipSubspecialty = $application->getFellowshipSubspecialty();

        //////////////////////// INTERVIEWER ///////////////////////////
        $interviewerRoleFellType = null;
        $interviewerFellTypeRoles = $em->getRepository('OlegUserdirectoryBundle:Roles')->findByFellowshipSubspecialty($fellowshipSubspecialty);
        foreach( $interviewerFellTypeRoles as $role ) {
            if( strpos($role,'INTERVIEWER') !== false ) {
                $interviewerRoleFellType = $role;
                break;
            }
        }
        if( !$interviewerRoleFellType ) {
            throw new EntityNotFoundException('Unable to find role by FellowshipSubspecialty='.$fellowshipSubspecialty);
        }

        foreach( $application->getInterviews() as $interview ) {
            $interviewer = $interview->getInterviewer();
            if( $interviewer ) {

                //add general interviewer role                
                //$interviewer->addRole('ROLE_FELLAPP_USER');
                //$interviewer->addRole('ROLE_FELLAPP_INTERVIEWER');

                //add specific interviewer role
                $interviewer->addRole($interviewerRoleFellType->getName());

            }
        }
        //////////////////////// EOF INTERVIEWER ///////////////////////////


        //////////////////////// OBSERVER ///////////////////////////
        foreach( $application->getObservers() as $observer ) {
            if( $observer ) {
                //add general observer role
                //$observer->addRole('ROLE_FELLAPP_USER');
                $observer->addRole('ROLE_FELLAPP_OBSERVER');
            }
        }
        //////////////////////// EOF OBSERVER ///////////////////////////

    }


    //process upload documents: CurriculumVitae(documents), FellowshipApplication(coverLetters), Examination(scores), FellowshipApplication(lawsuitDocuments), FellowshipApplication(reprimandDocuments)
    public function processDocuments($application) {

        $em = $this->getDoctrine()->getManager();

        //Avatar
        $em->getRepository('OlegUserdirectoryBundle:Document')->processDocuments( $application, 'avatar' );

        //CurriculumVitae
        $em->getRepository('OlegUserdirectoryBundle:Document')->processDocuments( $application, 'cv' );

        //FellowshipApplication(coverLetters)
        $em->getRepository('OlegUserdirectoryBundle:Document')->processDocuments( $application, 'coverLetter' );
        $em->getRepository('OlegUserdirectoryBundle:Document')->processDocuments( $application, 'lawsuitDocument');
        $em->getRepository('OlegUserdirectoryBundle:Document')->processDocuments( $application, 'reprimandDocument' );

        //Examination
        foreach( $application->getExaminations() as $examination ) {
            $em->getRepository('OlegUserdirectoryBundle:Document')->processDocuments( $examination );
        }

        //Reference .documents
        foreach( $application->getReferences() as $reference ) {
            $em->getRepository('OlegUserdirectoryBundle:Document')->processDocuments( $reference );
        }

        //Other .documents
        $em->getRepository('OlegUserdirectoryBundle:Document')->processDocuments( $application );

        //.itinerarys
        $em->getRepository('OlegUserdirectoryBundle:Document')->processDocuments( $application, 'itinerary' );

    }


    /**
     * @Route("/change-status/{id}/{status}", name="fellapp_status")
     * @Route("/status/{id}/{status}", name="fellapp_status_email")
     * @Method("GET")
     */
    public function statusAction( Request $request, $id, $status ) {

        //$logger = $this->container->get('logger');
        //$logger->notice('statusAction: status='.$status);

        $entity = $this->getDoctrine()->getRepository('OlegFellAppBundle:FellowshipApplication')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Fellowship Application by id='.$id);
        }

        if( false == $this->get('security.context')->isGranted("update","FellowshipApplication") ) {
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        $event = $this->changeFellAppStatus($entity, $status, $request);

        $this->get('session')->getFlashBag()->add(
            'notice',
            $event
        );

        if( $request->get('_route') == 'fellapp_status_email' ) {
            return $this->redirect( $this->generateUrl('fellapp_show',array('id' => $id)) );
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode("ok"));
        return $response;
    }
    
    public function changeFellAppStatus($fellapp, $status, $request) {
        
        $logger = $this->container->get('logger');
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        //get status object
        $statusObj = $em->getRepository('OlegFellAppBundle:FellAppStatus')->findOneByName($status);
        if( !$statusObj ) {
            $logger->error('statusAction: Unable to find FellAppStatus by name='.$status);
            throw new EntityNotFoundException('Unable to find FellAppStatus by name='.$status);           
        }

        //change status
        $fellapp->setAppStatus($statusObj);

        $em->persist($fellapp);
        $em->flush();

        //Every time an application is marked as "Priority", send an email to the user(s) with the corresponding "Fellowship Prpgram Coordinator" role (Cytopathology, etc), - in our case it will be Jessica - saying:
        if( $status == 'priority' ) {
            $break = "\r\n";
            $fellappUtil = $this->container->get('fellapp_util');
            $directorEmails = $fellappUtil->getDirectorsOfFellAppEmails($fellapp);
            $coordinatorEmails = $fellappUtil->getCoordinatorsOfFellAppEmails($fellapp);
            $responsibleEmails = array_unique (array_merge ($coordinatorEmails, $directorEmails));
            $logger->notice("Fellowship application ".$fellapp->getId()." status has been marked as Priority to the directors and coordinators emails " . implode(", ",$responsibleEmails));

            //Subject: FirstName LastName has marked FirstName LastName's FellowshipType fellowship application (ID:id#) as "Priority"
            $emailSubject = $user." has marked ".$fellapp->getUser()->getUsernameShortest()."'s ".$fellapp->getFellowshipSubspecialty().
                " fellowship application (ID:".$fellapp->getId().") as 'Priority'";

            //Body: FirstName LastName (WCMC CWID: xxx1234) has marked FirstName LastName's FellowshipType
            // fellowship application (ID:id#) as "Priority" on MM/DD/YYY at HH:MM.
            //Link to the application:
            //Clickable Link leading to the application web page
            //Download the Application PDF:
            //Clickable link to the PDF of the entire application
            $applicationLink = $this->container->get('router')->generate(
                'fellapp_show',
                array(
                    'id' => $fellapp->getId(),
                ),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $linkToGeneratedApplicantPDF = $this->container->get('router')->generate(
                'fellapp_view_pdf',
                array(
                    'id' => $fellapp->getId()
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

        $eventType = 'Fellowship Application Status changed to ' . $statusObj->getAction();

        $userSecUtil = $this->container->get('user_security_utility');
        $event = $eventType . '; application ID ' . $fellapp->getID() . ' by user ' . $user;
        $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$user,$fellapp,$request,$eventType);
        
        return $event;
    }


//    /**
//     * @Route("/status-sync/", name="fellapp_sincstatus")
//     * @Method("GET")
//     */
//    public function syncStatusAction( Request $request ) {
//
//        $em = $this->getDoctrine()->getManager();
//        $applications = $this->getDoctrine()->getRepository('OlegFellAppBundle:FellowshipApplication')->findAll();
//
//        foreach( $applications as $application ) {
//            $status = $application->getApplicationStatus();
//            $statusObj = $em->getRepository('OlegFellAppBundle:FellAppStatus')->findOneByName($status);
//            if( !$statusObj ) {
//                throw new EntityNotFoundException('Unable to find FellAppStatus by name='.$status);
//            }
//            $application->setAppStatus($statusObj);
//            //$application->setApplicationStatus(NULL);
//        }
//
//        $em->flush();
//
//        return $this->redirect( $this->generateUrl('fellapp_home') );
//    }


    /**
     * @Route("/interview-evaluation/show/{id}", name="fellapp_interview_show")
     * @Route("/interview-evaluation/{id}", name="fellapp_interview_edit")
     * @Method("GET")
     * @Template("OlegFellAppBundle:Interview:new.html.twig")
     */
    public function interviewAction( Request $request, $id ) {

        //echo "status <br>";

//        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_INTERVIEWER') ){
//            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
//        }
        if( false == $this->get('security.context')->isGranted("create","Interview") ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $routeName = $request->get('_route');

        $interview = $em->getRepository('OlegFellAppBundle:Interview')->find($id);

        if( !$interview ) {
            throw $this->createNotFoundException('Unable to find Fellowship Application Interview by id='.$id);
        }

        $user = $this->get('security.context')->getToken()->getUser();

        //check if the interviewer is the same as current user
        $interviewer = $interview->getInterviewer();
        //echo "interviewer=".$interviewer."<br>";
        $interviewId = null;
        if( $interviewer ) {
            $interviewId = $interviewer->getId();
        } else {
            throw $this->createNotFoundException('Interviewer is undefined');
        }
        //echo $user->getId()."?=".$interviewId."<br>";
        if( $user->getId() != $interviewId ) {
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        if( $routeName == "fellapp_interview_edit" && $interview->getTotalRank() && $interview->getTotalRank() > 0 ) {
            return $this->redirect( $this->generateUrl('fellapp_interview_show',array('id' => $interview->getId())) );
        }

        if( $routeName == "fellapp_interview_show" ) {
            $cycle = "show";
            $method = "GET";
            $action = null;
            $disabled = true;
        }

        if( $routeName == "fellapp_interview_edit" ) {
            $cycle = "edit";
            $method = "POST";
            $action = $this->generateUrl('fellapp_interview_update', array('id' => $interview->getId()));
            $disabled = false;
        }

        $params = array(
            'cycle' => $cycle,
            'sc' => $this->get('security.context'),
            'em' => $em,
            'interviewer' => $interview->getInterviewer(),
            'showFull' => false
        );

        $form = $this->createForm(
            new InterviewType($params),
            $interview,
            array(
                'disabled' => $disabled,
                'method' => $method,
                'action' => $action
            )
        );

        return array(
            'form' => $form->createView(),
            'entity' => $interview,
            'pathbase' => 'fellapp',
            'cycle' => $cycle,
            'sitename' => $this->container->getParameter('fellapp.sitename')
        );

    }

    /**
     * @Route("/interview/update/{id}", name="fellapp_interview_update")
     * @Method("POST")
     * @Template("OlegFellAppBundle:Interview:new.html.twig")
     */
    public function interviewUpdateAction( Request $request, $id ) {

        //echo "status <br>";

//        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_INTERVIEWER') ){
//            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
//        }
        if( false == $this->get('security.context')->isGranted("create","Interview") ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();

        $interview = $em->getRepository('OlegFellAppBundle:Interview')->find($id);

        if( !$interview ) {
            throw $this->createNotFoundException('Unable to find Fellowship Application Interview by id='.$id);
        }

        //check if the interviewer is the same as current user
        $user = $this->get('security.context')->getToken()->getUser();
        if( $user->getId() != $interview->getInterviewer()->getId() ) {
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        $cycle = 'edit';
        $method = "POST";
        $action = $this->generateUrl('fellapp_interview_update', array('id' => $interview->getId()));
        $disabled = false;

        $params = array(
            'cycle' => $cycle,
            'sc' => $this->get('security.context'),
            'em' => $em,
            'interviewer' => $interview->getInterviewer(),
            'showFull' => false
        );
        $form = $this->createForm(
            new InterviewType($params),
            $interview,
            array(
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

            $fellapp = $interview->getFellapp();

            $this->calculateScore($fellapp);
            
            //Upon submitting the first interview evaluation form for a given application, 
            //if the current application status is not "Interviewee", automatically switch it to "Interviewee".
            if( $fellapp->getAppStatus()->getName()."" != "interviewee" ) {
                $this->changeFellAppStatus($fellapp, "interviewee", $request);
            }
            
            $em->persist($interview);
            $em->flush();


            $applicant = $fellapp->getUser();
            $eventType = 'Fellowship Interview Evaluation Updated';
            $userSecUtil = $this->container->get('user_security_utility');
            $user = $this->get('security.context')->getToken()->getUser();
            //$event = $eventType . '; application ID ' . $fellapp->getId();
            $event = 'Fellowship Interview Evaluation for applicant '.$applicant->getUsernameOptimal().' (ID: '.$fellapp->getId().') has been submitted by ' . $user->getUsernameOptimal();
            $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$user,$fellapp,$request,$eventType);

            //return $this->redirect( $this->generateUrl('fellapp_home'));

            $this->get('session')->getFlashBag()->add(
                'notice',
                $event
            );

            return $this->redirect( $this->generateUrl('fellapp_interview_show',array('id' => $interview->getId())) );
        }


        return array(
            'form' => $form->createView(),
            'entity' => $interview,
            'pathbase' => 'fellapp',
            'cycle' => $cycle,
            'sitename' => $this->container->getParameter('fellapp.sitename')
        );

    }


//    /**
//     * @Route("/interview/new/{fellappid}/{interviewid}", name="fellapp_interview_new")
//     * @Route("/interview/new/{fellappid}/{interviewid}", name="fellapp_interview_new")
//     * @Method("GET")
//     * @Template("OlegFellAppBundle:Interview:new.html.twig")
//     */
//    public function createInterviewAction( Request $request ) {
//
//        //echo "status <br>";
//
//        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_INTERVIEWER') ){
//            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
//        }
//
//        $em = $this->getDoctrine()->getManager();
//
//        $interview = $this->getDoctrine()->getRepository('OlegFellAppBundle:Interview')->find($id);
//
//        if( !$interview ) {
//            throw $this->createNotFoundException('Unable to find Fellowship Application Interview by id='.$id);
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
//            'pathbase' => 'fellapp',
//            'cycle' => $cycle,
//            'sitename' => $this->container->getParameter('fellapp.sitename')
//        );
//
//    }







    /**
     * @Route("/remove/{id}", name="fellapp_remove")
     */
    public function removeAction($id) {

        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_ADMIN') ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        //echo "remove <br>";
        exit('remove not supported');

        return $this->redirect( $this->generateUrl('fellapp_home') );
    }




    /**
     * Manually import and populate applicants from Google
     *
     * @Route("/populate-import", name="fellapp_import_populate")
     */
    public function importAndPopulateAction(Request $request) {

        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_ADMIN') ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        $fellappImportPopulateUtil = $this->container->get('fellapp_importpopulate_util');

        $result = $fellappImportPopulateUtil->processFellAppFromGoogleDrive();

        $this->get('session')->getFlashBag()->add(
            'notice',
            $result
        );

        return $this->redirect( $this->generateUrl('fellapp_home') );

//        //1) import
//        $fileDb = $fellappUtil->importFellApp();
//
//        if( $fileDb ) {
//            $event = "Fellowship Application Spreadsheet file has been successful downloaded to the server with id=" . $fileDb->getId().", title=".$fileDb->getUniquename();
//            $flashType = 'notice';
//        } else {
//            $event = "Fellowship Application Spreadsheet download failed!";
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
//            return $this->redirect( $this->generateUrl('fellapp_home') );
//        }
//
//        //2) populate
//        $populatedCount = $fellappUtil->populateFellApp();
//
//        if( $populatedCount >= 0 ) {
//            $event = "Populated ".$populatedCount." Fellowship Applicantions.";
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
//        return $this->redirect( $this->generateUrl('fellapp_home') );
    }

    /**
     * Show home page
     *
     * @Route("/populate", name="fellapp_populate")
     */
    public function populateSpreadsheetAction(Request $request) {

        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_ADMIN') ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        $fellappUtil = $this->container->get('fellapp_util');
        $populatedCount = $fellappUtil->populateFellApp();

        if( $populatedCount >= 0 ) {
            $event = "Populated ".$populatedCount." Fellowship Applicantions.";
            $flashType = 'notice';
        } else {
            $event = "Google API service failed!";
            $flashType = 'warning';
        }

        $this->get('session')->getFlashBag()->add(
            $flashType,
            $event
        );

        return $this->redirect( $this->generateUrl('fellapp_home') );
    }


    /**
     * Import spreadsheet to C:\Program Files (x86)\Aperio\Spectrum\htdocs\order\scanorder\Scanorders2\web\Uploaded\fellapp\Spreadsheets
     *
     * @Route("/import", name="fellapp_import")
     */
    public function importAction(Request $request) {

        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_ADMIN') ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        $fellappUtil = $this->container->get('fellapp_util');
        $fileDb = $fellappUtil->importFellApp();

        if( $fileDb ) {
            $event = "Fellowship Application Spreadsheet file has been successful downloaded to the server with id=" . $fileDb->getId().", title=".$fileDb->getUniquename();
            $flashType = 'notice';
        } else {
            $event = "Fellowship Application Spreadsheet download failed!";
            $flashType = 'warning';
        }

        $this->get('session')->getFlashBag()->add(
            $flashType,
            $event
        );

        //exit('import event'.$event);

        return $this->redirect( $this->generateUrl('fellapp_home') );

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
//        return $this->redirect( $this->generateUrl('fellapp_home') );
    }




//    /**
//     * NOT USED NOW
//     * update report by js
//     *
//     * @Route("/update-report/", name="fellapp_update_report", options={"expose"=true})
//     * @Method("POST")
//     */
//    public function updateReportAction(Request $request) {
//
//        $id = $request->get('id');
//
//        $em = $this->getDoctrine()->getManager();
//        $entity = $em->getRepository('OlegFellAppBundle:FellowshipApplication')->find($id);
//
//        if( !$entity ) {
//            throw $this->createNotFoundException('Unable to find Fellowship Application by id='.$id);
//        }
//
//        echo "reports = " . count($entity->getReports()) . "<br>";
//        exit();
//
//        //update report if report does not exists
//        if( count($entity->getReports()) == 0 ) {
//            $fellappRepGen = $this->container->get('fellapp_reportgenerator');
//            $fellappRepGen->addFellAppReportToQueue( $id, 'overwrite' );
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
     * @Route("/download-pdf/{id}", name="fellapp_download_pdf")
     * @Route("/view-pdf/{id}", name="fellapp_view_pdf")
     * @Method("GET")
     */
    public function downloadReportAction(Request $request, $id) {

//        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_USER') ){
//            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
//        }
//        if( false == $this->get('security.context')->isGranted("read","FellowshipApplication") ){
//            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
//        }

        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegFellAppBundle:FellowshipApplication')->find($id);

        if( !$entity ) {
            throw $this->createNotFoundException('Unable to find Fellowship Application by id='.$id);
        }

        //user who has the same fell type can view or edit
        $fellappUtil = $this->container->get('fellapp_util');
        if( $fellappUtil->hasFellappPermission($user,$entity) == false ) {
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }
        if( false == $this->get('security.context')->isGranted("read",$entity) ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        //event log
        $userSecUtil = $this->container->get('user_security_utility');
        $event = "Report for Fellowship Application with ID".$id." has been downloaded by ".$user;
        $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$user,$entity,null,'Complete Fellowship Application PDF Downloaded');

        $reportDocument = $entity->getRecentReport();
        //echo "report=".$reportDocument."<br>";
        //exit();

        if( $reportDocument ) {

            $routeName = $request->get('_route');

            if( $routeName == "fellapp_view_pdf" ) {
                return $this->redirect( $this->generateUrl('fellapp_file_view',array('id' => $reportDocument->getId())) );
            } else {
                return $this->redirect( $this->generateUrl('fellapp_file_download',array('id' => $reportDocument->getId())) );
            }

        } else {

            //create report
            $fellappRepGen = $this->container->get('fellapp_reportgenerator');
            $argument = 'asap';
            //if( $this->get('security.context')->isGranted('ROLE_FELLAPP_COORDINATOR') ) {
                //$argument = 'overwrite';
            //}
            $fellappRepGen->addFellAppReportToQueue( $id, $argument );

            //exit('fellapp_download_pdf exit');

            $this->get('session')->getFlashBag()->add(
                'warning',
                'Complete Application PDF is not ready yet. Please try again later.'
            );

            return $this->redirect( $this->generateUrl('fellapp_show',array('id' => $id)) );
        }

    }


    /**
     * @Route("/regenerate-all-complete-application-pdfs/", name="fellapp_regenerate_reports")
     *
     * @Template("OlegFellAppBundle:Form:new.html.twig")
     */
    public function regenerateAllReportsAction(Request $request) {

        exit("This method is disabled");

        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_ADMIN') ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        $fellappRepGen = $this->container->get('fellapp_reportgenerator');
        $numDeleted = $fellappRepGen->regenerateAllReports();

        $em = $this->getDoctrine()->getManager();
        $fellapps = $em->getRepository('OlegFellAppBundle:FellowshipApplication')->findAll();
        $estimatedTime = count($fellapps)*5; //5 min for each report
        $this->get('session')->getFlashBag()->add(
            'notice',
            'All Application Reports will be regenerated. Estimated processing time for ' . count($fellapps) . ' reports is ' . $estimatedTime . ' minutes. Number of deleted processes in queue ' . $numDeleted
        );

        return $this->redirect( $this->generateUrl('fellapp_home') );
    }

    /**
     * @Route("/reset-queue-and-run/", name="fellapp_reset_queue_run")
     *
     * @Template("OlegFellAppBundle:Form:new.html.twig")
     */
    public function resetQueueRunAction(Request $request) {

        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_ADMIN') ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }

        $fellappRepGen = $this->container->get('fellapp_reportgenerator');
        $numUpdated = $fellappRepGen->resetQueueRun();

        $em = $this->getDoctrine()->getManager();
        $processes = $em->getRepository('OlegFellAppBundle:Process')->findAll();
        $estimatedTime = count($processes)*5; //5 min for each report
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Queue with ' . count($processes) . ' will be re-run. Estimated processing time is ' . $estimatedTime . ' minutes. Number of reset processes in queue ' . $numUpdated
        );

        return $this->redirect( $this->generateUrl('fellapp_home') );
    }


    
    /**
     * @Route("/download-applicants-list-excel/{currentYear}/{fellappTypeId}/{fellappIds}", name="fellapp_download_applicants_list_excel")
     */
    public function downloadApplicantListExcelAction(Request $request, $currentYear, $fellappTypeId, $fellappIds) {

//        if( false == $this->get('security.context')->isGranted('ROLE_FELLAPP_COORDINATOR') &&
//            false == $this->get('security.context')->isGranted('ROLE_FELLAPP_DIRECTOR') &&
//            false == $this->get('security.context')->isGranted('ROLE_FELLAPP_INTERVIEWER') &&
//            false == $this->get('security.context')->isGranted('ROLE_FELLAPP_OBSERVER')
//        ){
//            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
//        }
        if( false == $this->get('security.context')->isGranted("read","FellowshipApplication") ){
            return $this->redirect( $this->generateUrl('fellapp-nopermission') );
        }
       
        $em = $this->getDoctrine()->getManager();
        $fellowshipSubspecialty = null;
        $institutionNameFellappName = "";
        
        if( $fellappTypeId && $fellappTypeId > 0 ) {
            $fellowshipSubspecialty = $em->getRepository('OlegUserdirectoryBundle:FellowshipSubspecialty')->find($fellappTypeId);
        }
        
        if( $fellowshipSubspecialty ) {
            $institution = $fellowshipSubspecialty->getInstitution();
            $institutionNameFellappName = $institution." ".$fellowshipSubspecialty." ";
        }
        
        //[YEAR] [WCMC (top level of actual institution)] [FELLOWSHIP-TYPE] Fellowship Candidate Data generated on [DATE] at [TIME] EST.xls
        $fileName = $currentYear." ".$institutionNameFellappName."Fellowship Candidate Data generated on ".date('m/d/Y H:i').".xlsx";
        $fileName = str_replace("  ", " ", $fileName);
        $fileName = str_replace(" ", "-", $fileName);
        
        $fellappUtil = $this->container->get('fellapp_util');
        $excelBlob = $fellappUtil->createApplicantListExcel($fellappIds);
        
        $writer = \PHPExcel_IOFactory::createWriter($excelBlob, 'Excel2007');
        //ob_end_clean();
        //$writer->setIncludeCharts(true);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        //header('Content-Disposition: attachment;filename="fileres.xlsx"');

        // Write file to the browser
        $writer->save('php://output');

        exit();      
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
     * @Route("/test", name="fellapp_test")
     * @Method("GET")
     */
    public function testAction() {

        //test url on console
//        $fellappUtil = $this->container->get('fellapp_util');
//        $em = $this->getDoctrine()->getManager();
//        $fellowshipApplication = $em->getRepository('OlegFellAppBundle:FellowshipApplication')->find(162);
//        $fellappUtil->sendConfirmationEmailsOnApplicationPopulation($fellowshipApplication,$fellowshipApplication->getUser());
//        exit('email test');

        $googleSheetManagement = $this->container->get('fellapp_googleSheetManagement');

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
