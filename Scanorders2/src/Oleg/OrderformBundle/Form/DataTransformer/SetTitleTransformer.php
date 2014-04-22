<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 9/12/13
 * Time: 3:47 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\OrderformBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class SetTitleTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $em;
    private $user;
    private $className;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $em=null, $user=null, $className=null)
    {
        $this->em = $em;
        $this->user = $user;
        $this->className = $className;
    }

    public function getThisEm() {
        return $this->em;
    }

    /**
     * Use for 'Show'
     *
     * Transforms an object to a string.
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($type)
    {        

        if (null === $type) {
            return "";
        }

        //$type = $type->first();

        echo $this->className.":data transformer type=".$type."<br>";

        if( is_int($type) ) {
            $type = $this->em->getRepository('OlegOrderformBundle:'.$this->className)->findOneById($type);
            //echo "findOneById type=".$type."<br>";
        } else {
            //echo "not int <br>";
            $type = $this->em->getRepository('OlegOrderformBundle:'.$this->className)->findOneByName($type."");
        }
        
        if( null === $type ) {
            //echo "not found<br>";
            return "";
        }

        //echo "setTitles: data transformer id=".$type->getId()."<br>";

        return $type->getId();

    }

    /**
     * Use for 'Submit'
     *
     * Transforms a string (number) to an object (i.e. stain).
     *
     * @param  string $number
     *
     * @return $this->className|null
     *
     * @throws TransformationFailedException if object ($this->className) is not found.
     */
    public function reverseTransform($text)
    {

        echo "data reverse transformer text=".$text."<br>";
        //exit();

        if (!$text) {
            return null;
        }

        if( is_numeric ( $text ) ) {    //number => most probably it is id

            $entity = $this->em->getRepository('OlegOrderformBundle:'.$this->className)->findOneById($text);

            if( null === $entity ) {

                return $this->createNew($text); //create a new record in db

            } else {

                return $entity; //use found object

            }

        } else {    //text => most probably it is new name

            return $this->createNew($text); //create a new record in db

        }


    }

    public function createNew($name) {

        //echo "new: name=".$name."<br>";

        //check if it is already exists in db
        $entity = $this->em->getRepository('OlegOrderformBundle:'.$this->className)->findOneByName($name);
        
        if( null === $entity ) {
            $entityClass = "Oleg\\OrderformBundle\\Entity\\".$this->className;
            $newEntity = new $entityClass();
            $newEntity->setName($name);
            $newEntity->setCreatedate(new \DateTime());
            $newEntity->setType('default');
            $newEntity->setCreator($this->user);
            
            //get max orderinlist
            $query = $this->em->createQuery('SELECT MAX(c.orderinlist) as maxorderinlist FROM OlegOrderformBundle:'.$this->className.' c');
            $nextorder = $query->getSingleResult()['maxorderinlist']+10;          
            $newEntity->setOrderinlist($nextorder);

            $this->em->persist($newEntity);
            $this->em->flush($newEntity);

            return $newEntity;
        } else {

            return $entity;
        }

    }


}