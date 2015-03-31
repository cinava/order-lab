<?php

namespace Oleg\OrderformBundle\Controller;


use Oleg\OrderformBundle\Entity\AccessionAccession;
use Oleg\OrderformBundle\Entity\BlockOrder;
use Oleg\OrderformBundle\Entity\EmbedBlockOrder;
use Oleg\OrderformBundle\Entity\EncounterDate;
use Oleg\OrderformBundle\Entity\EncounterPatage;
use Oleg\OrderformBundle\Entity\Endpoint;
use Oleg\OrderformBundle\Entity\InstructionList;
use Oleg\OrderformBundle\Entity\OrderInfo;
use Oleg\OrderformBundle\Entity\PatientClinicalHistory;
use Oleg\OrderformBundle\Entity\PatientDob;
use Oleg\OrderformBundle\Entity\PatientMrn;
use Oleg\OrderformBundle\Entity\Report;
use Oleg\OrderformBundle\Entity\RequisitionForm;
use Oleg\OrderformBundle\Entity\Imaging;
use Oleg\OrderformBundle\Entity\ScanOrder;
use Oleg\OrderformBundle\Entity\SlideOrder;
use Oleg\OrderformBundle\Entity\StainOrder;
use Oleg\OrderformBundle\Form\DataTransformer\AccessionTypeTransformer;
use Oleg\OrderformBundle\Form\DataTransformer\MrnTypeTransformer;
use Oleg\UserdirectoryBundle\Entity\AttachmentContainer;
use Oleg\UserdirectoryBundle\Entity\Document;
use Oleg\UserdirectoryBundle\Entity\Institution;
use Oleg\UserdirectoryBundle\Entity\UserWrapper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oleg\OrderformBundle\Entity\Patient;
use Oleg\OrderformBundle\Entity\Encounter;
use Oleg\OrderformBundle\Form\PatientType;
use Oleg\OrderformBundle\Entity\Procedure;
use Oleg\OrderformBundle\Entity\Accession;
use Oleg\OrderformBundle\Entity\Part;
use Oleg\OrderformBundle\Entity\Block;
use Oleg\OrderformBundle\Entity\Slide;

use Oleg\OrderformBundle\Entity\LabOrder;
use Oleg\UserdirectoryBundle\Entity\DocumentContainer;

/**
 * Patient controller.
 *
 * @Route("/patient")
 */
class PatientController extends Controller
{


    /**
     * Lists all Patient entities.
     *
     * @Route("/", name="scan-patient-list")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OlegOrderformBundle:Patient')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * New Patient.
     *
     * @Route("/datastructure", name="scan-patient-new")
     * @Method("GET")
     * @Template("OlegOrderformBundle:Patient:new.html.twig")
     */
    public function newPatientAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();

        $thisparams = array(
            'objectNumber' => 1,
            'withorders' => true,
            'accession.attachmentContainer' => 5,
            'part.attachmentContainer' => 5,
        );
        $patient = $this->createPatientDatastructure($thisparams);

        $disabled = true;
        //$disabled = false;

        $params = array(
            'type' => 'multy',
            'cycle' => 'new',
            'user' => $user,
            'datastructure' => 'datastructure'
        );

        //specific fields
        $params['message.sources'] = true;
        $params['endpoint.system'] = true;
        $params['message.orderdate'] = true;
        $params['message.provider'] = true;
        $params['message.idnumber'] = true;

        //specific orders
//        $params['message.laborder'] = true;
//        $params['message.report'] = true;
//        $params['message.blockorder'] = true;
//        $params['message.slideorder'] = true;
//        $params['message.stainorder'] = true;

        $form = $this->createForm( new PatientType($params,$patient), $patient, array('disabled' => $disabled) );

        return array(
            'entity' => $patient,
            'form' => $form->createView(),
            'formtype' => 'Patient Data Structure',
            'type' => 'show',
            'datastructure' => 'datastructure'
        );
    }

    public function createPatientDatastructure( $params ) {

        if( array_key_exists('withfields', $params) ) {
            $withfields = $params['withfields'];
        } else {
            $withfields = true;
        }

        if( array_key_exists('persist', $params) ) {
            $persist = $params['persist'];
        } else {
            $persist = false;
        }

        if( array_key_exists('specificmessage', $params) ) {
            $specificmessage = $params['specificmessage'];
        } else {
            $specificmessage = false;
        }

        if( array_key_exists('objectNumber', $params) ) {
            $objectNumber = $params['objectNumber'];
        } else {
            $objectNumber = 1;
        }

        if( array_key_exists('withorders', $params) ) {
            $withorders = $params['withorders'];
        } else {
            $withorders = false;
        }

        if( array_key_exists('scanorder', $params) ) {
            $scanorderType = $params['scanorder'];
        } else {
            $scanorderType = false;
        }

        if( array_key_exists('accession.attachmentContainer', $params) ) {
            $attachmentContainerAccessionNumber = $params['accession.attachmentContainer'];
        } else {
            $attachmentContainerAccessionNumber = 0;
        }

        if( array_key_exists('part.attachmentContainer', $params) ) {
            $attachmentContainerPartNumber = $params['part.attachmentContainer'];
        } else {
            $attachmentContainerPartNumber = 0;
        }

        $em = $this->getDoctrine()->getManager();
        $securityUtil = $this->get('order_security_utility');

        $system = $securityUtil->getDefaultSourceSystem(); //'scanorder';
        $status = 'valid';
        $user = $this->get('security.context')->getToken()->getUser();

        $patient = new Patient($withfields,$status,$user,$system);
        $patient->addExtraFields($status,$user,$system);

        if( $persist ) {
            $em->persist($patient);
        }

        if( $specificmessage ) {
            $specificmessage->addPatient($patient);
        }

        for( $count = 0; $count < $objectNumber; $count++ ) {

            $encounter = new Encounter($withfields,$status,$user,$system);
            $encounter->addExtraFields($status,$user,$system);
            $patient->addEncounter($encounter);

            if( $persist ) {
                $em->persist($encounter);
            }

            if( $specificmessage ) {
                $specificmessage->addEncounter($encounter);
            }

            $procedure = new Procedure($withfields,$status,$user,$system);
            $procedure->addExtraFields($status,$user,$system);
            $encounter->addProcedure($procedure);

            if( $persist ) {
                $em->persist($procedure);
            }

            if( $specificmessage ) {
                $specificmessage->addProcedure($procedure);
            }

            $accession = new Accession($withfields,$status,$user,$system);
            $accession->addExtraFields($status,$user,$system);
            $procedure->addAccession($accession);

            if( $persist ) {
                $em->persist($accession);
            }

            if( $specificmessage ) {
                $specificmessage->addAccession($accession);
            }

            $part = new Part($withfields,$status,$user,$system);
            //$part->addExtraFields($status,$user,$system);
            $accession->addPart($part);

            if( $persist ) {
                $em->persist($part);
            }

            if( $specificmessage ) {
                $specificmessage->addPart($part);
            }

            $block = new Block($withfields,$status,$user,$system);

            //set specialStains to null
            $blockSpecialstain = $block->getSpecialStains()->first();
            $staintype = $em->getRepository('OlegOrderformBundle:StainList')->find(1);
            $blockSpecialstain->setStaintype($staintype);
            //$blockSpecialstain->setField('stain ' . $staintype);
            //echo "specialStain field=".$blockSpecialstain->getField()."<br>";
            //echo "specialStain staintype=".$blockSpecialstain->getStaintype()."<br>";

            $part->addBlock($block);

            if( $persist ) {
                $em->persist($block);
            }

            if( $specificmessage ) {
                $specificmessage->addBlock($block);
            }

//            $em = $this->getDoctrine()->getManager();
//            $Staintype = $em->getRepository('OlegOrderformBundle:StainList')->find(1);
//            $block->getSpecialStains()->first()->setStaintype($Staintype);
//            echo $block;
//            echo "staintype=".$block->getSpecialStains()->first()->getStaintype()->getId()."<br>";


            $slide = new Slide($withfields,'valid',$user,$system); //Slides are always valid by default
            //$slide->addExtraFields($status,$user,$system);
            $block->addSlide($slide);

            if( $persist ) {
                $em->persist($slide);
            }

            if( $specificmessage ) {
                $specificmessage->addSlide($slide);
            }

            //add scan (Imaging) to a slide
            for( $countImage = 1; $countImage < $objectNumber; $countImage++ ) {
                $slide->addScan( new Imaging('valid',$user,$system) );
            }

            //Accession: add 5 autopsy fields: add 5 documentContainers to attachmentContainer
            if( $attachmentContainerAccessionNumber > 0 ) {
                $attachmentContainerAccession = $accession->getAttachmentContainer();
                if( !$attachmentContainerAccession ) {
                    $attachmentContainerAccession = new AttachmentContainer();
                    $accession->setAttachmentContainer($attachmentContainerAccession);
                }
                for( $i=0; $i<$attachmentContainerAccessionNumber; $i++) {
                    $attachmentContainerAccession->addDocumentContainer( new DocumentContainer() );
                }
            }

            //Part: add 5 gross image fields: add 5 documentContainers to attachmentContainer
            if( $attachmentContainerPartNumber > 0 ) {
                $attachmentContainerPart = $part->getAttachmentContainer();
                if( !$attachmentContainerPart ) {
                    $attachmentContainerPart = new AttachmentContainer();
                    $part->setAttachmentContainer($attachmentContainerPart);
                }
                for( $i=0; $i<$attachmentContainerPartNumber; $i++) {
                    $attachmentContainerPart->addDocumentContainer( new DocumentContainer() );
                }
            }

            /////////////////////// testing: create specific messages ///////////////////////
            if( $withorders ) {

                $this->createAndAddSpecificMessage($slide,"Lab Order");

                //$this->createAndAddSpecificMessage($part,"Report");
                $this->createAndAddSpecificMessage($slide,"Report");

                $this->createAndAddSpecificMessage($part,"Block Order");

                $this->createAndAddSpecificMessage($block,"Slide Order");

                $this->createAndAddSpecificMessage($slide,"Stain Order");

            }

            if( $scanorderType && $scanorderType != "" ) {
                $message = $this->createAndAddSpecificMessage($slide,$scanorderType);
            }

            /////////////////////// EOF specific messages ///////////////////////

        }

        return $patient;
    }

    public function createAndAddSpecificMessage($object,$messageTypeStr) {

        $em = $this->getDoctrine()->getManager();
        $message = new OrderInfo();

        $user = $this->get('security.context')->getToken()->getUser();
        $message->setProvider($user);

//        $message->setIdnumber($messageTypeStr.' id number');

        $category = $em->getRepository('OlegOrderformBundle:MessageCategory')->findOneByName($messageTypeStr);
        $message->setMessageCategory($category);

        $source = new Endpoint();
        $message->addSource($source);

        $destination = new Endpoint();
        $message->addDestination($destination);

        //add attachment with 1 documentContainer
        $attachmentContainerPart = $message->getAttachmentContainer();
        if( !$attachmentContainerPart ) {
            $attachmentContainerPart = new AttachmentContainer();
            $message->setAttachmentContainer($attachmentContainerPart);
        }
        for( $i = 0; $i < 1; $i++ ) {
            $attachmentContainerPart->addDocumentContainer( new DocumentContainer() );
        }


        //add slide to message and input
        //$object->addOrderinfo($message);
        //set this slide as order input
        $message->addInputObject($object);


        if( $messageTypeStr == "Lab Order" ) {

            $laborder = new LabOrder();
            $laborder->setOrderinfo($message);
            $message->setLaborder($laborder);

        }

        if( $messageTypeStr == "Report" ) {

            $report = new Report();
            $report->setOrderinfo($message);
            $message->setReport($report);

            $signingPathologist = new UserWrapper();
            $report->addSigningPathologist($signingPathologist);

            $consultedPathologist = new UserWrapper();
            $report->addConsultedPathologist($consultedPathologist);

        }

        if( $messageTypeStr == "Block Order" ) {
            $blockorder = new BlockOrder();
            $blockorder->setOrderinfo($message);
            $message->setBlockorder($blockorder);

            $instruction = new InstructionList();
            $blockorder->setInstruction($instruction);
        }

        if( $messageTypeStr == "Slide Order" ) {
            $slideorder = new SlideOrder();
            $slideorder->setOrderinfo($message);
            $message->setSlideorder($slideorder);


            $instruction = new InstructionList();
            $slideorder->setInstruction($instruction);
        }

        if( $messageTypeStr == "Stain Order" ) {
            $stainorder = new StainOrder();
            $stainorder->setOrderinfo($message);
            $message->setStainorder($stainorder);

            $instruction = new InstructionList();
            $stainorder->setInstruction($instruction);
        }

        if( $messageTypeStr == "Multi-Slide Scan Order" ) {
            $scanorder = new ScanOrder();
            $scanorder->setOrderinfo($message);
            $message->$scanorder($scanorder);
        }

        return $message;
    }

    /**
     * Finds and displays a Patient entity.
     *
     * @Route("/{id}", name="scan-patient-show")
     * @Method("GET")
     * @Template("OlegOrderformBundle:Patient:new.html.twig")
     */
    public function showAction($id)
    {

        if( false === $this->get('security.context')->isGranted('ROLE_SCANORDER_SUBMITTER') &&
            false === $this->get('security.context')->isGranted('ROLE_SCANORDER_ORDERING_PROVIDER')
        ) {
            return $this->redirect( $this->generateUrl('scan-order-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $entity = $em->getRepository('OlegOrderformBundle:Patient')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Patient entity.');
        }

        $params = array(
            'type' => 'multy',
            'cycle' => "show",
            'user' => $user,
            'datastructure' => 'datastructure'
        );

        $form = $this->createForm( new PatientType($params,$entity), $entity, array('disabled' => true) );

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'formtype' => 'Patient Data Structure',
            'type' => 'show',
            'datastructure' => 'datastructure'
        );
    }

    /**
     * Displays a form to edit an existing Patient entity.
     *
     * @Route("/{id}/edit", name="scan-patient-edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegOrderformBundle:Patient')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Patient entity.');
        }

        $editForm = $this->createForm(new PatientType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Patient entity.
     *
     * @Route("/{id}", name="patient_update")
     * @Method("PUT")
     * @Template("OlegOrderformBundle:Patient:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OlegOrderformBundle:Patient')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Patient entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PatientType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('patient_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Creates a form to delete a Patient entity by id.
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






    //create Test Patient
    /**
     * @Route("/datastructure/new-test-patient", name="scan_testpatient_new")
     * @Method("GET")
     * @Template("OlegOrderformBundle:Patient:new.html.twig")
     */
    public function newTestPatientAction() {

        $securityUtil = $this->get('order_security_utility');
        $status = 'valid';
        $system = $securityUtil->getDefaultSourceSystem();
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        ///////////////////// prepare scanorder (or Slide Order?) /////////////////////
        $message = $this->createSpecificMessage("Multi-Slide Scan Order");

        $thisparams = array(
            'objectNumber' => 1,
            'withorders' => false,
            'persist' => false,
            'specificmessage' => $message
        );
        $patient = $this->createPatientDatastructure($thisparams);

        //add patient
        $patient->addOrderinfo($message);
        $message->addPatient($patient);

        echo "messages=".count($patient->getOrderinfo())."<br>";

        ///////////////////// populate patient with mrn, mrntype, name etc. /////////////////////
        $mrntypeStr = 'Test Patient MRN';
        $testpatients = $em->getRepository('OlegOrderformBundle:Patient')->findByMrntypeString($mrntypeStr);
        $testpatientmrnIndex = count($testpatients)+1;

        //mrn
        $mrntypeTransformer = new MrnTypeTransformer($em,$user);
        $mrntype = $mrntypeTransformer->reverseTransform($mrntypeStr);
        echo "mrntype id=".$mrntype->getId()."<br>";
        //$patientMrn = new PatientMrn($status,$user,$system);
        $patientMrn = $patient->getMrn()->first();
        $patientMrn->setKeytype($mrntype);
        $patientMrn->setField('testmrn-'.$testpatientmrnIndex);
        $patient->addMrn($patientMrn);

        //dob
        //$patientDob = new PatientDob($status,$user,$system);
        $patientDob = $patient->getDob()->first();
        $patientDob->setField( new \DateTime('01/30/1915') );
        $patient->addDob($patientDob);

        //clinical history
        //$patientClinHist = new PatientClinicalHistory($status,$user,$system);
        $patientClinHist = $patient->getClinicalHistory()->first();
        $patientClinHist->setField('Test Clinical History');
        $patient->addClinicalHistory($patientClinHist);
        ///////////////////// EOF populate patient with mrn, mrntype, name etc. /////////////////////


        ///////////////////// populate accession with accession number, accession type, etc. /////////////////////
        $accessiontypeStr = 'Test Accession Number';

        //accession
        $accessiontypeTransformer = new AccessionTypeTransformer($em,$user);
        $accessiontype = $accessiontypeTransformer->reverseTransform($accessiontypeStr);
        echo "accessiontype id=".$accessiontype->getId()."<br>";

        $encounterCount = 0;
        foreach( $patient->getEncounter() as $encounter ) {

            //set encounter age
            //$encounterAge = new EncounterPatage($status,$user,$system);
            //$encounterAge->setField($patient->calculateAgeInt());
            //$encounter->addPatage($encounterAge);

            //set encounter date
            //$encounterdate = new EncounterDate($status,$user,$system);
            //$encounter->addDate($encounterdate);

            $accession = $encounter->getProcedure()->first()->getAccession()->first();
            //echo $accession;

            $testaccessions = $em->getRepository('OlegOrderformBundle:Accession')->findByAccessiontypeString($accessiontypeStr);
            $testaccessionIndex = count($testaccessions)+$encounterCount;

            //$accessionNumber = new AccessionAccession($status,$user,$system);
            $accessionNumber = $accession->getAccession()->first();
            $accessionNumber->setKeytype($accessiontype);
            $accessionNumber->setField('testaccession-'.$testaccessionIndex);
            $accession->addAccession($accessionNumber);

            $encounterCount++;

            //block staintype

        }
        ///////////////////// EOF populate accession with accession number, accession type, etc. /////////////////////


        $message = $em->getRepository('OlegOrderformBundle:OrderInfo')->processOrderInfoEntity( $message, $user, null, $this->get('router'), $this->container );


        //$em->persist($patient);
        //$em->flush();

        if( $patient->getId() ) {
            return $this->redirect( $this->generateUrl('scan-patient-show',array('id'=>$patient->getId())) );
        } else {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Failed to create a test patient'
            );
            return $this->redirect( $this->generateUrl('scan-patient-list') );
        }

    }


    public function createSpecificMessage( $messageCategoryStr ) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $securityUtil = $this->get('order_security_utility');

        $system = $securityUtil->getDefaultSourceSystem(); //'scanorder';

        //set scan order
        $message = new OrderInfo();
        $scanOrder = new ScanOrder();
        $scanOrder->setOrderinfo($message);

        //set provider
        $message->setProvider($user);

        //set Source object
        $source = new Endpoint();
        $source->setSystem($system);
        $message->addSource($source);

        //set Destination object
        $destination = new Endpoint();
        $message->addDestination($destination);

        //type
        $category = $em->getRepository('OlegOrderformBundle:MessageCategory')->findOneByName($messageCategoryStr);
        $message->setMessageCategory($category);

        //set the default institution; check if user has at least one institution
        $orderUtil = $this->get('scanorder_utility');
        $userSiteSettings = $securityUtil->getUserPerSiteSettings($user);
        if( !$userSiteSettings ) {
            $orderUtil->setWarningMessageNoInstitution($user);
            return $this->redirect( $this->generateUrl('scan_home') );
        }
        $permittedInstitutions = $userSiteSettings->getPermittedInstitutionalPHIScope();
        if( count($permittedInstitutions) == 0 ) {
            $orderUtil->setWarningMessageNoInstitution($user);
            return $this->redirect( $this->generateUrl('scan_home') );
        }
        $permittedInstitutions = $userSiteSettings->getPermittedInstitutionalPHIScope();
        $message->setInstitution($permittedInstitutions->first());


        //set default department and division
//        $defaultsDepDiv = $securityUtil->getDefaultDepartmentDivision($entity,$userSiteSettings);
//        $department = $defaultsDepDiv['department'];
//        $division = $defaultsDepDiv['division'];

        //set message status
        $orderStatus = $em->getRepository('OlegOrderformBundle:Status')->findOneByName('Submitted');
        $message->setStatus($orderStatus);

        echo $message;
        echo "message institution=".$message->getInstitution()->getName()."<br>";
        echo "message accessions count=".count($message->getAccession())."<br>";

        return $message;
    }

}
