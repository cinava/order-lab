<?php
/**
 * Copyright (c) 2017 Cornell University
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace App\OrderformBundle\Repository;


/**
 * ProcedureRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProcedureRepository extends ArrayFieldAbstractRepository
{

    //exception for procedure: procedure is linked to a single accession => check if accession is already existed in DB, if existed => don't create procedure, but use existing procedure
    public function findUniqueByKey( $entity ) {

        //echo "find Unique By Key: Procedure: ".$entity;

        if( count($entity->getChildren()) != 1 ) {
            throw new \Exception( 'This entity must have only one child. Number of children=' . count($entity->getChildren()) );
        }

        $em = $this->_em;
        $foundAccession = $em->getRepository('AppOrderformBundle:Accession')->findUniqueByKey( $entity->getChildren()->first() );    //,"Accession","accession");

        if( $foundAccession ) {
            //echo "This entity alsready exists in DB ".$foundAccession."<br>";
            //get existing procedure
            return $foundAccession->getParent(); //Accession->getProcedure => procedure

        } else {
            return null;
        }
    }

    //make sure procedure type is set to "Auto-generated Procedure Number"
    public function changeKeytype($entity) {

        $key = $entity->obtainValidKeyField();

        if( !$key->getKeytype() || $key->getKeytype() == "" ) {
            //throw new \Exception( 'Procedure does not have a valid keytype. keytype=' . $key->getKeytype() );
            $em = $this->_em;
            $newkeytypeEntity = $em->getRepository('AppOrderformBundle:ProcedureType')->findOneByName("Auto-generated Procedure Number");
            $key->setKeytype($newkeytypeEntity);
        }

        if( $key == "" || $key->getField() != "Auto-generated Procedure Number" ) {
            $em = $this->_em;
            $newkeytypeEntity = $em->getRepository('AppOrderformBundle:ProcedureType')->findOneByName("Auto-generated Procedure Number");
            $key->setKeytype($newkeytypeEntity);
        }

        //strip zeros and record original
        $originalKey = $key->getField();
        $key->setOriginal($originalKey);
        $stripedKey = ltrim($originalKey, '0');
        $key->setField($stripedKey);

        return $entity;
    }

    //replace child if duplicated
    //$parent: encounter
    //procedure has only one accession
    public function replaceDuplicateEntities( $parent, $message ) {
        //echo "Procedure replace duplicates:".$parent;
        return $parent;
    }



    //process conflict if exists for accession number. Replace conflicting accession number by a new generated number.
    //This function redirects to the same overrided function by Accession Repository
    public function processDuplicationKeyField( $procedure, $message ) {

        $accessions = $procedure->getChildren();

        if( count($accessions) != 1 ) {
            throw new \Exception( 'Procedure entity must have only one Accession. Number of Accession found is ' . count($accessions) );
        }

        $accession = $accessions->first();

        $procedure->removeChildren($accession);

        //process conflict if exists for accession number. Replace conflicting accession number by a new generated number.
        $accession = $this->_em->getRepository('AppOrderformBundle:Accession')->processDuplicationKeyField($accession,$message);

        $procedure->addChildren($accession);

        return $procedure;
    }

}