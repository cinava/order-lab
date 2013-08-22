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

        //echo "key=".$key."<br>";
//        echo "pathservice=".$entity->getPathologyService();
//        exit();
        
        return $entity; 
    }
    
}
