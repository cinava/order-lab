<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 4/22/14
 * Time: 10:19 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Oleg\UserdirectoryBundle\Repository\UserWrapperRepository")
 * @ORM\Table(name="user_userWrapper")
 */
class UserWrapper extends ListAbstract {

    /**
     * @ORM\OneToMany(targetEntity="UserWrapper", mappedBy="original", cascade={"persist"})
     **/
    protected $synonyms;

    /**
     * @ORM\ManyToOne(targetEntity="UserWrapper", inversedBy="synonyms", cascade={"persist"})
     * @ORM\JoinColumn(name="original_id", referencedColumnName="id", nullable=true)
     **/
    protected $original;


//    /**
//     * @ORM\Id
//     * @ORM\Column(type="integer")
//     * @ORM\GeneratedValue(strategy="AUTO")
//     */
//    private $id;

//    /**
//     * must be synchronised with name in ListAbstract
//     *
//     * @ORM\Column(type="string", nullable=true)
//     */
//    private $userStr;
    //use name in ListAbstract as userStr

    /**
     * User object
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;


    //Phone Number: [free text]
    /**
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private $userWrapperPhone;

    //E-Mail: [free text]
    /**
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $userWrapperEmail;

    //Specialty: [link to the platform list manager's specialty list items here
    // http://collage.med.cornell.edu/order/directory/admin/list-manager/id/69 - allow more than one]
    /**
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\HealthcareProviderSpecialtiesList", cascade={"persist","remove"})
     */
    private $userWrapperSpecialty;

    //Source Site: [ID/name of the O R D E R site used to create this particular instance of the user wrapper object;
    // for user wrappers created on the Call Log Book, this would have the ID of the Call Log Book]
    /**
     * @ORM\ManyToOne(targetEntity="SourceSystemList")
     */
    private $userWrapperSource;



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
     * @return mixed
     */
    public function getUserWrapperPhone()
    {
        return $this->userWrapperPhone;
    }

    /**
     * @param mixed $userWrapperPhone
     */
    public function setUserWrapperPhone($userWrapperPhone)
    {
        $this->userWrapperPhone = $userWrapperPhone;
    }

    /**
     * @return mixed
     */
    public function getUserWrapperEmail()
    {
        return $this->userWrapperEmail;
    }

    /**
     * @param mixed $userWrapperEmail
     */
    public function setUserWrapperEmail($userWrapperEmail)
    {
        $this->userWrapperEmail = $userWrapperEmail;
    }

    /**
     * @return mixed
     */
    public function getUserWrapperSpecialty()
    {
        return $this->userWrapperSpecialty;
    }

    /**
     * @param mixed $userWrapperSpecialty
     */
    public function setUserWrapperSpecialty($userWrapperSpecialty)
    {
        $this->userWrapperSpecialty = $userWrapperSpecialty;
    }

    /**
     * @return mixed
     */
    public function getUserWrapperSource()
    {
        return $this->userWrapperSource;
    }

    /**
     * @param mixed $userWrapperSource
     */
    public function setUserWrapperSource($userWrapperSource)
    {
        $this->userWrapperSource = $userWrapperSource;
    }




    /**
     * @param mixed $userStr
     */
    public function setUserStr($userStr)
    {
        //$this->userStr = $userStr;
        $this->setName($userStr);
    }

    /**
     * @return mixed
     */
    public function getUserStr()
    {
        //return $this->userStr;
        return $this->getName();
    }

    public function __toString() {
        return $this->getFullName();
    }

    public function getFullName() {
        $fullName = "";

        if( $this->getUser() ) {
            $fullName = $fullName . $this->getUser()."";
            return $fullName;
        }

        if( $this->getName() ) {
            if( $fullName ) {
                $fullName = $fullName . " " .$this->getName()."";
            } else {
                $fullName = $this->getName()."";
            }
        }

        //echo "fullName=".$fullName."<br>";
        return $fullName;
    }

    public function getFullNameWithDetails() {
        $fullName = $this->getFullName();

        if( $this->getUserWrapperSource() ) {
            if( $fullName ) {
                $fullName = $fullName . " " .$this->getUserWrapperSource()."";
            } else {
                $fullName = $this->getUserWrapperSource()."";
            }
        }

        //echo "fullName=".$fullName."<br>";
        return $fullName;
    }

    //get user id or user string
    //used for transformer
    public function getEntity() {

        if( $this->getId() ) {
            return $this->getId();
        }

        return $this->getFullName();
    }


}