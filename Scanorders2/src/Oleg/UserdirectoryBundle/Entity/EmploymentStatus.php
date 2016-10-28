<?php

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_employmentStatus")
 */
class EmploymentStatus extends BaseUserAttributes
{

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="employmentStatus")
     * @ORM\JoinColumn(name="fosuser", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $hireDate;

    //Employee Type
    /**
     * @ORM\ManyToOne(targetEntity="EmploymentType")
     * @ORM\JoinColumn(name="employmentType_id", referencedColumnName="id", nullable=true)
     **/
    private $employmentType;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $terminationDate;

    /**
     * @ORM\ManyToOne(targetEntity="EmploymentTerminationType")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id", nullable=true)
     **/
    private $terminationType;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $terminationReason;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $jobDescriptionSummary;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $jobDescription;

    /**
     * Attachment can have many DocumentContainers; each DocumentContainers can have many Documents; each DocumentContainers has document type (DocumentTypeList)
     * @ORM\OneToOne(targetEntity="AttachmentContainer", cascade={"persist","remove"})
     **/
    private $attachmentContainer;

    /**
     * @ORM\ManyToOne(targetEntity="Institution")
     */
    private $institution;


    public function __construct($author=null) {
        parent::__construct($author);
        $this->setType(self::TYPE_PRIVATE);
        $this->setStatus(self::STATUS_VERIFIED);

        //add one document
        $this->createAttachmentDocument();
    }

    /**
     * @param mixed $hireDate
     */
    public function setHireDate($hireDate)
    {
        $this->hireDate = $hireDate;
    }

    /**
     * @return mixed
     */
    public function getHireDate()
    {
        return $this->hireDate;
    }

    /**
     * @param mixed $terminationDate
     */
    public function setTerminationDate($terminationDate)
    {
        $this->terminationDate = $terminationDate;
    }

    /**
     * @return mixed
     */
    public function getTerminationDate()
    {
        return $this->terminationDate;
    }

    /**
     * @param mixed $terminationReason
     */
    public function setTerminationReason($terminationReason)
    {
        $this->terminationReason = $terminationReason;
    }

    /**
     * @return mixed
     */
    public function getTerminationReason()
    {
        return $this->terminationReason;
    }

    /**
     * @param mixed $terminationType
     */
    public function setTerminationType($terminationType)
    {
        $this->terminationType = $terminationType;
    }

    /**
     * @return mixed
     */
    public function getTerminationType()
    {
        return $this->terminationType;
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
     * @param mixed $employmentType
     */
    public function setEmploymentType($employmentType)
    {
        $this->employmentType = $employmentType;
    }

    /**
     * @return mixed
     */
    public function getEmploymentType()
    {
        return $this->employmentType;
    }

    /**
     * @param mixed $attachmentContainer
     */
    public function setAttachmentContainer($attachmentContainer)
    {
        $this->attachmentContainer = $attachmentContainer;
    }

    /**
     * @return mixed
     */
    public function getAttachmentContainer()
    {
        return $this->attachmentContainer;
    }

    /**
     * @param mixed $jobDescription
     */
    public function setJobDescription($jobDescription)
    {
        $this->jobDescription = $jobDescription;
    }

    /**
     * @return mixed
     */
    public function getJobDescription()
    {
        return $this->jobDescription;
    }

    /**
     * @param mixed $jobDescriptionSummary
     */
    public function setJobDescriptionSummary($jobDescriptionSummary)
    {
        $this->jobDescriptionSummary = $jobDescriptionSummary;
    }

    /**
     * @return mixed
     */
    public function getJobDescriptionSummary()
    {
        return $this->jobDescriptionSummary;
    }

    /**
     * @return mixed
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * @param mixed $institution
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;
    }




    //create attachmentDocument holder with one DocumentContainer if not exists
    public function createAttachmentDocument() {
        //add one document
        $attachmentContainer = $this->getAttachmentContainer();
        //echo "attachmentContainer=".$attachmentContainer."<br>";
        if( !$attachmentContainer ) {
            $attachmentContainer = new AttachmentContainer();
            $this->setAttachmentContainer($attachmentContainer);
        }
        if( count($attachmentContainer->getDocumentContainers()) == 0 ) {
            $attachmentContainer->addDocumentContainer( new DocumentContainer() );
        }
    }


    public function __toString() {

        $documentContainersCount = 0;
        $documentsCount = 0;
        $attachmentContainer = $this->getAttachmentContainer();
        if( $attachmentContainer ) {
            foreach( $attachmentContainer->getDocumentContainers() as $documentContainer ) {
                $documentContainersCount++;
                $documentsCount = $documentsCount + count($documentContainer->getDocuments());
            }
        }

        return "Employment Status: id=".$this->getId().", documentContainersCount=".$documentContainersCount.", documentsCount=".$documentsCount;;
    }


}