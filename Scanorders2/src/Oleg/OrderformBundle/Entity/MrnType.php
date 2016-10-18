<?php

namespace Oleg\OrderformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Oleg\UserdirectoryBundle\Entity\ListAbstract;

/**
 * Note: this file is used in Oleg\UserdirectoryBundle\Entity\Identifier. Do not change!
 *
 * @ORM\Entity
 * @ORM\Table(name="scan_mrntype")
 */
class MrnType extends ListAbstract
{

    /**
     * @ORM\OneToMany(targetEntity="MrnType", mappedBy="original", cascade={"persist"})
     **/
    protected $synonyms;

    /**
     * @ORM\ManyToOne(targetEntity="MrnType", inversedBy="synonyms", cascade={"persist"})
     * @ORM\JoinColumn(name="original_id", referencedColumnName="id", nullable=true)
     **/
    protected $original;

    /**
     * @ORM\OneToMany(targetEntity="PatientMrn", mappedBy="keytype")
     */
    protected $patientmrn;


    public function __construct() {
		$this->synonyms = new ArrayCollection();
        $this->patientmrn = new ArrayCollection();
    }

    public function addPatientmrn(\Oleg\OrderformBundle\Entity\PatientMrn $patientmrn)
    {
        if( !$this->patientmrn->contains($patientmrn) ) {
            $this->patientmrn->add($patientmrn);
        }
        return $this;
    }

    public function removePatientmrn(\Oleg\OrderformBundle\Entity\PatientMrn $patientmrn)
    {
        $this->patientmrn->removeElement($patientmrn);
    }

    public function getPatientmrn()
    {
        return $this->patientmrn;
    }


    public function __toString()
    {
        $name = $this->name."";

        if( $this->abbreviation && $this->abbreviation != "" ) {
            $name = $this->abbreviation."";
        }

        return $name;
    }

}