<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Oleg\OrderformBundle\Helper\FormHelper;

/**
 * StainRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StainRepository extends EntityRepository
{
    
    //Make changes: 0 to H&E.
    public function processEntity( $in_entity ) { 
        
        $helper = new FormHelper();

        $stains = $helper->getStains();

        $key = $in_entity->getName();

        if( isset($key) && $key >= 0 ) {

            $stain = $stains[$key];
            $in_entity->setName( $stain );

//            if( $stain ) {
//                $in_entity->setName( $stain );
//            } else {
//                $in_entity->setName( $key );
//            }

        }

        return;
    }
    
}
