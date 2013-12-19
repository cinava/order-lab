<?php

namespace Oleg\OrderformBundle\Repository;


/**
 * PatientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PatientRepository extends ArrayFieldAbstractRepository
{

    public function getExtraEntityById( $extra ) {
        $em = $this->_em;
        return $em->getRepository('OlegOrderformBundle:MrnType')->findOneById($extra["mrntype"]);
    }

    //replace field entity if not existed from source object to destination object
    public function copyField( $entity, $field, $className, $methodName, $fields ) {
        $em = $this->_em;
        //echo "copyField!!! (Patient): class=".$className.$methodName.", id=".$field->getId().", field=".$field."<br>";

        //for Patient $field is not ID, but field value MRN number.
        //if id=null, check if entity already has mrn field (mrn+mrntype)
        if( !$field->getId() || $field->getId() == null || $field->getId() == "" ) {
            $foundFields = $em->getRepository('OlegOrderformBundle:'.$className.$methodName)->findByField($field."");
            //echo "count foundFields=".count($foundFields)."<br>";
            foreach( $foundFields as $thisField ) {
                //echo "mrntype ids compare: ".$thisField->getMrntype()->getId() . "?=" . $field->getMrntype()->getId() . "<br>";
                if( $thisField->getMrntype()->getId() == $field->getMrntype()->getId() && $thisField->getStatus() == self::STATUS_VALID ) {
                    //this field is already exists in entity => don't add this field
                    return $entity;
                }
            }
        }

        //if we reach this point, then now we have $field->getId(), exception - if not
        if( !$field->getId() || $field->getId() == null || $field->getId() == "" ) {
            throw new \Exception( 'Object '.$className.' does not have ID for field:'.$methodName );
        }

        $found = $em->getRepository('OlegOrderformBundle:'.$className.$methodName)->findOneById($field->getId());

        if( !$found ) {
            //echo( "### ".$methodName." not found !!!!!! => add <br>" );
            $methodName = "add".$methodName;
            $entity->$methodName( $field );
        } else {
            //
        }

        return $entity;
    }

    //filter out duplicate virtual (in form, not in DB) patients
    //after js check form, theoretically we should not have duplicate entities submitted by the form, but let's have it just in case ...
    public function removeDuplicateEntities( $entity ) {

        $patients = $entity->getPatient();

        if( count($patients) == 1 ) {
            return $entity;
        }

        $mrns = array();

        foreach( $patients as $patient ) {

            $mrn = $patient->getMrn();

            if( $mrn != null && $mrn != "" ) {
                if( count($mrns) == 0 || !in_array($mrn, $mrns) ) {
                    $mrns[] = $mrn;
                    //persist the rest of entities, because they will be added to DB.
                    $em = $this->_em;
                    $em->persist($patient);
                } else {
                    //echo "remove pat:".$patient;
                    $entity->removePatient($patient);
                }
            }
        }

        return $entity;
    }
    
}
