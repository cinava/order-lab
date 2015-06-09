<?php

namespace Oleg\UserdirectoryBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_generalEntity")
 */
class GeneralEntity
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    //Fields specifying a subject entity
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $entityNamespace;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $entityName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $entityId;





    /**
     * @param mixed $entityId
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @param mixed $entityName
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;
    }

    /**
     * @return mixed
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @param mixed $entityNamespace
     */
    public function setEntityNamespace($entityNamespace)
    {
        $this->entityNamespace = $entityNamespace;
    }

    /**
     * @return mixed
     */
    public function getEntityNamespace()
    {
        return $this->entityNamespace;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setObject($object) {
        $class = new \ReflectionClass($object);
        $className = $class->getShortName();
        $classNamespace = $class->getNamespaceName();

        if( $className && !$this->getEntityName() ) {
            $this->setEntityName($className);
        }

        if( $classNamespace && !$this->getEntityNamespace() ) {
            $this->setEntityNamespace($classNamespace);
        }

        if( !$this->getEntityId() && $object->getId() ) {
            //echo "setEntityId=".$object->getId()."<br>";
            $this->setEntityId($object->getId());
        }
    }

    public function getFullName() {
        if( $this->getId() ) {
            return $this->getEntityName() . " ID=" . $this->getEntityId();
        } else {
            return $this->getEntityName();
        }
    }


    public function __toString() {
        return $this->getFullName();
    }
}