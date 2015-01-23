<?php

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="user_fellowshipTitleList")
 */
class FellowshipTitleList extends ListAbstract
{

    /**
     * @ORM\OneToMany(targetEntity="FellowshipTitleList", mappedBy="original", cascade={"persist"})
     **/
    protected $synonyms;

    /**
     * @ORM\ManyToOne(targetEntity="FellowshipTitleList", inversedBy="synonyms", cascade={"persist"})
     * @ORM\JoinColumn(name="original_id", referencedColumnName="id", nullable=true)
     **/
    protected $original;


    public function __toString() {
        if( $this->getAbbreviation() ) {
            return $this->getAbbreviation() . " - " . $this->getName();
        }
        return $this->getName()."";
    }
    


}