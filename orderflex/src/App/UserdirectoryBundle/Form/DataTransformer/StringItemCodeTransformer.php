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

namespace App\UserdirectoryBundle\Form\DataTransformer;

use App\TranslationalResearchBundle\Entity\RequestCategoryTypeList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class StringItemCodeTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $em;
    private $user;

    /**
     * @param ObjectManager $em
     */
    public function __construct(EntityManagerInterface $em=null, $user=null)
    {
        $this->em = $em;
        $this->user = $user;
    }

    /**
     * Transforms an object to a string.
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($entity)
    {
        //echo "string data transformer: ".$entity."<br>";
        if (null === $entity) {
            //echo "return empty <br>";
            return "";
        }

//        if( $entity instanceof RequestCategoryTypeList ) {
//            return $entity->getProductId();
//            //return $entity->getProductId()."-i";
//        }

        //echo "return entity:".$entity." <br>";
        return $entity;  //Scan Region: entity is a string
    }

    /**
     * Transforms a string (number) to an object (i.e. stain).
     *
     * @param  string $number
     *
     * @return Stain|null
     *
     * @throws TransformationFailedException if object (stain) is not found.
     */
    public function reverseTransform($text)
    {
        //echo "data reverseTransform text=".$text."<br>";
        //exit();

        if (!$text) {
            return null;
        }

//        if( $text instanceof RequestCategoryTypeList ) {
//            return $text->getProductId();
//            //return $text->getProductId()."-i";
//        }

        return $text;
    }

}