<?php

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_transfusionHemolysisCheckResultsList")
 */
class TransfusionHemolysisCheckResultsList extends ListAbstract
{

    /**
     * @ORM\OneToMany(targetEntity="TransfusionHemolysisCheckResultsList", mappedBy="original")
     **/
    protected $synonyms;

    /**
     * @ORM\ManyToOne(targetEntity="TransfusionHemolysisCheckResultsList", inversedBy="synonyms")
     * @ORM\JoinColumn(name="original_id", referencedColumnName="id")
     **/
    protected $original;


}