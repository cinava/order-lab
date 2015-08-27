<?php

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_credentials")
 */
class Credentials extends BaseUserAttributes
{

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dob;

    /**
     * @ORM\ManyToOne(targetEntity="SexList", cascade={"persist"})
     * @ORM\JoinColumn(name="sex_id", referencedColumnName="id", nullable=true)
     * @ORM\JoinColumn(name="sex_id", referencedColumnName="id", nullable=true)
     */
    protected $sex;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ssn;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $numberCLIA;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $cliaExpirationDate;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $numberPFI;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $numberCOQ;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $coqExpirationDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $emergencyContactInfo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $hobby;

    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="credentials")
     */
    private $user;

    ///// Collections //////
    /**
     * @ORM\OneToMany(targetEntity="StateLicense", mappedBy="credentials", cascade={"persist"})
     */
    private $stateLicense;

    /**
     * @ORM\OneToMany(targetEntity="BoardCertification", mappedBy="credentials", cascade={"persist"})
     */
    private $boardCertification;

    /**
     * @ORM\OneToMany(targetEntity="CodeNYPH", mappedBy="credentials", cascade={"persist"})
     */
    private $codeNYPH;

    /**
     * @ORM\OneToMany(targetEntity="Identifier", mappedBy="credentials", cascade={"persist"})
     */
    private $identifiers;

    /**
     * @ORM\OneToMany(targetEntity="CurriculumVitae", mappedBy="credentials", cascade={"persist"})
     */
    private $cvs;

    /**
     * @ORM\OneToMany(targetEntity="Examination", mappedBy="credentials", cascade={"persist"})
     */
    private $examinations;

    /**
     * @ORM\OneToMany(targetEntity="Citizenship", mappedBy="credentials", cascade={"persist"})
     */
    private $citizenships;


    public function __construct($user,$addobjects=true) {

        parent::__construct();

        $this->stateLicense = new ArrayCollection();
        $this->boardCertification = new ArrayCollection();
        $this->codeNYPH = new ArrayCollection();
        $this->identifiers = new ArrayCollection();
        $this->cvs = new ArrayCollection();
        $this->examinations = new ArrayCollection();
        $this->citizenships = new ArrayCollection();


        $this->setType(self::TYPE_RESTRICTED);

        if( $addobjects ) {
            //create new state License
            $this->addStateLicense( new StateLicense() );

            //create new board Certification
            $this->addBoardCertification( new BoardCertification() );

            //create new Code NYPH
            $this->addCodeNYPH( new CodeNYPH() );
        }

    }

    /**
     * @param mixed $cliaExpirationDate
     */
    public function setCliaExpirationDate($cliaExpirationDate)
    {
        $this->cliaExpirationDate = $cliaExpirationDate;
    }

    /**
     * @return mixed
     */
    public function getCliaExpirationDate()
    {
        return $this->cliaExpirationDate;
    }

    /**
     * @param mixed $coqExpirationDate
     */
    public function setCoqExpirationDate($coqExpirationDate)
    {
        $this->coqExpirationDate = $coqExpirationDate;
    }

    /**
     * @return mixed
     */
    public function getCoqExpirationDate()
    {
        return $this->coqExpirationDate;
    }

    /**
     * @param mixed $dob
     */
    public function setDob($dob)
    {
        $this->dob = $dob;
    }

    /**
     * @return mixed
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * @param mixed $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
    }

    /**
     * @return mixed
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param mixed $numberCLIA
     */
    public function setNumberCLIA($numberCLIA)
    {
        $this->numberCLIA = $numberCLIA;
    }

    /**
     * @return mixed
     */
    public function getNumberCLIA()
    {
        return $this->numberCLIA;
    }

    /**
     * @param mixed $numberCOQ
     */
    public function setNumberCOQ($numberCOQ)
    {
        $this->numberCOQ = $numberCOQ;
    }

    /**
     * @return mixed
     */
    public function getNumberCOQ()
    {
        return $this->numberCOQ;
    }

    /**
     * @param mixed $numberPFI
     */
    public function setNumberPFI($numberPFI)
    {
        $this->numberPFI = $numberPFI;
    }

    /**
     * @return mixed
     */
    public function getNumberPFI()
    {
        return $this->numberPFI;
    }

    /**
     * @return mixed
     */
    public function getStateLicense()
    {
        return $this->stateLicense;
    }

    public function addStateLicense( $stateLicense )
    {
        if( !$stateLicense )
            return;

        if( !$this->stateLicense->contains($stateLicense) ) {
            $stateLicense->setCredentials($this);
            $this->stateLicense->add($stateLicense);
        }

    }

    public function removeStateLicense($stateLicense)
    {
        $this->stateLicense->removeElement($stateLicense);
    }

    /**
     * @return mixed
     */
    public function getBoardCertification()
    {
        return $this->boardCertification;
    }

    public function addBoardCertification( $boardCertification )
    {
        if( !$boardCertification )
            return;

        if( !$this->boardCertification->contains($boardCertification) ) {
            $boardCertification->setCredentials($this);
            $this->boardCertification->add($boardCertification);
        }

    }

    public function removeBoardCertification($boardCertification)
    {
        $this->boardCertification->removeElement($boardCertification);
    }

    /**
     * @param mixed $emergencyContactInfo
     */
    public function setEmergencyContactInfo($emergencyContactInfo)
    {
        $this->emergencyContactInfo = $emergencyContactInfo;
    }

    /**
     * @return mixed
     */
    public function getEmergencyContactInfo()
    {
        return $this->emergencyContactInfo;
    }

    /**
     * @param mixed $hobby
     */
    public function setHobby($hobby)
    {
        $this->hobby = $hobby;
    }

    /**
     * @return mixed
     */
    public function getHobby()
    {
        return $this->hobby;
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
     * @param mixed $ssn
     */
    public function setSsn($ssn)
    {
        $this->ssn = $ssn;
    }

    /**
     * @return mixed
     */
    public function getSsn()
    {
        return $this->ssn;
    }


    /**
     * @return mixed
     */
    public function getCodeNYPH()
    {
        return $this->codeNYPH;
    }
    public function addCodeNYPH( $codeNYPH )
    {
        if( !$codeNYPH )
            return;

        if( !$this->codeNYPH->contains($codeNYPH) ) {
            $codeNYPH->setCredentials($this);
            $this->codeNYPH->add($codeNYPH);
        }

    }
    public function removeCodeNYPH($codeNYPH)
    {
        $this->codeNYPH->removeElement($codeNYPH);
    }


    /**
     * @return mixed
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }
    public function addIdentifier( $identifier )
    {
        if( !$identifier )
            return;

        if( !$this->identifiers->contains($identifier) ) {
            $identifier->setCredentials($this);
            $this->identifiers->add($identifier);
        }

    }
    public function removeIdentifier($identifier)
    {
        $this->identifiers->removeElement($identifier);
    }

    public function getCvs()
    {
        return $this->cvs;
    }
    public function addCv($item)
    {
        if( $item && !$this->cvs->contains($item) ) {
            $item->setCredentials($this);
            $this->cvs->add($item);
        }

    }
    public function removeCv($item)
    {
        $this->cvs->removeElement($item);
    }

    public function addExamination($item)
    {
        if( $item && !$this->examinations->contains($item) ) {
            $this->examinations->add($item);
            $item->setCredentials($this);
        }
        return $this;
    }
    public function removeExamination($item)
    {
        $this->examinations->removeElement($item);
    }
    public function getExaminations()
    {
        return $this->examinations;
    }

    public function addCitizenship($item)
    {
        if( $item && !$this->citizenships->contains($item) ) {
            $this->citizenships->add($item);
            $item->setCredentials($this);
        }
        return $this;
    }
    public function removeCitizenship($item)
    {
        $this->citizenships->removeElement($item);
    }
    public function getCitizenships()
    {
        return $this->citizenships;
    }


    public function getOneRecentCv() {
        $items = $this->getCvs();
        $criteria = Criteria::create()
            //->where(Criteria::expr()->eq("user", $user))
            ->orderBy(array("creationDate" => Criteria::DESC))
        ;
        $itemsFiltered = $items->matching($criteria);

        return $itemsFiltered[0];
    }

    public function getOneRecentExamination() {
        $items = $this->getExaminations();
        $criteria = Criteria::create()
            //->where(Criteria::expr()->eq("user", $user))
            ->orderBy(array("creationDate" => Criteria::DESC))
        ;
        $itemsFiltered = $items->matching($criteria);

        return $itemsFiltered[0];
    }


    public function __toString() {
        return "Credentials";
    }

}