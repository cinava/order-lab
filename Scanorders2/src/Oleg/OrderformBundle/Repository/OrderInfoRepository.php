<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;
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

    //process orderinfo and all entities
    public function processOrderInfoEntity( $entity, $user, $type=null, $router = null ) {

        gc_enable();
        $em = $this->_em;
        //$em->getConnection()->getConfiguration()->setSQLLogger(null);

        //echo "memory limit=".ini_get("memory_limit")."<br>";
        //echo 'mem: ' . (memory_get_usage()/1024/1024) . "<br />\n";
        //exit();

        $this->user = $user;
        $this->router = $router;

        //replace duplicate entities to filter the similar entities.
        //$entity = $em->getRepository('OlegOrderformBundle:Patient')->replaceDuplicateProcedures( $entity, $entity );
        $entity = $this->replaceDuplicateEntities( $entity, $entity );

        //set Status with Type and Group
        //$status = $em->getRepository('OlegOrderformBundle:Status')->findOneByAction('Submit');
        //$entity->setStatus($status);

//        $blocks = $entity->getPatient()->first()->getProcedure()->first()->getAccession()->first()->getPart()->first()->getBlock();
//        echo "<br>############################## Start: blocks=".count($blocks).":<br>";
//        foreach($blocks as $block ) {
//            echo $block;
//        }
//        echo "##################################<br><br>";

        if( $type ) {
            $formtype = $em->getRepository('OlegOrderformBundle:FormType')->findOneByName( $type );
            $entity->setType($formtype);
        }
        
        if( $entity->getPriority() == "Routine" ) {      
            $entity->setScandeadline(NULL);
        }

        return $this->setOrderInfoResult( $entity );
    }
    
    public function setOrderInfoResult( $entity ) {

        $em = $this->_em;

        $patients = $entity->getPatient();

        //********** take care of educational and research director and principal investigator ***********//
        $entity = $em->getRepository('OlegOrderformBundle:Educational')->processEntity( $entity, $this->user );
        $entity = $em->getRepository('OlegOrderformBundle:Research')->processEntity( $entity, $this->user );
        //********** end of educational and research processing ***********//

        //echo "Count of patients=".count($patients)."<br>";

        foreach( $patients as $patient ) {

            //testing
//            $procedures = $patient->getChildren();
//            foreach( $procedures as $procedure ) {
//                echo "orig proc=".$procedure;
//                echo "orig keytype count=".count($procedure->obtainAllKeyfield())."<br>";
//                echo "orig first key=".$procedure->obtainAllKeyfield()->first()->getField().", orig first keytype=".$procedure->obtainAllKeyfield()->first()->getKeytype()."<br>";
//            }

            //make sure to remove name, sex and age from patient original object from form if id is null.
            //Those fields in Patient are just for displaying existing info.
            //$this->removeDisplayFields($patient);

            //echo "before patient oredreinfo count=".count($patient->getOrderinfo())."<br>";
            $entity->removePatient($patient);
            $patient = $em->getRepository('OlegOrderformBundle:Patient')->processEntity( $patient, $entity, "Patient", "mrn", "Procedure" );

            //$entity->addPatient($patient);
            //add children
            $em->getRepository('OlegOrderformBundle:Patient')->attachToParent( $entity, $patient );

            //save patient to db
            //$em->persist($patient);
            //$em->flush();
            //$em->clear();
        }

        //add slide's parents recursevely to this orderinfo
        $slides = $entity->getSlide();
        foreach( $slides as $slide ) {
            $this->addOrderinfoToThisAndAllParents( $slide, $entity );
        }

//        echo "<br><br>final patients count=".count($entity->getPatient())."<br>";
//        foreach( $entity->getPatient() as $patient ) {
//            foreach( $patient->getName() as $name ) {
//                echo "name=".$name."<br>";
//            }
//            echo 'patient nameCount='.count($patient->getName())." :".$patient->getName()->first().", status=".$patient->getName()->first()->getStatus()."<br>";
//////            echo 'patient orderinfo count='.count($patient->getOrderinfo())."<br>";
//////            //echo 'patient orderinfo='.$patient->getOrderinfo()->first()->getId()."<br>";
//            echo 'orderinfo patient ='.$entity->getPatient()->first()->getName()->first()."<br>";
//            echo $patient;
////            echo "patient accessions count =".count($patient->getProcedure()->first()->getAccession())."<br>";
////            echo "patient parts count =".count($patient->getProcedure()->first()->getAccession()->first()->getPart())."<br>";
////            //echo "patient accession=".$patient->getProcedure()->first()->getAccession()->first()."<br>";
//            echo "<br>--------------------------<br>";
//            $this->printTree( $patient );
//            echo "--------------------------<br>";
//        }

//        echo $entity;
//        $research = $entity->getResearch();
//        echo "<br>Res count=".count($research)."<br>";
//        $projectTitle = $research->getProjectTitle();
//        echo "projectTitle=".$projectTitle."<br>";
//        echo "projectTitle Id=".$projectTitle->getId()."<br>";
//        echo "projectTitle Type=".$projectTitle->getType()."<br>";
//        echo "count(setTitle)=".count($projectTitle->getSetTitles())."<br>";
//        echo "setTitle1=".$projectTitle->getSetTitles()->first()."<br>";
//        echo "<br>Accession count=".count($patient->getProcedure()->first()->getAccession())."<br>";
//        $acc = $patient->getProcedure()->first()->getAccession()->first();
//        echo "number=".$acc->obtainValidKeyField()."<br>";
//        echo "original=".$acc->obtainValidKeyField()->getOriginal()."<br>";
//        echo "keytype=".$acc->obtainValidKeyField()->getKeytype()."<br>";
//
//        echo "<br>Patient count=".count($patients)."<br>";
//        echo "number=".$patient->obtainValidKeyField()."<br>";
//        echo "original=".$patient->obtainValidKeyField()->getOriginal()."<br>";
//        echo "keytype=".$patient->obtainValidKeyField()->getKeytype()."<br>";
//
        //exit('orderinfo repoexit');

        $originalStatus = $entity->getStatus();
        //echo "status=".$originalStatus."<br>";
        //exit();

        if( $originalStatus == 'Not Submitted' ) {
            $entity->setOid(null);
        }

        if( $originalStatus == 'Amended' ) {

            $originalId = $entity->getOid();

            //find existing order in db
            $originalOrder = $em->getRepository('OlegOrderformBundle:OrderInfo')->findOneByOid($originalId);
            $originalOrderdate = $originalOrder->getOrderdate();

            $entity->setId(null);
            $entity->setOid($originalId);
        }

//        //echo "proxy user=".$entity->getProxyuser()."<br>";
//        echo "<br><br>final patients count=".count($entity->getPatient())."<br>";
//        echo $entity->getPatient()->first();
//        echo "<br>--------------------------<br>";
//        $this->printTree( $entity->getPatient()->first() );
//        echo "--------------------------<br>";

//        echo "<br><br>final slide count=".count($entity->getSlide())."<br>";
//        foreach( $entity->getSlide() as $elem ) {
//            echo $elem;
//        }

//        echo "<br>################################## Finish:<br>";
//        echo $entity->getPatient()->first()."<br>";
//        echo "final pat name count=".count($entity->getPatient()->first()->getName())."<br>";
//
//        echo "patients=".count($entity->getPatient())."<br>";
//        echo "procedures=".count($entity->getProcedure())."<br>";
//        echo "accessions=".count($entity->getAccession())."<br>";
//        echo "parts=".count($entity->getPart())."<br>";
//        echo "blocks=".count($entity->getBlock())."<br>";
//        echo "slides=".count($entity->getSlide())."<br>";
//        echo "block= ".$entity->getBlock()->first();
        //echo "acc's procedure=".$entity->getAccession()->first()->getParent();
//        echo $entity;
//        foreach( $entity->getBlock() as $elem ) {
//            echo $elem;
//        }
//        echo "proc acc count=".count($entity->getProcedure()->first()->getAccession()),"<br>";
//        foreach( $entity->getBlock() as $child ) {
//            echo "Res block=".$child."<br>";
//        }
//        foreach($entity->getAccession() as $acc) {
//            echo $acc->getParent()."=>";
//            echo $acc;
//        }
//        foreach($entity->getPatient()->first()->getName() as $el) {
//            echo "name=".$el."<br>";
//        }
//        foreach($entity->getPatient()->first()->getAge() as $el) {
//            echo "age=".$el."<br>";
//        }
//        foreach($entity->getPatient()->first()->getSex() as $el) {
//            echo "sex=".$el."<br>";
//        }

        //echo 'mem on order save: ' . (memory_get_usage()/1024/1024) . "<br />\n";
        //exit('orderinfo repoexit testing');

        //create new orderinfo
        //$em = $this->_em;
        $em->persist($entity);
        $em->flush();
        //TODO: amend order with new elements added cause: The connection was reset
        //$em->clear();   //test clear

        //echo 'mem before insert oid: ' . (memory_get_usage()/1024/1024) . "<br />\n";
        //exit('before insert oid');

        //insert oid to entity
        if( !$entity->getOid() ) {
            //echo "insert oid <br>";
            $entity->setOid($entity->getId());

            //if clear is used above => doctrine error: A new entity was found through the relationship 'Oleg\OrderformBundle\Entity\OrderInfo#patient' that was not configured to cascade persist operations
            //it is happened because all objects are not persisted anymore.
            $em->flush();
        }

        //clean empty blocks
        $blocks = $entity->getBlock();
        foreach( $blocks as $block ) {
            if( count($block->getSlide()) == 0 ) {
                //echo "final remove block from orderinfo: ".$block;
                $em->remove($block);
                $em->persist($block);
                $em->flush();
            }
        }
        $em->clear();
        ////////////////////// finished save new orderinfo ///////////////////////////


        //final step for amend: swap newly created oid with Superseded order oid
        if( $originalStatus == 'Amended' ) {

            //exit('amended orderinfo repoexit');
            $newId = $entity->getId();

            $user = $em->getRepository('OlegOrderformBundle:User')->findOneById($this->user->getId());

            //clone orderinfo object by id
            $orderUtil = new OrderUtil($em);
            $message = $orderUtil->changeStatus($originalId, 'Supersede', $user, $this->router, $newId);

            //now entity is a cloned order object
            //echo "rep: provider 3=".$entity->getProvider()."<br>";
            //$entity->setProvider($this->user);

            //swap oid
            $entity->setOid($originalId);

            //set orderdate from original order
            $entity->setOrderdate($originalOrderdate);

            //$em->persist($entity);
            $em->flush();
            $em->clear();
        }

        //*********** record history ***********//
        $entity = $em->getRepository('OlegOrderformBundle:OrderInfo')->findOneByOid($entity->getOid());
        $user = $em->getRepository('OlegOrderformBundle:User')->findOneById($this->user->getId());
        $history = new History();
        $history->setOrderinfo($entity);
        $history->setCurrentid($entity->getOid());
        $history->setCurrentstatus($entity->getStatus());
        $history->setProvider($user);
        $history->setRoles($user->getRoles());
        $history->setCurrentstatus($entity->getStatus());

        if( $originalStatus == 'Amended' ) {
            $history->setEventtype('Amended Order Submission');
            //get url link
            $supersedeId = $entity->getId();
            $url = $this->router->generate( 'multy_show', array('id' => $supersedeId) );
            $link = '<a href="'.$url.'">order '.$supersedeId.'</a>';
            //set note with this url link
            $history->setNote('Previous order content saved as a Superseded '.$link);
        } elseif( $originalStatus == 'Not Submitted' ) {
            $systemUser = $em->getRepository('OlegOrderformBundle:User')->findOneByUsername('system');
            $history->setProvider( $systemUser );
            $history->setNote('Auto-Saved Draft; Submit this order to Process');
            $history->setEventtype('Auto-saved at the time of auto-logout');
        } else {
            $history->setEventtype('Initial Order Submission');
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


    public function addOrderinfoToThisAndAllParents( $entity, $orderinfo ) {
        $className = new \ReflectionClass($entity);
        $shortClassName = $className->getShortName();
        $addClassMethod = "add".$shortClassName;    //"addPatient"

        if( $shortClassName == 'Patient' ) {    //don't add patient because it was added at the beginning on the controller
            return false;
        }

        //echo '<br>adding '.$shortClassName."<br>";

        //add if this object does not have yet this orderinfo (id=null)
        $orders = $entity->getOrderinfo();
        //echo "orders=".count($orders)."<br>";

        //if at least one order of this object does not have id => new order => this order => return true
        $thisOrderinfoCount = 0;
        foreach( $orders as $order ) {
            //echo "order id=".$order->getId()."<br>";
            if( $order->getId() && $order->getId() != '' ) {
                //echo "object has orderinfo with ID <br>";
            } else {
                $thisOrderinfoCount++;
                //echo "order no ID !!!!!!!!!!!!!!!!!!!!!!!!!!!!<br>";
            }
        }

        //echo $shortClassName.': thisOrderinfoCount='.$thisOrderinfoCount.'<br>';
        if( $thisOrderinfoCount == 0 ) {
            $orderinfo->$addClassMethod($entity);
        }

        $parent = $entity->getParent();
        //echo "parent = ".$parent;
        if( $parent ) {
            $this->addOrderinfoToThisAndAllParents( $parent, $orderinfo );
        }
    }


    public function removeDisplayFields($patient) {

        //name
        if( count($patient->getLastname()) > 1 ) {
            throw new \Exception('Patient has multiple field name, count='.count($patient->getLastname()));
        }
        $name = $patient->getLastname()->first();
        if( $name && !$name->getId() ) {
            $patient->removeLastname($name);
        }

        //age
        if( count($patient->getAge()) > 1 ) {
            throw new \Exception('Patient has multiple field age, count='.count($patient->getAge()));
        }
        $age = $patient->getAge()->first();
        if( $age && !$age->getId() ) {
            $patient->removeAge($age);
        }

        //sex
        if( count($patient->getSex()) > 1 ) {
            throw new \Exception('Patient has multiple field sex, count='.count($patient->getSex()));
        }
        $sex = $patient->getSex()->first();
        if( $sex && !$sex->getId() ) {
            $patient->removeSex($sex);
        }

    }

}
