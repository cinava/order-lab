<?php

namespace Proxies\__CG__\Oleg\UserdirectoryBundle\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Lecture extends \Oleg\UserdirectoryBundle\Entity\Lecture implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', 'user', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\Lecture' . "\0" . 'lectureDate', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\Lecture' . "\0" . 'importance', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\Lecture' . "\0" . 'title', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\Lecture' . "\0" . 'organization', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\Lecture' . "\0" . 'city', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\Lecture' . "\0" . 'state', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\Lecture' . "\0" . 'country', 'id', 'author', 'updateAuthor', 'updateAuthorRoles', 'type', 'status', 'createdate', 'updatedate', 'orderinlist'];
        }

        return ['__isInitialized__', 'user', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\Lecture' . "\0" . 'lectureDate', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\Lecture' . "\0" . 'importance', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\Lecture' . "\0" . 'title', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\Lecture' . "\0" . 'organization', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\Lecture' . "\0" . 'city', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\Lecture' . "\0" . 'state', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\Lecture' . "\0" . 'country', 'id', 'author', 'updateAuthor', 'updateAuthorRoles', 'type', 'status', 'createdate', 'updatedate', 'orderinlist'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Lecture $proxy) {
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
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
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
    public function setImportance($importance)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setImportance', [$importance]);

        return parent::setImportance($importance);
    }

    /**
     * {@inheritDoc}
     */
    public function getImportance()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getImportance', []);

        return parent::getImportance();
    }

    /**
     * {@inheritDoc}
     */
    public function setLectureDate($lectureDate)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLectureDate', [$lectureDate]);

        return parent::setLectureDate($lectureDate);
    }

    /**
     * {@inheritDoc}
     */
    public function getLectureDate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLectureDate', []);

        return parent::getLectureDate();
    }

    /**
     * {@inheritDoc}
     */
    public function setUser($user)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUser', [$user]);

        return parent::setUser($user);
    }

    /**
     * {@inheritDoc}
     */
    public function getUser()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUser', []);

        return parent::getUser();
    }

    /**
     * {@inheritDoc}
     */
    public function setTitle($title)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTitle', [$title]);

        return parent::setTitle($title);
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTitle', []);

        return parent::getTitle();
    }

    /**
     * {@inheritDoc}
     */
    public function setCity($city)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCity', [$city]);

        return parent::setCity($city);
    }

    /**
     * {@inheritDoc}
     */
    public function getCity()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCity', []);

        return parent::getCity();
    }

    /**
     * {@inheritDoc}
     */
    public function setCountry($country)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCountry', [$country]);

        return parent::setCountry($country);
    }

    /**
     * {@inheritDoc}
     */
    public function getCountry()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCountry', []);

        return parent::getCountry();
    }

    /**
     * {@inheritDoc}
     */
    public function setOrganization($organization)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOrganization', [$organization]);

        return parent::setOrganization($organization);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrganization()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOrganization', []);

        return parent::getOrganization();
    }

    /**
     * {@inheritDoc}
     */
    public function setState($state)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setState', [$state]);

        return parent::setState($state);
    }

    /**
     * {@inheritDoc}
     */
    public function getState()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getState', []);

        return parent::getState();
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
    public function setAuthor($author)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAuthor', [$author]);

        return parent::setAuthor($author);
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthor()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAuthor', []);

        return parent::getAuthor();
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCreatedate', []);

        return parent::setCreatedate();
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCreatedate', []);

        return parent::getCreatedate();
    }

    /**
     * {@inheritDoc}
     */
    public function setType($type)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setType', [$type]);

        return parent::setType($type);
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getType', []);

        return parent::getType();
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
    public function setOrderinlist($orderinlist)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOrderinlist', [$orderinlist]);

        return parent::setOrderinlist($orderinlist);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrderinlist()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOrderinlist', []);

        return parent::getOrderinlist();
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
    public function getUpdateAuthor()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUpdateAuthor', []);

        return parent::getUpdateAuthor();
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdatedate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUpdatedate', []);

        return parent::setUpdatedate();
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdatedate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUpdatedate', []);

        return parent::getUpdatedate();
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdateAuthorRoles()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUpdateAuthorRoles', []);

        return parent::getUpdateAuthorRoles();
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdateAuthorRoles($roles)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUpdateAuthorRoles', [$roles]);

        return parent::setUpdateAuthorRoles($roles);
    }

    /**
     * {@inheritDoc}
     */
    public function addUpdateAuthorRole($role)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addUpdateAuthorRole', [$role]);

        return parent::addUpdateAuthorRole($role);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusStr()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatusStr', []);

        return parent::getStatusStr();
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusStrByStatus($status)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatusStrByStatus', [$status]);

        return parent::getStatusStrByStatus($status);
    }

}
