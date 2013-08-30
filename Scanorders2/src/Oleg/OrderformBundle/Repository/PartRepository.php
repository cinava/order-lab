<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Oleg\OrderformBundle\Helper\FormHelper;

/**
 * PartRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PartRepository extends EntityRepository
{
    
    //this function will create an entity if it doesn't exist or return the existing entity object
    public function processEntity( $part, $accession=null ) {  
        
        $em = $this->_em;
        
        $helper = new FormHelper();        

//        $key = $part->getSourceOrgan();
//        if( isset($key) && $key >= 0 ) {
//            $sourceOrgan = $helper->getSourceOrgan();
//            $part->setSourceOrgan( $sourceOrgan[$key] );
//        }
        
        $key = $part->getName();
        if( isset($key) && $key >= 0 ) {
            $name = $helper->getPart();
            $part->setName( $name[$key] );
        }
        
        if( $accession == null || $accession->getId() == null ) {
            $em->persist($part);
            //$em->flush();
            return $part;
        }
        
        //check if accession already has part with the same name.
        $part_found = $em->getRepository('OlegOrderformBundle:Part')->findOneBy( array(
            'accession' => $accession,
            'name' => $part->getName()
        ));
        
        
        if( $part_found == null ) {
            $em->persist($part);
            //$em->flush();
            return $part;
        }
        
        if( $part_found->getName() != $part->getName() ) {
            $em->persist($part);
            //$em->flush();
            return $part;
        }

        //copy all children to existing entity
        foreach( $part->getBlock() as $block ) {
            $part_found->addBlock( $block );                   
        }
//        foreach( $part->getSlide() as $slide ) {
//            $part_found->addSlide( $slide );
//        }

        $em->persist($part_found);
        return $part_found; 
    }
    
}
