<?php

namespace Proxies\__CG__\Oleg\UserdirectoryBundle\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class EmploymentStatus extends \Oleg\UserdirectoryBundle\Entity\EmploymentStatus implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'user', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'hireDate', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'employmentType', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'terminationDate', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'terminationType', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'terminationReason', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'jobDescriptionSummary', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'jobDescription', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'attachmentContainer', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'institution', 'id', 'author', 'updateAuthor', 'updateAuthorRoles', 'type', 'status', 'createdate', 'updatedate', 'orderinlist'];
        }

        return ['__isInitialized__', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'user', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'hireDate', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'employmentType', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'terminationDate', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'terminationType', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'terminationReason', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'jobDescriptionSummary', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'jobDescription', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'attachmentContainer', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\EmploymentStatus' . "\0" . 'institution', 'id', 'author', 'updateAuthor', 'updateAuthorRoles', 'type', 'status', 'createdate', 'updatedate', 'orderinlist'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (EmploymentStatus $proxy) {
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
    public function setHireDate($hireDate)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setHireDate', [$hireDate]);

        return parent::setHireDate($hireDate);
    }

    /**
     * {@inheritDoc}
     */
    public function getHireDate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getHireDate', []);

        return parent::getHireDate();
    }

    /**
     * {@inheritDoc}
     */
    public function setTerminationDate($terminationDate)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTerminationDate', [$terminationDate]);

        return parent::setTerminationDate($terminationDate);
    }

    /**
     * {@inheritDoc}
     */
    public function getTerminationDate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTerminationDate', []);

        return parent::getTerminationDate();
    }

    /**
     * {@inheritDoc}
     */
    public function setTerminationReason($terminationReason)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTerminationReason', [$terminationReason]);

        return parent::setTerminationReason($terminationReason);
    }

    /**
     * {@inheritDoc}
     */
    public function getTerminationReason()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTerminationReason', []);

        return parent::getTerminationReason();
    }

    /**
     * {@inheritDoc}
     */
    public function setTerminationType($terminationType)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTerminationType', [$terminationType]);

        return parent::setTerminationType($terminationType);
    }

    /**
     * {@inheritDoc}
     */
    public function getTerminationType()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTerminationType', []);

        return parent::getTerminationType();
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
    public function setEmploymentType($employmentType)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEmploymentType', [$employmentType]);

        return parent::setEmploymentType($employmentType);
    }

    /**
     * {@inheritDoc}
     */
    public function getEmploymentType()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEmploymentType', []);

        return parent::getEmploymentType();
    }

    /**
     * {@inheritDoc}
     */
    public function setAttachmentContainer($attachmentContainer)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAttachmentContainer', [$attachmentContainer]);

        return parent::setAttachmentContainer($attachmentContainer);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttachmentContainer()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAttachmentContainer', []);

        return parent::getAttachmentContainer();
    }

    /**
     * {@inheritDoc}
     */
    public function setJobDescription($jobDescription)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setJobDescription', [$jobDescription]);

        return parent::setJobDescription($jobDescription);
    }

    /**
     * {@inheritDoc}
     */
    public function getJobDescription()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getJobDescription', []);

        return parent::getJobDescription();
    }

    /**
     * {@inheritDoc}
     */
    public function setJobDescriptionSummary($jobDescriptionSummary)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setJobDescriptionSummary', [$jobDescriptionSummary]);

        return parent::setJobDescriptionSummary($jobDescriptionSummary);
    }

    /**
     * {@inheritDoc}
     */
    public function getJobDescriptionSummary()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getJobDescriptionSummary', []);

        return parent::getJobDescriptionSummary();
    }

    /**
     * {@inheritDoc}
     */
    public function getInstitution()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getInstitution', []);

        return parent::getInstitution();
    }

    /**
     * {@inheritDoc}
     */
    public function setInstitution($institution)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setInstitution', [$institution]);

        return parent::setInstitution($institution);
    }

    /**
     * {@inheritDoc}
     */
    public function createAttachmentDocument()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'createAttachmentDocument', []);

        return parent::createAttachmentDocument();
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
