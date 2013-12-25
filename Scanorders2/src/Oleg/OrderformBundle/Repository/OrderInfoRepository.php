<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Oleg\OrderformBundle\Helper\OrderUtil;

/**
 * OrderInfoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderInfoRepository extends ArrayFieldAbstractRepository {
    
    //process orderinfo and all entities
    public function processOrderInfoEntity( $entity, $type=null ) {

        //echo "orderinfo: ".$entity."<br>";
//        echo "patients count=".count($entity->getPatient())."<br>";
//        $this->printTree( $entity->getPatient()->first() );

//        foreach( $entity->getPatient() as $patient ) {
//            echo "patient id=".$patient->getId()."<br>";
//            echo "patient mrn=".$patient->getMrn()->first()."<br>";
//            echo "patient name=".$patient->getName()->first()."<br>";
//            echo "patient oredreinfo count=".count($patient->getOrderinfo())."<br>";
//            echo "patient slide=".$patient->getProcedure()->first()->getAccession()->first()->getPart()->first()->getBlock()->first()->getSlide()->first()."<br>";
//              echo "patient accessions count =".count($patient->getProcedure()->first()->getAccession())."<br>";
//              echo "patient accession=".$patient->getProcedure()->first()->getAccession()->first()."<br>";
//
//            echo "patient count age=".count($patient->getAge())."<br>";
//            foreach( $patient->getAge() as $age ) {
//                echo "age: id=".$age->getId().", field=".$age."<br>";
//            }
//            echo "patient count clinicalHistory=".count($patient->getClinicalHistory())."<br>";
//            foreach( $patient->getClinicalHistory() as $ch ) {
//                echo "ch: id=".$ch->getId().", field=".$ch."<br>";
//            }
//        }
//        exit();

        $em = $this->_em;

        //one way to solev multi duplicate entities to filter the similar entities. But for complex entities such as Specimen or Block it is not easy to filter duplicates out.
        //$entity = $em->getRepository('OlegOrderformBundle:Patient')->removeDuplicateEntities( $entity );

        //set Status with Type and Group
        $status = $em->getRepository('OlegOrderformBundle:Status')->findOneByAction('Submit');
        $entity->setStatus($status);

        if( $type ) {
            $entity->setType($type);
        }

        //return $entity;
        return $this->setOrderInfoResult( $entity );
    }
    
    public function setOrderInfoResult( $entity ) {       

        $em = $this->_em;

        $patients = $entity->getPatient();
        //echo $patients->first();

        //process data quality
        foreach( $entity->getDataquality() as $dataquality) {

            //set correct mrntype
            $mrntype = $em->getRepository('OlegOrderformBundle:MrnType')->findOneById( $dataquality->getMrntype() );
            $dataquality->setMrntype($mrntype);
            $dataquality->setOrderinfo($entity);
            $dataquality->setProvider($entity->getProvider()->first());
            $dataquality->setStatus('active');

//            echo "dataquality: description=".$dataquality->getDescription()."<br>";
//            echo "dataquality: accession=".$dataquality->getAccession()."<br>";
//            echo "dataquality: mrn=".$dataquality->getMrn()."<br>";
//            echo "dataquality: mrn text=".$dataquality->getMrntype()."<br>";

        } //foreach

        //echo "patients count=".count($patients)."<br>";
//        echo "dataquality count=".count($entity->getDataquality())."<br>";
//        if( count($entity->getDataquality()) > 0 ) {
//            echo "dataquality: description=".$entity->getDataquality()->first()->getDescription()."<br>";
//            echo "dataquality: accession=".$entity->getDataquality()->first()->getAccession()."<br>";
//            echo "dataquality: mrn=".$entity->getDataquality()->first()->getMrn()."<br>";
//            echo "dataquality: mrn text=".$entity->getDataquality()->first()->getMrntype()."<br>";
//        }

        foreach( $patients as $patient ) {
            //echo "before patient oredreinfo count=".count($patient->getOrderinfo())."<br>";
            $entity->removePatient($patient);
            $patient = $em->getRepository('OlegOrderformBundle:Patient')->processEntity( $patient, $entity, "Patient", "mrn", "Procedure" );
            $entity->addPatient($patient);
        }
       
        //echo "<br><br>final patients count=".count($entity->getPatient())."<br>";
        //foreach( $entity->getPatient() as $patient ) {
//            echo 'patient nameCount='.count($patient->getName())." :".$patient->getName()->first().", status=".$patient->getName()->first()->getStatus()."<br>";
//////            echo 'patient orderinfo count='.count($patient->getOrderinfo())."<br>";
//////            //echo 'patient orderinfo='.$patient->getOrderinfo()->first()->getId()."<br>";
//            echo 'orderinfo patient ='.$entity->getPatient()->first()->getName()->first()."<br>";
            //echo $patient;
////            echo "patient accessions count =".count($patient->getProcedure()->first()->getAccession())."<br>";
////            echo "patient parts count =".count($patient->getProcedure()->first()->getAccession()->first()->getPart())."<br>";
////            //echo "patient accession=".$patient->getProcedure()->first()->getAccession()->first()."<br>";
            //echo "<br>--------------------------<br>";
            //$this->printTree( $patient );
            //echo "--------------------------<br>";
        //}

        exit('orderinfo repo exit');

        //TODO: make copy of orderinfo
        if( $entity->getCicle() == 'amend' ) {
                   
            $originalId = $entity->getId();
            //echo "originalId=".$originalId."<br>";
            
            $newOrderinfo = clone $entity;
            $newOrderinfo->setCicle('submit');
            $newOrderinfo->setId(null);
            $newOrderinfo->setOriginalid($originalId);
            
            $orderUtil = new OrderUtil($em);
            $message = $orderUtil->changeStatus($originalId, 'Amend');
            
            //$entity->setId(null);
                      
//            echo "orig id=".$entity->getId().",:".$entity."<br>";
//            echo "newOrderinfo id=".$newOrderinfo->getId().",:".$newOrderinfo."<br>";
//            
//            echo "<br><br>final patients count=".count($newOrderinfo->getPatient())."<br>";
//            echo "<br>--------------------------<br>";
//            $this->printTree( $newOrderinfo->getPatient()->first() );
//            echo "--------------------------<br>";
                       
            $entity = $newOrderinfo;
        }
                             
        //create new orderinfo
        $em->persist($entity);
        $em->flush();                     
        
        //clean empty blocks
        //TODO: do it in part repository
        $blocks = $entity->getBlock();
        foreach( $blocks as $block ) {
            if( count($block->getSlide()) == 0 ) {
                //echo "final remove block from orderinfo: ".$block;
                $em->remove($block);
                $em->flush();
            }
        }
        
//        if( $entity->getCicle() == 'amend' ) {
//            echo "update entity with amend=".$entity."<br>";
//            $entity->setId(null);           
//            $entity->setOriginalid($originalId);
//            $em->persist($entity);
//            $em->flush();
//        }

        //$em->clear();
        return $entity;
    }

}
