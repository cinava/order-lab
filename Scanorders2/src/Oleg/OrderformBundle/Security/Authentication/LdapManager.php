<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 1/22/14
 * Time: 1:21 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\OrderformBundle\Security\Authentication;

use FR3D\LdapBundle\Ldap\LdapManager as BaseLdapManager;
use FR3D\LdapBundle\Model\LdapUserInterface;
use FR3D\LdapBundle\Driver\LdapDriverInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Oleg\OrderformBundle\Helper\Parameters;

class LdapManager extends BaseLdapManager
{

    private $timezone;
    private $em;

    public function __construct( LdapDriverInterface $driver, $userManager, array $params, $timezone = null, $em = null ) {

        parent::__construct($driver,$userManager,$params);

        $this->timezone = $timezone;
        $this->em = $em;
    }

    protected function hydrate(UserInterface $user, array $entry)
    {

        //exit("using ldap! <br>");

        parent::hydrate($user, $entry);

        $user->setCreatedby('ldap');
        $user->getPreferences()->setTimezone($this->timezone);

        $user->addRole('ROLE_SCANORDER_UNAPPROVED_SUBMITTER');

        if( $user->getUsername() == "oli2002" || $user->getUsername() == "vib9020" ) {
            $user->addRole('ROLE_SCANORDER_ADMIN');
            $user->addRole('ROLE_SCANORDER_SUBMITTER');
            $user->removeRole('ROLE_SCANORDER_UNAPPROVED_SUBMITTER');
        }

        //assign Institution
        if( $user->getInstitution() == NULL || count($user->getInstitution()) == 0 ) {
            $params = $this->em->getRepository('OlegOrderformBundle:SiteParameters')->findAll();
            if( count($params) != 1 ) {
                throw new \Exception( 'Must have only one parameter object. Found '.count($params).'object(s)' );
            }
            $param = $params[0];
            $institution = $param->getAutoAssignInstitution();
            if( $institution ) {
                $user->addInstitution($institution);
            }
        }

    }

}