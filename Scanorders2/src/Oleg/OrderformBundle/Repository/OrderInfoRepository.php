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
    
    //make correct object
    public function processEntity( $entity, $type ) {
        
//        echo "orderifno repos id=".$entity->getId()."<br>";
//        echo "orderifno repos provider=".$entity->getProvider()."<br>";\

        $em = $this->_em;

        //one way to solev multi duplicate entities to filter the similar entities. But for complex entities such as Specimen or Block it is not easy to filter duplicates out.
        //$entity = $this->removeDuplicateEntities($entity);
        $entity = $em->getRepository('OlegOrderformBundle:Patient')->removeDuplicateEntities( $entity );

        //set Status with Type and Group
        //$status = $em->getRepository('OlegOrderformBundle:Status')->setStatus('Submit');
        $status = $em->getRepository('OlegOrderformBundle:Status')->findOneByAction('Submit');
        //$entity->setStatus("active");
        $entity->setStatus($status);

        $entity->setType($type);

        $helper = new FormHelper();
        
        $slideDelivery = $helper->getSlideDelivery();
        $key = $entity->getSlideDelivery();
        if( isset($key) && $key >= 0 ) {
            $entity->setSlideDelivery( trim($slideDelivery[$key]) );
        }
        
        $returnSlide = $helper->getReturnSlide();
        $key = $entity->getReturnSlide();
        if( isset($key) && $key >= 0 ) {
            $entity->setReturnSlide( trim($returnSlide[$key]) );
        }
             
        $key = $entity->getPathologyService();   
        if( isset($key) && $key >= 0 ) {
            $pathologyService = $helper->getPathologyService();
            $entity->setPathologyService( trim($pathologyService[$key]) );
        }

        $key = $entity->getPriority();
        if( isset($key) && $key >= 0 ) {
            $priority = $helper->getPriority();
            $entity->setPriority( trim($priority[$key]) );
        }

        //echo "key=".$key."<br>";
//        echo "pathservice=".$entity->getPathologyService();
//        exit();
        
//        $patients = $entity->getPatient();
//        foreach( $patients as $patient ){
//            $patient = $em->getRepository('OlegOrderformBundle:Patient')->processEntity( $patient );
//        }
        
        //$em->persist($in_entity);
        //$em->flush();


        //return $entity;
        return $this->setResult( $entity );
    }
    
    public function setResult( $entity ) {
        
        $em = $this->_em;
        $em->persist($entity);      
        
        $patients = $entity->getPatient();
        //echo "patients count=".count($patients)."<br>";
        
        foreach( $patients as $patient ) {
            if( !$patient->getId() ) {
                //echo $patient;
                $entity->removePatient( $patient );
                $patient = $em->getRepository('OlegOrderformBundle:Patient')->processEntity( $patient, $entity );
                $entity->addPatient($patient);
            } else {
                continue;
            }
        }

        //echo "before orderinfo exit<br>";
        //exit();

        $em->flush();         
        return $entity;
    }

}
