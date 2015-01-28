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
     * status: valid, invalid
     * @ORM\Column(type="string", nullable=true)
     */
    protected $status;

//    /**
//     * default: 'scanorder'. source="import_from_Epic" or source="import_from_CoPath"
//     * @ORM\Column(type="string", nullable=true)
//     */
//    protected $source;
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
     * @ORM\ManyToOne(targetEntity="OrderInfo", cascade={"persist"})
     * @ORM\JoinColumn(name="orderinfo", referencedColumnName="id", nullable=true)
     */
    protected $orderinfo;


    /**
     * @ORM\OneToOne(targetEntity="DataQualityEventLog")
     * @ORM\JoinColumn(name="dqeventlog", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $dqeventlog;


    public function __construct( $status = 'valid', $provider = null, $source = null )
    {
        $this->status = $status;
        $this->provider = $provider;
        $this->source = $source;
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
     * @param mixed $orderinfo
     */
    public function setOrderinfo($orderinfo)
    {
        $this->orderinfo = $orderinfo;
    }

    /**
     * @return mixed
     */
    public function getOrderinfo()
    {
        return $this->orderinfo;
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





    public function __toString() {
        return $this->field."";
    }

}