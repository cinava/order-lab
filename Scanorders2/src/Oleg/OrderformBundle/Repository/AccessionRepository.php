<?php

namespace Oleg\OrderformBundle\Repository;

use Oleg\OrderformBundle\Form\DataTransformer\AccessionTypeTransformer;

/**
 * AccessionRepository
 * This class was generated by the Doctrine ORM.
 * Add your own custom repository methods below.
 */
class AccessionRepository extends ArrayFieldAbstractRepository {

    public function attachToOrderinfo( $entity, $orderinfo ) {
        return 0;   //don't add accession here. Added in Procedure's 'setResult' method
    }

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




    //process conflict if exists
    public function processDuplicationKeyField( $accession, $orderinfo ) {

        if( count($orderinfo->getDataquality()) == 0 ) {
            return $accession;
        }

        //echo "process Accession: ".$accession;
        //$this->printTree( $accession->getParent()->getParent() );

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

        //Now we know that this accession has MRN conflict
        //echo "Now we know that this accession has MRN conflict <br>";

        //$entity = $em->getRepository('OlegOrderformBundle:Accession')->createElement(null,$user,"Accession","accession");
        //1) take care of mrn-accession conflict: replace accession# with ACCESSIONNONPROVIDED:
        $accession->setId(null); //make sure to generate a new accession
        $accession->setStatusAllKeyfield(self::STATUS_INVALID);
        $accession->createKeyField();

        $em = $this->_em;
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

//        echo "<br>-----------------Original Accession:<br>";
//        $this->printTree( $accession );
//        echo "--------------------------<br>";
//        echo "finish process Accession: ".$accession."<br>";

        return $accession;

    }

    //filter out duplicate virtual (in form, not in DB) accessions from specimen
    public function removeDuplicateEntities( $procedure ) {

        $accessions = $procedure->getAccession();
        
        if( count($accessions) == 1 ) {
            return $procedure;
        }

        $accessionNums = array();

        foreach( $accessions as $accession ) {

            //echo "accession=".$accession."<br>";
            $accNum = $accession->getAccession();

            if( count($accessionNums) == 0 || !in_array($accNum, $accessionNums) ) {
                $accessionNums[] = $accNum;
                //persist the rest of entities, because they will be added to DB.
                $em = $this->_em;
                $em->persist($accession);
            } else {
                $procedure->removeAccession($accession);
            }

        }

        return $procedure;
    }

}
?>
