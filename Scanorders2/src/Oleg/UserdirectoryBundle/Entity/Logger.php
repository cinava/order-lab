<?php

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="logger")
 */
class Logger
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="siteName", type="string")
     */
    private $siteName;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $creationdate;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    private $roles = array();

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $useragent;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $width;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $height;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $event;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $serverresponse;


//    /////////// event type "edit" /////////////////
//
//    //Which user's information was edited
//    /**
//     * @ORM\ManyToOne(targetEntity="User")
//     * @ORM\JoinColumn(name="subjectuser_id", referencedColumnName="id", nullable=true)
//     */
//    private $subjectuser;
//
//    //Which field value(s) changed
//    /**
//     * @ORM\Column(type="string", nullable=true)
//     */
//    private $editfieldname;
//
//    //What was the old value
//    /**
//     * @ORM\Column(type="string", nullable=true)
//     */
//    private $editfieldoldvalue;
//
//    //What was the new value
//    /**
//     * @ORM\Column(type="string", nullable=true)
//     */
//    private $editfieldnewvalue;
//
//    /////////// EOF event type "edit" /////////////////


    public function __construct($siteName) {
        $this->siteName = $siteName;
    }


    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $siteName
     */
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;
    }

    /**
     * @return mixed
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreationdate()
    {
        $this->creationdate = new \DateTime();
    }

    public function getCreationdate()
    {
        return $this->creationdate;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        if( $roles ) {
            foreach( $roles as $role ) {
                $this->addRole($role."");
            }
        }

    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function addRole($role) {
        $this->roles[] = $role;
    }

    /**
     * @param mixed $useragent
     */
    public function setUseragent($useragent)
    {
        $this->useragent = $useragent;
    }

    /**
     * @return mixed
     */
    public function getUseragent()
    {
        return $this->useragent;
    }


    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param mixed $serverresponse
     */
    public function setServerresponse($serverresponse)
    {
        $this->serverresponse = $serverresponse;
    }

    /**
     * @return mixed
     */
    public function getServerresponse()
    {
        return $this->serverresponse;
    }


    public function addEvent( $newEvent ) {

        $event = $this->getEvent();

        $event = $event . $newEvent;

        $this->setEvent( $event );
    }


//    //Edit events
//
//    /**
//     * @param mixed $editfieldname
//     */
//    public function setEditfieldname($editfieldname)
//    {
//        $this->editfieldname = $editfieldname;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getEditfieldname()
//    {
//        return $this->editfieldname;
//    }
//
//    /**
//     * @param mixed $editfieldnewvalue
//     */
//    public function setEditfieldnewvalue($editfieldnewvalue)
//    {
//        $this->editfieldnewvalue = $editfieldnewvalue;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getEditfieldnewvalue()
//    {
//        return $this->editfieldnewvalue;
//    }
//
//    /**
//     * @param mixed $editfieldoldvalue
//     */
//    public function setEditfieldoldvalue($editfieldoldvalue)
//    {
//        $this->editfieldoldvalue = $editfieldoldvalue;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getEditfieldoldvalue()
//    {
//        return $this->editfieldoldvalue;
//    }
//
//    /**
//     * @param mixed $subjectuser
//     */
//    public function setSubjectuser($subjectuser)
//    {
//        $this->subjectuser = $subjectuser;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getSubjectuser()
//    {
//        return $this->subjectuser;
//    }




}