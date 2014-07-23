<?php

namespace Oleg\OrderformBundle\Controller;

use Oleg\OrderformBundle\Entity\Patient;
use Oleg\OrderformBundle\Entity\PatientMrn;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

use Oleg\OrderformBundle\Form\PatientType;
use Oleg\OrderformBundle\Entity\ClinicalHistory;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Oleg\OrderformBundle\Helper\UserUtil;

/**
 * OrderInfo controller.
 *
 * @Route("/check")
 * @Template("OlegOrderformBundle:Patient:edit_single.html.twig")
 */
class CheckController extends Controller {

    public function getArrayFieldJson( $fields, $childrenArr = null ) {

        //echo "fields count=".count($fields)."  ";
        $fieldJson = array();

        foreach( $fields as $field ) {

            //echo "field=".$field." ";

            if( $field == null ) {
                $fieldJson[] = null;
                continue;
            }

            $providerStr = "";
            //echo "provider count=".count($field->getProvider()).", provider name=".$field->getProvider()->getUsername().", ";

            $provider = $field->getProvider();
            if( $provider ) {
                if( $provider->getDisplayName() != "" ) {
                    $providerStr = $providerStr." ".$provider->getDisplayName();
                } else {
                    $providerStr = $providerStr." ".$provider->getUsername();
                }
            }

            //echo "providerStr=".$providerStr.", ";

            $transformer = new DateTimeToStringTransformer(null,null,'m/d/Y');
            $dateStr = $transformer->transform($field->getCreationdate());

            $hist = array();
            $hist['id'] = $field->getId();
            $hist['text'] = $field."";
            $hist['provider'] = $providerStr;
            $hist['date'] = $dateStr;
            $hist['validity'] = $field->getStatus();

            if( $childrenArr ) {
                foreach( $childrenArr as $child ) {
                    $getMethod = "get".$child;
                    //echo "getMethod=".$getMethod."<br>";

                    if( $child == "keytype" ) {
                        $childValue = $field->$getMethod()->getId();
                        //echo "childValue=".$childValue."<br>";
                        $hist[$child] = $childValue;
                        $hist['keytypename'] = $field->$getMethod()."";
                    } else
                    if( $child == "staintype" ) {
                        $childValue = $field->$getMethod()->getId();
                        $hist[$child] = $childValue;
                    } else {
                        $childValue = $field->$getMethod()."";
                        //echo "childValue=".$childValue."<br>";
                        $hist[$child] = $childValue;
                    }


                }
            }

            $fieldJson[] = $hist;

        }

        return $fieldJson;
    }


    /**
     * Find an element in DB
     * @Route("/patient/check", name="get-patientdata")
     * @Method("GET")   //TODO: use POST?
     */
    public function getPatientAction() {

        $user = $this->get('security.context')->getToken()->getUser();

        $request = $this->get('request');
        $key = trim( $request->get('key') );
        $keytype = trim( $request->get('extra') );
        $inst = trim( $request->get('inst') );

        $originalKeytype = $keytype;
        
        $em = $this->getDoctrine()->getManager();
        $keytype = $em->getRepository('OlegOrderformBundle:Patient')->getCorrectKeytypeId($keytype,$user);

        $extra = array();
        $extra["keytype"] = $keytype;

        //echo "key=".$key.", keytype=".$keytype.", inst=".$inst." ";

        $validity = array('valid','reserved');

        $institutions = array();
        $institutions[] = $inst;

        $entity = $em->getRepository('OlegOrderformBundle:Patient')->findOneByIdJoinedToField($institutions,$key,"Patient","mrn",$validity,true,$extra);   //findOneByIdJoinedToMrn($mrn);

        $element = array();
        
        $security_content = $this->get('security.context');
        $user = $this->get('security.context')->getToken()->getUser();
        $userUtil = new UserUtil();
        if( $entity && !$userUtil->hasPermission($entity,$security_content) ) {
            //echo "no permission ";
            //$entity->filterArrayFields($user,true);

            if( $entity->obtainExistingFields(true) == 0 ) { //if all fields are empty make entity = null
                $entity = null;
            }

        }

        if( !is_numeric ( $originalKeytype ) ) {
            $originalKeytype = $keytype;
        }
        
        $originalKeytype = $em->getRepository('OlegOrderformBundle:MrnType')->findOneById($originalKeytype);
        if( $originalKeytype == "Existing Auto-generated MRN" && !$entity ) {
            $entity = null;               
            $element = -2;
        }
        
        if( $entity ) {

            //TODO: get one 'valid' MRN for this user?
            //TODO: get one 'valid' DOB for this user?

            $element = array(
                //'inmrn'=>$mrn,
                'id'=>$entity->getId(),
                'mrn'=>$this->getArrayFieldJson($entity->getMrn(),array('keytype')),
                'fullname'=>$entity->getFullPatientName(),
                'sex'=>$this->getArrayFieldJson( array($entity->obtainValidField('sex')) ),
                'dob'=>$this->getArrayFieldJson($entity->getDob()),
                'age'=>$entity->calculateAge(),
                'clinicalHistory'=>$this->getArrayFieldJson($entity->getClinicalHistory())
                //'clinicalHistory'=>$this->getArrayFieldJson( array($entity->obtainValidField('clinicalHistory')) )
            );
        } 

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($element));
        return $response;
    }

    /**
     * Create new element with status RESERVED
     * @Route("/patient/generate", name="create-mrn")
     * @Method("GET")
     */
    public function createPatientAction() {

//        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
//            return $this->render('OlegOrderformBundle:Security:login.html.twig');
//        }

        $request = $this->get('request');
        $inst = trim( $request->get('inst') );

        $user = $this->get('security.context')->getToken()->getUser();

        $keytypeEntity = $this->getDoctrine()->getRepository('OlegOrderformBundle:MrnType')->findOneByName("Auto-generated MRN");
        $keytype = $keytypeEntity->getId().""; //id of "New York Hospital MRN" in DB

        $extra = array();
        $extra["keytype"] = $keytype;

        //echo "keytype=".$keytype."<br>";
        //exit();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OlegOrderformBundle:Patient')->createElement(
            $inst,
            null,       //status
            $user,      //provider
            "Patient",  //$className
            "mrn",      //$fieldName
            null,       //$parent
            null,       //$fieldValue
            $extra,     //$extra
            false        //$withfields
        );
        
        $element = array(
            'id'=>$entity->getId(),
            'mrn'=>$this->getArrayFieldJson($entity->getMrn(),array('keytype')),
            //'lastname'=>$this->getArrayFieldJson($entity->getLastName()),
            //'sex'=>$this->getArrayFieldJson($entity->getSex()),
            'dob'=>$this->getArrayFieldJson($entity->getDob()),
            //'age'=>$this->getArrayFieldJson($entity->getAge()),
            'clinicalHistory'=>$this->getArrayFieldJson($entity->getClinicalHistory())
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($element));
        return $response;
    }

    /**
     * @Route("/patient/delete/{key}", name="delete-mrn-keytype")
     * @Method("DELETE")
     */
    public function deleteMrnAction( Request $request ) {

        $user = $this->get('security.context')->getToken()->getUser();

        $key = trim( $request->get('key') );
        $keytype = trim( $request->get('extra') );

        $inst = trim( $request->get('inst') );
        $institutions = array();
        $institutions[] = $inst;

        $em = $this->getDoctrine()->getManager();
        $keytype = $em->getRepository('OlegOrderformBundle:Patient')->getCorrectKeytypeId($keytype,$user);

        $extra = array();
        $extra["keytype"] = $keytype;

        $res = $em->getRepository('OlegOrderformBundle:Patient')->deleteIfReserved( $institutions, $key,"Patient","mrn",$extra );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($res));
        return $response;
    }



    /************************ ACCESSION *************************/
    /**
     * Find accession by #
     * @Route("/accession/check", name="get-accession")
     * @Method("GET")
     */
    public function getAccessionAction() {

        $user = $this->get('security.context')->getToken()->getUser();

        $request = $this->get('request');
        $key = trim( $request->get('key') );
        $keytype = trim( $request->get('extra') );  //id or string of accession type
        $inst = trim( $request->get('inst') );
        
        $originalKeytype = $keytype;

        $em = $this->getDoctrine()->getManager();

        $keytype = $em->getRepository('OlegOrderformBundle:Accession')->getCorrectKeytypeId($keytype,$user);
        //echo "keytype2=".$keytype.", key=".$key." => ";

        $extra = array();
        $extra["keytype"] = $keytype;

        $validity = array('valid','reserved');

        $institutions = array();
        $institutions[] = $inst;

        $entity = $em->getRepository('OlegOrderformBundle:Accession')->findOneByIdJoinedToField($institutions,$key,"Accession","accession",$validity,true,$extra);

        $element = array();
              
        $security_content = $this->get('security.context');
        $userUtil = new UserUtil();
        //$permission = true;
        if( $entity && !$userUtil->hasPermission($entity,$security_content) ) {
            $user = $this->get('security.context')->getToken()->getUser();
            //$entity->filterArrayFields($user,true);

            //echo "procedure existing count=".$entity->getParent()->obtainExistingFields(true)."<br>";
            //echo "accession existing count=".$entity->obtainExistingFields(true)."<br>";
            if( $entity->obtainExistingFields(true) == 0 && $entity->getParent()->obtainExistingFields(true) == 0 ) { //if all fields are empty make entity = null
                $entity = null;
                //$permission = true;
            }
        }

        if( !is_numeric ( $originalKeytype ) ) {
            $originalKeytype = $keytype;
        }

        $originalKeytype = $em->getRepository('OlegOrderformBundle:AccessionType')->findOneById($originalKeytype);
        if( $originalKeytype == "Existing Auto-generated Accession Number" && !$entity ) {
            $entity = null;               
            $element = -2;
        }

        //echo "entity=".$entity."<br>";

        if( $entity ) {

            $parentKey = null;

            if( $entity->getProcedure() ) {

                $transformer = new DateTimeToStringTransformer(null,null,'m/d/Y');

                //find patient mrn
                $patient = $entity->getProcedure()->getPatient();
                //if( !$permission ) {
                //    $patient->filterArrayFields($user,true);
                //}

                if( $patient ) {
                    $parentKey = $patient->obtainValidKeyfield();
                    //$parentDob = $patient->obtainValidDob();
                    $parentDob = $patient->obtainValidField('dob');
                }

                //echo "parentKey=".$parentKey."<br>";

                if( $patient && $parentKey ) {
                    $parentKey = $patient->obtainValidKeyfield();
                    $dateStr = $transformer->transform($parentKey->getCreationdate());
                    $mrnstring = 'MRN '.$parentKey.' ['.$parentKey->getKeytype().'] (as submitted by '.$parentKey->getProvider().' on '. $dateStr.')';
                    $extraid = $parentKey->getKeytype()->getId()."";
                    $mrnkeytype = $em->getRepository('OlegOrderformBundle:MrnType')->findOneById($extraid);
                    if( $mrnkeytype == "Auto-generated MRN" ) {
                        //set to "Existing Auto-generated MRN" in order to correct set select2 to "Existing Auto-generated MRN"
                        $newkeytype = $em->getRepository('OlegOrderformBundle:MrnType')->findOneByName("Existing Auto-generated MRN");
                        $extraid = $newkeytype->getId()."";
                    }
                    $orderinfoString = "Order ".$patient->getOrderinfo()->first()->getId()." submitted on ".$transformer->transform($patient->getOrderinfo()->first()->getOrderdate()). " by ". $patient->getOrderinfo()->first()->getProvider();
                }

                $procedureName = $entity->getProcedure()->getName();

                //$encDate = $transformer->transform( $entity->getProcedure()->getEncounterDate()->first() );
                $encDate = $entity->getProcedure()->getEncounterDate();
                $patLastName = $entity->getProcedure()->getPatlastname();
                $patFirstName = $entity->getProcedure()->getPatfirstname();
                $patMiddleName = $entity->getProcedure()->getPatmiddlename();
                $patSex = $entity->getProcedure()->getPatsex();
                $patAge = $entity->getProcedure()->getPatage();
                $patHist = $entity->getProcedure()->getPathistory();

            }//if $entity->getProcedure()

            if( !$parentKey ) {
                $parentKey = null;
                $mrnstring = "";
                $extraid = "";
                $parentDob = "";
                $orderinfoString = "";
                $procedureName = array();

                $encDate = array();
                $patLastName = array();
                $patFirstName = array();
                $patMiddleName = array();
                $patSex = array();
                $patAge = array();
                $patHist = array();
            }

            //echo "mrnstring=".$mrnstring." ";

            $element = array(
                'id'=>$entity->getId(),
                'parent'=>$parentKey."",
                'extraid'=>$extraid,
                'parentdob'=>$parentDob."",
                'mrnstring'=>$mrnstring,
                'orderinfo'=>$orderinfoString,
                'procedure'=>$this->getArrayFieldJson($procedureName),

                'encounterDate'=>$this->getArrayFieldJson($encDate),
                'patlastname'=>$this->getArrayFieldJson($patLastName),
                'patfirstname'=>$this->getArrayFieldJson($patFirstName),
                'patmiddlename'=>$this->getArrayFieldJson($patMiddleName),
                'patsex'=>$this->getArrayFieldJson($patSex),
                'patage'=>$this->getArrayFieldJson($patAge),
                'pathistory'=>$this->getArrayFieldJson($patHist),

                'accession'=>$this->getArrayFieldJson($entity->getAccession(),array('keytype')),
                'accessionDate'=>$this->getArrayFieldJson($entity->getAccessionDate())
            );
        } 

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($element));
        return $response;
    }

    /**
     * Get next available Accession from DB
     * @Route("/accession/generate", name="create-accession")
     * @Method("GET")
     */
    public function createAccessionAction() {

//        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
//            return $this->render('OlegOrderformBundle:Security:login.html.twig');
//        }

        $request = $this->get('request');
        $inst = trim( $request->get('inst') );

        $user = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        //always use Auto-generated Accession Number keytype to generate the new key
        $typeEntity = $em->getRepository('OlegOrderformBundle:AccessionType')->findOneByName("Auto-generated Accession Number");
        $keytype = $typeEntity->getId().""; //id of "New York Hospital MRN" in DB

        $extra = array();
        $extra["keytype"] = $keytype;

        //$status, $provider, $className, $fieldName, $parent = null, $fieldValue = null, $extra = null, $withfields = true, $flush=true
        $entity = $em->getRepository('OlegOrderformBundle:Accession')->createElement(
            $inst,
            null,           //status
            $user,          //provider
            "Accession",    //$className
            "accession",    //$fieldName
            null,           //$parent
            null,           //$fieldValue
            $extra,         //$extra
            false           //$withfields
        );
        //echo "len=".count($entity->getMrn()).",mrn=".$entity->getMrn()->last()." ";

        $element = array(
            'id'=>$entity->getId(),
            'accession'=>$this->getArrayFieldJson($entity->getAccession(),array('keytype')),
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($element));
        return $response;
    }

    /**
     * @Route("/accession/delete/{key}", name="delete-accession")
     * @Method("DELETE")
     */
    public function deleteAccessionAction(Request $request) {

        $user = $this->get('security.context')->getToken()->getUser();

        $key = trim( $request->get('key') );
        $keytype = trim( $request->get('extra') );
        //echo "key=".$key.",keytype=".$keytype." | ";

        $inst = trim( $request->get('inst') );
        $institutions = array();
        $institutions[] = $inst;

        $em = $this->getDoctrine()->getManager();

        $keytype = $em->getRepository('OlegOrderformBundle:Accession')->getCorrectKeytypeId($keytype,$user);

        $extra = array();
        $extra["keytype"] = $keytype;

        $res = $em->getRepository('OlegOrderformBundle:Accession')->deleteIfReserved( $institutions, $key,"Accession","accession",$extra );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($res));
        return $response;
    }


    /************************ PART *************************/
    /**
     * Get Part from DB if existed
     * @Route("/part/check", name="get-part")
     * @Method("GET")
     */
    public function getPartAction() {

        $request = $this->get('request');
        $key = trim( $request->get('key') );
        $accession = trim( $request->get('parentkey') ); //need accession number to check if part exists in DB
        $keytype = trim( $request->get('parentextra') );
        //echo "key=".$key."   ";

        $inst = trim( $request->get('inst') );
        $institutions = array();
        $institutions[] = $inst;

        $validity = array('valid','reserved');

        $entity = $this->getDoctrine()->getRepository('OlegOrderformBundle:Part')->findOnePartByJoinedToField( $institutions, $accession, $keytype, $key, $validity );

        //echo "count=".count($entity)."<br>";
        //echo "partname=".$entity->getPartname()->first()."<br>";

        $element = array();

        if( !$entity && strpos($key,'NOPARTNAMEPROVIDED-') !== false ) {
            $entity = null;
            $element = -2;
        }
        
        $userUtil = new UserUtil();
        $security_content = $this->get('security.context');
        if( !$userUtil->hasPermission($entity,$security_content) ) {
            //$user = $this->get('security.context')->getToken()->getUser();
            //$entity->filterArrayFields($user,true);

            if( $entity->obtainExistingFields(true) == 0 ) { //if all fields are empty make entity = null
                $entity = null;
            }
        }     

        if( $entity ) {

            $element = array(
                'id'=>$entity->getId(),
                'partname'=>$this->getArrayFieldJson($entity->getPartname()),
                'sourceOrgan'=>$this->getArrayFieldJson($entity->getSourceOrgan()),
                'description'=>$this->getArrayFieldJson($entity->getDescription()),
                'disident'=>$this->getArrayFieldJson($entity->getDisident()),
                'paper'=>$this->getArrayFieldJson($entity->getPaper()),
                'diffDisident'=>$this->getArrayFieldJson($entity->getDiffDisident()),
                'diseaseType'=>$this->getArrayFieldJson( $entity->getDiseaseType(), array("origin","primaryorgan") )
            );
        } 

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($element));
        return $response;
    }

    /**
     * Get next available Part from DB by giving Accession number
     * @Route("/part/generate", name="create-part")
     * @Method("GET")
     */
    public function createPartAction() {

        $request = $this->get('request');
        $accession = trim( $request->get('parentkey') );
        $keytype = trim( $request->get('parentextra') );
        $inst = trim( $request->get('inst') );
        //echo "accession=(".$accession.")   ";

        if( $accession && $accession != ""  ) {

            $user = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $part = $em->getRepository('OlegOrderformBundle:Part')->createPartByAccession($inst,$accession,$keytype,$user);
            //echo "len=".count($entity->getMrn()).",mrn=".$entity->getMrn()->last()." ";

            if( $part ) {
                $user = $this->get('security.context')->getToken()->getUser();
                $part->getPartname()->first()->setProvider($user);
                //$validPartname = $em->getRepository('OlegOrderformBundle:Part')->obtainValidField($part->getPartname());
                $element = array(
                    'id'=>$part->getId(),
                    'partname'=>$this->getArrayFieldJson($part->getPartname())
                );
            } else {
                $element = null;
            }

        } else {

            $element = null;

        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($element));
        return $response;
    }

    /**
     * @Route("/part/delete/{key}", name="delete-part")
     * @Method("DELETE")
     */
    public function deletePartAction(Request $request) {

        $key = trim( $request->get('key') );
        $accession = trim( $request->get('parentkey') );
        $keytype = trim( $request->get('parentextra') );

        $extra = array();
        $extra["accession"] = $accession;
        $extra["keytype"] = $keytype;

        $inst = trim( $request->get('inst') );
        $institutions = array();
        $institutions[] = $inst;

        //echo "key=".$key." , accession=".$accession.", keytype=".$keytype."   ";

        $em = $this->getDoctrine()->getManager();
        $res = $em->getRepository('OlegOrderformBundle:Part')->deleteIfReserved( $institutions, $key,"Part","partname", $extra );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($res));
        return $response;
    }


    /************************ BLOCK *************************/
    /**
     * Get BLOCK from DB if existed
     * @Route("/block/check", name="get-block")
     * @Method("GET")
     */
    public function getBlockAction() {

        $request = $this->get('request');
        $key = trim($request->get('key'));       
        $partname = trim($request->get('parentkey')); //need partname to check if part exists in DB
        $accession = trim($request->get('grandparentkey')); //need accession number to check if part exists in DB 
        $keytype = trim($request->get('grandparentextra'));    
        //echo "key=".$key."   ";

        $inst = trim( $request->get('inst') );
        $institutions = array();
        $institutions[] = $inst;
        $validity = array('valid','reserved');

        if( $accession != "" && $partname != "" ) {
            $entity = $this->getDoctrine()->getRepository('OlegOrderformBundle:Block')->findOneBlockByJoinedToField( $institutions, $accession, $keytype, $partname, $key, $validity );

            //echo "count=".count($entity)."<br>";
            //echo "partname=".$entity->getPartname()->first()."<br>";
            
            $element = array();

            if( !$entity && strpos($key,'NOBLOCKNAMEPROVIDED-') !== false ) {
                $entity = null;
                $element = -2;
            }

            $security_content = $this->get('security.context');
            $userUtil = new UserUtil();
            if( !$userUtil->hasPermission($entity,$security_content) ) {
                //$user = $this->get('security.context')->getToken()->getUser();
                //$entity->filterArrayFields($user,true);

                if( $entity->obtainExistingFields(true) == 0 ) { //if all fields are empty make entity = null
                    $entity = null;
                }
            }

            if( $entity ) {

                $element = array(
                    'id'=>$entity->getId(),
                    'blockname'=>$this->getArrayFieldJson($entity->getBlockname()),
                    'sectionsource'=>$this->getArrayFieldJson($entity->getSectionsource()),
                    'specialStains'=>$this->getArrayFieldJson( $entity->getSpecialStains(), array("field","staintype") )
                );
            } 
            
        } else {
            $element = array();
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($element));
        return $response;
    }

    /**
     * Get next available Block from DB by giving Accession number and Part name
     * @Route("/block/generate", name="create-block")
     * @Method("GET")
     */
    public function createBlockAction() {


        $request = $this->get('request');           
        $partname = trim($request->get('parentkey'));
        $accession = trim($request->get('grandparentkey')); //need accession number to check if part exists in DB 
        $keytype = trim( $request->get('grandparentextra') );
        $inst = trim( $request->get('inst') );
        //echo "accession=(".$accession.")   ";

        if( $accession != "" && $partname != "" ) {

            $user = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $block = $em->getRepository('OlegOrderformBundle:Block')->createBlockByPartnameAccession($inst,$accession,$keytype,$partname,$user);
            //echo "len=".count($entity->getMrn()).",mrn=".$entity->getMrn()->last()." ";

            $user = $this->get('security.context')->getToken()->getUser();
            $block->getBlockname()->first()->setProvider($user);

            //echo "partname=".$part->getPartname()."  ";

            if( $block ) {
                //$validPartname = $em->getRepository('OlegOrderformBundle:Part')->obtainValidField($part->getPartname());
                $element = array(
                    'id'=>$block->getId(),
                    'blockname'=>$this->getArrayFieldJson($block->getBlockname())
                );
            } else {
                $element = null;
            }

        } else {

            $element = null;

        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($element));
        return $response;
    }

    /**
     * @Route("/block/delete/{key}", name="delete-block")
     * @Method("DELETE")
     */
    public function deleteBlockAction(Request $request) {

        $key = trim($request->get('key'));            
        $partname = trim($request->get('parentkey'));
        $accession = trim($request->get('grandparentkey'));
        $keytype = trim( $request->get('grandparentextra') );

        $inst = trim( $request->get('inst') );
        $institutions = array();
        $institutions[] = $inst;

        $extra = array();
        $extra["accession"] = $accession;
        $extra["keytype"] = $keytype;
        $extra["partname"] = $partname;

        $em = $this->getDoctrine()->getManager();
        $res = $em->getRepository('OlegOrderformBundle:Block')->deleteIfReserved( $institutions, $key,"Block","blockname", $extra );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($res));
        return $response;
    }


    //get keytype ids by keytype string for handsontable
    /**
     * @Route("/accession/keytype/{keytype}", name="get-accession-keytypeid")
     * @Route("/patient/keytype/{keytype}", name="get-patient-keytypeid")
     * @Method("GET")
     */
    public function getKeytypeIdAction(Request $request, $keytype) {

        $em = $this->getDoctrine()->getManager();

        if( $request->get('_route') == "get-accession-keytypeid" ) {
            $keytypeEntity = $em->getRepository('OlegOrderformBundle:AccessionType')->findOneByName($keytype);
        } else
        if( $request->get('_route') == "get-patient-keytypeid" ) {
            $keytypeEntity = $em->getRepository('OlegOrderformBundle:MrnType')->findOneByName($keytype);
        } else {
            $keytypeEntity = null;
        }

        if( $keytypeEntity ) {
            $keytype = $keytypeEntity->getId();
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($keytype));
        return $response;
    }

//    /**
//     * @Route("/userrole", name="get-user-role")
//     * @Method("POST")
//     */
//    public function getUserRoleAction(Request $request) {
//
//        $external = 'true';
//
//        $user = $this->get('security.context')->getToken()->getUser();
//
//        if( !$user->hasRole('ROLE_SCANORDER_EXTERNAL_SUBMITTER') && !$user->hasRole('ROLE_SCANORDER_EXTERNAL_ORDERING_PROVIDER') ) {
//            $external = 'not_external_role';
//        }
//
//        $response = new Response();
//        $response->headers->set('Content-Type', 'application/json');
//        $response->setContent(json_encode($external));
//        return $response;
//    }


}
