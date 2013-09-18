<?php

namespace Oleg\OrderformBundle\Repository;
use Doctrine\ORM\EntityRepository;

//use Oleg\OrderformBundle\Entity\Accession;

/**
 * AccessionRepository
 * This class was generated by the Doctrine ORM.
 * Add your own custom repository methods below.
 */
class AccessionRepository extends EntityRepository {
    
    //this function will create an accession if it doesn't exist or return the existing accession object
    public function processEntity( $accession, $orderinfo=null ) {

        $em = $this->_em;
        $accession = $em->getRepository('OlegOrderformBundle:Part')->removeDuplicateEntities( $accession );

        $entity = $this->findOneBy(array('accession' => $accession->getAccession()));

        if( !$entity ) {        
            //create new accession
            //echo "new accession";
            return $this->setResult( $accession, $orderinfo );          
        }

        //copy all children to existing entity
        foreach( $accession->getPart() as $part ) {
            $entity->addPart( $part );
        }

        //echo "db accession <br>";
        return $this->setResult( $entity, $orderinfo );        
    }
    
    public function setResult( $accession, $orderinfo=null ) {
               
        //echo "accession=".$accession."<br>";
        $em = $this->_em;
        $em->persist($accession);
                
        if( $orderinfo == null ) {
            return $accession;
        }
        
        $parts = $accession->getPart();
        foreach( $parts as $part ) {
            if( $em->getRepository('OlegOrderformBundle:Part')->notExists($part) ) {              
                $accession->removePart( $part );
                $part = $em->getRepository('OlegOrderformBundle:Part')->processEntity( $part, $accession, $orderinfo );
                $accession->addPart($part);
                $orderinfo->addPart($part);
            } else {
                continue;
            }
        }
                  
        //$em->flush($accession);
        
        return $accession;
    }

    //filter out duplicate virtual (in form, not in DB) accessions from specimen
    public function removeDuplicateEntities( $specimen ) {

        $accessions = $specimen->getAccession();

//        echo "accession count=".count($accessions)."<br>";
//        exit();
        
        if( count($accessions) == 1 ) {
            return $specimen;
        }

        $accessionNums = array();

        foreach( $accessions as $accession ) {

            //echo "accession=".$accession."<br>";
            $accNum = $accession->getAccession();

            if( count($accessionNums) == 0 || !in_array($accNum, $accessionNums) ) {
                $accessionNums[] = $accNum;
                //persist the rest of entities, because they will be added to DB.
                $em = $this->_em;
                $em->persist($accession);
            } else {
                $specimen->removeAccession($accession);
            }

        }

        return $specimen;
    }
    
    public function notExists($entity) {
        $id = $entity->getId();
        if( !$id ) {
            return true;
        }      
        $em = $this->_em;
        $found = $em->getRepository('OlegOrderformBundle:Accession')->findOneById($id);       
        if( null === $found ) {
            return true;
        } else {
            return false;
        }
    }

}
?>
