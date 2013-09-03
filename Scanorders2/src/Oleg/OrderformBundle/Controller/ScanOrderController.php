<?php

namespace Oleg\OrderformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
//use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Oleg\OrderformBundle\Entity\OrderInfo;
use Oleg\OrderformBundle\Form\OrderInfoType;
use Oleg\OrderformBundle\Entity\Patient;
use Oleg\OrderformBundle\Form\PatientType;
use Oleg\OrderformBundle\Entity\Specimen;
use Oleg\OrderformBundle\Form\SpecimenType;
use Oleg\OrderformBundle\Entity\Accession;
use Oleg\OrderformBundle\Form\AccessionType;
use Oleg\OrderformBundle\Entity\Part;
use Oleg\OrderformBundle\Form\PartType;
use Oleg\OrderformBundle\Entity\Block;
use Oleg\OrderformBundle\Form\BlockType;
use Oleg\OrderformBundle\Entity\Slide;
use Oleg\OrderformBundle\Form\SlideType;
use Oleg\OrderformBundle\Entity\Scan;
use Oleg\OrderformBundle\Form\ScanType;
use Oleg\OrderformBundle\Entity\Stain;
use Oleg\OrderformBundle\Form\StainType;
use Oleg\OrderformBundle\Form\FilterType;

use Oleg\OrderformBundle\Helper\ErrorHelper;
use Oleg\OrderformBundle\Helper\FormHelper;
use Oleg\OrderformBundle\Helper\EmailUtil;

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
     * @Method("GET")
     * @Template()
     */
    public function indexAction( Request $request ) {
        
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            //throw new AccessDeniedException();
            return $this->render('OlegOrderformBundle:Security:login.html.twig');
        }
        
        $em = $this->getDoctrine()->getManager();
        
              
        $form = $this->createForm(new FilterType(), null);         
        $form->bind($request);

        if( $this->get('request')->request->get('search') ) {
            
        }
        
        $repository = $this->getDoctrine()->getRepository('OlegOrderformBundle:OrderInfo');

        //by user
        $user = $this->get('security.context')->getToken()->getUser();
          
        $criteria = array();
        if( false === $this->get('security.context')->isGranted('ROLE_ADMIN') ) {    
            $criteria['provider'] = $user;
        }            
        
        $search = $form->get('search')->getData();
        $filter = $form->get('filter')->getData();
        $service = $form->get('service')->getData();

        //filter           
        if( $filter && $filter != 'all'  ) {     
            $criteria['status']= $filter;
        }

        //service
        //echo "service=".$service;
        //exit();
        $showprovider = 'false';
        if( $service && $service == 1  ) {
            $helper = new FormHelper();
            $email = $this->get('security.context')->getToken()->getAttribute('email');
            $userService = $helper->getUserPathology($email);
            if( $userService && $userService != ''  ) {
                $criteria['pathologyService']= trim($userService);
            }
            $showprovider = 'true';
        }

//        $pre_query = $repository->createQueryBuilder('order')
//                    ->orderBy('or.orderdate', 'DESC');
//        $query = $pre_query->getQuery();     
//        $entities = $query->getResult();
//        //findAll();
//        $limit = 10;
//        $num_pages = 1; // some calculation of what page you're currently on
//        $entities = $em->getRepository('OlegOrderformBundle:OrderInfo')->
//                    findBy(
//                            $criteria,
//                            array('orderdate'=>'desc'),
//                            $limit, // limit (doctrine)
//                            $limit * ($num_pages - 1) // offset (doctrine)
//                    );

        $orderby = "";

        $params = $this->getRequest()->query->all();
        $sort = $this->getRequest()->query->get('sort');

        if( $params == null || count($params) == 0 ) {
            $orderby = " ORDER BY orderinfo.id DESC";
        }

        if( $sort != 'orderinfo.id' ) {
            $orderby = " ORDER BY orderinfo.id DESC";
        }

        $criteriastr = "";
        //print_r($criteria);

        $count = 0;
        foreach( $criteria as $key => $value ){
            $criteriastr .= "orderinfo." . $key . "='" . $value . "'";
            if( count($criteria) > $count+1 ) {
                $criteriastr .= " AND ";
            }
            $count++;
        }
      
        //paginator
        //, COUNT(orderinfo.slide) as slides
//        $limit = 15;
        //$dql1 = "SELECT orderinfo FROM OlegOrderformBundle:OrderInfo orderinfo ".$criteriastr.$orderby;
        
//        $dql = "SELECT orderinfo FROM "
//                . "OlegOrderformBundle:OrderInfo orderinfo "
//                . "LEFT JOIN OlegOrderformBundle:Slide slide "
//                . "ON slide.orderinfo = orderinfo.id"
//                //. "OlegOrderformBundle:Accession accession "           
//                . $criteriastr.$orderby;
        
        $dql =  $repository->createQueryBuilder("orderinfo");
        
        //echo "<br>criteriastr=".$criteriastr."<br>";
        
//        if( $criteriastr != "" ) {
//            $dql->where($criteriastr);
//        }
        $criteriafull = "";
        if( $search && $search != '' ) {
//            $dql->innerJoin("orderinfo.slide", "slide");
//            $dql->innerJoin("slide.accession", "accession");           
//            $criteriafull = "slide.orderinfo = orderinfo.id AND slide.accession = accession.id AND accession.accession LIKE '%". 
//                           $search ."%'";    
                                  
            $dql->innerJoin("orderinfo.accession", "accession");           
            $criteriafull = "accession.accession LIKE '%" . $search . "%'";    
            
        }              
        
        if( $criteriastr != "" ) {
            if( $criteriafull != "" ) {
                $criteriafull .= " AND ";
            }
            $criteriafull .= $criteriastr;           
        } 
        
        if( $criteriafull != "" ) {
            $dql->where($criteriafull);
        }
        
        if( $orderby != "" ) {
            $dql->orderBy("orderinfo.id","DESC");
        }
        
        //echo "dql=".$dql;
        
        $limit = 15;
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1), /*page number*/
            $limit/*limit per page*/
        );

        //$slides = $em->getRepository('OlegOrderformBundle:Slide')->findAll();
        
        //check for active user requests
        $reqs = array();
        if( $this->get('security.context')->isGranted('ROLE_ADMIN') ) {                     
            $reqs = $em->getRepository('OlegOrderformBundle:UserRequest')->findByStatus("active");
        }
        
        return array(
            //'entities' => $entities,
            'form' => $form->createView(),
            'showprovider' => $showprovider,
            'pagination' => $pagination,
            'userreqs' => $reqs
        );
    }
    
    /**
     * Creates a new OrderInfo entity.
     *
     * @Route("/", name="singleorder_create")
     * @Method("POST")
     * @Template("OlegOrderformBundle:ScanOrder:new.html.twig")
     */
    public function createAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('OlegOrderformBundle:Security:login.html.twig');
        }
        
        //echo "scanorder createAction";
        $entity  = new OrderInfo();
        $form = $this->createForm(new OrderInfoType(null, null, $entity), $entity);
        $form->bind($request);
              
        $patient = new Patient();      
        $form_patient = $this->createForm(new PatientType(), $patient);
        $form_patient->bind($request);
        
        $procedure = new Specimen();
        $form_procedure = $this->createForm(new SpecimenType(), $procedure);
        $form_procedure->bind($request);
        
        $accession = new Accession();
        $form_accession = $this->createForm(new AccessionType(), $accession);
        $form_accession->bind($request);
        
        $part = new Part();
        $form_part = $this->createForm(new PartType(), $part);
        $form_part->bind($request);
        
        $block = new Block();
        $form_block = $this->createForm(new BlockType(), $block);
        $form_block->bind($request);

        $slide = new Slide();
        $form_slide = $this->createForm(new SlideType(), $slide);
        $form_slide->bind($request);

        $scan = new Scan();
        $form_scan = $this->createForm(new ScanType(), $scan);
        $form_scan->bind($request);

        $stain = new Stain();
        $form_stain = $this->createForm(new StainType(), $stain);
        $form_stain->bind($request);
        
        if(0) {
            $errorHelper = new ErrorHelper();
            $errors = $errorHelper->getErrorMessages($form);
            echo "<br>form errors:<br>";
            print_r($errors); 
            $errors = $errorHelper->getErrorMessages($form_patient);
            echo "<br>patient errors:<br>";
            print_r($errors); 
            $errors = $errorHelper->getErrorMessages($form_procedure);
            echo "<br>procedure errors:<br>";
            print_r($errors); 
            $errors = $errorHelper->getErrorMessages($form_accession);
            echo "<br>accession errors:<br>";
            print_r($errors);
            $errors = $errorHelper->getErrorMessages($form_part);
            echo "<br>part errors:<br>";
            print_r($errors);
            $errors = $errorHelper->getErrorMessages($form_block);
            echo "<br>block errors:<br>";
            print_r($errors);
            $errors = $errorHelper->getErrorMessages($form_slide);
            echo "<br>slide errors:<br>";
            print_r($errors);
            $errors = $errorHelper->getErrorMessages($form_scan);
            echo "<br>scan errors:<br>";
            print_r($errors);
            $errors = $errorHelper->getErrorMessages($form_stain);
            echo "<br>stain errors:<br>";
            print_r($errors);

        }
//            
//        echo "<br>stain type=".$slide->getStain()->getName()."<br>";
//        echo "scan mag=".$slide->getScan()->getMag()."<br>";        
        
        
        if( $form->isValid() && 
            $form_procedure->isValid() &&
            $form_accession->isValid() &&
            $form_part->isValid() &&    
            $form_block->isValid() &&
            $form_slide->isValid() &&
            $form_scan->isValid() &&
            $form_stain->isValid()
        ) {
            $em = $this->getDoctrine()->getManager();                            
                        
            $entity = $em->getRepository('OlegOrderformBundle:OrderInfo')->processEntity( $entity, "single" );
            
            //procedure/specimen: none
            //$procedure->addProcedure($accession);
            $patient = $em->getRepository('OlegOrderformBundle:Patient')->processEntity( $patient );                       
            $entity->addPatient($patient);
            //$em->persist($entity);          
            //$em->flush();
            
            $procedure = $em->getRepository('OlegOrderformBundle:Specimen')->processEntity( $procedure, array($accession) );
            $patient->addSpecimen($procedure);
            $entity->addSpecimen($procedure);
            //$em->persist($patient); 
            //$em->persist($procedure);
            //$em->flush();

            $accession = $em->getRepository('OlegOrderformBundle:Accession')->processEntity( $accession );
            $procedure->addAccession($accession);
            $entity->addAccession($accession);
            //$em->persist($accession);          
            //$em->flush();
            
            $part = $em->getRepository('OlegOrderformBundle:Part')->processEntity( $part, $accession );
            $accession->addPart($part);
            $entity->addPart($part);
            //$em->persist($part);          
            //$em->flush();
            
            $block = $em->getRepository('OlegOrderformBundle:Block')->processEntity( $block, $part );
            $part->addBlock($block);
            $entity->addBlock($block);
            //$em->persist($block);          
            //$em->flush();
            
            $slide = $em->getRepository('OlegOrderformBundle:Slide')->processEntity( $slide );
            //$em->getRepository('OlegOrderformBundle:Stain')->processEntity( $slide->getStain() );
            //$em->getRepository('OlegOrderformBundle:Scan')->processEntity( $slide->getScan() );
            //$accession->addSlide($slide); 
            //$part->addSlide($slide);
            $block->addSlide($slide);  
            $entity->addSlide($slide);

            $scan = $em->getRepository('OlegOrderformBundle:Scan')->processEntity( $scan );
            $slide->addScan($scan);
            $entity->addScan($scan);

            $stain = $em->getRepository('OlegOrderformBundle:Stain')->processEntity( $stain );
            $slide->addStain($stain);
            $entity->addStain($stain);

            $name = $form_stain["name"]->getData();
            
            echo "stain name=".$name."<br>";
                      
            print_r($request);
            
            echo $stain;
            
//            echo $entity;
//            echo $procedure;
//            echo "orderinfo proc count=".count($procedure->getOrderInfo())."<br>";
//            echo "proc count=".count($entity->getSpecimen())."<br>";
//            echo "orderinfo part count=".count($part->getOrderInfo())."<br>";
//            echo "part count=".count($entity->getPart())."<br>";
            exit();

            $em->persist($entity);
            $em->flush();

            
            $email = $this->get('security.context')->getToken()->getAttribute('email');

            $emailUtil = new EmailUtil();
            $emailUtil->sendEmail( $email, $entity, null );
                      
            return $this->render('OlegOrderformBundle:ScanOrder:thanks.html.twig', array(
                'orderid' => $entity->getId(),
            ));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'form_patient'   => $form_patient->createView(),
            'form_procedure'   => $form_procedure->createView(),
            'form_accession'   => $form_accession->createView(),
            'form_part'   => $form_part->createView(),
            'form_block'   => $form_block->createView(),
            'form_slide'   => $form_slide->createView(),
            'form_stain'   => $form_stain->createView(),
            'form_scan'   => $form_scan->createView(),
        );
    }
    
    /**
     * Displays a form to create a new OrderInfo + Scan entities.
     *
     * @Route("/", name="scanorder_new")
     * @Method("GET")
     * @Template("OlegOrderformBundle:ScanOrder:new.html.twig")
     */
    public function newAction()
    {            
        
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('OlegOrderformBundle:Security:login.html.twig');
        }
        
        $entity = new OrderInfo();
        $username = $this->get('security.context')->getToken()->getUser();
        $entity->setProvider($username);

        //get pathology service for this user by email
        $helper = new FormHelper();
        $email = $this->get('security.context')->getToken()->getAttribute('email');
        $service = $helper->getUserPathology($email);
//        if( $service ) {
//            $services = explode("/", $service);
//            $service = $services[0];
//        }

        //echo "service=".$service."<br>";
        $entity->setPathologyService($service);

        $form   = $this->createForm( new OrderInfoType("multy",$service, $entity), $entity );

        $patient = new Patient();      
        $form_patient   = $this->createForm(new PatientType(), $patient);
        
        $procedure = new Specimen();  //TODO: rename specimen to procedure    
        $form_procedure = $this->createForm(new SpecimenType(), $procedure);
        
        $accession = new Accession();      
        $form_accession   = $this->createForm(new AccessionType(), $accession);
         
        $part = new Part();      
        $form_part   = $this->createForm(new PartType(), $part);
            
        $block = new Block();      
        $form_block   = $this->createForm(new BlockType(), $block);
        
        $slide = new Slide();      
        $form_slide   = $this->createForm(new SlideType(), $slide);

        $scan = new Scan();
        $form_scan   = $this->createForm(new ScanType(), $scan);

        $stain = new Stain();
        $form_stain   = $this->createForm(new StainType(), $stain);
        
        return array(          
            'form' => $form->createView(),
            'form_patient' => $form_patient->createView(),
            'form_procedure' => $form_procedure->createView(),
            'form_accession' => $form_accession->createView(),
            'form_part' => $form_part->createView(),
            'form_block' => $form_block->createView(),
            'form_slide' => $form_slide->createView(),
            'form_scan' => $form_scan->createView(),
            'form_stain' => $form_stain->createView(),
        );
    }

    /**
     * Finds and displays a OrderInfo entity.
     *
     * @Route("/{id}", name="scanorder_show", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template("OlegOrderformBundle:ScanOrder:show.html.twig")
     */
    public function showAction($id)
    {
        
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('OlegOrderformBundle:Security:login.html.twig');
        }
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegOrderformBundle:OrderInfo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderInfo entity.');
        }

        $showForm = $this->createForm(new OrderInfoType(null, null, $entity), $entity, array('disabled' => true));
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $showForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing OrderInfo entity.
     *
     * @Route("/{id}/edit", name="scanorder_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('OlegOrderformBundle:Security:login.html.twig');
        }
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegOrderformBundle:OrderInfo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderInfo entity.');
        }

        $editForm = $this->createForm(new OrderInfoType(null, null, $entity), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing OrderInfo entity.
     *
     * @Route("/{id}", name="scanorder_update")
     * @Method("PUT")
     * @Template("OlegOrderformBundle:OrderInfo:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('OlegOrderformBundle:Security:login.html.twig');
        }
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegOrderformBundle:OrderInfo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderInfo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new OrderInfoType(null, null, $entity), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('scanorder_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a OrderInfo entity.
     *
     * @Route("/{id}", name="scanorder_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('OlegOrderformBundle:Security:login.html.twig');
        }
        
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OlegOrderformBundle:OrderInfo')->find($id);

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
     * @Route("/{id}/{status}/status", name="scanorder_status")
     * @Method("GET")
     * @Template()
     */
    public function statusAction($id, $status)
    {
        
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('OlegOrderformBundle:Security:login.html.twig');
        }
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegOrderformBundle:OrderInfo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderInfo entity.');
        }

        //check if user permission
        
        //$editForm = $this->createForm(new OrderInfoType(), $entity);
        //$deleteForm = $this->createDeleteForm($id);
        
        $entity->setStatus($status);
        $em->persist($entity);
        $em->flush();
        
        
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
    public function thanksAction( $orderid = '' )
    {    
        
        return $this->render('OlegOrderformBundle:ScanOrder:thanks.html.twig',
            array(
                'orderid' => $orderid            
            ));
    }
    
}
