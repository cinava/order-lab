<?php

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_location")
 */
class Location extends BaseUserAttributes
{

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $pager;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mobile;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $fax;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $room;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $street1;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $street2;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $zip;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $buildingName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $buildingAbbr;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $floor;

    /**
     * Associated NYPH Code
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $associatedCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $associatedClia;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $associatedCliaExpDate;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $associatedPfi;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="assistant", referencedColumnName="id")
     */
    private $assistant;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="locations")
     * @ORM\JoinColumn(name="fosuser", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", options={"default" = 1}, nullable=true)
     */
    private $removable;


    public function __construct($author=null) {
        $this->setRemovable(true);
        parent::__construct($author);
    }

    /**
     * @param mixed $assistant
     */
    public function setAssistant($assistant)
    {
        $this->assistant = $assistant;
    }

    /**
     * @return mixed
     */
    public function getAssistant()
    {
        return $this->assistant;
    }

    /**
     * @param mixed $associatedCode
     */
    public function setAssociatedCode($associatedCode)
    {
        $this->associatedCode = $associatedCode;
    }

    /**
     * @return mixed
     */
    public function getAssociatedCode()
    {
        return $this->associatedCode;
    }

    /**
     * @param mixed $associatedClia
     */
    public function setAssociatedClia($associatedClia)
    {
        $this->associatedClia = $associatedClia;
    }

    /**
     * @return mixed
     */
    public function getAssociatedClia()
    {
        return $this->associatedClia;
    }

    /**
     * @param mixed $associatedCliaExpDate
     */
    public function setAssociatedCliaExpDate($associatedCliaExpDate)
    {
        $this->associatedCliaExpDate = $associatedCliaExpDate;
    }

    /**
     * @return mixed
     */
    public function getAssociatedCliaExpDate()
    {
        return $this->associatedCliaExpDate;
    }

    /**
     * @param mixed $associatedPfi
     */
    public function setAssociatedPfi($associatedPfi)
    {
        $this->associatedPfi = $associatedPfi;
    }

    /**
     * @return mixed
     */
    public function getAssociatedPfi()
    {
        return $this->associatedPfi;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }



    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * @return mixed
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param mixed $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $pager
     */
    public function setPager($pager)
    {
        $this->pager = $pager;
    }

    /**
     * @return mixed
     */
    public function getPager()
    {
        return $this->pager;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return mixed
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $street1
     */
    public function setStreet1($street1)
    {
        $this->street1 = $street1;
    }

    /**
     * @return mixed
     */
    public function getStreet1()
    {
        return $this->street1;
    }

    /**
     * @param mixed $street2
     */
    public function setStreet2($street2)
    {
        $this->street2 = $street2;
    }

    /**
     * @return mixed
     */
    public function getStreet2()
    {
        return $this->street2;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param mixed $buildingAbbr
     */
    public function setBuildingAbbr($buildingAbbr)
    {
        $this->buildingAbbr = $buildingAbbr;
    }

    /**
     * @return mixed
     */
    public function getBuildingAbbr()
    {
        return $this->buildingAbbr;
    }

    /**
     * @param mixed $buildingName
     */
    public function setBuildingName($buildingName)
    {
        $this->buildingName = $buildingName;
    }

    /**
     * @return mixed
     */
    public function getBuildingName()
    {
        return $this->buildingName;
    }

    /**
     * @param mixed $floor
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;
    }

    /**
     * @return mixed
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $removable
     */
    public function setRemovable($removable)
    {
        $this->removable = $removable;
    }

    /**
     * @return mixed
     */
    public function getRemovable()
    {
        return $this->removable;
    }


    public function __toString() {
        return "Location";
    }


}