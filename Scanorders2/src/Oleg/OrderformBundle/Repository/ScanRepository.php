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

        //echo " key=".$key."<br>";
        //echo " value=".$mags[$key]."<br>";

        if( isset($key) && $key >= 0 ) {
            //echo "set name";
            $in_entity->setMag( $mags[$key] );
        }
        
        //TODO: change to object Status.php
        $in_entity->setStatus("Submitted");

        //create new
        $em = $this->_em;
        $em->persist($in_entity);
        echo "unit of work size=".$em->getUnitOfWork()->size()."<br>";
        $em->flush($in_entity);

        return $in_entity;
    }
    
}
