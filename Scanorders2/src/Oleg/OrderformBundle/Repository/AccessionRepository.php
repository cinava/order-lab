<?php

namespace Oleg\OrderformBundle\Repository;

use Oleg\OrderformBundle\Form\DataTransformer\AccessionTypeTransformer;

/**
 * AccessionRepository
 * This class was generated by the Doctrine ORM.
 * Add your own custom repository methods below.
 */
class AccessionRepository extends ArrayFieldAbstractRepository {

    public function changeKeytype($entity) {
        $key = $entity->obtainValidKeyField();
        $newkeytypeid = $this->getCorrectKeytypeId($key->getKeytype()->getId());
        if( $key == "" || $newkeytypeid != $key->getKeytype()->getId() ) {
            $em = $this->_em;
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

//        if( count($orderinfo->getDataquality()) == 0 ) {
//            return $accession;
//        }

        //echo "process Accession: ".$accession;
        //$this->printTree( $accession->getParent()->getParent() );

        $em = $this->_em;

        //process data quality
        $currentDataquality = null;

        //echo "dataquality count=".count($orderinfo->getDataquality())."<br>";

        foreach( $orderinfo->getDataquality() as $dataquality) {

            $accessionConflict = false;
            $patientConflict = false;

            //check if this dataquality's patient is corresponds to accession patient
            $mrn = $dataquality->getMrn();
            $mrntype = $dataquality->getMrntype();
            $validMrn = $accession->getParent()->getParent()->obtainValidKeyfield();
            $accmrn = $validMrn->getField();
            $accmrntype = $validMrn->getKeytype();
            //echo "compare patient: (".$mrn .")==(". $accmrn .") && (". $mrntype .")==(". $accmrntype.")<br>";

            if( $mrn && $mrn != '' && $accmrn && $accmrn != '' ) {
                //valid values are not empty
            } else {
                //echo "skip!!! <br>";
                $orderinfo->removeDataquality($dataquality);
                continue;   //remove and skip this dataquality
            }

            if( $mrn == $accmrn && trim($mrntype) == trim($accmrntype) ) {
                $patientConflict = true;
                //break;
            }

            $conflictAccessionNum = $dataquality->getAccession()."";
            $conflictAccessionType = $dataquality->getAccessiontype()."";
            $currentAccessionNum = $accession->obtainValidKeyfield();
            $currentAccessionType = $accession->obtainValidKeyfield()->getKeytype()."";
            //echo $currentAccessionNum."?=".$conflictAccessionNum.", newAccession=".$dataquality->getNewaccession()."<br>";
            //echo $currentAccessionType."?=".$conflictAccessionType."<br>";
            if( $currentAccessionNum == $conflictAccessionNum && $currentAccessionType == $conflictAccessionType ) { //only for match accessions and if this accession was not processed yet
                if( !$dataquality->getNewaccession() ) {
                    $accessionConflict = true;
                }
            }

            if( $accessionConflict && $patientConflict ) {
                $currentDataquality = $dataquality;
                break;
            }
        }

        if( !$currentDataquality ) {
            //echo "#####this is not conflict accession => return !!!!!! <br>";
            return $accession;
        }

        ///////////////// check DB directly for conflict, just in case JS fails to catch conflict and orderinfo's Dataquality is empty ////////////////////
        $dbconflict = false;
        $accKey = $accession->obtainValidKeyfield();
        $accValue = $accKey->getField()."";
        $accKeytype = $accKey->getKeytype()->getId();

        $procedure = $accession->getParent();
        if( !$procedure ) {
            throw new \Exception( 'Accession does not belong to Procedure' );
        }

        $patient = $procedure->getParent();
        if( !$patient ) {
            throw new \Exception( 'Procedure does not belong to Patient' );
        }

        $mrnKey = $patient->obtainValidKeyField();
        $mrnValue = $mrnKey->getField()."";
        $mrnKeytype = $mrnKey->getKeytype()->getId();
        //echo "mrnKeytype Id=".$mrnKeytype."<br>";

        if( $this->isDBConflictByAccession( $accValue, $accKeytype, $mrnValue, $mrnKeytype  ) ) {
            //echo "DB conflict!<br>";
            $dbconflict = true;

            $currentDataquality = new DataQuality();

            //set mrntype
            $mrntype = $em->getRepository('OlegOrderformBundle:MrnType')->findOneById( $mrnKeytype );
            $currentDataquality->setMrntype($mrntype);
            $currentDataquality->setMrn($mrnValue);

            //set accessiontype
            $accessiontype = $em->getRepository('OlegOrderformBundle:AccessionType')->findOneById( $accKeytype );
            $currentDataquality->setAccessiontype($accessiontype);
            $currentDataquality->setAccession($accValue);

            $currentDataquality->setOrderinfo($orderinfo);
            $currentDataquality->setProvider($orderinfo->getProvider()->first());
            $currentDataquality->setStatus('active');

        }
        ///////////////// EOF check DB directly for conflict, just in case JS fails to catch conflict and orderinfo's Dataquality is empty ////////////////////


        //Now we know that this accession has MRN conflict
        //echo "Now we know that this accession has MRN conflict <br>";

        //$entity = $em->getRepository('OlegOrderformBundle:Accession')->createElement(null,$user,"Accession","accession");
        //1) take care of mrn-accession conflict: replace accession# with ACCESSIONNONPROVIDED:
        $accession->setId(null); //make sure to generate a new accession
        $accession->setStatusAllKeyfield(self::STATUS_INVALID);
        $accession->createKeyField();

        $acctype = $em->getRepository('OlegOrderformBundle:AccessionType')->findOneByName("Auto-generated Accession Number");

        //we should have only one key field !!!
        $key = $accession->obtainValidKeyField();
        $key->setKeytype($acctype);
        $key->setStatus(self::STATUS_VALID);
        $key->setSource('scanorder');
        $key->setProvider($orderinfo->getProvider()->first());

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

//        echo "<br>-----------------Original Accession:<br>";
//        $this->printTree( $accession );
//        echo "--------------------------<br>";
//        echo "finish process Accession: ".$accession."<br>";

        return $accession;

    }

    //check if there is a conflict in DB
    public function isDBConflictByAccession( $accValue, $accKeytype, $mrnValue, $mrnKeytype ) {

        $extra = array();
        $extra["keytype"] = $accKeytype;

        $validity = array();
        $validity[] = "valid";
        $validity[] = "reserved";

        $accessions = $this->_em->getRepository('OlegOrderformBundle:Accession')->findOneByIdJoinedToField(
            $accValue,      //$fieldStr
            "Accession",    //$className
            "accession",    //$fieldName
            $validity,      //$validity
            false,           //$single
            $extra          //$extra
        );

        if( count($accessions) > 1 ) {
            throw new \Exception( 'More than one Accession found, but single entity is expected:  key='. $accValue. ', type=' . $accKeytype );
        }

        if( count($accessions) == 0 ) {
            return false;   //Accession does not exist in DB => no conflict
        }

        $accession = $accessions[0];

        $procedure = $accession->getParent();
        if( !$procedure ) {
            return false;
        }

        $patient = $procedure->getParent();
        if( !$patient ) {
            return false;
        }

        $mrnKey = $patient->obtainValidKeyField();
        $mrnValueDb = $mrnKey->getField()."";
        $mrnKeytypeDb = $mrnKey->getKeytype()->getId();

        if( $mrnValueDb == $mrnValue && $mrnKeytypeDb == $mrnKeytype ) {
            return true;
        }

        return false;
    }

}
?>
