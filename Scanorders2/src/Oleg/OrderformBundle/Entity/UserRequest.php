<?php

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Oleg\OrderformBundle\Repository\UserRequestRepository")
 * @ORM\Table(name="scan_accountrequest")
 * @ORM\HasLifecycleCallbacks
 */
class UserRequest
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $cwid;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $hascwid;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(
     *     message = "The email value should not be blank."
     * )
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    private $email;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $phone;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $job;

    /**
     * request permittedInstitutionalPHIScope
     *
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Institution")
     * @ORM\JoinTable(name="scan_accountrequest_institution",
     *      joinColumns={@ORM\JoinColumn(name="request_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="institution_id", referencedColumnName="id")}
     * )
     */
    private $requestedInstitutionalPHIScope;

    /**
     * requested Institution (ScanOrders Institution Scope)
     *
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\Institution")
     * @ORM\JoinColumn(name="institution_id", referencedColumnName="id")
     **/
    private $requestedScanOrderInstitutionScope;


    
//    /**
//     * @ORM\Column(type="string", nullable=true)
//     */
//    protected $department;
//
//    /**
//     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Service")
//     * @ORM\JoinTable(name="scan_accountrequest_service",
//     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
//     *      inverseJoinColumns={@ORM\JoinColumn(name="service_id", referencedColumnName="id")}
//     * )
//     */
//    protected $services;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $request;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $similaruser;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $primaryService;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $status;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $creationdate;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $actiondate;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $referencename;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $referenceemail;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $referencephone;


    function __construct()
    {
        //$this->services = new ArrayCollection();
        $this->requestedInstitutionalPHIScope = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $id;
    }

    /**
     * Set cwid
     *
     * @param string $cwid
     * @return Request
     */
    public function setCwid($cwid)
    {
        $this->cwid = $cwid;
    
        return $this;
    }

    /**
     * Get cwid
     *
     * @return string 
     */
    public function getCwid()
    {
        return $this->cwid;
    }

    /**
     * Set request
     *
     * @param string $request
     * @return Request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    
        return $this;
    }

    /**
     * Get request
     *
     * @return string 
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getJob() {
        return $this->job;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function setJob($job) {
        $this->job = $job;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
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


//    public function addServices(\Oleg\UserdirectoryBundle\Entity\Service $service)
//    {
//        if( !$this->services->contains($service) ) {
//            $this->services->add($service);
//        }
//
//        return $this;
//    }
//
//    public function removeServices(\Oleg\UserdirectoryBundle\Entity\Service $service)
//    {
//        $this->services->removeElement($service);
//    }
//
//    /**
//     * @param mixed $services
//     */
//    public function setServices($services)
//    {
//        if( $services->first() ) {
//            $this->primaryService = $services->first()->getId();
//        } else {
//            $this->primaryService = NULL;
//        }
//        $this->services = $services;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getServices()
//    {
//
//        $resArr = new ArrayCollection();
//        foreach( $this->services as $service ) {
//            if( $service->getId()."" == $this->getPrimaryService()."" ) {
//                //$resArr->removeElement($service);
//                //$resArr->first();
//                if( count($this->services) > 1 ) {
//                    $firstEl = $resArr->get(0);
//                    $resArr->set(0,$service);
//                    $resArr->add($firstEl);
//                } else {
//                    $resArr->add($service);
//                }
//            } else {
//                $resArr->add($service);
//            }
//        }
//        return $resArr;
//    }

    /**
     * @param mixed $primaryService
     */
    public function setPrimaryService($primaryService)
    {
        $this->primaryService = $primaryService;
    }

    /**
     * @return mixed
     */
    public function getPrimaryService()
    {
        return $this->primaryService;
    }

    /**
     * @param mixed $referenceemail
     */
    public function setReferenceemail($referenceemail)
    {
        $this->referenceemail = $referenceemail;
    }

    /**
     * @return mixed
     */
    public function getReferenceemail()
    {
        return $this->referenceemail;
    }

    /**
     * @param mixed $referencename
     */
    public function setReferencename($referencename)
    {
        $this->referencename = $referencename;
    }

    /**
     * @return mixed
     */
    public function getReferencename()
    {
        return $this->referencename;
    }

    /**
     * @param mixed $referencephone
     */
    public function setReferencephone($referencephone)
    {
        $this->referencephone = $referencephone;
    }

    /**
     * @return mixed
     */
    public function getReferencephone()
    {
        return $this->referencephone;
    }

    /**
     * @param mixed $similaruser
     */
    public function setSimilaruser($similaruser)
    {
        $this->similaruser = $similaruser;
    }

    /**
     * @return mixed
     */
    public function getSimilaruser()
    {
        return $this->similaruser;
    }

    /**
     * @param mixed $hascwid
     */
    public function setHascwid($hascwid)
    {
        $this->hascwid = $hascwid;
    }

    /**
     * @return mixed
     */
    public function getHascwid()
    {
        return $this->hascwid;
    }


    /**
     * @return mixed
     */
    public function getRequestedInstitutionalPHIScope()
    {
        return $this->requestedInstitutionalPHIScope;
    }

    public function setRequestedInstitutionalPHIScope( $requestedInstitutionalPHIScope )
    {
        $this->requestedInstitutionalPHIScope->clear();
        foreach( $requestedInstitutionalPHIScope as $institution ) {
            $this->addRequestedInstitutionalPHIScope($institution);
        }
        return $this->requestedInstitutionalPHIScope;
    }

    public function addRequestedInstitutionalPHIScope(\Oleg\UserdirectoryBundle\Entity\Institution $institution)
    {
        if( !$this->requestedInstitutionalPHIScope->contains($institution) ) {
            $this->requestedInstitutionalPHIScope->add($institution);
        }
        return $this;
    }

    public function removeRequestedInstitutionalPHIScope(\Oleg\UserdirectoryBundle\Entity\Institution $institution)
    {
        $this->requestedInstitutionalPHIScope->removeElement($institution);
    }

    /**
     * @param mixed $requestedScanOrderInstitutionScope
     */
    public function setRequestedScanOrderInstitutionScope($requestedScanOrderInstitutionScope)
    {
        $this->requestedScanOrderInstitutionScope = $requestedScanOrderInstitutionScope;
    }

    /**
     * @return mixed
     */
    public function getRequestedScanOrderInstitutionScope()
    {
        return $this->requestedScanOrderInstitutionScope;
    }



    /**
     * @param \DateTime $actiondate
     * @ORM\PreUpdate
     */
    public function setActiondate()
    {
        $this->actiondate = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getActiondate()
    {
        return $this->actiondate;
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



}