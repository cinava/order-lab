<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 10/4/13
 * Time: 12:56 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\UserdirectoryBundle\Security\Util;



use Oleg\UserdirectoryBundle\Entity\Permission;
use Oleg\UserdirectoryBundle\Form\DataTransformer\GenericTreeTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Oleg\UserdirectoryBundle\Entity\User;
use Oleg\UserdirectoryBundle\Util\UserUtil;
use Oleg\UserdirectoryBundle\Entity\Logger;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserSecurityUtil {

    protected $em;
    protected $sc;
    protected $container;

    public function __construct( $em, $sc, $container ) {
        $this->em = $em;
        $this->sc = $sc;
        $this->container = $container;
    }

    public function isCurrentUser( $id ) {

        $user = $this->sc->getToken()->getUser();

        $entity = $this->em->getRepository('OlegUserdirectoryBundle:User')->find($id);

        if( $entity && $entity->getId() === $user->getId() ) {
            return true;
        }

        return false;
    }

    //check for user preferences:
    //hide - Hide this profile
    //showToInstitutions - Only show this profile to members of the following institution(s)
    //showToRoles - Only show this profile to users with the following roles
    public function isUserVisible( $subjectUser, $currentUser ) {

        //always visible to Platform Administrator and Deputy Platform Administrator
        if( $this->sc->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            return true;
        }

        //always visible to current user
        if( $currentUser->getId() == $subjectUser->getId() ) {
            return true;
        }

        $preferences = $subjectUser->getPreferences();

        //hide - Hide this profile
        $hide = false;
        //If checked, profile View page should only show this profile to the user "owner" of the profile
        //and to users with Platform Administrator and Deputy Platform Administrator roles
        if( $preferences->getHide() ) {
            $hide = true;
        }

        //hide overwrite the two other checks below
        if( $hide ) {
            return false; //not visible
        }

        //showToInstitutions: false - if empty or check institutions if not empty
        $hideInstitution = false;
        $showToInstitutions = $preferences->getShowToInstitutions();
        if( count($showToInstitutions) > 0 ) {
            $hideInstitution = true;

            //check if $currentUser has one of the verified Institutions
            $type = null; //all types: AdministrativeTitle, AppointmentTitle, MedicalTitle
            $status = 1;  //1-verified
            $currentUserInstitutions = $currentUser->getInstitutions($type,$status);
            foreach( $currentUserInstitutions as $currentUserInstitution ) {
                //echo "currentUserInstitution=".$currentUserInstitution."<br>";
                if( $this->em->getRepository('OlegUserdirectoryBundle:Institution')->isNodeUnderParentnodes($showToInstitutions, $currentUserInstitution) ) {
                    $hideInstitution = false;
                    break;
                }
            }

            //check if $currentUser has one of the Institutional PHI Scope
            $securityUtil = $this->container->get('order_security_utility');
            $userSiteSettings = $securityUtil->getUserPerSiteSettings($subjectUser);
            $currentUserPermittedInstitutions = $userSiteSettings->getPermittedInstitutionalPHIScope();
            foreach( $currentUserPermittedInstitutions as $currentUserPermittedInstitution ) {
                //echo "currentUserInstitution=".$currentUserInstitution."<br>";
                if( $this->em->getRepository('OlegUserdirectoryBundle:Institution')->isNodeUnderParentnodes($showToInstitutions, $currentUserPermittedInstitution) ) {
                    $hideInstitution = false;
                    break;
                }
            }

//            foreach( $showToInstitutions as $showToInstitution ) {
//                //echo "verified inst count=".count($currentUserInst)."<br>";
//                //echo "showToInstitution=".$showToInstitution."<br>";
//                if( $currentUserInstitutions->contains($showToInstitution) ) {
//                    $hideInstitution = false;
//                    break;
//                }
//            }
        }


        //showToRoles
        $hideRole = false;
        $showToRoles = $preferences->getShowToRoles();
        if( count($showToRoles) > 0 ) {
            $hideRole = true;
            //check if current user has one of the role
            foreach( $showToRoles as $role ) {
                //echo "role=".$role."<br>";
                if( $this->sc->isGranted($role."") ) {
                    $hideRole = false;
                    break;
                }
            }
        }

        //echo "hideInstitution=".$hideInstitution."<br>";
        //echo "hideRole=".$hideRole."<br>";
        //exit();

        if( $hide ) {
            //exit('hide');
            //return false;
        }
        if( $hideInstitution ) {
            //exit('hideInstitution');
            //return false;
        }
        if( $hideRole ) {
            //exit('hideRole');
            //return false;
        }

        if( $hide || $hideInstitution || $hideRole ) {
            //exit('???');
            return false; //not visible
        } else {
            return true; //visible
        }

    }


    //used by login success handler to get user has access request
    public function getUserAccessRequest($user,$sitename) {
        $accessRequest = $this->em->getRepository('OlegUserdirectoryBundle:AccessRequest')->findOneBy(
            array('user' => $user, 'siteName' => $sitename)
        );

        return $accessRequest;
    }

    public function getUserAccessRequestsByStatus($sitename, $status) {
        $accessRequests = $this->em->getRepository('OlegUserdirectoryBundle:AccessRequest')->findBy(
            array('siteName' => $sitename, 'status' => $status)
        );

        return $accessRequests;
    }


    //check for the role in security context and in the user DB
    public function hasGlobalUserRole( $role, $user=null ) {

        if( false === $this->sc->isGranted('IS_AUTHENTICATED_FULLY') ) {
            return false;
        }

        if( $this->sc->isGranted($role) ) {
            return true;
        }

        //get user from DB?

        if( $user == null ) {
            $user = $this->sc->getToken()->getUser();
        }

//        if( $this->sc->isGranted('IS_AUTHENTICATED_ANONYMOUSLY') )
//            return false;

        if( !is_object($user) ) {
            //echo "user is not object: return false <br>";
            //exit();
            return false;
        } else {
            //echo "user is object <br>";
        }
        //exit();

        if( $user && $user->hasRole($role) ) {
            return true;
        }

        //echo "no role=".$role." => return false <br>";
        //exit();

        return false;
    }


    function idleLogout( $request, $sitename, $flag = null ) {

        $userUtil = new UserUtil();
        $res = $userUtil->getMaxIdleTimeAndMaintenance($this->em,$this->sc,$this->container);
        $maxIdleTime = $res['maxIdleTime'];
        $maintenance = $res['maintenance'];

        if( $maintenance ) {

            $msg = $userUtil->getSiteSetting($this->em,'MaintenancelogoutmsgWithDate');

        } else {

            if( $flag && $flag == 'saveorder' ) {
                $msg = 'You have been logged out after '.($maxIdleTime/60).' minutes of inactivity. You can find the order you have been working on in the list of your orders once you log back in.';
            } else {
                $msg = 'You have been logged out after '.($maxIdleTime/60).' minutes of inactivity.';
            }

        }

        $this->container->get('session')->getFlashBag()->add(
            'notice',
            $msg
        );

        $this->container->get('security.context')->setToken(null);
        //$this->get('request')->getSession()->invalidate();


        //return $this->redirect($this->generateUrl('login'));
        return new RedirectResponse( $this->container->get('router')->generate($sitename.'_login') );
    }

    function constructEventLog( $sitename, $user, $request ) {

        //get abbreviation from sitename:
        $siteObject = $this->getSiteBySitename($sitename,true);

        $logger = new Logger($siteObject);
        $logger->setUser($user);
        $logger->setRoles($user->getRoles());
        $logger->setUsername($user."");

        if( $request ) {
            $logger->setUseragent($_SERVER['HTTP_USER_AGENT']);
            $logger->setIp($request->getClientIp());
            $logger->setWidth($request->get('display_width'));
            $logger->setHeight($request->get('display_height'));
        }

        return $logger;
    }

    //get abbreviation from sitename:
    // fellowship-applications => fellapp
    // fellapp => fellapp
    public function getSiteBySitename( $sitename, $asObject=true ) {
        $repository = $this->em->getRepository('OlegUserdirectoryBundle:SiteList');
        $dql =  $repository->createQueryBuilder("list");
        $dql->select('list');
        $dql->where("list.name = :sitename OR list.abbreviation = :sitename");
        $query = $this->em->createQuery($dql);

        $query->setParameters(array('sitename'=>$sitename));

        $sitenameObject = $query->getSingleResult();

        if( $asObject ) {
            if( !$sitenameObject ) {
                throw $this->createNotFoundException('Unable to find SiteList entity by site name string='.$sitename);
            }
            return $sitenameObject;
        }

        if( !$sitenameObject ) {
            return $sitename;
        }

        return $sitenameObject->getAbbreviation();
    }

//    public function getDefaultUserKeytypeSafe() {
//        $userUtil = new UserUtil();
//        $userkeytype = $userUtil->getDefaultUsernameType($this->em);
//        if( $userkeytype == null ) {
//            //generate user keytypes
//            $userUtil->generateUsernameTypes($this->em,null);
//            $userkeytype = $userUtil->getDefaultUsernameType($this->em);
//        }
//        return $userkeytype;
//    }
    public function getDefaultUsernameType() {
        $userUtil = new UserUtil();
        $userkeytype = $userUtil->getDefaultUsernameType($this->em);
        return $userkeytype;
    }


    public function getUsernameType($abbreviation=null) {
        $userkeytype = null;
        if( $abbreviation ) {
            $userkeytype = $this->em->getRepository('OlegUserdirectoryBundle:UsernameType')->findOneBy(
                array(
                    'type' => array('default', 'user-added'),
                    'abbreviation' => array($abbreviation)
                ),
                array('orderinlist' => 'ASC')
            );

            return $userkeytype;
        } else {
            $userkeytypes = $this->em->getRepository('OlegUserdirectoryBundle:UsernameType')->findBy(
                array('type' => array('default', 'user-added')),
                array('orderinlist' => 'ASC')
            );

            //echo "userkeytypes=".$userkeytypes."<br>";
            //print_r($userkeytypes);
            if( $userkeytypes && count($userkeytypes) > 0 ) {
                $userkeytype = $userkeytypes[0];
            }
            return $userkeytypes;
        }
    }

    public function createCleanUsername($username) {
        $user = new User();
        return $user->createCleanUsername($username);
    }

    public function getUsernamePrefix($username) {
        $user = new User();
        return $user->getUsernamePrefix($username);
    }

    public function usernameIsValid($username) {
        $user = new User();
        return $user->usernameIsValid($username);
    }

    //array of emails for Admin users
    public function getUserEmailsByRole($sitename,$userRole) {

        if( $userRole == "Platform Administrator" ) {

            $roles = array("ROLE_PLATFORM_ADMIN","ROLE_PLATFORM_DEPUTY_ADMIN");

        } else if( $userRole == "Administrator" ) {

            if( $sitename == $this->container->getParameter('scan.sitename') ) {
                $roles = array("ROLE_SCANORDER_ADMIN");
            }

            if( $sitename == $this->container->getParameter('employees.sitename') ) {
                $roles = array("ROLE_USERDIRECTORY_ADMIN");
            }

            if( $sitename == $this->container->getParameter('fellapp.sitename') ) {
                $roles = array("ROLE_FELLAPP_COORDINATOR");
            }

            if( $sitename == $this->container->getParameter('deidentifier.sitename') ) {
                $roles = array("ROLE_DEIDENTIFICATOR_ADMIN");
            }

            if( $sitename == $this->container->getParameter('vacreq.sitename') ) {
                $roles = array("ROLE_VACREQ_ADMIN");
            }

            if( $sitename == $this->container->getParameter('calllog.sitename') ) {
                $roles = array("ROLE_CALLLOG_ADMIN");
            }

        } else {
            return null;
        }

        $users = $this->findByRoles($roles); //supports partial role name

        //echo "user count=".count($users)."<br>";

        $emails = array();
        if( $users && count($users) > 0 ) {

            foreach( $users as $user ) {
                //echo "user=".$user."<br>";
                if( $user->getEmail() ) {
                    //echo "email=".$user->getEmail()."<br>";
                    if( $user->getEmail() != "-1" ) {
                        $emails[] = $user->getEmail();
                    }
                }
            }

        }
        //print_r($emails);

        //return implode(", ", $emails);
        return $emails;
    }

    //$roles: role or partial role name
    public function findByRoles($roles) {

        $whereArr = array();
        foreach($roles as $role) {
            $whereArr[] = 'u.roles LIKE '."'%\"" . $role . "\"%'";
        }

        $qb = $this->em->createQueryBuilder();
        $qb->select('u')
            ->from('OlegUserdirectoryBundle:User', 'u')
            ->where( implode(' OR ',$whereArr) );

        //echo "query=".$qb."<br>";

        return $qb->getQuery()->getResult();
    }

    public function findSystemUser() {

        $systemusers = $this->em->getRepository('OlegUserdirectoryBundle:User')->findBy(
            array(
                //'keytype' => NULL,
                'primaryPublicUserId' => 'system'
            )
        );

        if( !$systemusers || count($systemusers) == 0  ) {
            return null;
        }

        $systemuser = $systemusers[0];

        return $systemuser;
    }

    //$nameStr: Unknown Event
    //$bundleName: UserdirectoryBundle
    //$className: EventTypeList
    //$params: array('type'=>'Medical')
    public function getObjectByNameTransformer( $author, $nameStr, $bundleName, $className, $params=null) {
        $transformer = new GenericTreeTransformer($this->em, $author, $className, $bundleName, $params);
        $nameStr = trim($nameStr);
        return $transformer->reverseTransform($nameStr);
    }

    //$subjectEntities: single object or array of objects
    public function createUserEditEvent($sitename,$event,$user,$subjectEntities,$request,$action='Unknown Event') {

        $logger = $this->container->get('logger');

        if( !$user ) {
            $logger->warning("createUserEditEvent: "."User is not defined for $sitename for event=".$event);
            return null;
        }

        $em = $this->em;
        $user = $em->getRepository('OlegUserdirectoryBundle:User')->find($user->getId());

        $eventLog = $this->constructEventLog($sitename,$user,$request);

        if( !$eventLog ) {
            $logger->warning("createUserEditEvent: "."eventLog entity has not been generated for sitename ".$sitename);
        }

        $eventLog->setEvent($event);

        //make sure timezone set to UTC
        //date_default_timezone_set('UTC');

        //set Event Type
//        $eventtype = $em->getRepository('OlegUserdirectoryBundle:EventTypeList')->findOneByName($action);
//        if( !$eventtype ) {
//            //$eventtype = $em->getRepository('OlegUserdirectoryBundle:EventTypeList')->findOneByName('Entity Updated');
//            $eventtype = new EventTypeList();
//            $userutil = new UserUtil();
//            return $userutil->setDefaultList( $eventtype, null, $user, $action );
//            $em->persist($eventtype);
//        }
//        $objectParams = array(
//            'className' => 'EventTypeList',
//            'fullClassName' => "Oleg\\UserdirectoryBundle\\Entity\\"."EventTypeList",
//            'fullBundleName' => 'UserdirectoryBundle'
//        );
//        $eventtype = $em->getRepository('OlegUserdirectoryBundle:EventTypeList')->convertStrToObject( $action, $objectParams, $user );
        $eventtype = $this->getObjectByNameTransformer($user,$action,'UserdirectoryBundle','EventTypeList');

        $eventLog->setEventType($eventtype);

        //set logger entity(s)
        if( $subjectEntities ) {

            if( method_exists($subjectEntities,'getId') ) {

                $subjectEntity = $subjectEntities;
                $ids = $subjectEntity->getId();

            } else {

                $idsArr = array();

                foreach( $subjectEntities as $subjectObject ) {
                    $idsArr[] = $subjectObject->getId();
                }

                $ids = implode(", ",$idsArr);
                $subjectEntity = $subjectEntities[0];

            }

            //get classname, entity name and id of subject entity
            $class = new \ReflectionClass($subjectEntity);
            $className = $class->getShortName();
            $classNamespace = $class->getNamespaceName();

            //set classname, entity name and id of subject entity
            $eventLog->setEntityNamespace($classNamespace);
            $eventLog->setEntityName($className);
            $eventLog->setEntityId($ids);

            //create EventObjectTypeList if not exists
            $eventObjectType = $this->getObjectByNameTransformer($user,$className,'UserdirectoryBundle','EventObjectTypeList');
            if( $eventObjectType ) {
                $eventLog->setObjectType($eventObjectType);
            }

        } else {
            //$logger->warning("createUserEditEvent: "."subjectEntities are not provided");
        }

        //$logger = $this->container->get('logger');
        //$logger->notice("usersec: timezone=".date_default_timezone_get());

        $em->persist($eventLog);
        $em->flush($eventLog);

        return $eventLog;
    }

    //add type to tree entity if exists
    public function addDefaultType($entity,$params) {
        $fullClassName = new \ReflectionClass($entity);
        $className = $fullClassName->getShortName();

        //add institutional type
        if( $className == "Institution" ) {
            if( array_key_exists('type',$params) && $params['type'] ) {
                $type = $this->em->getRepository('OlegUserdirectoryBundle:InstitutionType')->findOneByName($params['type']);
                $entity->addType($type);
            }
            if( array_key_exists('organizationalGroupType',$params) && $params['organizationalGroupType'] ) {
                $organizationalGroupType = $this->em->getRepository('OlegUserdirectoryBundle:OrganizationalGroupType')->findOneByName($params['organizationalGroupType']);
                $entity->setOrganizationalGroupType($organizationalGroupType);
            }
        }

        return $entity;
    }

    public function getDefaultSourceSystem($sitename=null)
    {

        $defaultSourceSystemName = 'ORDER Scan Order';

        if( $sitename == 'calllog' ) {
            $defaultSourceSystemName = 'ORDER Call Log Book';
        }
        if ($sitename == 'deidentifier' ) {
            $defaultSourceSystemName = 'ORDER Deidentifier';
        }
        if ($sitename == 'scan' || $sitename == null ) {
            $defaultSourceSystemName = 'ORDER Scan Order';  //'Scan Order';
        }

        $source = $this->em->getRepository('OlegUserdirectoryBundle:SourceSystemList')->findOneByName($defaultSourceSystemName);

        if( !$source ) {
            if( $this->container ) {
                $logger = $this->container->get('logger');
                $logger->warning('Warning (Not Found): Default Source System with name '.$defaultSourceSystemName);
            }
        }

        //echo "source=".$source."<br>";
        return $source;
    }


    //username - full username including user type ie svc_aperio_spectrum_@_wcmc-cwid
    public function constractNewUser($username) {

        $serviceContainer = $this->container;
        $em = $this->em;
        $userManager = $serviceContainer->get('fos_user.user_manager');
        $userSecUtil = $serviceContainer->get('user_security_utility');

        $usernamePrefix = $userSecUtil->getUsernamePrefix($username);
        $usernameClean = $userSecUtil->createCleanUsername($username);

        $default_time_zone = $serviceContainer->getParameter('default_time_zone');

        $user = $userManager->createUser();

        //////////////////////////////// get usertype ////////////////////////////////
        $userkeytype = $userSecUtil->getUsernameType($usernamePrefix);
        //echo "keytype=".$userkeytype."<br>";

        //first time login when DB is clean
        if( !$userkeytype ) {
            $userUtil = new UserUtil();
            $count_usernameTypeList = $userUtil->generateUsernameTypes($this->em);
            $userkeytype = $userSecUtil->getUsernameType($usernamePrefix);
        }

        if( !$userkeytype ) {
            throw new \Exception('User keytype is empty for prefix '.$usernamePrefix);
        }
        //////////////////////////////// EOF get usertype ////////////////////////////////

        $user->setKeytype($userkeytype);
        $user->setPrimaryPublicUserId($usernameClean);
        $user->setUniqueUsername();

        $user->setEnabled(true);
        $user->getPreferences()->setTimezone($default_time_zone);

        //add default locations
        $userGenerator = $this->container->get('user_generator');
        $userGenerator->addDefaultLocations($user,null);

        $user->setPassword("");

        //$userManager->updateUser($user);

        return $user;
    }


    //$name is entered by a user username. $name can be a guessed username
    //Use primaryPublicUserId as cwid
    public function getUserByUserstr( $name ) {

        //echo "get cwid name=".$name."<br>";

        $user = null;
        $cwid = null;

        //get cwid
        $strArr = explode(" ",$name);

        if( count($strArr) > 0 ) {
            $cwid = $strArr[0];
        }

        //1) try first part
        if( $cwid ) {
            //echo "cwid=".$cwid."<br>";
            $user = $this->em->getRepository('OlegUserdirectoryBundle:User')->findOneByPrimaryPublicUserId($cwid);
        }

        //2) try full name
        if( !$user ) {
            $user = $this->em->getRepository('OlegUserdirectoryBundle:User')->findOneByPrimaryPublicUserId($name);
        }

        //3) try full name
        if( !$user ) {

            $query = $this->em->createQueryBuilder()
                ->from('OlegUserdirectoryBundle:User', 'user')
                ->select("user")
                ->leftJoin("user.infos", "infos")
                ->where("infos.email=:name OR infos.displayName=:name")
                ->setParameters( array(
                    'name' => $name
                ));

            $users = $query->getQuery()->getResult();

            if( count($users) > 0 ) {
                $user = $users->first();
            }

        }

        return $user;
    }

    //mimic depreciated mysql_real_escape_string
    public function mysql_escape_mimic($inp) {

        //return mysql_real_escape_string($inp);

        $search=array("'",'"');
        $replace=array("","");
        $inp = str_replace($search,$replace,$inp);

        if(is_array($inp))
            return array_map(__METHOD__, $inp);

        if(!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    }




    ///////////////////////// User Role methods /////////////////////////
    public function getObjectRolesBySite( $object, $sitename, $associated=true ) {
        $objectSiteRoles = array();

        $roles = $this->getRolesBySite($sitename,$associated);

        foreach( $roles as $roleObject ) {
            if( $roleObject && $object->hasRole($roleObject->getName()) ) {
                $objectSiteRoles[] = $roleObject;
            }
        }

        return $objectSiteRoles;
    }

    public function getUserRolesBySite( $user, $sitename, $associated=true ) {
        $userSiteRoles = array();

        $roles = $this->getRolesBySite($sitename,$associated);

        foreach( $roles as $roleObject ) {
            //echo "roleObject=".$roleObject."<br>";
//            if( !$roleObject ) {
//                continue;
//            }
//            if( $associated ) {
//                if( $roleObject && $user->hasRole($roleObject->getName()) ) {
//                    $userSiteRoles[] = $roleObject;
//                }
//            } else {
//                //echo "not associated <br>";
//                if( $roleObject && !$user->hasRole($roleObject->getName()) ) {
//                    $userSiteRoles[] = $roleObject;
//                }
//            }
            if( $roleObject && $user->hasRole($roleObject->getName()) ) {
                $userSiteRoles[] = $roleObject;
            }
        }

        return $userSiteRoles;
    }

    public function getQueryUserBySite( $sitename ) {
        $dql = $this->getDqlUserBySite($sitename);
        $query = $this->em->createQuery($dql);
        return $query;
    }

    public function getDqlUserBySite( $sitename ) {

        //roles with sitename
        $roles = $this->getRolesBySite($sitename);
        //echo "roles count=".count($roles)."<br>";
        //print_r($roles);
        //exit('1');

        $repository = $this->em->getRepository('OlegUserdirectoryBundle:User');
        $dql =  $repository->createQueryBuilder("user");
        $dql->select('user');
        $dql->leftJoin("user.infos", "infos");
        $dql->leftJoin("user.keytype", "keytype");

        //roles where
        $whereArr = array();
        $where = "";
        $count = 0;
        foreach( $roles as $role ) {
            //$whereArr[] = "'".$role['name']."'";
            if( $count > 0 ) {
                $where .= " OR ";
            }
            $where .= "user.roles LIKE " . "'%".$role->getName()."%'";
            $count++;
        }
        //echo "where=".$where."<br>";

        if( !$where ) {
            $where = "1=0";
        }

        $dql->where($where);
        //echo "dql=".$dql."<br>";

        return $dql;
    }
    public function getRolesBySite( $sitename, $associated=true, $levelOnly=false ) {
        $repository = $this->em->getRepository('OlegUserdirectoryBundle:Roles');
        $dql =  $repository->createQueryBuilder("roles");
        $dql->select('roles');
        $dql->leftJoin("roles.sites", "sites");

        if( $associated ) {
            $dql->where("sites.name = :sitename OR sites.abbreviation = :sitename");
        } else {
            $dql->where("sites.name != :sitename AND sites.abbreviation != :sitename");
        }

        if( $levelOnly !== false ) {
            $dql->andWhere("roles.level = " . $levelOnly);
        }

        //only default and user-added types
        $dql->andWhere("roles.type = :typedef OR roles.type = :typeadd");

        $query = $this->em->createQuery($dql);

        $query->setParameters(array(
            "sitename" => $sitename,
            'typedef' => 'default',
            'typeadd' => 'user-added',
        ));

        $roles = $query->getResult();

        return $roles;
    }
    //NOT working. Not used.
    public function getQueryUserBySite_SingleQuery( $sitename ) {
        $repository = $this->em->getRepository('OlegUserdirectoryBundle:User');
        $dql =  $repository->createQueryBuilder("user");
        $dql->select('user');
        $dql->leftJoin("user.infos", "infos");

        //$dql->leftJoin('OlegUserdirectoryBundle:Roles', 'roles');
        $dql->leftJoin("OlegUserdirectoryBundle:Roles", "roles", "WITH", "user.roles LIKE '%roles.name%'");
        //$dql->leftJoin("OlegUserdirectoryBundle:SiteList", "sitelist", "WITH", "sitelist.id = sites.id");
        $dql->leftJoin("roles.sites", "sites");

        $dql->where("sites.name LIKE :sitename");
        //$dql->where("sites IS NULL");
        //$dql->where("sites.id=4");
        //$dql->where("roles.name = 'ROLE_DEIDENTIFICATOR_WCMC_NYP_ENQUIRER'");

        //echo "dql=".$dql."<br>";

        $query = $this->em->createQuery($dql);

        $query->setParameters(array(
            "sitename" => "'%".$this->siteName."%'"
        ));

        return $query;
    }

    public function getSiteRolesKeyValue( $sitename ) {
        $rolesArr = array();

        $roles = $this->getRolesBySite($sitename);

        foreach( $roles as $role ) {
            $rolesArr[$role->getName()] = $role->getAlias();
        }
        return $rolesArr;
    }

    public function getSiteRolesIdKeyValue( $sitename ) {
        $rolesArr = array();

        $roles = $this->getRolesBySite($sitename);

        foreach( $roles as $role ) {
            $rolesArr[$role->getId()] = $role->getAlias();
        }
        return $rolesArr;
    }

    //lowest level role == roles with level = 1
    public function getLowestRolesBySite( $sitename ) {
        return $this->getRolesBySite($sitename, true, 1);
    }

    public function addOnlySiteRoles( $subjectUser, $newUserSiteRoles, $sitename ) {
        $originalUserSiteRoles = $this->getUserRolesBySite( $subjectUser, $sitename, true );

        if( $originalUserSiteRoles == $newUserSiteRoles ) {
            return null;
        }

        foreach( $originalUserSiteRoles as $originalUserSiteRole ) {
            $subjectUser->removeRole($originalUserSiteRole);
        }

        foreach( $newUserSiteRoles as $newUserSiteRole ) {
            $subjectUser->addRole($newUserSiteRole);
        }

        //$arrayDiff = array_diff($originalUserSiteRoles, $newUserSiteRoles);
        $res = array(
            'originalUserSiteRoles' => $originalUserSiteRoles,
            'newUserSiteRoles' => $newUserSiteRoles
        );

        return $res;
    }
    ///////////////////////// EOF User Role methods /////////////////////////


    //$documentTypeFlag: only or except
    public function deleteOrphanFiles( $days, $documentType='Fellowship Application Spreadsheet', $documentTypeFlag='only' ) {

        if( $days == null || $days == "" || !is_int($days) ) {
            return "Invalid days parameter days=" . $days;
        }

        //$beforeDate = $startDate->format('Y');
        //DB date format: 2015-09-29 20:26:13.000000
        $nowDate = new \DateTime('now');
        $dateCorrectionStr = '-'.$days.' days';

        $beforeDate = $nowDate->modify($dateCorrectionStr)->format('Y-m-d');
        //echo "beforeDate=".$beforeDate."<br>";

        //get spreadsheets older than X year
        $repository = $this->em->getRepository('OlegUserdirectoryBundle:Document');
        $dql =  $repository->createQueryBuilder("document");
        $dql->select('document');
        $dql->leftJoin('document.type','documentType');

        $dql->where("document.entityNamespace IS NULL AND document.entityName IS NULL AND document.entityId IS NULL");
        $dql->andWhere("document.createdate < :beforeDate");

        $queryParameters = array(
            'beforeDate' => $beforeDate
        );

        if( $documentType ) {
            if( $documentTypeFlag == 'only' ) {
                $dql->andWhere("documentType.name = :documentType OR documentType.abbreviation = :documentType");
            }
            if( $documentTypeFlag == 'except' ) {
                $dql->andWhere("documentType.id IS NULL OR documentType.name != :documentType");
            }
            $queryParameters['documentType'] = $documentType;
        }

        $query = $this->em->createQuery($dql);

        //echo "query=".$query->getSql()."<br>";

        $query->setParameters($queryParameters);

        $documents = $query->getResult();

        //echo "documents count=".count($documents)."<br>";

        $deletedDocumentIdsArr = array();

        //foreach documents unlink and delete from DB
        foreach( $documents as $document ) {
            $deletedDocumentIdsArr[] = $document->getId();

            //document absolute path
            //$documentPath = $document->getAbsoluteUploadFullPath();
            $documentPath = $this->container->get('kernel')->getRootDir() . '/../web/' . $document->getUploadDirectory().'/'.$document->getUniquename();
            //$documentPath = "Uploaded/scan-order/documents/test.jpeg";
            //echo "documentPath=".$documentPath."<br>";

            //continue; //testing

            $this->em->remove($document);
            $this->em->flush();

            //remove file from folder
            if( is_file($documentPath) ) {
                //echo "file exists!!! ";
                unlink($documentPath);
            } else {
                //echo "file does exists??? ";
            }

            //break; //testing
        }

        return implode(",",$deletedDocumentIdsArr);
    }


    //return parameter specified by $parameter. If the first time login when site parameter does not exist yet, return -1.
    public function getSiteSettingParameter( $parameter ) {

        $params = $this->em->getRepository('OlegUserdirectoryBundle:SiteParameters')->findAll();

//        if( !$params ) {
//            //throw new \Exception( 'Parameter object is not found' );
//        }

        //echo "params count=".count($params)."<br>";

        if( count($params) == 0 ) {
            return -1;
        }

        if( count($params) > 1 ) {
            throw new \Exception( 'Must have only one parameter object. Found '.count($params).' object(s)' );
        }

        $param = $params[0];

        if( $parameter == null ) {
            return $param;
        }

        $getSettingMethod = "get".$parameter;
        $res = $param->$getSettingMethod();

        return $res;
    }

    //return absolute file name on the server which will work for web and command
    public function getAbsoluteServerFilePath( $document ) {
        return realpath($this->container->get('kernel')->getRootDir() . "/../web/" . $document->getServerPath());
    }

    //checkAndAddPermissionToRole($role,"Submit a Vacation Request","VacReqRequest","create")
    public function checkAndAddPermissionToRole($role,$permissionListStr,$permissionObjectListStr,$permissionActionListStr) {

        $count = 0;
        $em = $this->em;
        $permission = $em->getRepository('OlegUserdirectoryBundle:PermissionList')->findOneByName($permissionListStr);
        if( !$permission ) {
            exit("Permission is not found by name=".$permissionListStr);
        }

        //make sure permission is added to role: role->permissions(Permission)->permission(PermissionList)->(PermissionObjectList,PermissionActionList)
        //check if role has permission (Permission): PermissionList with $permissionListStr
        $permissionExists = false;
        foreach( $role->getPermissions() as $rolePermission ) {
            if( $rolePermission->getPermission() && $rolePermission->getPermission()->getId() == $permission->getId() ) {
                $permissionExists = true;
            }
        }
        if( !$permissionExists ) {
            $rolePermission = new Permission();
            $rolePermission->setPermission($permission);
            $role->addPermission($rolePermission);
            $count++;
        }

        //make sure object is set
        if( !$permission->getPermissionObjectList() ) {
            $permissionObject = $em->getRepository('OlegUserdirectoryBundle:PermissionObjectList')->findOneByName($permissionObjectListStr);
            $permission->setPermissionObjectList($permissionObject);
            $count++;
        }

        //make sure action is set
        if( !$permission->getPermissionActionList() ) {
            $permissionAction = $em->getRepository('OlegUserdirectoryBundle:PermissionActionList')->findOneByName($permissionActionListStr);
            $permission->setPermissionActionList($permissionAction);
            $count++;
        }

        return $count;
    }


    public function transformDatestrToDateWithSiteEventLog($datestr,$sitename) {
        $date = null;

        if( !$datestr ) {
            return $date;
        }
        $datestr = trim($datestr);
        //echo "###datestr=".$datestr."<br>";

        if( strtotime($datestr) === false ) {
            // bad format
            $msg = 'transformDatestrToDate: Bad format of datetime string='.$datestr;
            //throw new \UnexpectedValueException($msg);
            $logger = $this->container->get('logger');
            $logger->error($msg);

            //send email
            $userSecUtil = $this->container->get('user_security_utility');
            $systemUser = $userSecUtil->findSystemUser();
            $event = $sitename." warning: " . $msg;
            $userSecUtil->createUserEditEvent($sitename,$event,$systemUser,null,null,'Warning');

            //exit('bad');
            return $date;
        }

//        if( !$this->valid_date($datestr) ) {
//            $msg = 'Date string is not valid'.$datestr;
//            throw new \UnexpectedValueException($msg);
//            $logger = $this->container->get('logger');
//            $logger->error($msg);
//        }

        try {
            //this is used to convert data string as entered in new-york time to UTC
            $utcTz = new \DateTimeZone("UTC");
            $nyTz = new \DateTimeZone("America/New_York");
            $date = new \DateTime($datestr);
            $date->setTime(12, 00);
            //echo "Original: data=".$date->format('M d Y H:i:s')."<br>";
            $date->setTimezone( $nyTz );
            //echo "after set: data=".$date->format('M d Y H:i:s')."<br>";
            if( $date ) {
                $date->setTimezone($utcTz);
            //    echo "set timezone UTC: data=".$date->format('M d Y H:i:s')."<br>";
            //    //exit('1');
            } else {
            //    //exit("date object is null for datestr=".$datestr);
            }
        } catch (Exception $e) {
            $msg = 'Failed to convert string'.$datestr.'to DateTime:'.$e->getMessage();
            //throw new \UnexpectedValueException($msg);
            $logger = $this->container->get('logger');
            $logger->error($msg);
            $this->sendEmailToSystemEmail("Bad format of datetime string", $msg);
        }

        return $date;
    }
    public function sendEmailToSystemEmail($subject, $message) {
        //$logger = $this->container->get('logger');

        $userSecUtil = $this->container->get('user_security_utility');
        $systemEmail = $userSecUtil->getSiteSettingParameter('siteEmail');
        $emailUtil = $this->container->get('user_mailer_utility');

        //$logger->notice("sendEmailToSystemEmail: systemEmail=".$systemEmail."; subject=".$subject."; message=".$message);
        $emailUtil->sendEmail( $systemEmail, $subject, $message );
    }


    //{% set baseUrl = app.request.scheme ~'://'~app.request.host~app.request.getBaseURL() %}
    //{% set fullUrl = baseUrl ~ '/' ~ sitenameFull ~ '/' ~ entity.objectType.url ~ '/' ~ entity.entityId %}
    public function getAbsoluetFullLoggerUrl( $logger, $request ) {
        $url = null;
        //{% set baseUrl = app.request.scheme ~'://'~app.request.host~app.request.getBaseURL() %}
        //baseUrl ~ '/' ~ sitenameFull ~ '/' ~ entity.objectType.url ~ '/' ~ entity.entityId

        //exception for "Accession"
        if( $logger->getObjectType() == 'Accession' ) {
            ///re-identify/?accessionType=WHATEVER-YOU-NEED-TO-SET-THIS-TO&accessionNumber=

            $accessionType = $this->em->getRepository('OlegOrderformBundle:AccessionType')->findOneByName('Deidentifier ID');
            //echo "accessionType=".$accessionType."<br>";

            //find one valid accession
            $accessionAccession = $this->em->getRepository('OlegOrderformBundle:AccessionAccession')->findOneBy(
                array(
                    'accession' => $logger->getEntityId(),
                    'status' => 'deidentified-valid'
                )
            );

            if( $accessionAccession && $accessionType ) {

                $accessionNumber = $accessionAccession->getField();

                //path=deidentifier_search
                $url = $this->container->get('router')->generate(
                    'deidentifier_search',
                    array(
                        'accessionType' => $accessionType->getId(),
                        'accessionNumber' => $accessionNumber
                    )
                    //UrlGeneratorInterface::ABSOLUTE_URL
                );
                //echo "url=".$url."<br>";

            }//if accession

        }

        $siteName = $logger->getSite()->getName();
        if( $logger->getObjectType() == "SiteList" ) {
            $siteName = "directory";
        }

        if( !$url ) {
            $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
            $url = $baseUrl . '/' . $siteName . '/' . $logger->getObjectType()->getUrl() . '/' . $logger->getEntityId();
        }

        return $url;
    }

    public function getCurrentUserInstitution($user=null)
    {
        $em = $this->em;
        $securityUtil = $this->container->get('order_security_utility');
        $institution = null;

        if( $user ) {
            $userSiteSettings = $securityUtil->getUserPerSiteSettings($user);
            $institution = $userSiteSettings->getDefaultInstitution();
            //echo "1 inst=".$institution."<br>";
            if (!$institution) {
                $institutions = $securityUtil->getUserPermittedInstitutions($user);
                //echo "count inst=".count($institutions)."<br>";
                if (count($institutions) > 0) {
                    $institution = $institutions[0];
                }
                //echo "2 inst=".$institution."<br>";
            }
        }
        if (!$institution) {
            $institution = $em->getRepository('OlegUserdirectoryBundle:Institution')->findOneByAbbreviation("WCMC");
        }

        return $institution;
    }

    /////////////////////// getHeadInfo Return: Chief, Eyebrow Pathology ///////////////////////
    //Group by institutions
    public function getHeadInfo( $user ) {

        //testing
        //return $user->getHeadInfo();

        $instArr = array();

        $instArr = $this->addTitleInfo($instArr,'administrativeTitle',$user->getAdministrativeTitles());

        $instArr = $this->addTitleInfo($instArr,'appointmentTitle',$user->getAppointmentTitles());

        $instArr = $this->addTitleInfo($instArr,'medicalTitle',$user->getMedicalTitles());

        $instArr = $this->groupByInst($instArr);

        return $instArr;
    }
    public function addTitleInfo( $instArr, $tablename, $titles ) {
        foreach( $titles as $title ) {
            $headServiceId = null;
            $elementInfo = null;
            if( $title->getName() ) {
                $name = $title->getName()->getName()."";
                $titleId = null;
                if( $title->getName()->getId() ) {
                    $titleId = $title->getName()->getId();
                    //echo "titleId=".$titleId."<br>";
                }

                //If a title has Position Type = Head of Service, don't merge this title with any others.
                //$headService = false;
                if( method_exists($title,'getUserPositions') ) {
                    foreach( $title->getUserPositions() as $userPosition ) {
                        if ($userPosition && $userPosition->getName() == 'Head of Service') {
                            //$headService = true;
                            $headServiceId = $title->getId();
                            break;
                        }
                    }
                }

                $elementInfo = array(
                    'tablename'=>$tablename,
                    'id'=>$titleId,
                    'name'=>$name,
                    //'headService'=>$headServiceId
                );
                //$elementInfo = $this->getSearchSameObjectUrl($elementInfo);
            }

            //$headInfo[] = 'break-br';

            $instId = 0;
            $institution = null;
            if( $title->getInstitution() ) {
                $institution = $title->getInstitution();
                $instId = $title->getInstitution()->getId();

                if( $headServiceId ) {
                    $instId = $instId."-".$headServiceId;
                }

            }

            if( array_key_exists($instId,$instArr) ) {
                //echo $instId." => instId already exists<br>";
            } else {
                //echo $instId." => instId does not exists<br>";
                $instArr[$instId]['instInfo'] = $this->getHeadInstitutionInfoArr($institution,$headServiceId);
            }

            if( $elementInfo ) {
                //echo "titleInfo titleId=".$elementInfo['id']."<br>";
                $instArr[$instId]['titleInfo'][] = $elementInfo;
            }
        }//foreach titles

        return $instArr;
    }
    public function getHeadInstitutionInfoArr($institution,$headServiceId) {

        //echo "inst=".$institution."<br>";
        //echo "count=".count($headInfo)."<br>";
        $pid = null;

        $headInfo = array();

        //service
        if( $institution ) {

            $institutionThis = $institution;
            //echo "inst=".$institutionThis."<br>";

            $name = $institutionThis->getName()."";
            $titleId = null;
            if( $institutionThis->getId() ) {
                $titleId = $institutionThis->getId();
            }
            $pid = null;
            $parent = $institutionThis->getParent();
            if( $parent && $parent->getId() ) {
                $pid = $parent->getId();
            }

            if( $headServiceId ) {
                //$titleId = $titleId."-".$headServiceId;
                $pid = $pid."-".$headServiceId;
            }

            if( $name ) {
                $elementInfo = array('tablename' => 'Institution', 'id' => $titleId, 'pid' => $pid, 'name' => $name);
                //$elementInfo = $this->getSearchSameObjectUrl($elementInfo, 'small');
                $headInfo[] = $elementInfo;
            }

        }

        //division
        if( $institution && $institution->getParent() ) {

            $institutionThis = $institution->getParent();
            //echo "inst=".$institutionThis."<br>";

            $name = $institutionThis->getName()."";
            $titleId = null;
            if( $institutionThis->getId() ) {
                $titleId = $institutionThis->getId();
            }
            $pid = null;
            $parent = $institutionThis->getParent();
            if( $parent && $parent->getId() ) {
                $pid = $parent->getId();
            }

            if($name) {
                $elementInfo = array('tablename' => 'Institution', 'id' => $titleId, 'pid' => $pid, 'name' => $name);
                //$elementInfo = $this->getSearchSameObjectUrl($elementInfo, 'small');
                $headInfo[] = $elementInfo;
            }

        }

        //department
        if( $institution && $institution->getParent() && $institution->getParent()->getParent() ) {

            $institutionThis = $institution->getParent()->getParent();
            //echo "inst=".$institutionThis."<br>";

            $name = $institutionThis->getName()."";
            $titleId = null;
            if( $institutionThis->getId() ) {
                $titleId = $institutionThis->getId();
            }
            $pid = null;
            $parent = $institutionThis->getParent();
            if( $parent && $parent->getId() ) {
                $pid = $parent->getId();
            }

            if($name) {
                $elementInfo = array('tablename' => 'Institution', 'id' => $titleId, 'pid' => $pid, 'name' => $name);
                //$elementInfo = $this->getSearchSameObjectUrl($elementInfo, 'small');
                $headInfo[] = $elementInfo;
            }

        }

        //institution
        if( $institution && $institution->getParent() && $institution->getParent()->getParent() && $institution->getParent()->getParent()->getParent() ) {

            $institutionThis = $institution->getParent()->getParent()->getParent();
            //echo "inst=".$institutionThis."<br>";

            $name = $institutionThis->getName()."";
            $titleId = null;
            if( $institutionThis->getId() ) {
                $titleId = $institutionThis->getId();
            }
            $pid = null;
            $parent = $institutionThis->getParent();
            if( $parent && $parent->getId() ) {
                $pid = $parent->getId();
            }

            if($name) {
                $elementInfo = array('tablename' => 'Institution', 'id' => $titleId, 'pid' => $pid, 'name' => $name);
                //$elementInfo = $this->getSearchSameObjectUrl($elementInfo, 'small');
                $headInfo[] = $elementInfo;
            }

            //$headInfo[] = 'break-hr';
        }

        //$headInfo[] = 'break-hr';

        return $headInfo;
    }
    public function getSearchSameObjectUrl( $elementInfo, $style=null ) {
        $url = $this->container->get('router')->generate(
            'employees_search_same_object',
            array(
                'tablename' => $elementInfo['tablename'],
                'id' => $elementInfo['id'],
                'name'=> $elementInfo['name']
            )
            //UrlGeneratorInterface::ABSOLUTE_URL
        );

        if( $style ) {
            $name = "<$style>".$elementInfo['name']."</$style>";
        } else {
            $name = $elementInfo['name'];
        }

        $elementInfo = '<a href="'.$url.'">'.$name.'</a>';
        return $elementInfo;
    }
    public function groupByInst( $instArr ) {
        //group by last institution only:
        //Assistant Professor of Pathology and Laboratory Medicine
        //Cytopathology, Gynecologic Pathology
        //Anatomic Pathology
        //Pathology and Laboratory Medicine
        //Weill Cornell Medical College

//        echo "Input instArr: <pre>";
//        print_r($instArr);
//        echo "</pre><br>";
        //return $instArr;

        //1) get pid group
        $firstCombinedArr = array();
        foreach( $instArr as $recordArr ) {
            //echo "0firstTitleId=".$recordArr['titleInfo'][0]['id']."<br>";
            if( array_key_exists('titleInfo',$recordArr) && count($recordArr['titleInfo']) > 0 ) {
                $firstInstPid = $recordArr['instInfo'][0]['pid'];
                //$firstInstId = $recordArr['instInfo'][0]['id'];
                $firstTitleId = $recordArr['titleInfo'][0]['id'];
                if( $firstTitleId ) {
                    $firstCombineId = $firstTitleId . "-" . $firstInstPid;
                    $firstCombinedArr[$firstCombineId][] = $recordArr['instInfo'][0];
                }
                //echo "1firstTitleId=$firstTitleId<br>";
            }
        }
//        echo "firstCombinedArr:<pre>";
//        print_r($firstCombinedArr);
//        echo "</pre><br>";
//        echo "After 1 instArr:<pre>";
//        print_r($instArr);
//        echo "</pre><br>";

        //2) construct a new array using $firstCombinedArr
        $groupInstArr = array();
        foreach( $instArr as $recordArr ) {

            $firstInstPid = null;
            $firstTitleId = null;
            if( array_key_exists('instInfo',$recordArr) && count($recordArr['instInfo']) > 0 ) {
                $firstInstPid = $recordArr['instInfo'][0]['pid'];
                //$firstInstId = $recordArr['instInfo'][0]['id'];
                //$firstTitleId = $recordArr['titleInfo'][0]['id'];
                //$firstCombineId = $firstTitleId."-".$firstInstPid;
            }
            if( array_key_exists('titleInfo',$recordArr) && count($recordArr['titleInfo']) > 0 ) {
                //$firstInstPid = $recordArr['instInfo'][0]['pid'];
                //$firstInstId = $recordArr['instInfo'][0]['id'];
                $firstTitleId = $recordArr['titleInfo'][0]['id'];
                //$firstCombineId = $firstTitleId."-".$firstInstPid;
            }

            //if( $firstTitleId ) {
            //if( $recordArr['headService'] === false ) {
                if (array_key_exists('titleInfo', $recordArr)) {
                    foreach ($recordArr['titleInfo'] as $titleInfoArr) {
                        //echo "PID:$firstInstPid; titleInfoID:$firstTitleId; titleInfoArr:<pre>";
                        //print_r($titleInfoArr);
                        //echo "</pre><br>";
                        $firstTitleId = $titleInfoArr['id'];
                        $groupInstArr[$firstInstPid]['titleInfo'][$firstTitleId] = $this->getSearchSameObjectUrl($titleInfoArr);
                    }
                }
            //}

            $instHrefArr = array();
            foreach( $recordArr['instInfo'] as $instInfoArr ) {

                $thisFirstInstPid = $instInfoArr['pid'];
                $thisFirstCombineId = $firstTitleId."-".$instInfoArr['pid'];
                //echo "firstCombineId=".$firstCombineId."<br>";
                $instHrefEl = $this->getSearchSameObjectUrl($instInfoArr,'small');

                if( array_key_exists($thisFirstCombineId,$firstCombinedArr) ) {

                    $firstHrefElArr = array();
                    foreach( $firstCombinedArr[$thisFirstCombineId] as $firstEl ) {
                        $firstHrefElArr[] = $this->getSearchSameObjectUrl($firstEl,'small');
                    }
                    $instHrefEl = implode(", ",$firstHrefElArr);

                }

                $instHrefArr[$thisFirstInstPid] = $instHrefEl;
            }
            //echo "instInfo:<pre>";
            //print_r($instHrefArr);
            //echo "</pre><br>";
            $groupInstArr[$firstInstPid]['instInfo'] = $instHrefArr;



        }
//        echo "Final groupInstArr:<pre>";
//        print_r($groupInstArr);
//        echo "</pre><br>";

        return $groupInstArr;
    }
    /////////////////////// EOF getHeadInfo ///////////////////////


}