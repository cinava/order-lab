<?php

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class BaseLocation extends ListAbstract
{

    const STATUS_UNVERIFIED = 0;    //unverified (not trusted)
    const STATUS_VERIFIED = 1;      //verified by admin

    /**
     * status: valid, invalid
     * @ORM\Column(type="integer", options={"default" = 0}, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean", options={"default" = 1}, nullable=true)
     */
    private $removable;

    /**
     * @ORM\ManyToOne(targetEntity="LocationTypeList")
     * @ORM\JoinColumn(name="locationType", referencedColumnName="id")
     */
    private $locationType;

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
     * @ORM\OneToOne(targetEntity="GeoLocation", cascade={"persist"})
     **/
    private $geoLocation;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $floor;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $suit;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mailbox;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;


    public function __construct($creator=null) {
        $this->setRemovable(true);
        $this->setStatus(self::STATUS_UNVERIFIED);

        //set mandatory list attributes
        $this->setType('user-added');
        $this->setCreatedate(new \DateTime());
        $this->setOrderinlist(-1);

        if( $creator ) {
            $this->setCreator($creator);
        }
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
     * @param mixed $geoLocation
     */
    public function setGeoLocation($geoLocation)
    {
        $this->geoLocation = $geoLocation;
    }

    /**
     * @return mixed
     */
    public function getGeoLocation()
    {
        return $this->geoLocation;
    }

    /**
     * @param mixed $locationType
     */
    public function setLocationType($locationType)
    {
        $this->locationType = $locationType;
    }

    /**
     * @return mixed
     */
    public function getLocationType()
    {
        return $this->locationType;
    }

    /**
     * @param mixed $mailbox
     */
    public function setMailbox($mailbox)
    {
        $this->mailbox = $mailbox;
    }

    /**
     * @return mixed
     */
    public function getMailbox()
    {
        return $this->mailbox;
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
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $suit
     */
    public function setSuit($suit)
    {
        $this->suit = $suit;
    }

    /**
     * @return mixed
     */
    public function getSuit()
    {
        return $this->suit;
    }

    public function getStatusStr()
    {
        return $this->getStatusStrByStatus($this->getStatus());
    }


    public function getStatusStrByStatus($status)
    {
        $str = $status;

        if( $status == self::STATUS_UNVERIFIED )
            $str = "Pending Administrative Review";

        if( $status == self::STATUS_VERIFIED )
            $str = "Verified by Administration";

        return $str;
    }

    //interface function
    public function getAuthor()
    {
        return $this->getCreator();
    }
    public function setAuthor($author)
    {
        return $this->setCreator($author);
    }
    public function getUpdateAuthor()
    {
        return $this->getUpdatedby();
    }
    public function setUpdateAuthor($author)
    {
        return $this->setUpdatedby($author);
    }


    public function __toString() {

        return $this->getName();
    }



}