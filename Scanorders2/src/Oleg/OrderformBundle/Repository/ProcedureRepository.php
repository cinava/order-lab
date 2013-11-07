<?php

namespace Oleg\OrderformBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ProcedureRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProcedureRepository extends ArrayFieldAbstractRepository
{
    
    //Patient and Accession number is the key to check uniqueness for single slide order
    //input patient requires for single slide order, when objects are provided separately and procedure does not have patient
    public function processEntityProcedure( $in_entity, $patient, $accessions=null, $orderinfo=null ) {
        
        $em = $this->_em;

        $in_entity = $em->getRepository('OlegOrderformBundle:Accession')->removeDuplicateEntities( $in_entity );

//        $accessions = $patient->getAccession();

        //1) can't check uniqueness without accession number
        if( $accessions == null ) {
            return $this->setResult($in_entity, $orderinfo);
        }

        $accession_found = null;
        //2) if at least one accession belongs to a procedure, then potentially we can use this procedure
        foreach( $accessions as $accession ) {
            //if accession exists then return procedure for this accession; otherwise, create a new
//            $accession_found_this = $em->getRepository('OlegOrderformBundle:Accession')->findOneBy( array(
//                'accession' => $accession->getAccession()
//            ));
            $accession_found_this = $this->isExisted($accession,"Accession","accession");

            if( $accession_found_this != null && $accession_found_this->getProcedure() != null ) {
                $accession_found = $accession_found_this;
                echo "return by accession not found <br>";
                break;
            }
        }
        
        if( $accession_found == null || $accession_found->getProcedure() == null ) {
            echo "accession_found is null <br>";
            return $this->setResult($in_entity, $orderinfo);
        } else {
            $procedure = $accession_found->getProcedure();

            //check patient MRN
            if( $patient && $patient->getMrn() != "" && ($procedure->getPatient()->getMrn() == $patient->getMrn()) ) {
                //the same MRN => same Patient => the same procedure
                //copy all children to existing entity
                foreach( $in_entity->getAccession() as $accession ) {
                    $procedure->addAccession( $accession );
                }
                echo "MRN is the same <br>";
                return $this->setResult($procedure, $orderinfo, $in_entity );
            } else {
                echo "MRN is not the same <br>";
                return $this->setResult($in_entity, $orderinfo);
            }
        }
    }
    
    public function setResult( $procedure, $orderinfo=null, $original=null ) {

        $procedure->setPatient(null);
        $procedure->setStatus(1);

        $em = $this->_em;
        $em->persist($procedure);   
        
        if( $orderinfo == null ) {
            return $procedure;
        }

        //echo "1 procedure name provider=".$procedure->getName()->first()->getProvider()."<br>";
        //echo "1 procedure name validity=".$procedure->getName()->first()->getValidity()."<br>";

        $procedure = $this->processFieldArrays($procedure,$orderinfo,$original);

        $accessions = $procedure->getAccession();
        //echo "accession count=".count($accessions)."<br>";
//        foreach( $accessions as $accession ) {
//            echo $accession;
//        }
        
        foreach( $accessions as $accession ) {
            //echo $accession;
            if( $em->getRepository('OlegOrderformBundle:Accession')->notExists($accession, "Accession") ) {
                $procedure->removeAccession( $accession );
                $accession = $em->getRepository('OlegOrderformBundle:Accession')->processEntity( $accession, $orderinfo, "Accession", "accession", "Part" );
                $procedure->addAccession($accession);
                $orderinfo->addAccession($accession);
            } else {
                continue;
            }
        }

        echo $procedure."<br>";
        //echo "procedure name provider=".$procedure->getName()->first()->getProvider()."<br>";
        //echo "procedure name validity=".$procedure->getName()->first()->getValidity()."<br>";
        echo "procedure accession count=".count($procedure->getAccession())."<br>";
        echo "procedure accession provider=".$procedure->getAccession()->first()->getAccession()->first()->getProvider()."<br>";
        echo "procedure accession validity=".$procedure->getAccession()->first()->getAccession()->first()->getValidity()."<br>";

        //exit();
        //$em->flush($procedure);
        return $procedure;
    }

    //filter out duplicate virtual (in form, not in DB) procedures from provided patient
    public function removeDuplicateEntities( $patient ) {

        $procedures = $patient->getProcedure();
        //echo "procedure count=".count($procedures)."<br>";
        if( count($procedures) <= 1 ) {
            //echo "only 0 or 1 procedure found=".count($procedures)."<br>";
            //exit();
            return $patient;
        }

        //echo "procedure count1=".count($patient->getProcedure())."<br>";

        $count = 0;
        foreach( $procedures as $procedure ) {

            //echo $procedure;

            //1) check if accession is given
            $accessions = $procedure->getAccession();
            if( $accessions == null || count($accessions) == 0 ) {
                //can't check for duplicate without accession => don't remove this procedure => keep this procedure as unique
                //echo "cant check for duplicate without accession <br>";
                continue;
            }

            //2) check if at least one accession belongs to another (second) procedure, then we can potentially use this first procedure and remove the second one.
            $accession_found = null;
            foreach( $accessions as $accession ) {

                //if accession exists then return procedure for this accession; otherwise, create a new
                if( ($count+1) < count($procedures) ) { //make sure index exists and no need to check the last procedure

                    //if( in_array($accession, $procedures[$count+1]->getAccession() ) ) {
                    foreach( $procedures[$count+1]->getAccession() as $accNext ) {
                        //echo "compare: ".$accession->getAccession() ."?". $accNext->getAccession()."<br>";
                        if( $accession->getAccession() == $accNext->getAccession() ) {
                            $accession_found = $accession;
                            break;
                        }
                    }
                }
            }

            $em = $this->_em;

            if( $accession_found == null || $accession_found->getProcedure() == null ) {
                //don't remove
                //echo "no common accessions found <br>";
                //persist the rest of procedures, because they will be added to DB.
                $em->persist($procedure);
            } else {
                //now check if the next procedure (potentially to be removed) has the same MRN, don't check if MRN = "" => new dummy mrn will be generated
                if( $procedure->getPatient()->getMrn() != null && $procedure->getPatient()->getMrn() != "" ) {
                    if( $procedures[$count+1]->getPatient()->getMrn() == $procedure->getPatient()->getMrn() ) {
                        $patient->removeProcedure( $procedures[$count+1] );
                        //echo "remove procedure=".$procedure[$count+1];
                    } else {
                        $em->persist($procedure);
                    }
                }
            }

            $count++;
        }

        //echo "procedure count2=".count($patient->getprocedure())."<br>";
        //exit();

        return $patient;
    }
    
//    public function notExists($entity) {
//        $id = $entity->getId();
//        if( !$id ) {
//            return true;
//        }
//        $em = $this->_em;
//        $found = $em->getRepository('OlegOrderformBundle:Procedure')->findOneById($id);
//        if( null === $found ) {
//            return true;
//        } else {
//            return false;
//        }
//    }
    
}
