<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Oleg\OrderformBundle\Entity\Slide;
use Oleg\OrderformBundle\Helper\OrderUtil;
use Oleg\OrderformBundle\Entity\History;

/**
 * OrderInfoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderInfoRepository extends ArrayFieldAbstractRepository {

    protected $user;
    protected $router;
    protected $container;

    //process orderinfo and all entities
    public function processOrderInfoEntity( $entity, $user, $type, $router, $container ) {

        gc_enable();
        $em = $this->_em;
        //$em->getConnection()->getConfiguration()->setSQLLogger(null);

        $this->user = $user;
        $this->router = $router;
        $this->container = $container;

        //replace duplicate entities to filter the similar entities.
        $entity = $this->replaceDuplicateEntities( $entity, $entity );

        if( $type && !$entity->getType() ) {
            $formtype = $em->getRepository('OlegOrderformBundle:FormType')->findOneByName( $type );
            $entity->setType($formtype);
        }

        //persist specific orders if exists
//        $entity = $this->processSpecificOrders($entity);
        //echo "scanorder=".$entity->getScanorder()."<br>";
        //echo "laborder=".$entity->getLaborder()."<br>";
        //echo "slideReturnRequest=".$entity->getSlideReturnRequest()."<br>";
        //exit('exit');

        
        if( $entity->getPriority() == "Routine" ) {      
            $entity->setDeadline(NULL);
        }

        //********** take care of educational and research director and principal investigator ***********//
        $entity = $em->getRepository('OlegOrderformBundle:Educational')->processEntity( $entity, $this->user );
        $entity = $em->getRepository('OlegOrderformBundle:Research')->processEntity( $entity, $this->user );
        //********** end of educational and research processing ***********//

        //return $this->setOrderInfoResultTopToBottom( $entity );
        return $this->setOrderInfoResultBottomToTop( $entity );
    }

    //process objects from bottom (slide level) to top (patient level)
    public function setOrderInfoResultBottomToTop( $entity ) {

        $em = $this->_em;
        $this->setSlides($entity);

        $slides = $entity->getSlide();
        echo "slide count=".count($slides)."<br>";

        if( count($slides) == 0 ) {
            throw new \Exception( 'Order does not have any slides. Slide count='.count($slides) );
        }

        //now clean orderinfo from patients. Patients and all others objects will be added only via slides.
        $entity->clearPatient();

        //process all slides
        foreach( $slides as $slide ) {
            //echo "<br>###################### Process Slide:".$slide;

            //set correct accession in case of accession-mrn conflict
            $em->getRepository('OlegOrderformBundle:Accession')->setCorrectAccessionIfConflict( $slide, $entity );

            //process slide
            $slide = $em->getRepository('OlegOrderformBundle:Slide')->processEntity( $slide, $entity );

            //set block and part names if not set (block and part name auto generation requires accession number to be set)
            $this->postProcessing($entity);

        }

        $originalStatus = $entity->getStatus();
        echo "originalStatus=".$originalStatus."<br>";

        if( $originalStatus == 'Not Submitted' ) {
            $entity->setOid(null);
        }

        //set original order date and provider to the orders with amend status
        if( $originalStatus == 'Amended' ) {

            $originalId = $entity->getOid();

            //find existing order in db
            $originalOrder = $em->getRepository('OlegOrderformBundle:OrderInfo')->findOneByOid($originalId);
            $originalOrderdate = $originalOrder->getOrderdate();
            $originalProvider = $originalOrder->getProvider();

            $entity->setId(null);
            $entity->setOid($originalId);

            //set orderdate from original order
            $entity->setOrderdate($originalOrderdate);

            //set provider from original order
            $entity->setProvider($originalProvider);
        }

//        echo "<br>################################## Finish:<br>";
//        echo "patients=".count($entity->getPatient())."<br>";
//        echo "encounters=".count($entity->getEncounter())."<br>";
//        echo "pat: encounters=".count($entity->getPatient()->first()->getEncounter())."<br>";
//        echo "procedures=".count($entity->getProcedure())."<br>";
//        echo "pat: procedures=".count($entity->getPatient()->first()->getEncounter()->first()->getProcedure())."<br>";
//        echo "accessions=".count($entity->getAccession())."<br>";
//        echo "pat: accessions=".count($entity->getPatient()->first()->getEncounter()->first()->getProcedure()->first()->getAccession())."<br>";
//        echo "parts=".count($entity->getPart())."<br>";
//        echo "pat: parts=".count($entity->getPatient()->first()->getEncounter()->first()->getProcedure()->first()->getAccession()->first()->getPart())."<br>";
//        echo "blocks=".count($entity->getBlock())."<br>";;
//        echo "slides=".count($entity->getSlide())."<br>";

//        foreach( $entity->getProcedure()->first()->getPatlastname() as $lastname ){
//            echo "procedure lastname=".$lastname.", id=".$lastname->getId().", status=".$lastname->getStatus()."<br>";
//        }
//
//        foreach( $entity->getPatient()->first()->getLastname() as $lastname ){
//            echo "patient lastname=".$lastname.", id=".$lastname->getId().", status=".$lastname->getStatus()."<br>";
//        }

        //echo "<br>patient:".$entity->getPatient()->first()."<br>";
        //echo "part's acc:".$entity->getPart()->first()->getAccession()."<br>";
        //echo "projectTitle name=".$entity->getResearch()."<br>";
        //echo "projectTitle setTitleStr=".$entity->getResearch()->getSetTitleStr()."<br>";
        //echo $entity->getBlock()->first();
        //echo $entity->getSlide()->first();

        //throw new \Exception('TESTING');
        //exit('orderinfo repoexit testing');

        //create new orderinfo
        //$em = $this->_em;
        $em->persist($entity);
        $em->flush();

        //insert oid to entity
        if( !$entity->getOid() ) {
            //echo "insert oid <br>";
            $entity->setOid($entity->getId());

            //if clear is used above => doctrine error: A new entity was found through the relationship 'Oleg\OrderformBundle\Entity\OrderInfo#patient' that was not configured to cascade persist operations
            //it is happened because all objects are not persisted anymore.
            $em->flush();
        }
        ////////////////////// finished save new orderinfo ///////////////////////////


        //final step for amend: swap newly created oid with Superseded order oid
        if( $originalStatus == 'Amended' ) {

            $newId = $entity->getId();

            $user = $em->getRepository('OlegUserdirectoryBundle:User')->findOneById($this->user->getId());

            //clone orderinfo object by id
            $orderUtil = $this->container->get('scanorder_utility');
            $message = $orderUtil->changeStatus($originalId, 'Supersede', $user, $newId);

            //now entity is a cloned order object
            //echo "rep: provider 3=".$entity->getProvider()."<br>";
            //$entity->setProvider($this->user);

            //swap oid
            $entity->setOid($originalId);

            //$em->persist($entity);
            $em->flush();
            $em->clear();
        }

        //*********** record history ***********//
        $entity = $em->getRepository('OlegOrderformBundle:OrderInfo')->findOneByOid($entity->getOid());
        $user = $em->getRepository('OlegUserdirectoryBundle:User')->findOneById($this->user->getId());
        $history = new History();
        $history->setOrderinfo($entity);
        $history->setCurrentid($entity->getOid());
        $history->setCurrentstatus($entity->getStatus());
        $history->setProvider($user);
        $history->setRoles($user->getRoles());
        $history->setCurrentstatus($entity->getStatus());

        //record history
        if( $originalStatus == 'Amended' ) {
            $eventtype = $em->getRepository('OlegOrderformBundle:ProgressCommentsEventTypeList')->findOneByName('Amended Order Submission');
            $history->setEventtype($eventtype);
            //get url link
            $supersedeId = $entity->getId(); //use id because superseded order and amended order have swaped ids
            $url = $this->router->generate( 'multy_show', array('id' => $supersedeId) );
            $link = '<a href="'.$url.'">order '.$supersedeId.'</a>';
            //set note with this url link
            $history->setNote('Previous order content saved as a Superseded '.$link);
        } elseif( $originalStatus == 'Not Submitted' ) {
            $userSecUtil = $this->container->get('user_security_utility');
            $systemUser = $userSecUtil->findSystemUser();
            $history->setProvider( $systemUser );
            $history->setNote('Auto-Saved Draft; Submit this order to Process');
            $eventtype = $em->getRepository('OlegOrderformBundle:ProgressCommentsEventTypeList')->findOneByName('Auto-saved at the time of auto-logout');
            $history->setEventtype($eventtype);
        } else {
            $eventtype = $em->getRepository('OlegOrderformBundle:ProgressCommentsEventTypeList')->findOneByName('Initial Order Submission');
            $history->setEventtype($eventtype);
            $history->setChangedate($entity->getOrderdate());
        }

        $em->persist($history);
        $em->flush();
        //*********** EOF record history ***********//

        $em->clear();

        //exit('end of order processing');
        //echo 'mem on end of order processing: ' . (memory_get_usage()/1024/1024) . "<br />\n";

        return $entity;
    }

    public function setSlides($orderinfo) {
        //echo "orderinfo=".$orderinfo."<br>";
        $patients = $orderinfo->getPatient();
        //echo "patient count=".count($patients)."<br>";
        foreach( $patients as $patient ) {
            $this->addSlidesToOrderinfo($orderinfo, $patient);
        }
    }
    public function addSlidesToOrderinfo($orderinfo, $entity) {

        //echo $entity;
        $children = $entity->getChildren();

        if( !$children || count($children) == 0  ) {

            if( $entity instanceof Slide ) {
                //echo "Add slide=".$entity."<br>";
                $orderinfo->addSlide($entity);

            } else {
                //echo "not slides<br>";
            }

            return;
        }

        foreach( $children as $child ) {
            $this->addSlidesToOrderinfo($orderinfo, $child);
        }

    }

    public function processSpecificOrders( $orderinfo ) {

        $category = $orderinfo->getType();

        if( !$category ) {
            $orderinfo->setScanorder(null);
            $orderinfo->setLaborder(null);
            $orderinfo->setSlideReturnRequest(null);
            return $orderinfo;
        }

        if( strpos($category,'Scan Order') !== false ) {
            //$orderinfo->setScanorder(null);
            $orderinfo->setLaborder(null);
            $orderinfo->setSlideReturnRequest(null);
        } else
        if( strpos($category,'Lab Order') !== false ) {
            $orderinfo->setScanorder(null);
            //$orderinfo->setLaborder(null);
            $orderinfo->setSlideReturnRequest(null);
        } else {

        }

        return $orderinfo;
    }

}
