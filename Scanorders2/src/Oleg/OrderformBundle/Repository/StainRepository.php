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

        //create new
        $em = $this->_em;
        $em->persist($in_entity);
        //$em->flush($in_entity);

        return $in_entity;
    }
    
}
