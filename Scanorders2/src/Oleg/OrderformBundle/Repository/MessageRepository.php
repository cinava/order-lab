<?php
/**
 * Copyright (c) 2017 Cornell University
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Oleg\OrderformBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

use Oleg\OrderformBundle\Entity\Slide;
use Oleg\OrderformBundle\Entity\History;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends ArrayFieldAbstractRepository {

    protected $user;
    protected $router;
    protected $container;

    //process message and all entities
    public function processMessageEntity( $entity, $user, $type, $router, $container ) {

        gc_enable();
        $em = $this->_em;
        //$em->getConnection()->getConfiguration()->setSQLLogger(null);

        $this->user = $user;
        $this->router = $router;
        $this->container = $container;

        //replace duplicate entities to filter the similar entities.
        $entity = $this->replaceDuplicateEntities( $entity, $entity );

        if( $type && !$entity->getMessageCategory() ) {
            $category = $em->getRepository('OlegOrderformBundle:MessageCategory')->findOneByName( $type );
            $entity->setMessageCategory($category);
        }

        //persist specific orders if exists
//        $entity = $this->processSpecificOrders($entity);
        //echo "scanorder=".$entity->getScanorder()."<br>";
        //echo "laborder=".$entity->getLaborder()."<br>";
        //echo "slideReturnRequest=".$entity->getSlideReturnRequest()."<br>";
        //exit('exit');
        //echo "message inst=".$entity->getInstitution()."<br>";
        //exit('1');

        
        if( $entity->getPriority() == "Routine" ) {      
            $entity->setDeadline(NULL);
        }

        //********** take care of educational and research director and principal investigator ***********//
        $entity = $em->getRepository('OlegOrderformBundle:Educational')->processEntity( $entity, $this->user );
        $entity = $em->getRepository('OlegOrderformBundle:Research')->processEntity( $entity, $this->user );
        //********** end of educational and research processing ***********//

        //return $this->setMessageResultTopToBottom( $entity );
        return $this->setMessageResultBottomToTop( $entity );
    }

    //process objects from bottom (slide level) to top (patient level)
    public function setMessageResultBottomToTop( $entity ) {

        $em = $this->_em;
        $this->setSlides($entity);

        $slides = $entity->getSlide();
        echo "slide count=".count($slides)."<br>";

        if( count($slides) == 0 ) {
            throw new \Exception( 'Order does not have any slides. Slide count='.count($slides) );
        }

        //now clean message from patients. Patients and all others objects will be added only via slides.
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
            $originalOrder = $em->getRepository('OlegOrderformBundle:Message')->findOneByOid($originalId);
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
//        echo "first patient=".$entity->getPatient()->first()."<br>";
//        echo "encounters=".count($entity->getEncounter())."<br>";
//        echo "pat: encounters=".count($entity->getPatient()->first()->getEncounter())."<br>";
//        echo "first encounter=".$entity->getPatient()->first()->getEncounter()->first()."<br>";
//        echo "procedures=".count($entity->getProcedure())."<br>";
//        echo "pat: procedures=".count($entity->getPatient()->first()->getEncounter()->first()->getProcedure())."<br>";
//        echo "first procedure=".$entity->getPatient()->first()->getEncounter()->first()->getProcedure()->first()."<br>";
//        echo "accessions=".count($entity->getAccession())."<br>";
//        echo "pat: accessions=".count($entity->getPatient()->first()->getEncounter()->first()->getProcedure()->first()->getAccession())."<br>";
//        echo "first accession=".$entity->getPatient()->first()->getEncounter()->first()->getProcedure()->first()->getAccession()->first()."<br>";
//        echo "parts=".count($entity->getPart())."<br>";
//        echo "pat: parts=".count($entity->getPatient()->first()->getEncounter()->first()->getProcedure()->first()->getAccession()->first()->getPart())."<br>";
//        echo "first part=".$entity->getPatient()->first()->getEncounter()->first()->getProcedure()->first()->getAccession()->first()->getPart()->first()."<br>";
//        echo "blocks=".count($entity->getBlock())."<br>";
//        $firstBlock = $entity->getPatient()->first()->getEncounter()->first()->getProcedure()->first()->getAccession()->first()->getPart()->first()->getBlock()->first();
//        echo "first block=".$firstBlock."<br>";
//        echo "slides=".count($entity->getSlide())."<br>";
//        echo "first slide=".$entity->getSlide()->first()."<br>";
//
//        echo "block staintype count=".count($firstBlock->getSpecialStains())."<br>";
//        foreach( $firstBlock->getSpecialStains() as $specialStain ) {
//            echo "block staintype field=".$specialStain->getField()."<br>";
//            echo "block staintype staintype=".$specialStain->getStaintype()."<br>";
//        }

//        foreach( $entity->getProcedure()->first()->getPatlastname() as $lastname ){
//            echo "procedure lastname=".$lastname.", id=".$lastname->getId().", status=".$lastname->getStatus()."<br>";
//        }
//
//        foreach( $entity->getPatient()->first()->getLastname() as $lastname ){
//            echo "patient lastname=".$lastname.", id=".$lastname->getId().", status=".$lastname->getStatus()."<br>";
//        }

//        echo "<br>patient:".$entity->getPatient()->first()."<br>";
//        echo "part's acc:".$entity->getPart()->first()->getAccession()."<br>";
//        //echo "projectTitle name=".$entity->getResearch()."<br>";
//        //echo "projectTitle setTitleStr=".$entity->getResearch()->getSetTitleStr()."<br>";
//        echo $entity->getBlock()->first();
//        echo $entity->getSlide()->first();

        //throw new \Exception('TESTING');
        //exit('message repoexit testing');


        ////////////////////// create new message //////////////////////
        //$em = $this->_em;
        $em->persist($entity);
        $em->flush();
        ////////////////////// EOF create new message //////////////////////


        //set all slides as inputs
        foreach($entity->getSlide() as $slide) {
            //set this slide as order input
            $entity->addInputObject($slide);
        }

        //insert oid to entity
//        if( !$entity->getOid() ) {
//            //echo "insert oid <br>";
//            $entity->setOid($entity->getId());
//
//            //if clear is used above => doctrine error: A new entity was found through the relationship 'Oleg\OrderformBundle\Entity\Message#patient' that was not configured to cascade persist operations
//            //it is happened because all objects are not persisted anymore.
//            $em->flush();
//        }
//        echo "after inserting oid entity=".$entity."<br>";
        ////////////////////// finished save new message ///////////////////////////


        //final step for amend: swap newly created oid with Superseded order oid
        if( $originalStatus == 'Amended' ) {

            $newId = $entity->getId();

            $user = $em->getRepository('OlegUserdirectoryBundle:User')->findOneById($this->user->getId());

            //clone message object by id
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
        //echo "before find entity=".$entity."<br>";
        $entity = $em->getRepository('OlegOrderformBundle:Message')->findOneByOid($entity->getOid());
        $user = $em->getRepository('OlegUserdirectoryBundle:User')->findOneById($this->user->getId());
        $history = new History();
        $history->setMessage($entity);
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
            //$history->setChangedate($entity->getOrderdate());
        }

        $em->persist($history);
        $em->flush();
        //*********** EOF record history ***********//

        $em->clear();

        //exit('end of order processing');
        //echo 'mem on end of order processing: ' . (memory_get_usage()/1024/1024) . "<br />\n";

        return $entity;
    }

    public function setSlides($message) {
        //echo "message=".$message."<br>";
        $patients = $message->getPatient();
        //echo "patient count=".count($patients)."<br>";
        foreach( $patients as $patient ) {
            $this->addSlidesToMessage($message, $patient);
        }
    }
    public function addSlidesToMessage($message, $entity) {

        //echo $entity;
        $children = $entity->getChildren();

        if( $entity instanceof Slide ) {
            //echo "Add slide=".$entity."<br>";
            $message->addSlide($entity);

        } else {
            //echo "not slides =>".$entity."<br>";
        }

        if( !$children || count($children) == 0  ) {
            return;
        }

        foreach( $children as $child ) {
            $this->addSlidesToMessage($message, $child);
        }

    }

    //not used: TODELETE
    public function processSpecificOrders( $message ) {

        $categoryName = $message->getMessageCategory()->getName();

        if( !$categoryName ) {
            $message->setScanorder(null);
            $message->setLaborder(null);
            $message->setSlideReturnRequest(null);
            return $message;
        }

        if( strpos($categoryName,'Scan Order') !== false ) {
            //$message->setScanorder(null);
            $message->setLaborder(null);
            $message->setSlideReturnRequest(null);
        } else
        if( strpos($categoryName,'Lab Order') !== false ) {
            $message->setScanorder(null);
            //$message->setLaborder(null);
            $message->setSlideReturnRequest(null);
        } else {

        }

        return $message;
    }

    //if version is null => find by latest version
    public function findByOidAndVersion( $oid, $version=null ) {
        $message = null;
        $parameters = array();

        $repository = $this->_em->getRepository('OlegOrderformBundle:Message');
        $dql = $repository->createQueryBuilder("message");

        $dql->where("message.oid = :oid");
        $parameters['oid'] = $oid;

        if( $version ) {
            $dql->andWhere("message.version = :version");
            $parameters['version'] = $version;
        }

        $dql->orderBy('message.version','DESC');

        $query = $this->_em->createQuery($dql);
        $query->setParameters($parameters);
        $messages = $query->getResult();

        if( count($messages) > 0 ) {
            $message = $messages[0];
        }

        return $message;
    }

    public function findAllMessagesByOid( $oid, $exceptVersion=null ) {
        $message = null;
        $parameters = array();

        $repository = $this->_em->getRepository('OlegOrderformBundle:Message');
        $dql = $repository->createQueryBuilder("message");

        $dql->where("message.oid = :oid");
        $parameters['oid'] = $oid;

        if( $exceptVersion ) {
            $dql->andWhere("message.version != :exceptVersion");
            $parameters['exceptVersion'] = $exceptVersion;
        }

        $dql->orderBy('message.version','DESC');

        $query = $this->_em->createQuery($dql);
        $query->setParameters($parameters);
        $messages = $query->getResult();

        return $messages;
    }

    public function findLatestMessageByOid( $oid, $messages=null ) {
        $message = null;

        if( !$messages ) {
            $messages = $this->findAllMessagesByOid($oid);
        }

        if( count($messages) > 0 ) {
            $message = $messages[0];
        }

        return $message;
    }

}
