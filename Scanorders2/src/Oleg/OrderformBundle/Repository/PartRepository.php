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

    public function attachToParentAndOrderinfo( $part, $block, $orderinfo ) {

        $childClass = new \ReflectionClass($block);
        $childClassName = $childClass->getShortName();
        //echo "childClassName=".$childClassName."<br>";
        if( $childClassName == "Slide" ) {
            parent::attachToParentAndOrderinfo( $part, $block, $orderinfo );
            return;
        }

        if( $block ) {
            //echo $block;
            //do it, if the block is new. If nlock has ID then it was found in DB and it was created by someone else.
            if( !$block->getId() || $block->getId() == null || $block->getId() == "" ) {
                //echo "block slides=".count($block->getChildren())."<br>";
                //add only if this block has slides
                if( count($block->getChildren()) > 0 ) {
                    //echo "block has slides<br>";
                    $part->addChildren($block);
                } else {
                    //remove block if it does not have any slides
                    //echo "remove block <br>";
                    $part->removeBlock($block);
                    $block->setPart(null);
                }
            }
            //echo $block;

            $orderinfo->addBlock($block);
            //echo "PartRepo: add orderinfo for Block, :".$orderinfo;

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
    public function createPartByAccession( $accessionNumber, $keytype ) {

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
            $accession = $em->getRepository('OlegOrderformBundle:Accession')->createElement(null,null,"Accession","accession",null,$accessionNumber,$extra,false);
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
        $part = $em->getRepository('OlegOrderformBundle:Part')->createElement(null,null,"Part","partname",$accession,$partname,$extra,false);

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
            )->setParameter('field', $partname."")->setParameter('accession', $accession."")->setParameter('keytype', $keytype."");

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

            //echo "remove duplication: partname=".$part->getPartname()->first()."<br>";
            $thisName = $this->obtainValidField($part->getPartname());

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

}
