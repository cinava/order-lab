<?php

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Oleg\OrderformBundle\Entity\AccessionArrayFieldAbstract;

/**
 * @ORM\Entity
 * @ORM\Table(name="partDiagnosis")
 */
class PartDiagnosis extends PartArrayFieldAbstract
{

    /**
     * @ORM\ManyToOne(targetEntity="Part", inversedBy="diagnosis", cascade={"persist"})
     * @ORM\JoinColumn(name="part_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $part;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $field;

}