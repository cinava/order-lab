<?php

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Oleg\UserdirectoryBundle\Entity\ListAbstract;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_collaboration")
 */
class Collaboration extends ListAbstract
{
    /**
     * @ORM\OneToMany(targetEntity="Collaboration", mappedBy="original")
     **/
    protected $synonyms;

    /**
     * @ORM\ManyToOne(targetEntity="Collaboration", inversedBy="synonyms")
     * @ORM\JoinColumn(name="original_id", referencedColumnName="id")
     **/
    protected $original;

    /**
     * Collaboration Institutions
     *
     * @ORM\ManyToMany(targetEntity="Institution", inversedBy="collaborations")
     * @ORM\JoinTable(name="user_collaboration_institution",
     *      joinColumns={@ORM\JoinColumn(name="collaboration_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="institution_id", referencedColumnName="id")}
     *      )
     */
    private $institutions;


    /**
     * @ORM\ManyToOne(targetEntity="CollaborationTypeList")
     * @ORM\JoinColumn(name="collaborationType_id", referencedColumnName="id", nullable=true)
     */
    private $collaborationType;


//    /**
//     * Used only for Collaboration nodes under the Institutional tree.
//     *
//     * @ORM\ManyToOne(targetEntity="Institution", inversedBy="collaborations")
//     */
//    protected $institution;


    public function __construct() {
        $this->synonyms = new ArrayCollection();
        $this->institutions = new ArrayCollection();
    }


    public function addInstitution(\Oleg\UserdirectoryBundle\Entity\Institution $institution)
    {
        if( !$this->institutions->contains($institution) ) {
            $this->institutions->add($institution);
            //$institution->addCollaboration($this);
        }
    }

    public function removeInstitution(\Oleg\UserdirectoryBundle\Entity\Institution $institution)
    {
        $this->institutions->removeElement($institution);
    }

    public function getInstitutions()
    {
        return $this->institutions;
    }

    /**
     * @param mixed $collaborationType
     */
    public function setCollaborationType($collaborationType)
    {
        $this->collaborationType = $collaborationType;
    }

    /**
     * @return mixed
     */
    public function getCollaborationType()
    {
        return $this->collaborationType;
    }

//    /**
//     * @param mixed $institution
//     */
//    public function setInstitution($institution)
//    {
//        $this->institution = $institution;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getInstitution()
//    {
//        return $this->institution;
//    }




}