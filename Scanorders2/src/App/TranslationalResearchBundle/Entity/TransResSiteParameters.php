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

namespace App\TranslationalResearchBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

/**
 * @ORM\Entity
 * @ORM\Table(name="transres_siteParameters", uniqueConstraints={@ORM\UniqueConstraint(name="siteParameters_unique", columns={"projectSpecialty_id"})})
 * @ORM\HasLifecycleCallbacks
 */
class TransResSiteParameters {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\UserdirectoryBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $creator;

    /**
     * @ORM\ManyToOne(targetEntity="App\UserdirectoryBundle\Entity\User")
     * @ORM\JoinColumn(name="updateUser", referencedColumnName="id", nullable=true)
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
     * Hematopathology or AP/CP
     *
     * @ORM\ManyToOne(targetEntity="App\TranslationalResearchBundle\Entity\SpecialtyList", cascade={"persist"})
     * @ORM\JoinColumn(name="projectSpecialty_id", referencedColumnName="id", nullable=false)
     */
    private $projectSpecialty;

    /**
     * invoice header
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $transresFromHeader;

    /**
     * invoice footer
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $transresFooter;

    /**
     * Default Invoice Logos
     *
     * @ORM\ManyToMany(targetEntity="App\UserdirectoryBundle\Entity\Document", cascade={"persist","remove"})
     * @ORM\JoinTable(name="transres_transResSiteParameters_transresLogo",
     *      joinColumns={@ORM\JoinColumn(name="transResSiteParameter_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="transresLogo_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     * @ORM\OrderBy({"createdate" = "DESC"})
     **/
    private $transresLogos;

    /**
     * Email body for notification email when Invoice PDF is sent to PI
     * @ORM\Column(type="text", nullable=true)
     */
    private $transresNotificationEmail;

    /**
     * Email subject for notification email when Invoice PDF is sent to PI
     * @ORM\Column(type="text", nullable=true)
     */
    private $transresNotificationEmailSubject;

    ////////// Invoice reminder email ////////////
    /**
     * Translational Research Unpaid Invoice Reminder Schedule in Months 
     * over due in months (integer), reminder interval in months (integer), max reminder count (integer)
     * 
     * @ORM\Column(type="string", nullable=true)
     */
    private $invoiceReminderSchedule;

    /**
     * Translational Research Reminder Email Subject
     * @ORM\Column(type="text", nullable=true)
     */
    private $invoiceReminderSubject;

    /**
     * Translational Research Unpaid Invoice Reminder Email Body
     * @ORM\Column(type="text", nullable=true)
     */
    private $invoiceReminderBody;

    /**
     * Translational Research Reminder Email - Send From the Following Address
     * @ORM\Column(type="text", nullable=true)
     */
    private $invoiceReminderEmail;
    ////////// EOF Invoice reminder email ////////////

    /**
     * Email body for notification email is being to send to the Request's PI when Request status is changed to "Completed and Notified"
     * @ORM\Column(type="text", nullable=true)
     */
    private $requestCompletedNotifiedEmail;

    /**
     * Email subject for notification email is being to send to to the Request's PI when Request status is changed to "Completed and Notified"
     * @ORM\Column(type="text", nullable=true)
     */
    private $requestCompletedNotifiedEmailSubject;

    /**
     * Invoice's invoiceSalesperson
     *
     * @ORM\ManyToOne(targetEntity="App\UserdirectoryBundle\Entity\User")
     * @ORM\JoinColumn(name="invoiceSalesperson", referencedColumnName="id", nullable=true)
     */
    private $invoiceSalesperson;


    /**
     * Default Accession Type used in the System column in the Work Request handsontable
     *
     * @ORM\ManyToOne(targetEntity="App\OrderformBundle\Entity\AccessionType")
     */
    private $accessionType;


    //Packing Slip
    /**
     * Default Packing Slip Logos
     *
     * @ORM\ManyToMany(targetEntity="App\UserdirectoryBundle\Entity\Document", cascade={"persist","remove"})
     * @ORM\JoinTable(name="transres_transResSiteParameters_transresPackingSlipLogo",
     *      joinColumns={@ORM\JoinColumn(name="transResSiteParameter_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="transresPackingSlipLogo_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     * @ORM\OrderBy({"createdate" = "DESC"})
     **/
    private $transresPackingSlipLogos;

    /**
     * Packing Slip
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $transresPackingSlipTitle;

    /**
     * Department of Pathology and Laboratory Medicine
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $transresPackingSlipHeadline1;

    /**
     * Translational Research Program
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $transresPackingSlipHeadline2;

    /**
     * Blue (HTML color value)
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $transresPackingSlipHeadlineColor;

    /**
     * Red (HTML Color Value)
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $transresPackingSlipHighlightedColor;

    /**
     * Comment for Request
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $transresPackingSlipSubHeading1;

    /**
     * List of Deliverables
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $transresPackingSlipSubHeading2;

    /**
     * Please contact us for more information about this slip.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $transresPackingSlipFooter1;

    /**
     * Translational Research Program * 1300 York Ave., F512, New York, NY 10065 * Tel (212) 746-62255
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $transresPackingSlipFooter2;

    /**
     * Barcode size
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $barcodeSize;

    /**
     * Packing Slip font size
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $transresPackingSlipFontSize;

    //Project:
    /**
     * The answers you provide must reflect what has been requested in the approved IRB and the approved tissue request form.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $specimenDetailsComment;

    /**
     * 'NYP/WCM' Pathologist(s) Involved
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $institutionName;

    /**
     * from email address: trp-admin@med.cornell.edu
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $fromEmail;

    /**
     * Add trp@med.cornell.edu to site settings and use it for Cc for Work Request status change to "Completed" and "Completed and Notified"
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $notifyEmail;

    /**
     * Translational Research Email Notification Asking To Contact With Concerns:
     * Please review the deliverables and comments (if any), and if you have any concerns,
     * contact the Translational Research group by emailing User Name (email)...
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $emailNoteConcern;


    ////////// Project reminder ////////////
    //4 delay fields for review + 2 delay fields for missinginfo + 2 subject, body for review + 2 subject, body for missinginfo
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $projectReminderDelay_irb_review;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $projectReminderDelay_admin_review;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $projectReminderDelay_committee_review;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $projectReminderDelay_final_review;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $projectReminderDelay_irb_missinginfo;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $projectReminderDelay_admin_missinginfo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $projectReminderSubject_review;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $projectReminderBody_review;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $projectReminderSubject_missinginfo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $projectReminderBody_missinginfo;
    ////////// EOF Project reminder email ////////////


    ////////////// Pending Work Requests reminder email //////////////
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $pendingRequestReminderDelay;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pendingRequestReminderSubject;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pendingRequestReminderBody;
    ////////////// EOF Pending Work Requests reminder email //////////////

    ////////////// Completed Work Requests reminder email //////////////
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $completedRequestReminderDelay;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $completedRequestReminderSubject;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $completedRequestReminderBody;
    ////////////// EOF Completed Work Requests reminder email //////////////

    ////////////// Completed and Notified Work Requests without issued invoice reminder email //////////////
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $completedNoInvoiceRequestReminderDelay;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $completedNoInvoiceRequestReminderSubject;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $completedNoInvoiceRequestReminderBody;
    ////////////// EOF Completed Work Requests reminder email //////////////


//    /**
//     * @ORM\ManyToOne(targetEntity="App\UserdirectoryBundle\Entity\User")
//     * @ORM\JoinColumn(name="testuser", referencedColumnName="id", nullable=true)
//     */
//    private $testuser;


    public function __construct($user=null) {
        $this->setCreator($user);
        $this->setCreateDate(new \DateTime());

        $this->transresLogos = new ArrayCollection();
        $this->transresPackingSlipLogos = new ArrayCollection();
    }



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param mixed $creator
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
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
     * @return mixed
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @param mixed $createDate
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
    }

    /**
     * @return \DateTime
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
    public function getProjectSpecialty()
    {
        return $this->projectSpecialty;
    }

    /**
     * @param mixed $projectSpecialty
     */
    public function setProjectSpecialty($projectSpecialty)
    {
        $this->projectSpecialty = $projectSpecialty;
    }

    /**
     * @return mixed
     */
    public function getTransresFromHeader()
    {
        return $this->transresFromHeader;
    }

    /**
     * @param mixed $transresFromHeader
     */
    public function setTransresFromHeader($transresFromHeader)
    {
        $this->transresFromHeader = $transresFromHeader;
    }

    /**
     * @return mixed
     */
    public function getTransresFooter()
    {
        return $this->transresFooter;
    }

    /**
     * @param mixed $transresFooter
     */
    public function setTransresFooter($transresFooter)
    {
        $this->transresFooter = $transresFooter;
    }

    public function addTransresLogo($item)
    {
        if( $item && !$this->transresLogos->contains($item) ) {
            $this->transresLogos->add($item);
            $item->createUseObject($this);
        }
        return $this;
    }
    public function removeTransresLogo($item)
    {
        $this->transresLogos->removeElement($item);
        $item->clearUseObject();
    }
    public function getTransresLogos()
    {
        return $this->transresLogos;
    }

    /**
     * @return mixed
     */
    public function getTransresNotificationEmail()
    {
        return $this->transresNotificationEmail;
    }

    /**
     * @param mixed $transresNotificationEmail
     */
    public function setTransresNotificationEmail($transresNotificationEmail)
    {
        $this->transresNotificationEmail = $transresNotificationEmail;
    }

    /**
     * @return mixed
     */
    public function getTransresNotificationEmailSubject()
    {
        return $this->transresNotificationEmailSubject;
    }

    /**
     * @param mixed $transresNotificationEmailSubject
     */
    public function setTransresNotificationEmailSubject($transresNotificationEmailSubject)
    {
        $this->transresNotificationEmailSubject = $transresNotificationEmailSubject;
    }

    /**
     * @return mixed
     */
    public function getRequestCompletedNotifiedEmail()
    {
        return $this->requestCompletedNotifiedEmail;
    }

    /**
     * @param mixed $requestCompletedNotifiedEmail
     */
    public function setRequestCompletedNotifiedEmail($requestCompletedNotifiedEmail)
    {
        $this->requestCompletedNotifiedEmail = $requestCompletedNotifiedEmail;
    }

    /**
     * @return mixed
     */
    public function getRequestCompletedNotifiedEmailSubject()
    {
        return $this->requestCompletedNotifiedEmailSubject;
    }

    /**
     * @param mixed $requestCompletedNotifiedEmailSubject
     */
    public function setRequestCompletedNotifiedEmailSubject($requestCompletedNotifiedEmailSubject)
    {
        $this->requestCompletedNotifiedEmailSubject = $requestCompletedNotifiedEmailSubject;
    }

    /**
     * @return mixed
     */
    public function getInvoiceSalesperson()
    {
        return $this->invoiceSalesperson;
    }

    /**
     * @param mixed $invoiceSalesperson
     */
    public function setInvoiceSalesperson($invoiceSalesperson)
    {
        $this->invoiceSalesperson = $invoiceSalesperson;
    }

    /**
     * @return mixed
     */
    public function getAccessionType()
    {
        return $this->accessionType;
    }

    /**
     * @param mixed $accessionType
     */
    public function setAccessionType($accessionType)
    {
        $this->accessionType = $accessionType;
    }

    /**
     * @return mixed
     */
    public function getTransresPackingSlipLogos()
    {
        return $this->transresPackingSlipLogos;
    }
    public function addTransresPackingSlipLogo($item)
    {
        if( $item && !$this->transresPackingSlipLogos->contains($item) ) {
            $this->transresPackingSlipLogos->add($item);
            $item->createUseObject($this);
        }
        return $this;
    }
    public function removeTransresPackingSlipLogo($item)
    {
        $this->transresPackingSlipLogos->removeElement($item);
        $item->clearUseObject();
    }

    /**
     * @return mixed
     */
    public function getTransresPackingSlipTitle()
    {
        return $this->transresPackingSlipTitle;
    }

    /**
     * @param mixed $transresPackingSlipTitle
     */
    public function setTransresPackingSlipTitle($transresPackingSlipTitle)
    {
        $this->transresPackingSlipTitle = $transresPackingSlipTitle;
    }

    /**
     * @return mixed
     */
    public function getTransresPackingSlipHeadline1()
    {
        return $this->transresPackingSlipHeadline1;
    }

    /**
     * @param mixed $transresPackingSlipHeadline1
     */
    public function setTransresPackingSlipHeadline1($transresPackingSlipHeadline1)
    {
        $this->transresPackingSlipHeadline1 = $transresPackingSlipHeadline1;
    }

    /**
     * @return mixed
     */
    public function getTransresPackingSlipHeadline2()
    {
        return $this->transresPackingSlipHeadline2;
    }

    /**
     * @param mixed $transresPackingSlipHeadline2
     */
    public function setTransresPackingSlipHeadline2($transresPackingSlipHeadline2)
    {
        $this->transresPackingSlipHeadline2 = $transresPackingSlipHeadline2;
    }

    /**
     * @return mixed
     */
    public function getTransresPackingSlipHeadlineColor()
    {
        return $this->transresPackingSlipHeadlineColor;
    }

    /**
     * @param mixed $transresPackingSlipHeadlineColor
     */
    public function setTransresPackingSlipHeadlineColor($transresPackingSlipHeadlineColor)
    {
        $this->transresPackingSlipHeadlineColor = $transresPackingSlipHeadlineColor;
    }

    /**
     * @return mixed
     */
    public function getTransresPackingSlipHighlightedColor()
    {
        return $this->transresPackingSlipHighlightedColor;
    }

    /**
     * @param mixed $transresPackingSlipHighlightedColor
     */
    public function setTransresPackingSlipHighlightedColor($transresPackingSlipHighlightedColor)
    {
        $this->transresPackingSlipHighlightedColor = $transresPackingSlipHighlightedColor;
    }

    /**
     * @return mixed
     */
    public function getTransresPackingSlipSubHeading1()
    {
        return $this->transresPackingSlipSubHeading1;
    }

    /**
     * @param mixed $transresPackingSlipSubHeading1
     */
    public function setTransresPackingSlipSubHeading1($transresPackingSlipSubHeading1)
    {
        $this->transresPackingSlipSubHeading1 = $transresPackingSlipSubHeading1;
    }

    /**
     * @return mixed
     */
    public function getTransresPackingSlipSubHeading2()
    {
        return $this->transresPackingSlipSubHeading2;
    }

    /**
     * @param mixed $transresPackingSlipSubHeading2
     */
    public function setTransresPackingSlipSubHeading2($transresPackingSlipSubHeading2)
    {
        $this->transresPackingSlipSubHeading2 = $transresPackingSlipSubHeading2;
    }

    /**
     * @return mixed
     */
    public function getTransresPackingSlipFooter1()
    {
        return $this->transresPackingSlipFooter1;
    }

    /**
     * @param mixed $transresPackingSlipFooter1
     */
    public function setTransresPackingSlipFooter1($transresPackingSlipFooter1)
    {
        $this->transresPackingSlipFooter1 = $transresPackingSlipFooter1;
    }

    /**
     * @return mixed
     */
    public function getTransresPackingSlipFooter2()
    {
        return $this->transresPackingSlipFooter2;
    }

    /**
     * @param mixed $transresPackingSlipFooter2
     */
    public function setTransresPackingSlipFooter2($transresPackingSlipFooter2)
    {
        $this->transresPackingSlipFooter2 = $transresPackingSlipFooter2;
    }

    /**
     * @return mixed
     */
    public function getBarcodeSize()
    {
        return $this->barcodeSize;
    }

    /**
     * @param mixed $barcodeSize
     */
    public function setBarcodeSize($barcodeSize)
    {
        $this->barcodeSize = $barcodeSize;
    }

    /**
     * @return mixed
     */
    public function getTransresPackingSlipFontSize()
    {
        return $this->transresPackingSlipFontSize;
    }

    /**
     * @param mixed $transresPackingSlipFontSize
     */
    public function setTransresPackingSlipFontSize($transresPackingSlipFontSize)
    {
        $this->transresPackingSlipFontSize = $transresPackingSlipFontSize;
    }

    /**
     * @return mixed
     */
    public function getSpecimenDetailsComment()
    {
        return $this->specimenDetailsComment;
    }

    /**
     * @param mixed $specimenDetailsComment
     */
    public function setSpecimenDetailsComment($specimenDetailsComment)
    {
        $this->specimenDetailsComment = $specimenDetailsComment;
    }

    /**
     * @return mixed
     */
    public function getInstitutionName()
    {
        return $this->institutionName;
    }

    /**
     * @param mixed $institutionName
     */
    public function setInstitutionName($institutionName)
    {
        $this->institutionName = $institutionName;
    }

    /**
     * @return mixed
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @param mixed $fromEmail
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
    }

    /**
     * @return mixed
     */
    public function getNotifyEmail()
    {
        return $this->notifyEmail;
    }

    /**
     * @param mixed $notifyEmail
     */
    public function setNotifyEmail($notifyEmail)
    {
        $this->notifyEmail = $notifyEmail;
    }

    /**
     * @return mixed
     */
    public function getEmailNoteConcern()
    {
        return $this->emailNoteConcern;
    }

    /**
     * @param mixed $emailNoteConcern
     */
    public function setEmailNoteConcern($emailNoteConcern)
    {
        $this->emailNoteConcern = $emailNoteConcern;
    }

    /**
     * @return mixed
     */
    public function getInvoiceReminderSchedule()
    {
        return $this->invoiceReminderSchedule;
    }

    /**
     * @param mixed $invoiceReminderSchedule
     */
    public function setInvoiceReminderSchedule($invoiceReminderSchedule)
    {
        $this->invoiceReminderSchedule = $invoiceReminderSchedule;
    }

    /**
     * @return mixed
     */
    public function getInvoiceReminderSubject()
    {
        return $this->invoiceReminderSubject;
    }

    /**
     * @param mixed $invoiceReminderSubject
     */
    public function setInvoiceReminderSubject($invoiceReminderSubject)
    {
        $this->invoiceReminderSubject = $invoiceReminderSubject;
    }

    /**
     * @return mixed
     */
    public function getInvoiceReminderBody()
    {
        return $this->invoiceReminderBody;
    }

    /**
     * @param mixed $invoiceReminderBody
     */
    public function setInvoiceReminderBody($invoiceReminderBody)
    {
        $this->invoiceReminderBody = $invoiceReminderBody;
    }

    /**
     * @return mixed
     */
    public function getInvoiceReminderEmail()
    {
        return $this->invoiceReminderEmail;
    }

    /**
     * @param mixed $invoiceReminderEmail
     */
    public function setInvoiceReminderEmail($invoiceReminderEmail)
    {
        $this->invoiceReminderEmail = $invoiceReminderEmail;
    }

    
    /**
     * @return mixed
     */
    public function getProjectReminderDelayIrbReview()
    {
        return $this->projectReminderDelay_irb_review;
    }

    /**
     * @param mixed $projectReminderDelay_irb_review
     */
    public function setProjectReminderDelayIrbReview($projectReminderDelay_irb_review)
    {
        $this->projectReminderDelay_irb_review = $projectReminderDelay_irb_review;
    }

    /**
     * @return mixed
     */
    public function getProjectReminderDelayAdminReview()
    {
        return $this->projectReminderDelay_admin_review;
    }

    /**
     * @param mixed $projectReminderDelay_admin_review
     */
    public function setProjectReminderDelayAdminReview($projectReminderDelay_admin_review)
    {
        $this->projectReminderDelay_admin_review = $projectReminderDelay_admin_review;
    }

    /**
     * @return mixed
     */
    public function getProjectReminderDelayCommitteeReview()
    {
        return $this->projectReminderDelay_committee_review;
    }

    /**
     * @param mixed $projectReminderDelay_committee_review
     */
    public function setProjectReminderDelayCommitteeReview($projectReminderDelay_committee_review)
    {
        $this->projectReminderDelay_committee_review = $projectReminderDelay_committee_review;
    }

    /**
     * @return mixed
     */
    public function getProjectReminderDelayFinalReview()
    {
        return $this->projectReminderDelay_final_review;
    }

    /**
     * @param mixed $projectReminderDelay_final_review
     */
    public function setProjectReminderDelayFinalReview($projectReminderDelay_final_review)
    {
        $this->projectReminderDelay_final_review = $projectReminderDelay_final_review;
    }

    /**
     * @return mixed
     */
    public function getProjectReminderDelayIrbMissinginfo()
    {
        return $this->projectReminderDelay_irb_missinginfo;
    }

    /**
     * @param mixed $projectReminderDelay_irb_missinginfo
     */
    public function setProjectReminderDelayIrbMissinginfo($projectReminderDelay_irb_missinginfo)
    {
        $this->projectReminderDelay_irb_missinginfo = $projectReminderDelay_irb_missinginfo;
    }

    /**
     * @return mixed
     */
    public function getProjectReminderDelayAdminMissinginfo()
    {
        return $this->projectReminderDelay_admin_missinginfo;
    }

    /**
     * @param mixed $projectReminderDelay_admin_missinginfo
     */
    public function setProjectReminderDelayAdminMissinginfo($projectReminderDelay_admin_missinginfo)
    {
        $this->projectReminderDelay_admin_missinginfo = $projectReminderDelay_admin_missinginfo;
    }

    /**
     * @return mixed
     */
    public function getProjectReminderSubjectReview()
    {
        return $this->projectReminderSubject_review;
    }

    /**
     * @param mixed $projectReminderSubject_review
     */
    public function setProjectReminderSubjectReview($projectReminderSubject_review)
    {
        $this->projectReminderSubject_review = $projectReminderSubject_review;
    }

    /**
     * @return mixed
     */
    public function getProjectReminderBodyReview()
    {
        return $this->projectReminderBody_review;
    }

    /**
     * @param mixed $projectReminderBody_review
     */
    public function setProjectReminderBodyReview($projectReminderBody_review)
    {
        $this->projectReminderBody_review = $projectReminderBody_review;
    }

    /**
     * @return mixed
     */
    public function getProjectReminderSubjectMissinginfo()
    {
        return $this->projectReminderSubject_missinginfo;
    }

    /**
     * @param mixed $projectReminderSubject_missinginfo
     */
    public function setProjectReminderSubjectMissinginfo($projectReminderSubject_missinginfo)
    {
        $this->projectReminderSubject_missinginfo = $projectReminderSubject_missinginfo;
    }

    /**
     * @return mixed
     */
    public function getProjectReminderBodyMissinginfo()
    {
        return $this->projectReminderBody_missinginfo;
    }

    /**
     * @param mixed $projectReminderBody_missinginfo
     */
    public function setProjectReminderBodyMissinginfo($projectReminderBody_missinginfo)
    {
        $this->projectReminderBody_missinginfo = $projectReminderBody_missinginfo;
    }

    /**
     * @return mixed
     */
    public function getPendingRequestReminderDelay()
    {
        return $this->pendingRequestReminderDelay;
    }

    /**
     * @param mixed $pendingRequestReminderDelay
     */
    public function setPendingRequestReminderDelay($pendingRequestReminderDelay)
    {
        $this->pendingRequestReminderDelay = $pendingRequestReminderDelay;
    }

    /**
     * @return mixed
     */
    public function getPendingRequestReminderSubject()
    {
        return $this->pendingRequestReminderSubject;
    }

    /**
     * @param mixed $pendingRequestReminderSubject
     */
    public function setPendingRequestReminderSubject($pendingRequestReminderSubject)
    {
        $this->pendingRequestReminderSubject = $pendingRequestReminderSubject;
    }

    /**
     * @return mixed
     */
    public function getPendingRequestReminderBody()
    {
        return $this->pendingRequestReminderBody;
    }

    /**
     * @param mixed $pendingRequestReminderBody
     */
    public function setPendingRequestReminderBody($pendingRequestReminderBody)
    {
        $this->pendingRequestReminderBody = $pendingRequestReminderBody;
    }

    /**
     * @return mixed
     */
    public function getCompletedRequestReminderDelay()
    {
        return $this->completedRequestReminderDelay;
    }

    /**
     * @param mixed $completedRequestReminderDelay
     */
    public function setCompletedRequestReminderDelay($completedRequestReminderDelay)
    {
        $this->completedRequestReminderDelay = $completedRequestReminderDelay;
    }

    /**
     * @return mixed
     */
    public function getCompletedRequestReminderSubject()
    {
        return $this->completedRequestReminderSubject;
    }

    /**
     * @param mixed $completedRequestReminderSubject
     */
    public function setCompletedRequestReminderSubject($completedRequestReminderSubject)
    {
        $this->completedRequestReminderSubject = $completedRequestReminderSubject;
    }

    /**
     * @return mixed
     */
    public function getCompletedRequestReminderBody()
    {
        return $this->completedRequestReminderBody;
    }

    /**
     * @param mixed $completedRequestReminderBody
     */
    public function setCompletedRequestReminderBody($completedRequestReminderBody)
    {
        $this->completedRequestReminderBody = $completedRequestReminderBody;
    }

    /**
     * @return mixed
     */
    public function getCompletedNoInvoiceRequestReminderDelay()
    {
        return $this->completedNoInvoiceRequestReminderDelay;
    }

    /**
     * @param mixed $completedNoInvoiceRequestReminderDelay
     */
    public function setCompletedNoInvoiceRequestReminderDelay($completedNoInvoiceRequestReminderDelay)
    {
        $this->completedNoInvoiceRequestReminderDelay = $completedNoInvoiceRequestReminderDelay;
    }

    /**
     * @return mixed
     */
    public function getCompletedNoInvoiceRequestReminderSubject()
    {
        return $this->completedNoInvoiceRequestReminderSubject;
    }

    /**
     * @param mixed $completedNoInvoiceRequestReminderSubject
     */
    public function setCompletedNoInvoiceRequestReminderSubject($completedNoInvoiceRequestReminderSubject)
    {
        $this->completedNoInvoiceRequestReminderSubject = $completedNoInvoiceRequestReminderSubject;
    }

    /**
     * @return mixed
     */
    public function getCompletedNoInvoiceRequestReminderBody()
    {
        return $this->completedNoInvoiceRequestReminderBody;
    }

    /**
     * @param mixed $completedNoInvoiceRequestReminderBody
     */
    public function setCompletedNoInvoiceRequestReminderBody($completedNoInvoiceRequestReminderBody)
    {
        $this->completedNoInvoiceRequestReminderBody = $completedNoInvoiceRequestReminderBody;
    }






    public function __toString(){
        //return "Site Parameters ID ".$this->getId()." for ".$this->getProjectSpecialty();
        return "Site Parameters for ".$this->getProjectSpecialty()->getName();
    }




}