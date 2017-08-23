<?php

namespace Proxies\__CG__\Oleg\OrderformBundle\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class EncounterAttendingPhysician extends \Oleg\OrderformBundle\Entity\EncounterAttendingPhysician implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = [];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', 'encounter', 'field', '' . "\0" . 'Oleg\\OrderformBundle\\Entity\\EncounterAttendingPhysician' . "\0" . 'attendingPhysicianSpecialty', '' . "\0" . 'Oleg\\OrderformBundle\\Entity\\EncounterAttendingPhysician' . "\0" . 'attendingPhysicianPhone', '' . "\0" . 'Oleg\\OrderformBundle\\Entity\\EncounterAttendingPhysician' . "\0" . 'attendingPhysicianEmail', 'id', 'provider', 'status', 'source', 'creationdate', 'message', 'updateDate', 'updateSource', 'updateAuthor', 'dqeventlog', 'changeFieldArr'];
        }

        return ['__isInitialized__', 'encounter', 'field', '' . "\0" . 'Oleg\\OrderformBundle\\Entity\\EncounterAttendingPhysician' . "\0" . 'attendingPhysicianSpecialty', '' . "\0" . 'Oleg\\OrderformBundle\\Entity\\EncounterAttendingPhysician' . "\0" . 'attendingPhysicianPhone', '' . "\0" . 'Oleg\\OrderformBundle\\Entity\\EncounterAttendingPhysician' . "\0" . 'attendingPhysicianEmail', 'id', 'provider', 'status', 'source', 'creationdate', 'message', 'updateDate', 'updateSource', 'updateAuthor', 'dqeventlog', 'changeFieldArr'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (EncounterAttendingPhysician $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * {@inheritDoc}
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);

        parent::__clone();
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getAttendingPhysicianSpecialty()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAttendingPhysicianSpecialty', []);

        return parent::getAttendingPhysicianSpecialty();
    }

    /**
     * {@inheritDoc}
     */
    public function setAttendingPhysicianSpecialty($attendingPhysicianSpecialty)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAttendingPhysicianSpecialty', [$attendingPhysicianSpecialty]);

        return parent::setAttendingPhysicianSpecialty($attendingPhysicianSpecialty);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttendingPhysicianPhone()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAttendingPhysicianPhone', []);

        return parent::getAttendingPhysicianPhone();
    }

    /**
     * {@inheritDoc}
     */
    public function setAttendingPhysicianPhone($attendingPhysicianPhone)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAttendingPhysicianPhone', [$attendingPhysicianPhone]);

        return parent::setAttendingPhysicianPhone($attendingPhysicianPhone);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttendingPhysicianEmail()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAttendingPhysicianEmail', []);

        return parent::getAttendingPhysicianEmail();
    }

    /**
     * {@inheritDoc}
     */
    public function setAttendingPhysicianEmail($attendingPhysicianEmail)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAttendingPhysicianEmail', [$attendingPhysicianEmail]);

        return parent::setAttendingPhysicianEmail($attendingPhysicianEmail);
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEmail', []);

        return parent::getEmail();
    }

    /**
     * {@inheritDoc}
     */
    public function setEncounter(\Oleg\OrderformBundle\Entity\Encounter $encounter = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEncounter', [$encounter]);

        return parent::setEncounter($encounter);
    }

    /**
     * {@inheritDoc}
     */
    public function getEncounter()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEncounter', []);

        return parent::getEncounter();
    }

    /**
     * {@inheritDoc}
     */
    public function setField($field = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setField', [$field]);

        return parent::setField($field);
    }

    /**
     * {@inheritDoc}
     */
    public function getField()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getField', []);

        return parent::getField();
    }

    /**
     * {@inheritDoc}
     */
    public function setParent($parent)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setParent', [$parent]);

        return parent::setParent($parent);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getParent', []);

        return parent::getParent();
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setCreationdate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCreationdate', []);

        return parent::setCreationdate();
    }

    /**
     * {@inheritDoc}
     */
    public function getCreationdate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCreationdate', []);

        return parent::getCreationdate();
    }

    /**
     * {@inheritDoc}
     */
    public function setProvider($provider)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProvider', [$provider]);

        return parent::setProvider($provider);
    }

    /**
     * {@inheritDoc}
     */
    public function getProvider()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProvider', []);

        return parent::getProvider();
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($status)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStatus', [$status]);

        return parent::setStatus($status);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatus', []);

        return parent::getStatus();
    }

    /**
     * {@inheritDoc}
     */
    public function setSource($source)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSource', [$source]);

        return parent::setSource($source);
    }

    /**
     * {@inheritDoc}
     */
    public function getSource()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSource', []);

        return parent::getSource();
    }

    /**
     * {@inheritDoc}
     */
    public function setMessage($message)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMessage', [$message]);

        return parent::setMessage($message);
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMessage', []);

        return parent::getMessage();
    }

    /**
     * {@inheritDoc}
     */
    public function setDqeventlog(\Oleg\OrderformBundle\Entity\DataQualityEventLog $dqeventlog)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDqeventlog', [$dqeventlog]);

        return parent::setDqeventlog($dqeventlog);
    }

    /**
     * {@inheritDoc}
     */
    public function getDqeventlog()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDqeventlog', []);

        return parent::getDqeventlog();
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdateDate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUpdateDate', []);

        return parent::getUpdateDate();
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdateDate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUpdateDate', []);

        return parent::setUpdateDate();
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdateSource()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUpdateSource', []);

        return parent::getUpdateSource();
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdateSource($updateSource)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUpdateSource', [$updateSource]);

        return parent::setUpdateSource($updateSource);
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdateAuthor()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUpdateAuthor', []);

        return parent::getUpdateAuthor();
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdateAuthor($updateAuthor)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUpdateAuthor', [$updateAuthor]);

        return parent::setUpdateAuthor($updateAuthor);
    }

    /**
     * {@inheritDoc}
     */
    public function setFieldChangeArray($fieldName, $oldValue, $newValue)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFieldChangeArray', [$fieldName, $oldValue, $newValue]);

        return parent::setFieldChangeArray($fieldName, $oldValue, $newValue);
    }

    /**
     * {@inheritDoc}
     */
    public function getChangeFieldArr()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getChangeFieldArr', []);

        return parent::getChangeFieldArr();
    }

    /**
     * {@inheritDoc}
     */
    public function setChangeFieldArr($changeFieldArr)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setChangeFieldArr', [$changeFieldArr]);

        return parent::setChangeFieldArr($changeFieldArr);
    }

    /**
     * {@inheritDoc}
     */
    public function formatDataToString($data)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'formatDataToString', [$data]);

        return parent::formatDataToString($data);
    }

    /**
     * {@inheritDoc}
     */
    public function formatTimeToString($time)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'formatTimeToString', [$time]);

        return parent::formatTimeToString($time);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__toString', []);

        return parent::__toString();
    }

    /**
     * {@inheritDoc}
     */
    public function capitalizeIfNotAllCapital($s)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'capitalizeIfNotAllCapital', [$s]);

        return parent::capitalizeIfNotAllCapital($s);
    }

}
