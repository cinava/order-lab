<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 4/22/14
 * Time: 9:30 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

//* @ORM\Table(name="scan_research",
// *  indexes={
//    *      @ORM\Index( name="projectTitleStr_idx", columns={"projectTitleStr"} ),
// *      @ORM\Index( name="setTitleStr_idx", columns={"setTitleStr"} )
// *  }

/**
 * @ORM\Entity(repositoryClass="Oleg\OrderformBundle\Repository\ResearchRepository")
 * @ORM\Table(name="scan_research")
 */
class Research
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Message", mappedBy="research")
     */
    protected $message;

    /**
     * @ORM\OneToMany(targetEntity="Slide", mappedBy="research")
     */
    protected $slides;

    //principal as entered by a user. Use a wrapper because research can have multiple PIs
    /**
     * Keep info as principal name as entered by a user and id to a principal
     * @ORM\OneToMany(targetEntity="PrincipalWrapper", mappedBy="research", cascade={"persist"})
     * @ORM\JoinColumn(name="principal_id", referencedColumnName="id", nullable=true)
     */
    protected $principalWrappers;

    /**
     * primarySet - name of the primary PI. Indicates if the primaryPrincipal was set by this order
     * @ORM\Column(type="string", nullable=true)
     */
    protected $primarySet;


    //project title as entered by a user
//    /**
//     * @ORM\Column(type="string", nullable=true)
//     */
//    protected $projectTitleStr;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="ProjectTitleList", cascade={"persist"})
//     * @ORM\JoinColumn(name="projectTitle_id", referencedColumnName="id", nullable=true)
//     */
//    protected $projectTitle;
//
//    //principal as entered by a user
//    /**
//     * @ORM\Column(type="string", nullable=true)
//     */
//    protected $setTitleStr;

    /**
     * @ORM\ManyToOne(targetEntity="ProjectTitleTree",cascade={"persist"})
     */
    protected $projectTitle;


    public function __construct() {
        $this->principalWrappers = new ArrayCollection();
        $this->slides = new ArrayCollection();
    }

//    public function __clone() {
//        if ($this->id) {
//            $this->setId(null);
//        }
//    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

//    /**
//     * @param mixed $slide
//     */
//    public function setSlide($slide)
//    {
//        $this->slide = $slide;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getSlide()
//    {
//        return $this->slide;
//    }
    /**
     * @param \Oleg\OrderformBundle\Entity\Slide $slide
     * @return Block
     */
    public function addSlide(\Oleg\OrderformBundle\Entity\Slide $slide)
    {
        if( !$this->slides->contains($slide) ) {
            $slide->setResearch($this);
            $this->slides->add($slide);
        }

        return $this;
    }

    /**
     * Remove slide
     *
     * @param \Oleg\OrderformBundle\Entity\Slide $slide
     */
    public function removeSlide(\Oleg\OrderformBundle\Entity\Slide $slide)
    {
        $this->slides->removeElement($slide);
    }

    /**
     * Get slide
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSlides()
    {
        return $this->slides;
    }


    /**
     * @param mixed $projectTitle
     */
    public function setProjectTitle($projectTitle)
    {
        $this->projectTitle = $projectTitle;
    }

    /**
     * @return mixed
     */
    public function getProjectTitle()
    {
        return $this->projectTitle;
    }

    /**
     * @return mixed
     */
    public function getPrincipalWrappers()
    {
        //entity is PrincipalWrapper class => order will show the same order as entered by a user
        return $this->principalWrappers;

        //entity is PIList class => we can shows Primary PI as the first principal
//        if( $this->getProjectTitle() ) {
//            return $this->getProjectTitle()->getPrincipals(); //to keep order according to Primary PI
//        } else {
//            return $this->principalWrappers;
//        }

    }

    /**
     * Add principalWrappers
     *
     * @param $principal
     * @return Research
     */
    public function addPrincipalWrapper($principal)
    {
        $principalWrapper = new principalWrapper();
        $principalWrapper->setPrincipalStr( $principal->getName() );
        $principalWrapper->setPrincipal( $principal );

        if( !$this->principalWrappers->contains($principalWrapper) ) {
            $this->principalWrappers->add($principalWrapper);
            $principalWrapper->setResearch($this);
        }

        return $this;
    }

    /**
     * Remove principalWrappers
     *
     * @param PrincipalWrappers $principalWrappers
     */
    public function removePrincipalWrapper($principalWrapper)
    {
        $this->principalWrappers->removeElement($principalWrapper);
    }

//    /**
//     * @param mixed $projectTitleStr
//     */
//    public function setProjectTitleStr($projectTitleStr)
//    {
//        $this->projectTitleStr = $projectTitleStr;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getProjectTitleStr()
//    {
//        return $this->projectTitleStr."";
//    }
//
//    /**
//     * @param mixed $setTitleStr
//     */
//    public function setSetTitleStr($setTitleStr)
//    {
//        $this->setTitleStr = $setTitleStr;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getSetTitleStr()
//    {
//        return $this->setTitleStr."";
//    }

    /**
     * @param mixed $primarySet
     */
    public function setPrimarySet($primarySet)
    {
        $this->primarySet = $primarySet;
    }

    /**
     * @return mixed
     */
    public function getPrimarySet()
    {
        return $this->primarySet;
    }



    public function isEmpty()
    {
        //if( $this->getProjectTitleStr() == '' ) {
        if( $this->getProjectTitle()."" == "" ) {
            return true;
        } else {
            return false;
        }
    }

    public function __toString(){
        //return "Research: id=".$this->id.", project=".$this->projectTitle.", project type=".$this->getProjectTitle()->getType().", principal=".$this->principal.", countSetTitles=".count($this->projectTitle->getSetTitles())."<br>";
        return "Research: id=".$this->id.", project=".$this->projectTitle."<br>";
    }

}