<?php

namespace Oleg\OrderformBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Oleg\UserdirectoryBundle\Entity\BaseCompositeNode;
use Oleg\UserdirectoryBundle\Entity\ComponentCategoryInterface;
use Oleg\UserdirectoryBundle\Entity\CompositeNodeInterface;
use Symfony\Component\Validator\Constraints as Assert;


//This list has a link to the patient list (i.e. PathologyCallComplexPatients) via entityNamespace, entityName, entityId

/**
 * Use Composite pattern:
 * The composite pattern describes that a group of objects is to be treated in the same
 * way as a single instance of an object. The intent of a composite is to "compose" objects into tree structures
 * to represent part-whole hierarchies. Implementing the composite pattern lets clients treat individual objects
 * and compositions uniformly.
 * Use Doctrine Extension Tree for tree manipulation.
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass="Oleg\UserdirectoryBundle\Repository\TreeRepository")
 * @ORM\Table(
 *  name="scan_patientListHierarchy",
 *  indexes={
 *      @ORM\Index( name="patientListHierarchy_name_idx", columns={"name"} ),
 *  }
 * )
 */
class PatientListHierarchy extends BaseCompositeNode {

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="PatientListHierarchy", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     **/
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="PatientListHierarchy", mappedBy="parent", cascade={"persist","remove"})
     * @ORM\OrderBy({"lft" = "ASC"})
     **/
    protected $children;

    /**
     * @ORM\OneToMany(targetEntity="PatientListHierarchy", mappedBy="original", cascade={"persist"})
     **/
    protected $synonyms;

    /**
     * @ORM\ManyToOne(targetEntity="PatientListHierarchy", inversedBy="synonyms", cascade={"persist"})
     * @ORM\JoinColumn(name="original_id", referencedColumnName="id", nullable=true)
     **/
    protected $original;



    /**
     * Organizational Group Types - mapper between the level number and level title.
     * For example, OrganizationalGroupType with level=1, set this level to 1.
     * Default types have a positive level numbers, all other types have negative level numbers.
     *
     * @ORM\ManyToOne(targetEntity="PatientListHierarchyGroupType", cascade={"persist"})
     */
    private $organizationalGroupType;

    /**
     * @ORM\ManyToOne(targetEntity="Oleg\OrderformBundle\Entity\Patient", cascade={"persist"})
     */
    private $patient;


    public function __construct() {
        parent::__construct();
    }



    /**
     * @return mixed
     */
    public function getOrganizationalGroupType()
    {
        return $this->organizationalGroupType;
    }

    /**
     * @param mixed $organizationalGroupType
     */
    public function setOrganizationalGroupType($organizationalGroupType)
    {
        $this->organizationalGroupType = $organizationalGroupType;
    }


    /**
     * @return mixed
     */
    public function getPatient()
    {
        return $this->patient;
    }

    /**
     * @param mixed $patient
     */
    public function setPatient($patient)
    {
        $this->patient = $patient;
    }





    public function getClassName() {
        return "PatientListHierarchy";
    }




    public function __toString() {
        $parentName = "";
        if( $this->getParent() ) {
            $parentName = ", parent=".$this->getParent()->getName();
        }
        $patientName = "";
        if( $this->getPatient() ) {
            $patientName = ", patient=".$this->getPatient()->obtainPatientInfoTitle();
        }
        return "Patient List: ".$this->getName().", level=".$this->getLevel().", orderinlist=".$this->getOrderinlist().$parentName.$patientName;
    }




}