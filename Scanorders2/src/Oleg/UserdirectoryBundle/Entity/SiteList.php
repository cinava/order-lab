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

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_siteList")
 */
class SiteList extends ListAbstract
{

    /**
     * @ORM\OneToMany(targetEntity="SiteList", mappedBy="original")
     **/
    protected $synonyms;

    /**
     * @ORM\ManyToOne(targetEntity="SiteList", inversedBy="synonyms")
     * @ORM\JoinColumn(name="original_id", referencedColumnName="id")
     **/
    protected $original;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $selfSignUp;

    /**
     * @ORM\ManyToMany(targetEntity="Roles", cascade={"persist"})
     * @ORM\JoinTable(name="user_sites_lowestRoles",
     *      joinColumns={@ORM\JoinColumn(name="site_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     **/
    private $lowestRoles;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $accessibility;

    /**
     * Show Link on Homepage
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showLinkHomePage;

    /**
     * Show Link in Navbar
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showLinkNavbar;



    public function __construct( $creator = null ) {
        parent::__construct($creator);

        $this->lowestRoles = new ArrayCollection();

        $this->setShowLinkHomePage(true);
        $this->setShowLinkNavbar(true);
    }
    

    /**
     * @return mixed
     */
    public function getSelfSignUp()
    {
        return $this->selfSignUp;
    }

    /**
     * @param mixed $selfSignUp
     */
    public function setSelfSignUp($selfSignUp)
    {
        $this->selfSignUp = $selfSignUp;
    }

    public function addLowestRole(Roles $role)
    {
        if( !$this->lowestRoles->contains($role) ) {
            $this->lowestRoles->add($role);
        }
    }
    public function removeLowestRole(Roles $role)
    {
        $this->lowestRoles->removeElement($role);
    }
    public function getLowestRoles()
    {
        return $this->lowestRoles;
    }

    /**
     * @return mixed
     */
    public function getAccessibility()
    {
        return $this->accessibility;
    }

    /**
     * @param mixed $accessibility
     */
    public function setAccessibility($accessibility)
    {
        $this->accessibility = $accessibility;
    }

    /**
     * @return mixed
     */
    public function getShowLinkHomePage()
    {
        return $this->showLinkHomePage;
    }

    /**
     * @param mixed $showLinkHomePage
     */
    public function setShowLinkHomePage($showLinkHomePage)
    {
        $this->showLinkHomePage = $showLinkHomePage;
    }

    /**
     * @return mixed
     */
    public function getShowLinkNavbar()
    {
        return $this->showLinkNavbar;
    }

    /**
     * @param mixed $showLinkNavbar
     */
    public function setShowLinkNavbar($showLinkNavbar)
    {
        $this->showLinkNavbar = $showLinkNavbar;
    }

    



}