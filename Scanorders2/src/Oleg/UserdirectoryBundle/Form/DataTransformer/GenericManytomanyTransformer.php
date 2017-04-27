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

/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 9/12/13
 * Time: 3:47 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\UserdirectoryBundle\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

use Oleg\UserdirectoryBundle\Entity\User;
use Oleg\UserdirectoryBundle\Security\Util\UserSecurityUtil;

class GenericManyToManyTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    protected $em;
    protected $user;
    protected $bundleName;
    protected $className;
    protected $params;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $em=null, $user=null, $className=null, $bundleName=null, $params=null)
    {
        $this->em = $em;
        $this->user = $user;
        $this->bundleName = $bundleName;
        $this->className = $className;
        $this->params = $params;
    }

    public function getThisEm() {
        return $this->em;
    }


    /**
     * Transforms an array of objects or name strings to ids.
     */
    public function transform( $entities )
    {
        //echo $entities->first()->getName()."<br>";
        //echo "!!!!!!!!!!!transform: entities=".$entities."<br>";
        //echo $this->className.": transform: count=".count($entities)."<br>";
        //var_dump($entities);

        $array = new \Doctrine\Common\Collections\ArrayCollection();

        if( !$entities || null === $entities->toArray() ) {
            //echo $this->className.": return empty array";
            return $array;
        }

        if( count($entities) == 0 ) {
            return null;
        }

        if( count($entities) > 0 ) {
            $idArr = [];
            foreach( $entities as $entity ) {
                if( $entity ) {
                    //echo $entity;
                    $idArr[] = $entity->getId();
                }
            }

            //return array with primaryPrincipal as the first element
            //echo "idArr:<br>";
            //var_dump($idArr);
            //echo "return:".implode(",", $idArr)."<br>";

            return implode(",", $idArr);
        }

        return $entities->first()->getId();
    }

    /**
     * Transforms a string (number) to an object.
     *
     * @param  string $number
     *
     * @return Stain|null
     *
     * @throws TransformationFailedException if object (stain) is not found.
     */
    public function reverseTransform($text)
    {

        //echo "!!!!!!!!!!!data reverse transformer text=".$text."<br>";
        //exit();

        $newListArr = new \Doctrine\Common\Collections\ArrayCollection();

        if( !$text ) {
            //echo "return empty array <br>";
            return $newListArr;
        }

        //echo "text array<br>";
        //exit();
        $textArr = explode(",", $text);
        foreach( $textArr as $entity ) {
            $newListArr = $this->addEntity( $newListArr, $entity );
        }

        //echo "reverseTransform: return count:".count($newListArr)."<br>";
        return $newListArr;
    }

    public function addEntity( $newListArr, $entity ) {

        if( is_numeric ( $entity ) ) {    //number => most probably it is id

            //echo "principal=".$username." => numeric => most probably it is id<br>";

            $entity = $this->em->getRepository('Oleg'.$this->bundleName.':'.$this->className)->findOneById($entity);

            if( null === $entity ) {

                $newList = $this->createNew($entity); //create a new record in db

                $newListArr->add($newList);

                return $newListArr;

            } else {

                $newListArr->add($entity);

                return $newListArr;

            }

        } else {    //text => most probably it is new name or multiple ids

            //echo "principal=".$username." => text => most probably it is new name or multiple ids<br>";

            $newList = $this->createNew($entity); //create a new record in db

            if( $newList ) {
                //echo "newList=".$newList."<br>";
                $newListArr->add($newList);
            }

            return $newListArr;

        }

    }

    public function createNew($name) {

        //echo "enter create new name=".$name."<br>";
        //exit('create new !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');

        if( !$name || $name == "" ) {
            //exit('child name is NULL');
            return null;
        }

        //check if it is already exists in db
        $entity = $this->em->getRepository('Oleg'.$this->bundleName.':'.$this->className)->findOneByName($name."");
        
        if( null === $entity ) {

            //echo "create new with name=".$name."<br>";
            //echo "user=".$this->user."<br>"; //user must be an object (exist in DB)
            if( !$this->user instanceof User ) {
                //user = system user
                $userSecUtil = new UserSecurityUtil($this->em,null,null);
                $this->user = $userSecUtil->findSystemUser();
            }

            $newEntity = $this->createNewEntity($name."",$this->className,$this->user);

            if( method_exists($newEntity,'getParent')  ) {
                //don't flush this entity because it has parent and parent can not be set here
                //echo "this entity has parent => don't create <br>";
                //echo "name=".$newEntity->getName()."<br>";
                //$this->em->persist($newEntity);
                return $newEntity;
            }

            //echo "persist and flush !!!!!!!!!!!!!!!! <br>";
            $this->em->persist($newEntity);
            $this->em->flush($newEntity);

            return $newEntity;
        } else {

            return $entity;
        }

    }


    public function createNewEntity($name,$className,$creator) {

        if( !$name || $name == "" ) {
            return null;
        }

        $fullClassName = "Oleg\\".$this->bundleName."\\Entity\\".$className;
        $newEntity = new $fullClassName();

        //add default type
        $userSecUtil = new UserSecurityUtil($this->em,null,null);
        $newEntity = $userSecUtil->addDefaultType($newEntity,$this->params);

        $newEntity = $this->populateEntity($newEntity);

        $newEntity->setName($name."");
        $newEntity->setCreator($creator);

        return $newEntity;
    }

    public function populateEntity($entity) {
        //exit('1');

        $entity->setCreatedate(new \DateTime());
        $entity->setType('user-added');

        $fullClassName = new \ReflectionClass($entity);
        $className = $fullClassName->getShortName();

        //get max orderinlist
        $query = $this->em->createQuery('SELECT MAX(c.orderinlist) as maxorderinlist FROM Oleg'.$this->bundleName.':'.$className.' c');
        $nextorder = $query->getSingleResult()['maxorderinlist']+10;
        $entity->setOrderinlist($nextorder);

        return $entity;
    }

}