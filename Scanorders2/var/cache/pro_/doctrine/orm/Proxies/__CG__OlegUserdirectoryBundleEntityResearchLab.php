<?php

namespace Proxies\__CG__\Oleg\UserdirectoryBundle\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class ResearchLab extends \Oleg\UserdirectoryBundle\Entity\ResearchLab implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', 'synonyms', 'original', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'institution', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'user', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'foundedDate', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'dissolvedDate', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'location', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'weblink', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'comments', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'commentDummy', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'pis', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'piDummy', 'id', 'name', 'abbreviation', 'shortname', 'description', 'type', 'creator', 'createdate', 'updatedby', 'updatedon', 'orderinlist', 'updateAuthorRoles', 'fulltitle', 'linkToListId', 'objectType', 'entityId', 'entityNamespace', 'entityName', 'version'];
        }

        return ['__isInitialized__', 'synonyms', 'original', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'institution', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'user', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'foundedDate', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'dissolvedDate', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'location', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'weblink', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'comments', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'commentDummy', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'pis', '' . "\0" . 'Oleg\\UserdirectoryBundle\\Entity\\ResearchLab' . "\0" . 'piDummy', 'id', 'name', 'abbreviation', 'shortname', 'description', 'type', 'creator', 'createdate', 'updatedby', 'updatedon', 'orderinlist', 'updateAuthorRoles', 'fulltitle', 'linkToListId', 'objectType', 'entityId', 'entityNamespace', 'entityName', 'version'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (ResearchLab $proxy) {
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
    public function getComments()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getComments', []);

        return parent::getComments();
    }

    /**
     * {@inheritDoc}
     */
    public function addComment($comment)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addComment', [$comment]);

        return parent::addComment($comment);
    }

    /**
     * {@inheritDoc}
     */
    public function removeComment($comment)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeComment', [$comment]);

        return parent::removeComment($comment);
    }

    /**
     * {@inheritDoc}
     */
    public function getPis()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPis', []);

        return parent::getPis();
    }

    /**
     * {@inheritDoc}
     */
    public function addPi($pi)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addPi', [$pi]);

        return parent::addPi($pi);
    }

    /**
     * {@inheritDoc}
     */
    public function removePi($pi)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removePi', [$pi]);

        return parent::removePi($pi);
    }

    /**
     * {@inheritDoc}
     */
    public function setDissolvedDate($dissolvedDate)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDissolvedDate', [$dissolvedDate]);

        return parent::setDissolvedDate($dissolvedDate);
    }

    /**
     * {@inheritDoc}
     */
    public function getDissolvedDate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDissolvedDate', []);

        return parent::getDissolvedDate();
    }

    /**
     * {@inheritDoc}
     */
    public function setFoundedDate($foundedDate)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFoundedDate', [$foundedDate]);

        return parent::setFoundedDate($foundedDate);
    }

    /**
     * {@inheritDoc}
     */
    public function getFoundedDate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFoundedDate', []);

        return parent::getFoundedDate();
    }

    /**
     * {@inheritDoc}
     */
    public function setLocation($location)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLocation', [$location]);

        return parent::setLocation($location);
    }

    /**
     * {@inheritDoc}
     */
    public function getLocation()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLocation', []);

        return parent::getLocation();
    }

    /**
     * {@inheritDoc}
     */
    public function addUser($user)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addUser', [$user]);

        return parent::addUser($user);
    }

    /**
     * {@inheritDoc}
     */
    public function removeUser($user)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeUser', [$user]);

        return parent::removeUser($user);
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
    public function setWeblink($weblink)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setWeblink', [$weblink]);

        return parent::setWeblink($weblink);
    }

    /**
     * {@inheritDoc}
     */
    public function getWeblink()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getWeblink', []);

        return parent::getWeblink();
    }

    /**
     * {@inheritDoc}
     */
    public function setCommentDummy($commentDummy)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCommentDummy', [$commentDummy]);

        return parent::setCommentDummy($commentDummy);
    }

    /**
     * {@inheritDoc}
     */
    public function getCommentDummy()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCommentDummy', []);

        return parent::getCommentDummy();
    }

    /**
     * {@inheritDoc}
     */
    public function setPiDummy($piDummy)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPiDummy', [$piDummy]);

        return parent::setPiDummy($piDummy);
    }

    /**
     * {@inheritDoc}
     */
    public function getPiDummy()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPiDummy', []);

        return parent::getPiDummy();
    }

    /**
     * {@inheritDoc}
     */
    public function setComment($text, $user)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setComment', [$text, $user]);

        return parent::setComment($text, $user);
    }

    /**
     * {@inheritDoc}
     */
    public function setPiUser($user)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPiUser', [$user]);

        return parent::setPiUser($user);
    }

    /**
     * {@inheritDoc}
     */
    public function removeDependents($user)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeDependents', [$user]);

        return parent::removeDependents($user);
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
    public function setAuthor($author)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAuthor', [$author]);

        return parent::setAuthor($author);
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
    public function setUpdateAuthor($author)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUpdateAuthor', [$author]);

        return parent::setUpdateAuthor($author);
    }

    /**
     * {@inheritDoc}
     */
    public function addSynonym($synonym)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addSynonym', [$synonym]);

        return parent::addSynonym($synonym);
    }

    /**
     * {@inheritDoc}
     */
    public function removeSynonym($synonyms)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeSynonym', [$synonyms]);

        return parent::removeSynonym($synonyms);
    }

    /**
     * {@inheritDoc}
     */
    public function getSynonyms()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSynonyms', []);

        return parent::getSynonyms();
    }

    /**
     * {@inheritDoc}
     */
    public function setOriginal($original)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOriginal', [$original]);

        return parent::setOriginal($original);
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginal()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOriginal', []);

        return parent::getOriginal();
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
    public function setId($id)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', [$name]);

        return parent::setName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getName', []);

        return parent::getName();
    }

    /**
     * {@inheritDoc}
     */
    public function setAbbreviation($abbreviation)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAbbreviation', [$abbreviation]);

        return parent::setAbbreviation($abbreviation);
    }

    /**
     * {@inheritDoc}
     */
    public function getAbbreviation()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAbbreviation', []);

        return parent::getAbbreviation();
    }

    /**
     * {@inheritDoc}
     */
    public function setShortname($shortname)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setShortname', [$shortname]);

        return parent::setShortname($shortname);
    }

    /**
     * {@inheritDoc}
     */
    public function getShortname()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getShortname', []);

        return parent::getShortname();
    }

    /**
     * {@inheritDoc}
     */
    public function setDescription($description)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDescription', [$description]);

        return parent::setDescription($description);
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDescription', []);

        return parent::getDescription();
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
    public function setCreatedate($createdate)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCreatedate', [$createdate]);

        return parent::setCreatedate($createdate);
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
    public function setCreator(\Oleg\UserdirectoryBundle\Entity\User $creator = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCreator', [$creator]);

        return parent::setCreator($creator);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreator()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCreator', []);

        return parent::getCreator();
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
    public function getLinkToListId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLinkToListId', []);

        return parent::getLinkToListId();
    }

    /**
     * {@inheritDoc}
     */
    public function setLinkToListId($linkToListId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLinkToListId', [$linkToListId]);

        return parent::setLinkToListId($linkToListId);
    }

    /**
     * {@inheritDoc}
     */
    public function getObjectType()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getObjectType', []);

        return parent::getObjectType();
    }

    /**
     * {@inheritDoc}
     */
    public function setObjectType($objectType)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setObjectType', [$objectType]);

        return parent::setObjectType($objectType);
    }

    /**
     * {@inheritDoc}
     */
    public function getVersion()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getVersion', []);

        return parent::getVersion();
    }

    /**
     * {@inheritDoc}
     */
    public function setVersion($version)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setVersion', [$version]);

        return parent::setVersion($version);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntityNamespace()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEntityNamespace', []);

        return parent::getEntityNamespace();
    }

    /**
     * {@inheritDoc}
     */
    public function setEntityNamespace($entityNamespace)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEntityNamespace', [$entityNamespace]);

        return parent::setEntityNamespace($entityNamespace);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntityName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEntityName', []);

        return parent::getEntityName();
    }

    /**
     * {@inheritDoc}
     */
    public function setEntityName($entityName)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEntityName', [$entityName]);

        return parent::setEntityName($entityName);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntityId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEntityId', []);

        return parent::getEntityId();
    }

    /**
     * {@inheritDoc}
     */
    public function setEntityId($entityId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEntityId', [$entityId]);

        return parent::setEntityId($entityId);
    }

    /**
     * {@inheritDoc}
     */
    public function setFulltitle($fulltitle)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFulltitle', [$fulltitle]);

        return parent::setFulltitle($fulltitle);
    }

    /**
     * {@inheritDoc}
     */
    public function getFulltitle()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFulltitle', []);

        return parent::getFulltitle();
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
    public function getOptimalName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOptimalName', []);

        return parent::getOptimalName();
    }

    /**
     * {@inheritDoc}
     */
    public function getOptimalAbbreviationName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOptimalAbbreviationName', []);

        return parent::getOptimalAbbreviationName();
    }

    /**
     * {@inheritDoc}
     */
    public function getOptimalNameShortnameAbbreviation()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOptimalNameShortnameAbbreviation', []);

        return parent::getOptimalNameShortnameAbbreviation();
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdatedby($user)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUpdatedby', [$user]);

        return parent::setUpdatedby($user);
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdatedby()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUpdatedby', []);

        return parent::getUpdatedby();
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdatedon()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUpdatedon', []);

        return parent::setUpdatedon();
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdatedon()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUpdatedon', []);

        return parent::getUpdatedon();
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isEmpty', []);

        return parent::isEmpty();
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
    public function onCreateUpdate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'onCreateUpdate', []);

        return parent::onCreateUpdate();
    }

    /**
     * {@inheritDoc}
     */
    public function createFullTitle()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'createFullTitle', []);

        return parent::createFullTitle();
    }

    /**
     * {@inheritDoc}
     */
    public function setObject($object)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setObject', [$object]);

        return parent::setObject($object);
    }

    /**
     * {@inheritDoc}
     */
    public function clearObject()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'clearObject', []);

        return parent::clearObject();
    }

    /**
     * {@inheritDoc}
     */
    public function isVisible()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isVisible', []);

        return parent::isVisible();
    }

    /**
     * {@inheritDoc}
     */
    public function getObjectTypeName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getObjectTypeName', []);

        return parent::getObjectTypeName();
    }

    /**
     * {@inheritDoc}
     */
    public function getObjectTypeId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getObjectTypeId', []);

        return parent::getObjectTypeId();
    }

}
