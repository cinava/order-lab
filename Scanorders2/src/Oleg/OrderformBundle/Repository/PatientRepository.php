<?php

namespace Oleg\OrderformBundle\Repository;

use Oleg\OrderformBundle\Form\DataTransformer\MrnTypeTransformer;

/**
 * PatientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PatientRepository extends ArrayFieldAbstractRepository
{

    public function changeKeytype( $entity ) {

        $em = $this->_em;

        $key = $entity->obtainValidKeyField();

        if( !$key->getKeytype() || $key->getKeytype() == "" ) {
            //this can happen when accession is generated by a user on the form
            //throw new \Exception( 'Patient does not have a valid keytype. keytype=' . $key->getKeytype() );
            $keytype = $em->getRepository('OlegOrderformBundle:MrnType')->findOneByName("Auto-generated MRN");
            $key->setKeytype($keytype);
        }

        //echo "keytype=".$key->getKeytype()."<br>";
        $newkeytypeid = $this->getCorrectKeytypeId( $key->getKeytype()->getId() );
        //echo "newkeytypeid=".$newkeytypeid."<br>";
        if( $key == "" || $newkeytypeid != $key->getKeytype()->getId() ) {  //$key == "" is the same as $key->getName().""
            $newkeytypeEntity = $em->getRepository('OlegOrderformBundle:MrnType')->findOneByName("Auto-generated MRN");
            $key->setKeytype($newkeytypeEntity);
        }

        //strip zeros and record original
        $originalKey = $key->getField();
        $stripedKey = ltrim($originalKey,'0');
        $key->setField($stripedKey);
        $key->setOriginal($originalKey);

        return $entity;
    }

    public function getCorrectKeytypeId($keytypeid,$user=null) {
        $em = $this->_em;

        if( is_numeric ( $keytypeid ) ) {
            $keytypeEntity = $em->getRepository('OlegOrderformBundle:MrnType')->findOneById($keytypeid);
        } else {
            //create a new MrnType entity
            $mrnTypeTransformer = new MrnTypeTransformer($em,$user);
            $keytypeEntity = $mrnTypeTransformer->createNew($keytypeid);
        }

        if( $keytypeEntity->getName()."" == "Existing Auto-generated MRN" ) {
            $keytypeEntity = $em->getRepository('OlegOrderformBundle:MrnType')->findOneByName("Auto-generated MRN");
        }
        return $keytypeEntity->getId();
    }

    public function getExtraEntityById( $extra ) {
        $em = $this->_em;
        return $em->getRepository('OlegOrderformBundle:MrnType')->findOneById($extra["keytype"]);
    }

    //replace child of patient if duplicated
    //$parent: patient
    //$orderinfo: orderinfo
    public function replaceDuplicateEntities( $parent, $orderinfo ) {

        //echo "Patient replace duplicates: parent: ".$parent;
        //echo "Patient replace duplicates: orderinfo: ".$orderinfo;

        $encounters = $parent->getChildren(); //encounters

        if( !$encounters ) {
            return $parent;
        }

        $count = 0;
        foreach( $encounters as $encounter ) {    //child is Encounter object
            //echo $count.": Testing child(Encounter)=".$encounter."<br>";

            if( count($encounter->getChildren()) != 1 ) {
                throw new \Exception( 'This entity must have only one child. Number of children=' . count($encounter->getChildren()) );
            }

            //get procedure
            $procedure = $encounter->getChildren()->first(); //in scanorder, encounter has only one procedure
            //echo "must be procedure:".$procedure;

            //get accession
            $accessions = null;
            $accession = null;
            if( $procedure ) {
                $accessions = $procedure->getChildren();
                $accession = $accessions->first(); //in scanorder, procedure has only one accession
            }

            if( !$accession ) {
                continue;
            }

            //echo "must be accession:".$accession;
            //echo "0 accession slide count=".count($accession->getPart()->first()->getBlock()->first()->getSlide())."<br>";

            //$sameChild = $this->findSimilarChild($parent,$encounter->getChildren()->first());
            $em = $this->_em;
            //$sameChild = $em->getRepository('OlegOrderformBundle:Encounter')->findSimilarChild( $parent, $encounter->getChildren()->first() );
            $foundAccession = $em->getRepository('OlegOrderformBundle:Accession')->findSimilarChild( $parent, $accession );

            //echo "similar child=".$foundAccession."<br>";
            //echo "0 foundAcc slide count=".count($foundAccession->getPart()->first()->getBlock()->first()->getSlide())."<br>";

            if( $foundAccession ) {  //accession
                //echo "Found similar child to:".$accession."<br>";
                //exit('process same child');

                //Note: assume that js will not submit two similar accession with different contest. JS must check for existing accession in DB and in the form!
                //Copy all children element from checked $accession to found accession $foundAccession

                $foundProcedure = $foundAccession->getParent();
                $foundEncounter = $foundAccession->getParent()->getParent();

                //copy accessions from checked $accession to found accession $foundAccession
                foreach( $accession->getChildren() as $accessionChild ) {
                    $foundAccession->addChildren($accessionChild);
                }

                //Copy Fields for Encounter
                //echo "<br>######################################## Process similar fields ########################################<br>";
                $foundEncounter = $this->processFieldArrays($foundEncounter,$orderinfo,$encounter);
                //echo "######################################## EOF Process similar fields ########################################<br>";

                //Copy Fields for Procedure
                //echo "<br>######################################## Process similar fields ########################################<br>";
                $foundProcedure = $this->processFieldArrays($foundProcedure,$orderinfo,$procedure);
                //echo "######################################## EOF Process similar fields ########################################<br>";

                //Copy Fields for Accession
                //echo "<br>######################################## Process similar fields ########################################<br>";
                $foundAccession = $this->processFieldArrays($foundAccession,$orderinfo,$accession);
                //echo "######################################## EOF Process similar fields ########################################<br>";

                //clear encounter-procedure-accession from patient (parent) and from orderinfo
                //$foundProcedure->removeAccession($accession);
                //$foundEncounter->removeProcedure($procedure);
                $parent->removeEncounter($encounter);

                $orderinfo->removeAccession($accession);
                $orderinfo->removeProcedure($procedure);
                $orderinfo->removeEncounter($encounter);

                //add foundEncounter to patient
                $parent->addEncounter($foundEncounter);
                $orderinfo->addEncounter($foundEncounter);

                //add $foundProcedure to orderinfo
                $orderinfo->addProcedure($foundProcedure);

                //add $foundAccession to orderinfo
                $orderinfo->addAccession($foundAccession);

                //echo "1 foundAcc slide count=".count($foundAccession->getPart()->first()->getBlock()->first()->getSlide())."<br>";

            }

            $count++;
        }

        return $parent;
    }


    
}
