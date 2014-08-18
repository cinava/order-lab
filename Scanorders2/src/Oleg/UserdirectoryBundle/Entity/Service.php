<?php

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="service")
 */
class Service extends ListAbstract
{
    /**
     * @ORM\OneToMany(targetEntity="Division", mappedBy="original")
     **/
    protected $synonyms;

    /**
     * @ORM\ManyToOne(targetEntity="Division", inversedBy="synonyms")
     * @ORM\JoinColumn(name="original_id", referencedColumnName="id")
     **/
    protected $original;

    /**
     * Parent
     * @ORM\ManyToOne(targetEntity="Division", inversedBy="services")
     * @ORM\JoinColumn(name="division", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $division;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="service")
     **/
    protected $users;



    public function __construct() {
        $this->synonyms = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * Add synonyms
     *
     * @param \Oleg\UserdirectoryBundle\Entity\Division $synonyms
     * @return Division
     */
    public function addSynonym(\Oleg\UserdirectoryBundle\Entity\Division $synonyms)
    {
        $this->synonyms->add($synonyms);
        return $this;
    }

    /**
     * Remove synonyms
     *
     * @param \Oleg\UserdirectoryBundle\Entity\Division $synonyms
     */
    public function removeSynonym(\Oleg\UserdirectoryBundle\Entity\Division $synonyms)
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
     * Set original
     *
     * @param \Oleg\UserdirectoryBundle\Entity\Division $original
     * @return Division
     */
    public function setOriginal(\Oleg\UserdirectoryBundle\Entity\Division $original = null)
    {
        $this->original = $original;
    
        return $this;
    }

    /**
     * Get original
     *
     * @return \Oleg\UserdirectoryBundle\Entity\Division
     */
    public function getOriginal()
    {
        return $this->original;
    }


    /**
     * Add user
     *
     * @param \Oleg\UserdirectoryBundle\Entity\User $user
     * @return
     */
    public function addUser(\Oleg\UserdirectoryBundle\Entity\User $user)
    {
        if( !$this->users->contains($user) ) {
            $this->users->add($user);
        }
    }

    /**
     * Remove user
     *
     * @param \Oleg\UserdirectoryBundle\Entity\User $user
     */
    public function removeUser(\Oleg\UserdirectoryBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $division
     */
    public function setDivision($division)
    {
        $this->division = $division;
    }

    /**
     * @return mixed
     */
    public function getDivision()
    {
        return $this->division;
    }



}