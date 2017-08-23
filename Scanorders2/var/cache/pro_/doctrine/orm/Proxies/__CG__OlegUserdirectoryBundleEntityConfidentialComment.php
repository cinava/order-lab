<?php

namespace Proxies\__CG__\Oleg\UserdirectoryBundle\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class ConfidentialComment extends \Oleg\UserdirectoryBundle\Entity\ConfidentialComment implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', 'user', 'documents', 'id', 'author', 'updateAuthor', 'updateAuthorRoles', 'type', 'status', 'createdate', 'updatedate', 'orderinlist'];
        }

        return ['__isInitialized__', 'user', 'documents', 'id', 'author', 'updateAuthor', 'updateAuthorRoles', 'type', 'status', 'createdate', 'updatedate', 'orderinlist'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (ConfidentialComment $proxy) {
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
    public function __toString()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__toString', []);

        return parent::__toString();
    }

    /**
     * {@inheritDoc}
     */
    public function setComment($comment)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setComment', [$comment]);

        return parent::setComment($comment);
    }

    /**
     * {@inheritDoc}
     */
    public function getComment()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getComment', []);

        return parent::getComment();
    }

    /**
     * {@inheritDoc}
     */
    public function setCommentType($commentType)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCommentType', [$commentType]);

        return parent::setCommentType($commentType);
    }

    /**
     * {@inheritDoc}
     */
    public function setCommentTypeList($commentType)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCommentTypeList', [$commentType]);

        return parent::setCommentTypeList($commentType);
    }

    /**
     * {@inheritDoc}
     */
    public function getCommentType()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCommentType', []);

        return parent::getCommentType();
    }

    /**
     * {@inheritDoc}
     */
    public function setCommentTypeStr($commentTypeStr)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCommentTypeStr', [$commentTypeStr]);

        return parent::setCommentTypeStr($commentTypeStr);
    }

    /**
     * {@inheritDoc}
     */
    public function getCommentTypeStr()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCommentTypeStr', []);

        return parent::getCommentTypeStr();
    }

    /**
     * {@inheritDoc}
     */
    public function addDocument($document)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addDocument', [$document]);

        return parent::addDocument($document);
    }

    /**
     * {@inheritDoc}
     */
    public function removeDocument($document)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeDocument', [$document]);

        return parent::removeDocument($document);
    }

    /**
     * {@inheritDoc}
     */
    public function getDocuments()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDocuments', []);

        return parent::getDocuments();
    }

    /**
     * {@inheritDoc}
     */
    public function setDocuments($documents)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDocuments', [$documents]);

        return parent::setDocuments($documents);
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
