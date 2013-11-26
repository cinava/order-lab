<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Oleg\OrderformBundle\Helper\FormHelper;

/**
 * BlockRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BlockRepository extends ArrayFieldAbstractRepository
{


    //this function will create an entity if it doesn't exist or return the existing entity object
    public function processBlockEntity( $block, $part=null, $orderinfo=null ) {

        echo "<br><br>processBlockEntity blockname=".$block->getBlockname()->first()."<br>";

        $em = $this->_em;

        //$part = $em->getRepository('OlegOrderformBundle:Block')->removeDuplicateEntities( $part );

        if( $part == null || $part->getId() == null) { //new part number was entered
            echo "******* Block Case 1: part id null => new part number was entered <br>";
            return $this->setResult( $block, $orderinfo );
        }

        $accession = $part->getAccession();
        if( $accession == null ||$accession->getId() == null ) { //new accession number was entered
            echo "******* Block Case 1: accession id null => new accession number was entered <br>";
            $part = $this->setResult( $block, $orderinfo );
            return $part;
        }


        //check if part already has block with the same name.
        echo "******* check block uniqueness by blockname, partname and accession<br>";
        $validPartname = $this->getValidField( $part->getPartname() );
        if( count($block->getBlockname()) > 1 ) {
            $validBlockname = $this->getValidField( $block->getBlockname() );
        } else {
            $validBlockname = $block->getBlockname()->first();
        }

        echo "valid partname=".$validPartname.", blockname=".$validBlockname."<br>";

        //if $validPartname does not exist in DB, then we can not check findOnePartByJoinedToField, so $part_found will be null
        if( !$validBlockname || $validBlockname->getId() == null ) {
            echo "block_found = null <br>";
            $block_found = null;
        } else {
            $block_found = $this->findOneBlockByJoinedToField( $this->getValidField($accession->getAccession()), $validPartname, $validBlockname );
            echo "block_found <br>";
        }

        if( $block_found == null ) {
            echo "******* Block Case 2: part id is not null, but block is not found in DB<br>";

            //create new block
            $newBlock = $em->getRepository('OlegOrderformBundle:Block')->createBlockByPartnameAccession( $this->getValidField($accession->getAccession()), $validPartname );

            //copy children from provided form block $block to a newly created block $newBlock
            foreach( $block->getSlide() as $slide ) {
                $newBlock->addSlide( $slide );
            }

            $block = $this->setResult( $newBlock, $orderinfo, $block );
            return $block;

        } else {
            echo "******* Block Case 3: part id is not null and block is existed in DB<br>";
            //it should be only 1 block, so return the first one (single one).
            $block_res = $block_found[0];

            //copy all children to existing entity
            foreach( $block->getSlide() as $slide ) {
                $block_res->addSlide($slide);
            }
            return $this->setResult( $block_res, $orderinfo, $block );
        }

    }
    
    public function setResult( $block, $orderinfo=null, $original=null ) {
        
        $em = $this->_em;
        $em->persist($block);

        if( $orderinfo == null ) {
            return $block;
        }

        $block = $this->processFieldArrays($block,$orderinfo,$original);
        
        $slides = $block->getSlide();      
        foreach( $slides as $slide ) {         
            if( $em->getRepository('OlegOrderformBundle:Slide')->notExists($slide,"Slide") ) {
                $block->removeSlide( $slide );
                $slide = $em->getRepository('OlegOrderformBundle:Slide')->processEntity( $slide, $orderinfo );               
                $block->addSlide($slide);                                                                                                                             
                $orderinfo->addSlide($slide);
            } else {
                continue;
            }         
        }

        echo "block name=".$block->getBlockname()->first()."<br>";
                 
        //$em->flush($block);
        //exit();
        
        return $block;
    }

    //filter out duplicate virtual (in form, not in DB) blocks from a part
    //since we check the block for this particular part, then use just block's name (?!)
    public function removeDuplicateEntities( $part ) {

        $blocks = $part->getBlock();

        if( count($blocks) == 1 ) {
            return $part;
        }

        $names = array();

        foreach( $blocks as $block ) {

            $thisName = $block->getBlockname();

            if( count($names) == 0 || !in_array($thisName, $names) ) {
                $names[] = $thisName;
                //persist the rest of entities, because they will be added to DB.
                $em = $this->_em;
                $em->persist($block);
            } else {
                $part->removeBlock($block);
            }
        }

        return $part;
    }

    public function findOneBlockByJoinedToField( $accession, $partname, $blockname, $validity=null ) {

        $onlyValid = "";
        if( $validity ) {
            $onlyValid = " AND bfield.validity=1";
        }

        $query = $this->getEntityManager()
            ->createQuery('
            SELECT b FROM OlegOrderformBundle:Block b
            JOIN b.blockname bfield
            JOIN b.part p
            JOIN p.partname pp
            JOIN p.accession a
            JOIN a.accession aa
            WHERE bfield.field = :field AND aa.field = :accession AND pp.field = :partname'.$onlyValid
            )->setParameter('field', $blockname."")->setParameter('accession', $accession."")->setParameter('partname', $partname."");

        $blocks = $query->getResult();

        if( $blocks ) {
            return $blocks[0];
        } else {
            return null;
        }

    }

    //create new Block by provided accession number and part name
    public function createBlockByPartnameAccession( $accessionNumber, $partname ) {

        if( !$accessionNumber || $accessionNumber == "" ) {
            return null;
        }

        if( !$partname || $partname == "" ) {
            return null;
        }

        $em = $this->_em;

        //1a) Check accession
        $accession = $em->getRepository('OlegOrderformBundle:Accession')->findOneByIdJoinedToField($accessionNumber,"Accession","accession",true);
        if( !$accession ) {
            //1) create Accession if not existed. We must create parent (accession), because we will create part object which must be linked to its parent
            //                                                                                      $status, $provider, $className, $fieldName, $parent, $fieldValue
            $accession = $em->getRepository('OlegOrderformBundle:Accession')->createElement(null,null,"Accession","accession",null,$accessionNumber);
        }

        //1b) Check part by partname and accession number
//        $part = $em->getRepository('OlegOrderformBundle:Part')->findOneByIdJoinedToField($partname,"Part","partname",true,false);
        $part = $em->getRepository('OlegOrderformBundle:Part')->findOnePartByJoinedToField( $accessionNumber, $partname, true );
        if( !$part ) {
            //1) create Part if not existed. We must create parent , because we will create an object which must be linked to its parent
            //                                                               $status, $provider, $className, $fieldName, $parent, $fieldValue
            $part = $em->getRepository('OlegOrderformBundle:Part')->createElement(null,null,"Part","partname",$accession,$partname);
        }

        //2) find next available part name by accession number
        $blockname = $em->getRepository('OlegOrderformBundle:Block')->findNextBlocknameByAccessionPartname($accessionNumber,$partname);

        //3) before part create: check if block with $blockname $partname and $accessionNumber does not exists in DB
        //$blockFound = $em->getRepository('OlegOrderformBundle:Block')->findOneByIdJoinedToField($blockname,"Block","blockname",true);
        $blockFound = $em->getRepository('OlegOrderformBundle:Block')->findOneBlockByJoinedToField($accessionNumber, $partname, $blockname,true);

        if( $blockFound ) {
            return $blockFound;
        }

        //echo "create block, partname=".$part->getPartname()->first().", partid=".$part->getId()."<br>";

        //4) create block object by blockname and link it to the parent
        $block = $em->getRepository('OlegOrderformBundle:Block')->createElement(null,null,"Block","blockname",$part,$blockname);

        return $block;
    }

    public function findNextBlocknameByAccessionPartname( $accessionNumber, $partname ) {
        if( !$accessionNumber || $accessionNumber == "" ) {
            return null;
        }

        if( !$partname || $partname == "" ) {
            return null;
        }

        $helper = new FormHelper();
        $names = $helper->getBlock();

        foreach( $names as $name ) {
            $query = $this->getEntityManager()
                ->createQuery('
            SELECT b FROM OlegOrderformBundle:Block b
            JOIN b.blockname bfield
            JOIN b.part p
            JOIN p.partname pp
            JOIN p.accession a
            JOIN a.accession aa
            WHERE bfield.field = :field AND aa.field = :accession AND pp.field = :partname'
                )->setParameter('field', $name)->setParameter('accession', $accessionNumber."")->setParameter('partname', $partname."");

            $block = $query->getResult();

            if( !$block ) {
                //echo "blockname="+$name;
                return $name;
            }
        }

        return null;
    }
    
}
