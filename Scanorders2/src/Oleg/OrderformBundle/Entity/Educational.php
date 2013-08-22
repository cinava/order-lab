<?php

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Oleg\OrderformBundle\Repository\EducationalRepository")
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
     * @ORM\Column(type="string", nullable=true, length=200)
     */
    protected $goal;

    /**
     * @ORM\Column(type="string", nullable=true, length=200)
     */
    protected $course;

    /**
     * @ORM\Column(type="string", nullable=true, length=200)
     */
    protected $lesson;
    




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
     * Set goal
     *
     * @param string $goal
     * @return Educational
     */
    public function setGoal($goal)
    {
        $this->goal = $goal;
    
        return $this;
    }

    /**
     * Get goal
     *
     * @return string 
     */
    public function getGoal()
    {
        return $this->goal;
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
}