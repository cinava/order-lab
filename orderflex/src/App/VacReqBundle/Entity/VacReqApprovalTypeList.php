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


//"Time Away Approval Group Type" with 2 values: Faculty, Fellows

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="vacreq_approvaltypelist")
 */
class VacReqApprovalTypeList extends ListAbstract {

    /**
     * @ORM\OneToMany(targetEntity="VacReqApprovalTypeList", mappedBy="original", cascade={"persist"})
     **/
    protected $synonyms;

    /**
     * @ORM\ManyToOne(targetEntity="VacReqApprovalTypeList", inversedBy="synonyms", cascade={"persist"})
     * @ORM\JoinColumn(name="original_id", referencedColumnName="id", nullable=true)
     **/
    protected $original;

    //Add a reference to the “Time Away Approval Group Type” for each approver group vacation site
    // and display this value in a select2 drop down menu under the approval group.
    //associated with vacation group (insitution): one VacReqApprovalTypeList can have many Institution
    //Institution n-----1 VacReqApprovalTypeList
    //when add/edit group, choose institution and select VacReqApprovalTypeList which will link to this institution

    //1) Institution has ManyToOne to VacReqApprovalTypeList: Institution->getVacReqApprovalTypeList
    // => vac days accrued per month, max vac days, allow carry over
    //Easy, but in this case Institution is UserDirectoryBundle will have a reference to VacReqBundle

    //2) VacReqApprovalTypeList has OneToMany or ManyToMany (unique) to Institution: institution => getApprovalType(institution)
//    /**
//     * @ORM\ManyToOne(targetEntity="App\UserdirectoryBundle\Entity\Institution")
//     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
//     */
//    private $institution;

    /**
     * @ORM\ManyToMany(targetEntity="App\UserdirectoryBundle\Entity\Institution", cascade={"persist"})
     * @ORM\JoinTable(name="vacreq_approvaltypes_institutions",
     *      joinColumns={@ORM\JoinColumn(name="approvaltype_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="institution_id", referencedColumnName="id")}
     *      )
     **/
    private $institutions;




    public function __construct() {
        $this->institutions = new ArrayCollection();
    }


    public function getInstitutions()
    {
        return $this->institutions;
    }
    public function addInstitution($item)
    {
        if( $item && !$this->institutions->contains($item) ) {
            $this->institutions->add($item);
        }
    }
    public function removeInstitution($item)
    {
        $this->institutions->removeElement($item);
    }

}