<?php

namespace Oleg\OrderformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oleg\OrderformBundle\Entity\OrderInfo;
use Oleg\OrderformBundle\Form\OrderInfoType;

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
     * @Route("/index", name="index")
     * @Route("/admin/index", name="adminindex")
     * @Method("GET")
     * @Template()
     */
    public function indexAction( Request $request ) {

        $em = $this->getDoctrine()->getManager();

        $routeName = $request->get('_route');
        //echo "routeName=".$routeName."<br>";

        //by user
        $user = $this->get('security.context')->getToken()->getUser();

        if( $routeName == "adminindex" ) {
            $services = $this->getServiceFilter();
        } else {
            $services = null;
        }
        
        //create filters
        $form = $this->createForm(new FilterType( $this->getFilter(), $user, $services ), null);
        $form->bind($request);

        $repository = $this->getDoctrine()->getRepository('OlegOrderformBundle:OrderInfo');
        $dql =  $repository->createQueryBuilder("orderinfo");
        //$dql->addSelect('orderinfo');
        //$dql->addSelect('COUNT(slides) as slidecount');
        //$dql->addGroupBy('orderinfo');
        $dql->select('orderinfo, COUNT(slides) as slidecount');
        $dql->groupBy('orderinfo');
        $dql->addGroupBy('orderinfo');
        $dql->addGroupBy('status.name');
        $dql->addGroupBy('formtype.name');
        $dql->addGroupBy('provider.username');

        $dql->innerJoin("orderinfo.slide", "slides");
        $dql->innerJoin("orderinfo.provider", "provider");
        $dql->innerJoin("orderinfo.type", "formtype");

        $search = $form->get('search')->getData();
        $filter = $form->get('filter')->getData();
        $service = $form->get('service')->getData();

        //service
        //echo "<br>service=".$service;
        //exit();

        $criteriastr = "";

        //***************** Pathology Service filetr ***************************//
        $showprovider = 'false';

        //***************** Service filter ***************************//
        //if( $routeName == "adminindex" ) {
        if( is_numeric($service)  ) {

            $userService = $user->getPathologyServices();

            if( !$userService ) {
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'You are not assign to any pathology service; All orders are shown.'
                );
            }

            $pathService = $em->getRepository('OlegOrderformBundle:PathServiceList')->find($service);

            if( $userService && $userService != ''  ) {
                if( $criteriastr != "" ) {
                    $criteriastr .= " AND ";
                }
                $criteriastr .= " orderinfo.pathologyService=".$pathService->getId();
            }
            $showprovider = 'true';
        } else {
            //this implemented below in "User filter"
        }
        //***************** END of Pathology Service filetr ***************************//


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

                case "All Filled":
                    $criteriastr .= " status.name LIKE '%Filled%'";
                    break;
                case "All Filled and Returned":
                    $criteriastr .= " status.name LIKE '%Filled%' AND status.name LIKE '%Returned%'";
                    break;
                case "All Filled and Not Returned":
                    $criteriastr .= " status.name LIKE '%Filled%' AND status.name NOT LIKE '%Returned%'";
                    break;
                case "All Not Filled":
                    $criteriastr .= " status.name NOT LIKE '%Filled%'";
                    break;
                case "All On Hold":
                    $criteriastr .= " status.name LIKE '%On Hold%'";
                    break;
                case "Canceled (All)":
                    $criteriastr .= " status.name = 'Canceled by Submitter' OR status.name = 'Canceled by Processor'";
                    break;
                default:
                    ;
            }

        }
        //***************** END of Status filetr ***************************//

        //***************** Superseded filter ***************************//
        if( false === $this->get('security.context')->isGranted('ROLE_PROCESSOR') ) {
            //$superseded_status = $em->getRepository('OlegOrderformBundle:Status')->findOneByName('Superseded');
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


        if( $routeName == "index" ) {
            //***************** User filter ***************************//
            //TODO: test leftJoin. innerJoin does not show orders without proxyuser link
            $dql->leftJoin("orderinfo.proxyuser", "proxyuser");
            //show only my order if i'm not an admin and Pathology Services are not choosen
            if( false === $this->get('security.context')->isGranted('ROLE_PROCESSOR') && $service == 0 ) {
                if( $criteriastr != "" ) {
                    $criteriastr .= " AND ";
                }
                $criteriastr .= "( provider.id=".$user->getId();

                //***************** Proxy User Orders *************************//
                $criteriastr .= " OR proxyuser.id=".$user->getId();
                //***************** END of Proxy User Orders *************************//

                $criteriastr .= " )";
            }

            if( $service == "My Orders" ) {
                //show only my order if i'm not an admin and Pathology Services are not choosen
                //Orders I Personally Placed and Proxy Orders Placed For Me
                if( $service == 0 &&
                    false === $this->get('security.context')->isGranted('ROLE_PROCESSOR')

                ) {
                    if( $criteriastr != "" ) {
                        $criteriastr .= " AND ";
                    }
                    $criteriastr .= "( provider.id=".$user->getId();

                    //***************** Proxy User Orders *************************//
                    $criteriastr .= " OR proxyuser.id=".$user->getId();
                    //***************** END of Proxy User Orders *************************//

                    $criteriastr .= " )";
                }
            }
            if( $service == "Orders I Personally Placed" ) {
                if( false === $this->get('security.context')->isGranted('ROLE_PROCESSOR') && $service == 0 ) {
                    if( $criteriastr != "" ) {
                        $criteriastr .= " AND ";
                    }
                    $criteriastr .= "provider.id=".$user->getId();
                }
            }
            if( $service == "Proxy Orders Placed For Me" ) {
                if( false === $this->get('security.context')->isGranted('ROLE_PROCESSOR') && $service == 0 ) {
                    if( $criteriastr != "" ) {
                        $criteriastr .= " AND ";
                    }
                    //***************** Proxy User Orders *************************//
                    $criteriastr .= "proxyuser.id=".$user->getId();
                    //***************** END of Proxy User Orders *************************//
                }
            }
            //***************** END of User filetr ***************************//
        }
        
        if( $routeName == "adminindex" ) {
            //echo "admin index filter <br>";
            //***************** Service filter ***************************//
            
            //***************** End of Service filter ***************************//
        }

        //echo "<br>criteriastr=".$criteriastr."<br>";
        
        if( $criteriastr != "" ) {
            //TODO: use ->setParameter(1, $caravan);
            $dql->where($criteriastr);
        }

        $params = $this->getRequest()->query->all();
        $sort = $this->getRequest()->query->get('sort');
        
        if( $routeName == "index" ) {          
            if( $params == null || count($params) == 0 ) {
                $dql->orderBy("orderinfo.orderdate","DESC");
            }
            if( $sort != 'orderinfo.oid' ) {
                $dql->orderBy("orderinfo.orderdate","DESC");
            }
        }
               
        if( $routeName == "adminindex" ) {
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
        $reqs = array();
        if( $this->get('security.context')->isGranted('ROLE_PROCESSOR') ) {
            $reqs = $em->getRepository('OlegOrderformBundle:UserRequest')->findByStatus("active");
        }

        //check for active access requests
        $accessreqs = array();
        if( $this->get('security.context')->isGranted('ROLE_PROCESSOR') ) {
            $accessreqs = $em->getRepository('OlegOrderformBundle:User')->findByAppliedforaccess('active');
        }
        
        return array(
            'form' => $form->createView(),
            'showprovider' => $showprovider,
            'pagination' => $pagination,
            'userreqs' => $reqs,
            'accessreqs' => $accessreqs,
            'routename'=>$routeName
        );
    }


    //requirements={"id" = "\d+"}
    /**
     * Deletes a OrderInfo entity.
     *
     * @Route("/{id}", name="scanorder_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {

        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect( $this->generateUrl('logout') );
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
     * @Route("/{id}/{status}/status", name="scanorder_status")
     * @Method("GET")
     * @Template()
     */
    public function statusAction($id, $status) {

        if( false === $this->get('security.context')->isGranted('ROLE_SUBMITTER') &&
            false === $this->get('security.context')->isGranted('ROLE_EXTERNAL_SUBMITTER')
        ) {
            return $this->redirect( $this->generateUrl('logout') );
        }
        
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();

        $orderUtil = new OrderUtil($em);

        $res = $orderUtil->changeStatus($id, $status, $user);

        if( $res['result'] == 'conflict' ) {   //redirect to amend
            return $this->redirect( $this->generateUrl( 'order_amend', array('id' => $res['oid']) ) );
        }

        $this->get('session')->getFlashBag()->add('notice',$res['message']);

        return $this->redirect($this->generateUrl('index'));
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

        //add special cases
        $specials = array(
            "All" => "All Statuses",
            "All Filled" => "All Filled",
            "All Filled and Returned" => "All Filled and Returned",
            "All Filled and Not Returned" => "All Filled and Not Returned",
            "All Not Filled" => "All Not Filled",
            "All On Hold" => "All On Hold"
        );

        $filterType = array();
        foreach( $specials as $key => $value ) {
            $filterType[$key] = $value;
        }

        //add statuses
        foreach( $statuses as $status ) {
            //echo "type: id=".$status->getId().", name=".$status->getName()."<br>";
            $filterType[$status->getId()] = $status->getName();
            if( $status->getName() == "Not Submitted" ) {
                $filterType["Canceled (All)"] = "Canceled (All)";
            }
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

}
