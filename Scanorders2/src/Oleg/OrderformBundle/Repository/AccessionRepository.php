<?php

namespace Oleg\OrderformBundle\Repository;

use Oleg\OrderformBundle\Entity\DataQualityMrnAcc;
use Oleg\OrderformBundle\Form\DataTransformer\AccessionTypeTransformer;
use Oleg\OrderformBundle\Entity\Block;
use Oleg\OrderformBundle\Entity\Accession;
use Oleg\OrderformBundle\Security\Util\SecurityUtil;

/**
 * AccessionRepository
 * This class was generated by the Doctrine ORM.
 * Add your own custom repository methods below.
 */
class AccessionRepository extends ArrayFieldAbstractRepository {



    public function changeKeytype($entity) {

        $em = $this->_em;

        $key = $entity->obtainValidKeyField();

        if( !$key->getKeytype() || $key->getKeytype() == "" ) {
            //this can happen when accession is generated by a user on the form
            //throw new \Exception( 'Accession does not have a valid keytype. keytype=' . $key->getKeytype() );
            $keytype = $em->getRepository('OlegOrderformBundle:AccessionType')->findOneByName("Auto-generated Accession Number");
            $key->setKeytype($keytype);
        }

        $newkeytypeid = $this->getCorrectKeytypeId($key->getKeytype()->getId());
        if( $key == "" || $newkeytypeid != $key->getKeytype()->getId() ) {
            $newkeytypeEntity = $em->getRepository('OlegOrderformBundle:AccessionType')->findOneByName("Auto-generated Accession Number");
            $key->setKeytype($newkeytypeEntity);
        }

        //strip zeros and record original
        $originalKey = $key->getField();
        $key->setOriginal($originalKey);
        $stripedKey = ltrim($originalKey,'0');
        $key->setField($stripedKey);

//        echo $entity;
//        echo "num of keys=".count($entity->obtainKeyField())."<br>";
//        echo "number=".$entity->obtainValidKeyField()."<br>";
//        echo "original=".$entity->obtainValidKeyField()->getOriginal()."<br>";
//        echo "keytype=".$entity->obtainValidKeyField()->getKeytype()."<br>";

        return $entity;
    }

    //if keytype is "Existing Auto-generated Accession Number", then get "Auto-generated Accession Number" object and return its id
    //return id of the correct AccessionType
    public function getCorrectKeytypeId($keytypeid,$user=null) {
        $em = $this->_em;

        if( is_numeric ( $keytypeid ) ) {
            $keytypeEntity = $em->getRepository('OlegOrderformBundle:AccessionType')->findOneById($keytypeid);
        } else {
            //create a new AccessionType entity
            $accTypeTransformer = new AccessionTypeTransformer($em,$user);
            $keytypeEntity = $accTypeTransformer->createNew($keytypeid);
        }

        if( $keytypeEntity->getName()."" == "Existing Auto-generated Accession Number" ) {
            $keytypeEntity = $em->getRepository('OlegOrderformBundle:AccessionType')->findOneByName("Auto-generated Accession Number");
        }
        return $keytypeEntity->getId();
    }

    public function getExtraEntityById( $extra ) {
        $em = $this->_em;
        return $em->getRepository('OlegOrderformBundle:AccessionType')->findOneById($extra["keytype"]);
    }


    //process conflict if exists for accession number. Replace conflicting accession number by a new generated number.
    public function processDuplicationKeyField( $accession, $orderinfo ) {

        if( !($accession instanceof Accession) ) {
            echo 'Provided entity is not Accession, entity:'.$accession;
            throw new \Exception( 'Provided entity is not Accession, entity:'.$accession );
        }

        //echo $accession;

        $em = $this->_em;

        //process data quality
        $currentDataquality = null;

        echo "dataquality count=".count($orderinfo->getDataqualityMrnAcc())."<br>";

        //loop through all conflicts to find out if this accession is conflicted
        //To determine if this accession has geberated conflict: 1) compare accession number/type and mrn number/type of dataquality and form
        foreach( $orderinfo->getDataqualityMrnAcc() as $dataquality) {

            $accessionConflict = false;
            $patientConflict = false;

            //check if this dataquality's patient is corresponds to accession patient
            $mrn = $dataquality->getMrn();
            $mrntype = $dataquality->getMrntype();
            $validMrn = $accession->getParent()->getParent()->obtainValidKeyfield();
            $accmrn = $validMrn->getField();
            $accmrntype = $validMrn->getKeytype();
            echo "compare patient: (".$mrn .")==(". $accmrn .") && (". $mrntype .")==(". $accmrntype.")<br>";
            echo "acc patient:". $accession->getParent()->getParent();

            if( $mrntype == "" || $accmrntype == "" ) {
                throw new \Exception( 'Conflicting MRN Type is not provided: mrntype=' . $mrntype . ", accmrntype=" .$accmrntype );
            }

            if( $mrn && $mrn != '' && $accmrn && $accmrn != '' ) {
                //valid values are not empty
            } else {
                //echo "skip!!! <br>";
                $orderinfo->removeDataqualityMrnAcc($dataquality);
                continue;   //remove and skip this dataquality
            }

            if( $mrn == $accmrn && trim($mrntype) == trim($accmrntype) ) {
                $patientConflict = true;   //was true?
                //echo "patientConflict=".$patientConflict."<br>";
                //break;
            }
            echo "patientConflict=".$patientConflict."<br>";

            $conflictAccessionNum = $dataquality->getAccession()."";
            $conflictAccessionType = $dataquality->getAccessiontype()."";
            $currentAccessionNum = $accession->obtainValidKeyfield();
            $currentAccessionType = $accession->obtainValidKeyfield()->getKeytype()."";

            if( $conflictAccessionType == "" ) {
                throw new \Exception( 'Conflicting Accession Type is not provided: ' . $conflictAccessionType );
            }

            echo $currentAccessionNum."?=".$conflictAccessionNum.", newAccession=".$dataquality->getNewaccession()."<br>";
            echo $currentAccessionType."?=".$conflictAccessionType."<br>";

            if( $currentAccessionNum == $conflictAccessionNum && $currentAccessionType == $conflictAccessionType ) { //only for match accessions and if this accession was not processed yet
                if( !$dataquality->getNewaccession() ) {
                    $accessionConflict = true;
                }
            }

            echo "accessionConflict=".$accessionConflict.", patientConflict=".$patientConflict."<br>";

            if( $accessionConflict && $patientConflict ) {
                $currentDataquality = $dataquality;
                break;
            }
        }

        ///////////////// check DB directly for conflict, just in case JS fails to catch conflict and orderinfo's Dataquality is empty ////////////////////
        $dbconflict = false;
        if( $currentDataquality == null ) {

            echo "check conflict in DB <br>";
            $accKey = $accession->obtainValidKeyfield();
            $accValue = $accKey->getField()."";
            $accKeytype = $accKey->getKeytype()->getId();

            $procedure = $accession->getParent();
            if( !$procedure ) {
                throw new \Exception( 'Accession does not belong to Procedure' );
            }

            $encounter = $procedure->getParent();
            if( !$encounter ) {
                throw new \Exception( 'Procedure does not belong to Patient' );
            }

            $patient = $encounter->getParent();
            if( !$patient ) {
                throw new \Exception( 'Encounter does not belong to Patient' );
            }

            $mrnKey = $patient->obtainValidKeyField();
            $mrnValue = $mrnKey->getField()."";
            $mrnKeytype = $mrnKey->getKeytype()->getId();

            if( !$accession->getInstitution() ) {
                $accession->setInstitution($orderinfo->getInstitution());
            }
            $institutions = array($accession->getInstitution()->getId());
            //echo "mrnKeytype Id=".$mrnKeytype."<br>";

            if( $this->isDBConflictByAccession( $institutions, $accValue, $accKeytype, $mrnValue, $mrnKeytype  ) ) {
                echo "DB conflict!<br>";
                $dbconflict = true;

                $currentDataquality = new DataQualityMrnAcc();

                //set mrntype
                $mrntype = $em->getRepository('OlegOrderformBundle:MrnType')->findOneById( $mrnKeytype );
                $currentDataquality->setMrntype($mrntype);
                $currentDataquality->setMrn($mrnValue);

                //set accessiontype
                $accessiontype = $em->getRepository('OlegOrderformBundle:AccessionType')->findOneById( $accKeytype );
                $currentDataquality->setAccessiontype($accessiontype);
                $currentDataquality->setAccession($accValue);

                $currentDataquality->setOrderinfo($orderinfo);
                $currentDataquality->setProvider($orderinfo->getProvider());
                $currentDataquality->setStatus('active');

                $orderinfo->addDataqualityMrnAcc($currentDataquality);

            }
        }
        ///////////////// EOF check DB directly for conflict, just in case JS fails to catch conflict and orderinfo's Dataquality is empty ////////////////////

        if( $currentDataquality == null && $dbconflict == false ) {
            echo "#####this is not conflict accession => return !!!!!! <br>";
            return $accession;
        }

        //Now we know that this accession has MRN conflict
        echo "Now we know that this accession has MRN conflict <br>";

        //1) take care of mrn-accession conflict: replace accession# with ACCESSIONNONPROVIDED:
        $accession->setId(null); //make sure to generate a new accession
        $accession->setStatusAllKeyfield(self::STATUS_INVALID);
        $accession->createKeyField();

        $acctype = $em->getRepository('OlegOrderformBundle:AccessionType')->findOneByName("Auto-generated Accession Number");

        $securityUtil = new SecurityUtil($em,null,null);
        $source = $securityUtil->getDefaultSourceSystem();

        //we should have only one key field !!!
        $key = $accession->obtainValidKeyField();
        $key->setKeytype($acctype);
        $key->setStatus(self::STATUS_VALID);
        $key->setSource($source);
        $key->setProvider($orderinfo->getProvider());

        if( !$accession->getInstitution() ) {
            $accession->setInstitution($orderinfo->getInstitution());
        }

        $nextKey = $this->getNextNonProvided($accession,null,$orderinfo);
        $key->setField($nextKey);

        //set new accession number to dataquality
        $currentDataquality->setNewaccession($nextKey);
        $currentDataquality->setNewaccessiontype($acctype);

        if( $dbconflict ) {
            $desc = "MRN-Accession conflict detected in submit logic. Submitted values are ".
            "Accession Number: ".$accValue.", Accession Type: ".$accKeytype.", MRN: ".$mrnValue.", MRN Type: ".$mrnKeytype."\n" .
            "Conflict was resolved by generating new Accession with Accession Number:".$nextKey.", Accession Type:".$acctype;
            $currentDataquality->setDescription($desc);
        }

        echo "<br>-----------------Original Accession:<br>";
        $this->printTree( $accession );
        echo "--------------------------<br>";
        echo "finish process Accession: ".$accession."<br>";

        return $accession;

    }

    //check if there is a conflict in DB
    public function isDBConflictByAccession( $institutions, $accValue, $accKeytype, $mrnValue, $mrnKeytype ) {

        if( !$accValue || $accValue == "" || !$accKeytype || $accKeytype == "" || !$mrnValue || $mrnValue == "" || !$mrnKeytype || $mrnKeytype == "" ) {
            return false;
        }

        $extra = array();
        $extra["keytype"] = $accKeytype;

        $validity = array();
        $validity[] = self::STATUS_VALID;
        //$validity[] = self::STATUS_RESERVED;

        $accessions = $this->_em->getRepository('OlegOrderformBundle:Accession')->findOneByIdJoinedToField(
            $institutions,
            $accValue,      //$fieldStr
            "Accession",    //$className
            "accession",    //$fieldName
            $validity,      //$validity
            false,           //$single
            $extra          //$extra
        );

        //echo "accessions count=".count($accessions)."<br>";

        foreach( $accessions as $acc ){
            echo $acc;
        }

        if( count($accessions) > 1 ) {
            //throw new \Exception( 'More than one Accession found, but single entity is expected:  key='. $accValue. ', type=' . $accKeytype . ', found=' . count($accessions) );
            //TODO: for now use the first accession. Make sure only one unique accession is created
        }

        if( count($accessions) == 0 ) {
            return false;   //Accession does not exist in DB => no conflict
        }

        $accession = $accessions[0];

        $procedure = $accession->getParent();
        if( !$procedure ) {
            return false;
        }

        $patient = $procedure->getParent()->getParent();
        if( !$patient ) {
            return false;
        }

        $mrnKey = $patient->obtainValidKeyField();
        $mrnValueDb = $mrnKey->getField()."";
        $mrnKeytypeDb = $mrnKey->getKeytype()->getId();

        //echo "compare found accession's mrn: mrnValueDb:".$mrnValueDb." == mrnValue:".$mrnValue." && mrnKeytypeDb:".$mrnKeytypeDb." == mrnKeytype:".$mrnKeytype."<br>";

        if( $mrnValueDb == $mrnValue && $mrnKeytypeDb == $mrnKeytype ) {
            return false;
        } else {
            return true;
        }

        return false;
    }


    public function setCorrectAccessionIfConflict( $slide, $orderinfo ) {

        $slideParent = $slide->getParent();

        $class = new \ReflectionClass($slideParent);
        $className = $class->getShortName();

        if( $slideParent instanceof Block ) {
                        //    block     part        accession
            $accession = $slideParent->getParent()->getParent();
        } else {
                        //    part      accession
            $accession = $slideParent->getParent();
        }

        $accession = $this->processDuplicationKeyField( $accession, $orderinfo );


    }



    //find similar accession in patient.
    //$parent: patient
    //$newChild: accession
    //find similar child and return the first one
    //return false if no similar children are found
    public function findSimilarChild($parent,$newChild) {
        echo "Accession: find similar parent: ".$parent." <br>";
        echo "Accession: find similar Child to: ".$newChild." <br>";

        $encounters = $parent->getChildren();

        //echo "<br>";
        //echo $newChild;
        //echo "newChild key=".$newChild->obtainValidKeyfield()."<br>";
        if( $newChild->obtainValidKeyfield()."" == "" ) {   //no name is provided, so can't compare => does not exist
            //echo "false: no name <br>";
            return false;
        }

        if( !$encounters || count($encounters) == 0 ) { //no children => does not exist
            //echo "false: no children <br>";
            return false;
        }

        foreach( $encounters as $encounter ) {
            //echo $child;

            $procedures = $encounter->getProcedure();

            if( count($procedures) != 1 ) {
                throw new \Exception( 'Encounter must have only one Procedure child. Number of children=' . count($procedures) );
            }

            $procedure = $procedures->first();

            $accessions = $procedure->getAccession();

            if( count($accessions) != 1 ) {
                throw new \Exception( 'Procedure must have only one Accession child. Number of children=' . count($procedures) );
            }

            $accession = $accessions->first();

//            if( $accession === $newChild ) {
//                echo "the same child: continue<br>";
//                return false;
//            }

            if( $this->entityEqualByComplexKey($accession, $newChild) ) {
                //echo "MATCH!: ".$child." <br>";
                return $accession;
            } else {
                //echo "NO MATCH! <br>";
            }

        }//foreach

        return false;
    }

}
?>
