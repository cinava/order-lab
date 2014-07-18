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

    public function cleanAndProcessEmptyArrayFields($entity) {

        $em = $this->_em;
        //$staintype = $em->getRepository('OlegOrderformBundle:StainList')->find("Auto-generated Accession Number");
        $repository = $em->getRepository('OlegOrderformBundle:StainList');
        $dql =  $repository->createQueryBuilder("stain");
        $dql->select('MIN(stain.orderinlist) AS default_staintype');
        $query = $this->getEntityManager()->createQuery($dql);
        $default_staintype = $query->getSingleResult()['default_staintype'];
        //$default_staintype = $staintypeResult['default_staintype'];

        //exit('blocks clean and process, default_staintype='.$default_staintype);

        $entity->cleanAndSetDefaultEmptyArrayFields($default_staintype);
        return $entity;
    }

//    public function attachToParent( $block, $slide ) {
//
////        //echo "slide type=".$slide->getSlidetype()."<br>";
////        //echo $slide;
//
//        //reattach slide to part if it is Cytopathology
//        if( (!$slide->getId() || $slide->getId() == "") &&  //only new slides
//            $slide->getSlidetype() == "Cytopathology"       //&& only Cytopathology slides
//        ) {
//            //echo "Cytopathology => attach slide to part<br>";
//            $part = $block->getParent();
//            $part->addSlide($slide);
//            $block->removeSlide($slide);
//            $slide->setBlock(null);
//        } else {
//            //echo "Regular slide => attach slide to block <br>";
//            $block->addChildren($slide);    //addSlide
//        }
//
//    }

    //override parent method to get next key string
    public function getNextNonProvided($entity, $extra=null, $orderinfo=null) {
        $part= $entity->getParent();
        $partname = $part->obtainValidKeyfield()."";
        $accession= $part->getParent();

        $key = $accession->obtainValidKeyfield();
        $accessionNumber = $key."";
        $keytype = $key->getKeytype()->getId();

        return $this->findNextBlocknameByAccessionPartname( $entity->getInstitution()->getId(), $accessionNumber, $keytype, $partname, $orderinfo );
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
        $validity = array(self::STATUS_VALID,self::STATUS_RESERVED); //false;

        $institutions = array($entity->getInstitution()->getId());

        return $this->findOneBlockByJoinedToField( $institutions, $accessionNumber, $keytype, $partname, $blockname, $validity );
    }

    //              findOneByIdJoinedToField( $fieldStr, $className, $fieldName, $validity=null, $single=true, $extra=null )
    public function findOneByIdJoinedToField( $institutions, $fieldStr, $className, $fieldName, $validity=null, $single=true, $extra=null ) {
               
        $accessionNumber = $extra['accession'];
        $keytype = $extra['keytype'];
        $partname = $extra['partname'];
        
        return $this->findOneBlockByJoinedToField( $institutions, $accessionNumber, $keytype, $partname, $fieldStr, $validity, $single );
    }

    public function findOneBlockByJoinedToField( $institutions, $accession, $keytype, $partname, $blockname, $validities=null, $single=true ) {

        //echo "BLOCK find: accession=".$accession.", keytype=".$keytype.", partname=".$partname.", blockname=".$blockname.", validity=".$validity." \n ";

        if( count($institutions) == 0 || !$accession || $accession == "" || !$keytype || $keytype == "" || !$partname || $partname == "" || !$blockname || $blockname == "" ) {
            return null;
        }

//        $onlyValid = "";
//        if( $validity ) {
//            //echo " check Block validity ";
//            if( $validity != "" && $validity !=  1 ) {
//                //echo "validity == string1 ";
//            } else if( $validity ==  1 ) {
//                //echo "validity == true ";
//                $validity = self::STATUS_VALID;
//            } else {
//                //echo "else-validity == string ";
//            }
//            $onlyValid = " AND b.status='".$validity."' AND bfield.status='".self::STATUS_VALID."'";
//        }
//        //echo "validity=".$onlyValid."\n";
        //add validity conditions
        $validityStr = "";
        if( $validities && is_array($validities) && count($validities)>0 ) {
            $validityStr = " AND (";
            $count = 1;
            foreach( $validities as $validity ) {
                $validityStr .= "b.status='".$validity."'";
                if( $count < count($validities) ) {
                    $validityStr .= " OR ";
                }
                $count++;
            }
            $validityStr .= ")";
        }
        //echo "validityStr=".$validityStr." <br> ";

        $extraStr = "";
        $parameters = array();      
        $parameters['field'] = $blockname."";
        if( $accession && $accession != "" && $partname && $partname != "" ) {
            $extraStr = ' AND aa.field = :accession AND pp.field = :partname AND aa.keytype = :keytype';
            $parameters['accession'] = $accession;
            $parameters['keytype'] = $keytype;
            $parameters['partname'] = $partname;                      
        }

        //add institution conditions
        $instStr = "";
        if( $institutions && is_array($institutions) && count($institutions)>0 ) {
            $instStr = " AND (";
            $count = 1;
            foreach( $institutions as $inst ) {
                $instStr .= "b.institution=".$inst."";
                if( $count < count($institutions) ) {
                    $instStr .= " OR ";
                }
                $count++;
            }
            $instStr .= ")";
        }
        //echo "instStr=".$instStr." <br> ";

        $query = $this->getEntityManager()
            ->createQuery('
                SELECT b FROM OlegOrderformBundle:Block b
                JOIN b.blockname bfield
                JOIN b.part p
                JOIN p.partname pp
                JOIN p.accession a
                JOIN a.accession aa
                WHERE bfield.field = :field' . $extraStr . $validityStr . $instStr
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
    public function createBlockByPartnameAccession( $institution, $accessionNumber, $keytype, $partname, $provider ) {

        if( !$accessionNumber || $accessionNumber == "" ) {
            return null;
        }

        if( !$partname || $partname == "" ) {
            return null;
        }

        $institutions = array($institution);

        $extra = array();
        $extra['keytype'] = $keytype;
        $extra['accession'] = $accessionNumber;
        $extra['partname'] = $partname;

        $withfields = false;

        $em = $this->_em;

        //1a) Check accession
        $accession = $em->getRepository('OlegOrderformBundle:Accession')->findOneByIdJoinedToField( $institutions,$accessionNumber,"Accession","accession", self::STATUS_RESERVED, true, $extra );   //find reserved accession, because this method called only by "check" button
        if( !$accession ) {
            //1) create Accession if not existed. We must create parent (accession), because we will create part object which must be linked to its parent
            //                                                                                      $status, $provider, $className, $fieldName, $parent, $fieldValue
            $accession = $em->getRepository('OlegOrderformBundle:Accession')->createElement($institution,null,$provider,"Accession","accession",null,$accessionNumber,$extra,$withfields);
        }

        //1b) Check part by partname and accession number
        $part = $em->getRepository('OlegOrderformBundle:Part')->findOnePartByJoinedToField( $institutions, $accessionNumber, $keytype, $partname, self::STATUS_RESERVED );    //find reserved part,  because this method called only by "check" button
        if( !$part ) {
            //1) create Part if not existed. We must create parent , because we will create an object which must be linked to its parent
            //                                                               $status, $provider, $className, $fieldName, $parent, $fieldValue
            $part = $em->getRepository('OlegOrderformBundle:Part')->createElement($institution,null,$provider,"Part","partname",$accession,$partname,$extra,$withfields);
        }

        //2) find next available part name by accession number
        $blockname = $em->getRepository('OlegOrderformBundle:Block')->findNextBlocknameByAccessionPartname($institution,$accessionNumber,$keytype,$partname);
        //echo "next blockname generated=".$blockname."<br>";
        
        //3) before create: check if element with keys does not exists in DB
        //echo "before create block: ".$accessionNumber." ". $keytype." ". $partname." ". $blockname."<br>\n";
        $blockFound = $em->getRepository('OlegOrderformBundle:Block')->findOneBlockByJoinedToField($institutions,$accessionNumber, $keytype, $partname, $blockname, false);  //validity=true if it was called by submit, false - if it was called by check button

        if( $blockFound ) {            
            return $blockFound;
        }

        //echo "#############Create block, partname=".$part->getPartname()->first().", partid=".$part->getId()."<br>";

        //4) create block object by blockname and link it to the parent
        $block = $em->getRepository('OlegOrderformBundle:Block')->createElement($institution,null,$provider,"Block","blockname",$part,$blockname,$extra,$withfields);

        return $block;
    }

    public function findNextBlocknameByAccessionPartname( $institution, $accessionNumber, $keytype, $partname, $orderinfo=null ) {

        if( !$institution || $institution == "" || !$accessionNumber || $accessionNumber == "" ) {
            return null;
        }

        if( !$partname || $partname == "" ) {
            return null;
        }
               
        $name = "NOBLOCKNAMEPROVIDED";

        //institution
        $inst = " AND p.institution=".$institution;

        $query = $this->getEntityManager()
            ->createQuery('
            SELECT MAX(bblockname.field) as max'.'blockname'.' FROM OlegOrderformBundle:Block b
            JOIN b.blockname bblockname  
            JOIN b.part p
            JOIN p.partname pp
            JOIN p.accession a
            JOIN a.accession aa
            WHERE bblockname.field LIKE :name AND aa.field = :accession AND aa.keytype = :keytype AND pp.field = :partname' . $inst
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

    //$parent is block. Don't replace slides
    public function replaceDuplicateEntities($parent,$orderinfo) {
        return $parent;
    }

}
