<?php

namespace Oleg\OrderformBundle\Repository;

//use Doctrine\ORM\EntityRepository;
//use Oleg\OrderformBundle\Helper\FormHelper;
//use Oleg\OrderformBundle\Entity\BlockBlockname;

/**
 * BlockRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BlockRepository extends ArrayFieldAbstractRepository
{

    public function attachToParent( $block, $slide ) {

//        //echo "slide type=".$slide->getSlidetype()."<br>";
//        //echo $slide;

        //reattach slide to part if it is Cytopathology
        if( (!$slide->getId() || $slide->getId() == "") &&  //only new slides
            $slide->getSlidetype() == "Cytopathology"       //&& only Cytopathology slides
        ) {
            //echo "Cytopathology => attach slide to part<br>";
            $part = $block->getParent();
            $part->addSlide($slide);
            $block->removeSlide($slide);
            $slide->setBlock(null);
        } else {
            //echo "Regular slide => attach slide to block <br>";
            $block->addChildren($slide);    //addSlide
        }

    }

    //override parent method to get next key string
    public function getNextNonProvided($entity, $extra=null, $orderinfo=null) {
        $part= $entity->getParent();
        $partname = $part->obtainValidKeyfield()."";
        $accession= $part->getParent();

        $key = $accession->obtainValidKeyfield();
        $accessionNumber = $key."";
        $keytype = $key->getKeytype()->getId();

        return $this->findNextBlocknameByAccessionPartname( $accessionNumber, $keytype, $partname, $orderinfo );
    }

    //override parent method to find unique entity in DB
    public function findUniqueByKey($entity) {

        $blockname = $entity->obtainValidKeyfield()."";
        $part= $entity->getParent();
        $partname = $part->obtainValidKeyfield()."";
        $accession= $part->getParent();
        $key = $accession->obtainValidKeyfield();
        $accessionNumber = $key."";
        $keytype = $key->getKeytype()->getId();
        $validity = false;

        return $this->findOneBlockByJoinedToField( $accessionNumber, $keytype, $partname, $blockname, $validity );
    }

    //              findOneByIdJoinedToField( $fieldStr, $className, $fieldName, $validity=null, $single=true, $extra=null )
    public function findOneByIdJoinedToField($fieldStr, $className, $fieldName, $validity=null, $single=true, $extra=null ) {      
               
        $accessionNumber = $extra['accession'];
        $keytype = $extra['keytype'];
        $partname = $extra['partname'];
        
        return $this->findOneBlockByJoinedToField( $accessionNumber, $keytype, $partname, $fieldStr, $validity, $single );
    }

    public function findOneBlockByJoinedToField( $accession, $keytype, $partname, $blockname, $validity=null, $single=true ) {

        //echo "BLOCK find:".$accession.", ".$keytype.", ".$partname.", ".$blockname.", ".$validity." \n ";

        $onlyValid = "";
        if( $validity ) {
            //echo " check Block validity ";
            if( $validity != "" && $validity !=  1 ) {
                //echo "validity == string1 ";
            } else if( $validity ==  1 ) {
                //echo "validity == true ";
                $validity = self::STATUS_VALID;
            } else {
                //echo "else-validity == string ";
            }
            $onlyValid = " AND b.status='".$validity."' AND bfield.status='".self::STATUS_VALID."'";
        }
        //echo "validity=".$onlyValid."\n";

        $extraStr = "";
        $parameters = array();      
        $parameters['field'] = $blockname."";
        if( $accession && $accession != "" && $partname && $partname != "" ) {
            $extraStr = ' AND aa.field = :accession AND pp.field = :partname AND aa.keytype = :keytype';
            $parameters['accession'] = $accession;
            $parameters['keytype'] = $keytype;
            $parameters['partname'] = $partname;                      
        }       
        
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT b FROM OlegOrderformBundle:Block b
                JOIN b.blockname bfield
                JOIN b.part p
                JOIN p.partname pp
                JOIN p.accession a
                JOIN a.accession aa
                WHERE bfield.field = :field' . $extraStr . $onlyValid           
            )->setParameters($parameters);  //->setParameter('field', $blockname."");                            
        
        $blocks = $query->getResult();

        if( $blocks ) {           
            if( $single ) {
                return $blocks[0];
            } else {
                return $blocks;
            }
        } else {
            return null;
        }

    }

    //create new Block by provided accession number and part name
    public function createBlockByPartnameAccession( $accessionNumber, $keytype, $partname ) {

        if( !$accessionNumber || $accessionNumber == "" ) {
            return null;
        }

        if( !$partname || $partname == "" ) {
            return null;
        }

        $extra = array();
        $extra['keytype'] = $keytype;
        $extra['accession'] = $accessionNumber;
        $extra['partname'] = $partname;

        $em = $this->_em;

        //1a) Check accession
        $accession = $em->getRepository('OlegOrderformBundle:Accession')->findOneByIdJoinedToField( $accessionNumber,"Accession","accession", self::STATUS_RESERVED, true, $extra );   //find reserved accession, because this method called only by "check" button
        if( !$accession ) {
            //1) create Accession if not existed. We must create parent (accession), because we will create part object which must be linked to its parent
            //                                                                                      $status, $provider, $className, $fieldName, $parent, $fieldValue
            $accession = $em->getRepository('OlegOrderformBundle:Accession')->createElement(null,null,"Accession","accession",null,$accessionNumber,$extra,true);
        }

        //1b) Check part by partname and accession number
        $part = $em->getRepository('OlegOrderformBundle:Part')->findOnePartByJoinedToField( $accessionNumber, $keytype, $partname, self::STATUS_RESERVED );    //find reserved part,  because this method called only by "check" button
        if( !$part ) {
            //1) create Part if not existed. We must create parent , because we will create an object which must be linked to its parent
            //                                                               $status, $provider, $className, $fieldName, $parent, $fieldValue
            $part = $em->getRepository('OlegOrderformBundle:Part')->createElement(null,null,"Part","partname",$accession,$partname,$extra,true);
        }

        //2) find next available part name by accession number
        $blockname = $em->getRepository('OlegOrderformBundle:Block')->findNextBlocknameByAccessionPartname($accessionNumber,$keytype,$partname);
        //echo "next blockname generated=".$blockname."<br>";
        
        //3) before create: check if element with keys does not exists in DB
        //echo "before create block: ".$accessionNumber." ". $keytype." ". $partname." ". $blockname."<br>\n";
        $blockFound = $em->getRepository('OlegOrderformBundle:Block')->findOneBlockByJoinedToField($accessionNumber, $keytype, $partname, $blockname, false);  //validity=true if it was called by submit, false - if it was called by check button

        if( $blockFound ) {            
            return $blockFound;
        }

        //echo "#############Create block, partname=".$part->getPartname()->first().", partid=".$part->getId()."<br>";

        //4) create block object by blockname and link it to the parent
        $block = $em->getRepository('OlegOrderformBundle:Block')->createElement(null,null,"Block","blockname",$part,$blockname,$extra,true);

        return $block;
    }

    public function findNextBlocknameByAccessionPartname( $accessionNumber, $keytype, $partname, $orderinfo=null ) {
        if( !$accessionNumber || $accessionNumber == "" ) {
            return null;
        }

        if( !$partname || $partname == "" ) {
            return null;
        }
               
        $name = "NOBLOCKNAMEPROVIDED";

        $query = $this->getEntityManager()
            ->createQuery('
            SELECT MAX(bblockname.field) as max'.'blockname'.' FROM OlegOrderformBundle:Block b
            JOIN b.blockname bblockname  
            JOIN b.part p
            JOIN p.partname pp
            JOIN p.accession a
            JOIN a.accession aa
            WHERE bblockname.field LIKE :name AND aa.field = :accession AND aa.keytype = :keytype AND pp.field = :partname'
            )->setParameter('name', '%'.$name.'%')->setParameter('accession', $accessionNumber."")->setParameter('partname', $partname."")->setParameter('keytype', $keytype."");

        $lastField = $query->getSingleResult();
        $index = 'max'.'blockname';
        $lastFieldStr = $lastField[$index];
        //echo "lastFieldStr=".$lastFieldStr."<br>";
        
        //return $this->getNextByMax($lastFieldStr, $name);

        $maxKey = $this->getNextByMax($lastFieldStr, $name);

        //check if the valid bigger key was already assigned to the element of the same class attached to this order
        if( $orderinfo ) {
            $className = "Block";
            $getSameEntity = "get".$className;
            foreach( $orderinfo->$getSameEntity() as $same ) {
                if( $same->getStatus() == self::STATUS_VALID ) {
                    $key = $same->obtainValidKeyfield();
                    $newBiggerKey = $this->getBiggerKey($maxKey,$key,$name);
                    if( $newBiggerKey != -1 ) {
                        $maxKey = $newBiggerKey;
                    }
                }
            }
        }

        //return $this->getNextByMax($lastFieldStr, $name);
        return $maxKey;
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

}
