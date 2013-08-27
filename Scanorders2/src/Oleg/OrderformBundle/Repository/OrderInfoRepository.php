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
//        echo "orderifno repos provider=".$entity->getProvider()."<br>";

        $entity->setStatus("active"); 
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
        
        return $entity; 
    }
       
}
