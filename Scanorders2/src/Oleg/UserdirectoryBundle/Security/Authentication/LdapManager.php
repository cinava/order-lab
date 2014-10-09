<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 1/22/14
 * Time: 1:21 PM
 * To change this template use File | Settings | File Templates.
 */

//Note: this ldap extends FR3D\LdapBundle\Security\Authentication\LdapAuthenticationProvider
//Note: findUserBy: $entries = $this->driver->search($this->params['baseDn'], $filter, $this->ldapAttributes); causes login delay
//Note: execution order: findUserByUsername, findUserBy, hydrate, bind
//If user already exists in DB then LdapManager->findUserByUsername is not used.
//Therefore: first user is checked by fosuser bundle if it exists in DB, then it check in LDAP. => user is got from DB or new user is created by LDAP
//Then user is authenticated by LDAP bind
//So to overwrite username different from LDAP, login page username should be split by two fields: user keytype and username

namespace Oleg\UserdirectoryBundle\Security\Authentication;

use FR3D\LdapBundle\Ldap\LdapManager as BaseLdapManager;
use FR3D\LdapBundle\Model\LdapUserInterface;
use FR3D\LdapBundle\Driver\LdapDriverInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

use Oleg\UserdirectoryBundle\Entity\User;

class LdapManager extends BaseLdapManager
{

    private $timezone;
    private $em;
    private $container;

    private $supportedUsertypes = array('wcmc-cwid');
    private $usernamePrefix;


    public function __construct( LdapDriverInterface $driver, $userManager, array $params, $container, $em ) {

        //print_r($params);
        //exit("constractor ldap <br>");

        parent::__construct($driver,$userManager,$params);

        $this->timezone = $container->getParameter('default_time_zone');
        $this->em = $em;
        $this->container = $container;
    }




    public function findUserByUsername($username)
    {

        $userSecUtil = $this->container->get('user_security_utility');

        //don't authenticate users without WCMC CWID keytype
        $usernamePrefix = $userSecUtil->getUsernamePrefix($username);
        if( in_array($usernamePrefix, $this->supportedUsertypes) == false ) {
            throw new BadCredentialsException('The usertype '.$usernamePrefix.' can not be authenticated by ldap.');
        }

        $this->usernamePrefix = $usernamePrefix;

        //clean username
        $usernameClean = $userSecUtil->createCleanUsername($username);

        return parent::findUserByUsername($usernameClean);
    }

    protected function hydrate(UserInterface $user, array $entry) {

        parent::hydrate($user, $entry);

        $user->setCreatedby('ldap');
        $user->getPreferences()->setTimezone($this->timezone);

        //modify user: set keytype and primary public user id
        $usernameClean = $user->getUsername();
        $userSecUtil = $this->container->get('user_security_utility');
        $userkeytype = $userSecUtil->getUsernameType($this->usernamePrefix);
        $user->setKeytype($userkeytype);
        $user->setPrimaryPublicUserId($usernameClean);

        //TODO: remove this on production!
        if(     $user->getPrimaryPublicUserId() == "oli2002"
            ||  $user->getPrimaryPublicUserId() == "vib9020"
            ||  $user->getPrimaryPublicUserId() == "svc_aperio_spectrum"
        ) {
            $user->addRole('ROLE_ADMIN');
        }

//        echo "<br>hydrate: user's keytype=".$user->getKeytype()." <br>";
//        echo "user's username=".$user->getUsername()." <br>";
//        echo "user's primaryPublicUserId=".$user->getPrimaryPublicUserId()." <br>";
//        print_r($user->getRoles());
        //exit('exit hydrate');

    }

    public function bind(UserInterface $user, $password)
    {

//        echo "before: user's username=".$user->getUsername()." <br>";

        //always clean username before bind, use primaryPublicUserId
        $user->setUsername( $user->getPrimaryPublicUserId() );

//        //don't authenticate users without WCMC CWID keytype
//        if( $user->getKeytype()->getAbbreviation() != $this->supportedUsertype ) {
//            throw new BadCredentialsException('The usertype '.$user->getKeytype()->getAbbreviation().' can not be authenticated by ldap.');
//        }

        $bindRes = parent::bind($user, $password);

        if( $bindRes ) {
            //replace only username
            $user->setUniqueUsername();
        }

//        echo "after: user's username=".$user->getUsername()." <br>";
//        echo "<br>bindRes=".$bindRes."<br>";
//        //exit('exit bind');

        return $bindRes;
    }



}