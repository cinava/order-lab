<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 4/23/14
 * Time: 3:16 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\UserdirectoryBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ListAbstractRepository extends EntityRepository {

    //inputs: name, class name, user, parent field name, parent
    //output: new list entity (i.e. ProjectTitleList or SetTitleList)
    public function convertStrToObject( $name, $objectParams, $user, $parentFieldName = null, $parentId=null ) {

        if( !$name || $name == '' ) {
            return NULL;
        }

        $criterions = array( 'name' => $name );

        //echo "use parentId=".$parentId.", fieldname=".$parentFieldName."<br>";
        if( $parentFieldName ) {
            if( !$parentId ) {
                $parentId = -1; //if parentId is not set yet (object does not exists), force not found to create a new entity
            }
            //echo "use parentId=".$parentId."<br>";
            $criterions[$parentFieldName] = $parentId;
        }


        $entity = $this->_em->getRepository($objectParams['fullBundleName'].':'.$objectParams['className'])->findOneBy( $criterions );

        if( !$entity ) {
            echo $objectParams['className'].': not found <br>';
            //create a new setTitle
            $entity = $this->createNewListEntity($objectParams,$name,$user);
        } else {
            //echo $objectParams['className'].': found <br>';
        }

        return $entity;

    }

    //create a new List Entity (i.e. setTitle)
    public function createNewListEntity( $objectParams, $name, $user ) {

        //$className = "SetTitleList";
        $entityClass = $objectParams['fullClassName'];
        $newEntity = new $entityClass();
        $newEntity->setName($name);
        $newEntity->setCreatedate(new \DateTime());
        $newEntity->setType('default');
        $newEntity->setCreator($user);

        //get max orderinlist
        $query = $this->_em->createQuery('SELECT MAX(c.orderinlist) as maxorderinlist FROM '.$objectParams['fullBundleName'].':'.$objectParams['className'].' c');
        $nextorder = $query->getSingleResult()['maxorderinlist']+10;
        $newEntity->setOrderinlist($nextorder);

        return $newEntity;
    }

}