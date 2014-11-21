<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 10/24/13
 * Time: 12:14 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class ListAbstract
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string")
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $abbreviation;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    protected $type;

    /**
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * @Assert\NotBlank
     */
    protected $creator;

    /**
     * @var \DateTime
     * @ORM\Column(name="createdate", type="datetime")
     * @Assert\NotBlank
     */
    protected $createdate;

    /**
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\User")
     * @ORM\JoinColumn(name="updatedby_id", referencedColumnName="id",nullable=true)
     */
    protected $updatedby;

    /**
     * @var \DateTime
     * @ORM\Column(name="updatedon", type="datetime", nullable=true)
     */
    protected $updatedon;

    /**
     * Indicates the order in the list
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    protected $orderinlist;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $updateAuthorRoles = array();



    public function __construct() {

        $this->synonyms = new ArrayCollection();

    }




    public function addSynonym($synonyms)
    {
        if( !$this->synonyms->contains($synonyms) ) {
            $this->synonyms->add($synonyms);
        }
        return $this;
    }

    public function removeSynonym($synonyms)
    {
        $this->synonyms->removeElement($synonyms);
    }

    /**
     * Get synonyms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSynonyms()
    {
        return $this->synonyms;
    }

    /**
     * @param mixed $original
     */
    public function setOriginal($original)
    {
        $this->original = $original;
    }

    /**
     * @return mixed
     */
    public function getOriginal()
    {
        return $this->original;
    }




    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return List
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return List
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name."";
    }

    /**
     * @param mixed $abbreviation
     */
    public function setAbbreviation($abbreviation)
    {
        $this->abbreviation = $abbreviation;
    }

    /**
     * @return mixed
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }



    /**
     * Set type
     *
     * @param string $type
     * @return List
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createdate
     *
     * @param \DateTime $createdate
     * @return List
     */
    public function setCreatedate($createdate)
    {
        $this->createdate = $createdate;

        return $this;
    }

    /**
     * Get createdate
     *
     * @return \DateTime
     */
    public function getCreatedate()
    {
        return $this->createdate;
    }

    /**
     * Set creator
     *
     * @param \Oleg\UserdirectoryBundle\Entity\User $creator
     * @return List
     */
    public function setCreator(\Oleg\UserdirectoryBundle\Entity\User $creator=null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \Oleg\UserdirectoryBundle\Entity\User $creator
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param mixed $orderinlist
     */
    public function setOrderinlist($orderinlist)
    {
        $this->orderinlist = $orderinlist;
    }

    /**
     * @return mixed
     */
    public function getOrderinlist()
    {
        return $this->orderinlist;
    }


    public function __toString()
    {
        return $this->name."";
    }

    /**
     * @param mixed $updatedby
     */
    public function setUpdatedby($user)
    {
        //if( $user ) {
            $this->updatedby = $user;
        //}
    }

    /**
     * @return mixed
     */
    public function getUpdatedby()
    {
        return $this->updatedby;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedon()
    {
        if( $this->id ) {
            $this->updatedon = new \DateTime();
        }
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedon()
    {
        return $this->updatedon;
    }

    public function isEmpty() {
        if( $this->name == '' ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getUpdateAuthorRoles()
    {
        return $this->updateAuthorRoles;
    }


    public function setUpdateAuthorRoles($roles) {
        foreach( $roles as $role ) {
            $this->addUpdateAuthorRole($role."");
        }
    }

    public function addUpdateAuthorRole($role) {
        $role = strtoupper($role);
        if( !in_array($role, $this->updateAuthorRoles, true) ) {
            $this->updateAuthorRoles[] = $role;
        }
    }

    //for entity with synonyms
//    public function setSynonyms($synonyms = null) {
//        echo "set synonym=".$synonyms."<br>";
//        exit();
//        $newsynonyms = new ArrayCollection();
//        if( $synonyms ) {
//            $newsynonyms->add($synonyms);
//            $this->synonyms = $newsynonyms;
//        } else {
//            $this->synonyms = $newsynonyms;
//        }
//        return $this;
//    }


}