<?php

namespace Oleg\OrderformBundle\Repository;


use Oleg\OrderformBundle\Entity\PatientLastName;
use Oleg\OrderformBundle\Entity\PatientFirstName;
use Oleg\OrderformBundle\Entity\PatientMiddleName;
use Oleg\OrderformBundle\Entity\PatientSex;
use Oleg\OrderformBundle\Entity\PatientAge;

/**
 * ProcedureRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProcedureRepository extends ArrayFieldAbstractRepository
{

    //$entity is procedure
    public function processEntity( $entity, $orderinfo ) {

        if( !$entity ) {
            throw new \Exception('Provided entity for processing is null');
            //return $entity;
        }

        $em = $this->_em;
        $class = new \ReflectionClass($entity);
        $className = $class->getShortName();

        //echo "<br>processEntity className (overwrited by procedure)=".$className.", keyFieldName=".$entity->obtainKeyFieldName()."<br>";
        //echo $entity;

        //check and remove duplication objects such as two Part 'A'.
        $entity = $em->getRepository('OlegOrderformBundle:'.$className)->replaceDuplicateEntities( $entity, $orderinfo );

        //process conflict if exists for accession number. Replace conflicting accession number by a new generated number.
        $entity = $em->getRepository('OlegOrderformBundle:'.$className)->processDuplicationKeyField($entity,$orderinfo);

        $keys = $entity->obtainAllKeyfield();

        //echo "count keys=".count($keys)."<br>";
        //echo "key=".$keys->first()."<br>";

        if( count($keys) == 0 ) {
            $entity->createKeyField();  //this can happen for procedure, because key and keytype fields are hidden in the form
            //throw new \Exception( 'Key field does not exists for '.$className );
        } elseif( count($keys) > 1 ) {
            //throw new \Exception( 'This Object ' . $className . ' must have only one key field. Number of key field=' . count($keys) );
            //echo( 'This Object ' . $className . ' should have only one key field. Number of key field=' . count($keys) );
        }

        $key = $entity->obtainValidKeyField();
        //echo "valid key=".$key.", status=".$key->getStatus()."<br>";

        //change keytype from Existing Auto-generated MRN to Existing Auto-generated MRN
        $entity = $this->changeKeytype($entity);

        if( $orderinfo->getStatus() == 'Amended' ) {
            $found = null;
        } else {
            //this is a main function to check uniqueness
            $found = $this->findUniqueByKey($entity);   //$found - procedure in DB
        }

        if( $found ) {
            //echo "Case 2 (Procedure): object exists in DB (eneterd key is for existing object): Copy Children, Copy Fields <br>";
            //CopyChildren: copy form's object children to the found one.
            foreach( $entity->getChildren() as $child ) {
                //echo "adding: ".$child."<br>";
                $found->addChildren( $child );
            }

            //procedure were obtained from accession, so it's not persisted.
            $em->persist($found);

            //add procedure's name, sex, age to the corresponding patient fields
            //$this->copyCommonFieldsToPatient($entity,$orderinfo->getProvider());

            return $this->setResult($found, $orderinfo, $entity);

        } else
        if( $key == "" ) {
            //echo "Case 1: Empty form object (all fields are empty): generate next available key and assign to this object <br>";

            $newkeytypeEntity = $em->getRepository('OlegOrderformBundle:EncounterType')->findOneByName("Auto-generated Encounter Number");
            $key->setKeytype($newkeytypeEntity);

            $nextKey = $this->getNextNonProvided($entity,null,$orderinfo);  //"NO".strtoupper($fieldName)."PROVIDED", $className, $fieldName);

            //we should have only one key field !!!
            $key->setField($nextKey);
            $key->setStatus(self::STATUS_VALID);
            $key->setProvider($orderinfo->getProvider());

        }
        else {
            //echo "Case 3: object does not exist in DB (new key is eneterd) or it's amend <br>";
            //throw new \Exception('Invalid logic for Procedure, key='.$key);
        }

        $accessions = $entity->getAccession();
        if( count($accessions) > 1 ) {
            throw new \Exception( 'More than one Accession in the Procedure. Number of accession=' . count($accessions) );
        }

        //add procedure's name, sex, age to the corresponding patient fields in case if this is a new procedure (not found in DB)
        $this->copyCommonFieldsToPatient($entity,$orderinfo->getProvider());

        return $this->setResult($entity, $orderinfo);

    }

    //add procedure's name, sex, age to the corresponding patient fields
    public function copyCommonFieldsToPatient($procedure,$user) {

        $patient = $procedure->getParent();

        $source = "scanorder";
        $status = self::STATUS_VALID;

        //lastname
        //echo "proc last name count=".count($procedure->getPatlastname())."<br>";
        if( count($procedure->getPatlastname()) > 0 ) {
            if( $this->validFieldIsSet( $patient->getLastname() ) ) {
                $status = self::STATUS_INVALID;
            }
            $patientlastname = new PatientLastName($status,$user,$source);
            $patientlastname->setField($procedure->getPatlastname()->first()->getField());
            $patientlastname->setProcedure($procedure);
            $patient->addLastname($patientlastname);
        }

        //firstname
        if( count($procedure->getPatfirstname()) > 0 ) {
            if( $this->validFieldIsSet( $patient->getFirstname() ) ) {
                $status = self::STATUS_INVALID;
            }
            $patientfirstname = new PatientFirstName($status,$user,$source);
            $patientfirstname->setField($procedure->getPatfirstname()->first()->getField());
            $patientfirstname->setProcedure($procedure);
            $patient->addFirstname($patientfirstname);
        }

        //middlename
        if( count($procedure->getPatmiddlename()) > 0 ) {
            if( $this->validFieldIsSet( $patient->getMiddlename() ) ) {
                $status = self::STATUS_INVALID;
            }
            $patientmiddlename = new PatientMiddleName($status,$user,$source);
            $patientmiddlename->setField($procedure->getPatmiddlename()->first()->getField());
            $patientmiddlename->setProcedure($procedure);
            $patient->addMiddlename($patientmiddlename);
        }

        //sex
        if( count($procedure->getPatsex()) > 0 ) {
            if( $this->validFieldIsSet( $patient->getSex() ) ) {
                $status = self::STATUS_INVALID;
            }
            $patientsex = new PatientSex($status,$user,$source);
            //echo "procedure sex=".$procedure->getPatsex()->first()."<br>";
            $patientsex->setField($procedure->getPatsex()->first());
            $patientsex->setProcedure($procedure);
            $patient->addSex($patientsex);
        }

        //age
        if( count($procedure->getPatage()) > 0 ) {
            if( $this->validFieldIsSet( $patient->getAge() ) ) {
                $status = self::STATUS_INVALID;
            }
            $patientage = new PatientAge($status,$user,$source);
            //echo "procedure age=".$procedure->getPatage()->first()->getField()."<br>";
            $patientage->setField($procedure->getPatage()->first()->getField());
            $patientage->setProcedure($procedure);
            $patient->addAge($patientage);
        }

    }



    //exception for procedure: procedure is linked to a single accession => check if accession is already existed in DB, if existed => don't create procedure, but use existing procedure
    public function findUniqueByKey( $entity ) {

        //echo "findUniqueByKey: Procedure: ".$entity;

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
            //echo "This entity alsready exists in DB ".$foundAccession."<br>";
            //get existing procedure
            return $foundAccession->getParent(); //Accession->getProcedure => procedure

        } else {
            return null;
        }
    }

    //replace child if duplicated
    //$parent: patient
    //procedure has only one accession
    public function replaceDuplicateEntities( $parent, $orderinfo ) {
        //echo "Procedure replace duplicates:".$parent;
        return $parent;
    }



    //find similar procedure in patient.
    //However, procedure is identified by accession number
    //$parent: patient
    //$newChild: accession
    //find similar child and return the first one
    //return false if no similar children are found
    public function findSimilarChild($parent,$newChild) {
        //echo "Procedure: find similar Child to: ".$newChild." <br>";

        $children = $parent->getChildren();

        //echo "<br>";
        //echo $newChild;
        //echo "newChild key=".$newChild->obtainValidKeyfield()."<br>";
        if( $newChild->obtainValidKeyfield()."" == "" ) {   //no name is provided, so can't compare => does not exist
            //echo "false: no name <br>";
            return false;
        }

        if( !$children || count($children) == 0 ) { //no children => does not exist
            //echo "false: no children <br>";
            return false;
        }

        foreach( $children as $child ) {
            //echo $child;

            if( count($child->getAccession()) != 1 ) {
                throw new \Exception( 'This entity must have only one child. Number of children=' . count($child->getAccession()) );
            }

            if( $child->getAccession()->first() === $newChild ) {
                //echo "the same child: continue<br>";
                return false;
            }

            if( $this->entityEqualByComplexKey($child->getAccession()->first(), $newChild) ) {
                //echo "MATCH!: ".$child." <br>";
                return $child;
            } else {
                //echo "NO MATCH! <br>";
            }

        }//foreach

        return false;
    }


    //process conflict if exists for accession number. Replace conflicting accession number by a new generated number.
    //This function redirects to the same overrided function by Accession Repository
    public function processDuplicationKeyField( $procedure, $orderinfo ) {

        $accessions = $procedure->getChildren();

//        foreach( $accessions as $acc ) {
//            echo $acc."<br>";
//        }

        if( count($accessions) != 1 ) {
            throw new \Exception( 'Procedure entity must have only one Accession. Number of Accession found is ' . count($accessions) );
        }

        $accession = $accessions->first();

        $procedure->removeChildren($accession);

        //process conflict if exists for accession number. Replace conflicting accession number by a new generated number.
        $accession = $this->_em->getRepository('OlegOrderformBundle:Accession')->processDuplicationKeyField($accession,$orderinfo);

        $procedure->addChildren($accession);

        return $procedure;
    }

}
