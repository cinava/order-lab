<?php

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_roles")
 */
class Roles extends ListAbstract {

    /**
     * Alias is a display name for each role, i.e.: ROLE_SCANORDER_ADMIN => Administrator
     * @ORM\Column(type="string", nullable=true)
     */
    protected $alias;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @param mixed $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }





}