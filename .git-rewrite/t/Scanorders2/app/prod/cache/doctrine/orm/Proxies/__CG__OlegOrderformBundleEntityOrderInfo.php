<?php

namespace Proxies\__CG__\Oleg\OrderformBundle\Entity;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class OrderInfo extends \Oleg\OrderformBundle\Entity\OrderInfo implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["id"];
        }
        $this->__load();
        return parent::getId();
    }

    public function setOrderdate()
    {
        $this->__load();
        return parent::setOrderdate();
    }

    public function getOrderdate()
    {
        $this->__load();
        return parent::getOrderdate();
    }

    public function setPathologyService($pathologyService)
    {
        $this->__load();
        return parent::setPathologyService($pathologyService);
    }

    public function getPathologyService()
    {
        $this->__load();
        return parent::getPathologyService();
    }

    public function setPriority($priority)
    {
        $this->__load();
        return parent::setPriority($priority);
    }

    public function getPriority()
    {
        $this->__load();
        return parent::getPriority();
    }

    public function getScandeadline()
    {
        $this->__load();
        return parent::getScandeadline();
    }

    public function getReturnoption()
    {
        $this->__load();
        return parent::getReturnoption();
    }

    public function setScandeadline($scandeadline)
    {
        $this->__load();
        return parent::setScandeadline($scandeadline);
    }

    public function setReturnoption($returnoption)
    {
        $this->__load();
        return parent::setReturnoption($returnoption);
    }

    public function setSlideDelivery($slideDelivery)
    {
        $this->__load();
        return parent::setSlideDelivery($slideDelivery);
    }

    public function getSlideDelivery()
    {
        $this->__load();
        return parent::getSlideDelivery();
    }

    public function setReturnSlide($returnSlide)
    {
        $this->__load();
        return parent::setReturnSlide($returnSlide);
    }

    public function getReturnSlide()
    {
        $this->__load();
        return parent::getReturnSlide();
    }

    public function setProvider($provider)
    {
        $this->__load();
        return parent::setProvider($provider);
    }

    public function getProvider()
    {
        $this->__load();
        return parent::getProvider();
    }

    public function getStatus()
    {
        $this->__load();
        return parent::getStatus();
    }

    public function getType()
    {
        $this->__load();
        return parent::getType();
    }

    public function setStatus($status)
    {
        $this->__load();
        return parent::setStatus($status);
    }

    public function setType($type)
    {
        $this->__load();
        return parent::setType($type);
    }

    public function addPatient(\Oleg\OrderformBundle\Entity\Patient $patient)
    {
        $this->__load();
        return parent::addPatient($patient);
    }

    public function removePatient(\Oleg\OrderformBundle\Entity\Patient $patient)
    {
        $this->__load();
        return parent::removePatient($patient);
    }

    public function getPatient()
    {
        $this->__load();
        return parent::getPatient();
    }

    public function addSlide(\Oleg\OrderformBundle\Entity\Slide $slide)
    {
        $this->__load();
        return parent::addSlide($slide);
    }

    public function removeSlide(\Oleg\OrderformBundle\Entity\Slide $slide)
    {
        $this->__load();
        return parent::removeSlide($slide);
    }

    public function getSlide()
    {
        $this->__load();
        return parent::getSlide();
    }

    public function __toString()
    {
        $this->__load();
        return parent::__toString();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'orderdate', 'pathologyService', 'status', 'type', 'priority', 'scandeadline', 'returnoption', 'slideDelivery', 'returnSlide', 'provider', 'patient', 'slide');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields as $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}