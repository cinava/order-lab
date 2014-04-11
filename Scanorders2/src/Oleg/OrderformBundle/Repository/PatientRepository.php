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

    public function changeKeytype($entity) {
        $key = $entity->obtainValidKeyField();
        $newkeytypeid = $this->getCorrectKeytypeId( $key->getKeytype()->getId() );
        if( $key == "" || $newkeytypeid != $key->getKeytype()->getId() ) {  //$key == "" is the same as $key->getName().""
            $em = $this->_em;
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

//    //replace field entity if not existed from source object to destination object
//    public function copyField_TODEL( $entity, $field, $className, $methodName, $fields ) {
//        $em = $this->_em;
//        echo "copyField!!! (Patient): class=".$className.$methodName.", id=".$field->getId().", field=".$field."<br>";
//
//        //echo $methodName.": this fields count=".count($fields)."<br>";
//
//        //if similar field is already set and provided field is empty => don't add provided field
//        if( !$field || trim($field) == "" ) {
//            if( $this->validFieldIsSet( $fields ) ) {
//                //echo "field is empty and non empty valid field exists => don't add provided field => return<br>";
//                return $entity;
//            }
//        }
//
//        //for Patient $field is not ID, but field value MRN number.
//        //if id=null, check if entity already has mrn field (mrn+mrntype)
//        if( !$field->getId() || $field->getId() == null || $field->getId() == "" ) {
//            //echo "field value=".$field."<br>";
//            $foundFields = $em->getRepository('OlegOrderformBundle:'.$className.$methodName)->findByField($field.""); //PatientMrn
//            //echo "count foundFields=".count($foundFields)."<br>";
//            foreach( $foundFields as $thisField ) {
//                echo "thisField=".$thisField->getField().", field=".$field->getField().", original=".$field->getOriginal()."<br>";
//                echo "field id=".$field->getId()."<br>";
//                if( $thisField->getId() == $field->getId() && $thisField->getStatus() == self::STATUS_VALID ) {
//                    //this field is already exists in entity => don't add this field
//                    return $entity;
//                }
//            }
//        }
//
//        //if we reach this point, then now we have $field->getId(), exception - if not
//        if( !$field->getId() || $field->getId() == null || $field->getId() == "" ) {
//            throw new \Exception( 'Object '.$className.' does not have ID for field:'.$methodName );
//        }
//
//        $found = $em->getRepository('OlegOrderformBundle:'.$className.$methodName)->findOneById($field->getId());
//
//        if( !$found ) {
//            //echo( "### ".$methodName." not found !!!!!! => add <br>" );
//            $methodName = "add".$methodName;
//            $entity->$methodName( $field );
//        } else {
//            //
//        }
//
//        return $entity;
//    }

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
