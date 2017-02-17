<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 3/18/14
 * Time: 7:21 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\OrderformBundle\Security\Util;

use Oleg\UserdirectoryBundle\Util\UserUtil;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

use Oleg\UserdirectoryBundle\Entity\PerSiteSettings;

use Oleg\UserdirectoryBundle\Entity\User;

//include_once '..\conf\Spectrum.ini';
//include_once '\Skeleton.php';
//include_once '\DatabaseRoutines.php';
include_once '..\DatabaseRoutines.php';

class AperioUtil {

    private $ldap = true;
    private $test = false;

    //private $supportedUsertypes = array('aperio');  //array('aperio','wcmc-cwid');

    public function __construct() {
        //
    }

    public function aperioAuthenticateToken( TokenInterface $token, $serviceContainer, $em ) {

        //echo "Aperio Authenticator: user name=".$token->getUsername().", Credentials=".$token->getCredentials()."<br>";
        //exit("using Aperio Authenticator: authenticate Token");

        $userSecUtil = $serviceContainer->get('user_security_utility');

        //don't authenticate users without WCMC CWID keytype
        //$usernamePrefix = $userSecUtil->getUsernamePrefix($token->getUsername());
        //echo "usernamePrefix=".$usernamePrefix."<br>";

        //if( in_array($usernamePrefix, $this->supportedUsertypes) == false ) {
        //    return NULL;
        //    //throw new BadCredentialsException('Aperio Authentication: the usertype '.$usernamePrefix.' can not be authenticated by ' . implode(', ',$this->supportedUsertypes));
        //}

        $usernameClean = $userSecUtil->createCleanUsername($token->getUsername());

        //check if user exists in Aperio DB
        $AuthResult = $this->AperioAuth( $usernameClean, $token->getCredentials() );

        //print_r($AuthResult);
        //exit();

        if( $AuthResult && isset($AuthResult['UserId']) && $AuthResult['ReturnCode'] == 0 ) {
            //echo "<br>Aperio got UserId!<br>";

            $userManager = $serviceContainer->get('fos_user.user_manager');

            $user = $userManager->findUserByUsername($token->getUsername());

            if( !$user ) {

                $user = $userSecUtil->constractNewUser($token->getUsername());
                $user->setEmail($AuthResult['E_Mail']);
                $user->setCreatedby('aperio');

                ////////// assign Institution //////////
                $perSiteSettings = null;
                $params = $em->getRepository('OlegUserdirectoryBundle:SiteParameters')->findAll();
                if( count($params) > 0 ) { //if zero found => initial admin login after DB clean
                    if( count($params) != 1 ) {
                        throw new \Exception( 'Must have only one parameter object. Found '.count($params).' object(s)' );
                    }
                    $param = $params[0];
                    $institution = $param->getAutoAssignInstitution();
                    if( $institution ) {
                        //set institution to per site settings
                        $perSiteSettings = new PerSiteSettings();
                        $userSecUtil = $serviceContainer->get('user_security_utility');
                        $systemUser = $userSecUtil->findSystemUser();
                        $perSiteSettings->setAuthor($systemUser);
                        $perSiteSettings->setUser($user);
                        $perSiteSettings->addPermittedInstitutionalPHIScope($institution);
                        $em->persist($perSiteSettings);
                    }
                }
                ////////// EOF assign Institution //////////

                ////////// check if aperio username was set in UserRequest for this user (identification by email). //////////
                $userRequest = $em->getRepository('OlegOrderformBundle:UserRequest')->findOneByEmail($AuthResult['E_Mail']);
                if( $userRequest ) {
                    if( $userRequest->getStatus() != 'approved' ) {
                        throw new AuthenticationException('The Aperio authentication failed. User Account Request was not approved, status='.$userRequest->getStatus());
                    } else {
                        //add institutions to per site settings
                        if( $perSiteSettings ) {
                            foreach( $userRequest->getInstitution() as $inst ) {
                                $perSiteSettings->addPermittedInstitutionalPHIScope($inst);
                            }
                        }
                    }
                }
                ////////// EOF check if aperio username was set in UserRequest //////////

                //set Roles: aperio users can submit order by default.
                $user->addRole('ROLE_SCANORDER_SUBMITTER');

                //cwid is admin cwid
                if( $user->getPrimaryPublicUserId() == "cwid" || $user->getPrimaryPublicUserId() == "cwid" ) {
                    $user->addRole('ROLE_PLATFORM_ADMIN');
                }
                if( $this->test ) {
                    $user->addRole('ROLE_SCANORDER_UNAPPROVED');
                    $user->removeRole('ROLE_SCANORDER_SUBMITTER');
                }

                //for testing without ldap
                if( !$this->ldap ) {
                    echo "Aperio Auth Tesing: Remove it !!!";
                    $user->setPrimaryPublicUserId("testuser4");
                    $user->addRole('ROLE_SCANORDER_ADMIN');
                }

                $user->setPassword("");

                $userManager->updateUser($user);

            } //if !user


            //get roles: Faculty, Residents, or Fellows
            $_SESSION['AuthToken'] = $AuthResult['Token'];
            $userid = $AuthResult['UserId'];

            //echo "aperio userid=".$userid."<br>";

            $aperioRoles = $this->getUserGroupMembership($userid);

            //print_r($aperioRoles);
            //exit('aperio util');

            $stats = $this->setUserPathologyRolesByAperioRoles( $user, $aperioRoles );

            //exit('AperioAuth');

            return $user;

        } else {
            //exit('Aperio Auth failed');
            //throw new AuthenticationException('The Aperio authentication failed. Authentication Result:'.implode(";",$AuthResult));
            return NULL;
        }

        //throw new AuthenticationException('Aperio: Invalid username or password');
        return NULL;
    }


    public function AperioAuth( $loginName, $password ) {

        //echo "Aperio Auth Changeit back !!!";
        //exit();
        //echo " skip login=".$loginName.", pass=". $password." <br>";

        set_error_handler(array($this, 'errorToException'));

        if( $this->ldap ) {
            //$DataServerURL = "http://127.0.0.1:86";

            if( !function_exists('GetDataServerURL') ) {
                return null;
            }

            try {

                $DataServerURL = GetDataServerURL();

                //echo "DataServerURL=".$DataServerURL."<br>";  //$DataServerURL = "http://127.0.0.1:86";

                $client = new \Aperio_Aperio($DataServerURL);//,"","","","");

                //$this->errorTest();

            } catch (MongoCursorException $e) {

                //throw new \Exception( 'Can not connect to Aperio Data Server. Please try again later' );

            }

            //$DataServerURL = GetDataServerURL();
            //$client = new \Aperio_Aperio($DataServerURL);//,"","","","");
            $AuthResult = $client->Authenticate($loginName,$password);

            //check if auth is ok: define ('LOGON_FAILED', '-7004');           // UserName is incorrect
            //echo "ReturnCode=".$AuthResult['ReturnCode']."<br>";
            if( $AuthResult['ReturnCode'] == '-7004' || !isset($AuthResult['UserId']) ) {
                //echo "LOGON_FAILED!!! <br>";
                return $AuthResult;
            }

        } else {
            //echo "Aperio Auth Changeit back !!!";
            $AuthResult = array(
                'UserId' => 11,
                'ReturnCode' => 0,
                'E_Mail' => 'email@dummy'
            );
            //$loginName = 'oli2002';
        }

        //echo "<br>AuthResult:<br>";
        //print_r($AuthResult);
        //exit('AperioAuth');

        return $AuthResult;
    }



    public function getUserGroupMembership($userid) {
        if( $userid ) {
            return ADB_GetUserGroupMembership($userid);
        }
        return null;
    }

    //set user roles based on the user roles from aperio:
    public function setUserPathologyRolesByAperioRoles( $user, $aperioRoles ) {

        $addedRoles = array();

        if( !$aperioRoles && count($aperioRoles) == 0 ) {
            return $addedRoles;
        }

        $addOrderingProviderRole = false;

        $addedFaculty = false;
        $addedFellow = false;
        $addedResident = false;

        $addedOrdering = false;
        $addedDirector = false;
        $addedPrincipal = false;

        foreach( $aperioRoles as $role ) {

            //echo "Role: Id = ".$role['Id'].", Description=".$role['Description'].", Name=".$role['Name']."<br>";
            if( $role['Name'] == "Faculty" ) {
                $addedFaculty = !$user->hasRole("ROLE_SCANORDER_PATHOLOGY_FACULTY");
                $user->addRole("ROLE_SCANORDER_PATHOLOGY_FACULTY");
                $addOrderingProviderRole = true;
            }
            if( $role['Name'] == "Fellows" ) {
                $addedFellow = !$user->hasRole("ROLE_SCANORDER_PATHOLOGY_FELLOW");
                $user->addRole("ROLE_SCANORDER_PATHOLOGY_FELLOW");
                $addOrderingProviderRole = true;
            }
            if( $role['Name'] == "Residents" ) {
                $addedResident = !$user->hasRole("ROLE_SCANORDER_PATHOLOGY_RESIDENT");
                $user->addRole("ROLE_SCANORDER_PATHOLOGY_RESIDENT");
                $addOrderingProviderRole = true;
            }

        } //foreach

        if( $addOrderingProviderRole ) {
            $addedOrdering = !$user->hasRole("ROLE_SCANORDER_ORDERING_PROVIDER");
            $user->addRole('ROLE_SCANORDER_ORDERING_PROVIDER');   //Ordering Provider

            $addedDirector = !$user->hasRole("ROLE_SCANORDER_COURSE_DIRECTOR");
            $user->addRole('ROLE_SCANORDER_COURSE_DIRECTOR');

            $addedPrincipal = !$user->hasRole("ROLE_SCANORDER_PRINCIPAL_INVESTIGATOR");
            $user->addRole('ROLE_SCANORDER_PRINCIPAL_INVESTIGATOR');

            //
        }

        if( $addedFaculty )
            $addedRoles[] = 'Faculty';

        if( $addedFellow )
            $addedRoles[] = 'Fellow';

        if( $addedResident )
            $addedRoles[] = 'Resident';

        ///////////////

        if( $addedOrdering )
            $addedRoles[] = 'Ordering Provider';

        if( $addedDirector )
            $addedRoles[] = 'Course Director';

        if( $addedPrincipal )
            $addedRoles[] = 'Principal Investigator';

        return $addedRoles;
    }

    public function getUserIdByUserName( $UserName ) {

//        ADB_GetFilteredRecordList(
//            $TableName='Slide',
//            $RecordsPerPage=0,
//            $PageIndex=0,
//            $SelectColumns=array(),
//            $FilterColumns=array(),
//            $FilterOperators=array(),
//            $FilterValues=array(),
//            $FilterTables=array(),
//            $SortByField='',
//            $SortOrder='Descending',
//            &$TotalCount = NULL,
//            $Distinct = false);

        $UserId = null;

        //echo "user name=".$UserName."<br>";
        if( !isset($_SESSION['AuthToken']) || empty($_SESSION['AuthToken'])) {
            echo 'Set and not empty, and no undefined index error! <br>';
            return null;
        }

        echo "AuthToken=".$_SESSION['AuthToken']."<br>";

        $Users = ADB_GetFilteredRecordList(
            'Users',                        //$TableName
            0,                              //$RecordsPerPage
            0,                              //$PageIndex
            array('Id'),                    //$SelectColumns
            array('LoginName'),             //$FilterColumns
            array('='),                     //$FilterOperators
            array($UserName),               //$FilterValues
            array('Users')                  //$FilterTables
        );

        echo "res count=".count($Users).":";
        var_dump($Users);
        echo "<br>";
        exit('1');

        if( count($Users) == 1 ) {
            $User = $Users[0];
            $UserId = $User['Id'];
        }

//        echo "UserId=".$UserId."<br>";

        return $UserId;
    }

    public function errorToException($code, $message, $file = null, $line = 0) {
        //return true;
        if( error_reporting() == 0 || $code == E_WARNING || $code == E_NOTICE || $code == E_USER_NOTICE ) {
            return true;
        }
        //echo "error_reporting=".error_reporting().", code=".$code." ,message=".$message."<br>";
        //exit();
        throw new \Exception("Error exception: code=".$code." ,message=".$message.", file=".$file.", line=".$line);
    }

    public function errorTest() {
        trigger_error("DataServer timed-out after 10 seconds when trying to load this page.  Please wait a moment and try again by pressing the refresh button.", E_USER_ERROR);
    }



}