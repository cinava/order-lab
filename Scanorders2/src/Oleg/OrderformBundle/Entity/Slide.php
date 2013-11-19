<?php

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Oleg\OrderformBundle\Repository\SlideRepository")
 * @ORM\Table(name="slide")
 */
class Slide
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    
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

//    /**
//     * @ORM\ManyToOne(targetEntity="Part", inversedBy="slide")
//     * @ORM\JoinColumn(name="part_id", referencedColumnName="id")
//     */
//    protected $type;
    
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
    
    public function __construct()
    {
        $this->orderinfo = new ArrayCollection();
        $this->scan = new ArrayCollection();
        $this->stain = new ArrayCollection();
        $this->specialStains = new ArrayCollection();
        $this->relevantScans = new ArrayCollection();
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

//    public function getDiagnosis() {
//        return $this->diagnosis;
//    }
//
//    public function setDiagnosis($diagnosis) {
//        $this->diagnosis = $diagnosis;
//    }

    public function getMicroscopicdescr() {
        return $this->microscopicdescr;
    }

    public function setMicroscopicdescr($microscopicdescr) {
        $this->microscopicdescr = $microscopicdescr;
    }

    public function getRelevantscan() {
        return $this->relevantscan;
    }

    public function setRelevantscan($relevantscan) {
        $this->relevantscan = $relevantscan;
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

    public function setParent($parent = null)
    {
        //$type = $this->getType();
        //$this->$type = $parent;
        $this->block = $parent;

        return $this;
    }
    public function getParent()
    {
        return $this->block;
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
     * Add orderinfo
     *
     * @param \Oleg\OrderformBundle\Entity\OrderInfo $orderinfo
     * @return Slide
     */
    public function addOrderinfo(\Oleg\OrderformBundle\Entity\OrderInfo $orderinfo)
    {
        if( !$this->orderinfo->contains($orderinfo) ) {
            $this->orderinfo->add($orderinfo);
        }  
    }

    /**
     * Remove orderinfo
     *
     * @param \Oleg\OrderformBundle\Entity\OrderInfo $orderinfo
     */
    public function removeOrderinfo(\Oleg\OrderformBundle\Entity\OrderInfo $orderinfo)
    {
        $this->orderinfo->removeElement($orderinfo);
    }

    /**
     * Get orderinfos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrderinfo()
    {
        return $this->orderinfo;
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
            $this->scan[] = $scan;
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
            $this->stain[] = $stain;
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
        return "Slide: id=".$this->getId()."<br>";
    }

    /**
     * Add specialStains
     *
     * @param \Oleg\OrderformBundle\Entity\SpecialStains $specialStains
     * @return Slide
     */
    public function addSpecialStain( $specialStains )
    {
        if( $specialStains != null ) {
            if( !$this->specialStains->contains($specialStains) ) {
                $specialStains->setSlide($this);
                $this->specialStains[] = $specialStains;
            }
        }
    
        return $this;
    }

    /**
     * Remove specialStains
     *
     * @param \Oleg\OrderformBundle\Entity\SpecialStains $specialStains
     */
    public function removeSpecialStain(\Oleg\OrderformBundle\Entity\SpecialStains $specialStains)
    {
        $this->specialStains->removeElement($specialStains);
    }

    /**
     * Get specialStains
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
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
        if( $relevantScans != null ) {
            if( !$this->relevantScans->contains($relevantScans) ) {
                $relevantScans->setSlide($this);
                $this->relevantScans[] = $relevantScans;
            }
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
}