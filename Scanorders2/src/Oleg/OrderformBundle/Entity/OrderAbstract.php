<?php

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class OrderAbstract
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
     * @var \DateTime
     *
     * @ORM\Column(name="orderdate", type="datetime", nullable=true)
     *
     */
    private $orderdate;

    /**
     * @ORM\ManyToOne(targetEntity="Status", inversedBy="orderinfo", cascade={"persist"})
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="FormType", cascade={"persist"})
     * @ORM\JoinColumn(name="formtype_id", referencedColumnName="id")
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="User", cascade={"persist"})
     * @ORM\JoinTable(name="provider_orderinfo",
     *      joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="provider_id", referencedColumnName="id")}
     * )
     */
    private $provider;

    /**
     * @ORM\ManyToMany(targetEntity="User", cascade={"persist"})
     * @ORM\JoinTable(name="proxyuser_orderinfo",
     *      joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="proxyuser_id", referencedColumnName="id")}
     * )
     */
    protected $proxyuser;

    /**
     * @ORM\OneToMany(targetEntity="History", mappedBy="orderinfo", cascade={"persist"})
     */
    private $history;

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->provider = new ArrayCollection();
        $this->proxyuser = new ArrayCollection();
        $this->history = new ArrayCollection();
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

    public function setId($id)
    {
        $this->id = $id;
        return $id;
    }

    /**
    * @ORM\PrePersist
    */
    public function setOrderdate($date=null) {
        if( $date ) {
            $this->orderdate = $date;
        } else {
            $this->orderdate = new \DateTime();
        }
    }

    /**
     * Get orderdate
     *
     * @return \DateTime 
     */
    public function getOrderdate()
    {
        return $this->orderdate;
    }

    public function getHistory()
    {
        return $this->history;
    }

    public function addHistory($history)
    {
        if( !$this->history->contains($history) ) {
            $this->history->add($history);
        }
    }

    public function removeHistory($history)
    {
        $this->history->removeElement($history);
    }
    
    public function getStatus() {
        return $this->status;
    }

    public function getType() {
        return $this->type;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Set provider
     *
     * @param \stdClass $provider
     * @return OrderInfo
     */
    public function setProvider($provider)
    {
        if( is_array($provider ) ) {
            $this->provider = $provider;
        } else {
            $this->provider->clear();
            $this->provider->add($provider);
        }

        return $this;
    }

    /**
     * Get provider
     *
     * @return \stdClass
     */
    public function getProvider()
    {
        return $this->provider;
    }

    public function addProvider(\Oleg\OrderformBundle\Entity\User $provider)
    {
        if( !$this->provider->contains($provider) ) {
            $this->provider->add($provider);
        }

        return $this;
    }

    public function removeProvider(\Oleg\OrderformBundle\Entity\User $provider)
    {
        $this->provider->removeElement($provider);
    }


    public function addProxyuser(\Oleg\OrderformBundle\Entity\User $proxyuser)
    {
        if( $proxyuser ) {
            if( !$this->proxyuser->contains($proxyuser) ) {
                $this->proxyuser->add($proxyuser);
            }
        }

        return $this;
    }

    public function removeProxyuser(\Oleg\OrderformBundle\Entity\User $proxyuser)
    {
        $this->proxyuser->removeElement($proxyuser);
    }

    /**
     * @param mixed $proxyuser
     */
    public function setProxyuser($proxyuser)
    {
        if( $proxyuser ) {
            if( is_array($proxyuser) ) {
                $this->proxyuser = $proxyuser;
            } else {
                $this->proxyuser->clear();
                $this->proxyuser->add($proxyuser);
            }
        }

    }

    /**
     * @return mixed
     */
    public function getProxyuser()
    {
        return $this->proxyuser;
    }



}