<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Oleg\OrderformBundle\Helper\FormHelper;

use Oleg\OrderformBundle\Entity\Part;
use Oleg\OrderformBundle\Entity\PartPartname;

/**
 * PartRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PartRepository extends ArrayFieldAbstractRepository
{
    
    //this function will create an entity if it doesn't exist or return the existing entity object
    public function processEntityPart( $part, $accession=null, $orderinfo=null ) {

        echo "<br><br>processEntityPart partname=".$part->getPartname()->first()."<br>";

        $em = $this->_em;

        //$part = $em->getRepository('OlegOrderformBundle:Block')->removeDuplicateEntities( $part );
        
        if( $accession->getId() == null ) { //by this point, accession object should be already created
            echo "******* Part Case 1: accession id null<br>";
            $part = $this->setResult( $part, $orderinfo );
            return $part;
        }
        
        //check if accession already has part with the same name.
        echo "******* check part uniqueness by partname and accession<br>";
        $validAccession = $this->getValidField( $accession->getAccession() );
        if( count($part->getPartname()) > 1 ) {
            $validPartname = $this->getValidField( $part->getPartname() );
        } else {
            $validPartname = $part->getPartname()->first();
        }

        echo "valid accession#=".$validAccession.", partname=".$validPartname."<br>";
        //exit();

        //if $validPartname does not exist in DB, then we can not check findOnePartByJoinedToField, so $part_found will be null
        if( !$validPartname || $validPartname->getId() == null ) {
            $part_found = null;
        } else {
            $part_found = $this->findOnePartByJoinedToField( $validAccession, $validPartname );
        }

        if( $part_found == null ) {
            echo "******* Part Case 2: accession id is not null, but part is not found in DB<br>";

            //create new part
            $newPart = $em->getRepository('OlegOrderformBundle:Part')->createPartByPartnameAndAccession( $validAccession."" );

            //copy children from provided form part $part to a newly created part $newPart
            foreach( $part->getBlock() as $block ) {
                $newPart->addBlock( $block );
            }

            $part = $this->setResult( $newPart, $orderinfo, $part );
            return $part;
        } else {
            echo "******* Part Case 3: accession id is not null and part is existed in DB<br>";

            foreach( $part->getBlock() as $block ) {
                $part_found->addBlock( $block );
            }
            $part = $this->setResult( $part_found, $orderinfo, $part );
            return $part;
        }

    }
    
    public function setResult( $part, $orderinfo=null, $original=null ) {

        if( $part ) {
            echo "part yes <br>";
        } else {
            echo "part null <br>";
        }

        echo "1 part name partname=".$part->getPartname()->first()."<br>";
        echo "1 part name partname count=".count($part->getPartname())."<br>";
        if( count($part->getPartname()) > 0 ) {
            echo "1 part name provider=".$part->getPartname()->first()->getProvider()."<br>";
            echo "1 part name validity=".$part->getPartname()->first()->getValidity()."<br>";
        }
//        echo "1 part name sourceOrgan=".$part->getSourceOrgan()->first()."<br>";
//        echo "1 part name description=".$part->getDescription()->first().",count=".count($part->getDescription())."<br>";
//        echo "1 part name disidentis count=".count($part->getdisidentis())."<br>";
//        echo "1 part name disidentis=".$part->getdisidentis()[0].",count=".count($part->getdisidentis()).", provider=".$part->getdisidentis()[0]->getProvider().", partCount=".count($part->getdisidentis()[0]->getPart())."<br>";
//        //echo "1 part name provider=".$part->getPartname()->first()->getProvider()."<br>";
//        //echo "1 part name validity=".$part->getPartname()->first()->getValidity()."<br>";
//        echo "1 part=".$part."<br>";

        //$part->setdisidentis(null);  //TODO: fix fields when accession is null
        //$part->setDiffDiagnoses(null);

        $em = $this->_em;
        $em->persist($part);

        if( $orderinfo == null ) {
            return $part;
        }

        $part = $this->processFieldArrays($part,$orderinfo,$original);

        $blocks = $part->getBlock();    
        
        foreach( $blocks as $block ) {
            if( $em->getRepository('OlegOrderformBundle:Block')->notExists($block,"Block") ) {
                $part->removeBlock( $block );
                $block = $em->getRepository('OlegOrderformBundle:Block')->processBlockEntity( $block, $part, $orderinfo );
                $part->addBlock($block);
                $orderinfo->addBlock($block);
            } else {
                continue;
            }
        }

        echo "####################################################<br>";
        echo "2 part name partname=".$part->getPartname()->first()."<br>";
        if( count($part->getPartname()) > 0 ) {
            echo "2 part name provider=".$part->getPartname()->first()->getProvider()."<br>";
            echo "2 part name validity=".$part->getPartname()->first()->getValidity()."<br>";
        }
        echo "2 part name sourceOrgan=".$part->getSourceOrgan()->first()."<br>";

        $descr = $part->getDescription()[0];
        if( $descr ) {
            echo "descr yes <br>";
            echo "2 part descr->getField()=".$descr->getField()."<br>";
            echo "2 part descr->getPart()=".$descr->getPart()."<br>";
            echo "2 part name description=".$part->getDescription()->first().",count=".count($part->getDescription())."<br>";
        } else {
            echo "descr null <br>";
        }

        echo "2 part name disident count=".count($part->getDisident())."<br>";
        $disident = $part->getDisident()[0];
        if( $disident ) {
            echo "disident yes <br>";
            echo "2 part disident->getField()=".$disident->getField()."<br>";
            echo "2 part disident->getPart()=".$disident->getPart()."<br>";
        } else {
            echo "disident null <br>";
        }

        //echo "2 part name disident=".$part->getdisident()[0].",count=".count($part->getdisident()).", provider=".$part->getdisident()[0]->getProvider().", partCount=".count($part->getdisident()[0]->getPart()).", validity=".$part->getdisident()[0]->getValidity()."<br>";
        //echo "2 part name Description=".$part->getDescription()[0].",count=".count($part->getDescription()).", provider=".$part->getDescription()[0]->getProvider().", partCount=".count($part->getDescription()[0]->getPart()).", validity=".$part->getDescription()[0]->getValidity()."<br>";

        echo "2 part name diffDisident count=".count($part->getdiffDisident())."<br>";
        $diffDisident = $part->getdiffDisident()[0];
        if( $diffDisident ) {
            echo "diffDisident yes <br>";
            echo "2 part diffDisident->getField()=".$diffDisident->getField()."<br>";
            echo "2 part diffDisident->getPart()=".$diffDisident->getPart()."<br>";
        } else {
            echo "diffDisident null <br>";
        }

        echo "2 part=".$part."<br>";
        echo "####################################################<br>";

        //$em->flush($part);
        //exit();
        
        return $part;
    }

    //filter out duplicate virtual (in form, not in DB) parts from accession
    //unique part can be identified by the accession and part name => same part has the same accession number and part name;
    //since we check the part for this particular accession, then use just part's name (?!)
    public function removeDuplicateEntities( $accession ) {

        $parts = $accession->getPart();
        //echo "<br>remove duplication: part count=".count($parts)."<br>";

        if( count($parts) == 1 ) {
            return $accession;
        }

        $names = array();

        foreach( $parts as $part ) {

            echo "remove duplication: partname=".$part->getPartname()->first()."<br>";
            $thisName = $this->getValidField($part->getPartname());

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

    public function findNextPartnameByAccession( $accessionNumber ) {
        if( !$accessionNumber || $accessionNumber == "" ) {
            return null;
        }

        $helper = new FormHelper();
        $names = $helper->getPart();

        foreach( $names as $name ) {
            $query = $this->getEntityManager()
                ->createQuery('
            SELECT p FROM OlegOrderformBundle:Part p
            JOIN p.partname pfield
            JOIN p.accession a
            JOIN a.accession aa
            WHERE pfield.field = :field AND aa.field = :accession'
                )->setParameter('field', $name)->setParameter('accession', $accessionNumber);

            $part = $query->getResult();

            if( !$part ) {
                return $name;
            }
        }

        return null;
    }

    //create new Part by provided accession number
    public function createPartByPartnameAndAccession( $accessionNumber ) {

        if( !$accessionNumber || $accessionNumber == "" ) {
            return null;
        }

        $em = $this->_em;

        $accession = $em->getRepository('OlegOrderformBundle:Accession')->findOneByIdJoinedToField($accessionNumber,"Accession","accession",true);

        if( !$accession ) {
            //1) create Accession if not existed. We must create parent (accession), because we will create part object which must be linked to its parent
            //                                                                     $status, $provider, $className, $fieldName, $parent, $fieldValue
            $accession = $em->getRepository('OlegOrderformBundle:Accession')->createElement(null,null,"Accession","accession",null,$accessionNumber);
        }

        //2) find next available part name by accession number
        $partname = $em->getRepository('OlegOrderformBundle:Part')->findNextPartnameByAccession($accessionNumber);

        //3) before part create: check if part with $partname does not exists in DB
        $partFound = $em->getRepository('OlegOrderformBundle:Part')->findOneByIdJoinedToField($partname,"Part","partname",true);

        if( $partFound ) {
            return $partFound;
        }

        //echo "create part, accession=".$accession->getAccession()->first().", partid=".$accession->getId()."<br>";

        //4) create part object by partname and link it to the parent
        $part = $em->getRepository('OlegOrderformBundle:Part')->createElement(null,null,"Part","partname",$accession,$partname);

        return $part;
    }

    public function findOnePartByJoinedToField( $accession, $partname, $validity=null ) {

        $onlyValid = "";
        if( $validity ) {
            //echo "Part check validity ";
            $onlyValid = " AND cfield.validity=1";
        }

        $query = $this->getEntityManager()
            ->createQuery('
            SELECT p FROM OlegOrderformBundle:Part p
            JOIN p.partname pfield
            JOIN p.accession a
            JOIN a.accession aa
            WHERE pfield.field = :field AND aa.field = :accession'.$onlyValid
            )->setParameter('field', $partname)->setParameter('accession', $accession);

        $parts = $query->getResult();

        if( $parts ) {
            return $parts[0];
        } else {
            return null;
        }

    }

    //use abstract method
    public function createPart( $name=null, $accession=null, $status = null ) {

        if( !$status ) {
            $status = self::STATUS_RESERVED;
        }

        if( !$name ) {
            $name = "A";
        }

        $part = new Part();
        $part->setStatus($status);

        $partname = new PartPartname();
        $partname->setField($name);
        $partname->setValidity(1);

//        if( $accession && !$accession->getId() ) {
//            $entity = $em->getRepository('OlegOrderformBundle:Accession')->createElement(null,null,"Accession","accession");
//            $em->persist($accession);
//            $entity->setParent($accession);
//        }

        $part->addPartname($partname);
        $em = $this->_em;
        $em->persist($part);
        $em->flush();

        return $part;
    }
    
    public function presetEntity( $part ) {

        //$part->setDiseaseType("Non-Neoplastic");

        return $part;

    }

}
