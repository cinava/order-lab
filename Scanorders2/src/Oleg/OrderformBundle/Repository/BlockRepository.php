<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Oleg\OrderformBundle\Helper\FormHelper;
use Oleg\OrderformBundle\Entity\BlockBlockname;

/**
 * BlockRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BlockRepository extends ArrayFieldAbstractRepository
{


    //this function will create an entity if it doesn't exist or return the existing entity object
    public function processEntityBlock_OLD( $block, $part=null, $orderinfo=null ) {

        echo "<br><br>processEntityPart partname=".$part->getPartname()->first()."<br>";

        $accession = $part->getAccession();

        if( !$accession->getAccession() || count($accession->getAccession())==0 ) {
            throw new \Exception('Accession does not have an accession number.');
        }

        $validAccession = $this->getValidField($accession->getAccession());

        if( !$part->getPartname() || count($part->getPartname())==0 ) {
            throw new \Exception('Part does not have an part name.');
        }

        $validPartname = $this->getValidField( $part->getPartname() );

        if( count($block->getBlockname()) == 0 ) { //empty key field
            echo "******* Block Case 1: key field is empty => createBlockName only <br>";
            //create a key field with next available key value and set this key field to form object (Advantage: no need to copy children)
            $block = $this->createBlockName( $validAccession, $validPartname, $block );
        }

        $blocknamecount = count($block->getBlockname());
        if( $blocknamecount > 1 ) {
            echo "blockname count > 1  <br>";
            $validBlockname = $this->getValidField( $block->getBlockname() );
        } else if( $blocknamecount == 1 ) {
            echo "only one partname <br>";
            $validBlockname = $block->getBlockname()->first();
        } else {
            //echo "partnamecount is 0 => LOGIC WARNING !!!<br>";
            throw new \Exception('Part still does not have an part name.');
        }

        echo "valid accession#=".$validAccession.", partname=".$validPartname.", blockname=".$validBlockname."<br>";

        //check if part already has block with the same name.
        echo "******* check block uniqueness by blockname, partname and accession<br>";

        //if $validBlockname does not exist in DB, then we can not check findOneBlockByJoinedToField, so $block_found will be null
        if( !$validBlockname || $validBlockname == "" ) {
            echo "block_found = null => LOGIC WARNING !!!<br>";
            $block_found = null;
        } else {
            $block_found = $this->findOneBlockByJoinedToField( $validAccession, $validPartname, $validBlockname );
            echo "block_found <br>";
        }

        if( $block_found == null ) {

            echo "******* Block Case 2: block is not found in DB<br>";
            return $this->setResult( $block, $orderinfo );

        } else {

            echo "******* Block Case 3: block is existed in DB<br>";

            //copy all children to existing entity
            foreach( $block->getSlide() as $slide ) {
                $block_found->addSlide($slide);
            }
            return $this->setResult( $block_found, $orderinfo, $block );

        }

    }

    public function createBlockName( $accession, $part, $block ) {
        $fieldValue = $this->findNextBlocknameByAccessionPartname($accession, $part );  //next blockname
        echo "next blockname  generated=".$fieldValue."<br>";
        $field = new BlockBlockname(1);
        $field->setField($fieldValue);
        $block->clearBlockname();
        $block->addBlockname( $field );
        return $block;
    }
    
    public function setResult_OLD( $block, $orderinfo=null, $original=null ) {
        
        $em = $this->_em;
        $em->persist($block);

        if( $orderinfo == null ) {
            return $block;
        }

        $block = $this->processFieldArrays($block,$orderinfo,$original);
        
        $slides = $block->getSlide();      
        foreach( $slides as $slide ) {         
//            if( $em->getRepository('OlegOrderformBundle:Slide')->notExists($slide,"Slide") ) {
            if(1) {
                $block->removeSlide( $slide );
                $slide = $em->getRepository('OlegOrderformBundle:Slide')->processEntity( $slide, $orderinfo );               
                $block->addSlide($slide);                                                                                                                             
                //$orderinfo->addSlide($slide);
            } else {
                continue;
            }
            $orderinfo->addSlide($slide);
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


    //override parent method to get next key string
    public function getNextNonProvided($entity) {
        $part= $entity->getParent();
        $partname = $part->getValidKeyfield()."";
        $accession= $part->getParent();
        $accessionNumber = $accession->getValidKeyfield()."";
        return $this->findNextBlocknameByAccessionPartname( $accessionNumber, $partname );
    }

    //override parent method to find unique entity in DB
    public function findUniqueByKey($entity) {

        $blockname = $entity->getValidKeyfield()."";
        $part= $entity->getParent();
        $partname = $part->getValidKeyfield()."";
        $accession= $part->getParent();
        $accessionNumber = $accession->getValidKeyfield()."";

        return $this->findOneBlockByJoinedToField( $accessionNumber, $partname, $blockname, null );
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
        //echo "next blockname generated=".$blockname."<br>";
        
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
     
//            $query = $this->getEntityManager()
//                ->createQuery('
//            SELECT b FROM OlegOrderformBundle:Block b
//            JOIN b.blockname bfield
//            JOIN b.part p
//            JOIN p.partname pp
//            JOIN p.accession a
//            JOIN a.accession aa
//            WHERE bfield.field = :field AND aa.field = :accession AND pp.field = :partname'
//                )->setParameter('field', $name)->setParameter('accession', $accessionNumber."")->setParameter('partname', $partname."");
               
        $name = "NOBLOCKNAMEPROVIDED";

        $query = $this->getEntityManager()
            ->createQuery('
            SELECT MAX(bblockname.field) as max'.'blockname'.' FROM OlegOrderformBundle:Block b
            JOIN b.blockname bblockname  
            JOIN b.part p
            JOIN p.partname pp
            JOIN p.accession a
            JOIN a.accession aa
            WHERE bblockname.field LIKE :name AND aa.field = :accession AND pp.field = :partname'
            )->setParameter('name', '%'.$name.'%')->setParameter('accession', $accessionNumber."")->setParameter('partname', $partname."");

        $lastField = $query->getSingleResult();
        $index = 'max'.'blockname';
        $lastFieldStr = $lastField[$index];
        //echo "lastFieldStr=".$lastFieldStr."<br>";
        
        return $this->getNextByMax($lastFieldStr, $name);
    }
    
}
