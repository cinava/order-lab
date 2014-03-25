<?php

namespace Oleg\OrderformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;

//use Oleg\OrderformBundle\Entity\OrderInfo;
//use Oleg\OrderformBundle\Form\OrderInfoType;
use Oleg\OrderformBundle\Form\FilterType;
use Oleg\OrderformBundle\Entity\Document;
use Oleg\OrderformBundle\Helper\OrderUtil;


//ScanOrder joins OrderInfo + Scan
/**
 * OrderInfo controller.
 *
 * @Route("/")
 */
class ScanOrderController extends Controller {

    /**
     * Lists all OrderInfo entities.
     *
     * @Route("/", name="scan-order-home")
     * @Method("GET")
     * @Template("OlegOrderformBundle:Default:home.html.twig")
     */
    public function indexAction( Request $request ) {

        if( false == $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') ){
            return $this->redirect( $this->generateUrl('scan-order-nopermission') );
        }

        return array();
    }

    /**
     * Lists all OrderInfo entities.
     *
     * @Route("/my-scan-orders", name="my-scan-orders")
     * @Route("/incoming-scan-orders", name="incoming-scan-orders")
     * @Method("GET")
     * @Template("OlegOrderformBundle:ScanOrder:index.html.twig")
     */
    public function orderListAction( Request $request ) {

        $em = $this->getDoctrine()->getManager();

        $routeName = $request->get('_route');
        //echo "routeName=".$routeName."<br>";

        if( $routeName == "incoming-scan-orders" && false === $this->get('security.context')->isGranted('ROLE_PROCESSOR')) {
            return $this->redirect( $this->generateUrl('my-scan-orders') );
        }

        //by user
        $user = $this->get('security.context')->getToken()->getUser();
        //echo "user=".$user;
        if( !is_object($user) ) {
            return $this->redirect( $this->generateUrl('scan-order-nopermission') );
        }

        if( $routeName == "incoming-scan-orders" ) {
            $services = $this->getServiceFilter();
        } else {
            $services = null;
        }
        
        //create filters
        $form = $this->createForm(new FilterType( $this->getFilter(), $user, $services ), null);
        $form->bind($request);

        $repository = $this->getDoctrine()->getRepository('OlegOrderformBundle:OrderInfo');
        $dql =  $repository->createQueryBuilder("orderinfo");
        $dql->select('orderinfo, COUNT(slides) as slidecount');
        $dql->groupBy('orderinfo');
        $dql->addGroupBy('status.name');
        $dql->addGroupBy('formtype.name');
        $dql->addGroupBy('provider.username');

        $dql->innerJoin("orderinfo.slide", "slides");
        $dql->innerJoin("orderinfo.provider", "provider");
        $dql->innerJoin("orderinfo.type", "formtype");
        $dql->innerJoin("orderinfo.history", "history");

        $search = $form->get('search')->getData();
        $filter = $form->get('filter')->getData();
        $service = $form->get('service')->getData();

        //service
        //echo "<br>service=".$service."<br>";
        //exit();

        $criteriastr = "";

        //***************** Pathology Service filetr ***************************//
        $showprovider = 'false';

        //***************** Status filetr ***************************//
        $dql->innerJoin("orderinfo.status", "status");
        //echo "status filter = ".$filter."<br>";
        if( $filter && is_numeric($filter) && $filter > 0 ) {
            if( $criteriastr != "" ) {
                $criteriastr .= " AND ";
            }
            $criteriastr .= " status.id=" . $filter;
        }

        //filter special cases
        if( $filter && is_string($filter) && $filter != "All" ) {

            //echo "filter=".$filter;
            if( $criteriastr != "" ) {
                $criteriastr .= " AND ";
            }

            switch( $filter ) {

                case "With New Comments":
                    $orderUtil = new OrderUtil($em);
                    $newCommentsCriteriaStr = "( " . $orderUtil->getNewCommentsCriteriaStr($this->get('security.context')) . " ) ";
                    $criteriastr .= $newCommentsCriteriaStr;
                    break;
                case "With Comments":
                    $orderUtil = new OrderUtil($em);
                    $newCommentsCriteriaStr = "( " . $orderUtil->getNewCommentsCriteriaStr($this->get('security.context'),'all_comments') . " ) ";
                    $criteriastr .= $newCommentsCriteriaStr;
                    break;
                case "All Filled":
                    $criteriastr .= " status.name LIKE '%Filled%'";
                    break;
                case "All Filled & Returned":
                    $criteriastr .= " status.name LIKE '%Filled%' AND status.name LIKE '%Returned%'";
                    break;
                case "All Filled & Not Returned":
                    $criteriastr .= " status.name LIKE '%Filled%' AND status.name NOT LIKE '%Returned%'";
                    break;
                case "All Not Filled":
                    $criteriastr .= " status.name NOT LIKE '%Filled%' AND status.name NOT LIKE '%Not Submitted%'";
                    break;
                case "All On Hold":
                    $criteriastr .= " status.name LIKE '%On Hold%'";
                    break;
                case "All Canceled":
                    $criteriastr .= " status.name = 'Canceled by Submitter' OR status.name = 'Canceled by Processor'";
                    break;
                case "All Submitted & Amended":
                    $criteriastr .= " status.name = 'Submitted' OR status.name = 'Amended'";
                    break;
                case "All Stat":
                    $criteriastr .= " orderinfo.priority = 'Stat'";
                    break;
                case "Stat & Not Filled":
                    $criteriastr .= " orderinfo.priority = 'Stat' AND status.name NOT LIKE '%Filled%'";
                    break;
                case "Stat & Filled":
                    $criteriastr .= " orderinfo.priority = 'Stat' AND status.name LIKE '%Filled%'";
                    break;
                default:
                    ;
            }

        }
        //***************** END of Status filetr ***************************//

        //***************** Superseded filter ***************************//
        if( false === $this->get('security.context')->isGranted('ROLE_PROCESSOR') ) {
            //$superseded_status = $em->getRepository('OlegOrderformBundle:Status')->findOneByName('Superseded');
            if( $criteriastr != "" ) {
                $criteriastr .= " AND ";
            }
            $criteriastr .= " status.name != 'Superseded'";
        }
        //***************** END of Superseded filetr ***************************//


        //***************** Search filetr ***************************//
        if( $search && $search != "" ) {
            if( $criteriastr != "" ) {
                $criteriastr .= " AND ";
            }
            $dql->innerJoin("orderinfo.accession", "accessionobj");
            $dql->innerJoin("accessionobj.accession", "accession");
            $criteriastr .= "accession.field LIKE '%" . $search . "%'";
            
        }
        //***************** END of Search filetr ***************************//

        //***************** User filter ***************************//
        if( $routeName == "my-scan-orders" ) {

            $crituser = "";

            //echo $routeName.": service=".$service."<br>";
            //select only orders where this user is author or proxy user, except "Where I am Course Director" and "Where I am Principal Investigator" cases.
            if(
                $service != "Where I am Course Director" &&
                $service != "Where I am Principal Investigator"
            ) {

                $dql->leftJoin("orderinfo.proxyuser", "proxyuser");

                //show only my order and the orders where I'm a proxy
                //Orders I Personally Placed and Proxy Orders Placed For Me: $service == "My Orders"

                $crituser .= "( provider.id=".$user->getId();

                //***************** Proxy User Orders *************************//
                $crituser .= " OR proxyuser.id=".$user->getId();
                //***************** END of Proxy User Orders *************************//

                $crituser .= " )";
            }

            //***************** Service filter: show all orders with chosen pathology service matched with current user's service *****************//
            $allservices = $this->allServiceFilter( $service, $routeName, $user, $crituser );
            if( $allservices != "" ) {
                $showprovider = 'true';
                $crituser .= $allservices;
            }
            //***************** EOF Service filter: show all orders with chosen pathology service matched with current user's service *****************//

            //show all for ROLE_DIVISION_CHIEF: remove all user's restriction
            if( $this->get('security.context')->isGranted('ROLE_DIVISION_CHIEF') ) {
                $crituser = "";
            }

            if( $service == "Orders I Personally Placed" ) {
                //echo "Orders I Personally Placed <br>";
                if( $crituser != "" ) {
                    $crituser .= " AND ";
                }
                $crituser .= "provider.id=".$user->getId();
            }
            if( $service == "Proxy Orders Placed For Me" ) {
                //echo "Proxy Orders Placed For Me <br>";
                if( $crituser != "" ) {
                    $crituser .= " AND ";
                }
                //***************** Proxy User Orders *************************//
                $crituser .= "proxyuser.id=".$user->getId();
                //***************** END of Proxy User Orders *************************//
            }
            if( $service == "Where I am Course Director" ) {
                $dql->innerJoin("orderinfo.educational", "educational");
                if( $crituser != "" ) {
                    $crituser .= " AND ";
                }
                $crituser .= "educational.director=".$user->getId();
            }
            if( $service == "Where I am Principal Investigator" ) {
                //echo "Where I am Principal Investigator <br>";
                $dql->innerJoin("orderinfo.research", "research");
                if( $crituser != "" ) {
                    $crituser .= " AND ";
                }
                $crituser .= "research.principal=".$user->getId();
            }

            if( $criteriastr != "" && $crituser != "" ) {
                $criteriastr = $criteriastr." AND ".$crituser;
            } else {
                $criteriastr .= $crituser;
            }

        }
        //***************** END of User filetr ***************************//

        if( $routeName == "incoming-scan-orders" ) {
            //echo "admin index filter <br>";
            //***************** Service filter ***************************//
            
            //***************** End of Service filter ***************************//
        }

        //echo "<br>criteriastr=".$criteriastr."<br>";
        
        if( $criteriastr != "" ) {
            //TODO: use ->setParameter(1, $caravan);
            $dql->where($criteriastr);
        }

        $params = $this->get('request_stack')->getCurrentRequest()->query->all();
        $sort = $this->get('request_stack')->getCurrentRequest()->query->get('sort');
        
        if( $routeName == "my-scan-orders" ) {
            if( $params == null || count($params) == 0 ) {
                $dql->orderBy("orderinfo.orderdate","DESC");
            }
            if( $sort != 'orderinfo.oid' ) {
                $dql->orderBy("orderinfo.orderdate","DESC");
            }
        }
               
        if( $routeName == "incoming-scan-orders" ) {
            if( $sort == '' ) {
                $dql->orderBy("orderinfo.priority","DESC");
                $dql->addOrderBy("orderinfo.scandeadline","ASC");
                $dql->addOrderBy("orderinfo.orderdate","DESC");
            }
        }

//        $dql->orderBy("status.name","DESC");
        
        //echo "dql=".$dql;
        
        $limit = 15;
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1), /*page number*/
            $limit/*limit per page*/
        );

        //check for active user requests
        $accountreqs = array();
        if( $this->get('security.context')->isGranted('ROLE_PROCESSOR') ) {
            $accountreqs = $em->getRepository('OlegOrderformBundle:UserRequest')->findByStatus("active");
        }

        //check for active access requests
        $accessreqs = array();
        if( $this->get('security.context')->isGranted('ROLE_PROCESSOR') ) {
            $accessreqs = $em->getRepository('OlegOrderformBundle:User')->findByAppliedforaccess('active');
        }

        $processorComments = $em->getRepository('OlegOrderformBundle:ProcessorComments')->findAll();
        
        return array(
            'form' => $form->createView(),
            'showprovider' => $showprovider,
            'pagination' => $pagination,
            'accountreqs' => $accountreqs,
            'accessreqs' => $accessreqs,
            'routename' => $routeName,
            'comments' => $processorComments
        );
    }


    //requirements={"id" = "\d+"}
    /**
     * Deletes a OrderInfo entity.
     *
     * @Route("/scan-order/{id}/delete", name="scanorder_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {

        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect( $this->generateUrl('scan-order-nopermission') );
        }

        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OlegOrderformBundle:OrderInfo')->findOneByOid($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find OrderInfo entity.');
            }

//            $scan_entities = $em->getRepository('OlegOrderformBundle:Scan')->
//                    findBy(array('scanorder_id'=>$id));

//            $scan_entities = $em->getRepository('OlegOrderformBundle:Scan')->findBy(
//                array('scanorder' => $id)
//            );
            $entity->removeAllChildren();

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('scanorder'));
    }

    /**
     * Change status of orderinfo
     *
     * @Route("/scan-order/{id}/status/{status}/", name="scanorder_status")
     * @Method("GET")
     * @Template()
     */
    public function statusAction(Request $request, $id, $status) {

        if( false === $this->get('security.context')->isGranted('ROLE_SUBMITTER') &&
            false === $this->get('security.context')->isGranted('ROLE_EXTERNAL_SUBMITTER')
        ) {
            return $this->redirect( $this->generateUrl('scan-order-nopermission') );
        }
        
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();

        $orderUtil = new OrderUtil($em);

        //make uppercase: cancel, sumbit, un-cancel (Un-Cancel)
        //$status = str_replace("-"," ",$status);
        $status = ucwords($status);
        //$status = str_replace(" ","-",$status);

        $res = $orderUtil->changeStatus($id, $status, $user);

        if( $res['result'] == 'conflict' ) {   //redirect to amend
            return $this->redirect( $this->generateUrl( 'order_amend', array('id' => $res['oid']) ) );
        }

        $this->get('session')->getFlashBag()->add('status-changed',$res['message']);


        $referer_url = $request->headers->get('referer');
        //$referer_url = 'my-scan-orders';
        //return $this->redirect($this->generateUrl($previouspath));
        return new RedirectResponse($referer_url);
    }

    /**
     * Creates a form to delete a OrderInfo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    
    /**   
     * @Route("/thanks", name="thanks")
     * 
     * @Template("OlegOrderformBundle:ScanOrder:thanks.html.twig")
     */
    public function thanksAction( $oid = '' )
    {    
        
        return $this->render('OlegOrderformBundle:ScanOrder:thanks.html.twig',
            array(
                'oid' => $oid
            ));
    }

    public function getFilter() {
        $em = $this->getDoctrine()->getManager();

        if( $this->get('security.context')->isGranted('ROLE_PROCESSOR') ) {
            $statuses = $em->getRepository('OlegOrderformBundle:Status')->findAll();
        } else {
            $repository = $this->getDoctrine()->getRepository('OlegOrderformBundle:Status');
            $dql = $repository->createQueryBuilder("status");
            //$dql->where('status.action IS NOT NULL');
            $dql->where("status.name != 'Superseded'");
            $statuses = $dql->getQuery()->getResult();
        }

        //add special cases statuses
        $specials = array(
            "All" => "All Statuses",
            "All Not Filled" => "All Not Filled",
            "All On Hold" => "All On Hold",
            "All Stat" => "All Stat",
            //All Canceled here
            "All Submitted & Amended" => "All Submitted & Amended",
            "All Filled" => "All Filled",
            "All Filled & Not Returned" => "All Filled & Not Returned",
            "All Filled & Returned" => "All Filled & Returned",
            "With New Comments" => "With New Comments",
            "With Comments" => "With Comments",
            "Stat & Not Filled" => "Stat & Not Filled",
            "Stat & Filled" => "Stat & Filled"
        );

        $filterType = array();
        foreach( $specials as $key => $value ) {
            $filterType[$key] = $value;
            if( $value == "All Stat" ) {
                $filterType["All Canceled"] = "All Canceled";   //add after Not Submitted
            }
        }

        //add statuses from DB
        foreach( $statuses as $status ) {
            //echo "type: id=".$status->getId().", name=".$status->getName()."<br>";
            $filterType[$status->getId()] = $status->getName();
        }

        return $filterType;
    }
    
    
    public function getServiceFilter() {
        $em = $this->getDoctrine()->getManager();

        if( $this->get('security.context')->isGranted('ROLE_PROCESSOR') ) {
            $statuses = $em->getRepository('OlegOrderformBundle:PathServiceList')->findAll();
        } 

        //add special cases
        $specials = array(
            "All" => "All Services",          
        );

        $filterType = array();
        foreach( $specials as $key => $value ) {
            $filterType[$key] = $value;
        }

        //add statuses
        foreach( $statuses as $status ) {
            //echo "type: id=".$status->getId().", name=".$status->getName()."<br>";
            $filterType[$status->getId()] = $status->getName();           
        }

        return $filterType;
    }

    //Pathology Service filetr
    public function allServiceFilter( $service, $routeName, $user, $criterions ) {

        $criteriastr = "";
        $em = $this->getDoctrine()->getManager();

        if( $this->get('security.context')->isGranted('ROLE_DIVISION_CHIEF') ) {
            return $criteriastr;
        }

        //for "My Orders" get all user services and chief services
        if( $routeName == "my-scan-orders" ) {

            $services = array();
            $userServices = $user->getPathologyServices();

            if( $this->get('security.context')->isGranted('ROLE_SERVICE_CHIEF') ) {
                $chiefServices = $user->getChiefservices();
                if( $userServices && count($userServices)>0 ) {
                    $services = array_merge($userServices, $chiefServices);
                } else {
                    $services = $chiefServices;
                }
            }

            foreach( $services as $service ) {
                if( $service && $service != "" ) {
                    if( $criteriastr != "" ) {
                        $criteriastr .= " OR ";
                    }
                    $criteriastr .= " orderinfo.pathologyService=".$service->getId();
                }
            }//foreach

        }

        //for "Incoming Orders" select only chosen service
        if( $routeName == "incoming-scan-orders" ) {

            if( is_numeric($service)  ) {

                $pathService = $em->getRepository('OlegOrderformBundle:PathServiceList')->find($service);

                if( !$pathService ) {
                    throw new \Exception( 'Unable to find Service '.$service );
                }

                $criteriastr = " orderinfo.pathologyService=".$pathService->getId();

            }

        }

        if( $criterions != "" ) {
            $criteriastr = " OR (" . $criteriastr . ") ";
        }

        return $criteriastr;
    }

}
