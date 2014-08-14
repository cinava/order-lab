<?php

namespace Oleg\OrderformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="division")
 */
class Division extends ListAbstract
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
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="divisions")
     * @ORM\JoinColumn(name="department", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $department;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="division")
     **/
    protected $users;




    public function __construct() {
        $this->synonyms = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * Add synonyms
     *
     * @param \Oleg\OrderformBundle\Entity\Division $synonyms
     * @return Division
     */
    public function addSynonym(\Oleg\OrderformBundle\Entity\Division $synonyms)
    {
        $this->synonyms->add($synonyms);
        return $this;
    }

    /**
     * Remove synonyms
     *
     * @param \Oleg\OrderformBundle\Entity\Division $synonyms
     */
    public function removeSynonym(\Oleg\OrderformBundle\Entity\Division $synonyms)
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
     * @param \Oleg\OrderformBundle\Entity\Division $original
     * @return Division
     */
    public function setOriginal(\Oleg\OrderformBundle\Entity\Division $original = null)
    {
        $this->original = $original;
    
        return $this;
    }

    /**
     * Get original
     *
     * @return \Oleg\OrderformBundle\Entity\Division
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * @param mixed $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * @return mixed
     */
    public function getDepartment()
    {
        return $this->department;
    }



    /**
     * Add user
     *
     * @param \Oleg\OrderformBundle\Entity\User $user
     * @return
     */
    public function addUser(\Oleg\OrderformBundle\Entity\User $user)
    {
        if( !$this->users->contains($user) ) {
            $this->users->add($user);
        }
    }

    /**
     * Remove user
     *
     * @param \Oleg\OrderformBundle\Entity\User $user
     */
    public function removeUser(\Oleg\OrderformBundle\Entity\User $user)
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


}