<?php

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="educational")
 */
class Educational
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $course;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lesson;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="director_id", referencedColumnName="id")
     */
    protected $director;

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
     * Set course
     *
     * @param string $course
     * @return Educational
     */
    public function setCourse($course)
    {
        $this->course = $course;
    
        return $this;
    }

    /**
     * Get course
     *
     * @return string 
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set lesson
     *
     * @param string $lesson
     * @return Educational
     */
    public function setLesson($lesson)
    {
        $this->lesson = $lesson;
    
        return $this;
    }

    /**
     * Get lesson
     *
     * @return string 
     */
    public function getLesson()
    {
        return $this->lesson;
    }

    /**
     * @param mixed $director
     */
    public function setDirector($director)
    {
        $this->director = $director;
    }

    /**
     * @return mixed
     */
    public function getDirector()
    {
        return $this->director;
    }


    public function __toString(){

        return "Principal: id=".$this->id.", course=".$this->course.", lesson".$this->lesson."<br>";
    }

}