<?php

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

use Oleg\UserdirectoryBundle\Entity\BaseUserAttributes;

/**
 * @ORM\Entity
 * @ORM\Table(
 *  name="scan_perSiteSettings",
 *  indexes={
 *      @ORM\Index( name="user_idx", columns={"fosuser"} ),
 *  }
 * )
 */
class PerSiteSettings extends BaseUserAttributes
{

    /**
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Institution")
     * @ORM\JoinTable(name="scan_perSiteSettings_institution",
     *      joinColumns={@ORM\JoinColumn(name="perSiteSettings_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="institution_id", referencedColumnName="id")}
     *      )
     **/
    private $permittedInstitutionalPHIScope;


//    /**
//     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Service")
//     * @ORM\JoinTable(name="scan_perSiteSettings_service",
//     *      joinColumns={@ORM\JoinColumn(name="perSiteSettings_id", referencedColumnName="id")},
//     *      inverseJoinColumns={@ORM\JoinColumn(name="service_id", referencedColumnName="id")}
//     *      )
//     **/
//    private $scanOrdersServicesScope;
//
//    /**
//     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Service")
//     * @ORM\JoinTable(name="scan_chiefServices_service",
//     *      joinColumns={@ORM\JoinColumn(name="perSiteSettings_id", referencedColumnName="id")},
//     *      inverseJoinColumns={@ORM\JoinColumn(name="service_id", referencedColumnName="id")}
//     *      )
//     **/
//    private $chiefServices;



    /**
     * defaultInstitution (ScanOrders Institution Scope)
     *
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\Institution")
     * @ORM\JoinColumn(name="institution_id", referencedColumnName="id")
     **/
    private $scanOrderInstitutionScope;

//    /**
//     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\Department")
//     * @ORM\JoinColumn(name="department_id", referencedColumnName="id")
//     **/
//    private $defaultDepartment;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\Division")
//     * @ORM\JoinColumn(name="division_id", referencedColumnName="id")
//     **/
//    private $defaultDivision;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\Service")
//     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
//     **/
//    private $defaultService;

    /**
     * @ORM\OneToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\User")
     * @ORM\JoinColumn(name="fosuser", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $tooltip;


    public function __construct() {

        parent::__construct();

        $this->permittedInstitutionalPHIScope = new ArrayCollection();
        //$this->scanOrdersServicesScope = new ArrayCollection();
        //$this->chiefServices = new ArrayCollection();
        $this->setType(self::TYPE_RESTRICTED);
        $this->tooltip = true;

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


//    /**
//     * @param mixed $defaultService
//     */
//    public function setDefaultService($defaultService)
//    {
//        $this->defaultService = $defaultService;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getDefaultService()
//    {
//        return $this->defaultService;
//    }
//
//    /**
//     * @param mixed $defaultDepartment
//     */
//    public function setDefaultDepartment($defaultDepartment)
//    {
//        $this->defaultDepartment = $defaultDepartment;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getDefaultDepartment()
//    {
//        return $this->defaultDepartment;
//    }
//
//    /**
//     * @param mixed $defaultDivision
//     */
//    public function setDefaultDivision($defaultDivision)
//    {
//        $this->defaultDivision = $defaultDivision;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getDefaultDivision()
//    {
//        return $this->defaultDivision;
//    }
//    /**
//     * @param mixed $defaultInstitution
//     */
//    public function setDefaultInstitution($defaultInstitution)
//    {
//        $this->defaultInstitution = $defaultInstitution;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getDefaultInstitution()
//    {
//        return $this->defaultInstitution;
//    }




    //permittedInstitutionalPHIScope
    public function getPermittedInstitutionalPHIScope()
    {
        return $this->permittedInstitutionalPHIScope;
    }

    public function addPermittedInstitutionalPHIScope( $permittedInstitutionalPHIScope )
    {
        if( !$this->permittedInstitutionalPHIScope->contains($permittedInstitutionalPHIScope) ) {
            $this->permittedInstitutionalPHIScope->add($permittedInstitutionalPHIScope);
        }

    }

    public function removePermittedInstitutionalPHIScope($permittedInstitutionalPHIScope)
    {
        $this->permittedInstitutionalPHIScope->removeElement($permittedInstitutionalPHIScope);
    }

    /**
     * @param mixed $scanOrderInstitutionScope
     */
    public function setScanOrderInstitutionScope($scanOrderInstitutionScope)
    {
        $this->scanOrderInstitutionScope = $scanOrderInstitutionScope;
    }

    /**
     * @return mixed
     */
    public function getScanOrderInstitutionScope()
    {
        return $this->scanOrderInstitutionScope;
    }




//    //ScanOrdersServicesScope
//    public function getScanOrdersServicesScope()
//    {
//        return $this->scanOrdersServicesScope;
//    }
//
//    public function addScanOrdersServicesScope( $scanOrdersServicesScope )
//    {
//        if( !$this->scanOrdersServicesScope->contains($scanOrdersServicesScope) ) {
//            $this->scanOrdersServicesScope->add($scanOrdersServicesScope);
//        }
//
//    }
//
//    public function removeScanOrdersServicesScope($scanOrdersServicesScope)
//    {
//        $this->scanOrdersServicesScope->removeElement($scanOrdersServicesScope);
//    }
//
//
//    //chiefServices
//    public function getChiefServices()
//    {
//        return $this->chiefServices;
//    }
//
//    public function addChiefService( $chiefService )
//    {
//        if( !$this->chiefServices->contains($chiefService) ) {
//            $this->chiefServices->add($chiefService);
//        }
//
//    }
//
//    public function removeChiefService($chiefService)
//    {
//        $this->chiefServices->removeElement($chiefService);
//    }

    /**
     * @param mixed $tooltip
     */
    public function setTooltip($tooltip)
    {
        $this->tooltip = $tooltip;
    }

    /**
     * @return mixed
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }




}