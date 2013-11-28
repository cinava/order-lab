<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Oleg\OrderformBundle\Entity\PartList;
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

        if( !$accession->getAccession() || count($accession->getAccession())==0 ) {
            throw $this->createNotFoundException('Accession does not have an accession number.');
        }

        $validAccession = $this->getValidField( $accession->getAccession() );

        //$em = $this->_em;
        //$part = $em->getRepository('OlegOrderformBundle:Block')->removeDuplicateEntities( $part );

        echo "part name partname=".$part->getPartname()->first()."<br>";
        echo "part name partname count=".count($part->getPartname())."<br>";
        if( count($part->getPartname()) > 0 ) {
            echo "part name provider=".$part->getPartname()->first()->getProvider()."<br>";
            echo "part name validity=".$part->getPartname()->first()->getValidity()."<br>";
        }

        if( count($part->getPartname()) == 0 ) { //empty key field
            echo "******* Part Case 1: key field is empty => createPartName only <br>";
            //create a key field with next available key value and set this key field to form object (Advantage: no need to copy children)
            $part = $this->createPartName( $part, $validAccession."" );
        }

        $partnamecount = count($part->getPartname());
        if( $partnamecount > 1 ) {
            echo "partname count > 1  <br>";
            $validPartname = $this->getValidField( $part->getPartname() );
        } else if( $partnamecount == 1 ) {
            echo "only one partname <br>";
            $validPartname = $part->getPartname()->first();
        } else {
            echo "partnamecount is 0 => LOGIC WARNING !!!<br>";
        }

        echo "valid accession#=".$validAccession.", partname=".$validPartname."<br>";

        //check if accession already has part with the same name.
        echo "******* check part uniqueness by partname and accession<br>";

        //if $validPartname does not exist in DB, then we can not check findOnePartByJoinedToField, so $part_found will be null
        if( !$validPartname || $validPartname == "" ) {
            echo "part_found = null => LOGIC WARNING !!!<br>";
            $part_found = null;
        } else {
            echo "find part <br>";
            $part_found = $this->findOnePartByJoinedToField( $validAccession->getField()."", $validPartname->getField()."" );
        }

        if( $part_found == null ) {
            echo "******* Part Case 2: part is not found in DB<br>";

            $part = $this->setResult( $part, $orderinfo );
            return $part;

        } else {
            echo "******* Part Case 3: part is existed in DB<br>";

            foreach( $part->getBlock() as $block ) {
                $part_found->addBlock( $block );
            }
            $part = $this->setResult( $part_found, $orderinfo, $part );
            return $part;
        }

    }

    public function createPartName( $part, $accession ) {
        echo "partname1 count=".count($part->getPartname())."<br>";
        $fieldValue = $this->findNextPartnameByAccession($accession);   //next partname
        echo "next partname generated=".$fieldValue."<br>";
        $field = new PartPartname(1);
        $field->setField($fieldValue);
        $part->clearPartname();
        $part->addPartname( $field );
        echo "partname2 count=".count($part->getPartname())."<br>";
        return $part;
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

        echo "1 part name diseaseType=".$part->getDiseaseType()->first()."<br>";
        echo "1 part name diseaseType count=".count($part->getDiseaseType())."<br>";
        if( count($part->getDiseaseType()) > 0 ) {
            echo "1 part diseaseType provider=".$part->getDiseaseType()->first()->getProvider()."<br>";
            echo "1 part diseaseType validity=".$part->getDiseaseType()->first()->getValidity()."<br>";
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
//            if(1) {
                $part->removeBlock( $block );
                $block = $em->getRepository('OlegOrderformBundle:Block')->processEntityBlock( $block, $part, $orderinfo );
                $part->addBlock($block);
                //$orderinfo->addBlock($block);
            } else {
                continue;
            }
            $orderinfo->addBlock($block);
        }

        echo "####################################################<br>";
//        echo "2 part name partname=".$part->getPartname()->first()."<br>";
//        if( count($part->getPartname()) > 0 ) {
//            echo "2 part name provider=".$part->getPartname()->first()->getProvider()."<br>";
//            echo "2 part name validity=".$part->getPartname()->first()->getValidity()."<br>";
//        }
//        echo "2 part name sourceOrgan=".$part->getSourceOrgan()->first()."<br>";

//        $descr = $part->getDescription()[0];
//        if( $descr ) {
//            echo "descr yes <br>";
//            echo "2 part descr->getField()=".$descr->getField()."<br>";
//            echo "2 part descr->getPart()=".$descr->getPart()."<br>";
//            echo "2 part name description=".$part->getDescription()->first().",count=".count($part->getDescription())."<br>";
//        } else {
//            echo "descr null <br>";
//        }

//        echo "2 part name disident count=".count($part->getDisident())."<br>";
//        $disident = $part->getDisident()[0];
//        if( $disident ) {
//            echo "disident yes <br>";
//            echo "2 part disident->getField()=".$disident->getField()."<br>";
//            echo "2 part disident->getPart()=".$disident->getPart()."<br>";
//        } else {
//            echo "disident null <br>";
//        }

        //echo "2 part name disident=".$part->getdisident()[0].",count=".count($part->getdisident()).", provider=".$part->getdisident()[0]->getProvider().", partCount=".count($part->getdisident()[0]->getPart()).", validity=".$part->getdisident()[0]->getValidity()."<br>";
        //echo "2 part name Description=".$part->getDescription()[0].",count=".count($part->getDescription()).", provider=".$part->getDescription()[0]->getProvider().", partCount=".count($part->getDescription()[0]->getPart()).", validity=".$part->getDescription()[0]->getValidity()."<br>";

//        echo "2 part name diffDisident count=".count($part->getdiffDisident())."<br>";
//        $diffDisident = $part->getdiffDisident()[0];
//        if( $diffDisident ) {
//            echo "diffDisident yes <br>";
//            echo "2 part diffDisident->getField()=".$diffDisident->getField()."<br>";
//            echo "2 part diffDisident->getPart()=".$diffDisident->getPart()."<br>";
//        } else {
//            echo "diffDisident null <br>";
//        }

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

        //echo "findNextPartnameByAccession: accessionNumber=".$accessionNumber."<br>";
        $name = "NOPARTNAMEPROVIDED";

        $query = $this->getEntityManager()
            ->createQuery('
            SELECT MAX(ppartnamefield.name) as max'.'partname'.' FROM OlegOrderformBundle:Part p
            JOIN p.partname ppartname
            JOIN ppartname.field ppartnamefield
            JOIN p.accession a
            JOIN a.accession aa
            WHERE ppartnamefield.name LIKE :name AND aa.field = :accession'
            )->setParameter('name', '%'.$name.'%')->setParameter('accession', $accessionNumber."");

        $lastField = $query->getSingleResult();
        $index = 'max'.'partname';
        $lastFieldStr = $lastField[$index];
        //echo "lastFieldStr=".$lastFieldStr."<br>";
        $fieldIndexArr = explode("-",$lastFieldStr);
        //echo "count=".count($fieldIndexArr)."<br>";
        if( count($fieldIndexArr) > 1 ) {
            $fieldIndex = $fieldIndexArr[1];
        } else {
            $fieldIndex = 0;
        }
        $fieldIndex = ltrim($fieldIndex,'0') + 1;
        $paddedfield = str_pad($fieldIndex,10,'0',STR_PAD_LEFT);
        //echo "paddedfield=".$paddedfield."<br>";
        //exit();
        return $name.'-'.$paddedfield;

    }

    public function findNextPartnameByAccession_OLD( $accessionNumber ) {
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
                )->setParameter('field', $name)->setParameter('accession', $accessionNumber."");

            $part = $query->getResult();

            if( !$part ) {
                return $name;
            }
        }

        return null;
    }

    //create new Part by provided accession number
    public function createPartByAccession( $accessionNumber ) {

        //echo "accessionNumber=".$accessionNumber."<br>";

        if( !$accessionNumber || $accessionNumber == "" ) {
            return null;
        }

        $accessionNumber = $accessionNumber."";

        $em = $this->_em;

        $accessions = $em->getRepository('OlegOrderformBundle:Accession')->findOneByIdJoinedToField($accessionNumber,"Accession","accession",true,false); //find multi: all accessions with given $accessionNumber

        if( count($accessions) > 1 ) {
            throw $this->createNotFoundException('More than one entity found.');
        }

        if( !$accessions ) {
            //echo "accession is not found in DB, accessionNumber=".$accessionNumber."<br>";
            //1) create Accession if not existed. We must create parent (accession), because we will create part object which must be linked to its parent
            //                                                                     $status, $provider, $className, $fieldName, $parent, $fieldValue
            $accession = $em->getRepository('OlegOrderformBundle:Accession')->createElement(null,null,"Accession","accession",null,$accessionNumber);
        } else {
            $accession = $accessions[0];
            //echo "accession is found in DB, accessionNumber=".$accessionNumber.", id=".$accession->getId()."<br>";
            //echo "accession is found in DB, accessionNumber=".$accessionNumber."<br>";
        }

        //2) find next available part name by accession number
        $partname = $em->getRepository('OlegOrderformBundle:Part')->findNextPartnameByAccession($accessionNumber);
//        $partname = $em->getRepository('OlegOrderformBundle:Part')->getNextNonProvided("NOPARTNAMEPROVIDED", "Part", "partname");
        //echo "next partlist generated=".$partname."<br>";
        //exit();


        //3) before part create: check if part with $partname does not exists in DB
        $partFound = $em->getRepository('OlegOrderformBundle:Part')->findOneByIdJoinedToField($partname,"Part","partname",true);

        if( $partFound ) {
            return $partFound;
        }

        //echo "create part, accession=".$accession->getAccession()->first().", partid=".$accession->getId()."<br>";
        //exit();

        //echo "create part <br>";
        //4) create part object by partname and link it to the parent
        $part = $em->getRepository('OlegOrderformBundle:Part')->createElement(null,null,"Part","partname",$accession,$partname);

        return $part;
    }

    public function findOnePartByJoinedToField( $accession, $partname, $validity=null ) {

        $onlyValid = "";
        if( $validity ) {
            //echo "Part check validity ";
            $onlyValid = " AND pfield.validity=1";
        }

        $query = $this->getEntityManager()
            ->createQuery('
            SELECT p FROM OlegOrderformBundle:Part p
            JOIN p.partname pfield
            JOIN p.accession a
            JOIN a.accession aa
            WHERE pfield.field = :field AND aa.field = :accession'.$onlyValid
            )->setParameter('field', $partname."")->setParameter('accession', $accession."");

        $parts = $query->getResult();

        if( $parts ) {
            //echo "parts count=".count($parts)."<br>";
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
