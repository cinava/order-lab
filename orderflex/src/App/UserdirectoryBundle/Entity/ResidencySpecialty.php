<?php
/**
 * Copyright (c) 2017 Cornell University
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace App\UserdirectoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

//TODO: turn it to BaseCompositeNode
#[ORM\Table(name: 'user_residencySpecialty')]
#[ORM\Entity]
class ResidencySpecialty extends ListAbstract
{

    #[ORM\OneToMany(targetEntity: 'ResidencySpecialty', mappedBy: 'original')]
    protected $synonyms;

    #[ORM\ManyToOne(targetEntity: 'ResidencySpecialty', inversedBy: 'synonyms')]
    #[ORM\JoinColumn(name: 'original_id', referencedColumnName: 'id', nullable: true)]
    protected $original;



    //fellowshipSubspecialty - children
    #[ORM\OneToMany(targetEntity: 'FellowshipSubspecialty', mappedBy: 'parent', cascade: ['persist'])]
    private $children;


    #[ORM\Column(type: 'boolean', nullable: true)]
    private $boardCertificateAvailable;


    //Residency application fields
    #[ORM\ManyToOne(targetEntity: 'Institution')]
    #[ORM\JoinColumn(name: 'institution_id', referencedColumnName: 'id', nullable: true)]
    protected $institution;

    #[ORM\JoinTable(name: 'user_residencyspecialty_coordinator')]
    #[ORM\JoinColumn(name: 'residencyspecialty_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'coordinator_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: 'User')]
    private $coordinators;

    #[ORM\JoinTable(name: 'user_residencyspecialty_director')]
    #[ORM\JoinColumn(name: 'residencyspecialty_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'director_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: 'User')]
    private $directors;

    #[ORM\JoinTable(name: 'user_residencyspecialty_interviewer')]
    #[ORM\JoinColumn(name: 'residencyspecialty_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'interviewer_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: 'User')]
    private $interviewers;

    /**
     * Application season start date
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private $seasonYearStart;

    /**
     * Application season end date
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private $seasonYearEnd;



    public function __construct( $author = null ) {
        $this->children = new ArrayCollection();

        $this->coordinators = new ArrayCollection();
        $this->directors = new ArrayCollection();
        $this->interviewers = new ArrayCollection();

        parent::__construct($author);
    }


    public function addChild($child)
    {
        if( $child && !$this->children->contains($child) ) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }
    public function removeChild($child)
    {
        $this->children->removeElement($child);
    }
    public function getChildren()
    {
        return $this->children;
    }

    //mapper functions to deal with tree logic
    public function addFellowshipSubspecialty($child) {
        $this->addChild($child);
    }
    public function removeFellowshipSubspecialty($child) {
        $this->removeChild($child);
    }

    /**
     * @param mixed $boardCertificateAvailable
     */
    public function setBoardCertificateAvailable($boardCertificateAvailable)
    {
        $this->boardCertificateAvailable = $boardCertificateAvailable;
    }

    /**
     * @return mixed
     */
    public function getBoardCertificateAvailable()
    {
        return $this->boardCertificateAvailable;
    }

    /**
     * @return mixed
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * @param mixed $institution
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;
    }

    public function addCoordinator($item)
    {
        if( $item && !$this->coordinators->contains($item) ) {
            $this->coordinators->add($item);
        }
        return $this;
    }
    public function removeCoordinator($item)
    {
        $this->coordinators->removeElement($item);
    }
    public function getCoordinators()
    {
        return $this->coordinators;
    }

    public function addDirector($item)
    {
        if( $item && !$this->directors->contains($item) ) {
            $this->directors->add($item);
        }
        return $this;
    }
    public function removeDirector($item)
    {
        $this->directors->removeElement($item);
    }
    public function getDirectors()
    {
        return $this->directors;
    }

    public function addInterviewer($item)
    {
        if( $item && !$this->interviewers->contains($item) ) {
            $this->interviewers->add($item);
        }
        return $this;
    }
    public function removeInterviewer($item)
    {
        $this->interviewers->removeElement($item);
    }
    public function getInterviewers()
    {
        return $this->interviewers;
    }

    /**
     * @return mixed
     */
    public function getSeasonYearStart()
    {
        return $this->seasonYearStart;
    }

    /**
     * @param mixed $seasonYearStart
     */
    public function setSeasonYearStart($seasonYearStart)
    {
        $this->seasonYearStart = $seasonYearStart;
    }

    /**
     * @return mixed
     */
    public function getSeasonYearEnd()
    {
        return $this->seasonYearEnd;
    }

    /**
     * @param mixed $seasonYearEnd
     */
    public function setSeasonYearEnd($seasonYearEnd)
    {
        $this->seasonYearEnd = $seasonYearEnd;
    }

    
    
    

    //$methodStr: getInterviewers
    public function isUserExistByMethodStr( $user, $methodStr ) {
        foreach( $this->$methodStr() as $thisUser ) {
            if( $thisUser->getId() == $user->getId() ) {
                return true;
            }
        }
        return false;
    }


    public function getTreeName() {
        return $this->getName();
    }

    public function getClassName()
    {
        return "ResidencySpecialty";
    }



}