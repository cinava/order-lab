<?php

namespace Oleg\UserdirectoryBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\AttributeOverrides;
use Doctrine\ORM\Mapping\AttributeOverride;

use FOS\UserBundle\Model\User as BaseUser;

//Use FOSUser bundle: https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Resources/doc/index.md
//User is a reserved keyword in SQL so you cannot use it as table name
//Generate unique username (post): primaryPublicUserId + "_" + keytype + "_" _ id


//[Doctrine\DBAL\DBALException]
// An exception occurred while executing 'ALTER TABLE user_fosuser ALTER COLUMN username NVARCHAR(180) NOT NULL':
// SQLSTATE[42000]: [Microsoft][ODBC Driver 11 for SQL Server][SQL Server]The index 'username_idx' is dependent on column 'username'.
//Caused by FriendsOfSymfony/FOSUserBundle forked from KnpLabs/KnpUserBundle commit 6fefb7212dfc629ebc74af67d75a62d274a44c95
//Make username and email length compatible with actual database limitations regarding key and index length
//So use "friendsofsymfony/user-bundle": "v2.0.0-alpha3"

/**
 * @ORM\Entity(repositoryClass="Oleg\UserdirectoryBundle\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"keytype", "primaryPublicUserId"},
 *     errorPath="primaryPublicUserId",
 *     message="Can not create a new user: the combination of the Primary Public User ID Type and Primary Public User ID is already in use."
 * )
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user_fosuser",
 *  indexes={
 *      @ORM\Index( name="keytype_idx", columns={"keytype"} ),
 *      @ORM\Index( name="primaryPublicUserId_idx", columns={"primaryPublicUserId"} ),
 *      @ORM\Index( name="username_idx", columns={"username"} )
 *  }
 * )
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="email",
 *          column=@ORM\Column(
 *              name     = "email",
 *              type     = "string",
 *              unique   = false,
 *              nullable = true
 *          )
 *      ),
 *      @ORM\AttributeOverride(name="emailCanonical",
 *          column=@ORM\Column(
 *              name     = "email_canonical",
 *              type     = "string",
 *              unique   = false,
 *              nullable = true
 *          )
 *      )
 * })
 */
class User extends BaseUser {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

//    /**
//     * @ORM\Column(name="email", type="string", unique=false, nullable=true)
//     */
//    protected $email;
//
//    /**
//     * @ORM\Column(name="email_canonical", type="string", unique=false, nullable=true)
//     */
//    protected $emailCanonical;

    /**
     * Ldap Object Distinguished Name
     * @var string $dn
     */
    private $dn;

    /**
     * Primary Public User ID Type
     *
     * @ORM\ManyToOne(targetEntity="UsernameType", inversedBy="users")
     * @ORM\JoinColumn(name="keytype", referencedColumnName="id", nullable=true)
     */
    protected $keytype;

    /**
     * @ORM\Column(name="primaryPublicUserId", type="string")
     */
    private $primaryPublicUserId;

    /**
     * includes 7+2 fields: suffix, firstName, middleName, lastName, displayName, initials, preferredPhone, email/emailCanonical (used instead of extended user's email)
     *
     * @ORM\OneToMany(targetEntity="UserInfo", mappedBy="user", cascade={"persist","remove"},
     *  indexBy="firstName,middleName,lastName,displayName,email"
     * )
     * @ORM\OrderBy({"displayName" = "ASC"})
     */
    private $infos;

    /**
     * @ORM\Column(name="createdby", type="string", nullable=true)
     */
    private $createdby;

    /**
     * @ORM\OneToOne(targetEntity="UserPreferences", inversedBy="user", cascade={"persist","remove"})
     */
    private $preferences;

    /**
     * @ORM\OneToOne(targetEntity="Credentials", inversedBy="user", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="credentials_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $credentials;

    /**
     * @ORM\OneToMany(targetEntity="Training", mappedBy="user", cascade={"persist","remove"})
     * @ORM\OrderBy({"completionDate" = "DESC", "orderinlist" = "ASC"})
     */
    private $trainings;

    /**
     * @ORM\OneToMany(targetEntity="Oleg\FellAppBundle\Entity\FellowshipApplication", mappedBy="user", cascade={"persist","remove"})
     * @ORM\OrderBy({"orderinlist" = "ASC"})
     */
    private $fellowshipApplications;

    /**
     * @ORM\OneToMany(targetEntity="Location", mappedBy="user", cascade={"persist","remove"})
     */
    private $locations;

    /**
     * @ORM\OneToMany(targetEntity="AdministrativeTitle", mappedBy="user", cascade={"persist","remove"})
     * @ORM\OrderBy({"orderinlist" = "ASC", "priority" = "ASC", "endDate" = "ASC"})
     */
    private $administrativeTitles;

    /**
     * @ORM\OneToMany(targetEntity="AppointmentTitle", mappedBy="user", cascade={"persist","remove"})
     * @ORM\OrderBy({"orderinlist" = "ASC", "priority" = "ASC", "endDate" = "ASC"})
     */
    private $appointmentTitles;

    /**
     * @ORM\OneToMany(targetEntity="MedicalTitle", mappedBy="user", cascade={"persist","remove"})
     * @ORM\OrderBy({"orderinlist" = "ASC", "priority" = "ASC", "endDate" = "ASC"})
     */
    private $medicalTitles;

    /**
     * @ORM\OneToMany(targetEntity="EmploymentStatus", mappedBy="user", cascade={"persist","remove"})
     * @ORM\OrderBy({"terminationDate" = "ASC"})
     */
    private $employmentStatus;

    /**
     * @ORM\ManyToMany(targetEntity="ResearchLab", inversedBy="user", cascade={"persist"})
     * @ORM\JoinTable(name="user_users_researchlabs")
     **/
    private $researchLabs;

    /**
     * @ORM\ManyToMany(targetEntity="Grant", inversedBy="user", cascade={"persist"})
     * @ORM\JoinTable(name="user_users_grants")
     **/
    private $grants;

    /**
     * @ORM\ManyToMany(targetEntity="Publication", inversedBy="users", cascade={"persist","remove"})
     * @ORM\JoinTable(name="user_users_publications")
     * @ORM\OrderBy({"updatedate" = "DESC", "publicationDate" = "DESC"})
     */
    private $publications;

    /**
     * @ORM\ManyToMany(targetEntity="Book", inversedBy="users", cascade={"persist","remove"})
     * @ORM\JoinTable(name="user_users_books")
     * @ORM\OrderBy({"updatedate" = "DESC", "publicationDate" = "DESC"})
     */
    private $books;

    /**
     * @ORM\OneToMany(targetEntity="Lecture", mappedBy="user", cascade={"persist","remove"})
     * @ORM\OrderBy({"lectureDate" = "DESC"})
     */
    private $lectures;

    /**
     * @ORM\OneToMany(targetEntity="PrivateComment", mappedBy="user", cascade={"persist","remove"})
     * @ORM\OrderBy({"commentTypeStr" = "ASC", "updatedate" = "DESC", "orderinlist" = "ASC"})
     */
    private $privateComments;

    /**
     * @ORM\OneToMany(targetEntity="PublicComment", mappedBy="user", cascade={"persist","remove"})
     * @ORM\OrderBy({"commentTypeStr" = "ASC", "updatedate" = "DESC", "orderinlist" = "ASC"})
     */
    private $publicComments;

    /**
     * @ORM\OneToMany(targetEntity="ConfidentialComment", mappedBy="user", cascade={"persist","remove"})
     * @ORM\OrderBy({"commentTypeStr" = "ASC", "updatedate" = "DESC", "orderinlist" = "ASC"})
     */
    private $confidentialComments;

    /**
     * @ORM\OneToMany(targetEntity="AdminComment", mappedBy="user", cascade={"persist","remove"})
     * @ORM\OrderBy({"commentTypeStr" = "ASC", "updatedate" = "DESC", "orderinlist" = "ASC"})
     */
    private $adminComments;


    /**
     * @ORM\OneToOne(targetEntity="Document")
     * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id")
     **/
    private $avatar;

    /**
     * @ORM\OneToMany(targetEntity="Permission", mappedBy="user", cascade={"persist","remove"})
     */
    private $permissions;

    /**
     * @ORM\OneToOne(targetEntity="PerSiteSettings", mappedBy="user", cascade={"persist","remove"})
     */
    private $perSiteSettings;



    function __construct( $addobjects=true )
    {
        $this->infos = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->administrativeTitles = new ArrayCollection();
        $this->appointmentTitles = new ArrayCollection();
        $this->medicalTitles = new ArrayCollection();
        $this->employmentStatus = new ArrayCollection();
        $this->researchLabs = new ArrayCollection();
        $this->grants = new ArrayCollection();
        $this->trainings = new ArrayCollection();
        $this->fellowshipApplications = new ArrayCollection();
        $this->publications = new ArrayCollection();
        $this->books = new ArrayCollection();
        $this->lectures = new ArrayCollection();
        $this->permissions = new ArrayCollection();

        $this->privateComments = new ArrayCollection();
        $this->publicComments = new ArrayCollection();
        $this->confidentialComments = new ArrayCollection();
        $this->adminComments = new ArrayCollection();

        //create user info
        $userInfo = new UserInfo();
        $this->addInfo($userInfo);

        //create preferences
        $userPref = new UserPreferences();
        //$userPref->setTooltip(true);
        $userPref->setTimezone('America/New_York');
        $userPref->setUser($this);
        $this->setPreferences($userPref);

        //create credentials
        $this->setCredentials(new Credentials($this,$addobjects));

        //set unlocked, enabled
        $this->setLocked(false);
        $this->setEnabled(true);

        parent::__construct();
    }

    /**
     * @param string $dn
     */
    public function setDn($dn)
    {
        $this->dn = $dn;
    }

    /**
     * @return string
     */
    public function getDn()
    {
        return $this->dn;
    }

    public function setIdNull() {
        $this->id = null;
    }


    /**
     * @param mixed $avatar
     */
    public function setAvatar($avatar)
    {
        //1) clear old avatar image if exists
        if( $this->getAvatar() ) {
            $this->getAvatar()->clearUseObject();
        }

        //2) set avatar
        $this->avatar = $avatar;
        if( $avatar ) {
            $avatar->createUseObject($this);
        }
    }

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $keytype
     */
    public function setKeytype($keytype)
    {
        $this->keytype = $keytype;
    }

    /**
     * @return mixed
     */
    public function getKeytype()
    {
        return $this->keytype;
    }

    /**
     * @param mixed $primaryPublicUserId
     */
    public function setPrimaryPublicUserId($primaryPublicUserId)
    {
        $this->primaryPublicUserId = $primaryPublicUserId;
    }

    /**
     * @return mixed
     */
    public function getPrimaryPublicUserId()
    {
        return $this->primaryPublicUserId;
    }

//    //password can not be NULL
//    public function setPassword($password) {
//        if( $password == NULL ) {
//            $this->password = "";
//        } else {
//            $this->password = $password;
//        }
//    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        if( count($this->getAdministrativeTitles()) == 0 ) {
            $administrativeTitle = new AdministrativeTitle($this);
            $administrativeTitle->setName($title);
            $this->addAdministrativeTitle($administrativeTitle);
        } else {
            $this->getAdministrativeTitles()->first()->setName($title);
        }

    }


    /**
     * @param mixed $createdby
     */
    public function setCreatedby($createdby = 'ldap')
    {
        $this->createdby = $createdby;
    }

    /**
     * @return mixed
     */
    public function getCreatedby()
    {
        return $this->createdby;
    }

    //
    public function getMainLocation() {

        $loc = $this->getLocations()->get(0);

        if( $loc && $loc->hasLocationTypeName("Employee Office") ) {
            return $loc;
        }

        foreach( $this->getLocations() as $loc ) {
            if( $loc->hasLocationTypeName("Employee Office") ) {
                return $loc;
            }
            if( $loc->getName() == "Main Office" ) {
                return $loc;
            }
        }

        return null;
    }

    public function getHomeLocation() {

        $loc = $this->getLocations()->get(1);

        if( $loc->hasLocationTypeName("Employee Home") ) {
            return $loc;
        }

        foreach( $this->getLocations() as $loc ) {
            if( $loc->hasLocationTypeName("Employee Home") ) {
                return $loc;
            }
            if( $loc->getName() == "Home" ) {
                return $loc;
            }
        }

        return null;
    }


    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->roles, true);
    }


    /**
     * @param mixed $preferences
     */
    public function setPreferences($preferences)
    {
        $this->preferences = $preferences;
    }

    /**
     * @return mixed
     */
    public function getPreferences()
    {
        return $this->preferences;
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


    public function addTraining($training)
    {
        if( $training && !$this->trainings->contains($training) ) {
            $this->trainings->add($training);
            $training->setUser($this);
        }

        return $this;
    }
    public function removeTraining($training)
    {
        $this->trainings->removeElement($training);
    }
    public function getTrainings()
    {
        return $this->trainings;
    }

    public function addFellowshipApplication($training)
    {
        if( $training && !$this->fellowshipApplications->contains($training) ) {
            $this->fellowshipApplications->add($training);
            $training->setUser($this);
        }

        return $this;
    }
    public function removeFellowshipApplication($training)
    {
        $this->fellowshipApplications->removeElement($training);
    }
    public function getFellowshipApplications()
    {
        return $this->fellowshipApplications;
    }


    public function addLocation($location)
    {
        if( $location && !$this->locations->contains($location) ) {
            $this->locations->add($location);
            $location->setUser($this);
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

    public function addAdministrativeTitle($administrativeTitle)
    {
        if( $administrativeTitle && !$this->administrativeTitles->contains($administrativeTitle) ) {
            $this->administrativeTitles->add($administrativeTitle);
            $administrativeTitle->setUser($this);

            //$administrativeTitle->setHeads();
        }
    
        return $this;
    }
    public function removeAdministrativeTitle($administrativeTitle)
    {
        //$administrativeTitle->unsetHeads();
        $this->administrativeTitles->removeElement($administrativeTitle);
    }
    public function getAdministrativeTitles()
    {
        return $this->administrativeTitles;
    }

    public function addAppointmentTitle($appointmentTitle)
    {
        if( $appointmentTitle && !$this->appointmentTitles->contains($appointmentTitle) ) {
            $this->appointmentTitles->add($appointmentTitle);
            $appointmentTitle->setUser($this);
        }
    
        return $this;
    }
    public function removeAppointmentTitle($appointmentTitle)
    {
        $this->appointmentTitles->removeElement($appointmentTitle);
    }
    public function getAppointmentTitles()
    {
        return $this->appointmentTitles;
    }

    public function addMedicalTitle($medicalTitle)
    {
        if( $medicalTitle && !$this->medicalTitles->contains($medicalTitle) ) {
            $this->medicalTitles->add($medicalTitle);
            $medicalTitle->setUser($this);
        }
        return $this;
    }
    public function removeMedicalTitle($medicalTitle)
    {
        $this->medicalTitles->removeElement($medicalTitle);
    }
    public function getMedicalTitles()
    {
        return $this->medicalTitles;
    }


    /**
     * @param mixed $credentials
     */
    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @return mixed
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    public function addEmploymentStatus($employmentStatus)
    {
        if( $employmentStatus ) {
            if( !$this->employmentStatus->contains($employmentStatus) ) {
                $this->employmentStatus->add($employmentStatus);
                $employmentStatus->setUser($this);
            }
        }

        return $this;
    }
    public function addEmploymentStatu($employmentStatus) {
        $this->addEmploymentStatus($employmentStatus);
        return $this;
    }

    public function removeEmploymentStatus($employmentStatus)
    {
        $this->employmentStatus->removeElement($employmentStatus);
    }
    public function removeEmploymentStatu($employmentStatus)
    {
        $this->removeEmploymentStatus($employmentStatus);
    }

    /**
     * Get employmentStatus
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmploymentStatus()
    {
        return $this->employmentStatus;
    }



    /**
     * @return mixed
     */
    public function getPrivateComments()
    {
        return $this->privateComments;
    }
    public function addPrivateComment( $comment )
    {
        if( !$comment )
            return;

        if( !$this->privateComments->contains($comment) ) {
            $comment->setUser($this);
            $this->privateComments->add($comment);
        }
    }
    public function removePrivateComment($comment)
    {
        $this->privateComments->removeElement($comment);
    }


    /**
     * @return mixed
     */
    public function getPublicComments()
    {
        return $this->publicComments;
    }
    public function addPublicComment( $comment )
    {
        if( !$comment )
            return;

        if( !$this->publicComments->contains($comment) ) {
            $comment->setUser($this);
            $this->publicComments->add($comment);
        }
    }
    public function removePublicComment($comment)
    {
        $this->publicComments->removeElement($comment);
    }


    /**
     * @return mixed
     */
    public function getAdminComments()
    {
        return $this->adminComments;
    }
    public function addAdminComment( $comment )
    {
        if( !$comment )
            return;

        if( !$this->adminComments->contains($comment) ) {
            $comment->setUser($this);
            $this->adminComments->add($comment);
        }
    }
    public function removeAdminComment($comment)
    {
        $this->adminComments->removeElement($comment);
    }


    /**
     * @return mixed
     */
    public function getConfidentialComments()
    {
        return $this->confidentialComments;
    }
    public function addConfidentialComment( $comment )
    {
        if( !$comment )
            return;

        if( !$this->confidentialComments->contains($comment) ) {
            $comment->setUser($this);
            $this->confidentialComments->add($comment);
        }
    }
    public function removeConfidentialComment($comment)
    {
        $this->confidentialComments->removeElement($comment);
    }



    public function addResearchLab($researchLab)
    {
        if( $researchLab && !$this->researchLabs->contains($researchLab) ) {
            $this->researchLabs->add($researchLab);
            //$researchLab->addUser($this);
        }

        return $this;
    }
    public function removeResearchLab($researchLab)
    {
        $this->researchLabs->removeElement($researchLab);
        //$researchLab->removeUser($this);
    }
    public function getResearchLabs()
    {
        return $this->researchLabs;
    }


    public function addGrant($item)
    {
        if( $item && !$this->grants->contains($item) ) {
            $this->grants->add($item);
            $item->addUser($this);
        }
        return $this;
    }
    public function removeGrant($item)
    {
        $this->grants->removeElement($item);
        $item->removeUser($this);
    }
    public function getGrants()
    {
        return $this->grants;
    }


    public function addPublication($item)
    {
        if( $item && !$this->publications->contains($item) ) {
            $this->publications->add($item);
            //$item->addUser($this);
        }
        return $this;
    }
    public function removePublication($item)
    {
        $this->publications->removeElement($item);
        //$item->removeUser($this);
    }
    public function getPublications()
    {
        return $this->publications;
    }

    public function addBook($item)
    {
        if( $item && !$this->books->contains($item) ) {
            $this->books->add($item);
        }
        return $this;
    }
    public function removeBook($item)
    {
        $this->books->removeElement($item);
    }
    public function getBooks()
    {
        return $this->books;
    }


    public function addLecture($item)
    {
        if( $item && !$this->lectures->contains($item) ) {
            $this->lectures->add($item);
            $item->setUser($this);
        }
        return $this;
    }
    public function removeLecture($item)
    {
        $this->lectures->removeElement($item);
    }
    public function getLectures()
    {
        return $this->lectures;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }
    public function addPermission($item)
    {
        if( $item && !$this->permissions->contains($item) ) {
            $this->permissions->add($item);
            $item->setUser($this);
        }
    }
    public function removePermission($item)
    {
        $this->permissions->removeElement($item);
    }


    public function __toString() {
        return $this->getUserNameStr();
    }





    public function setUsernameForce($username)
    {
        $this->username = $username;
        $this->setUsernameCanonicalForce($username);
        return $this;
    }
    //do not overwrite username when user id is set (user already exists in DB)
    public function setUsername($username)
    {
        if( $this->getId() && $username != $this->getUsername() ) {
            //continue without error
            return;
            //exit('Can not change username when user is in DB: username='.$username.', existing username='.$this->getUsername().', id='.$this->getId());
            //throw new \Exception( 'Can not change username when user is in DB: username='.$username.', existing username='.$this->getUsername().', id='.$this->getId() );
        }

        $this->username = $username;

        return $this;
    }
    public function setUsernameCanonicalForce($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;
    }
    public function setUsernameCanonical($usernameCanonical)
    {
        if( $this->getId() && $usernameCanonical != $this->getUsernameCanonical() ) {
            //continue without error
            return;
            //exit('Can not change canonical username when user is in DB: username='.$usernameCanonical.', existing canonical username='.$this->getUsername().', id='.$this->getId());
            //throw new \Exception( 'Can not change canonical username when user is in DB: username='.$usernameCanonical.', existing canonical username='.$this->getUsername().', id='.$this->getId() );
        }

        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }


    //Username utilities methods
    public function setUniqueUsername() {
        $this->setUsername($this->createUniqueUsername());
    }

    public function createUniqueUsername() {
        return $this->createUniqueUsernameByKeyKeytype($this->getKeytype(),$this->getPrimaryPublicUserId());
    }

    public function createUniqueUsernameByKeyKeytype($keytype,$key) {
        $username = $key."_@_".$keytype->getAbbreviation();
        $usernamestr = preg_replace('/\s+/', '-', $username);   //replace all whitespaces by '-'
        return $usernamestr;
    }

    public function createCleanUsername($username) {
        $usernameArr = explode("_@_",$username);
        return $usernameArr[0];
    }

    public function getUsernamePrefix($username) {
        $usernameArr = explode("_@_",$username);
        if( array_key_exists(1, $usernameArr) ) {
            $prefix = $usernameArr[1];
        } else {
            $prefix = "";
        }
        return $prefix;
    }

    public function usernameIsValid($username) {
        if( strpos($username,"_@_") !== false ) {
            return true;
        }
        return false;
    }



    /**
     * @ORM\PrePersist
     */
    public function setDisplaynameIfEmpty()
    {
        if( !$this->getDisplayName() || $this->getDisplayName() == "" ) {
            $this->setDisplayname( $this->getUsernameOptimal() );
        }
    }



    public function addInfo($info)
    {
        if( $info && !$this->infos->contains($info) ) {
            $this->infos->add($info);
            $info->setUser($this);
        }

        return $this;
    }
    public function removeInfo($info)
    {
        $this->infos->removeElement($info);
    }
    public function getInfos()
    {
        return $this->infos;
    }

    /////////////////////////// user's info mapper 7+2: //////////////////////////////////////
    /// suffix, firstName, middleName, lastName, displayName, initials, preferredPhone, preferredEmail/emailCanonical (used instead of extended user's email) ///
    /**
     * @param mixed $suffix
     */
    public function setSuffix($suffix)
    {
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $infos->first()->setSuffix($suffix);
        }
    }

    /**
     * @return mixed
     */
    public function getSuffix()
    {
        $value = null;
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $value = $infos->first()->getSuffix();
        }
        return $value;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $infos->first()->setFirstName($firstName);
        }
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        $value = null;
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $value = $infos->first()->getFirstName();
        }
        return $value;
    }

    /**
     * @param mixed $middleName
     */
    public function setMiddleName($middleName)
    {
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $infos->first()->setMiddleName($middleName);
        }
    }

    /**
     * @return mixed
     */
    public function getMiddleName()
    {
        $value = null;
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $value = $infos->first()->getMiddleName();
        }
        return $value;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $infos->first()->setLastName($lastName);
        }
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        $value = null;
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $value = $infos->first()->getLastName();
        }
        return $value;
    }


    /**
     * @param mixed $firstName
     */
    public function setSalutation($salutation)
    {
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $infos->first()->setSalutation($salutation);
        }
    }

    public function getSalutation()
    {
        $value = null;
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $value = $infos->first()->getSalutation();
        }
        return $value;
    }


    public function getLastNameUppercase() {
        return $this->capitalizeIfNotAllCapital($this->getLastName());
    }
    public function getFirstNameUppercase() {
        return $this->capitalizeIfNotAllCapital($this->getFirstName());
    }
    function capitalizeIfNotAllCapital($s) {
        if( strlen(preg_replace('![^A-Z]+!', '', $s)) == strlen($s) ) {
            $s = ucfirst(strtolower($s));
        }
        return ucwords($s);
    }
//    function isAllCapital($s) {
//        if( $this->count_capitals($s) == strlen($s) ) {
//            return true;
//        }
//        return false;
//    }
//    function count_capitals($s) {
//        return strlen(preg_replace('![^A-Z]+!', '', $s));
//    }

    /**
     * @param mixed $displayName
     */
    public function setDisplayName($displayName)
    {
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $infos->first()->setDisplayName($displayName);
        }
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        $value = null;
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $value = $infos->first()->getDisplayName();
        }
        return $value;
    }

    /**
     * @param mixed $preferredPhone
     */
    public function setPreferredPhone($preferredPhone)
    {
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $infos->first()->setPreferredPhone($preferredPhone);
        }
    }

    /**
     * @return mixed
     */
    public function getPreferredPhone()
    {
        $value = null;
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $value = $infos->first()->getPreferredPhone();
        }
        return $value;
    }

    /**
     * @param mixed $initials
     */
    public function setInitials($initials)
    {
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $infos->first()->setInitials($initials);
        }
    }

    /**
     * @return mixed
     */
    public function getInitials()
    {
        $value = null;
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $value = $infos->first()->getInitials();
        }
        return $value;
    }

   //overwrite email
    public function setEmail($email)
    {
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $infos->first()->setEmail($email);
        }
    }
    //overwrite email
    public function getEmail()
    {
        $value = null;
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $value = $infos->first()->getEmail();
        }
        return $value;
    }
    //overwrite canonical email
    public function setEmailCanonical($emailCanonical)
    {
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $infos->first()->setEmailCanonical($emailCanonical);
        }
    }
    //overwrite canonical email
    public function getEmailCanonical()
    {
        $value = null;
        $infos = $this->getInfos();
        if( count($infos) > 0 ) {
            $value = $infos->first()->getEmailCanonical();
        }
        return $value;
    }

    /**
     * @param mixed $perSiteSettings
     */
    public function setPerSiteSettings($perSiteSettings)
    {
        $this->perSiteSettings = $perSiteSettings;
    }

    /**
     * @return mixed
     */
    public function getPerSiteSettings()
    {
        return $this->perSiteSettings;
    }

    /////////////////////////////////////////////////////////////////






    //////////////////// util methods ////////////////////////

    public function getCleanUsername() {
        return $this->createCleanUsername( $this->getUsername() );
    }

    //show user's FirstName LastName - userName (userNameType)
    public function getUserNameStr() {

        $primaryUseridKeytypeStr = $this->getPrimaryUseridKeytypeStr();
        $displayName = $this->getDisplayName();

        if( !$displayName ) {
            $displayName = $this->getFirstName() . " " . $this->getLastName();
        }

        if( $displayName && $primaryUseridKeytypeStr ) {
            return $displayName . " - " . $this->getPrimaryUseridKeytypeStr();
        }

        if( $primaryUseridKeytypeStr && !$displayName ){
            return $primaryUseridKeytypeStr."";
        }

        if( $displayName && !$primaryUseridKeytypeStr ){
            return $displayName."";
        }

        //Old user name string representation
//        $primaryUseridKeytypeStr = $this->getPrimaryUseridKeytypeStr();
//        $displayName = $this->getDisplayName();
//
//        if( $displayName && $primaryUseridKeytypeStr ) {
//            return $primaryUseridKeytypeStr." - ".$displayName;
//        }
//
//        if( $primaryUseridKeytypeStr && !$displayName ){
//            return $primaryUseridKeytypeStr."";
//        }
//
//        if( $displayName && !$primaryUseridKeytypeStr ){
//            return $displayName."";
//        }
    }


    public function getUserNameShortStr() {
        return $this->getPrimaryPublicUserId();
    }

    public function getPrimaryUseridKeytypeStr() {
        if( $this->getKeytype() && $this->getKeytype()->getName() ) {
            return $this->getPrimaryPublicUserId()." (".$this->getKeytype()->getName().")";
        } else {
            return $this->getPrimaryPublicUserId();
        }
    }


    public function getUsernameShortest() {
        if( $this->getDisplayName() ) {
            return $this->getDisplayName();
        } else {
            return $this->getPrimaryPublicUserId();
        }
    }

    //TODO:
    //optimal name

    //the user has a Preferred Name, start with preferred name;
    //If the user has no preferred name, but does have a first and last name, concatenate them
    //and start with the first and last name combo; if the user has no first name, use just the last name;
    //if the user has no last name, use the first name; if the user has none of the three names, start with the User ID:
    public function getUsernameOptimal() {

        $degrees = array();
        $titles = array();

        //get appended degrees
        foreach( $this->getTrainings() as $training ) {
            if( $training->getAppendDegreeToName() && $training->getDegree() ) {
                $degrees[] = $training->getDegree();
            }
            if( $training->getAppendFellowshipTitleToName() && $training->getFellowshipTitle() ) {
                if( $training->getFellowshipTitle()->getAbbreviation() ) {
                    $titles[] = $training->getFellowshipTitle()->getAbbreviation();
                }
            }
        }

        $degreesStr = implode(", ", $degrees);
        $titlesStr = implode(", ", $titles);

        $degreesAndTitlesStr = $degreesStr;
        if( $titlesStr ) {
            $degreesAndTitlesStr = $degreesAndTitlesStr . ", " . $titlesStr;
        }

        if( $degreesAndTitlesStr ) {
            $degreesAndTitlesStr = ", " . $degreesAndTitlesStr;
        }


        if( $this->getDisplayName() ) {
            return $this->getDisplayName() . $degreesAndTitlesStr;
        }

        if( $this->getLastName() && $this->getFirstName() ) {
            return $this->getLastName() . " " . $this->getFirstName() . $degreesAndTitlesStr;
        }

        if( $this->getLastName() ) {
            return $this->getLastName() . $degreesAndTitlesStr;
        }

        if( $this->getFirstName() ) {
            return $this->getFirstName() . $degreesAndTitlesStr;
        }

        if( $this->getPrimaryPublicUserId() ) {
            return $this->getPrimaryPublicUserId() . $degreesAndTitlesStr;
        }

        return $this->getId();
    }


    //get all institutions from administrative and appointment titles.
    //status: 0-unverified, 1-verified
    public function getInstitutions($type=null,$status=null) {
        $institutions = new ArrayCollection();
        if( $type == null || $type == "AdministrativeTitle" ) {
            foreach( $this->getAdministrativeTitles() as $adminTitles ) {
                if( $adminTitles->getInstitution() && $adminTitles->getInstitution()->getId() && $adminTitles->getInstitution()->getName() != "" )
                    if( !$institutions->contains($adminTitles->getInstitution()) ) {
                        if( $status == null || $adminTitles->getStatus() == $status ) {
                            $institutions->add($adminTitles->getInstitution());
                        }
                    }
            }
        }
        if( $type == null || $type == "AppointmentTitle" ) {
            foreach( $this->getAppointmentTitles() as $appTitles ) {
                if( $appTitles->getInstitution() && $appTitles->getInstitution()->getId() && $appTitles->getInstitution()->getName() != "" )
                    if( !$institutions->contains($appTitles->getInstitution()) ) {
                        if( $status == null || $appTitles->getStatus() == $status ) {
                            $institutions->add($appTitles->getInstitution());
                        }
                    }
            }
        }
        if( $type == null || $type == "MedicalTitle" ) {
            foreach( $this->getMedicalTitles() as $medicalTitles ) {
                if( $medicalTitles->getInstitution() && $medicalTitles->getInstitution()->getId() && $medicalTitles->getInstitution()->getName() != "" )
                    if( !$institutions->contains($medicalTitles->getInstitution()) ) {
                        if( $status == null || $medicalTitles->getStatus() == $status ) {
                            $institutions->add($medicalTitles->getInstitution());
                        }
                    }
            }
        }
        //echo "inst count=".count($institutions)."<br>";
//        foreach( $institutions as $institution ) {
//            echo "inst=".$institution->getId()."-".$institution->getName()."<br>";
//        }

        return $institutions;
    }

    //return array grouped by institution name:
    //instArr[instName][] = array('rootId'=>$rootId,'instId'=>$instId)
    //Cases:
    // Software Development (WCMC)
    // Pathology Informatics (NYP, WCMC)
    //Molecular Hematopathology (Hematopathology-WCMC, Molecular and Genomic Pathology-WCMC, Hematopathology-NYP, Molecular and Genomic Pathology-NYP)"
    public function getDeduplicatedInstitutions() {

        $instArr = array();

        foreach( $this->getInstitutions() as $institution ) {
            $instName = $institution->getName()."";
            //echo "instName=".$instName."<br>";

            //default uniqueName = "WCMC"
            $uniqueName = $institution->getRootName($institution)."";

            $rootId = null;
            if( $institution->getRootName($institution) ) {
                $rootId = $institution->getRootName($institution)->getId();
            }

            $institution = array(
                'rootId'=>$rootId,
                'uniqueName'=>$uniqueName,
                'parentName'=>$institution->getParent()."",
                'instId'=>$institution->getId(),
                'instNameWithRoot'=>$institution->getNodeNameWithRoot()
            );

            $instArr[$instName][] = $institution;
        }

//        echo '<pre>';
//        print_r($instArr);
//        echo  '</pre>';

        //constract a new correct array
        $instResArr = array();

        foreach ($instArr as $instKeyName => $instInfoArr) {
            foreach ($instInfoArr as $institution) {
                //check if name is already exists in instArr, but id different
                if( !$this->isSingleUniqueKeyMatchCombination($instInfoArr, 'uniqueName', $institution['uniqueName']) ) {
                    //echo "different inst with same name=".$institution['instId']."<br>";
                    //uniqueName = "Hematopathology-WCMC"
                    $institution['uniqueName'] = $institution['parentName']."-".$institution['uniqueName'];
                }
                $instResArr[$instKeyName][] = $institution;
            }
        }

//        echo '<pre>';
//        print_r($instResArr);
//        echo  '</pre>';

        return $instResArr;
    }
    public function isSingleUniqueKeyMatchCombination($array,$key1,$match1) {
        $count = 0;
        foreach( $array as $element ) {
            //echo $count.": compare: ".$element[$key1]."==".$match1."=>";
            if( $element[$key1] == $match1 ) {
                $count++;
                //echo "not unique! <br>";
                if( $count > 1 ) {
                    //echo "not unique! <br>";
                    return false;
                }
            } else {
                //echo "<br>";
            }
        }
        return true;
    }
//    public function isUniqueKeyMatchCombination($array,$key1,$match1,$key2,$match2) {
//        $count = 0;
//        foreach( $array as $element ) {
//            echo $count.": compare: ".$element[$key1]."==".$match1." && ".$element[$key2]."==".$match2."<br>";
//            if( $element[$key1] == $match1 && $element[$key2] == $match2 ) {
//                $count++;
//                if( $count > 1 ) {
//                    echo "not unique! <br>";
//                    return false;
//                }
//            }
//        }
//        return true;
//    }

    //TODO: check performance of foreach. It might be replaced by direct DB query
    public function getBosses() {
        $res = array();
        //$bosses = new ArrayCollection();
        foreach( $this->getAdministrativeTitles() as $adminTitles ) {
            $bosses = new ArrayCollection();
            foreach( $adminTitles->getBoss() as $boss ) {
                $bosses->add($boss);
                //$res[$adminTitles->getId()][] = $boss;
            }
            $res[$adminTitles->getId()]['bosses'] = $bosses;
            $res[$adminTitles->getId()]['titleobject'] = $adminTitles;
        }
        //return $bosses;
        return $res;
    }

    //returns: [Medical Director of Informatics] Victor Brodsky
    public function getTitleAndNameByTitle( $admintitle ) {
        return "[" . $admintitle->getName() . "] " . $this->getUsernameOptimal();
    }

    //from any (first) "Administrative Title" if available, if not - from any/first title
    public function getDetailsArr() {

        $res = array("title"=>null,"institution"=>null);

//        $adminTitles = $this->getAdministrativeTitles();
//        if( count($adminTitles)>0 ) {
//            $title = $adminTitles->first();
//            if( !$res["title"] ) {
//                $res["title"] = $title->getName()."";
//            }
//            if( !$res["institution"] ) {
//                $res["institution"] = $title->getInstitution()."";
//            }
//        }
        $adminTitles = $this->getAppointmentTitles();
        $res = $this->getDetailsArrFromTitles($adminTitles,$res);

        $appTitles = $this->getAppointmentTitles();
        $res = $this->getDetailsArrFromTitles($appTitles,$res);

        $medTitles = $this->getMedicalTitles();
        $res = $this->getDetailsArrFromTitles($medTitles,$res);

        return $res;
    }
    public function getDetailsArrFromTitles($titles,$res) {
        if( count($titles)>0 ) {
            $title = $titles->first();
            if( !$res["title"] ) {
                $res["title"] = $title->getName()."";
            }
            if( !$res["institution"] ) {
                $res["institution"] = $title->getInstitution();
            }
        }
        return $res;
    }


    //Testing
    public function getMemoryUsage() {
        //return round(memory_get_usage() / 1024);
        return memory_get_usage();
    }

    //TODO: check performance of foreach. It might be replaced by direct DB query
    public function getAssistants() {
        $assistants = new ArrayCollection();
        $ids = array();
        foreach( $this->getLocations() as $location ) {
            foreach( $location->getAssistant() as $assistant ) {
                $assistants->add($assistant);
                $ids[] = $assistant->getId();
            }
        }

        $res = array();
        $res['entities'] = $assistants;
        $res['ids'] = $ids;
        //print_r($ids);

        return $res;
    }

    public function getSiteRoles($sitename) {

        $roles = array();

        if( $sitename == 'employees' ) {
            $sitename = 'userdirectory';
        }

        //return $this->getRoles();

        foreach( $this->getRoles() as $role ) {
            if( stristr($role, $sitename) ) {
                $roles[] = $role;
            }
        }

//        foreach( $this->getRoles() as $role ) {
//            $roleObject = $em->getRepository('OlegUserdirectoryBundle:Roles')->findOneByName($role);
//            if( $roleObject && $roleObject->hasSite( $this->siteName ) ) {
//                $originalRoles[] = $role;
//            }
//        }

        return $roles;
    }

    //Preferred: (646) 555-5555
    //Main Office Line: (212) 444-4444
    //Main Office Mobile: (123) 333-3333
    public function getAllPhones() {
        $phonesArr = array();
        //get all locations phones
        foreach( $this->getLocations() as $location ) {
            if( count($location->getLocationTypes()) == 0 || !$location->hasLocationTypeName("Employee Home") ) {
                if( $location->getPhone() ) {
                    $phone = array();
                    $phone['prefix'] = $location->getName()." Line: ";
                    $phone['phone'] = $location->getPhone();
                    $phonesArr[] = $phone;
                }
                if( $location->getMobile() ) {
                    $phone = array();
                    $phone['prefix'] = $location->getName()." Mobile: ";
                    $phone['phone'] = $location->getMobile();
                    $phonesArr[] = $phone;
                }
                if( $location->getPager() ) {
                    $phone = array();
                    $phone['prefix'] = $location->getName()." Pager: ";
                    $phone['phone'] = $location->getPager();
                    $phonesArr[] = $phone;
                }
//                if( $location->getIc() )
//                    $phonesArr[] = $location->getName()." Intercom: ".$location->getIc();
            }
        }

        if( $this->getPreferredPhone() ) {
            $phone = array();
            if( count($phonesArr) > 0 ) {
                $phone['prefix'] = "Preferred: ";
            } else {
                $phone['prefix'] = "";
            }
            $phone['phone'] = $this->getPreferredPhone();
            $phonesArr[] = $phone;
        }

        if( count($phonesArr) == 1 ) {
            $phonesArr[0]['prefix'] = "";
        }

        rsort($phonesArr);

        return $phonesArr;
    }

    public function getAllEmail() {
        $emailArr = array();
        //echo "count loc=".count($this->getLocations())."<br>";
        //get all locations phones
        foreach( $this->getLocations() as $location ) {
            //echo "loc=".$location."<br>";
            if( count($location->getLocationTypes()) == 0 || $location->hasLocationTypeName("Employee Home") ) {
                //echo "email:".$location->getEmail()."<br>";
                if( $location->getEmail() ) {
                    $email = array();
                    $email['prefix'] = $location->getName().": ";
                    $email['email'] = $this->getEmail();
                    $emailArr[] = $email;
                }
            }
        }

        if( $this->getEmail() ) {
            $email = array();
            if( count($emailArr) > 0 ) {
                $email['prefix'] = "Preferred: ";
            } else {
                $email['prefix'] = "";
            }
            $email['email'] = $this->getEmail();
            $emailArr[] = $email;
        }

        if( count($emailArr) == 1 ) {
            $emailArr[0]['prefix'] = "";
        }

        rsort($emailArr);

        return $emailArr;
    }

    public function getSingleEmail() {
        if( $this->getEmail() ) {
            return $this->getEmail();
        }

        foreach( $this->getLocations() as $location ) {
            if( $location->getEmail() && $location->hasLocationTypeName("Employee Office") ) {
                return $location->getEmail();
            }
        }

        foreach( $this->getLocations() as $location ) {
            if( $location->getEmail() ) {
                return $location->getEmail();
            }
        }

        return null;
    }

    public function isInUserArray( $users ) {
        if( !$users ) {
            return false;
        }
        foreach( $users as $user ) {
            //echo "user=".$user."<br>";
            if( $user->getId() == $this->getId() ) {
                return true;
            }
        }
        return false;
    }


    /////////////////////// NOT USED!!! Return: Chief, Eyebrow Pathology ///////////////////////
    //Group by institutions
    public function getHeadInfo() {
        $instArr = array();

        $instArr = $this->addTitleInfo($instArr,'administrativeTitle',$this->getAdministrativeTitles());

        $instArr = $this->addTitleInfo($instArr,'appointmentTitle',$this->getAppointmentTitles());

        $instArr = $this->addTitleInfo($instArr,'medicalTitle',$this->getMedicalTitles());

        return $instArr;
    }
    public function addTitleInfo($instArr,$tablename,$titles) {
        foreach( $titles as $title ) {
            $elementInfo = null;
            if( $title->getName() ) {
                $name = $title->getName()->getName()."";
                $titleId = null;
                if( $title->getName()->getId() ) {
                    $titleId = $title->getName()->getId();
                }
                $elementInfo = array('tablename'=>$tablename,'id'=>$titleId,'name'=>$name);
            }

            //$headInfo[] = 'break-br';

            $instId = 0;
            $institution = null;
            if( $title->getInstitution() ) {
                $institution = $title->getInstitution();
                $instId = $title->getInstitution()->getId();
            }

            if( array_key_exists($instId,$instArr) ) {
                //echo $instId." => instId already exists<br>";
            } else {
                //echo $instId." => instId does not exists<br>";
                $instArr[$instId]['instInfo'] = $this->getHeadInstitutionInfoArr($institution);
            }
            $instArr[$instId]['titleInfo'][] = $elementInfo;
            $instArr[$instId]['old'] = 1;
        }//foreach titles

        return $instArr;
    }
    public function getHeadInstitutionInfoArr($institution) {

        //echo "inst=".$institution."<br>";
        //echo "count=".count($headInfo)."<br>";
        $pid = null;

        $headInfo = array();

        //service
        if( $institution ) {

            $institutionThis = $institution;
            //echo "inst=".$institutionThis."<br>";

            $name = $institutionThis->getName()."";
            $titleId = null;
            if( $institutionThis->getId() ) {
                $titleId = $institutionThis->getId();
            }
            $pid = null;
            $parent = $institutionThis->getParent();
            if( $parent && $parent->getId() ) {
                $pid = $parent->getId();
            }
            $elementInfo = array('tablename'=>'Institution','id'=>$titleId,'pid'=>$pid,'name'=>$name);
            $headInfo[] = $elementInfo;

        }

        //division
        if( $institution && $institution->getParent() ) {

            $institutionThis = $institution->getParent();
            //echo "inst=".$institutionThis."<br>";

            $name = $institutionThis->getName()."";
            $titleId = null;
            if( $institutionThis->getId() ) {
                $titleId = $institutionThis->getId();
            }
            $pid = null;
            $parent = $institutionThis->getParent();
            if( $parent && $parent->getId() ) {
                $pid = $parent->getId();
            }
            $elementInfo = array('tablename'=>'Institution','id'=>$titleId,'pid'=>$pid,'name'=>$name);
            $headInfo[] = $elementInfo;

        }

        //department
        if( $institution && $institution->getParent() && $institution->getParent()->getParent() ) {

            $institutionThis = $institution->getParent()->getParent();
            //echo "inst=".$institutionThis."<br>";

            $name = $institutionThis->getName()."";
            $titleId = null;
            if( $institutionThis->getId() ) {
                $titleId = $institutionThis->getId();
            }
            $pid = null;
            $parent = $institutionThis->getParent();
            if( $parent && $parent->getId() ) {
                $pid = $parent->getId();
            }
            $elementInfo = array('tablename'=>'Institution','id'=>$titleId,'pid'=>$pid,'name'=>$name);
            $headInfo[] = $elementInfo;

        }

        //institution
        if( $institution && $institution->getParent() && $institution->getParent()->getParent() && $institution->getParent()->getParent()->getParent() ) {

            $institutionThis = $institution->getParent()->getParent()->getParent();
            //echo "inst=".$institutionThis."<br>";

            $name = $institutionThis->getName()."";
            $titleId = null;
            if( $institutionThis->getId() ) {
                $titleId = $institutionThis->getId();
            }
            $pid = null;
            $parent = $institutionThis->getParent();
            if( $parent && $parent->getId() ) {
                $pid = $parent->getId();
            }
            $elementInfo = array('tablename'=>'Institution','id'=>$titleId,'pid'=>$pid,'name'=>$name);
            $headInfo[] = $elementInfo;

            //$headInfo[] = 'break-hr';
        }

        //$headInfo[] = 'break-hr';

        return $headInfo;
    }

    public function getUniqueTitles( $titles ) {
        $titlesArr = new ArrayCollection();
        $titleNameIdsArr = array();
        foreach( $titles as $title ) {
            if( $title->getName() ) {
                $nameId = $title->getName()->getId();
                if (!in_array($nameId, $titleNameIdsArr)) {
                    $titleNameIdsArr[] = $nameId;
                    $titlesArr->add($title);
                }
            }
        }
        return $titlesArr;
    }
    /////////////////////// EOF Return: Chief, Eyebrow Pathology ///////////////////////

    //TODO: create dynamic roles as in http://php-and-symfony.matthiasnoback.nl/2012/07/symfony2-security-creating-dynamic-roles-using-roleinterface/
    //ROLE_DEIDENTIFICATOR_USE: if one of the user role has DEIDENTIFICATOR site then create this role. It will solve security.yml problem


}