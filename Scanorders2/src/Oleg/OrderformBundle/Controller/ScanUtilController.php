<?php

namespace Oleg\OrderformBundle\Controller;

use Oleg\UserdirectoryBundle\Controller\UtilController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Oleg\OrderformBundle\Helper\FormHelper;

//TODO: optimise by removing foreach loops

/**
 * Message controller.
 *
 * @Route("/util")
 */
class ScanUtilController extends UtilController {

    /**
     * @Route("/common/generic/{name}", name="scan_get_generic_select2")
     * @Method("GET")
     */
    public function getGenericAction( Request $request, $name ) {

        return $this->getGenericList($request,$name);
    }

    public function getClassBundleByName($name) {

        $bundleName = "OrderformBundle";

        switch( $name ) {

            case "parttitle":
                $className = "ParttitleList";
                break;
            case "labtesttype":
                $className = "LabTestType";
                break;
            case "embedderinstruction":
                $className = "EmbedderInstructionList";
                break;

            default:
                $className = null;
        }

        $res = array(
            'className' => $className,
            'bundleName' => $bundleName
        );

        return $res;
    }





////////////////// we can convert almost all functions below to use getGenericAction method by using js getComboboxGeneric(null,'embedderinstruction',_embedderinstruction,false,'','scan');

    /**
     * @Route("/stain", name="get-stain")
     * @Method("GET")
     */
    public function getStainsAction() {

        $em = $this->getDoctrine()->getManager();
        //$addwhere = "";

        $request = $this->get('request');
        $opt = trim( $request->get('opt') );

        //echo "opt=".$opt."<br>";

//        if( $this->get('security.context')->isGranted('ROLE_SCANORDER_DIVISION_CHIEF') ||
//            $this->get('security.context')->isGranted('ROLE_SCANORDER_SERVICE_CHIEF')
//        ) {
//            $addwhere = " OR list.type = 'user-added' ";
//        }

        $query = $em->createQueryBuilder()
            ->from('OlegOrderformBundle:StainList', 'list')
            ->select("list.id as id, list.fulltitle as text")
            ->leftJoin("list.original","original")
            ->where("original.id IS NULL")
            ->groupBy("list")
//            ->groupBy("list.id")
//            ->addGroupBy("list.orderinlist")
//            ->addGroupBy("list.fulltitle")
            ->orderBy("list.orderinlist","ASC"); //ASC DESC

        if( $opt ) {
            $user = $this->get('security.context')->getToken()->getUser();
            $query->andWhere("list.type = 'default' OR ( list.type = 'user-added' AND list.creator = :user)")->setParameter('user',$user);
        }

        //echo "query=".$query." ";

        //$output = $query->getQuery()->getResult('StainHydrator');
        $output = $query->getQuery()->getResult();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;
    }

    /**
     * Displays a form to create a new Message + Scan entities.
     * @Route("/procedure", name="get-procedure")
     * @Method("GET")
     */
    public function getProcedureAction() {

        $em = $this->getDoctrine()->getManager();

//        $query = $em->createQuery(
//            'SELECT proc.id as id, proc.name as text
//            FROM OlegOrderformBundle:ProcedureList proc WHERE proc.type = :type'
//        )->setParameter('type', 'default');
//
//        //$empty = array("id"=>0,"text"=>"");
//        $output = $query->getResult();
//        //array_unshift($output, $empty);

        $request = $this->get('request');
        $opt = trim( $request->get('opt') );

        $query = $em->createQueryBuilder()
            ->from('OlegOrderformBundle:ProcedureList', 'list')
            ->select("list.id as id, list.name as text")
            //->where("list.creator = ".$user)
            ->orderBy("list.orderinlist","ASC");

        if( $opt ) {
            $user = $this->get('security.context')->getToken()->getUser();
            $query->where("list.type = 'default' OR ( list.type = 'user-added' AND list.creator = :user)")->setParameter('user',$user);
        }

        //echo "query=".$query." ";

        $output = $query->getQuery()->getResult();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;
    }
    
    /**
     * @Route("/organ", name="get-organ")
     * @Method("GET")
     */
    public function getOrgansAction() {

        $em = $this->getDoctrine()->getManager();

//        $query = $em->createQuery(
//            'SELECT proc.id as id, proc.name as text
//            FROM OlegOrderformBundle:OrganList proc WHERE proc.type = :type'
//        )->setParameter('type', 'default');
//
//        //$empty = array("id"=>0,"text"=>"");
//        $output = $query->getResult();
//        //array_unshift($output, $empty);

        $request = $this->get('request');
        $opt = trim( $request->get('opt') );

        $query = $em->createQueryBuilder()
            ->from('OlegOrderformBundle:OrganList', 'list')
            ->select("list.id as id, list.name as text")
            ->orderBy("list.orderinlist","ASC");

        if( $opt ) {
            $user = $this->get('security.context')->getToken()->getUser();
            $query->where("list.type = 'default' OR ( list.type = 'user-added' AND list.creator = :user)")->setParameter('user',$user);
        }

        $output = $query->getQuery()->getResult();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;
    }

    /**
     * Displays a form to create a new Message + Scan entities.
     * @Route("/scanregion", name="get-scanregion")
     * @Method("GET")
     */
    public function getScanRegionAction() {

//        $em = $this->getDoctrine()->getManager();
//        $query = $em->createQuery(
//            'SELECT obj.name FROM OlegOrderformBundle:RegionToScan obj'
//        );
//        $res = $query->getResult();

        $arr = array();

        $user = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        //$entities = $em->getRepository('OlegOrderformBundle:RegionToScan')->findByType('default');

        //////////////////////////////////// 1) get all default list ////////////////////////////////////
        $query = $em->createQueryBuilder()
            ->from('OlegOrderformBundle:RegionToScan', 'list')
            ->select("list.name")
            ->where("list.type = 'default' OR ( list.type = 'user-added' AND list.creator = :user)")->setParameter('user',$user)
            ->groupBy('list')
            ->orderBy("list.orderinlist","ASC");
        $entities = $query->getQuery()->getResult();
        //////////////////////////////////// END OF 1 ///////////////////////////////////////////


        //////////////// 2) create addwhere to does not select scanregion elements with the same name as in list names //////////////////////
        $addwhere = "";
        $count = 1;
        $parametersArr = array();
        foreach( $entities as $entity ) {
            $arr[] = $entity["name"];
            $parametersArr['text'.$count] = $entity["name"];
            $addwhere = $addwhere . "scan.scanregion != :text".$count;
            if( count($entities) > $count ) {
                $addwhere = $addwhere . " AND ";
            }
            $count++;
        }

        if( $addwhere != "" ) {
            $addwhere = " AND (" . $addwhere . ")";
        }

        //echo "addwhere=".$addwhere." \n ";
        //////////////////////////////////// END OF 2 ///////////////////////////////////////////

//        //add custom added values
//        //TODO: add custom values, added by ordering provider
//        $user = $this->get('security.context')->getToken()->getUser();
//        $entities = $this->getDoctrine()->getRepository('OlegOrderformBundle:Imaging')->findByProvider($user);
//        foreach( $entities as $entity ) {
//            $arr[] = $entity->getScanregion();
//        }

        //////////////// 3) add custom added values by order id (if id is set) //////////////////////
        $request = $this->get('request');
        $id = trim( $request->get('opt') );

        if( $id && $id != "undefined" ) {
            $message = $this->getDoctrine()->getRepository('OlegOrderformBundle:Message')->findOneByOid($id);
            if( $message ) {
                $slides = $message->getSlide();
                foreach( $slides as $slide ) {
                    $arr[] = $slide->getScan()->first()->getScanregion();
                }
            }
        }
        //////////////////////////////////// END OF 3 ///////////////////////////////////////////


        //////////////// 4) add custom added values from all my orders //////////////////////
        $parametersArr['user'] = $user;

        $query = $em->createQueryBuilder()
            ->from('OlegOrderformBundle:Message', 'list')
            ->select("scan.scanregion")
            ->innerJoin("list.slide","slide")
            ->innerJoin("slide.scan","scan")
            ->innerJoin("scan.provider","provider")
            ->groupBy('scan')
            ->addGroupBy('scan.scanregion')
            ->where( "provider = :user ".$addwhere )
            ->setParameters( $parametersArr );

        //echo "query=".$query." \n ";

        $myOrders = $query->getQuery()->getResult();

        foreach( $myOrders as $scanreg ) {
            //echo $scanreg['scanregion']." => ";
            $arr[] = $scanreg['scanregion'];
        }
        //////////////////////////////////// END OF 4 ///////////////////////////////////////////
        
        $output = array();
        
        //$count = 0;
        foreach( $arr as $region ) {
            $element = array('id'=>$region, 'text'=>$region);
            $output[] = $element;          
            //$count++;
        }
        

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;
    }
    
    /**
     * Displays a form to create a new Message + Scan entities.
     * @Route("/delivery", name="get-orderdelivery")
     * @Method("GET")
     */
    public function getOrderDeliveryAction() {

        $arr = array();

        $user = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        //$entities = $em->getRepository('OlegOrderformBundle:OrderDelivery')->findByType('default');

        //////////////////////////////////// 1) get all default list ////////////////////////////////////
        $query = $em->createQueryBuilder()
            ->from('OlegOrderformBundle:OrderDelivery', 'list')
            ->select("list.name")
            ->where("list.type = 'default' OR ( list.type = 'user-added' AND list.creator = :user)")->setParameter('user',$user)
            ->groupBy('list')
            ->orderBy("list.orderinlist","ASC");
        $entities = $query->getQuery()->getResult();
        //////////////////////////////////// END OF 1 ///////////////////////////////////////////


        //////////////// 2) create addwhere to does not select scanregion elements with the same name as in list names //////////////////////
        $addwhere = "";
        $count = 1;
        $parametersArr = array();
        foreach( $entities as $entity ) {
            $arr[] = $entity["name"];
            $parametersArr['text'.$count] = $entity["name"];
            $addwhere = $addwhere . "scanorder.delivery != :text".$count;
            if( count($entities) > $count ) {
                $addwhere = $addwhere . " AND ";
            }
            $count++;
        }

        if( $addwhere != "" ) {
            $addwhere = " AND (" . $addwhere . ")";
        }

        //echo "addwhere=".$addwhere." \n ";
        //////////////////////////////////// END OF 2 ///////////////////////////////////////////

        //////////////// 3) add custom added values by order id (if id is set) //////////////////////
        $request = $this->get('request');
        $id = trim( $request->get('opt') );

        if( $id && $id != "undefined" ) {
            $message = $this->getDoctrine()->getRepository('OlegOrderformBundle:Message')->findOneByOid($id);
            if( $message ) {
                $arr[] = $message->getScanorder()->getDelivery();
            }
        }
        //////////////////////////////////// END OF 3 ///////////////////////////////////////////

        //////////////// 4) add custom added values from all my orders //////////////////////
        $parametersArr['user'] = $user;

        $query = $em->createQueryBuilder()
            ->from('OlegOrderformBundle:Message', 'list')
            ->select("scanorder.delivery")
            ->innerJoin("list.provider","provider")
            ->innerJoin("list.scanorder","scanorder")
            ->groupBy('scanorder.delivery')
            ->where( "provider = :user ".$addwhere )
            ->setParameters( $parametersArr );

        //echo "query=".$query." \n ";

        $myOrders = $query->getQuery()->getResult();

        foreach( $myOrders as $scanreg ) {
            //echo $scanreg['scanregion']." => ";
            $arr[] = $scanreg['delivery'];
        }
        //////////////////////////////////// END OF 4 ///////////////////////////////////////////

        $output = array();
        
        //$count = 0;
        foreach( $arr as $region ) {
            $element = array('id'=>$region, 'text'=>$region);
            $output[] = $element;          
            //$count++;
        }
        
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;
    }


    /**
     * @Route("/partname", name="get-partname")
     * @Method("GET")
     */
    public function getPartnameAction() {

        $formHelper = new FormHelper();
        $arr = $formHelper->getPart();

        //add custom added values by order id
        $request = $this->get('request');
        $id = trim( $request->get('opt') );

        if( $id && $id != "undefined" ) {
            $message = $this->getDoctrine()->getRepository('OlegOrderformBundle:Message')->findOneByOid($id);
            if( $message ) {
                $parts = $message->getPart();
                foreach( $parts as $part ) {
                    foreach( $part->getPartname() as $partname ) {
                        $arr[] = $partname."";
                    }
                }
            }
        }

        $output = array();

        foreach( $arr as $var ) {
            $element = array('id'=>$var."", 'text'=>$var.""); 
            $output[] = $element;
        }
        
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;
    }

    /**
     * @Route("/blockname", name="get-blockname")
     * @Method("GET")
     */
    public function getBlocknameAction() {

        $formHelper = new FormHelper();
        $arr = $formHelper->getBlock();

        //add custom added values by order id
        $request = $this->get('request');
        $id = trim( $request->get('opt') );

        if( $id && $id != "undefined" ) {
            $message = $this->getDoctrine()->getRepository('OlegOrderformBundle:Message')->findOneByOid($id);
            if( $message ) {
                $blocks = $message->getBlock();
                foreach( $blocks as $block ) {
                    foreach( $block->getBlockname() as $blockname ) {
                        $arr[] = $blockname."";
                    }
                }
            }
        }

        $output = array();

        foreach( $arr as $var ) {
            $element = array('id'=>$var."", 'text'=>$var.""); 
            $output[] = $element;
        }
        
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;
    }

    /**
     * @Route("/accessiontype", name="get-accessiontype")
     * @Method("GET")
     */
    public function getAccessionTypeAction() {

        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');
        $opt = trim( $request->get('opt') );
        $type = trim( $request->get('type') );

        //echo "opt=".$opt."<br>";

        $query = $em->createQueryBuilder()
            ->from('OlegOrderformBundle:AccessionType', 'list')
            ->select("list.id as id, list.name as text")
            ->orderBy("list.orderinlist","ASC");

        $user = $this->get('security.context')->getToken()->getUser();

        if( $type == "single" ) {
            if( $opt && $opt != "undefined" ) {
                $query->where("list.type = :type OR list.type = :typetma OR ( list.type = 'user-added' AND list.creator = :user)");    //->setParameter('type', 'default')->setParameter('typetma', 'TMA');
                $query->setParameters( array('type' => 'default', 'typetma' => 'TMA', 'user' => $user) );
            }
        } else {
            if( $opt && $opt != "undefined" ) {
                $query->where("list.type = :type AND list.type != :typetma OR ( list.type = 'user-added' AND list.creator = :user)");   //->setParameter('type', 'default')->setParameter('typetma', 'TMA');
                $query->setParameters( array('type' => 'default', 'typetma' => 'TMA', 'user' => $user) );
            } else {
                $query->where('list.type != :type')->setParameter('type', 'TMA');
            }
        }

        //echo "query=".$query."<br>";

        $output = $query->getQuery()->getResult();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;
    }

    /**
     * @Route("/mrntype", name="get-mrntype")
     * @Method("GET")
     */
    public function getMrnTypeAction() {

        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');
        $opt = trim( $request->get('opt') );
        $type = trim( $request->get('type') );

        //echo "opt=".$opt."<br>";

        $query = $em->createQueryBuilder()
            ->from('OlegOrderformBundle:MrnType', 'list')
            ->select("list.id as id, list.name as text")
            ->orderBy("list.orderinlist","ASC");

        $user = $this->get('security.context')->getToken()->getUser();

        if( $opt && $opt != "undefined" ) {
            $query->where("list.type = :type OR ( list.type = 'user-added' AND list.creator = :user)");
            $query->setParameters( array('type' => 'default', 'user' => $user) );
        }

        //echo "query=".$query."<br>";

        $output = $query->getQuery()->getResult();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;
    }


    /**
     * @Route("/slidetype", name="get-slidetype")
     * @Method("GET")
     */
    public function getSlideTypesAction() {

        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');

        $query = $em->createQueryBuilder()
            ->from('OlegOrderformBundle:SlideType', 'list')
            ->select("list.name as text")
            ->where("list.type='default'")
            ->orderBy("list.orderinlist","ASC");

        //echo "query=".$query."<br>";

        $output = $query->getQuery()->getResult();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;
    }



    /**
     * @Route("/optionalusereducational", name="get-optionalusereducational")
     * @Route("/optionaluserresearch", name="get-optionaluserresearch")
     * @Method("GET")
     */
    public function getOptionalUserAction() {

        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');
        $holderId = trim( $request->get('opt') ); //associated object ProjectTitleTree id
        $routeName = $request->get('_route');

        if( $routeName == "get-optionalusereducational" ) {
            $role = "ROLE_SCANORDER_COURSE_DIRECTOR";
            $prefix = 'Oleg';
            $bundleName = 'OrderformBundle';
            $className = 'CourseTitleTree';
        }
        if( $routeName == "get-optionaluserresearch" ) {
            $role = "ROLE_SCANORDER_PRINCIPAL_INVESTIGATOR";
            $prefix = 'Oleg';
            $bundleName = 'OrderformBundle';
            $className = 'ProjectTitleTree';
        }

        //1) ProjectTitleTree id => get research => get principalWrappers
        if( $holderId && $holderId != "undefined" ) {
            $query = $em->createQueryBuilder()
                ->from($prefix.$bundleName.':'.$className, 'list')
                //->select("userWrappers.id as id, CONCAT(userWrappers.name,CONCAT(' - ',userWrappersUserInfos.displayName)) as text")
                ->select("userWrappers.id as id, (CASE WHEN userWrappersUser.id IS NULL THEN userWrappers.name ELSE userWrappers.name+' - '+userWrappersUserInfos.displayName END) as text")
                //->select("userWrappers.id as id, userWrappers.name as text")
                ->leftJoin("list.userWrappers","userWrappers")
                ->leftJoin("userWrappers.user","userWrappersUser")
                ->leftJoin("userWrappersUser.infos","userWrappersUserInfos")

                ->where("list.id = :holderId AND (userWrappers.type = :type OR userWrappers.type = :type2)")
                ->orderBy("list.orderinlist","ASC")
                ->setParameters( array(
                    'holderId' => $holderId,
                    'type' => 'default',
                    'type2' => 'user-added'
                ));

            //echo "query=".$query."<br>";

            $output = $query->getQuery()->getResult();
        } else {
            $output = array();
        }

        //var_dump($output);

        //2) add users with ROLE_SCANORDER_COURSE_DIRECTOR and ROLE_SCANORDER_PRINCIPAL_INVESTIGATOR
        $query = $em->createQueryBuilder()
            ->from('OlegUserdirectoryBundle:User', 'list')
            //->select("list.id as id, list.username as text")
            ->select("list")
            ->where("list.roles LIKE :role")
            ->orderBy("list.id","ASC")
            ->setParameter('role', '%"' . $role . '"%');

        $users = $query->getQuery()->getResult();

        foreach( $users as $user ) {
            $element = array('id'=>$user->getPrimaryPublicUserId()."", 'text'=>$user."");
            if( !$this->in_complex_array($user."",$output) ) {
                //echo "add user id=".$user->getId()."\n";
                $output[] = $element;
            }
        }

        //echo "\nfinal output:";
        //var_dump($output);
        //echo "\n";

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;

    }



    /**
     * @Route("/account", name="get-account")
     * @Method("GET")
     */
    public function getAccountAction() {

        $whereServicesList = "";

        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');
        $opt = trim( $request->get('opt') );

        $query = $em->createQueryBuilder()
            ->from('OlegOrderformBundle:Account', 'list')
            ->select("list.id as id, list.name as text")
            ->orderBy("list.orderinlist","ASC");

        $user = $this->get('security.context')->getToken()->getUser();

        if( $this->get('security.context')->isGranted('ROLE_SCANORDER_PROCESSOR') ) {
            //$query->where("list.type = 'user-added' AND list.creator = :user")->setParameter('user',$user);
        } else {
            $query->where("list.type = 'user-added' AND list.creator = :user")->setParameter('user',$user);
        }

        $output = $query->getQuery()->getResult();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;
    }


    /**
     * @Route("/urgency", name="get-urgency")
     * @Method("GET")
     */
    public function getUrgencyAction() {


        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');
        $opt = trim( $request->get('opt') );

        $query = $em->createQueryBuilder()
            ->from('OlegOrderformBundle:Urgency', 'list')
            ->select("list.id as id, list.name as text")
            ->orderBy("list.orderinlist","ASC");

        $user = $this->get('security.context')->getToken()->getUser();

        $query->where("list.type = 'default' OR ( list.type = 'user-added' AND list.creator = :user)")->setParameter('user',$user);

        $urgencies = $query->getQuery()->getResult();

        $output = array();
        foreach( $urgencies as $urgency ) {
            //echo "urgency=".$urgency->getName()." ";
            //var_dump($urgency);
            $element = array('id'=>$urgency['text']."", 'text'=>$urgency['text']."");
            $output[] = $element;
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;
    }

    /**
     * @Route("/returnlocation", name="scan_get_returnlocation")
     * @Method("GET")
     */
    public function getReturnLocationAction(Request $request) {

        $providerid = trim( $request->get('providerid') );
        $proxyid = trim( $request->get('proxyid') );

        if( $providerid == 'undefined' ) {
            $providerid = null;
        }

        if( $proxyid == 'undefined' ) {
            $proxyid = null;
        }

        //get default returnLocation option
        $orderUtil = $this->get('scanorder_utility');
        $returnLocations = $orderUtil->getOrderReturnLocations(null,$providerid,$proxyid);
        $preferredLocations = $returnLocations['preferred_choices'];

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder()
            ->from('OlegUserdirectoryBundle:Location', 'list')
            ->select("list")
            ->orderBy("user.username","ASC")
            ->addOrderBy("list.name","ASC");

        $query->where("list.type = :typedef OR list.type = :typeadd")->setParameters(array('typedef' => 'default','typeadd' => 'user-added'));

        //Exclude from the list locations of type "Patient Contact Information", "Medical Office", and "Inpatient location".
        $andWhere = "locationTypes.name IS NULL OR ".
            "(" .
                "locationTypes.name !='Patient Contact Information' AND ".
                "locationTypes.name !='Medical Office' AND ".
                "locationTypes.name !='Inpatient location' AND ".
                "locationTypes.name !='Employee Home'" .
            ")";

        $query->leftJoin("list.locationTypes", "locationTypes");
        $query->leftJoin("list.user", "user");
        $query->andWhere($andWhere);

        //exclude system user:  "user.keytype IS NOT NULL AND user.primaryPublicUserId != 'system'"; //"user.email != '-1'"
        $query->andWhere("user.id IS NULL OR (user.keytype IS NOT NULL AND user.primaryPublicUserId != 'system')");

        //exclude preferred locations (they will be added later)
        $prefLocs = "";
        foreach( $preferredLocations as $loc ) {
            if( $prefLocs != "" ) {
                $prefLocs = $prefLocs . " AND ";
            }
            $prefLocs = $prefLocs . " list.id != " .$loc->getId();
        }
        //echo "prefLocs=".$prefLocs."<br>";
        if( $prefLocs ) {
            $query->andWhere($prefLocs);
        }

        //do not show (exclude) all locations that are tied to a user who has no current employment periods (all of whose employment periods have an end date)
        $curdate = date("Y-m-d", time());
        $query->leftJoin("user.employmentStatus", "employmentStatus");
        $currentusers = "employmentStatus.terminationDate IS NULL OR employmentStatus.terminationDate > '".$curdate."'";
        $query->andWhere($currentusers);

        //echo "query=".$query." | ";

        $locations = $query->getQuery()->getResult();
        //echo "loc count=".count($locations)."<br>";

        $output = array();

        foreach( $preferredLocations as $location ) {
            $element = array('id'=>$location->getId(), 'text'=>$location->getNameFull());
            $output[] = $element;
        }

        foreach( $locations as $location ) {
            $element = array('id'=>$location->getId(), 'text'=>$location->getNameFull());
            $output[] = $element;
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;
    }



    /**
     * Get all users and user wrappers combined
     * @Route("/common/proxyuser", name="scan_get_proxyuser")
     * @Method("GET")
     */
    public function getProxyusersAction() {

        $em = $this->getDoctrine()->getManager();

        $output = array();

        ///////////// get all wrapper users /////////////
        $query = $em->createQueryBuilder()
            ->from('OlegUserdirectoryBundle:UserWrapper', 'list')
            ->leftJoin("list.user", "user")
            ->leftJoin("user.infos", "infos")
            ->leftJoin("user.employmentStatus", "employmentStatus")
            ->leftJoin("employmentStatus.employmentType", "employmentType")
            ->select("list")
            ->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
            //->select("list.id as id, infos.displayName as text")
            ->orderBy("infos.displayName","ASC");
        $userWrappers = $query->getQuery()->getResult();
        foreach( $userWrappers as $userWrapper ) {
            $element = array(
                'id'        => $userWrapper->getId(),
                'text'      => $userWrapper.""
            );
            if( !$this->in_complex_array($userWrapper."",$output) ) {
                $output[] = $element;
            }
        }
        ///////////// EOF get all wrapper users /////////////

        ///////////// get all users /////////////
        $query = $em->createQueryBuilder()
            ->from('OlegUserdirectoryBundle:User', 'list')
            ->leftJoin("list.infos", "infos")
            ->leftJoin("list.employmentStatus", "employmentStatus")
            ->leftJoin("employmentStatus.employmentType", "employmentType")
            ->select("list")
            ->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL")
            //->select("infos.displayName as id, infos.displayName as text")
            ->orderBy("infos.displayName","ASC");

        $users = $query->getQuery()->getResult();

        foreach( $users as $user ) {
            $element = array('id'=>$user."", 'text'=>$user."");
            if( !$this->in_complex_array($user."",$output) ) {
                $output[] = $element;
            }
        }
        ///////////// EOF get all users /////////////

        //$output = array_merge($users,$output);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($output));
        return $response;
    }


    //search if $needle exists in array $products
    public function in_complex_array($needle,$products,$indexstr='text') {
        foreach( $products as $product ) {
            if ( $product[$indexstr] === $needle ) {
                return true;
            }
        }
        return false;
    }

}
