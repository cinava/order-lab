<?php

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity(repositoryClass="Oleg\OrderformBundle\Repository\EncounterRepository")
 * @ORM\Table(name="scan_encounter")
 */
class Encounter extends ObjectAbstract
{

    /**
     * @ORM\OneToMany(targetEntity="EncounterName", mappedBy="encounter", cascade={"persist"})
     */
    protected $name;

    /**
     * Encounter Number
     * @ORM\OneToMany(targetEntity="EncounterNumber", mappedBy="encounter", cascade={"persist"})
     */
    protected $number;
    
    /**
     * parent
     * @ORM\ManyToOne(targetEntity="Patient", inversedBy="encounter")
     * @ORM\JoinColumn(name="patient", referencedColumnName="id")
     */
    protected $patient; 
    
    /**
     * Encounter might have many Procedures (children)
     * 
     * @ORM\OneToMany(targetEntity="Procedure", mappedBy="encounter")
     */
    protected $procedure;
    
    /**
     * @ORM\ManyToMany(targetEntity="OrderInfo", mappedBy="encounter")
     **/
    protected $orderinfo;


    //Patient's info: age, name, sex, date, history
    /**
     * @ORM\OneToMany(targetEntity="EncounterDate", mappedBy="encounter", cascade={"persist"})
     */
    protected $date;

    /**
     * @ORM\OneToMany(targetEntity="EncounterPatsuffix", mappedBy="encounter", cascade={"persist"})
     */
    protected $patsuffix;

    /**
     * @ORM\OneToMany(targetEntity="EncounterPatlastname", mappedBy="encounter", cascade={"persist"})
     */
    protected $patlastname;

    /**
     * @ORM\OneToMany(targetEntity="EncounterPatfirstname", mappedBy="encounter", cascade={"persist"})
     */
    protected $patfirstname;

    /**
     * @ORM\OneToMany(targetEntity="EncounterPatmiddlename", mappedBy="encounter", cascade={"persist"})
     */
    protected $patmiddlename;

    /**
     * @ORM\OneToMany(targetEntity="EncounterPatsex", mappedBy="encounter", cascade={"persist"})
     */
    protected $patsex;

    /**
     * @ORM\OneToMany(targetEntity="EncounterPatage", mappedBy="encounter", cascade={"persist"})
     */
    protected $patage;

    /**
     * @ORM\OneToMany(targetEntity="EncounterPathistory", mappedBy="encounter", cascade={"persist"})
     */
    protected $pathistory;


    ///////////////// additional extra fields not shown on scan order /////////////////
    /**
     * Encounter location
     * @ORM\OneToMany(targetEntity="EncounterLocation", mappedBy="encounter", cascade={"persist"})
     */
    private $location;

    /**
     * Encounter order
     * @ORM\OneToMany(targetEntity="EncounterOrder", mappedBy="encounter", cascade={"persist"})
     */
    private $order;

    /**
     * @ORM\OneToMany(targetEntity="EncounterInpatientinfo", mappedBy="encounter", cascade={"persist"})
     */
    private $inpatientinfo;
    ///////////////// EOF additional extra fields not shown on scan order /////////////////


    public function __construct( $withfields=false, $status='invalid', $provider=null, $source=null ) {
        parent::__construct($status,$provider,$source);
        $this->procedure = new ArrayCollection();

        //fields:
        $this->name = new ArrayCollection();
        $this->number = new ArrayCollection();
        $this->date = new ArrayCollection();

        $this->patsuffix = new ArrayCollection();
        $this->patlastname = new ArrayCollection();
        $this->patmiddlename = new ArrayCollection();
        $this->patfirstname = new ArrayCollection();

        $this->patsex = new ArrayCollection();
        $this->patage = new ArrayCollection();

        $this->pathistory = new ArrayCollection();

        //extra
        $this->location = new ArrayCollection();
        $this->order = new ArrayCollection();
        $this->inpatientinfo = new ArrayCollection();

        if( $withfields ) {
            $this->addName( new EncounterName($status,$provider,$source) );
            $this->addNumber( new EncounterNumber($status,$provider,$source) );
            $this->addDate( new EncounterDate($status,$provider,$source) );
            $this->addPatsuffix( new EncounterPatsuffix($status,$provider,$source) );
            $this->addPatlastname( new EncounterPatlastname($status,$provider,$source) );
            $this->addPatfirstname( new EncounterPatfirstname($status,$provider,$source) );
            $this->addPatmiddlename( new EncounterPatmiddlename($status,$provider,$source) );
            $this->addPatsex( new EncounterPatsex($status,$provider,$source) );
            $this->addPatage( new EncounterPatage($status,$provider,$source) );
            $this->addPathistory( new EncounterPathistory($status,$provider,$source) );

            //testing data structure
            $this->addExtraFields($status,$provider,$source);
        }
    }

    public function makeDependClone() {
        $this->name = $this->cloneDepend($this->name,$this);
        $this->number = $this->cloneDepend($this->number,$this);
        $this->date = $this->cloneDepend($this->date,$this);
        $this->patsuffix = $this->cloneDepend($this->patsuffix,$this);
        $this->patlastname = $this->cloneDepend($this->patlastname,$this);
        $this->patfirstname = $this->cloneDepend($this->patfirstname,$this);
        $this->patmiddlename = $this->cloneDepend($this->patmiddlename,$this);
        $this->patsex = $this->cloneDepend($this->patsex,$this);
        $this->patage = $this->cloneDepend($this->patage,$this);
        $this->pathistory = $this->cloneDepend($this->pathistory,$this);

        //extra fields
        $this->location = $this->cloneDepend($this->location,$this);
        $this->order = $this->cloneDepend($this->order,$this);
        $this->inpatientinfo = $this->cloneDepend($this->inpatientinfo,$this);
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }
    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }
    public function addDate($date)
    {
        if( $date == null ) {
            $date = new EncounterDate();
        }

        if( !$this->date->contains($date) ) {
            $this->date->add($date);
            $date->setEncounter($this);
        }

        return $this;
    }
    public function removeDate($date)
    {
        $this->date->removeElement($date);
    }

    /**
     * @param mixed $patage
     */
    public function setPatage($patage)
    {
        $this->patage = $patage;
    }
    /**
     * @return mixed
     */
    public function getPatage()
    {
        return $this->patage;
    }
    public function addPatage($patage)
    {
        if( $patage == null ) {
            $patage = new EncounterPatage();
        }

        if( !$this->patage->contains($patage) ) {
            $patage->setEncounter($this);
            $this->patage->add($patage);
        }

        return $this;
    }
    public function removePatage($patage)
    {
        $this->patage->removeElement($patage);
    }

    /**
     * @param mixed $pathistory
     */
    public function setPathistory($pathistory)
    {
        $this->pathistory = $pathistory;
    }
    /**
     * @return mixed
     */
    public function getPathistory()
    {
        return $this->pathistory;
    }
    public function addPathistory($pathistory)
    {
        if( $pathistory == null ) {
            $pathistory = new EncounterPathistory();
        }

        if( !$this->pathistory->contains($pathistory) ) {
            $pathistory->setEncounter($this);
            $this->pathistory->add($pathistory);
        }

        return $this;
    }
    public function removePathistory($pathistory)
    {
        $this->pathistory->removeElement($pathistory);
    }


    public function setPatsuffix($patsuffix)
    {
        $this->patsuffix = $patsuffix;
    }
    public function getPatsuffix()
    {
        return $this->patsuffix;
    }
    public function addPatsuffix($patsuffix)
    {
        if( $patsuffix == null ) {
            $patsuffix = new EncounterPatsuffix();
        }

        if( !$this->patsuffix->contains($patsuffix) ) {
            $patsuffix->setEncounter($this);
            $this->patsuffix->add($patsuffix);
        }

        return $this;
    }
    public function removePatsuffix($patsuffix)
    {
        $this->patsuffix->removeElement($patsuffix);
    }



    /**
     * @param mixed $patlastname
     */
    public function setPatlastname($patlastname)
    {
        $this->patlastname = $patlastname;
    }
    /**
     * @return mixed
     */
    public function getPatlastname()
    {
        return $this->patlastname;
    }
    public function addPatlastname($patlastname)
    {
        if( $patlastname == null ) {
            $patlastname = new EncounterPatlastname();
        }

        if( !$this->patlastname->contains($patlastname) ) {
            $patlastname->setEncounter($this);
            $this->patlastname->add($patlastname);
        }

        return $this;
    }
    public function removePatlastname($patlastname)
    {
        $this->patlastname->removeElement($patlastname);
    }


    /**
     * @param mixed $patfirstname
     */
    public function setPatfirstname($patfirstname)
    {
        $this->patfirstname = $patfirstname;
    }
    /**
     * @return mixed
     */
    public function getPatfirstname()
    {
        return $this->patfirstname;
    }
    public function addPatfirstname($patfirstname)
    {
        if( $patfirstname == null ) {
            $patfirstname = new EncounterPatfirstname();
        }

        if( !$this->patfirstname->contains($patfirstname) ) {
            $patfirstname->setEncounter($this);
            $this->patfirstname->add($patfirstname);
        }

        return $this;
    }
    public function removePatfirstname($patfirstname)
    {
        $this->patfirstname->removeElement($patfirstname);
    }

    /**
     * @param mixed $patmiddlename
     */
    public function setPatmiddlename($patmiddlename)
    {
        $this->patmiddlename = $patmiddlename;
    }
    /**
     * @return mixed
     */
    public function getPatmiddlename()
    {
        return $this->patmiddlename;
    }
    public function addPatmiddlename($patmiddlename)
    {
        if( $patmiddlename == null ) {
            $patmiddlename = new EncounterPatmiddlename();
        }

        if( !$this->patmiddlename->contains($patmiddlename) ) {
            $patmiddlename->setEncounter($this);
            $this->patmiddlename->add($patmiddlename);
        }

        return $this;
    }
    public function removePatmiddlename($patmiddlename)
    {
        $this->patmiddlename->removeElement($patmiddlename);
    }


    /**
     * @param mixed $patsex
     */
    public function setPatsex($patsex)
    {
        $this->patsex = $patsex;
    }
    /**
     * @return mixed
     */
    public function getPatsex()
    {
        return $this->patsex;
    }
    public function addPatsex($patsex)
    {
        if( $patsex == null ) {
            $patsex = new EncounterPatsex();
        }

        if( !$this->patsex->contains($patsex) ) {
            $patsex->setEncounter($this);
            $this->patsex->add($patsex);
        }

        return $this;
    }
    public function removePatsex($patsex)
    {
        $this->patsex->removeElement($patsex);
    }


    //Name
    public function getName() {
        return $this->name;
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function addName($name)
    {
        if( $name == null ) {
            $name = new EncounterName();
        }

        if( !$this->name->contains($name) ) {
            $name->setEncounter($this);
            $this->name->add($name);
        }

        return $this;
    }
    public function removeName($name)
    {
        $this->name->removeElement($name);
    }

    public function clearName()
    {
        $this->name->clear();
    }

    //Encounter Number
    public function getNumber() {
        return $this->number;
    }
    public function setNumber($number) {
        $this->number = $number;
    }
    public function addNumber($number)
    {
        if( $number ) {
            if( !$this->number->contains($number) ) {
                $this->number->add($number);
                $number->setEncounter($this);
            }
        }

        return $this;
    }
    public function removeNumber($number)
    {
        $this->number->removeElement($number);
    }

    public function clearNumber()
    {
        $this->number->clear();
    }

    /**
     * Add procedure
     *
     * @param \Oleg\OrderformBundle\Entity\Procedure $procedure
     * @return Encounter
     */
    public function addProcedure(\Oleg\OrderformBundle\Entity\Procedure $procedure)
    {
        if( !$this->procedure->contains($procedure) ) {
            $this->procedure->add($procedure);
            $procedure->setEncounter($this);
        }
    
        return $this;
    }
    /**
     * Remove procedure
     *
     * @param \Oleg\OrderformBundle\Entity\Procedure $procedure
     */
    public function removeProcedure(\Oleg\OrderformBundle\Entity\Procedure $procedure)
    {
        $this->procedure->removeElement($procedure);
    }
    /**
     * Get procedure
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProcedure()
    {
        return $this->procedure;
    }
    public function setProcedure(\Doctrine\Common\Collections\ArrayCollection $procedure)
    {
        $this->procedure = $procedure;
    }
    public function clearProcedure(){
        $this->procedure->clear();
    }

    /**
     * Set patient
     *
     * @param \Oleg\OrderformBundle\Entity\Patient $patient
     * @return Encounter
     */
    public function setPatient(\Oleg\OrderformBundle\Entity\Patient $patient = null)
    {
        $this->patient = $patient;
    
        return $this;
    }
    /**
     * Get patient
     *
     * @return \Oleg\OrderformBundle\Entity\Patient 
     */
    public function getPatient()
    {
        return $this->patient;
    }





    ///////////////////////// Extra fields /////////////////////////
    public function addExtraFields($status,$provider,$source) {
        $this->addLocation( new EncounterLocation($status,$provider,$source) );
        $this->addOrder( new EncounterOrder($status,$provider,$source) );
        $this->addInpatientinfo( new EncounterInpatientinfo($status,$provider,$source) );

    }

    public function getLocation()
    {
        return $this->location;
    }
    public function addLocation($location)
    {
        if( $location && !$this->location->contains($location) ) {
            $this->location->add($location);
            $location->setEncounter($this);
        }

        return $this;
    }
    public function removeLocation($location)
    {
        $this->location->removeElement($location);
    }

    public function getOrder()
    {
        return $this->order;
    }
    public function addOrder($order)
    {
        if( $order && !$this->order->contains($order) ) {
            $this->order->add($order);
            $order->setEncounter($this);
        }

        return $this;
    }
    public function removeOrder($order)
    {
        $this->order->removeElement($order);
    }

    public function getInpatientinfo()
    {
        return $this->inpatientinfo;
    }
    public function addInpatientinfo($inpatientinfo)
    {
        if( $inpatientinfo && !$this->inpatientinfo->contains($inpatientinfo) ) {
            $this->inpatientinfo->add($inpatientinfo);
            $inpatientinfo->setEncounter($this);
        }

        return $this;
    }
    public function removeInpatientinfo($inpatientinfo)
    {
        $this->inpatientinfo->removeElement($inpatientinfo);
    }
    ///////////////////////// EOF Extra fields /////////////////////////


    public function __toString() {

        $encNames = "";
        foreach( $this->getName() as $name ) {
            $encNames = $encNames . " name=". $name. " (provider=".$name->getProvider().", status=".$name->getStatus().") ";
        }

        $patfirstname = "";
        foreach( $this->getpatfirstname() as $name ) {
            $patfirstname = $patfirstname . " patfirstname=". $name. " (provider=".$name->getProvider().", status=".$name->getStatus().", alias=".$name->getAlias().") ";
        }

        $patlastname = "";
        foreach( $this->getpatlastname() as $name ) {
            $patlastname = $patlastname . " patlastname=". $name. " (provider=".$name->getProvider().", status=".$name->getStatus().", alias=".$name->getAlias().") ";
        }

        $patAge = "";
        foreach( $this->getPatage() as $name ) {
            $patAge = $patAge . " patage=". $name. " (provider=".$name->getProvider().", status=".$name->getStatus().") ";
        }

        $patSex = "";
        foreach( $this->getPatsex() as $name ) {
            $patSex = $patSex . " patsex=". $name. " (provider=".$name->getProvider().", status=".$name->getStatus().") ";
        }

        $hist = "";
        foreach( $this->getPathistory() as $name ) {
            $hist = $hist . " pathist=". $name. " (provider=".$name->getProvider().", status=".$name->getStatus().") ";
        }

        return 'Encounter: id=' . $this->id . ", patientFirstName=".$this->getPatient()->getFirstname()->first().
            ", patfirstname=" . $patfirstname .
            ", patlastname=" . $patlastname .
            ", patage=" . $patAge . ", patsex=".$patSex.", Clinical History=".$hist.
            ", encounterNameCount=" . count($this->getName()) . " => Names=".$encNames.
            ", encounterCount=" . count($this->number) .
            ": encounter->first=" . $this->number->first() .
            ", parentId=".$this->getParent()->getId().
            "; linked procedureCount=".count($this->procedure).":".$this->procedure->first();
    }


    //parent, children, key field methods
    public function setParent($parent)
    {
        $this->setPatient($parent);
        return $this;
    }

    public function getParent()
    {
        return $this->getPatient();
    }

    public function getChildren() {
        return $this->getProcedure();
    }

    public function addChildren($child) {
        $this->addProcedure($child);
    }

    public function removeChildren($child) {
        $this->removeProcedure($child);
    }

    public function setChildren($children) {
        $this->setProcedure($children);
    }
    
    //don't use 'get' because later repo functions relay on "get" keyword
    public function obtainKeyField() {
        return $this->getNumber();
    }

    public function obtainKeyFieldName() {
        return "number";
    }

    public function createKeyField() {
        $this->addNumber( new EncounterNumber() );
        return $this->obtainKeyField();
    }

    public function getArrayFields() {
        $fieldsArr = array(
            'Name','Number','Date','Patsuffix','Patlastname','Patfirstname','Patmiddlename','Patage','Patsex','Pathistory',
            //extra fields
            'Location', 'Order', 'Inpatientinfo'
        );
        return $fieldsArr;
    }

}