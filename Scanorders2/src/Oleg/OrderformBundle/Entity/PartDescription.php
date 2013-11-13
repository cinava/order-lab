<?php

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

//use Oleg\OrderformBundle\Entity\PartArrayFieldAbstract;

/**
 * @ORM\Entity
 * @ORM\Table(name="partDescription")
 */
class PartDescription extends PartArrayFieldAbstract
{

    /**
     * @ORM\ManyToOne(targetEntity="Part", inversedBy="description", cascade={"persist"})
     * @ORM\JoinColumn(name="part_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $part;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $field;

}