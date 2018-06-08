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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_siteParameters")
 */
class SiteParameters {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Max idle time in minutes
     * @ORM\Column(type="text", nullable=true)
     */
    private $maxIdleTime;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $environment;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $siteEmail;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $dbServerAddress;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $dbServerPort;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $dbServerAccountUserName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $dbServerAccountPassword;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $dbDatabaseName;

    //////// email (default gmail free SMTP Server Example) //////////
    /**
     * mailerHost: smtp.gmail.com
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $smtpServerAddress;

    /**
     * smtp or gmail (google gmail requires only gmail username and password)
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $mailerTransport;

    /**
     * oauth
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $mailerAuthMode;

    /**
     * tls or ssl
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $mailerUseSecureConnection;

    /**
     * GMail account (email@gmail.com)
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $mailerUser;

    /**
     * GMail password
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $mailerPassword;

    /**
     * 465 or 587
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $mailerPort;

    /**
     * use spooled email
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mailerSpool;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mailerFlushQueueFrequency;

    /**
     * emails will deliver only to these emails
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $mailerDeliveryAddresses;

    //mailer_transport: smtp
    //mailer_user: null
    //mailer_password: null

    //transport: smtp
    //host:      smtp.gmail.com
    //username:     #email@gmail.com
    //password:            #gmail_password
    //    #auth_mode: oauth
    //port:      587
    //encryption: tls
    //////// EOF email (default gmail free SMTP Server Example) //////////

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $aDLDAPServerAddress;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $aDLDAPServerPort;

    /**
     * LDAP bind used for ldap_search or for simple authentication ldap_bind
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $aDLDAPServerOu;

    /**
     * Used for ldap_search, if null, the ldap_search is not used
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $aDLDAPServerAccountUserName;

    /**
     * Used for ldap_search, if null, the ldap_search is not used
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $aDLDAPServerAccountPassword;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ldapExePath;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ldapExeFilename;

    /**
     * Default Primary Public User ID Type
     *
     * @ORM\OneToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\UsernameType")
     */
    private $defaultPrimaryPublicUserIdType;

    /**
     * Enable auto-assignment of Institutional Scope
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enableAutoAssignmentInstitutionalScope;

    /**
     * @ORM\OneToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\Institution")
     */
    private $autoAssignInstitution;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pacsvendorSlideManagerDBServerAddress;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pacsvendorSlideManagerDBServerPort;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pacsvendorSlideManagerDBUserName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pacsvendorSlideManagerDBPassword;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pacsvendorSlideManagerDBName;


    //Footer
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $institutionurl;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $institutionname;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $departmenturl;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $departmentname;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $subinstitutionurl;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $subinstitutionname;

    /**
     * Show copyright line on every footer
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showCopyrightOnFooter;

    //Maintanence mode
    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $maintenance;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $maintenanceenddate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $maintenancelogoutmsg;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $maintenanceloginmsg;

    //uploads path
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $scanuploadpath;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $employeesuploadpath;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $avataruploadpath;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $fellappuploadpath;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $vacrequploadpath;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $transresuploadpath;


    //site titles and messages
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $mainHomeTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $listManagerTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $eventLogTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $siteSettingsTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $contentAboutPage;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $underLoginMsgUser;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $underLoginMsgScan;

    ///////////////////// FELLAPP /////////////////////
    /**
     * Path to the local copy of the fellowship application form
     * https://script.google.com/a/macros/pathologysystems.org/d/14jgVkEBCAFrwuW5Zqiq8jsw37rc4JieHkKrkYz1jyBp_DFFyTjRGKgHj/edit
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $codeGoogleFormFellApp;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $allowPopulateFellApp;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $confirmationSubjectFellApp;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $confirmationBodyFellApp;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $confirmationEmailFellApp;

    /**
     * Client Email to get GoogleSrevice: i.e. '1040591934373-1sjcosdt66bmani0kdrr5qmc5fibmvk5@developer.gserviceaccount.com'
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $clientEmailFellApp;

    /**
     * Path to p12 key file: i.e. /../Util/FellowshipApplication-f1d9f98353e5.p12
     * E:\Program Files (x86)\pacsvendor\pacsname\htdocs\order\scanorder\Scanorders2\src\Oleg\FellAppBundle\Util\FellowshipApplication-f1d9f98353e5.p12
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $p12KeyPathFellApp;

    /**
     * https://www.googleapis.com/auth/drive https://spreadsheets.google.com/feeds
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $googleDriveApiUrlFellApp;

    /**
     * Impersonate user Email: i.e. olegivanov@pathologysystems.org
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $userImpersonateEmailFellApp;

    /**
     * Template Google Spreadsheet ID (1ITacytsUV2yChbfOSVjuBoW4aObSr_xBfpt6m_vab48)
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $templateIdFellApp;

    /**
     * Backup Google Spreadsheet ID (19KlO1oCC88M436JzCa89xGO08MJ1txQNgLeJI0BpNGo)
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $backupFileIdFellApp;

    /**
     * Application Google Drive Folder ID (0B2FwyaXvFk1efmc2VGVHUm5yYjJRWGFYYTF0Z2N6am9iUFVzcTc1OXdoWEl1Vmc0LWdZc0E)
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $folderIdFellApp;

    /**
     * Backup Sheet Last Modified Date
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $backupUpdateDatetimeFellApp;

    /**
     * Local Institution to which every imported application is set: Pathology Fellowship Programs (WCMC)
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $localInstitutionFellApp;

    /**
     * [ checkbox ] Delete successfully imported applications from Google Drive
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleteImportedAplicationsFellApp;

    /**
     * checkbox for "Automatically delete downloaded applications that are older than [X] year(s)
     * (set it at 2) [this is to delete old excel sheets that are downloaded from google drive.
     * Make sure it is functional and Google/Excel sheets containing applications older than
     * the amount of years set by this option is auto-deleted along with the linked downloaded documents.
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleteOldAplicationsFellApp;

    /**
     * Used in checkbox for "Automatically delete downloaded applications that are older than [X] year(s)
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $yearsOldAplicationsFellApp;

    /**
     * Path to spreadsheets: i.e. Spreadsheets
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $spreadsheetsPathFellApp;

    /**
     * Path to upload applicants documents: i.e. FellowshipApplicantUploads
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $applicantsUploadPathFellApp;


    /**
     * Path to upload applicants documents used in ReportGenerator: i.e. Reports
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $reportsUploadPathFellApp;

    /**
     * Link to the Application Page (so the users can click and see how it looks)
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $applicationPageLinkFellApp;

    ////////////////////// third party software //////////////////////////
    /**
     * C:\Program Files (x86)\LibreOffice 5\program
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $libreOfficeConvertToPDFPathFellApp;
    /**
     * path\LibreOffice 5\program
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $libreOfficeConvertToPDFPathFellAppLinux;

    /**
     * soffice
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $libreOfficeConvertToPDFFilenameFellApp;
    /**
     * soffice
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $libreOfficeConvertToPDFFilenameFellAppLinux;

    /**
     * --headless -convert-to pdf -outdir
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $libreOfficeConvertToPDFArgumentsdFellApp;
    /**
     * --headless -convert-to pdf -outdir
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $libreOfficeConvertToPDFArgumentsdFellAppLinux;

    /**
     * C:\Program Files (x86)\pacsvendor\pacsname\htdocs\order\scanorder\Scanorders2\vendor\olegutil\PDFTKBuilderPortable\App\pdftkbuilder
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $pdftkPathFellApp;
    /**
     * path\order\scanorder\Scanorders2\vendor\olegutil\PDFTKBuilderPortable\App\pdftkbuilder
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $pdftkPathFellAppLinux;

    /**
     * pdftk
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $pdftkFilenameFellApp;
    /**
     * pdftk
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $pdftkFilenameFellAppLinux;

    /**
     * ###inputFiles### cat output ###outputFile### dont_ask
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $pdftkArgumentsFellApp;
    /**
     * ###inputFiles### cat output ###outputFile### dont_ask
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $pdftkArgumentsFellAppLinux;

    /**
     * Ghostscript
     * C:\Program Files (x86)\pacsvendor\pacsname\htdocs\order\scanorder\Scanorders2\vendor\olegutil\Ghostscript\bin
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $gsPathFellApp;
    /**
     * Ghostscript
     * path\order\scanorder\Scanorders2\vendor\olegutil\Ghostscript\bin
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $gsPathFellAppLinux;

    /**
     * Ghostscript
     * gswin64c.exe
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $gsFilenameFellApp;
    /**
     * Ghostscript
     * gswin64c.exe
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $gsFilenameFellAppLinux;

    /**
     * Ghostscript
     * -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile= ###outputFile###  -c .setpdfwrite -f ###inputFiles###
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $gsArgumentsFellApp;
    /**
     * Ghostscript
     * -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile= ###outputFile###  -c .setpdfwrite -f ###inputFiles###
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $gsArgumentsFellAppLinux;
    ////////////////////// EOF third party software //////////////////////////
    ///////////////////// EOF FELLAPP /////////////////////

    // Co-Path //
    //Production
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBServerAddress;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBServerPort;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBAccountUserName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBAccountPassword;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $LISName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $LISVersion;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBServerAddressTest;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBServerPortTest;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBAccountUserNameTest;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBAccountPasswordTest;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBNameTest;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $LISNameTest;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $LISVersionTest;


    //Development
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBServerAddressDevelopment;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBServerPortDevelopment;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBAccountUserNameDevelopment;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBAccountPasswordDevelopment;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $coPathDBNameDevelopment;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $LISNameDevelopment;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $LISVersionDevelopment;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $academicYearStart;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $academicYearEnd;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $holidaysUrl;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vacationAccruedDaysPerMonth;

    //Live Site Root URL: http://c.med.cornell.edu/order/
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $liveSiteRootUrl;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enableMetaphone;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pathMetaphone;

    /**
     * Initial Configuration Completed
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $initialConfigurationCompleted;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $networkDrivePath;

    /**
     * Permitted failed log in attempts
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $permittedFailedLoginAttempt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $captchaSiteKey;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $captchaSecretKey;

    /**
     * Enable Captcha at Sign Up
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $captchaEnabled;


    ////////////////////////// LDAP notice messages /////////////////////////
    /**
     * Notice for attempting to reset password for an LDAP-authenticated account.
     * The password for your [[CWID]] can only be changed or reset by visiting the enterprise password management page or by calling the help desk at ‭1 (212) 746-4878:
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $noticeAttemptingPasswordResetLDAP;
    
    /**
     * Notice to prompt user to use Active Directory account to log in:
     * Please use your CWID to log in.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $loginInstruction;

    /**
     * Notice to prompt user with no Active Directory account to sign up for a new account:
     * Sign up for an account if you have no CWID.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $noticeSignUpNoCwid;

    /**
     * Account request question asking whether applicant has an Active Directory account:
     * Do you (the person for whom the account is being requested) have a [CWID] username?
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $noticeHasLdapAccount;

    /**
     * Full local name for active directory account:
     * WCMC CWID
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $noticeLdapName;
    ////////////////////////// EOF LDAP notice messages /////////////////////////


    /////////////// Specific Site Parameters //////////////////////
    /**
     * New User pre-populated. Defaults for an Organizational Group
     * @ORM\OneToMany(targetEntity="OrganizationalGroupDefault", mappedBy="siteParameter", cascade={"persist","remove"})
     */
    private $organizationalGroupDefaults;

    /**
     * Defaults for an Organizational Group
     * @ORM\OneToOne(targetEntity="Oleg\CallLogBundle\Entity\CalllogSiteParameter", cascade={"persist","remove"})
     */
    private $calllogSiteParameter;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $calllogResources;

    /**
     * Navbar Employee List Filter Institution #1: [Dropdown with WCM selected]
     *
     * @ORM\ManyToOne(targetEntity="Institution")
     */
    private $navbarFilterInstitution1;

    /**
     * Navbar Employee List Filter Institution #1: [Dropdown with NYP selected]
     *
     * @ORM\ManyToOne(targetEntity="Institution")
     */
    private $navbarFilterInstitution2;

    /**
     * Default Accession Type for Deidentifier Defaults
     *
     * @ORM\ManyToOne(targetEntity="Oleg\OrderformBundle\Entity\AccessionType")
     */
    private $defaultDeidentifierAccessionType;

    /**
     * Default Accession Type for ScanOrder Type
     *
     * @ORM\ManyToOne(targetEntity="Oleg\OrderformBundle\Entity\AccessionType")
     */
    private $defaultScanAccessionType;

    /**
     * Default Mrn Type for ScanOrder Type
     *
     * @ORM\ManyToOne(targetEntity="Oleg\OrderformBundle\Entity\MrnType")
     */
    private $defaultScanMrnType;

    /**
     * Default Slide Delivery
     *
     * @ORM\ManyToOne(targetEntity="Oleg\OrderformBundle\Entity\OrderDelivery")
     */
    private $defaultScanDelivery;

//    /**
//     * Default Institutional PHI Scope
//     *
//     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\Institution")
//     */
//    private $defaultInstitutionalPHIScope;

    /**
     * Default Organization Recipient
     *
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\Institution")
     */
    private $defaultOrganizationRecipient;

    /**
     * Default Scanner
     *
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\Equipment")
     */
    private $defaultScanner;


    function __construct( $addobjects=true )
    {
        $this->organizationalGroupDefaults = new ArrayCollection();
        $this->setMaintenance(false);
        $this->setShowCopyrightOnFooter(true);
    }



    public function addOrganizationalGroupDefault($item)
    {
        if( $item && !$this->organizationalGroupDefaults->contains($item) ) {
            $this->organizationalGroupDefaults->add($item);
            $item->setSiteParameter($this);
        }

        return $this;
    }
    public function removeOrganizationalGroupDefault($item)
    {
        $this->organizationalGroupDefaults->removeElement($item);
    }
    public function getOrganizationalGroupDefaults()
    {
        return $this->organizationalGroupDefaults;
    }

    /**
     * @param mixed $maxIdleTime
     */
    public function setMaxIdleTime($maxIdleTime)
    {
        $this->maxIdleTime = $maxIdleTime;
    }

    /**
     * @return mixed
     */
    public function getMaxIdleTime()
    {
        return $this->maxIdleTime;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param mixed $aDLDAPServerAccountPassword
     */
    public function setADLDAPServerAccountPassword($aDLDAPServerAccountPassword)
    {
        $this->aDLDAPServerAccountPassword = $aDLDAPServerAccountPassword;
    }

    /**
     * @return mixed
     */
    public function getADLDAPServerAccountPassword()
    {
        return $this->aDLDAPServerAccountPassword;
    }

    /**
     * @param mixed $aDLDAPServerAccountUserName
     */
    public function setADLDAPServerAccountUserName($aDLDAPServerAccountUserName)
    {
        $this->aDLDAPServerAccountUserName = $aDLDAPServerAccountUserName;
    }

    /**
     * @return mixed
     */
    public function getADLDAPServerAccountUserName()
    {
        return $this->aDLDAPServerAccountUserName;
    }

    /**
     * @param mixed $aDLDAPServerAddress
     */
    public function setADLDAPServerAddress($aDLDAPServerAddress)
    {
        $this->aDLDAPServerAddress = $aDLDAPServerAddress;
    }

    /**
     * @return mixed
     */
    public function getADLDAPServerAddress()
    {
        return $this->aDLDAPServerAddress;
    }

    /**
     * @param mixed $aDLDAPServerOu
     */
    public function setADLDAPServerOu($aDLDAPServerOu)
    {
        $this->aDLDAPServerOu = $aDLDAPServerOu;
    }

    /**
     * @return mixed
     */
    public function getADLDAPServerOu()
    {
        return $this->aDLDAPServerOu;
    }

    /**
     * @param mixed $aDLDAPServerPort
     */
    public function setADLDAPServerPort($aDLDAPServerPort)
    {
        $this->aDLDAPServerPort = $aDLDAPServerPort;
    }

    /**
     * @return mixed
     */
    public function getADLDAPServerPort()
    {
        return $this->aDLDAPServerPort;
    }

    /**
     * @param mixed $pacsvendorSlideManagerDBName
     */
    public function setPacsvendorSlideManagerDBName($pacsvendorSlideManagerDBName)
    {
        $this->pacsvendorSlideManagerDBName = $pacsvendorSlideManagerDBName;
    }

    /**
     * @return mixed
     */
    public function getPacsvendorSlideManagerDBName()
    {
        return $this->pacsvendorSlideManagerDBName;
    }

    /**
     * @param mixed $pacsvendorSlideManagerDBPassword
     */
    public function setPacsvendorSlideManagerDBPassword($pacsvendorSlideManagerDBPassword)
    {
        $this->pacsvendorSlideManagerDBPassword = $pacsvendorSlideManagerDBPassword;
    }

    /**
     * @return mixed
     */
    public function getPacsvendorSlideManagerDBPassword()
    {
        return $this->pacsvendorSlideManagerDBPassword;
    }

    /**
     * @param mixed $pacsvendorSlideManagerDBServerAddress
     */
    public function setPacsvendorSlideManagerDBServerAddress($pacsvendorSlideManagerDBServerAddress)
    {
        $this->pacsvendorSlideManagerDBServerAddress = $pacsvendorSlideManagerDBServerAddress;
    }

    /**
     * @return mixed
     */
    public function getPacsvendorSlideManagerDBServerAddress()
    {
        return $this->pacsvendorSlideManagerDBServerAddress;
    }

    /**
     * @param mixed $pacsvendorSlideManagerDBServerPort
     */
    public function setPacsvendorSlideManagerDBServerPort($pacsvendorSlideManagerDBServerPort)
    {
        $this->pacsvendorSlideManagerDBServerPort = $pacsvendorSlideManagerDBServerPort;
    }

    /**
     * @return mixed
     */
    public function getPacsvendorSlideManagerDBServerPort()
    {
        return $this->pacsvendorSlideManagerDBServerPort;
    }

    /**
     * @param mixed $pacsvendorSlideManagerDBUserName
     */
    public function setPacsvendorSlideManagerDBUserName($pacsvendorSlideManagerDBUserName)
    {
        $this->pacsvendorSlideManagerDBUserName = $pacsvendorSlideManagerDBUserName;
    }

    /**
     * @return mixed
     */
    public function getPacsvendorSlideManagerDBUserName()
    {
        return $this->pacsvendorSlideManagerDBUserName;
    }

    /**
     * @param mixed $coPathDBAccountPassword
     */
    public function setCoPathDBAccountPassword($coPathDBAccountPassword)
    {
        $this->coPathDBAccountPassword = $coPathDBAccountPassword;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBAccountPassword()
    {
        return $this->coPathDBAccountPassword;
    }

    /**
     * @param mixed $coPathDBAccountUserName
     */
    public function setCoPathDBAccountUserName($coPathDBAccountUserName)
    {
        $this->coPathDBAccountUserName = $coPathDBAccountUserName;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBAccountUserName()
    {
        return $this->coPathDBAccountUserName;
    }

    /**
     * @param mixed $coPathDBName
     */
    public function setCoPathDBName($coPathDBName)
    {
        $this->coPathDBName = $coPathDBName;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBName()
    {
        return $this->coPathDBName;
    }

    /**
     * @param mixed $coPathDBServerAddress
     */
    public function setCoPathDBServerAddress($coPathDBServerAddress)
    {
        $this->coPathDBServerAddress = $coPathDBServerAddress;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBServerAddress()
    {
        return $this->coPathDBServerAddress;
    }

    /**
     * @param mixed $coPathDBServerPort
     */
    public function setCoPathDBServerPort($coPathDBServerPort)
    {
        $this->coPathDBServerPort = $coPathDBServerPort;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBServerPort()
    {
        return $this->coPathDBServerPort;
    }

    /**
     * @param mixed $LISName
     */
    public function setLISName($LISName)
    {
        $this->LISName = $LISName;
    }

    /**
     * @return mixed
     */
    public function getLISName()
    {
        return $this->LISName;
    }

    /**
     * @param mixed $LISVersion
     */
    public function setLISVersion($LISVersion)
    {
        $this->LISVersion = $LISVersion;
    }

    /**
     * @return mixed
     */
    public function getLISVersion()
    {
        return $this->LISVersion;
    }



    /**
     * @param mixed $dbDatabaseName
     */
    public function setDbDatabaseName($dbDatabaseName)
    {
        $this->dbDatabaseName = $dbDatabaseName;
    }

    /**
     * @return mixed
     */
    public function getDbDatabaseName()
    {
        return $this->dbDatabaseName;
    }

    /**
     * @param mixed $dbServerAccountPassword
     */
    public function setDbServerAccountPassword($dbServerAccountPassword)
    {
        $this->dbServerAccountPassword = $dbServerAccountPassword;
    }

    /**
     * @return mixed
     */
    public function getDbServerAccountPassword()
    {
        return $this->dbServerAccountPassword;
    }

    /**
     * @param mixed $dbServerAccountUserName
     */
    public function setDbServerAccountUserName($dbServerAccountUserName)
    {
        $this->dbServerAccountUserName = $dbServerAccountUserName;
    }

    /**
     * @return mixed
     */
    public function getDbServerAccountUserName()
    {
        return $this->dbServerAccountUserName;
    }

    /**
     * @param mixed $dbServerAddress
     */
    public function setDbServerAddress($dbServerAddress)
    {
        $this->dbServerAddress = $dbServerAddress;
    }

    /**
     * @return mixed
     */
    public function getDbServerAddress()
    {
        return $this->dbServerAddress;
    }

    /**
     * @param mixed $dbServerPort
     */
    public function setDbServerPort($dbServerPort)
    {
        $this->dbServerPort = $dbServerPort;
    }

    /**
     * @return mixed
     */
    public function getDbServerPort()
    {
        return $this->dbServerPort;
    }

    /**
     * @param mixed $siteEmail
     */
    public function setSiteEmail($siteEmail)
    {
        $this->siteEmail = $siteEmail;
    }

    /**
     * @return mixed
     */
    public function getSiteEmail()
    {
        return $this->siteEmail;
    }

    /**
     * @param mixed $smtpServerAddress
     */
    public function setSmtpServerAddress($smtpServerAddress)
    {
        $this->smtpServerAddress = $smtpServerAddress;
    }

    /**
     * @return mixed
     */
    public function getSmtpServerAddress()
    {
        return $this->smtpServerAddress;
    }

    /**
     * @return mixed
     */
    public function getMailerTransport()
    {
        return $this->mailerTransport;
    }

    /**
     * @param mixed $mailerTransport
     */
    public function setMailerTransport($mailerTransport)
    {
        $this->mailerTransport = $mailerTransport;
    }

    /**
     * @return mixed
     */
    public function getMailerAuthMode()
    {
        return $this->mailerAuthMode;
    }

    /**
     * @param mixed $mailerAuthMode
     */
    public function setMailerAuthMode($mailerAuthMode)
    {
        $this->mailerAuthMode = $mailerAuthMode;
    }

    /**
     * @return mixed
     */
    public function getMailerUseSecureConnection()
    {
        return $this->mailerUseSecureConnection;
    }

    /**
     * @param mixed $mailerUseSecureConnection
     */
    public function setMailerUseSecureConnection($mailerUseSecureConnection)
    {
        $this->mailerUseSecureConnection = $mailerUseSecureConnection;
    }

    /**
     * @return mixed
     */
    public function getMailerUser()
    {
        return $this->mailerUser;
    }

    /**
     * @param mixed $mailerUser
     */
    public function setMailerUser($mailerUser)
    {
        $this->mailerUser = $mailerUser;
    }

    /**
     * @return mixed
     */
    public function getMailerPassword()
    {
        return $this->mailerPassword;
    }

    /**
     * @param mixed $mailerPassword
     */
    public function setMailerPassword($mailerPassword)
    {
        $this->mailerPassword = $mailerPassword;
    }

    /**
     * @return mixed
     */
    public function getMailerPort()
    {
        return $this->mailerPort;
    }

    /**
     * @param mixed $mailerPort
     */
    public function setMailerPort($mailerPort)
    {
        $this->mailerPort = $mailerPort;
    }

    /**
     * @return mixed
     */
    public function getMailerSpool()
    {
        return $this->mailerSpool;
    }

    /**
     * @param mixed $mailerSpool
     */
    public function setMailerSpool($mailerSpool)
    {
        $this->mailerSpool = $mailerSpool;
    }

    /**
     * @return mixed
     */
    public function getMailerFlushQueueFrequency()
    {
        return $this->mailerFlushQueueFrequency;
    }

    /**
     * @param mixed $mailerFlushQueueFrequency
     */
    public function setMailerFlushQueueFrequency($mailerFlushQueueFrequency)
    {
        $this->mailerFlushQueueFrequency = $mailerFlushQueueFrequency;
    }

    /**
     * @return mixed
     */
    public function getMailerDeliveryAddresses()
    {
        return $this->mailerDeliveryAddresses;
    }

    /**
     * @param mixed $mailerDeliveryAddresses
     */
    public function setMailerDeliveryAddresses($mailerDeliveryAddresses)
    {
        $this->mailerDeliveryAddresses = $mailerDeliveryAddresses;
    }

    /**
     * @param mixed $autoAssignInstitution
     */
    public function setAutoAssignInstitution($autoAssignInstitution)
    {
        $this->autoAssignInstitution = $autoAssignInstitution;
    }

    /**
     * @return mixed
     */
    public function getAutoAssignInstitution()
    {
        return $this->autoAssignInstitution;
    }

    /**
     * @return mixed
     */
    public function getEnableAutoAssignmentInstitutionalScope()
    {
        return $this->enableAutoAssignmentInstitutionalScope;
    }

    /**
     * @param mixed $enableAutoAssignmentInstitutionalScope
     */
    public function setEnableAutoAssignmentInstitutionalScope($enableAutoAssignmentInstitutionalScope)
    {
        $this->enableAutoAssignmentInstitutionalScope = $enableAutoAssignmentInstitutionalScope;
    }

    /**
     * @param mixed $departmentname
     */
    public function setDepartmentname($departmentname)
    {
        $this->departmentname = $departmentname;
    }

    /**
     * @return mixed
     */
    public function getDepartmentname()
    {
        return $this->departmentname;
    }

    /**
     * @param mixed $departmenturl
     */
    public function setDepartmenturl($departmenturl)
    {
        $this->departmenturl = $departmenturl;
    }

    /**
     * @return mixed
     */
    public function getDepartmenturl()
    {
        return $this->departmenturl;
    }

    /**
     * @param mixed $institutionname
     */
    public function setInstitutionname($institutionname)
    {
        $this->institutionname = $institutionname;
    }

    /**
     * @return mixed
     */
    public function getInstitutionname()
    {
        return $this->institutionname;
    }

    /**
     * @param mixed $institutionurl
     */
    public function setInstitutionurl($institutionurl)
    {
        $this->institutionurl = $institutionurl;
    }

    /**
     * @return mixed
     */
    public function getInstitutionurl()
    {
        return $this->institutionurl;
    }

    /**
     * @return mixed
     */
    public function getSubinstitutionurl()
    {
        return $this->subinstitutionurl;
    }

    /**
     * @param mixed $subinstitutionurl
     */
    public function setSubinstitutionurl($subinstitutionurl)
    {
        $this->subinstitutionurl = $subinstitutionurl;
    }

    /**
     * @return mixed
     */
    public function getSubinstitutionname()
    {
        return $this->subinstitutionname;
    }

    /**
     * @param mixed $subinstitutionname
     */
    public function setSubinstitutionname($subinstitutionname)
    {
        $this->subinstitutionname = $subinstitutionname;
    }

    /**
     * @return mixed
     */
    public function getShowCopyrightOnFooter()
    {
        return $this->showCopyrightOnFooter;
    }

    /**
     * @param mixed $showCopyrightOnFooter
     */
    public function setShowCopyrightOnFooter($showCopyrightOnFooter)
    {
        $this->showCopyrightOnFooter = $showCopyrightOnFooter;
    }

    /**
     * @param mixed $maintenance
     */
    public function setMaintenance($maintenance)
    {
        $this->maintenance = $maintenance;
    }

    /**
     * @return mixed
     */
    public function getMaintenance()
    {
        return $this->maintenance;
    }

    /**
     * @param mixed $maintenanceenddate
     */
    public function setMaintenanceenddate($maintenanceenddate)
    {
        $this->maintenanceenddate = $maintenanceenddate;
    }

    /**
     * @return mixed
     */
    public function getMaintenanceenddate()
    {
        return $this->maintenanceenddate;
    }

    public function getMaintenanceenddateString() {
        $transformer = new DateTimeToStringTransformer(null,null,'m/d/Y H:i');
        return $transformer->transform($this->maintenanceenddate);
    }

    /**
     * @param mixed $maintenanceloginmsg
     */
    public function setMaintenanceloginmsg($maintenanceloginmsg)
    {
        $this->maintenanceloginmsg = $maintenanceloginmsg;
    }

    /**
     * @return mixed
     */
    public function getMaintenanceloginmsg()
    {
        return $this->maintenanceloginmsg;
    }

    public function getMaintenanceloginmsgWithDate()
    {
        $msg = str_replace("[[datetime]]", $this->getUntilDate(), $this->getMaintenanceloginmsg());
        return $msg;
    }

    public function getUntilDate() {

        $transformer = new DateTimeToStringTransformer(null,"America/New_York",'m/d/Y H:i');
        $now = new \DateTime('now');
        $nowStr = $transformer->transform($now);

        $transformer = new DateTimeToStringTransformer(null,null,'m/d/Y H:i');
        $maint = $this->getMaintenanceenddate();
        $maintStr = $transformer->transform($maint);

        //echo "maint=".$maintStr.", now=".$nowStr."<br>";

        $now_time = strtotime($nowStr);
        $maint_time = strtotime($maintStr);

        //echo "maint=".$maint_time.", now=".$now_time."<br>";

        if( !$this->getMaintenanceenddate() || $maint_time < $now_time ) {
            $untilDate = date_modify( $now, '+1 hour' );
            $transformer = new DateTimeToStringTransformer(null,"America/New_York",'m/d/Y H:i');
            $untilDateStr = $transformer->transform($untilDate);
        } else {
            $untilDateStr = $this->getMaintenanceenddateString();
        }

        return $untilDateStr;
    }

    /**
     * @param mixed $maintenancelogoutmsg
     */
    public function setMaintenancelogoutmsg($maintenancelogoutmsg)
    {
        $this->maintenancelogoutmsg = $maintenancelogoutmsg;
    }

    /**
     * @return mixed
     */
    public function getMaintenancelogoutmsg()
    {
        return $this->maintenancelogoutmsg;
    }
    public function getMaintenancelogoutmsgWithDate()
    {
        $msg = str_replace("[[datetime]]", $this->getUntilDate(), $this->getMaintenancelogoutmsg());
        return $msg;
    }

    /**
     * @param mixed $employeesuploadpath
     */
    public function setEmployeesuploadpath($employeesuploadpath)
    {
        $this->employeesuploadpath = $employeesuploadpath;
    }

    /**
     * @return mixed
     */
    public function getEmployeesuploadpath()
    {
        return $this->employeesuploadpath;
    }

    /**
     * @param mixed $scanuploadpath
     */
    public function setScanuploadpath($scanuploadpath)
    {
        $this->scanuploadpath = $scanuploadpath;
    }

    /**
     * @return mixed
     */
    public function getScanuploadpath()
    {
        return $this->scanuploadpath;
    }

    /**
     * @param mixed $fellappuploadpath
     */
    public function setFellappuploadpath($fellappuploadpath)
    {
        $this->fellappuploadpath = $fellappuploadpath;
    }

    /**
     * @return mixed
     */
    public function getFellappuploadpath()
    {
        return $this->fellappuploadpath;
    }

    /**
     * @param mixed $avataruploadpath
     */
    public function setAvataruploadpath($avataruploadpath)
    {
        $this->avataruploadpath = $avataruploadpath;
    }

    /**
     * @return mixed
     */
    public function getAvataruploadpath()
    {
        return $this->avataruploadpath;
    }

    /**
     * @param mixed $listManagerTitle
     */
    public function setListManagerTitle($listManagerTitle)
    {
        $this->listManagerTitle = $listManagerTitle;
    }

    /**
     * @return mixed
     */
    public function getListManagerTitle()
    {
        return $this->listManagerTitle;
    }

    /**
     * @param mixed $mainHomeTitle
     */
    public function setMainHomeTitle($mainHomeTitle)
    {
        $this->mainHomeTitle = $mainHomeTitle;
    }

    /**
     * @return mixed
     */
    public function getMainHomeTitle()
    {
        return $this->mainHomeTitle;
    }

    /**
     * @param mixed $eventLogTitle
     */
    public function setEventLogTitle($eventLogTitle)
    {
        $this->eventLogTitle = $eventLogTitle;
    }

    /**
     * @return mixed
     */
    public function getEventLogTitle()
    {
        return $this->eventLogTitle;
    }

    /**
     * @param mixed $siteSettingsTitle
     */
    public function setSiteSettingsTitle($siteSettingsTitle)
    {
        $this->siteSettingsTitle = $siteSettingsTitle;
    }

    /**
     * @return mixed
     */
    public function getSiteSettingsTitle()
    {
        return $this->siteSettingsTitle;
    }

    /**
     * @param mixed $contentAboutPage
     */
    public function setContentAboutPage($contentAboutPage)
    {
        $this->contentAboutPage = $contentAboutPage;
    }

    /**
     * @return mixed
     */
    public function getContentAboutPage()
    {
        return $this->contentAboutPage;
    }

    /**
     * @param mixed $underLoginMsgScan
     */
    public function setUnderLoginMsgScan($underLoginMsgScan)
    {
        $this->underLoginMsgScan = $underLoginMsgScan;
    }

    /**
     * @return mixed
     */
    public function getUnderLoginMsgScan()
    {
        return $this->underLoginMsgScan;
    }

    /**
     * @param mixed $underLoginMsgUser
     */
    public function setUnderLoginMsgUser($underLoginMsgUser)
    {
        $this->underLoginMsgUser = $underLoginMsgUser;
    }

    /**
     * @return mixed
     */
    public function getUnderLoginMsgUser()
    {
        return $this->underLoginMsgUser;
    }

    /**
     * @param mixed $ldapExeFilename
     */
    public function setLdapExeFilename($ldapExeFilename)
    {
        $this->ldapExeFilename = $ldapExeFilename;
    }

    /**
     * @return mixed
     */
    public function getLdapExeFilename()
    {
        return $this->ldapExeFilename;
    }

    /**
     * @return mixed
     */
    public function getDefaultPrimaryPublicUserIdType()
    {
        return $this->defaultPrimaryPublicUserIdType;
    }

    /**
     * @param mixed $defaultPrimaryPublicUserIdType
     */
    public function setDefaultPrimaryPublicUserIdType($defaultPrimaryPublicUserIdType)
    {
        $this->defaultPrimaryPublicUserIdType = $defaultPrimaryPublicUserIdType;
    }

    /**
     * @param mixed $ldapExePath
     */
    public function setLdapExePath($ldapExePath)
    {
        $this->ldapExePath = $ldapExePath;
    }

    /**
     * @return mixed
     */
    public function getLdapExePath()
    {
        return $this->ldapExePath;
    }

    /**
     * @param mixed $allowPopulateFellApp
     */
    public function setAllowPopulateFellApp($allowPopulateFellApp)
    {
        $this->allowPopulateFellApp = $allowPopulateFellApp;
    }

    /**
     * @return mixed
     */
    public function getAllowPopulateFellApp()
    {
        return $this->allowPopulateFellApp;
    }

    public function getConfirmationSubjectFellApp() {
        return $this->confirmationSubjectFellApp;
    }

    public function setConfirmationSubjectFellApp($confirmationSubjectFellApp) {
        $this->confirmationSubjectFellApp = $confirmationSubjectFellApp;
    }
    
    public function getConfirmationBodyFellApp() {
        return $this->confirmationBodyFellApp;
    }

    public function setConfirmationBodyFellApp($confirmationBodyFellApp) {
        $this->confirmationBodyFellApp = $confirmationBodyFellApp;
    }

    public function getConfirmationEmailFellApp() {
        return $this->confirmationEmailFellApp;
    }

    public function setConfirmationEmailFellApp($confirmationEmailFellApp) {
        $this->confirmationEmailFellApp = $confirmationEmailFellApp;
    }

    /**
     * @param mixed $LISNameDevelopment
     */
    public function setLISNameDevelopment($LISNameDevelopment)
    {
        $this->LISNameDevelopment = $LISNameDevelopment;
    }

    /**
     * @return mixed
     */
    public function getLISNameDevelopment()
    {
        return $this->LISNameDevelopment;
    }

    /**
     * @param mixed $LISNameTest
     */
    public function setLISNameTest($LISNameTest)
    {
        $this->LISNameTest = $LISNameTest;
    }

    /**
     * @return mixed
     */
    public function getLISNameTest()
    {
        return $this->LISNameTest;
    }

    /**
     * @param mixed $LISVersionDevelopment
     */
    public function setLISVersionDevelopment($LISVersionDevelopment)
    {
        $this->LISVersionDevelopment = $LISVersionDevelopment;
    }

    /**
     * @return mixed
     */
    public function getLISVersionDevelopment()
    {
        return $this->LISVersionDevelopment;
    }

    /**
     * @param mixed $LISVersionTest
     */
    public function setLISVersionTest($LISVersionTest)
    {
        $this->LISVersionTest = $LISVersionTest;
    }

    /**
     * @return mixed
     */
    public function getLISVersionTest()
    {
        return $this->LISVersionTest;
    }

    /**
     * @param mixed $coPathDBAccountPasswordDevelopment
     */
    public function setCoPathDBAccountPasswordDevelopment($coPathDBAccountPasswordDevelopment)
    {
        $this->coPathDBAccountPasswordDevelopment = $coPathDBAccountPasswordDevelopment;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBAccountPasswordDevelopment()
    {
        return $this->coPathDBAccountPasswordDevelopment;
    }

    /**
     * @param mixed $coPathDBAccountPasswordTest
     */
    public function setCoPathDBAccountPasswordTest($coPathDBAccountPasswordTest)
    {
        $this->coPathDBAccountPasswordTest = $coPathDBAccountPasswordTest;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBAccountPasswordTest()
    {
        return $this->coPathDBAccountPasswordTest;
    }

    /**
     * @param mixed $coPathDBAccountUserNameDevelopment
     */
    public function setCoPathDBAccountUserNameDevelopment($coPathDBAccountUserNameDevelopment)
    {
        $this->coPathDBAccountUserNameDevelopment = $coPathDBAccountUserNameDevelopment;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBAccountUserNameDevelopment()
    {
        return $this->coPathDBAccountUserNameDevelopment;
    }

    /**
     * @param mixed $coPathDBAccountUserNameTest
     */
    public function setCoPathDBAccountUserNameTest($coPathDBAccountUserNameTest)
    {
        $this->coPathDBAccountUserNameTest = $coPathDBAccountUserNameTest;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBAccountUserNameTest()
    {
        return $this->coPathDBAccountUserNameTest;
    }

    /**
     * @param mixed $coPathDBNameDevelopment
     */
    public function setCoPathDBNameDevelopment($coPathDBNameDevelopment)
    {
        $this->coPathDBNameDevelopment = $coPathDBNameDevelopment;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBNameDevelopment()
    {
        return $this->coPathDBNameDevelopment;
    }

    /**
     * @param mixed $coPathDBNameTest
     */
    public function setCoPathDBNameTest($coPathDBNameTest)
    {
        $this->coPathDBNameTest = $coPathDBNameTest;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBNameTest()
    {
        return $this->coPathDBNameTest;
    }

    /**
     * @param mixed $coPathDBServerAddressDevelopment
     */
    public function setCoPathDBServerAddressDevelopment($coPathDBServerAddressDevelopment)
    {
        $this->coPathDBServerAddressDevelopment = $coPathDBServerAddressDevelopment;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBServerAddressDevelopment()
    {
        return $this->coPathDBServerAddressDevelopment;
    }

    /**
     * @param mixed $coPathDBServerAddressTest
     */
    public function setCoPathDBServerAddressTest($coPathDBServerAddressTest)
    {
        $this->coPathDBServerAddressTest = $coPathDBServerAddressTest;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBServerAddressTest()
    {
        return $this->coPathDBServerAddressTest;
    }

    /**
     * @param mixed $coPathDBServerPortDevelopment
     */
    public function setCoPathDBServerPortDevelopment($coPathDBServerPortDevelopment)
    {
        $this->coPathDBServerPortDevelopment = $coPathDBServerPortDevelopment;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBServerPortDevelopment()
    {
        return $this->coPathDBServerPortDevelopment;
    }

    /**
     * @param mixed $coPathDBServerPortTest
     */
    public function setCoPathDBServerPortTest($coPathDBServerPortTest)
    {
        $this->coPathDBServerPortTest = $coPathDBServerPortTest;
    }

    /**
     * @return mixed
     */
    public function getCoPathDBServerPortTest()
    {
        return $this->coPathDBServerPortTest;
    }

    /**
     * @return mixed
     */
    public function getClientEmailFellApp()
    {
        return $this->clientEmailFellApp;
    }

    /**
     * @param mixed $clientEmailFellApp
     */
    public function setClientEmailFellApp($clientEmailFellApp)
    {
        $this->clientEmailFellApp = $clientEmailFellApp;
    }

    /**
     * @return mixed
     */
    public function getP12KeyPathFellApp()
    {
        return $this->p12KeyPathFellApp;
    }

    /**
     * @param mixed $p12KeyPathFellApp
     */
    public function setP12KeyPathFellApp($p12KeyPathFellApp)
    {
        $this->p12KeyPathFellApp = $p12KeyPathFellApp;
    }

    /**
     * @return mixed
     */
    public function getUserImpersonateEmailFellApp()
    {
        return $this->userImpersonateEmailFellApp;
    }

    /**
     * @param mixed $userImpersonateEmailFellApp
     */
    public function setUserImpersonateEmailFellApp($userImpersonateEmailFellApp)
    {
        $this->userImpersonateEmailFellApp = $userImpersonateEmailFellApp;
    }


    /**
     * @return mixed
     */
    public function getLocalInstitutionFellApp()
    {
        return $this->localInstitutionFellApp;
    }

    /**
     * @param mixed $localInstitutionFellApp
     */
    public function setLocalInstitutionFellApp($localInstitutionFellApp)
    {
        $this->localInstitutionFellApp = $localInstitutionFellApp;
    }

    /**
     * @return mixed
     */
    public function getDeleteImportedAplicationsFellApp()
    {
        return $this->deleteImportedAplicationsFellApp;
    }

    /**
     * @param mixed $deleteImportedAplicationsFellApp
     */
    public function setDeleteImportedAplicationsFellApp($deleteImportedAplicationsFellApp)
    {
        $this->deleteImportedAplicationsFellApp = $deleteImportedAplicationsFellApp;
    }

    /**
     * @return mixed
     */
    public function getDeleteOldAplicationsFellApp()
    {
        return $this->deleteOldAplicationsFellApp;
    }

    /**
     * @param mixed $deleteOldAplicationsFellApp
     */
    public function setDeleteOldAplicationsFellApp($deleteOldAplicationsFellApp)
    {
        $this->deleteOldAplicationsFellApp = $deleteOldAplicationsFellApp;
    }

    /**
     * @return mixed
     */
    public function getSpreadsheetsPathFellApp()
    {
        return $this->spreadsheetsPathFellApp;
    }

    /**
     * @param mixed $spreadsheetsPathFellApp
     */
    public function setSpreadsheetsPathFellApp($spreadsheetsPathFellApp)
    {
        $this->spreadsheetsPathFellApp = $spreadsheetsPathFellApp;
    }

    /**
     * @return mixed
     */
    public function getApplicantsUploadPathFellApp()
    {
        return $this->applicantsUploadPathFellApp;
    }

    /**
     * @param mixed $applicantsUploadPathFellApp
     */
    public function setApplicantsUploadPathFellApp($applicantsUploadPathFellApp)
    {
        $this->applicantsUploadPathFellApp = $applicantsUploadPathFellApp;
    }

    /**
     * @return mixed
     */
    public function getYearsOldAplicationsFellApp()
    {
        return $this->yearsOldAplicationsFellApp;
    }

    /**
     * @param mixed $yearsOldAplicationsFellApp
     */
    public function setYearsOldAplicationsFellApp($yearsOldAplicationsFellApp)
    {
        $this->yearsOldAplicationsFellApp = $yearsOldAplicationsFellApp;
    }

    /**
     * @return mixed
     */
    public function getApplicationPageLinkFellApp()
    {
        return $this->applicationPageLinkFellApp;
    }

    /**
     * @param mixed $applicationPageLinkFellApp
     */
    public function setApplicationPageLinkFellApp($applicationPageLinkFellApp)
    {
        $this->applicationPageLinkFellApp = $applicationPageLinkFellApp;
    }

    /**
     * @return mixed
     */
    public function getReportsUploadPathFellApp()
    {
        return $this->reportsUploadPathFellApp;
    }

    /**
     * @param mixed $reportsUploadPathFellApp
     */
    public function setReportsUploadPathFellApp($reportsUploadPathFellApp)
    {
        $this->reportsUploadPathFellApp = $reportsUploadPathFellApp;
    }

    /**
     * @return mixed
     */
    public function getGoogleDriveApiUrlFellApp()
    {
        return $this->googleDriveApiUrlFellApp;
    }

    /**
     * @param mixed $googleDriveApiUrlFellApp
     */
    public function setGoogleDriveApiUrlFellApp($googleDriveApiUrlFellApp)
    {
        $this->googleDriveApiUrlFellApp = $googleDriveApiUrlFellApp;
    }

    /**
     * @return mixed
     */
    public function getCodeGoogleFormFellApp()
    {
        return $this->codeGoogleFormFellApp;
    }

    /**
     * @param mixed $codeGoogleFormFellApp
     */
    public function setCodeGoogleFormFellApp($codeGoogleFormFellApp)
    {
        $this->codeGoogleFormFellApp = $codeGoogleFormFellApp;
    }

    /**
     * @return mixed
     */
    public function getTemplateIdFellApp()
    {
        return $this->templateIdFellApp;
    }

    /**
     * @param mixed $templateIdFellApp
     */
    public function setTemplateIdFellApp($templateIdFellApp)
    {
        $this->templateIdFellApp = $templateIdFellApp;
    }

    /**
     * @return mixed
     */
    public function getBackupFileIdFellApp()
    {
        return $this->backupFileIdFellApp;
    }

    /**
     * @param mixed $backupFileIdFellApp
     */
    public function setBackupFileIdFellApp($backupFileIdFellApp)
    {
        $this->backupFileIdFellApp = $backupFileIdFellApp;
    }

    /**
     * @return mixed
     */
    public function getFolderIdFellApp()
    {
        return $this->folderIdFellApp;
    }

    /**
     * @param mixed $folderIdFellApp
     */
    public function setFolderIdFellApp($folderIdFellApp)
    {
        $this->folderIdFellApp = $folderIdFellApp;
    }

    /**
     * @return mixed
     */
    public function getBackupUpdateDatetimeFellApp()
    {
        return $this->backupUpdateDatetimeFellApp;
    }

    /**
     * @param mixed $backupUpdateDatetimeFellApp
     */
    public function setBackupUpdateDatetimeFellApp($backupUpdateDatetimeFellApp)
    {
        $this->backupUpdateDatetimeFellApp = $backupUpdateDatetimeFellApp;
    }

    /**
     * @return mixed
     */
    public function getVacrequploadpath()
    {
        return $this->vacrequploadpath;
    }

    /**
     * @param mixed $vacrequploadpath
     */
    public function setVacrequploadpath($vacrequploadpath)
    {
        $this->vacrequploadpath = $vacrequploadpath;
    }

    /**
     * @return mixed
     */
    public function getTransresuploadpath()
    {
        return $this->transresuploadpath;
    }

    /**
     * @param mixed $transresuploadpath
     */
    public function setTransresuploadpath($transresuploadpath)
    {
        $this->transresuploadpath = $transresuploadpath;
    }


    /**
     * @return mixed
     */
    public function getAcademicYearStart()
    {
        return $this->academicYearStart;
    }

    /**
     * @param mixed $academicYearStart
     */
    public function setAcademicYearStart($academicYearStart)
    {
        $this->academicYearStart = $academicYearStart;
    }

    /**
     * @return mixed
     */
    public function getAcademicYearEnd()
    {
        return $this->academicYearEnd;
    }

    /**
     * @param mixed $academicYearEnd
     */
    public function setAcademicYearEnd($academicYearEnd)
    {
        $this->academicYearEnd = $academicYearEnd;
    }

    /**
     * @return mixed
     */
    public function getHolidaysUrl()
    {
        return $this->holidaysUrl;
    }

    /**
     * @param mixed $holidaysUrl
     */
    public function setHolidaysUrl($holidaysUrl)
    {
        $this->holidaysUrl = $holidaysUrl;
    }

    /**
     * @return mixed
     */
    public function getVacationAccruedDaysPerMonth()
    {
        return $this->vacationAccruedDaysPerMonth;
    }

    /**
     * @param mixed $vacationAccruedDaysPerMonth
     */
    public function setVacationAccruedDaysPerMonth($vacationAccruedDaysPerMonth)
    {
        $this->vacationAccruedDaysPerMonth = $vacationAccruedDaysPerMonth;
    }

    /**
     * @return mixed
     */
    public function getLiveSiteRootUrl()
    {
        return $this->liveSiteRootUrl;
    }

    /**
     * @param mixed $liveSiteRootUrl
     */
    public function setLiveSiteRootUrl($liveSiteRootUrl)
    {
        $this->liveSiteRootUrl = $liveSiteRootUrl;
    }

    /**
     * @return mixed
     */
    public function getEnableMetaphone()
    {
        return $this->enableMetaphone;
    }

    /**
     * @param mixed $enableMetaphone
     */
    public function setEnableMetaphone($enableMetaphone)
    {
        $this->enableMetaphone = $enableMetaphone;
    }

    /**
     * @return mixed
     */
    public function getPathMetaphone()
    {
        return $this->pathMetaphone;
    }

    /**
     * @param mixed $pathMetaphone
     */
    public function setPathMetaphone($pathMetaphone)
    {
        $this->pathMetaphone = $pathMetaphone;
    }

    /**
     * @return mixed
     */
    public function getCalllogResources()
    {
        return $this->calllogResources;
    }

    /**
     * @param mixed $calllogResources
     */
    public function setCalllogResources($calllogResources)
    {
        $this->calllogResources = $calllogResources;
    }

    /**
     * @return mixed
     */
    public function getLoginInstruction()
    {
        return $this->loginInstruction;
    }

    /**
     * @param mixed $loginInstruction
     */
    public function setLoginInstruction($loginInstruction)
    {
        $this->loginInstruction = $loginInstruction;
    }

    /**
     * @return mixed
     */
    public function getInitialConfigurationCompleted()
    {
        return $this->initialConfigurationCompleted;
    }

    /**
     * @param mixed $initialConfigurationCompleted
     */
    public function setInitialConfigurationCompleted($initialConfigurationCompleted)
    {
        $this->initialConfigurationCompleted = $initialConfigurationCompleted;
    }

    ////////////////////// third party software //////////////////////////
    /////////////////////// WINDOWS /////////////////////////
    /**
     * @return mixed
     */
    public function getLibreOfficeConvertToPDFArgumentsdFellApp()
    {
        return $this->libreOfficeConvertToPDFArgumentsdFellApp;
    }

    /**
     * @param mixed $libreOfficeConvertToPDFArgumentsdFellApp
     */
    public function setLibreOfficeConvertToPDFArgumentsdFellApp($libreOfficeConvertToPDFArgumentsdFellApp)
    {
        $this->libreOfficeConvertToPDFArgumentsdFellApp = $libreOfficeConvertToPDFArgumentsdFellApp;
    }

    /**
     * @return mixed
     */
    public function getLibreOfficeConvertToPDFFilenameFellApp()
    {
        return $this->libreOfficeConvertToPDFFilenameFellApp;
    }

    /**
     * @param mixed $libreOfficeConvertToPDFFilenameFellApp
     */
    public function setLibreOfficeConvertToPDFFilenameFellApp($libreOfficeConvertToPDFFilenameFellApp)
    {
        $this->libreOfficeConvertToPDFFilenameFellApp = $libreOfficeConvertToPDFFilenameFellApp;
    }

    /**
     * @return mixed
     */
    public function getLibreOfficeConvertToPDFPathFellApp()
    {
        return $this->libreOfficeConvertToPDFPathFellApp;
    }

    /**
     * @param mixed $libreOfficeConvertToPDFPathFellApp
     */
    public function setLibreOfficeConvertToPDFPathFellApp($libreOfficeConvertToPDFPathFellApp)
    {
        $this->libreOfficeConvertToPDFPathFellApp = $libreOfficeConvertToPDFPathFellApp;
    }

    /**
     * @return mixed
     */
    public function getPdftkPathFellApp()
    {
        return $this->pdftkPathFellApp;
    }

    /**
     * @param mixed $pdftkPathFellApp
     */
    public function setPdftkPathFellApp($pdftkPathFellApp)
    {
        $this->pdftkPathFellApp = $pdftkPathFellApp;
    }

    /**
     * @return mixed
     */
    public function getGsPathFellApp()
    {
        return $this->gsPathFellApp;
    }

    /**
     * @param mixed $gsPathFellApp
     */
    public function setGsPathFellApp($gsPathFellApp)
    {
        $this->gsPathFellApp = $gsPathFellApp;
    }

    /**
     * @return mixed
     */
    public function getGsFilenameFellApp()
    {
        return $this->gsFilenameFellApp;
    }

    /**
     * @param mixed $gsFilenameFellApp
     */
    public function setGsFilenameFellApp($gsFilenameFellApp)
    {
        $this->gsFilenameFellApp = $gsFilenameFellApp;
    }

    /**
     * @return mixed
     */
    public function getGsArgumentsFellApp()
    {
        return $this->gsArgumentsFellApp;
    }

    /**
     * @param mixed $gsArgumentsFellApp
     */
    public function setGsArgumentsFellApp($gsArgumentsFellApp)
    {
        $this->gsArgumentsFellApp = $gsArgumentsFellApp;
    }

    /**
     * @return mixed
     */
    public function getPdftkFilenameFellApp()
    {
        return $this->pdftkFilenameFellApp;
    }

    /**
     * @param mixed $pdftkFilenameFellApp
     */
    public function setPdftkFilenameFellApp($pdftkFilenameFellApp)
    {
        $this->pdftkFilenameFellApp = $pdftkFilenameFellApp;
    }

    /**
     * @return mixed
     */
    public function getPdftkArgumentsFellApp()
    {
        return $this->pdftkArgumentsFellApp;
    }

    /**
     * @param mixed $pdftkArgumentsFellApp
     */
    public function setPdftkArgumentsFellApp($pdftkArgumentsFellApp)
    {
        $this->pdftkArgumentsFellApp = $pdftkArgumentsFellApp;
    }

    /////////////// LINUX /////////////////
    /**
     * @return mixed
     */
    public function getLibreOfficeConvertToPDFPathFellAppLinux()
    {
        return $this->libreOfficeConvertToPDFPathFellAppLinux;
    }

    /**
     * @param mixed $libreOfficeConvertToPDFPathFellAppLinux
     */
    public function setLibreOfficeConvertToPDFPathFellAppLinux($libreOfficeConvertToPDFPathFellAppLinux)
    {
        $this->libreOfficeConvertToPDFPathFellAppLinux = $libreOfficeConvertToPDFPathFellAppLinux;
    }

    /**
     * @return mixed
     */
    public function getLibreOfficeConvertToPDFFilenameFellAppLinux()
    {
        return $this->libreOfficeConvertToPDFFilenameFellAppLinux;
    }

    /**
     * @param mixed $libreOfficeConvertToPDFFilenameFellAppLinux
     */
    public function setLibreOfficeConvertToPDFFilenameFellAppLinux($libreOfficeConvertToPDFFilenameFellAppLinux)
    {
        $this->libreOfficeConvertToPDFFilenameFellAppLinux = $libreOfficeConvertToPDFFilenameFellAppLinux;
    }

    /**
     * @return mixed
     */
    public function getLibreOfficeConvertToPDFArgumentsdFellAppLinux()
    {
        return $this->libreOfficeConvertToPDFArgumentsdFellAppLinux;
    }

    /**
     * @param mixed $libreOfficeConvertToPDFArgumentsdFellAppLinux
     */
    public function setLibreOfficeConvertToPDFArgumentsdFellAppLinux($libreOfficeConvertToPDFArgumentsdFellAppLinux)
    {
        $this->libreOfficeConvertToPDFArgumentsdFellAppLinux = $libreOfficeConvertToPDFArgumentsdFellAppLinux;
    }

    /**
     * @return mixed
     */
    public function getPdftkPathFellAppLinux()
    {
        return $this->pdftkPathFellAppLinux;
    }

    /**
     * @param mixed $pdftkPathFellAppLinux
     */
    public function setPdftkPathFellAppLinux($pdftkPathFellAppLinux)
    {
        $this->pdftkPathFellAppLinux = $pdftkPathFellAppLinux;
    }

    /**
     * @return mixed
     */
    public function getPdftkFilenameFellAppLinux()
    {
        return $this->pdftkFilenameFellAppLinux;
    }

    /**
     * @param mixed $pdftkFilenameFellAppLinux
     */
    public function setPdftkFilenameFellAppLinux($pdftkFilenameFellAppLinux)
    {
        $this->pdftkFilenameFellAppLinux = $pdftkFilenameFellAppLinux;
    }

    /**
     * @return mixed
     */
    public function getPdftkArgumentsFellAppLinux()
    {
        return $this->pdftkArgumentsFellAppLinux;
    }

    /**
     * @param mixed $pdftkArgumentsFellAppLinux
     */
    public function setPdftkArgumentsFellAppLinux($pdftkArgumentsFellAppLinux)
    {
        $this->pdftkArgumentsFellAppLinux = $pdftkArgumentsFellAppLinux;
    }

    /**
     * @return mixed
     */
    public function getGsPathFellAppLinux()
    {
        return $this->gsPathFellAppLinux;
    }

    /**
     * @param mixed $gsPathFellAppLinux
     */
    public function setGsPathFellAppLinux($gsPathFellAppLinux)
    {
        $this->gsPathFellAppLinux = $gsPathFellAppLinux;
    }

    /**
     * @return mixed
     */
    public function getGsFilenameFellAppLinux()
    {
        return $this->gsFilenameFellAppLinux;
    }

    /**
     * @param mixed $gsFilenameFellAppLinux
     */
    public function setGsFilenameFellAppLinux($gsFilenameFellAppLinux)
    {
        $this->gsFilenameFellAppLinux = $gsFilenameFellAppLinux;
    }

    /**
     * @return mixed
     */
    public function getGsArgumentsFellAppLinux()
    {
        return $this->gsArgumentsFellAppLinux;
    }

    /**
     * @param mixed $gsArgumentsFellAppLinux
     */
    public function setGsArgumentsFellAppLinux($gsArgumentsFellAppLinux)
    {
        $this->gsArgumentsFellAppLinux = $gsArgumentsFellAppLinux;
    }
    ////////////////////// EOF third party software //////////////////////////

    /**
     * @return mixed
     */
    public function getNetworkDrivePath()
    {
        return $this->networkDrivePath;
    }

    /**
     * @param mixed $networkDrivePath
     */
    public function setNetworkDrivePath($networkDrivePath)
    {
        $this->networkDrivePath = $networkDrivePath;
    }

    /**
     * @return mixed
     */
    public function getPermittedFailedLoginAttempt()
    {
        return $this->permittedFailedLoginAttempt;
    }

    /**
     * @param mixed $permittedFailedLoginAttempt
     */
    public function setPermittedFailedLoginAttempt($permittedFailedLoginAttempt)
    {
        $this->permittedFailedLoginAttempt = $permittedFailedLoginAttempt;
    }

    /**
     * @return mixed
     */
    public function getCaptchaSiteKey()
    {
        return $this->captchaSiteKey;
    }

    /**
     * @param mixed $captchaSiteKey
     */
    public function setCaptchaSiteKey($captchaSiteKey)
    {
        $this->captchaSiteKey = $captchaSiteKey;
    }

    /**
     * @return mixed
     */
    public function getCaptchaSecretKey()
    {
        return $this->captchaSecretKey;
    }

    /**
     * @param mixed $captchaSecretKey
     */
    public function setCaptchaSecretKey($captchaSecretKey)
    {
        $this->captchaSecretKey = $captchaSecretKey;
    }

    /**
     * @return mixed
     */
    public function getCaptchaEnabled()
    {
        return $this->captchaEnabled;
    }

    /**
     * @param mixed $captchaEnabled
     */
    public function setCaptchaEnabled($captchaEnabled)
    {
        $this->captchaEnabled = $captchaEnabled;
    }

    /**
     * @return mixed
     */
    public function getNoticeAttemptingPasswordResetLDAP()
    {
        return $this->noticeAttemptingPasswordResetLDAP;
    }

    /**
     * @param mixed $noticeAttemptingPasswordResetLDAP
     */
    public function setNoticeAttemptingPasswordResetLDAP($noticeAttemptingPasswordResetLDAP)
    {
        $this->noticeAttemptingPasswordResetLDAP = $noticeAttemptingPasswordResetLDAP;
    }

    /**
     * @return mixed
     */
    public function getNoticeSignUpNoCwid()
    {
        return $this->noticeSignUpNoCwid;
    }

    /**
     * @param mixed $noticeSignUpNoCwid
     */
    public function setNoticeSignUpNoCwid($noticeSignUpNoCwid)
    {
        $this->noticeSignUpNoCwid = $noticeSignUpNoCwid;
    }

    /**
     * @return mixed
     */
    public function getNoticeHasLdapAccount()
    {
        return $this->noticeHasLdapAccount;
    }

    /**
     * @param mixed $noticeHasLdapAccount
     */
    public function setNoticeHasLdapAccount($noticeHasLdapAccount)
    {
        $this->noticeHasLdapAccount = $noticeHasLdapAccount;
    }

    /**
     * @return mixed
     */
    public function getNoticeLdapName()
    {
        return $this->noticeLdapName;
    }

    /**
     * @param mixed $noticeLdapName
     */
    public function setNoticeLdapName($noticeLdapName)
    {
        $this->noticeLdapName = $noticeLdapName;
    }

    /**
     * @return mixed
     */
    public function getCalllogSiteParameter()
    {
        return $this->calllogSiteParameter;
    }

    /**
     * @param mixed $calllogSiteParameter
     */
    public function setCalllogSiteParameter($calllogSiteParameter)
    {
        $this->calllogSiteParameter = $calllogSiteParameter;
    }

    /**
     * @return mixed
     */
    public function getNavbarFilterInstitution1()
    {
        return $this->navbarFilterInstitution1;
    }

    /**
     * @param mixed $navbarFilterInstitution1
     */
    public function setNavbarFilterInstitution1($navbarFilterInstitution1)
    {
        $this->navbarFilterInstitution1 = $navbarFilterInstitution1;
    }

    /**
     * @return mixed
     */
    public function getNavbarFilterInstitution2()
    {
        return $this->navbarFilterInstitution2;
    }

    /**
     * @param mixed $navbarFilterInstitution2
     */
    public function setNavbarFilterInstitution2($navbarFilterInstitution2)
    {
        $this->navbarFilterInstitution2 = $navbarFilterInstitution2;
    }

    /**
     * @return mixed
     */
    public function getDefaultDeidentifierAccessionType()
    {
        return $this->defaultDeidentifierAccessionType;
    }

    /**
     * @param mixed $defaultDeidentifierAccessionType
     */
    public function setDefaultDeidentifierAccessionType($defaultDeidentifierAccessionType)
    {
        $this->defaultDeidentifierAccessionType = $defaultDeidentifierAccessionType;
    }

    /**
     * @return mixed
     */
    public function getDefaultScanAccessionType()
    {
        return $this->defaultScanAccessionType;
    }

    /**
     * @param mixed $defaultScanAccessionType
     */
    public function setDefaultScanAccessionType($defaultScanAccessionType)
    {
        $this->defaultScanAccessionType = $defaultScanAccessionType;
    }

    /**
     * @return mixed
     */
    public function getDefaultScanMrnType()
    {
        return $this->defaultScanMrnType;
    }

    /**
     * @param mixed $defaultScanMrnType
     */
    public function setDefaultScanMrnType($defaultScanMrnType)
    {
        $this->defaultScanMrnType = $defaultScanMrnType;
    }

    /**
     * @return mixed
     */
    public function getDefaultScanDelivery()
    {
        return $this->defaultScanDelivery;
    }

    /**
     * @param mixed $defaultScanDelivery
     */
    public function setDefaultScanDelivery($defaultScanDelivery)
    {
        $this->defaultScanDelivery = $defaultScanDelivery;
    }

//    /**
//     * @return mixed
//     */
//    public function getDefaultInstitutionalPHIScope()
//    {
//        return $this->defaultInstitutionalPHIScope;
//    }
//
//    /**
//     * @param mixed $defaultInstitutionalPHIScope
//     */
//    public function setDefaultInstitutionalPHIScope($defaultInstitutionalPHIScope)
//    {
//        $this->defaultInstitutionalPHIScope = $defaultInstitutionalPHIScope;
//    }

    /**
     * @return mixed
     */
    public function getDefaultOrganizationRecipient()
    {
        return $this->defaultOrganizationRecipient;
    }

    /**
     * @param mixed $defaultOrganizationRecipient
     */
    public function setDefaultOrganizationRecipient($defaultOrganizationRecipient)
    {
        $this->defaultOrganizationRecipient = $defaultOrganizationRecipient;
    }

    /**
     * @return mixed
     */
    public function getDefaultScanner()
    {
        return $this->defaultScanner;
    }

    /**
     * @param mixed $defaultScanner
     */
    public function setDefaultScanner($defaultScanner)
    {
        $this->defaultScanner = $defaultScanner;
    }

    
}