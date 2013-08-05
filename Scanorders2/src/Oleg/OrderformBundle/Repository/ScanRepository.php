<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Oleg\OrderformBundle\Helper\FormHelper;

/**
 * ScanRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ScanRepository extends EntityRepository
{
    
    //Make changes.
    public function processEntity( $in_entity ) { 
        
        $helper = new FormHelper();
        $mags = $helper->getMags();
        $key = $in_entity->getMag();
        //echo " value=".$mags[$key]."<br>";      
        $in_entity->setMag( $mags[$key] );
                  
        return;
    }
    
}
