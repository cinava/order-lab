<?php
/**
 * Copyright (c) 2017 Cornell University
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace App\VacReqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\UserdirectoryBundle\Entity\ListAbstract;
use Symfony\Component\Validator\Constraints as Assert;


//Actual observed holidays that are taking in vacation days calculations (subset of the VacReqHolidayList list)
/**
 * @ORM\Entity
 * @ORM\Table(name="vacreq_observedholidayList")
 */
class VacReqObservedHolidayList extends ListAbstract {

    /**
     * @ORM\OneToMany(targetEntity="VacReqObservedHolidayList", mappedBy="original", cascade={"persist"})
     **/
    protected $synonyms;

    /**
     * @ORM\ManyToOne(targetEntity="VacReqObservedHolidayList", inversedBy="synonyms", cascade={"persist"})
     * @ORM\JoinColumn(name="original_id", referencedColumnName="id", nullable=true)
     **/
    protected $original;

    /**
     * Holiday Name
     * @ORM\Column(type="string", nullable=true)
     */
    private $holidayName;

    /**
     * Holiday Date
     * @ORM\Column(type="date", nullable=true)
     */
    private $holidayDate;

    //“Country” attribute (set to [US] by default)
    /**
     * @ORM\ManyToOne(targetEntity="App\UserdirectoryBundle\Entity\Countries")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $country;

    //"Observed By" - showing all organizational groups in a Select2 drop down menu
    /**
     * @ORM\ManyToMany(targetEntity="App\UserdirectoryBundle\Entity\Institution", cascade={"persist"})
     * @ORM\JoinTable(name="vacreq_observedholiday_institution",
     *      joinColumns={@ORM\JoinColumn(name="observedholiday_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="institution_id", referencedColumnName="id")}
     *      )
     **/
    private $institutions;



    function __construct($author=null) {
        parent::__construct($author);
        $this->institutions = new ArrayCollection();
    }


    /**
     * @return mixed
     */
    public function getHolidayDate()
    {
        return $this->holidayDate;
    }

    /**
     * @param mixed $holidayDate
     */
    public function setHolidayDate($holidayDate)
    {
        $this->holidayDate = $holidayDate;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getInstitutions()
    {
        return $this->institutions;
    }

    /**
     * @param mixed $institution
     */
    public function addInstitution($institution)
    {
        if( !$this->institutions->contains($institution) ) {
            $this->institutions->add($institution);
        }
    }

    public function removeInstitution($institution)
    {
        $this->institutions->removeElement($institution);
    }

    /**
     * @return mixed
     */
    public function getHolidayName()
    {
        return $this->holidayName;
    }

    /**
     * @param mixed $holidayName
     */
    public function setHolidayName($holidayName)
    {
        $this->holidayName = $holidayName;
    }



}