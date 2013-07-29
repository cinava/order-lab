<?php

namespace Oleg\OrderformBundle\Entity;

//use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Oleg\OrderformBundle\Repository\StainRepository")
 * @ORM\Table(name="stain")
 */
class Stain
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=200)
     * @Assert\NotBlank
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string", nullable=true, length=200)
     */
    protected $stainer;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date;
    
    /**
     * @ORM\OneToOne(targetEntity="Slide", inversedBy="stain", cascade={"persist"})
     * @ORM\JoinColumn(name="slide_id", referencedColumnName="id")   
     */
    //protected $slide;

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
     * Set name
     *
     * @param string $name
     * @return Stain
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set stainer
     *
     * @param string $stainer
     * @return Stain
     */
    public function setStainer($stainer)
    {
        $this->stainer = $stainer;
    
        return $this;
    }

    /**
     * Get stainer
     *
     * @return string 
     */
    public function getStainer()
    {
        return $this->stainer;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Stain
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set slide
     *
     * @param \Oleg\OrderformBundle\Entity\Slide $slide
     * @return Stain
     */
//    public function setSlide(\Oleg\OrderformBundle\Entity\Slide $slide = null)
//    {
//        $this->slide = $slide;
//    
//        return $this;
//    }

    /**
     * Get slide
     *
     * @return \Oleg\OrderformBundle\Entity\Slide 
     */
//    public function getSlide()
//    {
//        return $this->slide;
//    }
//    
//    public function __toString() {
//        return $this->name;
//    }
}