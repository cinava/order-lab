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

namespace App\OrderformBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use App\OrderformBundle\Entity\StainList;

class StainTransformer implements DataTransformerInterface
{
    private $em;
    private $user;
    
    public function __construct(EntityManagerInterface $em=null, $user=null)
    {
        $this->em = $em;
        $this->user = $user;
    }

    /**
     * Transforms an object (issue) to a string (number).
     */
    public function transform($stain): mixed
    {

        if (null === $stain) {
            return "";
        }

        //echo "data transformer stain=".$stain."<br>";
        //echo "data transformer stain id=".$stain->getId()."<br>";

        if( is_int($stain) ) {
            //echo "transform stain by id=".$stain->getId()."<br>";
        //process.py script: replaced namespace by ::class: ['AppOrderformBundle:StainList'] by [StainList::class]
            $stain = $this->em->getRepository(StainList::class)->findOneById($stain);
            //echo "findOneById stain=".$stain."<br>";
        }
        
        if( null === $stain ) {
            return "";
        }

        return $stain->getId();
    }

    /**
     * Transforms a string (number) to an object (i.e. stain).
     *
     * @throws TransformationFailedException if object (stain) is not found.
     */
    public function reverseTransform($text): mixed
    {

        //echo "data transformer text=".$text."<br>";
        //exit();

        if (!$text) {
            //echo "return null".$text."<br>";
            return null;
        }

        if( is_numeric ( $text ) ) {    //number => most probably it is id

        //process.py script: replaced namespace by ::class: ['AppOrderformBundle:StainList'] by [StainList::class]
            $entity = $this->em->getRepository(StainList::class)->findOneById($text);

            if( null === $entity ) {

                return $this->createNewStain($text); //create a new record in db

            } else {

                return $entity; //use found object

            }

        } else {    //text => most probably it is new name

            //echo "text => most probably it is new name=".$text."<br>";
            return $this->createNewStain($text); //create a new record in db

        }

    }

    public function createNewStain($name) {

        //check if it is already exists in db
        //process.py script: replaced namespace by ::class: ['AppOrderformBundle:StainList'] by [StainList::class]
        $entity = $this->em->getRepository(StainList::class)->findOneByName($name);
        //echo "db entity=".$entity."<br>";
        if( null === $entity ) {

            $stain = new StainList();
            $stain->setName($name);
            $stain->setCreatedate(new \DateTime());
            $stain->setType('user-added');
            $stain->setCreator($this->user);

            //get max orderinlist
            //$query = $this->em->createQuery('SELECT MAX(c.orderinlist) as maxorderinlist FROM AppOrderformBundle:StainList c');
            $query = $this->em->createQuery('SELECT MAX(c.orderinlist) as maxorderinlist FROM App\\OrderformBundle\\Entity\\StainList c');
            $nextorder = $query->getSingleResult()['maxorderinlist']+10;          
            $stain->setOrderinlist($nextorder);
            
            $this->em->persist($stain);
            //$this->em->flush($stain);
            $this->em->flush();

            return $stain;
        } else {
            //echo "return db entity=".$entity."<br>";
            return $entity;
        }

    }


}