<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;

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
        
        if( $accession == null ) {      
            $em->persist($part);
            $em->flush();
            
            return $part;
        }
        
        //check if accession already has part with the same name. TODO: is it correct to find by part as an object
//        $accession_found = $em->getRepository('OlegOrderformBundle:Accession')->findOneBy( array(
//            'accession' => $accession->getAccession(),
//            'part' => $part
//        ));
        $part_found = $em->getRepository('OlegOrderformBundle:Part')->findOneBy( array(
            'accession' => $accession,
            'name' => $part->getName()
        ));
        
        
        if( $part_found == null ) {
            $em->persist($part);
            $em->flush();
            
            return $part;
        }
        
        if( $part_found->getName() != $part->getName() ) {
            $em->persist($part);
            $em->flush();
            
            return $part;
        }
        
        
        return $part_found; 
    }
    
}
