<?php

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Oleg\OrderformBundle\Repository\SlideRepository")
 * @ORM\Table(name="slide")
 */
class Slide extends OrderAbstract
{

    //*******************************// 
    // first step fields 
    //*******************************//

    /**
     * @ORM\ManyToOne(targetEntity="Block", inversedBy="slide")
     * @ORM\JoinColumn(name="block_id", referencedColumnName="id")
     */
    protected $block;

    /**
     * @ORM\ManyToOne(targetEntity="Part", inversedBy="slide")
     * @ORM\JoinColumn(name="part_id", referencedColumnName="id")
     */
    protected $part;
    
    //*********************************************// 
    // second part of the form (optional) 
    //*********************************************//                
    
    /**
     * @ORM\Column(type="text", nullable=true, length=10000)
     */
    protected $microscopicdescr;

    /**
     * @param \Doctrine\Common\Collections\Collection $property
     * @ORM\OneToMany(targetEntity="SpecialStains", mappedBy="slide", cascade={"persist"})
     */
    protected $specialStains;

    /**
     * @param \Doctrine\Common\Collections\Collection $property
     * @ORM\OneToMany(targetEntity="RelevantScans", mappedBy="slide", cascade={"persist"})
     */
    protected $relevantScans;
    
    /**
     * @ORM\Column(type="string", nullable=true, length=200)
     */
    protected $barcode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="SlideType", cascade={"persist"})
     * @ORM\JoinColumn(name="slidetype_id", referencedColumnName="id", nullable=true)
     */
    protected $slidetype;

    /**
     * @ORM\OneToMany(targetEntity="Scan", mappedBy="slide", cascade={"persist"})
     */
    protected $scan;

    /**
     * @ORM\OneToMany(targetEntity="Stain", mappedBy="slide", cascade={"persist"})
     */
    protected $stain;
    
    /**
     * @ORM\ManyToMany(targetEntity="OrderInfo", mappedBy="slide")
     **/
    protected $orderinfo;

    /**
     * @ORM\OneToOne(
     *      targetEntity="Educational",
     *      inversedBy="slide",
     *      cascade={"persist"}
     * )
     * @ORM\JoinColumn(
     *      name="educational_id",
     *      referencedColumnName="id"
     * )
     */
    private $educational;

//    /**
//     * @ORM\OneToOne(
//     *      targetEntity="Research",
//     *      inversedBy="slide",
//     *      cascade={"persist"}
//     * )
//     * @ORM\JoinColumn(
//     *      name="research_id",
//     *      referencedColumnName="id"
//     * )
//     */
//    private $research;
    /**
     * @ORM\ManyToOne(targetEntity="Research", inversedBy="slides", cascade={"persist"})
     * @ORM\JoinColumn(name="research_id", referencedColumnName="id")
     */
    private $research;
    
    public function __construct( $withfields=false, $status='valid', $provider=null, $source=null )
    {
        parent::__construct($status,$provider);
        $this->scan = new ArrayCollection();
        $this->stain = new ArrayCollection();
        $this->specialStains = new ArrayCollection();
        $this->relevantScans = new ArrayCollection();

        if( $withfields ) {
            $this->addRelevantScan( new RelevantScans($status,$provider,$source) );
            $this->addSpecialStain( new SpecialStains($status,$provider,$source) );
            $this->addScan( new Scan($status,$provider,$source) );
            $this->addStain( new Stain($status,$provider,$source) );
        }
    }

    public function makeDependClone() {
        $this->scan = $this->cloneDepend($this->scan,$this);
        $this->stain = $this->cloneDepend($this->stain,$this);
        $this->specialStains = $this->cloneDepend($this->specialStains,$this);
        $this->relevantScans = $this->cloneDepend($this->relevantScans,$this);

//        foreach( $this->scan as $depend ) {
//            echo "after depend id=".$depend->getId()."<br>";
//        }

    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getMicroscopicdescr() {
        return $this->microscopicdescr;
    }

    public function setMicroscopicdescr($microscopicdescr) {
        $this->microscopicdescr = $microscopicdescr;
    }

    public function getRelevantscan() {
        return $this->relevantScans;
    }

    public function setRelevantscan($relevantscan) {
        $this->relevantScans = $relevantscan;
    }

    /**
     * Set block
     * @param \Oleg\OrderformBundle\Entity\Block $block
     * @return Slide
     */
    public function setBlock(\Oleg\OrderformBundle\Entity\Block $block = null)
    {
        $this->block = $block;
    
        return $this;
    }

    /**
     * Get block
     */
    public function getBlock()
    {
        return $this->block;
    }

    public function setPart(\Oleg\OrderformBundle\Entity\Part $part = null)
    {
        $this->part = $part;

        return $this;
    }
    public function getPart()
    {
        return $this->part;
    }

    /**
     * Set barcode
     *
     * @param string $barcode
     * @return Slide
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    
        return $this;
    }

    /**
     * Get barcode
     *
     * @return string 
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * Add scan
     *
     * @param \Oleg\OrderformBundle\Entity\Scan $scan
     * @return Slide
     */
    public function addScan(\Oleg\OrderformBundle\Entity\Scan $scan)
    {
        if( !$this->scan->contains($scan) ) {
            $scan->setSlide($this);
            $this->scan->add($scan);
        }
    
        return $this;
    }

    /**
     * Remove scan
     *
     * @param \Oleg\OrderformBundle\Entity\Scan $scan
     */
    public function removeScan(\Oleg\OrderformBundle\Entity\Scan $scan)
    {
        $this->scan->removeElement($scan);
    }

    /**
     * Get scan
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getScan()
    {
        return $this->scan;
    }

    /**
     * Add stain
     *
     * @param \Oleg\OrderformBundle\Entity\Stain $stain
     * @return Slide
     */
    public function addStain(\Oleg\OrderformBundle\Entity\Stain $stain)
    {
        if( !$this->stain->contains($stain) ) {
            $stain->setSlide($this);
            $this->stain->add($stain);
        }
    
        return $this;
    }

    /**
     * Remove stain
     *
     * @param \Oleg\OrderformBundle\Entity\Stain $stain
     */
    public function removeStain(\Oleg\OrderformBundle\Entity\Stain $stain)
    {
        $this->stain->removeElement($stain);
    }

    /**
     * Get stain
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStain()
    {
        return $this->stain;
    }
    
    
    public function __toString() {
        $stain = "";
        $mag = "";
        if( count($this->getStain()) > 0 && count($this->getScan())>0 ) {
            $mag = $this->getScan()->first()->getField();
            $stain = $this->getStain()->first()->getField();
        }

        return "Slide: id=".$this->getId().", title=".$this->getTitle().", slidetype=".$this->getSlidetype().
                ", provider=".$this->getProvider().
                ", parentId=".$this->getParent()->getId().
                ", orderinfo count=".count($this->getOrderinfo()).", first orderinfo:=".$this->getOrderinfo()->first().
                ", scan count=".count($this->getScan()).", firstscanid=".$this->getScan()->first()->getId().
                ", stain count=".count($this->getStain()).", firststainid=".$this->getStain()->first()->getId().
                //", specialStains count=".count($this->getSpecialStains()).", firstspecialStainsId=".$this->getSpecialStains()->first()->getId().
                ", stain=".$stain.", mag=".$mag.
                ", relScansCount=".count($this->getRelevantScans()).":".$this->getRelevantScans()->first()."<br>";
    }


    public function addSpecialStain( $specialStains )
    {
        if( $specialStains != null ) {
            if( !$this->specialStains->contains($specialStains) ) {
                $this->specialStains->add($specialStains);
                $specialStains->setSlide($this);
                $specialStains->setProvider($this->getProvider());
            }
        }
        return $this;
    }

    public function removeSpecialStain(\Oleg\OrderformBundle\Entity\SpecialStains $specialStains)
    {
        $this->specialStains->removeElement($specialStains);
    }

    public function getSpecialStains()
    {
        return $this->specialStains;
    }
    

    /**
     * Add relevantScans
     *
     * @param \Oleg\OrderformBundle\Entity\RelevantScans $relevantScans
     * @return Slide
     */
    public function addRelevantScan( $relevantScans )
    {

        if( $relevantScans == null ) {
            $relevantScans = new RelevantScans();
        }

        if( !$this->relevantScans->contains($relevantScans) ) {
            $this->relevantScans->add($relevantScans);
            $relevantScans->setSlide($this);
            $relevantScans->setProvider($this->getProvider());
        }

        return $this;
    }

    /**
     * Remove relevantScans
     *
     * @param \Oleg\OrderformBundle\Entity\RelevantScans $relevantScans
     */
    public function removeRelevantScan(\Oleg\OrderformBundle\Entity\RelevantScans $relevantScans)
    {
        $this->relevantScans->removeElement($relevantScans);
    }

    /**
     * Get relevantScans
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRelevantScans()
    {
        return $this->relevantScans;
    }

    /**
     * @param mixed $slidetype
     */
    public function setSlidetype($slidetype)
    {
        $this->slidetype = $slidetype;
    }

    /**
     * @return mixed
     */
    public function getSlidetype()
    {
        return $this->slidetype;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $educational
     */
    public function setEducational($educational)
    {
        $this->educational = $educational;
    }

    /**
     * @return mixed
     */
    public function getEducational()
    {
        return $this->educational;
    }

    /**
     * @param mixed $research
     */
    public function setResearch($research)
    {
        $this->research = $research;
    }

    /**
     * @return mixed
     */
    public function getResearch()
    {
        return $this->research;
    }


    public function getChildren() {
        return null;    //new ArrayCollection();
    }

    public function obtainKeyField() {
        return null;
    }
    
    //parent, children, key field methods
    public function setParent($parent) {
        $parentClass = new \ReflectionClass($parent);
        $parentClassName = $parentClass->getShortName();
        if( $parentClassName == "Block" ) {
            //echo "add  Block <br>";
            $this->setBlock($parent);
        } else
        if( $parentClassName == "Part") {
            //echo "add  Slide <br>";
            $this->setPart($parent);
        } else {
            throw new \Exception('Parent can not be set of the class ' . $parentClassName );
        }
        return $this;
    }

    public function getParent() {
        if( $this->getBlock() ) {
            return $this->getBlock();
        } else if( $this->getPart() ) {
            return $this->getPart();
        } else {
            throw new \Exception( 'Slide does not have parent; slide:'.$this );
        }

    }


}