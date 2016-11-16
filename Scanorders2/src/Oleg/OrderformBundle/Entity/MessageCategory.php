<?php

namespace Oleg\OrderformBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Oleg\UserdirectoryBundle\Entity\BaseCompositeNode;
use Oleg\UserdirectoryBundle\Entity\ComponentCategoryInterface;
use Oleg\UserdirectoryBundle\Entity\CompositeNodeInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Use Composite pattern:
 * The composite pattern describes that a group of objects is to be treated in the same
 * way as a single instance of an object. The intent of a composite is to "compose" objects into tree structures
 * to represent part-whole hierarchies. Implementing the composite pattern lets clients treat individual objects
 * and compositions uniformly.
 * Use Doctrine Extension Tree for tree manipulation.
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass="Oleg\UserdirectoryBundle\Repository\TreeRepository")
 * @ORM\Table(
 *  name="scan_messageCategory",
 *  indexes={
 *      @ORM\Index( name="messageCategory_name_idx", columns={"name"} ),
 *  }
 * )
 */
class MessageCategory extends BaseCompositeNode {

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="MessageCategory", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="MessageCategory", mappedBy="parent", cascade={"persist","remove"})
     * @ORM\OrderBy({"lft" = "ASC"})
     **/
    protected $children;

    /**
     * @ORM\OneToMany(targetEntity="MessageCategory", mappedBy="original", cascade={"persist"})
     **/
    protected $synonyms;

    /**
     * @ORM\ManyToOne(targetEntity="MessageCategory", inversedBy="synonyms", cascade={"persist"})
     * @ORM\JoinColumn(name="original_id", referencedColumnName="id", nullable=true)
     **/
    protected $original;


    /**
     * Message Type Classifiers - mapper between the level number and level title.
     * level corresponds to this level integer: 1-Message Class, 2-Message Subclass, 3-Service, 4-Issue
     * Default types have a positive level numbers, all other types have negative level numbers.
     *
     * @ORM\ManyToOne(targetEntity="MessageTypeClassifiers", cascade={"persist"})
     */
    private $organizationalGroupType;


//    /**
//     * a single form node can be used only by one message category
//     * @ORM\OneToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\FormNode", cascade={"persist"})
//     */
    /**
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\FormNode")
     * @ORM\JoinTable(name="scan_messageCategory_formNode",
     *      joinColumns={@ORM\JoinColumn(name="messageCategory_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="formNode_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    private $formNodes;


    public function __construct($author=null) {
        parent::__construct($author);

        $this->formNodes = new ArrayCollection();
    }


    /**
     * @param mixed $organizationalGroupType
     */
    public function setOrganizationalGroupType($organizationalGroupType)
    {
        $this->organizationalGroupType = $organizationalGroupType;
        $this->setLevel($organizationalGroupType->getLevel());
    }

    /**
     * @return mixed
     */
    public function getOrganizationalGroupType()
    {
        return $this->organizationalGroupType;
    }

    /**
     * @return mixed
     */
    public function getFormNodes()
    {
        return $this->formNodes;
    }
    public function addFormNode($item)
    {
        if( !$this->formNodes->contains($item) ) {
            $this->formNodes->add($item);
        }
        return $this;
    }
    public function removeFormNode($item)
    {
        $this->formNodes->removeElement($item);
    }



    /**
     * Overwrite base setParent method: adjust this organizationalGroupType according to the first parent child
     * @param mixed $parent
     */
    public function setParent(CompositeNodeInterface $parent = null)
    {
        $this->parent = $parent;

        //change organizationalGroupType of this entity to the first child organizationalGroupType of the parent if does not exist
        if( !$this->getOrganizationalGroupType() ) {
            if( $parent && count($parent->getChildren()) > 0 ) {
                //$firstSiblingOrgGroupType = $parent->getChildren()->first()->getOrganizationalGroupType();
                //$this->setOrganizationalGroupType($firstSiblingOrgGroupType);
                $defaultChild = $this->getFirstDefaultChild($parent);
                $defaultSiblingOrgGroupType = $defaultChild->getOrganizationalGroupType();
                if( $defaultSiblingOrgGroupType ) {
                    $this->setOrganizationalGroupType($defaultSiblingOrgGroupType);
                } else {
                    //get default organizational group
                }
            }
        }
    }



    public function getClassName() {
        return "MessageCategory";
    }




    public function __toString() {
        $parentName = "";
        if( $this->getParent() ) {
            $parentName = ", parent=".$this->getParent()->getName();
        }
        return "Category: ".$this->getName().", level=".$this->getLevel().", orderinlist=".$this->getOrderinlist().$parentName;
    }

}