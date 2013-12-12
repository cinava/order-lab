<?php

namespace Oleg\OrderformBundle\Repository;

//use Doctrine\ORM\EntityRepository;
//use Oleg\OrderformBundle\Entity\Accession;

/**
 * AccessionRepository
 * This class was generated by the Doctrine ORM.
 * Add your own custom repository methods below.
 */
class AccessionRepository extends ArrayFieldAbstractRepository {

    //filter out duplicate virtual (in form, not in DB) accessions from specimen
    public function removeDuplicateEntities( $procedure ) {

        $accessions = $procedure->getAccession();
        
        if( count($accessions) == 1 ) {
            return $procedure;
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
                $procedure->removeAccession($accession);
            }

        }

        return $procedure;
    }

}
?>
