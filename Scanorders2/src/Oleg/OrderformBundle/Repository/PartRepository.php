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
    public function processEntity( $part, $accession=null, $orderinfo=null ) {
        
        $em = $this->_em;

        $part = $em->getRepository('OlegOrderformBundle:Block')->removeDuplicateEntities( $part );
        
//        $helper = new FormHelper();
//        $key = $part->getName();
//        if( isset($key) && $key >= 0 ) {
//            $name = $helper->getPart();
//            $part->setName( $name[$key] );
//        }
        
        if( $accession == null || $accession->getId() == null ) {
            //$em->persist($part);
            //$em->flush();
            //return $part;
            $part = $this->setResult( $part, $orderinfo );
            return $part;
        }
        
        //check if accession already has part with the same name.
        $part_found = $em->getRepository('OlegOrderformBundle:Part')->findOneBy( array(
            'accession' => $accession,
            'name' => $part->getName()
        ));
        
        
        if( $part_found == null ) {
            //$em->persist($part);
            //$em->flush();
            //return $part;
            $part = $this->setResult( $part, $orderinfo );
            return $part;
        }
        
        if( $part_found->getName() != $part->getName() ) {
            //$em->persist($part);
            //$em->flush();
            //return $part;
            $part = $this->setResult( $part, $orderinfo );
            return $part;
        }

        //copy all children to existing entity
        foreach( $part->getBlock() as $block ) {
            $part_found->addBlock( $block );
        }
//        foreach( $part->getSlide() as $slide ) {
//            $part_found->addSlide( $slide );
//        }

//        $em->persist($part_found);
//        return $part_found; 
        $part = $this->setResult( $part_found, $orderinfo );
        return $part;
    }
    
    public function setResult( $part, $orderinfo ) {
        
        $em = $this->_em;
        $em->persist($part);

        if( $orderinfo == null ) {
            return $part;
        }

        $blocks = $part->getBlock();    
        
        foreach( $blocks as $block ) {
            if( $em->getRepository('OlegOrderformBundle:Block')->notExists($block) ) {
                $part->removeBlock( $block );
                $block = $em->getRepository('OlegOrderformBundle:Block')->processEntity( $block, $part, $orderinfo );
                $part->addBlock($block);
                $orderinfo->addBlock($block);
            } else {
                continue;
            }
        }      
        
        //$em->flush($part);
        
        return $part;
    }

    //filter out duplicate virtual (in form, not in DB) parts from accession
    //unique part can be identified by the accession and part name => same part has the same accession number and part name;
    //since we check the part for this particular accession, then use just part's name (?!)
    public function removeDuplicateEntities( $accession ) {

        $parts = $accession->getPart();

        if( count($parts) == 1 ) {
            return $accession;
        }

        $names = array();

        foreach( $parts as $part ) {

            $thisName = $part->getName();

            if( count($names) == 0 || !in_array($thisName, $names) ) {
                $names[] = $thisName;
                //persist the rest of entities, because they will be added to DB.
                $em = $this->_em;
                $em->persist($part);
            } else {
                $accession->removePart($part);
            }

        }

        return $accession;
    }

    public function notExists($entity) {
        $id = $entity->getId();
        if( !$id ) {
            return true;
        }      
        $em = $this->_em;
        $found = $em->getRepository('OlegOrderformBundle:Part')->findOneById($id);       
        if( null === $found ) {
            return true;
        } else {
            return false;
        }
    }
    
    public function presetEntity( $part ) {

        //$part->setDiseaseType("Non-Neoplastic");

        return $part;

    }

}
