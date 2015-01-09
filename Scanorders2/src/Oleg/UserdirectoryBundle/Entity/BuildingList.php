<?php

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="user_buildingList")
 */
class BuildingList extends ListAbstract
{

    /**
     * @ORM\OneToMany(targetEntity="BuildingList", mappedBy="original", cascade={"persist"})
     **/
    protected $synonyms;

    /**
     * @ORM\ManyToOne(targetEntity="BuildingList", inversedBy="synonyms", cascade={"persist"})
     * @ORM\JoinColumn(name="original_id", referencedColumnName="id", nullable=true)
     **/
    protected $original;


    /**
     * @ORM\OneToOne(targetEntity="GeoLocation", cascade={"persist"})
     **/
    private $geoLocation;

    /**
     * @ORM\ManyToMany(targetEntity="Institution", inversedBy="buildings")
     * @ORM\JoinTable(name="user_buildings_institutions")
     **/
    private $institutions;

    /**
     * This is inverse side, because we link the building to the location (location is responsible for adding building) => mappedBy
     * @ORM\OneToMany(targetEntity="Location", mappedBy="building", cascade={"persist"})
     **/
    private $locations;

    /**
     * @ORM\ManyToMany(targetEntity="SuiteList", inversedBy="buildings")
     * @ORM\JoinTable(name="user_buildings_suites")
     **/
    private $suites;

    /**
     * @ORM\ManyToMany(targetEntity="RoomList", inversedBy="buildings")
     * @ORM\JoinTable(name="user_buildings_rooms")
     **/
    private $rooms;


    public function __construct($creator=null) {
        $this->synonyms = new ArrayCollection();

        $this->institutions = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->suites = new ArrayCollection();
        $this->rooms = new ArrayCollection();

        //set mandatory list attributes
        $this->setName("");
        $this->setType('user-added');
        $this->setCreatedate(new \DateTime());
        $this->setOrderinlist(-1);

        if( $creator ) {
            $this->setCreator($creator);
        }
    }


    public function addLocation($location)
    {
        if( !$this->locations->contains($location) ) {
            $this->locations->add($location);
            $location->setBuilding($this);
        }

        return $this;
    }
    public function removeLocation($location)
    {
        $this->locations->removeElement($location);
    }
    public function getLocations()
    {
        return $this->locations;
    }


    public function addSuite($suite)
    {
        if( $suite ) {
            if( !$this->suites->contains($suite) ) {
                $this->suites->add($suite);
                $suite->addBuilding($this);
            }
        }

        return $this;
    }
    public function removeSuite($suite)
    {
        $this->suites->removeElement($suite);
        $suite->removeBuilding($this);
    }
    public function getSuites()
    {
        return $this->suites;
    }


    public function addRoom($room)
    {
        if( $room ) {
            if( !$this->rooms->contains($room) ) {
                $this->rooms->add($room);
                $room->addBuilding($this);
            }
        }

        return $this;
    }
    public function removeRoom($room)
    {
        $this->rooms->removeElement($room);
        $room->removeBuilding($this);
    }
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return List
     */
    public function setName($name)
    {
        if( $name == null ) {
            $name = "";
        }

        $this->name = $name;

        return $this;
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


    public function getInstitutions()
    {
        return $this->institutions;
    }
    public function addInstitution($institution)
    {
        if( $institution ) {
            if( !$this->institutions->contains($institution) ) {
                $this->institutions->add($institution);
                $institution->addBuilding($this);
            }
        }

        return $this;
    }
    public function removeInstitution($institution)
    {
        $this->institutions->removeElement($institution);
        $institution->removeBuilding($this);
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


    public function getShortName() {
        $name = "";
        if( $this->getGeoLocation() != "" ) {
            $name = $this->getGeoLocation()."";
        }
        return $name;
    }

    //WCMC - Weill Cornell Medical College / 1300 York Ave / Abbreviation = C
    public function __toString() {

        $instName = "";
//        if( $this->getInstitution() ) {
//            if( $this->getInstitution()->getAbbreviation() ) {
//                $instName = $this->getInstitution()->getAbbreviation()."";
//            } else {
//                $instName = $this->getInstitution()->getName()."";
//            }
//        }
        $instNameArr = array();
        foreach( $this->getInstitutions() as $inst ) {
            if( $inst->getAbbreviation() ) {
                $thisInstName = $inst->getAbbreviation()."";
            } else {
                $thisInstName = $inst->getName()."";
            }
            $instNameArr[] = $thisInstName;
        }
        $instName = join(",",$instNameArr);

        $geoName = "";
        if( $this->getGeoLocation() != "" ) {
            $geoName = $this->getGeoLocation()."";
        }

        $name = "";
        if( $instName != "" ) {
            $name = $instName . " - ";
        }

        if( $this->getName() != "" ) {
            $name = $name . $this->getName() . " ";
        }

        if( $this->getAbbreviation() && $this->getAbbreviation() != "" ) {
            $name = $name . "(" . $this->getAbbreviation() . ")";
        }

        if( $geoName != "" ) {
            if( $name != "" && $this->getName() != "" ) {
                $name = $name . " / ";
            }
            $name = $name . $geoName;
        }

        return $name;
    }


}