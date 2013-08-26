<?php

namespace Oleg\OrderformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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

use Oleg\OrderformBundle\Entity\Educational;
use Oleg\OrderformBundle\Form\EducationalType;
use Oleg\OrderformBundle\Entity\Research;
use Oleg\OrderformBundle\Form\ResearchType;

use Oleg\OrderformBundle\Form\SlideMultiType;

use Oleg\OrderformBundle\Helper\ErrorHelper;
use Oleg\OrderformBundle\Helper\FormHelper;
use Oleg\OrderformBundle\Helper\EmailUtil;

//ScanOrder joins OrderInfo + Scan
/**
 * OrderInfo controller.
 *
 * @Route("/multi")
 */
class MultyScanOrderController extends Controller {
   
    /**
     * Lists all OrderInfo entities.
     *
     * @Route("/index", name="multyIndex")
     * @Method("GET")
     * @Template("OlegOrderformBundle:MultyScanOrder:index.html.twig")
     */
    public function multyIndexAction() {
        
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            //throw new AccessDeniedException();
            return $this->render('OlegOrderformBundle:Security:login.html.twig');
        }
        
        $em = $this->getDoctrine()->getManager();
        
        //findAll();
        $entities = $em->getRepository('OlegOrderformBundle:OrderInfo')->                   
                    findBy(array(), array('orderdate'=>'desc')); 
       
        //$slides = $em->getRepository('OlegOrderformBundle:Slide')->findAll();
        
        return array(
            'entities' => $entities,  
            //'slides' => $slides
        );
    }

    /**
     * Creates a new OrderInfo entity.
     *
     * @Route("/research/new", name="res_create")
     * @Route("/educational/new", name="edu_create")
     * @Route("/clinical/new", name="clinical_create")
     * @Method("POST")
     * @Template("OlegOrderformBundle:MultyScanOrder:new.html.twig")
     */
    public function multyCreateAction(Request $request)
    { 
        
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('OlegOrderformBundle:Security:login.html.twig'

            );
        }
        
        //echo " controller multy<br>";
        //exit();

        $entity  = new OrderInfo();

        $request = $this->container->get('request');
        $routeName = $request->get('_route');
        //echo "routeName=".$routeName;

        $type = "clinical";

        if( $routeName == "clinical_create") {
            $type = "clinical";
            //$entity->setEducational(null);
            //$entity->setResearch(null);
        }

        if( $routeName == "edu_create") {
            $type = "educational";
            //$entity->setResearch(null);
        }

        if( $routeName == "res_create") {
            $type = "research";
            //$entity->setEducational(null);
        }

        $form = $this->createForm(new OrderInfoType($type), $entity);
        $form->bind($request);

        if(0) {
            $errorHelper = new ErrorHelper();
            $errors = $errorHelper->getErrorMessages($form);
            //echo "<br>form errors:<br>";
            //print_r($errors);
        }
        
        //echo "Before validation main entity:<br>";

//        if( $form->isValid() ) {
        if( 1 ) {

            $em = $this->getDoctrine()->getManager();                            
                       
            $entity = $em->getRepository('OlegOrderformBundle:OrderInfo')->processEntity( $entity, $type );

//            echo "<br>Before loop:<br>";
//            echo $entity;
//            exit();
            
            //Patient
            //$pat_count = 0;
            foreach( $entity->getPatient() as $patient ) {
                if( !$patient->getId() ) {
                    //echo " pat id null <br>";
                    $entity->removePatient( $patient );
                    $patient = $em->getRepository('OlegOrderformBundle:Patient')->processEntity( $patient );
                    $entity->addPatient($patient);
                } else {
                    continue;
                }
                //echo $pat_count++." !!!!!!!!!!patient = ". $patient. "<br>";
                
                //Procedure
                $specimen_count = 0;
                foreach( $patient->getSpecimen() as $specimen ) {
                    if( !$specimen->getId() ) {
                        //echo " specimen id null <br>";
                        $patient->removeSpecimen( $specimen );
                        $specimen = $em->getRepository('OlegOrderformBundle:Specimen')->processEntity( $specimen, $specimen->getAccession() );
                        $patient->addSpecimen($specimen);
                        $entity->addSpecimen($specimen);
                    } else {
                        continue;
                    }
                    //echo $specimen_count++." !!!!!!!!!!specimen = ". $specimen. "<br>";
                    
                    //Accession
                    //$acc_count = 0;
                    foreach( $specimen->getAccession() as $accession ) {
                        if( !$accession->getId() ) {
                            $specimen->removeAccession( $accession );
                            $accession = $em->getRepository('OlegOrderformBundle:Accession')->processEntity( $accession );
                            $specimen->addAccession($accession);
                            $entity->addAccession($accession);
                        } else {
                            continue;
                        }
                        //echo $acc_count++." !!!!!!!!!!acc = ". $accession. "<br>";

                        //Part
                        //$part_count = 0;
                        foreach( $accession->getPart() as $part ) {
                            if( !$part->getId() ) {
                                $accession->removePart( $part );
                                $part = $em->getRepository('OlegOrderformBundle:Part')->processEntity( $part, $accession );
                                $accession->addPart($part);
                                $entity->addPart($part);
                            } else {
                                continue;
                            }
                            //echo $part_count++." !!!!!!!!!!part = ". $part. "<br>";
                            //Block
                            //$count=0;
                            foreach( $part->getBlock() as $block ) {
                                if( !$block->getId() ) {
                                    //echo " !!!!!!!!!! block0 = ". $block. "<br>";
                                    $part->removeBlock( $block );
                                    $block = $em->getRepository('OlegOrderformBundle:Block')->processEntity( $block, $part );
                                    $part->addBlock($block);
                                    $entity->addBlock($block);
                                    //echo " !!!!!!!!!! block1 = ". $block. "<br>";
                                } else {
                                    continue;
                                }
                                //echo $count++." !!!!!!!!!!block = ". $block. "<br>";
                                //Slide
                                foreach( $block->getSlide() as $slide ) {
                                    //echo "!!!!!!!!!!slide = ". $slide. "<br>";
                                    if( !$slide->getId() ) {
                                        $block->removeSlide( $slide );
                                        $slide = $em->getRepository('OlegOrderformBundle:Slide')->processEntity( $slide );
                                        $em->getRepository('OlegOrderformBundle:Stain')->processEntity( $slide->getStain() );
                                        $em->getRepository('OlegOrderformBundle:Scan')->processEntity( $slide->getScan() ); 
                                        
                                        //$accession->addSlide($slide);  
                                        //$part->addSlide($slide);  
                                        $block->addSlide($slide);                                                                                                                             
                                        $entity->addSlide($slide);                                                                           
                                    } else {
                                        continue;
                                    }
                                } //slide

                            } //block

                        } //part

                    } //accession

                } //procedure

            } //patient

            //echo "<br>End of loop<br>";
            //echo $entity;
            //exit();

            $em->persist($entity);
            $em->flush();

            //email
            $email = $this->get('security.context')->getToken()->getAttribute('email');

            $thanks_txt = "<p><h1>Thank You For Your Order !</h1></p>
                <p><h3>Order #".$entity->getId()." Successfully Submitted.</h3></p>
                <p><h3>Confirmation Email was sent to ".$email."</h3></p>";

            $emailUtil = new EmailUtil();
            
            //$emailUtil->sendEmail( $email, $entity, $thanks_txt );
            
            if( 0 ) {              
                ini_set( 'sendmail_from', "slidescan@med.cornell.edu" ); //My usual e-mail address
                ini_set( "SMTP", "smtp.med.cornell.edu" );  //My usual sender
                //ini_set( 'smtp_port', 25 );

                $thanks_txt =
                    "Thank You For Your Order !\r\n"
                    . "Order #" . $entity->getId() . " Successfully Submitted.\r\n"
                    . "Confirmation Email was sent to " . $email . "\r\n";

                $message = $thanks_txt;
                // In case any of our lines are larger than 70 characters, we should use wordwrap()
                $message = wordwrap($message, 70, "\r\n");
                // Send
                mail($email, 'Scan Order Confirmation', $message);
            }


//            $this->get('session')->getFlashBag()->add(
//                'notice',
//                'You successfully submit a scan request! Confirmation email sent!'
//            );
//            return $this->redirect( $this->generateUrl('clinical_new') );

            return $this->render('OlegOrderformBundle:ScanOrder:thanks.html.twig', array(
                'orderid' => $entity->getId(),
            ));

        }
        
        
        return array(           
            'form'   => $form->createView(),
            'type' => 'new'
        );    
    }    
    
    /**
     * Displays a form to create a new OrderInfo + Scan entities.
     *
     * @Route("/research/new", name="res_new")
     * @Route("/educational/new", name="edu_new")
     * @Route("/clinical/new", name="clinical_new")
     * @Method("GET")
     * @Template("OlegOrderformBundle:MultyScanOrder:new.html.twig")
     */
    public function newMultyAction()
    {

        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('OlegOrderformBundle:Security:login.html.twig');
        }

        $entity = new OrderInfo();
        $username = $this->get('security.context')->getToken()->getUser();
        $entity->setProvider($username);

        $patient = new Patient();
        $entity->addPatient($patient);

        //$patient2 = new Patient();
        //$entity->addPatient($patient2);

        $procedure = new Specimen();
        $patient->addSpeciman($procedure);

        //$procedure2 = new Specimen();
        //$patient->addSpeciman($procedure2);

        $accession = new Accession();
        $procedure->addAccession($accession);

        $part = new Part();
        $accession->addPart($part);

        $block = new Block();
        $part->addBlock($block);

        $slide = new Slide();
        $block->addSlide($slide);


        $request = $this->container->get('request');
        $routeName = $request->get('_route');
        //echo "routeName=".$routeName;
        $type = "clinical";
        if( $routeName == "edu_new") {
            //echo " add edu ";
            $type = "educational";
            $edu = new Educational();
            $entity->setEducational($edu);
        }

        if( $routeName == "res_new") {
            $type = "research";
            $res = new Research();
            $entity->setResearch($res);
        }

        //$slide2 = new Slide();
        //$block->addSlide($slide2);

        //get pathology service for this user by email
        $helper = new FormHelper();
        $email = $this->get('security.context')->getToken()->getAttribute('email');
        $service = $helper->getUserPathology($email);
//        if( $service ) {
//            $services = explode("/", $service);
//            $service = $services[0];
//        }
        $entity->setPathologyService($service);

        $form   = $this->createForm( new OrderInfoType($type,$service), $entity );
        
        return array(          
            'form' => $form->createView(),
            'type' => 'new',
            'multy' => $type
        );
    }



    /**
     * Displays a form to create a new OrderInfo + Scan entities.
     *
     * @Route("/{id}", name="multy_show", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template("OlegOrderformBundle:MultyScanOrder:new.html.twig")
     */
    public function showMultyAction($id)
    {

        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('OlegOrderformBundle:Security:login.html.twig');
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegOrderformBundle:OrderInfo')->find($id);

        $repository = $this->getDoctrine()->getRepository('OlegOrderformBundle:OrderInfo');
        $dql =  $repository->createQueryBuilder("orderinfo");
              
        //$repository = $this->getDoctrine()->getRepository('OlegOrderformBundle:Slide');
        //$dql =  $repository->createQueryBuilder("slide");
        //$dql->innerJoin("slide.orderinfo", "orderinfo");      
        //$dql->where("orderinfo.id = :orderid");
        
        $dql->innerJoin("orderinfo.patient", "patient");
        $dql->innerJoin("orderinfo.specimen", "specimen");     
        $dql->innerJoin("orderinfo.accession", "accession");
        $dql->innerJoin("orderinfo.part", "part");      
        $dql->innerJoin("orderinfo.block", "block");
        $dql->innerJoin("orderinfo.slide", "slide");
        $dql->where("orderinfo.id = :orderid AND slide.orderinfo = orderinfo");
        
        $dql->distinct('orderinfo.');
        //$dql->where("orderinfo.id = :orderid AND patient.orderinfo = orderinfo.id");
        //$dql->setParameter('orderid', $id);
        //$dql->setParameter('orderid', "active");
        
        $query = $em->createQuery($dql);
        $query->setParameter('orderid', $id);
        
        //$entity = $query->getResult();  
               
//        $slides = $em->getRepository('OlegOrderformBundle:OrderInfo')->findBy( array('slide' => $id) );      
//
//        $entity->clearPatient();
//
//        //get only elements with this orderinfo id (use slide object)
//        foreach( $slides as $slide  ) {
//
//            //get all
//            $block = $slide->getBlock();
//            $part = $block->getPart();
//            $accession = $slide->getAccession();
//            $specimen = $accession->getSpecimen();
//            $patient = $specimen->getPatient();
//
//            //clean
//            $block->clearSlide();
//
//            $part->clearSlide();
//            $part->clearBlock();
//
//            $accession->clearSlide();
//            $accession->clearPart();
//
//            $specimen->clearAccession();
//
//            $patient->clearSpecimen();
//
//            //re-build                     
//            $block->addSlide($slide);
//            $part->addBlock($block);
//            $accession->addPart($part);
//            $specimen->addAccession( $accession );
//            $patient->addSpecimen($specimen);
//            $entity->addPatient($patient);
//            $entity->addSlide($slide);
//
//            //echo "slide=".$slide;
//        }


        $form   = $this->createForm( new OrderInfoType(true, null, $entity), $entity, array('disabled' => true) );

//        echo "type=".$entity->getType();
//        exit();

        return array(
            'form' => $form->createView(),
            'type' => 'show',
            'multy' => $entity->getType()
        );
    }

 
}
