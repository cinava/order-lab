<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 10/24/13
 * Time: 12:14 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;


/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class ArrayFieldAbstract {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

     /**
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\User")
     * @ORM\JoinColumn(name="provider", referencedColumnName="id")
     */
    protected $provider;

    /**
     * status: valid, invalid, alias
     * @ORM\Column(type="string", nullable=true)
     */
    protected $status;

    //default: 'scanorder'. Other values (old): "import_from_Epic", "import_from_CoPath"
    /**
     * @ORM\ManyToOne(targetEntity="Oleg\UserdirectoryBundle\Entity\SourceSystemList")
     * @ORM\JoinColumn(name="source_id", referencedColumnName="id", nullable=true)
     */
    protected $source;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $creationdate;

    /**
     * @ORM\ManyToOne(targetEntity="Message", cascade={"persist"})
     * @ORM\JoinColumn(name="message", referencedColumnName="id", nullable=true)
     */
    protected $message;


    /**
     * @ORM\OneToOne(targetEntity="DataQualityEventLog")
     * @ORM\JoinColumn(name="dqeventlog", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $dqeventlog;

    private $className;

    protected $changeFieldArr  = array();


    public function __construct( $status = 'valid', $provider = null, $source = null )
    {
        $this->status = $status;
        $this->provider = $provider;
        $this->source = $source;

        $class = new \ReflectionClass($this);
        $this->className = $class->getShortName();
    }

    public function __clone() {
        if( $this->getId() ) {
            //echo "field ".$this->getId()." set id to null <br>";
            $this->setId(null);
        }
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreationdate()
    {
        $this->creationdate = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getCreationdate()
    {
        return $this->creationdate;
    }

    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        //echo $this->getId().": change status=".$status."<br>";
        $this->setFieldChangeArray("status",$this->status,$status);
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param \Oleg\OrderformBundle\Entity\DataQualityEventLog $dqeventlog
     */
    public function setDqeventlog(DataQualityEventLog $dqeventlog)
    {
        $this->dqeventlog = $dqeventlog;
    }

    /**
     * @return mixed
     */
    public function getDqeventlog()
    {
        return $this->dqeventlog;
    }


    public function setFieldChangeArray($fieldName,$oldValue,$newValue) {
        //echo $this->getId().": setFieldChangeArray $fieldName: old=".$oldValue."; new=".$newValue."<br>";
        if( $oldValue != $newValue ) {
            //echo "diff !!! ".$this->getId().": setFieldChangeArray $fieldName: old=".$oldValue."; new=".$newValue."<br>";
            if( $this->className ) {
                $className = $this->className;
            } else {
                $class = new \ReflectionClass($this);
                $className = $class->getShortName();
            }
            //echo $className.": parent id=".$this->getParent()->getId()."<br>";

            if( $this->getParent() ) {
                $holder = $this->getParent()->getHolderPatient();
                //echo "holder=".$holder."<br>";
                //echo "parent ok: this id=".$this->getId()."<br>";

                $changeFieldArr = array();

                if( $fieldName == "status" ) {
                    $fieldStr = $this->formatDataToString($this->getField());
                    $changeFieldArr[$className][$this->getId()][$fieldName]['old'] = $oldValue . " (" . $fieldStr . ")";
                    $changeFieldArr[$className][$this->getId()][$fieldName]['new'] = $newValue . " (" . $fieldStr . ")";
                    //$holder->addChangeObjectArr($changeFieldArr);
                } else {
                    $changeFieldArr[$className][$this->getId()][$fieldName]['old'] = $oldValue;
                    $changeFieldArr[$className][$this->getId()][$fieldName]['new'] = $newValue;
                }

                $holder->addChangeObjectArr($changeFieldArr);

//                echo "changeFieldArr:<br><pre>";
//                echo print_r($changeFieldArr);
//                echo "</pre>";
            } else {
                //$changeFieldArr = $this->changeFieldArr;
                //echo $fieldName.": no parent!!! <br>";
                //echo "this id=".$this->getId()."<br>";
                //echo "parent=".$this->getPatient()."<br>";
                //$changeFieldArr
                $changeFieldArr = array();
                $changeFieldArr[$className][$this->getId()][$fieldName]['old'] = $oldValue;
                $changeFieldArr[$className][$this->getId()][$fieldName]['new'] = $newValue;
                $this->setChangeFieldArr($changeFieldArr);

//                echo "changeFieldArr:<br><pre>";
//                echo print_r($this->changeFieldArr);
//                echo "</pre>";
            }

//            $changeObjectArr[$className][$this->getId()][$fieldName]['old'] = $oldValue;
//            $changeObjectArr[$className][$this->getId()][$fieldName]['new'] = $newValue;
//            $holder->setChangeObjectArr($changeObjectArr);
        }
    }

    /**
     * @return array
     */
    public function getChangeFieldArr()
    {
        return $this->changeFieldArr;
    }

    /**
     * @param array $changeFieldArr
     */
    public function setChangeFieldArr($changeFieldArr)
    {
        $this->changeFieldArr = $changeFieldArr;
    }


    public function formatDataToString($data) {
        if( $data && $data instanceof \DateTime ) {
            $transformer = new DateTimeToStringTransformer(null, null, 'Y-m-d');
            $dateStr = $transformer->transform($data);
            return $dateStr."";
        } else {
            return $data."";
        }
    }

    public function __toString() {
        return $this->field."";
    }

}