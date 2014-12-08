<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 10/4/13
 * Time: 12:56 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\UserdirectoryBundle\Util;

use Doctrine\Common\Collections\ArrayCollection;

use Oleg\OrderformBundle\Entity\PerSiteSettings;
use Oleg\OrderformBundle\Security\Util\AperioUtil;
use Oleg\UserdirectoryBundle\Entity\Location;
use Oleg\UserdirectoryBundle\Entity\User;
use Oleg\UserdirectoryBundle\Entity\AdministrativeTitle;
use Oleg\UserdirectoryBundle\Entity\Logger;
use Oleg\UserdirectoryBundle\Entity\UsernameType;
use Oleg\UserdirectoryBundle\Form\DataTransformer\GenericTreeTransformer;
use Oleg\UserdirectoryBundle\Security\Util\UserSecurityUtil;

class UserUtil {

    private $usernamePrefix = 'wcmc-cwid';

    public function generateUsersExcel( $em, $serviceContainer ) {
        $inputFileName = __DIR__ . '/../Util/users.xlsx';

        try {
            $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        //$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        //var_dump($sheetData);

        $count = 0;
        $serviceCount = 0;

        $default_time_zone = $serviceContainer->getParameter('default_time_zone');

        $userSecUtil = $serviceContainer->get('user_security_utility');
        $userkeytype = $userSecUtil->getUsernameType($this->usernamePrefix);

        ////////////// add system user /////////////////
        $systemuser = $this->createSystemUser($em,$userkeytype,$default_time_zone);
        ////////////// end of add system user /////////////////

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        //for each user in excel
        for ($row = 2; $row <= $highestRow; $row++){
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);

            //  Insert row data array into the database
//            echo $row.": ";
//            var_dump($rowData);
//            echo "<br>";

            $email = $rowData[0][11];
            list($username, $extra) = explode("@", $email);
            $phone = $rowData[0][8];
            $fax = $rowData[0][12];
            $firstName = $rowData[0][6];
            $lastName = $rowData[0][5];
            $title = $rowData[0][7];
            $office = $rowData[0][10];
            $services = explode("/",$rowData[0][2]);

            //echo "<br>divisions=".$rowData[0][2]." == ";
            //print_r($services);

            //create excel user
            $user = new User();
            $user->setKeytype($userkeytype);
            $user->setPrimaryPublicUserId($username);

            //set unique username
            $usernameUnique = $user->createUniqueUsername();
            $user->setUsername($usernameUnique);
            $user->setUsernameCanonical($usernameUnique);

            $user->setEmail($email);
            $user->setEmailCanonical($email);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setDisplayName($firstName." ".$lastName);
            $user->setPassword("");
            $user->setCreatedby('excel');
            $user->getPreferences()->setTimezone($default_time_zone);

            //add default locations
            $user = $this->addDefaultLocations($user,$systemuser,$em,$serviceContainer);

            //phone, fax, office are stored in Location object
            $mainLocation = $user->getMainLocation();
            $mainLocation->setPhone($phone);
            $mainLocation->setFax($fax);

            //set room object
            $roomObj = $this->getObjectByNameTransformer($office,$systemuser,'RoomList',$em);
            $mainLocation->setRoom($roomObj);

            //title is stored in Administrative Title
            $administrativeTitle = new AdministrativeTitle($systemuser);

            //set title object
            $titleObj = $this->getObjectByNameTransformer($title,$systemuser,'AdminTitleList',$em);
            $administrativeTitle->setName($titleObj);

            $user->addAdministrativeTitle($administrativeTitle);

            //add scanorder Roles
            $user->addRole('ROLE_SCANORDER_SUBMITTER');

            //************** get Aperio group roles and ROLE_SCANORDER_ORDERING_PROVIDER for this user **************//
            //TODO: this should be located on scanorder site
            $aperioUtil = new AperioUtil();
            $userid = $aperioUtil->getUserIdByUserName($username);
            $aperioRoles = $aperioUtil->getUserGroupMembership($userid);
            $stats = $aperioUtil->setUserPathologyRolesByAperioRoles( $user, $aperioRoles );
            //************** end of  Aperio group roles **************//

            foreach( $services as $service ) {

                $service = trim($service);

                if( $service != "" ) {
                    //echo " (".$service.") ";
                    $serviceEntity  = $em->getRepository('OlegUserdirectoryBundle:Service')->findOneByName($service);

                    if( $serviceEntity ) {
                        $administrativeTitle->setService($serviceEntity);
                        $division = $serviceEntity->getParent();
                        $administrativeTitle->setDivision($division);
                        $department = $division->getParent();
                        $administrativeTitle->setDepartment($department);
                        $institution = $department->getParent();
                        $administrativeTitle->setInstitution($institution);
                    } else {
                        //Don't create service if it is not found in the service list
//                        $serviceEntity = new \Oleg\UserdirectoryBundle\Entity\Service();
//                        $serviceEntity->setOrderinlist( $serviceCount );
//                        $serviceEntity->setCreator( $systemuser );
//                        $serviceEntity->setCreatedate( new \DateTime() );
//                        $serviceEntity->setName( trim($service) );
//                        $serviceEntity->setType('default');
//                        $em->persist($serviceEntity);
//                        $em->flush();
//                        $serviceCount = $serviceCount + 10;
                    }
                } //if

            } //foreach

            $user->setEnabled(true);
            $user->setLocked(false);
            $user->setExpired(false);

            $found_user = $em->getRepository('OlegUserdirectoryBundle:User')->findOneByUsername( $user->getUsername() );
            if( $found_user ) {
                //
            } else {
                //echo $username." not found ";
                $em->persist($user);
                $em->flush();
                $count++;


                //**************** create PerSiteSettings for this user **************//
                //TODO: this should be located on scanorder site
                $perSiteSettings = new PerSiteSettings($systemuser);
                $perSiteSettings->setUser($user);
                $params = $em->getRepository('OlegUserdirectoryBundle:SiteParameters')->findAll();
                if( count($params) != 1 ) {
                    throw new \Exception( 'Must have only one parameter object. Found '.count($params).' object(s)' );
                }
                $param = $params[0];
                $institution = $param->getAutoAssignInstitution();
                $perSiteSettings->addPermittedInstitutionalPHIScope($institution);
                $em->persist($perSiteSettings);
                $em->flush();
                //**************** EOF create PerSiteSettings for this user **************//

            }

        }//for each user

        //exit();
        return $count;
    }

    public function setLoginAttempt( $request, $security_content, $em, $options ) {

        $user = null;
        $username = null;
        $roles = null;

        if( !array_key_exists('serverresponse', $options) ) {
            //$options['serverresponse'] = null;
            $options['serverresponse'] = http_response_code();
        }

        $token = $security_content->getToken();

        if( $token ) {
            $user = $security_content->getToken()->getUser();
            $username = $token->getUsername();
            //print_r($user);
            if( $user && is_object($user) ) {
                $roles = $user->getRoles();
            } else {
                $user = null;
            }
        } else {
            $username = $request->get('_username');
        }

        $logger = new Logger($options['sitename']);
        $logger->setUser($user);
        $logger->setRoles($roles);
        $logger->setUsername($username);
        $logger->setIp($request->getClientIp());
        $logger->setUseragent($_SERVER['HTTP_USER_AGENT']);
        $logger->setWidth($request->get('display_width'));
        $logger->setHeight($request->get('display_height'));
        $logger->setEvent($options['event']);
        $logger->setServerresponse($options['serverresponse']);

        //set Event Type
        $eventtype = $em->getRepository('OlegUserdirectoryBundle:EventTypeList')->findOneByName($options['eventtype']);
        $logger->setEventType($eventtype);

        //exit();

        $em->persist($logger);
        $em->flush();

    }

    public function getMaxIdleTime($em) {

        $params = $em->getRepository('OlegUserdirectoryBundle:SiteParameters')->findAll();

        if( !$params ) {
            //new DB does not have SiteParameters object
            return 1800; //30 min
            //throw new \Exception( 'Parameter object is not found' );
        }

        if( count($params) != 1 ) {
            throw new \Exception( 'Must have only one parameter object. Found '.count($params).'object(s)' );
        }

        $param = $params[0];
        $maxIdleTime = $param->getMaxIdleTime();

        //return time in seconds
        $maxIdleTime = $maxIdleTime * 60;

        return $maxIdleTime;
    }

    public function getMaxIdleTimeAndMaintenance($em, $sc, $container) {

        $params = $em->getRepository('OlegUserdirectoryBundle:SiteParameters')->findAll();

        if( !$params ) {
            //new DB does not have SiteParameters object
            $res = array(
                'maxIdleTime' => 1800,
                'maintenance' => false
            );
            return $res; //30 min
            //throw new \Exception( 'Parameter object is not found' );
        }

        if( count($params) != 1 ) {
            throw new \Exception( 'Must have only one parameter object. Found '.count($params).'object(s)' );
        }

        $param = $params[0];
        $maxIdleTime = $param->getMaxIdleTime();
        $maintenance = $param->getMaintenance();

        //do not use maintenance for admin
        if( $sc->isGranted('ROLE_ADMIN') ) {
            $maintenance = false;
        }

        $debug = in_array( $container->get('kernel')->getEnvironment(), array('test', 'dev') );
        if( $debug ) {
            $maintenance = false;
        }

        //return time in seconds
        $maxIdleTime = $maxIdleTime * 60;

        $res = array(
            'maxIdleTime' => $maxIdleTime,
            'maintenance' => $maintenance
        );

        return $res;
    }

    //return parameter specified by $setting. If the first time login when site parameter does not exist yet, return -1.
    public function getSiteSetting($em,$setting) {

        $params = $em->getRepository('OlegUserdirectoryBundle:SiteParameters')->findAll();

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

        if( $setting == null ) {
            return $param;
        }

        $getSettingMethod = "get".$setting;
        $res = $param->$getSettingMethod();

        return $res;
    }

    public function generateUsernameTypes($em,$user=null) {

        if( $user == null ) {
            $user = $this->createSystemUser($em,null,null);
        }

        $entities = $em->getRepository('OlegUserdirectoryBundle:UsernameType')->findAll();

        if( $entities ) {
            return -1;
        }

        $elements = array(
            'wcmc-cwid'=>'WCMC CWID',
			'aperio'=>'Aperio eSlide Manager'
            //'autogenerated'=>'Autogenerated',
            //'local-user'=>'Local User'
        );

        $count = 1;
        foreach( $elements as $key=>$value ) {

            $entity = new UsernameType();
            $this->setDefaultList($entity,$count,$user,null);
            $entity->setName( trim($value) );
            $entity->setAbbreviation( trim($key) );

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);
    }

    public function setDefaultList( $entity, $count, $user, $name=null ) {
        $entity->setOrderinlist( $count );
        $entity->setCreator( $user );
        $entity->setCreatedate( new \DateTime() );
        $entity->setType('default');
        if( $name ) {
            $entity->setName( trim($name) );
        }
        return $entity;
    }

    public function createSystemUser($em,$userkeytype,$default_time_zone) {

        $userSecUtil = new UserSecurityUtil($em,null,null);

        $found_user = $userSecUtil->findSystemUser();

        if( !$found_user ) {

            $adminemail = $this->getSiteSetting($em,'siteEmail');
            $systemuser = new User();
            $systemuser->setKeytype($userkeytype);
            $systemuser->setPrimaryPublicUserId('system');
            $systemuser->setUsername('system');
            $systemuser->setUsernameCanonical('system');
            $systemuser->setEmail($adminemail);
            $systemuser->setEmailCanonical($adminemail);
            $systemuser->setPassword("");
            $systemuser->setCreatedby('system');
            $systemuser->addRole('ROLE_SCANORDER_PROCESSOR');
            $systemuser->getPreferences()->setTimezone($default_time_zone);
            $systemuser->setEnabled(true);
            $systemuser->setLocked(true); //system is locked, so no one can logged in with this account
            $systemuser->setExpired(false);
            $em->persist($systemuser);
            $em->flush();

        } else {

            $systemuser = $found_user;

        }

        return $systemuser;
    }

    public function getDefaultUsernameType($em) {
        $userkeytype = null;
        $userkeytypes = $em->getRepository('OlegUserdirectoryBundle:UsernameType')->findBy(array(),array('orderinlist' => 'ASC'),1);   //limit result by 1
        //echo "userkeytypes=".$userkeytypes."<br>";
        //print_r($userkeytypes);
        if( $userkeytypes && count($userkeytypes) > 0 ) {
            $userkeytype = $userkeytypes[0];
        }
        return $userkeytype;
    }



    //academic titles, administrative titles, sevices, and divisions: if a object has a non-empty end date that is older than today's date, it is a "past" object.
    //$time: 'current_only' - search only current, 'past_only' - search only past, 'all' - search current and past (no filter)
    public function getCriteriaStrByTime( $dql, $time, $searchField, $inputCriteriastr) {

        //echo "time filter: time=".$time."<br>";

        $criteriastr = "";
        $curdate = date("Y-m-d", time());

        switch( $time ) {
            case "current_only":
                //with an empty or future 'end date'

                //titles: endDate
                if( $searchField == null || $searchField == 'administrativeTitles' ) {
                    $criteriastr .= "(administrativeTitles.endDate IS NULL OR administrativeTitles.endDate > '".$curdate."')";
                    $criteriastr .= " OR ";
                }
                if( $searchField && $searchField == 'appointmentTitles' ) {
                    $criteriastr .= "(appointmentTitles.endDate IS NULL OR appointmentTitles.endDate > '".$curdate."')";
                    $criteriastr .= " OR ";
                }

                //research lab: dissolvedDate
                if( $searchField == null || $searchField == 'researchLabs' ) {
                    $criteriastr .= "(researchLabs.dissolvedDate IS NULL OR researchLabs.dissolvedDate > '".$curdate."')";
                    $criteriastr .= " OR ";
                }

                //Employment Status should have at least one group where Date of Termination is empty
                if( $searchField == null || $searchField == 'employmentStatus' ) {
                    $criteriastr .= "(";
                    $criteriastr .= "(employmentStatus IS NULL)";
                    $criteriastr .= " OR ";
                    $criteriastr .= "(employmentStatus.terminationDate IS NULL)";
                    $criteriastr .= " OR ";
                    $criteriastr .= "(employmentStatus.hireDate IS NOT NULL AND (employmentStatus.terminationDate IS NULL OR employmentStatus.terminationDate > '".$curdate."') )";
                    $criteriastr .= ")";
                }

                break;
            case "past_only":
                //past or empty or future 'end date'

                //titles: endDate
                if( $searchField == null || $searchField == 'administrativeTitles' ) {
                    $criteriastr .= "(administrativeTitles.endDate IS NOT NULL AND administrativeTitles.endDate < '".$curdate."')";
                    $criteriastr .= " OR ";
                }
                if( $searchField && $searchField == 'appointmentTitles' ) {
                    $criteriastr .= "(appointmentTitles.endDate IS NOT NULL AND appointmentTitles.endDate < '".$curdate."')";
                    $criteriastr .= " OR ";
                }

                //research lab: dissolvedDate
                if( $searchField == null || $searchField == 'researchLabs' ) {
                    $criteriastr .= "(researchLabs.dissolvedDate IS NOT NULL AND researchLabs.dissolvedDate < '".$curdate."')";
                    $criteriastr .= " OR ";
                }

                //Each group of fields in the employment status should have a non-empty Date of Termination.
                if( $searchField == null || $searchField == 'employmentStatus' ) {
                    //TODO: should the serach result display only users with all employment status have a non-empty Date of Termination?
                    $criteriastr .= "(";
                    $criteriastr .= "(employmentStatus IS NOT NULL)";
                    $criteriastr .= " AND ";
                    $criteriastr .= "(employmentStatus.hireDate IS NOT NULL AND employmentStatus.terminationDate IS NOT NULL AND employmentStatus.terminationDate < '".$curdate."')";
                    $criteriastr .= ")";
                }

                break;
            default:
                //do nothing
        }

        if( $inputCriteriastr && $inputCriteriastr != "" ) {
            if( $criteriastr != "" ) {
                $inputCriteriastr = $inputCriteriastr . " AND (" . $criteriastr . ")";
            }
        } else {
            $inputCriteriastr = $criteriastr;
        }

        return $inputCriteriastr;
    }


    public function indexLocation( $search, $request, $container, $doctrine ) {

        $repository = $doctrine->getRepository('OlegUserdirectoryBundle:Location');
        $dql =  $repository->createQueryBuilder("location");
        $dql->addSelect('location');

        $dql->leftJoin("location.user", "locationuser");
        $dql->leftJoin("location.service", "service");
        $dql->leftJoin("service.heads", "heads");

        $postData = $request->query->all();

        $sort = null;
        if( isset($postData['sort']) ) {
            //check for location sort
            if(
                strpos($postData['sort'],'location.') !== false ||
                strpos($postData['sort'],'heads.') !== false
            ) {
                $sort = $postData['sort'];
            }
        }

        if( $sort == null ) {
            $dql->orderBy("location.name","ASC");
        }

        //search
        $criteriastr = "";

        //Show ONLY orphaned locations
        $criteriastr .= "locationuser IS NULL";

        switch( $search ) {
            case "Common Locations":
                $criteriastr .= "";
                break;
            case "Pathology Common Locations":
                //filter by Department=Pathology and Laboratory Medicine
                $dql->leftJoin("location.department", "department");
                $criteriastr .= " AND ";
                $criteriastr .= "department.name LIKE '%Pathology%'";
                break;
            case "WCMC & NYP Pathology Common Locations":
                //filter by Institution=Weill Cornell Medical College & NYP and Department=Pathology
                $dql->leftJoin("location.department", "department");
                $dql->leftJoin("location.institution", "institution");
                $criteriastr .= " AND ";
                $criteriastr .= "department.name LIKE '%Pathology%'";
                $criteriastr .= " AND (";
                $criteriastr .= "institution.name LIKE 'Weill Cornell Medical College'";
                $criteriastr .= " OR ";
                $criteriastr .= "institution.name LIKE 'New York Hospital'";
                $criteriastr .= ")";

                break;
            case "WCMC Pathology Common Locations":
                //filter by Institution=Weill Cornell Medical College and Department=Pathology and Laboratory Medicine
                $dql->leftJoin("location.department", "department");
                $dql->leftJoin("location.institution", "institution");
                $criteriastr .= " AND ";
                $criteriastr .= "department.name LIKE 'Pathology and Laboratory Medicine'";
                $criteriastr .= " AND ";
                $criteriastr .= "institution.name LIKE 'Weill Cornell Medical College'";
                break;
            case "NYP Pathology Common Locations":
                //filter by Institution=New York Hospital and Department=Pathology and Laboratory Medicine
                $dql->leftJoin("location.department", "department");
                $dql->leftJoin("location.institution", "institution");
                $criteriastr .= " AND ";
                $criteriastr .= "department.name LIKE 'Pathology'";
                $criteriastr .= " AND ";
                $criteriastr .= "institution.name LIKE 'New York Hospital'";
                break;
            default:
                //search by name
                $criteriastr .= " AND location.name LIKE '%".$search."%'";
        }

        //The "Supervisor" column for the orphaned Location should be the person who belongs to the same "Service" as the orphan location according
        //to their Administrative or Academic Title, and who has the "Head of this Service" checkmarked checked for this service.
        //Since multiple people can check this checkmark for a given service, list all of them, separated by commas.


        $dql->where($criteriastr);

        //pass sorting parameters directly to query; Somehow, knp_paginator stoped correctly create pagination according to sorting parameters
        if( $sort ) {
            $dql = $dql . " ORDER BY $postData[sort] $postData[direction]";
        }

        //echo "Location dql=".$dql."<br>";

        $em = $doctrine->getManager();
        $query = $em->createQuery($dql);    //->setParameter('now', date("Y-m-d", time()));

        $limitFlag = true;
        if( $limitFlag ) {
            $limit = 10;
            $paginator  = $container->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $request->query->get('page', 1), /*page number*/
                $limit/*limit per page*/
            );
        } else {
            $pagination = $query->getResult();
        }

        return $pagination;

    }





    public function processInstTree( $treeholder, $em, $sc ) {

        $institution = $treeholder->getInstitution();
        $department = $treeholder->getDepartment();
        $division = $treeholder->getDivision();
        $service = $treeholder->getService();

        $user = $sc->getToken()->getUser();

        $department = $em->getRepository('OlegUserdirectoryBundle:Institution')->checkAndSetParent($user,$treeholder,$institution,$department);

        $division = $em->getRepository('OlegUserdirectoryBundle:Institution')->checkAndSetParent($user,$treeholder,$department,$division);

        $service = $em->getRepository('OlegUserdirectoryBundle:Institution')->checkAndSetParent($user,$treeholder,$division,$service);

        //set author if not set
        $this->setUpdateInfo($treeholder,$em,$sc);

        //exit('eof tree');
    }


    public function setUpdateInfo( $entity, $em, $sc ) {

        if( !$entity ) {
            return;
        }

        $user = $sc->getToken()->getUser();

        $author = $em->getRepository('OlegUserdirectoryBundle:User')->find($user->getId());

        //set author and roles if not set
        if( !$entity->getAuthor() ) {
            $entity->setAuthor($author);
        }

        if( $entity->getId() ) {
            if( $entity->getUpdateAuthor() == null ) {  //update author can be set to any user, not a current user
                $entity->setUpdateAuthor($author);
            }
            $entity->setUpdateAuthorRoles($entity->getUpdateAuthor()->getRoles());
        }
    }

    //add two default locations: Home and Main Office
    public function addDefaultLocations($entity,$creator,$em,$container) {

        if( $creator == null ) {
            $userSecUtil = $container->get('user_security_utility');
            $creator = $userSecUtil->findSystemUser();

            if( !$creator ) {
                $creator = $entity;
            }
        }

        //echo "creator=".$creator.", id=".$creator->getId()."<br>";

        //Main Office Location
        $mainLocation = new Location($creator);
        $mainLocation->setName('Main Office');
        $mainLocation->setRemovable(false);
        $mainLocType = $em->getRepository('OlegUserdirectoryBundle:LocationTypeList')->findOneByName("Employee Office");
        $mainLocation->setLocationType($mainLocType);
        $entity->addLocation($mainLocation);

        //Home Location
        $homeLocation = new Location($creator);
        $homeLocation->setName('Home');
        $homeLocation->setRemovable(false);
        $homeLocType = $em->getRepository('OlegUserdirectoryBundle:LocationTypeList')->findOneByName("Employee Home");
        $homeLocation->setLocationType($homeLocType);
        $entity->addLocation($homeLocation);

        return $entity;
    }


    public function replaceAdminTitleByObject($entity,$creator,$em,$container) {

        if( $creator == null ) {
            $userSecUtil = $container->get('user_security_utility');
            $creator = $userSecUtil->findSystemUser();

            if( !$creator ) {
                $creator = $entity;
            }
        }

        $adminTitle = $entity->getAdministrativeTitles()->first();
        $adminTitleName = $adminTitle->getName();

        if( $adminTitleName == null ) {
            return;
        }

//        $adminTitleNameObject = $em->getRepository('OlegUserdirectoryBundle:AdminTitleList')->findOneByName($adminTitleName);
//
//        if( !$adminTitleNameObject ) {
//
//            //generate admin Title Name
//            $treeTransf = new GenericTreeTransformer($em,$creator);
//            $adminTitleNameObject = $treeTransf->createNewEntity($adminTitleName,"AdminTitleList",$creator);
//
//            $em->persist($adminTitleNameObject);
//        }

        $adminTitleNameObject = $this->getObjectByNameTransformer( $adminTitleName, $creator, "AdminTitleList", $em );

        $adminTitle->setName($adminTitleNameObject);

    }

    //get string to object using transformer
    public function getObjectByNameTransformer( $name, $creator, $className, $em ) {

        if( $name == null || $name == "" ) {
            return null;
        }

        $nameObject = $em->getRepository('OlegUserdirectoryBundle:'.$className)->findOneByName($name);

        if( !$nameObject ) {

            //generate admin Title Name
            $treeTransf = new GenericTreeTransformer($em,$creator);
            $nameObject = $treeTransf->createNewEntity($name,$className,$creator);

            $em->persist($nameObject);
        }

        return $nameObject;
    }


    //clone user according to issue #392
    public function makeUserClone( $suser, $duser ) {

        //Time Zone: America / New York
        $duser->setPreferences( clone $suser->getPreferences() );

        //Administrative Title Type
        foreach( $suser->getAdministrativeTitles() as $object ) {
            $clone = clone $object;
            $duser->addAdministrativeTitle( $clone );
        }

        //Academic Titles
        foreach( $suser->getAppointmentTitles() as $object ) {
            $clone = clone $object;
            $duser->addAppointmentTitle( $clone );
        }

        //Locations
        //1) remove all locations
        $homeLocations = new ArrayCollection();
        foreach( $duser->getLocations() as $object ) {
            if( $object->getLocationType()->getName() == "Employee Home" ) {
                $homeLocations->add($object);
            }
            $duser->removeLocation($object);
        }
        //2) add cloned locations
        foreach( $suser->getLocations() as $object ) {
            if( $object->getLocationType()->getName() != "Employee Home" ) {
                $clone = clone $object;
                $duser->addLocation( $clone );
            }
        }
        //3) set home as the last location
        foreach( $homeLocations as $object ) {
            $duser->addLocation( $object );
        }


        //Medical License: Country, State
        $sMedicalLicense = $suser->getCredentials()->getStateLicense()->first();
        $duser->getCredentials()->getStateLicense()->first()->setCountry($sMedicalLicense->getCountry());
        $duser->getCredentials()->getStateLicense()->first()->setState($sMedicalLicense->getState());

        return $duser;
    }

}