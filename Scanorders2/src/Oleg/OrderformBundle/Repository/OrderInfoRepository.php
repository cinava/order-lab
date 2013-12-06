<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Oleg\OrderformBundle\Helper\FormHelper;

/**
 * OrderInfoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderInfoRepository extends EntityRepository
{
    
    //process orderinfo and all entities
    public function processEntity( $entity, $type ) {

//        echo "patients count=".count($entity->getPatient())."<br>";
//        foreach( $entity->getPatient() as $patient ) {
//            echo "patient mrn=".$patient->getMrn()->first()."<br>";
//            echo "patient name=".$patient->getName()->first()."<br>";
//            echo "patient slide=".$patient->getProcedure()->first()->getAccession()->first()->getPart()->first()->getBlock()->first()->getSlide()->first()."<br>";
//        }
        //exit();

        $em = $this->_em;

        //one way to solev multi duplicate entities to filter the similar entities. But for complex entities such as Specimen or Block it is not easy to filter duplicates out.
        $entity = $em->getRepository('OlegOrderformBundle:Patient')->removeDuplicateEntities( $entity );

        //set Status with Type and Group
        $status = $em->getRepository('OlegOrderformBundle:Status')->findOneByAction('Submit');
        $entity->setStatus($status);

        $entity->setType($type);

        $helper = new FormHelper();

        $key = $entity->getSlideDelivery();
        if (is_numeric($key)) {
            $slideDelivery = $helper->getSlideDelivery();
            if( isset($key) && $key >= 0 ) {
                $entity->setSlideDelivery( trim($slideDelivery[$key]) );
            }
        }

        $key = $entity->getReturnSlide();
        if (is_numeric($key)) {
            $returnSlide = $helper->getReturnSlide();
            if( isset($key) && $key >= 0 ) {
                $entity->setReturnSlide( trim($returnSlide[$key]) );
            }
        }

        $key = $entity->getPriority();
        if( isset($key) && $key >= 0 ) {
            $priority = $helper->getPriority();
            $entity->setPriority( trim($priority[$key]) );
        }

        //return $entity;
        return $this->setResult( $entity );
    }
    
    public function setResult( $entity ) {
        
        $em = $this->_em;
        $em->persist($entity);      
        
        $patients = $entity->getPatient();

        echo "patients count=".count($patients)."<br>";

        foreach( $patients as $patient ) {
            $patient = $em->getRepository('OlegOrderformBundle:Patient')->processEntity( $patient, $entity, "Patient", "mrn", "Procedure" );
            //$entity->addPatient($patient);
        }

        $em->flush();         
        return $entity;
    }

}
