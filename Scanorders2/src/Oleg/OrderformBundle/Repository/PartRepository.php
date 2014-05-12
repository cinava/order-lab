<?php

namespace Oleg\OrderformBundle\Repository;

/**
 * PartRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PartRepository extends ArrayFieldAbstractRepository
{

//    //if this element does not have any slide belonging to this order (with id=null) or children (empty branch for this orderinfo),
//    //so remove this element and all its parents from orderinfo
//    public function attachToOrderinfo( $entity, $orderinfo ) {
//
//        $children = $entity->getChildren();
//
//        $ret = 0;
//        $countNotEmptyChildren = 0;
//
//        foreach( $children as $child ) {
//            $childClass = new \ReflectionClass($child);
//            $childClassName = $childClass->getShortName();
//            if( $childClassName == "Block" ) {
//                //echo "check if this block has slides belongs to this orderinfo <br>";
//                $slides = $child->getChildren();
//                foreach( $slides as $slide ) {
//                    $res = $this->isEntityBelongsToOrderinfo( $slide, $orderinfo );
//                    if( $res ) {
//                        $countNotEmptyChildren++;
//                    }
//                }
//            } else
//            if( $childClassName == "Slide") {
//                //echo "check if this slide belongs to this orderinfo <br>";
//                $res = $this->isEntityBelongsToOrderinfo( $child, $orderinfo );
//                if( $res ) {
//                    $countNotEmptyChildren++;
//                }
//            } else {
//                throw new \Exception('Part has not valid child of the class ' . $childClassName );
//            }
//        }
//
//        if( $countNotEmptyChildren == 0 ) {
//            $this->removeThisAndAllParentsFromOrderinfo($entity,$orderinfo);
//            $ret = -1;
//        } else {
//            //echo "added to orderinfo: Part ret=".$ret.", count=".count($entity->getChildren())."<br>";
//            //echo $entity."<br>";
//            $orderinfo->addPart($entity);
//            $ret = 1;
//        }
//
//        return $ret;
//    }

    public function attachToParent( $part, $block ) {

        $childClass = new \ReflectionClass($block);
        $childClassName = $childClass->getShortName();
        //echo "childClassName=".$childClassName."<br>";

        if( $childClassName == "Slide" ) {
            parent::attachToParent( $part, $block );    //call parent method to simple attach slide to part
            return;
        }

        if( $block ) {

            //echo "adding block?:  ".$block;
            //do it, if the block is new. If block has ID then it was found in DB and it was created by someone else.
            //if( !$block->getId() || $block->getId() == null || $block->getId() == "" ) {
                //echo "block slides=".count($block->getChildren())."<br>";
                //add only if this block has slides
                if( count($block->getChildren()) > 0 ) {   //TODO: testing
                    //echo "block has slides<br>";
                    $part->addChildren($block);

//                    //replace similar child. For example, the form can have two blocks: Block 1 and Block 1 attached to the same Part.
//                    //So, use only one block instead of creating two same entity in DB.
//                    $sameChild = $this->findSimilarChild($part,$block);
//                    if( $sameChild ) {
//                        //attach all sub-children to found similar child
//                        $children = $block->getChildren();
//                        foreach( $children as $child ) {
//                            $sameChild->addChildren($child);
//                        }
//                    } else {
//                        $part->addChildren($block);
//                    }
                } else {
                    //remove block if it does not have any slides
                    //echo "remove block <br>";
                    $part->removeBlock($block);
                    $block->setPart(null);
                }
            //}
            //echo $block;

        }

    }


    //override parent method to get next key string
    public function getNextNonProvided( $entity, $extra=null, $orderinfo=null ) {
        $accession= $entity->getParent();
        //echo $entity;
        //echo $accession;
        $key = $accession->obtainValidKeyfield();
        $accessionNumber = $key."";
        $keytype = $key->getKeytype()->getId();
        return $this->findNextPartnameByAccession( $accessionNumber, $keytype, $orderinfo );
    }

    public function findNextPartnameByAccession( $accessionNumber, $keytype, $orderinfo=null ) {
        if( !$accessionNumber || $accessionNumber == "" ) {
            return null;
        }

        //echo "findNextPartnameByAccession: accessionNumber=".$accessionNumber."<br>";
        $name = "NOPARTNAMEPROVIDED";

        $query = $this->getEntityManager()
            ->createQuery('
            SELECT MAX(ppartname.field) as max'.'partname'.' FROM OlegOrderformBundle:Part p
            JOIN p.partname ppartname
            JOIN p.accession a
            JOIN a.accession aa
            WHERE ppartname.field LIKE :name AND aa.field = :accession AND aa.keytype = :keytype'
            )->setParameter('name', '%'.$name.'%')->setParameter('accession', $accessionNumber."")->setParameter('keytype', $keytype);

        $lastField = $query->getSingleResult();
        $index = 'max'.'partname';
        $lastFieldStr = $lastField[$index];
        //echo "lastFieldStr=".$lastFieldStr."<br>";

        //return $this->getNextByMax($lastFieldStr, $name);
        $maxKey = $this->getNextByMax($lastFieldStr, $name);

        //check if the valid bigger key was already assigned to the element of the same class attached to this order
        if( $orderinfo ) {
            $className = "Part";
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

    //create new Part by provided accession number
    public function createPartByAccession( $accessionNumber, $keytype, $provider ) {

        //echo "accessionNumber=".$accessionNumber."<br>";

        if( !$accessionNumber || $accessionNumber == "" ) {
            return null;
        }

        $accessionNumber = $accessionNumber."";

        $extra = array();
        $extra['keytype'] = $keytype;
        $extra['accession'] = $accessionNumber;

        $em = $this->_em;

        //1a) Check accession
        $accession = $em->getRepository('OlegOrderformBundle:Accession')->findOneByIdJoinedToField($accessionNumber,"Accession","accession",self::STATUS_RESERVED,true,$extra); //find multi: all accessions with given $accessionNumber

//        if( count($accessions) > 1 ) {
//            throw new \Exception('More than one entity found.');
//        }

        if( !$accession ) {
            //echo "accession is not found in DB, accessionNumber=".$accessionNumber."<br>";
            //1) create Accession if not existed. We must create parent (accession), because we will create part object which must be linked to its parent
            //                                                                     $status, $provider, $className, $fieldName, $parent, $fieldValue
            $accession = $em->getRepository('OlegOrderformBundle:Accession')->createElement(null,$provider,"Accession","accession",null,$accessionNumber,$extra,true);
        }
//        else {
//            $accession = $accessions[0];
//            //echo "accession is found in DB, accessionNumber=".$accessionNumber.", id=".$accession->getId()."<br>";
//            //echo "accession is found in DB, accessionNumber=".$accessionNumber."<br>";
//        }

        //2) find next available part name by accession number
        $partname = $em->getRepository('OlegOrderformBundle:Part')->findNextPartnameByAccession($accessionNumber,$keytype);
//        $partname = $em->getRepository('OlegOrderformBundle:Part')->getNextNonProvided("NOPARTNAMEPROVIDED", "Part", "partname");
        //echo "next partlist generated=".$partname."<br>";
        //exit();


        //3) before part create: check if part with $partname does not exists in DB
        $partFound = $this->findOnePartByJoinedToField( $accessionNumber, $keytype, $partname, false );    //validity=false - it was called by check button

        if( $partFound ) {
            return $partFound;
        }

        //echo "create part, accession=".$accession->getAccession()->first().", partid=".$accession->getId()."<br>";
        //exit();

        //echo "create part <br>";
        //4) create part object by partname and link it to the parent
        $part = $em->getRepository('OlegOrderformBundle:Part')->createElement(null,$provider,"Part","partname",$accession,$partname,$extra,true);

        return $part;
    }


    //override parent method to find unique entity in DB
    public function findUniqueByKey($entity) {

        $partname = $entity->obtainValidKeyfield()."";
        $accession = $entity->getAccession();
        $key = $accession->obtainValidKeyfield();
        $accessionNumber = $key."";
        $keytype = $key->getKeytype()->getId();
        $validity = false;

        return $this->findOnePartByJoinedToField( $accessionNumber, $keytype, $partname, $validity );
    }

    public function findOneByIdJoinedToField($fieldStr, $className, $fieldName, $validity=null, $single=true, $extra=null ) {

        $accessionNumber = $extra['accession'];
        $keytype = $extra['keytype'];
        //echo "accessionNumber=".$accessionNumber."|, keytype=".$keytype."| ";

        return $this->findOnePartByJoinedToField( $accessionNumber, $keytype, $fieldStr, $validity, $single );
    }

    //$accession - Accession number (string)
    //$partname - Part name (string)
    public function findOnePartByJoinedToField( $accession, $keytype, $partname, $validity=null, $single=true ) {

        $onlyValid = "";
        if( $validity ) {
            //echo "Part check validity ";
            if( $validity != "" && $validity !=  1 ) {
                //echo "validity == string1 validity=".$validity." |";
            } else if( $validity ==  1 ) {
                //echo "validity == true |";
                $validity = self::STATUS_VALID;
            } else {
                //echo "else-validity == string |";
            }
            $onlyValid = " AND p.status='".$validity."' AND pfield.status='".self::STATUS_VALID."'";
        }
        
        $extraStr = "";
        if( $accession && $accession != "" ) {
            $extraStr = ' AND aa.field = :accession AND aa.keytype = :keytype';
        }

        $query = $this->getEntityManager()
            ->createQuery('
            SELECT p FROM OlegOrderformBundle:Part p
            JOIN p.partname pfield
            JOIN p.accession a
            JOIN a.accession aa
            WHERE pfield.field = :field' . $extraStr . $onlyValid
            )->setParameter('field', $partname."");//->setParameter('accession', $accession."")->setParameter('keytype', $keytype."");

        if( $accession && $accession != "" ) {
           $query->setParameter('accession', $accession."")                  
                   ->setParameter('keytype', $keytype."");
        }
        
        $parts = $query->getResult();

        if( $parts ) {
            //echo "parts count=".count($parts)."|";
            if( $single ) {
                return $parts[0];
            } else {
                return $parts;
            }
        } else {
            //echo "parts with partname=".$partname.",accession=".$accession." is not found |";
            return null;
        }

    }

}
