<?php
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

class ResearchLabTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $em;
    private $user;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $em=null, $user=null, $className=null)
    {
        $this->em = $em;
        $this->user = $user;
        $this->className = $className;
    }

    /**
     * Transforms id or name to an object
     */
    public function transform($entity)
    {
        if( null === $entity || $entity == "" ) {
            return "";
        }

        //echo "data transformer entity=".$entity."<br>";
        //echo "data transformer entity id=".$entity->getId()."<br>";

        if( is_int($entity) ) {
            //echo "transform by name=".$entity." !!!<br>";
            $entity = $this->em->getRepository('OlegUserdirectoryBundle:'.$this->className)->find($entity);
            //echo "findOneById entity=".$entity."<br>";
        }
        else {
            //echo "transform by name=".$entity." ????????????????<br>";
            $entity = $this->em->getRepository('OlegUserdirectoryBundle:'.$this->className)->findOneByName($entity);
        }

        if( null === $entity ) {
            return "";
        }

        //return $entity->getId();

        //echo "count=".count($entity)."<br>";

        return $entity->getId();
    }

    /**
     * Transforms a string (number) to an object (i.e. stain).
     */
    public function reverseTransform($text)
    {
        //echo "data reverseTransform text=".$text."<br>";
        //exit();

        if (!$text) {
            return null;
        }

        if( is_numeric ( $text ) ) {    //number => most probably it is id
            //echo 'text is id <br>';
            $entity = $this->em->getRepository('OlegUserdirectoryBundle:'.$this->className)->findOneById($text);

            if( $entity ) {
                return $entity->getName();
            }
        }

        return $text;
    }

}