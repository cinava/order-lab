<?php

/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 4/11/2016
 * Time: 11:35 AM
 */

namespace Oleg\VacReqBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="vacreq_request")
 */
class VacReqRequest
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="exportId", type="integer", nullable=true)
     */
    private $exportId;

    /**
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $submitter;

    /**
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\User")
     * @ORM\JoinColumn(name="updateAuthor", referencedColumnName="id", nullable=true)
     */
    private $updateUser;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createDate;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateDate;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $phone;


    /**
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\Institution")
     */
    private $institution;


    /**
     * @ORM\OneToOne(targetEntity="VacReqRequestBusiness", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="requestBusiness_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     **/
    private $requestBusiness;


    /**
     * @ORM\OneToOne(targetEntity="VacReqRequestVacation", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="requestVacation_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     **/
    private $requestVacation;


    /**
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $approver;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $approvedRejectDate;


    //availability
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $availableViaEmail;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $availableEmail;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $availableViaCellPhone;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $availableCellPhone;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $availableViaOther;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $availableOther;

    /**
     * Not Available
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $availableNone;

//    /**
//     * Other
//     * @ORM\Column(type="string", nullable=true)
//     */
//    private $emergencyComment;
//
//    /**
//     * Cell Phone
//     * @ORM\Column(type="string", nullable=true)
//     */
//    private $emergencyPhone;
//
//    /**
//     * E-Mail
//     * @ORM\Column(type="string", nullable=true)
//     */
//    private $emergencyEmail;



    //extra not needed fields, but they are exists in the old site
    /**
     * REQUEST_STATUS_ID
     * status: pending, approved, rejected
     * @ORM\Column(type="string", nullable=true)
     */
    private $status;

    /**
     * FINAL_FIRST_DAY_AWAY
     * @ORM\Column(type="date", nullable=true)
     */
    private $firstDayAway;

    /**
     * FINAL_FIRST_DAY_BACK
     * @ORM\Column(type="date", nullable=true)
     */
    private $firstDayBackInOffice;

    /**
     * COMMENTS
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * UPDATE_COMMENTS
     * @ORM\Column(type="text", nullable=true)
     */
    private $updateComment;





    public function __construct($user=null) {
        $this->setUser($user);
        $this->setSubmitter($user);
        //$this->setStatus('pending');
        $this->setCreateDate(new \DateTime());
    }




    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getExportId()
    {
        return $this->exportId;
    }

    /**
     * @param mixed $exportId
     */
    public function setExportId($exportId)
    {
        $this->exportId = $exportId;
    }



    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
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
    public function getSubmitter()
    {
        return $this->submitter;
    }

    /**
     * @param mixed $submitter
     */
    public function setSubmitter($submitter)
    {
        $this->submitter = $submitter;
    }





    /**
     * @return mixed
     */
    public function getUpdateUser()
    {
        return $this->updateUser;
    }

    /**
     * @param mixed $updateUser
     */
    public function setUpdateUser($updateUser)
    {
        $this->updateUser = $updateUser;
    }

    /**
     * @return DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @param DateTime $createDate
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
    }

    /**
     * @return DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdateDate()
    {
        $this->updateDate = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getRequestBusiness()
    {
        return $this->requestBusiness;
    }

    /**
     * @param mixed $requestBusiness
     */
    public function setRequestBusiness($requestBusiness)
    {
        $this->requestBusiness = $requestBusiness;
    }

    /**
     * @return mixed
     */
    public function getRequestVacation()
    {
        return $this->requestVacation;
    }

    /**
     * @param mixed $requestVacation
     */
    public function setRequestVacation($requestVacation)
    {
        $this->requestVacation = $requestVacation;
    }

    /**
     * @return mixed
     */
    public function getAvailableViaEmail()
    {
        return $this->availableViaEmail;
    }

    /**
     * @param mixed $availableViaEmail
     */
    public function setAvailableViaEmail($availableViaEmail)
    {
        $this->availableViaEmail = $availableViaEmail;
    }

    /**
     * @return mixed
     */
    public function getAvailableEmail()
    {
        return $this->availableEmail;
    }

    /**
     * @param mixed $availableEmail
     */
    public function setAvailableEmail($availableEmail)
    {
        $this->availableEmail = $availableEmail;
    }

    /**
     * @return mixed
     */
    public function getAvailableViaCellPhone()
    {
        return $this->availableViaCellPhone;
    }

    /**
     * @param mixed $availableViaCellPhone
     */
    public function setAvailableViaCellPhone($availableViaCellPhone)
    {
        $this->availableViaCellPhone = $availableViaCellPhone;
    }

    /**
     * @return mixed
     */
    public function getAvailableCellPhone()
    {
        return $this->availableCellPhone;
    }

    /**
     * @param mixed $availableCellPhone
     */
    public function setAvailableCellPhone($availableCellPhone)
    {
        $this->availableCellPhone = $availableCellPhone;
    }

    /**
     * @return mixed
     */
    public function getAvailableViaOther()
    {
        return $this->availableViaOther;
    }

    /**
     * @param mixed $availableViaOther
     */
    public function setAvailableViaOther($availableViaOther)
    {
        $this->availableViaOther = $availableViaOther;
    }

    /**
     * @return mixed
     */
    public function getAvailableOther()
    {
        return $this->availableOther;
    }

    /**
     * @param mixed $availableOther
     */
    public function setAvailableOther($availableOther)
    {
        $this->availableOther = $availableOther;
    }

    /**
     * @return mixed
     */
    public function getAvailableNone()
    {
        return $this->availableNone;
    }

    /**
     * @param mixed $availableNone
     */
    public function setAvailableNone($availableNone)
    {
        $this->availableNone = $availableNone;
    }


    /**
     * @return mixed
     */
    public function getApprover()
    {
        return $this->approver;
    }

    /**
     * @param mixed $approver
     */
    public function setApprover($approver)
    {
        $this->approver = $approver;
        $this->setApprovedRejectDate(new \DateTime());
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

    /**
     * @return \DateTime
     */
    public function getApprovedRejectDate()
    {
        return $this->approvedRejectDate;
    }

    /**
     * @param \DateTime $approvedRejectDate
     */
    public function setApprovedRejectDate($approvedRejectDate)
    {
        $this->approvedRejectDate = $approvedRejectDate;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getFirstDayAway()
    {
        return $this->firstDayAway;
    }

    /**
     * @param mixed $firstDayAway
     */
    public function setFirstDayAway($firstDayAway)
    {
        $this->firstDayAway = $firstDayAway;
    }

    /**
     * @return mixed
     */
    public function getFirstDayBackInOffice()
    {
        return $this->firstDayBackInOffice;
    }

    /**
     * @param mixed $firstDayBackInOffice
     */
    public function setFirstDayBackInOffice($firstDayBackInOffice)
    {
        $this->firstDayBackInOffice = $firstDayBackInOffice;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getUpdateComment()
    {
        return $this->updateComment;
    }

    /**
     * @param mixed $updateComment
     */
    public function setUpdateComment($updateComment)
    {
        $this->updateComment = $updateComment;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }




    public function getOverallStatus()
    {
        if(
            $this->getRequestBusiness() && $this->getRequestBusiness()->getStatus() == 'approved' ||
            $this->getRequestVacation() && $this->getRequestVacation()->getStatus() == 'approved'
        ) {
            return 'approved';
        }

        if(
            $this->getRequestBusiness() && $this->getRequestBusiness()->getStatus() == 'rejected' ||
            $this->getRequestVacation() && $this->getRequestVacation()->getStatus() == 'rejected'
        ) {
            return 'rejected';
        }

        if(
            $this->getRequestBusiness() && $this->getRequestBusiness()->getStatus() == 'pending' ||
            $this->getRequestVacation() && $this->getRequestVacation()->getStatus() == 'pending'
        ) {
            return 'pending';
        }

        return null;
    }

    public function hasBusinessRequest() {
        if( $this->getRequestBusiness() && $this->getRequestBusiness()->getStartDate() ) {
            return true;
        }
        return false;
    }

    public function hasVacationRequest() {
        if( $this->getRequestVacation() && $this->getRequestVacation()->getStartDate() ) {
            return true;
        }
        return false;
    }


    public function __toString()
    {
        $break = "\r\n";
        //$transformer = new DateTimeToStringTransformer(null,null,'m/d/Y');

        $res = "Request ID: ".$this->getId().$break;
        $res .= "Person Away: ".$this->getUser().$break;
        $res .= "Organizational Group: ".$this->getInstitution().$break;
        $res .= "Phone Number for the person away: ".$this->getInstitution().$break.$break;

        if( $this->hasBusinessRequest() ) {
            $subRequest = $this->getRequestBusiness();
            $res .= $subRequest."".$break;
        }

        if( $this->hasVacationRequest() ) {
            $subRequest = $this->getRequestVacation();
            $res .= $subRequest."".$break;
        }

        return $res;
    }
}