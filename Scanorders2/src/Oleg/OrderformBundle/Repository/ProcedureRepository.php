<?php

namespace Oleg\OrderformBundle\Repository;

//use Doctrine\ORM\EntityRepository;

/**
 * ProcedureRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProcedureRepository extends ArrayFieldAbstractRepository
{

    public function processEntity( $entity, $orderinfo ) {

        $em = $this->_em;

        //debugging
        $patient = $entity->getParent();
        $procedures = $patient->getChildren();
        echo "procedure count=".count($procedures)."<br>";
        foreach( $procedures as $procedure ) {
            echo "Procedure process entity: ".$procedure;
        }

        //find accession
        $accessions = $entity->getAccession();
        //we should have only one Accession in the Procedure, because Procedure-Accession is considered as one object for now
        $accession = $accessions->first();

        //redirect to accession repository (skip procedure, because one procedure is linked to one accession)
        $accessionProcessed = $em->getRepository('OlegOrderformBundle:Accession')->processEntity( $accession, $orderinfo );

        //process Procedure
        $procedureProcessed = $accessionProcessed->getProcedure();

        //just process procedure (not setResult)
        $procedureProcessed = parent::processEntity( $procedureProcessed, $orderinfo );

        return $procedureProcessed;

    }

    public function setResult( $procedure, $orderinfo=null, $original=null ) {

        //set status 'valid'
        $procedure->setStatus(self::STATUS_VALID);

        //CopyFields
        $procedure = $this->processFieldArrays($procedure,$orderinfo,$original);

        //link orderinfo with accession
        $accession = $procedure->getAccession()->first();

        //echo "add Accession to orderinfo <br>";
        $orderinfo->addAccession($accession);

        if( !$procedure->getId() || $procedure->getId() == "" ) {
            echo "persist Procedure<br>";
            $em = $this->_em;
            $em->persist($procedure);
        } else {
            echo "merge Procedure<br>";
            //$em->merge($entity);
        }

        return $procedure;
    }

    //exception for procedure: procedure is linked to a single accession => check if accession is already existed in DB, if existed => don't create procedure, but use existing procedure
    public function findUniqueByKey( $entity ) {

        echo "findUniqueByKey: Procedure: ".$entity;

        if( count($entity->getChildren()) != 1 ) {
            throw new \Exception( 'This entity must have only one child. Number of children=' . count($entity->getChildren()) );
        }

//        $accession = $entity->getChildren()->first();
//        $class = new \ReflectionClass($accession);
//        $className = $class->getShortName();
//        echo "findUniqueByKey: Procedure: className=".$className."<br>";

        $em = $this->_em;
        $foundAccession = $em->getRepository('OlegOrderformBundle:Accession')->findUniqueByKey( $entity->getChildren()->first() );    //,"Accession","accession");

        if( $foundAccession ) {
            echo "This entity alsready exists in DB ".$foundAccession."<br>";
            //get existing procedure
            return $foundAccession->getParent(); //Accession->getProcedure => procedure

        } else {
            return null;
        }
    }


    //TODO: remove MRN check (check procedure only by Accession number)
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

}
