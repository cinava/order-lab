<?php

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="blockBlockname")
 */
class BlockBlockname extends BlockArrayFieldAbstract
{

    /**
     * @ORM\ManyToOne(targetEntity="Block", inversedBy="blockname", cascade={"persist"})
     * @ORM\JoinColumn(name="block_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $block;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $field;


}