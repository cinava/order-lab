<?php

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="perSiteSettings")
 */
class PerSiteSettings extends BaseUserAttributes
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $siteName;

    /**
     * @ORM\ManyToMany(targetEntity="Institution")
     * @ORM\JoinTable(name="perSiteSettings_institution",
     *      joinColumns={@ORM\JoinColumn(name="perSiteSettings_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="institution_id", referencedColumnName="id")}
     *      )
     **/
    private $permittedInstitutionalPHIScope;


    /**
     * @ORM\ManyToMany(targetEntity="Service")
     * @ORM\JoinTable(name="perSiteSettings_service",
     *      joinColumns={@ORM\JoinColumn(name="perSiteSettings_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="service_id", referencedColumnName="id")}
     *      )
     **/
    private $scanOrdersServicesScope;

    /**
     * @ORM\ManyToMany(targetEntity="Service")
     * @ORM\JoinTable(name="chiefServices_service",
     *      joinColumns={@ORM\JoinColumn(name="perSiteSettings_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="service_id", referencedColumnName="id")}
     *      )
     **/
    private $chiefServices;

    /**
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     **/
    private $defaultService;


    public function __construct() {
        $this->permittedInstitutionalPHIScope = new ArrayCollection();
        $this->scanOrdersServicesScope = new ArrayCollection();
        $this->chiefServices = new ArrayCollection();
    }



    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
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
     * @param mixed $defaultService
     */
    public function setDefaultService($defaultService)
    {
        $this->defaultService = $defaultService;
    }

    /**
     * @return mixed
     */
    public function getDefaultService()
    {
        return $this->defaultService;
    }

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

    //ScanOrdersServicesScope
    public function getScanOrdersServicesScope()
    {
        return $this->scanOrdersServicesScope;
    }

    public function addScanOrdersServicesScope( $scanOrdersServicesScope )
    {
        if( !$this->scanOrdersServicesScope->contains($scanOrdersServicesScope) ) {
            $this->scanOrdersServicesScope->add($scanOrdersServicesScope);
        }

    }

    public function removeScanOrdersServicesScope($scanOrdersServicesScope)
    {
        $this->scanOrdersServicesScope->removeElement($scanOrdersServicesScope);
    }


    //chiefServices
    public function getChiefServices()
    {
        return $this->chiefServices;
    }

    public function addChiefServices( $chiefServices )
    {
        if( !$this->chiefServices->contains($chiefServices) ) {
            $this->chiefServices->add($chiefServices);
        }

    }

    public function removeChiefServices($chiefServices)
    {
        $this->chiefServices->removeElement($chiefServices);
    }



}