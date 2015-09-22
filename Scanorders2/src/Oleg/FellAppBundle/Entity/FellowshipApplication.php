<?php
/**
 * Created by PhpStorm.
 * User: DevServer
 * Date: 8/24/15
 * Time: 11:08 AM
 */

namespace Oleg\FellAppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Oleg\UserdirectoryBundle\Entity\BaseUserAttributes;


/**
 * @ORM\Entity
 * @ORM\Table(name="fellapp_fellowshipApplication")
 */
class FellowshipApplication extends BaseUserAttributes {

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $googleFormId;

    /**
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\User", inversedBy="fellowshipApplications", cascade={"remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\FellowshipSubspecialty", cascade={"persist"})
     */
    private $fellowshipSubspecialty;

    /**
     * This should be the link to WCMC's "Department of Pathology and Laboratory Medicine"
     *
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\Institution")
     */
    private $institution;

    /**
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Document", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellApp_coverLetter",
     *      joinColumns={@ORM\JoinColumn(name="fellApp_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="coverLetter_id", referencedColumnName="id", onDelete="CASCADE", unique=true)}
     *      )
     * @ORM\OrderBy({"createdate" = "ASC"})
     **/
    private $coverLetters;

    /**
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Document", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellApp_cv",
     *      joinColumns={@ORM\JoinColumn(name="fellApp_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="cv_id", referencedColumnName="id", onDelete="CASCADE", unique=true)}
     *      )
     * @ORM\OrderBy({"createdate" = "ASC"})
     **/
    private $cvs;


    //Reprimands
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $reprimand;

    /**
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Document", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellApp_reprimandDocument",
     *      joinColumns={@ORM\JoinColumn(name="fellApp_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="reprimandDocument_id", referencedColumnName="id", onDelete="CASCADE", unique=true)}
     *      )
     * @ORM\OrderBy({"createdate" = "ASC"})
     **/
    private $reprimandDocuments;

    //Lawsuits
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lawsuit;

    /**
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Document", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellApp_lawsuitDocument",
     *      joinColumns={@ORM\JoinColumn(name="fellApp_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="lawsuitDocument_id", referencedColumnName="id", onDelete="CASCADE", unique=true)}
     *      )
     * @ORM\OrderBy({"createdate" = "ASC"})
     **/
    private $lawsuitDocuments;

    /**
     * @ORM\OneToMany(targetEntity="Reference", mappedBy="fellapp", cascade={"persist"})
     */
    private $references;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $honors;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $publications;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $memberships;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $signatureName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $signatureDate;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $interviewScore;

//    /**
//     * @ORM\Column(type="string", nullable=true)
//     */
//    private $applicationStatus;

    /**
     * application status as a list
     * @ORM\ManyToOne(targetEntity="FellAppStatus")
     */
    private $appStatus;

    /**
     * timestamp when google form is opened
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timestamp;


    /**
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Document", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellApp_report",
     *      joinColumns={@ORM\JoinColumn(name="fellApp_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="CASCADE", unique=true)}
     *      )
     * @ORM\OrderBy({"createdate" = "ASC"})
     **/
    private $reports;

    /**
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Document", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellApp_oldReport",
     *      joinColumns={@ORM\JoinColumn(name="fellApp_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="oldReport_id", referencedColumnName="id", onDelete="CASCADE", unique=true)}
     *      )
     * @ORM\OrderBy({"createdate" = "ASC"})
     **/
    private $oldReports;

    /**
     * Other Documents
     *
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Document", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellApp_document",
     *      joinColumns={@ORM\JoinColumn(name="fellApp_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id", onDelete="CASCADE", unique=true)}
     *      )
     * @ORM\OrderBy({"createdate" = "ASC"})
     **/
    private $documents;

    /**
     * Itinerarys
     *
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Document", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellApp_itinerary",
     *      joinColumns={@ORM\JoinColumn(name="fellApp_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="itinerary_id", referencedColumnName="id", onDelete="CASCADE", unique=true)}
     *      )
     * @ORM\OrderBy({"createdate" = "ASC"})
     **/
    private $itinerarys;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $interviewDate;

    /**
     * @ORM\OneToMany(targetEntity="Interview", mappedBy="fellapp", cascade={"persist","remove"})
     */
    private $interviews;


    /////////// user objects /////////////
//    /**
//     * @ORM\ManyToMany(targetEntity="EmploymentStatus")
//     * @ORM\JoinTable(name="fellapp_fellowshipApplication_employmentStatus",
//     *      joinColumns={@ORM\JoinColumn(name="fellowshipApplication_id", referencedColumnName="id")},
//     *      inverseJoinColumns={@ORM\JoinColumn(name="employmentStatus_id", referencedColumnName="id", unique=true)}
//     *      )
//     **/
//    private $employmentStatuses;

    /**
     * Other Documents
     *
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Document", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellApp_avatar",
     *      joinColumns={@ORM\JoinColumn(name="fellApp_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="avatar_id", referencedColumnName="id", onDelete="CASCADE", unique=true)}
     *      )
     * @ORM\OrderBy({"createdate" = "ASC"})
     **/
    private $avatars;

    /**
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Training", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellowshipApplication_training",
     *      joinColumns={@ORM\JoinColumn(name="fellowshipApplication_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="training_id", referencedColumnName="id", unique=true)}
     *      )
     * @ORM\OrderBy({"completionDate" = "DESC", "orderinlist" = "ASC"})
     **/
    private $trainings;

    /**
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Examination", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellowshipApplication_examination",
     *      joinColumns={@ORM\JoinColumn(name="fellowshipApplication_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="examination_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    private $examinations;

    /**
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Location", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellowshipApplication_location",
     *      joinColumns={@ORM\JoinColumn(name="fellowshipApplication_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="location_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    private $locations;

    /**
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\Citizenship", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellowshipApplication_citizenship",
     *      joinColumns={@ORM\JoinColumn(name="fellowshipApplication_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="citizenship_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    private $citizenships;

    /**
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\StateLicense", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellowshipApplication_stateLicense",
     *      joinColumns={@ORM\JoinColumn(name="fellowshipApplication_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="stateLicense_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    private $stateLicenses;

    /**
     * @ORM\ManyToMany(targetEntity="Oleg\UserdirectoryBundle\Entity\BoardCertification", cascade={"persist","remove"})
     * @ORM\JoinTable(name="fellapp_fellowshipApplication_boardCertification",
     *      joinColumns={@ORM\JoinColumn(name="fellowshipApplication_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="boardCertification_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    private $boardCertifications;

    /////////// EOF user objects /////////////


    public function __construct($author=null) {
        parent::__construct($author);

        $this->cvs = new ArrayCollection();
        $this->coverLetters = new ArrayCollection();
        $this->reprimandDocuments = new ArrayCollection();
        $this->lawsuitDocuments = new ArrayCollection();
        $this->references = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->oldReports = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->itinerarys = new ArrayCollection();
        $this->interviews = new ArrayCollection();

        //$this->employmentStatuses = new ArrayCollection();
        $this->trainings = new ArrayCollection();
        $this->avatars = new ArrayCollection();
        $this->examinations = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->citizenships = new ArrayCollection();
        $this->stateLicenses = new ArrayCollection();
        $this->boardCertifications = new ArrayCollection();

    }




    //////////////// user object //////////////////////
//    public function addEmploymentStatus($item)
//    {
//        if( $item && !$this->employmentStatuses->contains($item) ) {
//            $this->employmentStatuses->add($item);
//        }
//        return $this;
//    }
//    public function removeEmploymentStatus($item)
//    {
//        $this->employmentStatuses->removeElement($item);
//    }
//    public function getEmploymentStatuses()
//    {
//        return $this->employmentStatuses;
//    }

    public function addTraining($item)
    {
        if( $item && !$this->trainings->contains($item) ) {
            $this->trainings->add($item);
        }
        return $this;
    }
    public function removeTraining($item)
    {
        $this->trainings->removeElement($item);
    }
    public function getTrainings()
    {
        return $this->trainings;
    }


    public function addAvatar($item)
    {
        if( $item && !$this->avatars->contains($item) ) {
            $this->avatars->add($item);
        }
        return $this;
    }
    public function removeAvatar($item)
    {
        $this->avatars->removeElement($item);
    }
    public function getAvatars()
    {
        return $this->avatars;
    }


    public function addExamination($item)
    {
        if( $item && !$this->examinations->contains($item) ) {
            $this->examinations->add($item);
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

    public function addLocation($location)
    {
        if( $location && !$this->locations->contains($location) ) {
            $this->locations->add($location);
        }

        return $this;
    }
    public function removeLocation($locations)
    {
        $this->locations->removeElement($locations);
    }
    public function getLocations()
    {
        return $this->locations;
    }

    public function addCitizenship($item)
    {
        if( $item && !$this->citizenships->contains($item) ) {
            $this->citizenships->add($item);
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

    public function getStateLicenses()
    {
        return $this->stateLicenses;
    }
    public function addStateLicense($item)
    {
        if( $item && !$this->stateLicenses->contains($item) ) {
            $this->stateLicenses->add($item);
        }

    }
    public function removeStateLicense($item)
    {
        $this->stateLicenses->removeElement($item);
    }

    public function getBoardCertifications()
    {
        return $this->boardCertifications;
    }
    public function addBoardCertification($item)
    {
        if( $item && !$this->boardCertifications->contains($item) ) {
            $this->boardCertifications->add($item);
        }

    }
    public function removeBoardCertification($item)
    {
        $this->boardCertifications->removeElement($item);
    }

    //////////////// EOF user object //////////////////////




    /**
     * @param mixed $googleFormId
     */
    public function setGoogleFormId($googleFormId)
    {
        $this->googleFormId = $googleFormId;
    }

    /**
     * @return mixed
     */
    public function getGoogleFormId()
    {
        return $this->googleFormId;
    }


    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $fellowshipSubspecialty
     */
    public function setFellowshipSubspecialty($fellowshipSubspecialty)
    {
        $this->fellowshipSubspecialty = $fellowshipSubspecialty;
    }

    /**
     * @return mixed
     */
    public function getFellowshipSubspecialty()
    {
        return $this->fellowshipSubspecialty;
    }

    /**
     * @param mixed $institution
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;
    }

    /**
     * @return mixed
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
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



    public function getCvs()
    {
        return $this->cvs;
    }
    public function addCv($item)
    {
        if( $item && !$this->cvs->contains($item) ) {
            $this->cvs->add($item);
        }

    }
    public function removeCv($item)
    {
        $this->cvs->removeElement($item);
    }

    public function addCoverLetter($item)
    {
        if( $item && !$this->coverLetters->contains($item) ) {
            $this->coverLetters->add($item);
        }
        return $this;
    }
    public function removeCoverLetter($item)
    {
        $this->coverLetters->removeElement($item);
    }
    public function getCoverLetters()
    {
        return $this->coverLetters;
    }

    public function addReprimandDocument($item)
    {
        if( $item && !$this->reprimandDocuments->contains($item) ) {
            $this->reprimandDocuments->add($item);
        }
        return $this;
    }
    public function removeReprimandDocument($item)
    {
        $this->reprimandDocuments->removeElement($item);
    }
    public function getReprimandDocuments()
    {
        return $this->reprimandDocuments;
    }

    public function addLawsuitDocument($item)
    {
        if( $item && !$this->lawsuitDocuments->contains($item) ) {
            $this->lawsuitDocuments->add($item);
        }
        return $this;
    }
    public function removeLawsuitDocument($item)
    {
        $this->lawsuitDocuments->removeElement($item);
    }
    public function getLawsuitDocuments()
    {
        return $this->lawsuitDocuments;
    }

    /**
     * @param mixed $lawsuit
     */
    public function setLawsuit($lawsuit)
    {
        $this->lawsuit = $lawsuit;
    }

    /**
     * @return mixed
     */
    public function getLawsuit()
    {
        return $this->lawsuit;
    }

    /**
     * @param mixed $reprimand
     */
    public function setReprimand($reprimand)
    {
        $this->reprimand = $reprimand;
    }

    /**
     * @return mixed
     */
    public function getReprimand()
    {
        return $this->reprimand;
    }

    public function addReference($item)
    {
        if( $item && !$this->references->contains($item) ) {
            $this->references->add($item);
            $item->setFellapp($this);
        }
        return $this;
    }
    public function removeReference($item)
    {
        $this->references->removeElement($item);
    }
    public function getReferences()
    {
        return $this->references;
    }


    public function addReport($item)
    {
        if( $item && !$this->reports->contains($item) ) {
            $this->reports->add($item);
        }
        return $this;
    }
    public function removeReport($item)
    {
        $this->reports->removeElement($item);
    }
    public function getReports()
    {
        return $this->reports;
    }


    public function addOldReport($item)
    {
        if( $item && !$this->oldReports->contains($item) ) {
            $this->oldReports->add($item);
        }
        return $this;
    }
    public function removeOldReport($item)
    {
        $this->oldReports->removeElement($item);
    }
    public function getOldReports()
    {
        return $this->oldReports;
    }


    public function addItinerary($item)
    {
        if( $item && !$this->itinerarys->contains($item) ) {
            $this->itinerarys->add($item);
        }
        return $this;
    }
    public function removeItinerary($item)
    {
        $this->itinerarys->removeElement($item);
    }
    public function getItinerarys()
    {
        return $this->itinerarys;
    }


    public function addInterview($item)
    {
        if( $item && !$this->interviews->contains($item) ) {
            $this->interviews->add($item);
            $item->setFellapp($this);
        }
        return $this;
    }
    public function removeInterview($item)
    {
        $this->interviews->removeElement($item);
    }
    public function getInterviews()
    {
        return $this->interviews;
    }


    /**
     * @param mixed $honors
     */
    public function setHonors($honors)
    {
        $this->honors = $honors;
    }

    /**
     * @return mixed
     */
    public function getHonors()
    {
        return $this->honors;
    }

    /**
     * @param mixed $memberships
     */
    public function setMemberships($memberships)
    {
        $this->memberships = $memberships;
    }

    /**
     * @return mixed
     */
    public function getMemberships()
    {
        return $this->memberships;
    }

    /**
     * @param mixed $publications
     */
    public function setPublications($publications)
    {
        $this->publications = $publications;
    }

    /**
     * @return mixed
     */
    public function getPublications()
    {
        return $this->publications;
    }

    /**
     * @param mixed $signatureName
     */
    public function setSignatureName($signatureName)
    {
        $this->signatureName = $signatureName;
    }

    /**
     * @return mixed
     */
    public function getSignatureName()
    {
        return $this->signatureName;
    }

    /**
     * @param mixed $signatureDate
     */
    public function setSignatureDate($signatureDate)
    {
        $this->signatureDate = $signatureDate;
    }

    /**
     * @return mixed
     */
    public function getSignatureDate()
    {
        return $this->signatureDate;
    }

    /**
     * @param mixed $interviewScore
     */
    public function setInterviewScore($interviewScore)
    {
        $this->interviewScore = $interviewScore;
    }

    /**
     * @return mixed
     */
    public function getInterviewScore()
    {
        return $this->interviewScore;
    }

//    /**
//     * @param mixed $applicationStatus
//     */
//    public function setApplicationStatus($applicationStatus)
//    {
//        $this->applicationStatus = $applicationStatus;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getApplicationStatus()
//    {
//        return $this->applicationStatus;
//    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function addDocument($item)
    {
        if( $item && !$this->documents->contains($item) ) {
            $this->documents->add($item);
        }
        return $this;
    }
    public function removeDocument($item)
    {
        $this->documents->removeElement($item);
    }
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @param mixed $interviewDate
     */
    public function setInterviewDate($interviewDate)
    {
        $this->interviewDate = $interviewDate;
    }

    /**
     * @return mixed
     */
    public function getInterviewDate()
    {
        return $this->interviewDate;
    }

    /**
     * @param mixed $appStatus
     */
    public function setAppStatus($appStatus)
    {
        $this->appStatus = $appStatus;
    }

    /**
     * @return mixed
     */
    public function getAppStatus()
    {
        return $this->appStatus;
    }






    public function clearReports() {
        $this->reports->clear();
    }

    public function getRecentReport() {
        if( count($this->getReports()) > 0 ) {
            return $this->getReports()->last();
        } else {
            return null;
        }
    }

    public function getRecentCoverLetter() {
        if( count($this->getCoverLetters()) > 0 ) {
            return $this->getCoverLetters()->last();
        } else {
            return null;
        }
    }

    public function getRecentCv() {
        if( count($this->getCvs()) > 0 ) {
            return $this->getCvs()->last();
        } else {
            return null;
        }
    }

    public function getRecentAvatar() {
        if( count($this->getAvatars()) > 0 ) {
            return $this->getAvatars()->last();
        } else {
            return null;
        }
    }

//    public function getRecentExaminationScores() {
//        $recentExamination = $this->getUser()->getCredentials()->getOneRecentExamination();
//        return $recentExamination->getScores();
//    }
    public function getExaminationScores() {
        $scores = new ArrayCollection();
        foreach( $this->getExaminations() as $examination ) {
            foreach( $examination->getScores() as $score ) {
                if( $score && !$scores->contains($score) ) {
                    $scores->add($score);
                }
            }
        }
        return $scores;
    }

    public function getReferenceLetters() {
        $refletters = new ArrayCollection();
        foreach( $this->getReferences() as $reference ) {
            foreach( $reference->getDocuments() as $refletter ) {
                if( $refletter && !$refletters->contains($refletter) ) {
                    $refletters->add($refletter);
                }
            }
        }
        return $refletters;
    }

    public function getRecentReprimand() {
        if( count($this->getReprimandDocuments()) > 0 ) {
            return $this->getReprimandDocuments()->last();
        } else {
            return null;
        }
    }

    public function getRecentLegalExplanation() {
        if( count($this->getLawsuitDocuments()) > 0 ) {
            return $this->getLawsuitDocuments()->last();
        } else {
            return null;
        }
    }

    public function getRecentItinerary() {
        if( count($this->getItinerarys()) > 0 ) {
            return $this->getItinerarys()->last();
        } else {
            return null;
        }
    }

//    //interface methods
//    public function addDocument($item)
//    {
//        $this->addCoverLetter($item);
//        return $this;
//    }
//    public function removeDocument($item)
//    {
//        $this->removeCoverLetter($item);
//    }
//    public function getDocuments()
//    {
//        return $this->getCoverLetters();
//    }



    public function __toString() {
        return "FellowshipApplication";
    }

} 