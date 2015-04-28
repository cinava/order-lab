<?php

namespace Oleg\UserdirectoryBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Intl\Intl;

use Oleg\OrderformBundle\Entity\PerSiteSettings;
use Oleg\UserdirectoryBundle\Entity\AdministrativeTitle;
use Oleg\UserdirectoryBundle\Entity\BuildingList;
use Oleg\UserdirectoryBundle\Entity\CompletionReasonList;
use Oleg\UserdirectoryBundle\Entity\DocumentTypeList;
use Oleg\UserdirectoryBundle\Entity\EmploymentType;
use Oleg\UserdirectoryBundle\Entity\FellowshipSubspecialty;
use Oleg\UserdirectoryBundle\Entity\FellowshipTitleList;
use Oleg\UserdirectoryBundle\Entity\GeoLocation;
use Oleg\UserdirectoryBundle\Entity\HonorTrainingList;
use Oleg\UserdirectoryBundle\Entity\InstitutionType;
use Oleg\UserdirectoryBundle\Entity\Location;
use Oleg\UserdirectoryBundle\Entity\MedicalSpecialties;
use Oleg\UserdirectoryBundle\Entity\MedicalTitleList;
use Oleg\UserdirectoryBundle\Entity\ResearchLab;
use Oleg\UserdirectoryBundle\Entity\ResidencySpecialty;
use Oleg\UserdirectoryBundle\Entity\SourceOrganization;
use Oleg\UserdirectoryBundle\Entity\SourceSystemList;
use Oleg\UserdirectoryBundle\Entity\TrainingDegreeList;
use Oleg\UserdirectoryBundle\Entity\User;

use Oleg\UserdirectoryBundle\Entity\SiteParameters;
use Oleg\UserdirectoryBundle\Util\UserUtil;
use Oleg\UserdirectoryBundle\Entity\Roles;
use Oleg\UserdirectoryBundle\Entity\Institution;
use Oleg\UserdirectoryBundle\Entity\Department;
use Oleg\UserdirectoryBundle\Entity\Division;
use Oleg\UserdirectoryBundle\Entity\Service;
use Oleg\UserdirectoryBundle\Entity\States;
use Oleg\UserdirectoryBundle\Entity\BoardCertifiedSpecialties;
use Oleg\UserdirectoryBundle\Entity\EmploymentTerminationType;
use Oleg\UserdirectoryBundle\Entity\EventTypeList;
use Oleg\UserdirectoryBundle\Entity\IdentifierTypeList;
use Oleg\UserdirectoryBundle\Entity\FellowshipTypeList;
use Oleg\UserdirectoryBundle\Entity\ResidencyTrackList;
use Oleg\UserdirectoryBundle\Entity\LocationTypeList;
use Oleg\UserdirectoryBundle\Entity\Countries;
use Oleg\UserdirectoryBundle\Entity\Equipment;
use Oleg\UserdirectoryBundle\Entity\EquipmentType;
use Oleg\UserdirectoryBundle\Entity\LocationPrivacyList;
use Oleg\UserdirectoryBundle\Entity\RoleAttributeList;
use Oleg\UserdirectoryBundle\Entity\LanguageList;
use Symfony\Component\Intl\Locale\Locale;


/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * Admin Page
     *
     * @Route("/lists/", name="user_admin_index")
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:Admin:index.html.twig")
     */
    public function indexAction()
    {

        $environment = 'dev'; //default

        $em = $this->getDoctrine()->getManager();
        $params = $roles = $em->getRepository('OlegUserdirectoryBundle:SiteParameters')->findAll();

        if( count($params) > 1 ) {
            throw new \Exception( 'Must have only one parameter object. Found '.count($params).'object(s)' );
        }

        if( count($params) == 1 ) {
            $param = $params[0];
            $environment = $param->getEnvironment();
        }

        return $this->render('OlegUserdirectoryBundle:Admin:index.html.twig', array('environment'=>$environment));
    }


    /**
     * Populate DB
     *
     * @Route("/populate-all-lists-with-default-values", name="user_generate_all")
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:Admin:index.html.twig")
     */
    public function generateAllAction()
    {
        $userutil = new UserUtil();
        $user = $this->get('security.context')->getToken()->getUser();

        $max_exec_time = ini_get('max_execution_time');
        ini_set('max_execution_time', 900); //900 seconds = 15 minutes

        $default_time_zone = $this->container->getParameter('default_time_zone');

        $count_institutiontypes = $this->generateInstitutionTypes();         //must be first
        $count_institution = $this->generateInstitutions();                  //must be first

        $count_siteParameters = $this->generateSiteParameters();    //can be run only after institution generation

        $count_roles = $this->generateRoles();
        $count_employmentTypes = $this->generateEmploymentTypes();
        $count_terminationTypes = $this->generateTerminationTypes();
        $count_eventTypeList = $this->generateEventTypeList();
        $count_usernameTypeList = $userutil->generateUsernameTypes($this->getDoctrine()->getManager(),$user);
        $count_identifierTypeList = $this->generateIdentifierTypeList();
        $count_fellowshipTypeList = $this->generateFellowshipTypeList();
        $count_residencyTrackList = $this->generateResidencyTrackList();

        $count_medicalTitleList = $this->generateMedicalTitleList();
        $count_medicalSpecialties = $this->generateMedicalSpecialties();

        $count_equipmentType = $this->generateEquipmentType();
        $count_equipment = $this->generateEquipment();

        $count_states = $this->generateStates();
        $count_countryList = $this->generateCountryList();
        $count_languages = $this->generateLanguages();

        $count_locationTypeList = $this->generateLocationTypeList();
        $count_locprivacy = $this->generateLocationPrivacy();

        $count_buildings = $this->generateBuildings();
        $count_locations = $this->generateLocations();

        $count_reslabs = $this->generateResLabs();

        $count_users = $userutil->generateUsersExcel($this->getDoctrine()->getManager(),$this->container);

        $count_testusers = $this->generateTestUsers();

        $count_boardSpecialties = $this->generateBoardSpecialties();

        $count_sourcesystems = $this->generateSourceSystems();

        $count_documenttypes = $this->generateDocumentTypes();

        //training
        $count_completionReasons = $this->generateCompletionReasons();
        $count_trainingDegrees = $this->generateTrainingDegrees();
        //$count_majorTrainings = $this->generateMajorTrainings();
        //$count_minorTrainings = $this->generateMinorTrainings();
        $count_HonorTrainings = $this->generateHonorTrainings();
        $count_FellowshipTitles = $this->generateFellowshipTitles();
        $count_residencySpecialties = $this->generateResidencySpecialties();

        $count_sourceOrganizations = $this->generatesourceOrganizations();

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Generated Tables: '.
            'Source Systems='.$count_sourcesystems.', '.
            'Roles='.$count_roles.', '.
            'Site Settings='.$count_siteParameters.', '.
            'Institution Types='.$count_institutiontypes.', '.
            'Institutions='.$count_institution.', '.
            'Users='.$count_users.', '.
            'Test Users='.$count_testusers.', '.
            'Board Specialties='.$count_boardSpecialties.', '.
            'Employment Types='.$count_employmentTypes.', '.
            'Employment Types of Termination='.$count_terminationTypes.', '.
            'Event Log Types='.$count_eventTypeList.', '.
            'Username Types='.$count_usernameTypeList.', '.
            'Identifier Types='.$count_identifierTypeList.', '.
            'Residency Tracks='.$count_residencyTrackList.', '.
            'Fellowship Types='.$count_fellowshipTypeList.', '.
            'Medical Titles='.$count_medicalTitleList.', '.
            'Medical Specialties='.$count_medicalSpecialties.', '.
            'Equipment Types='.$count_equipmentType.', '.
            'Equipment='.$count_equipment.', '.
            'Location Types='.$count_locationTypeList.', '.
            'Location Privacy='.$count_locprivacy.', '.
            'States='.$count_states.', '.
            'Countries='.$count_countryList.', '.
            'Languages='.$count_languages.', '.
            'Locations='.$count_locations.', '.
            'Buildings='.$count_buildings.', '.
            'Reaserch Labs='.$count_reslabs.', '.
            'Completion Reasons='.$count_completionReasons.', '.
            'Training Degrees='.$count_trainingDegrees.', '.
            'Residency Specialties='.$count_residencySpecialties.', '.
            //'Major Trainings ='.$count_majorTrainings.', '.
            //'Minor Trainings ='.$count_minorTrainings.', '.
            'Honor Trainings='.$count_HonorTrainings.', '.
            'Fellowship Titles='.$count_FellowshipTitles.', '.
            'Document Types='.$count_documenttypes.', '.
            'Source Organizations='.$count_sourceOrganizations.' '.

            ' (Note: -1 means that this table is already exists)'
        );


        ini_set('max_execution_time', $max_exec_time); //set back to the original value

        return $this->redirect($this->generateUrl('user_admin_index'));
    }


    /**
     * @Route("/populate-residency-specialties-with-default-values", name="generate_residencyspecialties")
     * @Method("GET")
     * @Template()
     */
    public function generateResidencySpecialtiesAction()
    {

        $count = $this->generateResidencySpecialties();
        if( $count >= 0 ) {

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Created '.$count. ' Residency Specialties'
            );

            return $this->redirect($this->generateUrl('user_admin_index'));

        } else {

            $this->get('session')->getFlashBag()->add(
                'notice',
                'This table is already exists!'
            );

            return $this->redirect($this->generateUrl('user_admin_index'));
        }

    }



//////////////////////////////////////////////////////////////////////////////

    public function setDefaultList( $entity, $count, $user, $name=null ) {
//        $entity->setOrderinlist( $count );
//        $entity->setCreator( $user );
//        $entity->setCreatedate( new \DateTime() );
//        $entity->setType('default');
//        if( $name ) {
//            $entity->setName( trim($name) );
//        }
//        return $entity;
        $userutil = new UserUtil();
        return $userutil->setDefaultList( $entity, $count, $user, $name );
    }

   
    //Generate or Update roles
    public function generateRoles() {

        $em = $this->getDoctrine()->getManager();

        //generate role can update the role too
//        $entities = $em->getRepository('OlegUserdirectoryBundle:Roles')->findAll();
//        if( $entities ) {
//            //return -1;
//        }

        //Note: fos user has role ROLE_SCANORDER_SUPER_ADMIN

        $types = array(

            //////////// general roles are set by security.yml only ////////////

            //general super admin role for all sites
            "ROLE_PLATFORM_ADMIN" => array("Platform Administrator","Full access for all sites"),
            "ROLE_PLATFORM_DEPUTY_ADMIN" => array("Deputy Platform Administrator",'The same as "Platform Administrator" role can do except assign or remove "Platform Administrator" or "Deputy Platform Administrator" roles'),
            //"ROLE_BANNED" => "Banned user for all sites",                 //general super admin role for all sites
            //"ROLE_UNAPPROVED" => "Unapproved User",                       //general unapproved user

            //////////// Scanorder roles ////////////
            "ROLE_SCANORDER_ADMIN" => array("ScanOrder Administrator","Full access for Scan Order site"),
            "ROLE_SCANORDER_PROCESSOR" => array("ScanOrder Processor","Allow to view all orders and change scan order status"),

            "ROLE_SCANORDER_DIVISION_CHIEF" => array("ScanOrder Division Chief","Allow to view and amend all orders for this division(institution)"),  //view or modify all orders of the same division(institution)
            "ROLE_SCANORDER_SERVICE_CHIEF" => array("ScanOrder Service Chief","Allow to view and amend all orders for this service"),    //view or modify all orders of the same service

            "ROLE_SCANORDER_DATA_QUALITY_ASSURANCE_SPECIALIST" => array("ScanOrder Data Quality Assurance Specialist","Allow to make data quality modification"),

            //"ROLE_USER" => "User", //this role must be always assigned to the authenticated user. Required by fos user bundle.

            "ROLE_SCANORDER_SUBMITTER" => array("ScanOrder Submitter","Allow submit new orders, amend own order"),
            "ROLE_SCANORDER_ORDERING_PROVIDER" => array("ScanOrder Ordering Provider","Allow submit new orders, amend own order"),

            "ROLE_SCANORDER_PATHOLOGY_FELLOW" => array("ScanOrder Pathology Fellow",""),
            "ROLE_SCANORDER_PATHOLOGY_FACULTY" => array("ScanOrder Pathology Faculty",""),

            "ROLE_SCANORDER_COURSE_DIRECTOR" => array("ScanOrder Course Director","Allow to be a Course Director in Educational orders"),
            "ROLE_SCANORDER_PRINCIPAL_INVESTIGATOR" => array("ScanOrder Principal Investigator","Allow to be a Principal Investigator in Research orders"),

            "ROLE_SCANORDER_UNAPPROVED_SUBMITTER" => array("ScanOrder Unapproved Submitter","Does not allow to visit Scan Order site"),
            "ROLE_SCANORDER_BANNED" => array("ScanOrder Banned User","Does not allow to visit Scan Order site"),

            "ROLE_SCANORDER_ONCALL_TRAINEE" => array("OrderPlatform On Call Trainee","Allow to see the phone numbers & email of Home location"),
            "ROLE_SCANORDER_ONCALL_ATTENDING" => array("OrderPlatform On Call Attending","Allow to see the phone numbers & email of Home location"),

            //////////// EmployeeDirectory roles ////////////
            "ROLE_USERDIRECTORY_ADMIN" => array("EmployeeDirectory Administrator","Full access for Employee Directory site"),
            "ROLE_USERDIRECTORY_EDITOR" => array("EmployeeDirectory Editor","Allow to edit all employees; Can not change roles for users, but can grant access via access requests"),
            "ROLE_USERDIRECTORY_OBSERVER" => array("EmployeeDirectory Observer","Allow to view all employees"),
            "ROLE_USERDIRECTORY_BANNED" => array("EmployeeDirectory Banned User","Does not allow to visit Employee Directory site"),
            "ROLE_USERDIRECTORY_UNAPPROVED" => array("EmployeeDirectory Unapproved User","Does not allow to visit Employee Directory site"),

        );

        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $types as $role => $aliasDescription ) {

            $alias = $aliasDescription[0];
            $description = $aliasDescription[1];

            $entity = $em->getRepository('OlegUserdirectoryBundle:Roles')->findOneByName(trim($role));

            if( !$entity ) {
                $entity = new Roles();
                $this->setDefaultList($entity,$count,$username,null);
            }

            $entity->setName( trim($role) );
            $entity->setAlias( trim($alias) );
            $entity->setDescription( trim($description) );

            //set attributes for ROLE_SCANORDER_ONCALL_TRAINEE
            if( $role == "ROLE_SCANORDER_ONCALL_TRAINEE" ) {
                $attr = new RoleAttributeList();
                $this->setDefaultList($attr,1,$username,"Call Pager");
                $attr->setValue("(111) 111-1111");
                $entity->addAttribute($attr);
            }
            //set attributes for ROLE_SCANORDER_ONCALL_ATTENDING
            if( $role == "ROLE_SCANORDER_ONCALL_ATTENDING" ) {
                $attr = new RoleAttributeList();
                $this->setDefaultList($attr,10,$username,"Call Pager");
                $attr->setValue("(222) 222-2222");
                $entity->addAttribute($attr);
            }

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);
    }

    public function generateSiteParameters() {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:SiteParameters')->findAll();

        if( $entities ) {
            return -1;
        }

        $types = array(
            "maxIdleTime" => "30",
            "environment" => "dev",
            "siteEmail" => "oli2002@med.cornell.edu", //"slidescan@med.cornell.edu",

            "smtpServerAddress" => "smtp.med.cornell.edu",

            "aDLDAPServerAddress" => "cumcdcp02.a.wcmc-ad.net",
            "aDLDAPServerPort" => "389",
            "aDLDAPServerOu" => "a.wcmc-ad.net",    //used for DC
            "aDLDAPServerAccountUserName" => "svc_aperio_spectrum",
            "aDLDAPServerAccountPassword" => "Aperi0,123",
            "ldapExePath" => "../src/Oleg/UserdirectoryBundle/Util/",
            "ldapExeFilename" => "LdapSaslCustom.exe",

            "dbServerAddress" => "127.0.0.1",
            "dbServerPort" => "null",
            "dbServerAccountUserName" => "symfony2",
            "dbServerAccountPassword" => "symfony2",
            "dbDatabaseName" => "ScanOrder",

            "institutionurl" => "http://weill.cornell.edu",
            "institutionname" => "Weill Cornell Medical College",
            "departmenturl" => "http://www.cornellpathology.com",
            "departmentname" => "Pathology and Laboratory Medicine Department",

            "maintenance" => false,
            //"maintenanceenddate" => null,
            "maintenancelogoutmsg" =>   'The scheduled maintenance of this software has begun.'.
                                        ' The administrators are planning to return this site to a fully functional state on or before [[datetime]].'.
                                        'If you were in the middle of entering order information, it was saved as an "Unsubmitted" order '.
                                        'and you should be able to submit that order after the maintenance is complete.',
            "maintenanceloginmsg" =>    'The scheduled maintenance of this software has begun. The administrators are planning to return this site to a fully '.
                                        'functional state on or before [[datetime]]. If you were in the middle of entering order information, '.
                                        'it was saved as an "Unsubmitted" order and you should be able to submit that order after the maintenance is complete.',

            //uploads
            "avataruploadpath" => "directory/avatars",
            "employeesuploadpath" => "directory/documents",
            "scanuploadpath" => "scan-order/documents",

            "mainHomeTitle" => "Welcome to the O R D E R platform!",
            "listManagerTitle" => "List Manager",
            "eventLogTitle" => "Event Log",
            "siteSettingsTitle" => "Site Settings",
            "contentAboutPage" => '
                <p>
                    This site is built on the platform titled "O R D E R" (as in the opposite of disorder).
                </p>

                <p>
                    Designers: Victor Brodsky, Oleg Ivanov
                </p>

                <p>
                    Developer: Oleg Ivanov
                </p>

                <p>
                    Quality Assurance Testers: Oleg Ivanov, Steven Bowe, Emilio Madrigal
                </p>

                <p>
                    We are continuing to improve this software. If you have a suggestion or believe you have encountered an issue, please don\'t hesitate to email
                <a href="mailto:slidescan@med.cornell.edu" target="_top">slidescan@med.cornell.edu</a> and attach relevant screenshots.
                </p>

                <br>

                <p>
                O R D E R is made possible by:
                </p>

                <br>

                <p>

                        <ul>


                    <li>
                        <a href="http://php.net">PHP</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://symfony.com">Symfony</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://doctrine-project.org">Doctrine</a>
                    </li>

                    <br>                  
					
					<li>
                        <a href="https://msdn.microsoft.com/en-us/library/aa366156.aspx">MSDN library: ldap_bind_s</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/symfony/SwiftmailerBundle">SwiftmailerBundle</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/symfony/AsseticBundle">AsseticBundle</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/FriendsOfSymfony/FOSUserBundle">FOSUserBundle</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://phpexcel.codeplex.com/">PHP Excel</a>
                    </li>

                    <br>

                    <li>

                        <a href="https://github.com/1up-lab/OneupUploaderBundle">OneupUploaderBundle</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://www.dropzonejs.com/">Dropzone JS</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/KnpLabs/KnpPaginatorBundle">KnpPaginatorBundle</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://twig.sensiolabs.org/doc/advanced.html">Twig</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://getbootstrap.com/">Bootstrap</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/kriskowal/q">JS promises Q</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://jquery.com">jQuery</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://jqueryui.com/">jQuery UI</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/RobinHerbots/jquery.inputmask">jQuery Inputmask</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://ivaynberg.github.io/select2/">Select2</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://www.eyecon.ro/bootstrap-datepicker/">Bootstrap Datepicker</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://www.malot.fr/bootstrap-datetimepicker/demo.php">Bootstrap DateTime Picker</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/twitter/typeahead.js/">Typeahead with Bloodhound</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://fengyuanchen.github.io/cropper/">Image Cropper</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://handsontable.com/">Handsontable</a>
                    </li>


                </ul>
                </p>
            '
            //"underLoginMsgUser" => "",
            //"underLoginMsgScan => ""

        );

        $params = new SiteParameters();

        $count = 0;
        foreach( $types as $key => $value ) {
            $method = "set".$key;
            $params->$method( $value );
            $count = $count + 10;
        }

        //assign Institution
        $institutionName = 'Weill Cornell Medical College';
        $institution = $em->getRepository('OlegUserdirectoryBundle:Institution')->findOneByName($institutionName);
        if( !$institution ) {
            throw new \Exception( 'Institution was not found for name='.$institutionName );
        }
        $params->setAutoAssignInstitution($institution);

        $em->persist($params);
        $em->flush();

        return round($count/10);
    }



    public function generateInstitutionTypes() {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:InstitutionType')->findAll();

        if( $entities ) {
            return -1;
        }

        $elements = array(
            'Medical',
            'Educational'
        );


        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $name ) {

            $entity = new InstitutionType();
            $this->setDefaultList($entity,$count,$username,$name);

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);

    }

    //https://bitbucket.org/weillcornellpathology/scanorder/issue/221/multiple-office-locations-and-phone
    public function generateInstitutions() {

        $username = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:Institution')->findAll();

        if( $entities ) {
            return -1;
        }

        $wcmcDep = array(
            'Anesthesiology',
            'Biochemistry',
            'Feil Family Brain and Mind Research Institute',
            'Cardiothoracic Surgery' => array(
                'Thoracic Surgery'
            ),
            'Cell and Developmental Biology' => null,
            'Dermatology' => null,
            'Genetic Medicine' => null,
            'Healthcare Policy and Research' => array(
                'Biostatistics and Epidemiology',
                'Comparative Effectiveness and Outcomes Research',
                'Health Informatics',
                'Health Policy and Economics',
                'Health Systems Innovation and Implementation Science'
            ),
            'Weill Department of Medicine' => array(
                'Cardiology',
                'Clinical Epidemiology and Evaluative Sciences Research',
                'Clinical Pharmacology'                                                     //continue  dep
            ),
            'Microbiology and Immunology' => null,
            'Neurological Surgery' => null,
            'Neurology' => array(
                "Alzheimer's Disease & Memory Disorders",
                "Diagnostic Testing - Evoked Potentials, EEG & EMG",
                "Doppler (Transcranial and Carotid Duplex) Ultrasound Studies"              //continue
            ),
            'Obstetrics and Gynecology' => array(
                'General Ob/Gyn',
                'Gynecology',
                'Gynecologic Oncology'                                                     //continue
            ),
            'Ophthalmology' => null,
            'Orthopaedic Surgery' => null,
            'Otolaryngology - Head and Neck Surgery' => null,
            'Pathology and Laboratory Medicine' => array(
                'shortname' => 'Pathology',
                //divisions
                'Anatomic Pathology' => array(
                    //services
                    'Autopsy Pathology',
                    'Breast Pathology',
                    'Cardiopulmonary Pathology',
                    'Cytopathology',
                    'Dermatopathology',
                    'Gastrointestinal and Liver Pathology',
                    'Genitourinary Pathology',
                    'Gynecologic Pathology',
                    'Head and Neck Pathology',
                    'Hematopathology',
                    'Neuropathology',
                    'Pediatric Pathology',
                    'Perinatal and Obstetric Pathology',
                    'Renal Pathology',
                    'Surgical Pathology'
                ),
                'Hematopathology' => array(
                    'Immunopathology',
                    'Molecular Hematopathology'
                ),
                'Weill Cornell Pathology Consultation Services' => array(
                    'Breast Pathology',
                    'Dermatopathology',
                    'Gastrointestinal and Liver Pathology',
                    'Genitourinary Pathology',
                    'Gynecologic Pathology',
                    'Hematopathology',
                    'Perinatal and Obstetrical Pathology',
                    'Renal Pathology'
                ),
                'Laboratory Medicine' => array(
                    'Clinical Chemistry',
                    'Cytogenetics',
                    'Routine and special coagulation',
                    'Endocrinology',
                    'Routine and special hematology',
                    'Immunochemistry',
                    'Serology',
                    'Immunohematology',
                    'Microbiology',
                    'Molecular diagnostics',
                    'Toxicology',
                    'Mycology',
                    'Therapeutic drug monitoring',
                    'Parasitology',
                    'Virology'
                ),
                'Pathology Informatics'
            ),
            'Pediatrics' => array(
                'Cardiology',
                'Child Development',
                'Child Neurology'                                                           //continue
            ),
            'Pharmacology' => null,
            'Physiology and Biophysics' => null,
            'Psychiatry' => array(
                'Sackler Institute for Developmental Psychobiology'
            ),
            'Primary Care' => null,
            'Radiology' => null,
            'Radiation Oncology' => null,
            'Rehabilitation Medicine' => null,
            'Reproductive Medicine' => array(
                'Center for Reproductive Medicine and Infertility (CRMI)',
                'Center for Male Reproductive Medicine and Microsurgery'
            ),
            'Surgery' => array(
                'Breast Surgery',
                'Burn, Critical Care and Trauma',
                'Colon & Rectal Surgery',                                                   //continue
            ),
            'Urology' => array(
                'Brady Urologic Health Center'
            ),
            'Other Centers' => array(
                'Ansary Stem Cell Institute',
                'Center for Complementary and Integrative Medicine',
                'Center for Healthcare Informatics and Policy'                              //continue
            )

        );
        $wcmc = array(
            'abbreviation'=>'WCMC',
            'departments'=>$wcmcDep
        );

        //http://nyp.org/services/index.html
        $nyhDep = array(
            'Allergy, Immunology and Pulmonology' => null,
            'Anesthesiology' => null,
            'Cancer (Oncology)' => null,
            'Cancer Screening and Awareness' => null,
            'Cardiology' => null,
			'Complementary, Alternative, and Integrative Medicine' => null,
            'Dermatology' => null,
            'Diabetes and Endocrinology' => null,
            'Digestive Diseases' => null,
            'Ear, Nose, and Throat (Otorhinolaryngology)' => null,
            'Geriatrics' => null,
            'Hematology (Blood Disorders)' => null,
            'Infectious Diseases/International Medicine' => null,
            'Internal Medicine' => null,
            'Nephrology (Kidney Disease)' => null,
            'Neurology and Neuroscience' => null,
            'Obstetrics and Gynecology' => null,
            'Ophthalmology' => null,
            'Pain Medicine' => null,
            'Pathology' => null,
            'Pediatrics' => null,
            'Preventive Medicine and Nutrition' => null,
            'Psychiatry and Mental Health' => null,
            'Radiation Oncology' => null,
            'Radiology' => null,
            'Rehabilitation Medicine' => null,
            'Rheumatology' => null,
            "Women's Health" => null
        );

        $nyh = array(
            'abbreviation'=>'NYP',
            'departments'=>$nyhDep
        );


        $wcmcq = array(
            'abbreviation'=>'WCMCQ',
            'departments'
        );

        $mskDep = array(
            'Anesthesiology and Critical Care Medicine' => null,
            'Laboratory Medicine' => null,
            'Medicine' => null
            //continue
        );
        $msk = array(
            'abbreviation'=>'MSK',
            'departments'=>$mskDep
        );

        $hssDep = array(
            'Orthopedic Surgery' => null,
            'Anesthesiology' => null,
            'Medicine' => null
            //continue
        );
        $hss = array(
            'abbreviation'=>'HSS',
            'departments'=>$hssDep
        );

        $institutions = array(
            'Weill Cornell Medical College'=>$wcmc,
            "New York Hospital"=>$nyh,
            "Weill Cornell Medical College Qatar"=>$wcmcq,
            "Memorial Sloan Kettering Cancer Center"=>$msk,
            "Hospital for Special Surgery"=>$hss
        );


        $medicalType = $em->getRepository('OlegUserdirectoryBundle:InstitutionType')->findOneByName('Medical');

        $instCount = 1;
        foreach( $institutions as $institutionname=>$infos ) {
            $institution = new Institution();
            $this->setDefaultList($institution,$instCount,$username,$institutionname);
            $institution->setAbbreviation( trim($infos['abbreviation']) );

            $institution->addType($medicalType);

            if( array_key_exists('departments', $infos) && $infos['departments'] && is_array($infos['departments'])  ) {

                $depCount = 0;
                foreach( $infos['departments'] as $departmentname=>$divisions ) {

                    $department = new Department();

                    if( is_numeric($departmentname) ){
                        $departmentname = $infos['departments'][$departmentname];
                    }
                    //echo "departmentname=".$departmentname."<br>";
                    $this->setDefaultList($department,$depCount,$username,$departmentname);

                    if( $divisions && is_array($divisions) ) {
                        $divCount = 0;
                        foreach( $divisions as $divisionname=>$services ) {

                            //shortname
                            if( $divisionname === 'shortname' && $services ) {
                                //echo "<br> services=".$services."<br>";
                                $department->setShortname($services);
                                continue;
                            }

                            $division = new Division();
                            if( is_numeric($divisionname) ){
                                $divisionname = $divisions[$divisionname];
                            }
                            $this->setDefaultList($division,$divCount,$username,$divisionname);


                            if( $services && is_array($services) ) {
                                $serCount = 0;
                                foreach( $services as $servicename ) {
                                    $service = new Service();
                                    if( is_numeric($servicename) ){
                                        $servicename = $services[$servicename];
                                    }
                                    $this->setDefaultList($service,$serCount,$username,$servicename);

                                    $division->addService($service);
                                    $serCount = $serCount + 10;
                                }
                            }//services


                            $department->addDivision($division);
                            $divCount = $divCount + 10;
                        }
                    }//divisions

                    $institution->addDepartment($department);
                    $depCount = $depCount + 10;

                }
            }//departmets

            $em->persist($institution);
            $em->flush();
            $instCount = $instCount + 10;
        } //foreach

        return round($instCount/10);
    }


    public function generateStates() {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:States')->findAll();

        if( $entities ) {
            return -1;
        }

        $states = array(
            'AL'=>"Alabama",
            'AK'=>"Alaska",
            'AZ'=>"Arizona",
            'AR'=>"Arkansas",
            'CA'=>"California",
            'CO'=>"Colorado",
            'CT'=>"Connecticut",
            'DE'=>"Delaware",
            'DC'=>"District Of Columbia",
            'FL'=>"Florida",
            'GA'=>"Georgia",
            'HI'=>"Hawaii",
            'ID'=>"Idaho",
            'IL'=>"Illinois",
            'IN'=>"Indiana",
            'IA'=>"Iowa",
            'KS'=>"Kansas",
            'KY'=>"Kentucky",
            'LA'=>"Louisiana",
            'ME'=>"Maine",
            'MD'=>"Maryland",
            'MA'=>"Massachusetts",
            'MI'=>"Michigan",
            'MN'=>"Minnesota",
            'MS'=>"Mississippi",
            'MO'=>"Missouri",
            'MT'=>"Montana",
            'NE'=>"Nebraska",
            'NV'=>"Nevada",
            'NH'=>"New Hampshire",
            'NJ'=>"New Jersey",
            'NM'=>"New Mexico",
            'NY'=>"New York",
            'NC'=>"North Carolina",
            'ND'=>"North Dakota",
            'OH'=>"Ohio",
            'OK'=>"Oklahoma",
            'OR'=>"Oregon",
            'PA'=>"Pennsylvania",
            'RI'=>"Rhode Island",
            'SC'=>"South Carolina",
            'SD'=>"South Dakota",
            'TN'=>"Tennessee",
            'TX'=>"Texas",
            'UT'=>"Utah",
            'VT'=>"Vermont",
            'VA'=>"Virginia",
            'WA'=>"Washington",
            'WV'=>"West Virginia",
            'WI'=>"Wisconsin",
            'WY'=>"Wyoming"
        );

        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $states as $key => $value ) {

            $entity = new States();
            $this->setDefaultList($entity,$count,$username,null);
            $entity->setName( trim($value) );
            $entity->setAbbreviation( trim($key) );

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);
    }


    public function generateCountryList() {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:Countries')->findAll();

        if( $entities ) {
            return -1;
        }

        $elements = array(
            "Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda",
            "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus",
            "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil",
            "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada",
            "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands",
            "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire",
            "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic",
            "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)",
            "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories",
            "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala",
            "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong",
            "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan",
            "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan",
            "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania",
            "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta",
            "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of",
            "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles",
            "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman",
            "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico",
            "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines",
            "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)",
            "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena",
            "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic",
            "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago",
            "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom",
            "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)",
            "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe"
        );



        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $value ) {

            $entity = new Countries();
            $this->setDefaultList($entity,$count,$username,null);
            $entity->setName( trim($value) );

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);

    }


    public function generateLanguages() {

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OlegUserdirectoryBundle:LanguageList')->findAll();
        if( $entities ) {
            return -1;
        }

        //\Locale::setDefault('ru');
        $elements = Intl::getLanguageBundle()->getLanguageNames();
        //print_r($elements);
        //exit();

        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $abbreviation=>$name ) {

            //$entity = $em->getRepository('OlegUserdirectoryBundle:LanguageList')->findOneByAbbreviation($abbreviation);

            //testing
//            if( $entity ) {
//                $em->remove($entity);
//                $em->flush();
//                echo "remove entity with ".$abbreviation."<br>";
//            }

            $entity = null;

            if( !$entity ) {
                $entity = new LanguageList();
                $this->setDefaultList($entity,$count,$username,null);
                $entity->setName( trim($name) );
                $entity->setAbbreviation( trim($abbreviation) );
            }

            \Locale::setDefault($abbreviation);
            $languageNativeName = Intl::getLanguageBundle()->getLanguageName($abbreviation);

            //uppercase the first letter
            $languageNativeName = mb_convert_case(mb_strtolower($languageNativeName), MB_CASE_TITLE, "UTF-8");

//            if( $abbreviation == 'ru' ) {
//                echo $abbreviation."=(".$languageNativeName.")<br>";
//                exit();
//            }

            $entity->setNativeName($languageNativeName);

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach
        //exit('1');

        \Locale::setDefault('en');

        return round($count/10);

    }


    public function generateBoardSpecialties() {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:BoardCertifiedSpecialties')->findAll();

        if( $entities ) {
            return -1;
        }

        $elements = array(
            'Anatomic Pathology',
            'Clinical Pathology',
            'Hematopathology',
            'Cytopathology',
            'Molecular Genetic Pathology',
            'Immunopathology',
            'Pediatric Pathology',
            'Neuropathology',
            'Dermatopathology',
            'Medical Microbiology',
            'Blood Banking/Transfusion Medicine',
            'Forensic Pathology',
            'Chemical Pathology'
        );


        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $value ) {

            $entity = new BoardCertifiedSpecialties();
            $this->setDefaultList($entity,$count,$username,null);
            $entity->setName( trim($value) );

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);

    }


    public function generateSourceSystems() {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:SourceSystemList')->findAll();

        if( $entities ) {
            return -1;
        }

        $elements = array(
            'Scan Order',
            'WCMC Epic Practice Management',
            'WCMC Epic Ambulatory EMR',
            'NYH Paper Requisition',
            'Written or oral referral'
        );


        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $value ) {

            $entity = new SourceSystemList();
            $this->setDefaultList($entity,$count,$username,$value);

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);

    }


    public function generateDocumentTypes() {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:DocumentTypeList')->findAll();

        if( $entities ) {
            return -1;
        }

        $elements = array(
            'Avatar Image',
            'Comment Document',
            'Autopsy Image',
            'Gross Image',
            'Part Document',
            'Block Image',
            'Microscopic Image',
            'Whole Slide Image',
            'Requisition Form Image',
            'Outside Report Reference Representation'
        );


        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $value ) {

            $entity = new DocumentTypeList();
            $this->setDefaultList($entity,$count,$username,$value);

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);

    }

    public function generateEmploymentTypes() {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:EmploymentType')->findAll();

        if( $entities ) {
            return -1;
        }

        $elements = array(
            'Full Time',
            'Part Time'
        );

        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $value ) {

            $entity = new EmploymentType();
            $this->setDefaultList($entity,$count,$username,$value);

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);

    }


    public function generateTerminationTypes() {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:EmploymentTerminationType')->findAll();

        if( $entities ) {
            return -1;
        }

        $elements = array(
            'Graduated',
            'Quit',
            'Retired',
            'Fired'
        );

        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $value ) {

            $entity = new EmploymentTerminationType();
            $this->setDefaultList($entity,$count,$username,null);
            $entity->setName( trim($value) );

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);

    }

    public function generateEventTypeList() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:EventTypeList')->findAll();

        if( $entities ) {
            return -1;
        }

        $elements = array(
            'Login Page Visit',
            'Successful Login',
            'Bad Credentials',
            'Unsuccessful Login Attempt',
            'Unapproved User Login Attempt',
            'Banned User Login Attempt',
            'User Created',
            'User Updated',
            'Search'
        );

        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $value ) {

            $entity = new EventTypeList();
            $this->setDefaultList($entity,$count,$username,null);
            $entity->setName( trim($value) );

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);
    }


    public function generateIdentifierTypeList() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:IdentifierTypeList')->findAll();

        if( $entities ) {
            return -1;
        }

        $elements = array(
            'WCMC Employee Identification Number (EIN)',
            'National Provider Identifier (NPI)',
            'MRN'
        );

        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $value ) {

            $entity = new IdentifierTypeList();
            $this->setDefaultList($entity,$count,$username,null);
            $entity->setName( trim($value) );

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);
    }


    public function generateFellowshipTypeList() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:FellowshipTypeList')->findAll();

        if( $entities ) {
            return -1;
        }

        $elements = array(
            "Blood banking/Transfusion medicine",
            "Chemistry",
            "Dermatopathology",
            "Forensic pathology",
            "Genitourinary pathology",
            "Hematopathology",
            "Molecular genetic pathology",
            "Pathology informatics",
            "Pulmonary/Mediastinal pathology",
            "Soft tissue/Bone pathology",
            "Breast pathology",
            "Cytopathology",
            "Diagnostic immunology",
            "Gastrointestinal pathology",
            "Gynecologic pathology",
            "Medical microbiology",
            "Neuropathology",
            "Pediatric pathology",
            "Renal pathology",
            "Surgical/Oncologic pathology"
        );

        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $value ) {

            $entity = new FellowshipTypeList();
            $this->setDefaultList($entity,$count,$username,null);
            $entity->setName( trim($value) );

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);
    }

    public function generateResidencyTrackList() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:ResidencyTrackList')->findAll();

        if( $entities ) {
            return -1;
        }

        $elements = array(
            'AP',
            'CP',
            'AP/CP'
        );

        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $value ) {

            $entity = new ResidencyTrackList();
            $this->setDefaultList($entity,$count,$username,null);
            $entity->setName( trim($value) );

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);
    }


    public function generateMedicalTitleList() {
        $em = $this->getDoctrine()->getManager();

        $elements = array(
            'Assistant Attending Pathologist',
            'Associate Attending Pathologist',
            'Attending Pathologist',
            'Resident',
            'Fellow'
        );

        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $value ) {

            $value = trim($value);

            if( $em->getRepository('OlegUserdirectoryBundle:MedicalTitleList')->findOneByName($value) ) {
                continue;
            }

            $entity = new MedicalTitleList();
            $this->setDefaultList($entity,$count,$username,$value);

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);
    }

    public function generateMedicalSpecialties() {
        $em = $this->getDoctrine()->getManager();

        $elements = array(
            'Autopsy Pathology',
            'Breast Pathology',
            'Cardiopulmonary Pathology',
            'Clinical Microbiology',
            'Cytogenetics',
            'Cytopathology',
            'Dermatopathology',
            'Gastrointestinal and Liver Pathology',
            'Genitourinary Pathology',
            'Gynecologic Pathology',
            'Head and Neck Pathology',
            'Hematopathology',
            'Immunopathology',
            'Molecular and Genomic Pathology',
            'Molecular Hematopathology',
            'Neuropathology',
            'Pathology Informatics',
            'Pediatric Pathology',
            'Perinatal and Obstetric Pathology',
            'Renal Pathology',
            'Surgical Pathology',
            'Transfusion Medicine'
        );

        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $value ) {

            $value = trim($value);

            if( $em->getRepository('OlegUserdirectoryBundle:MedicalSpecialties')->findOneByName($value) ) {
                continue;
            }

            $entity = new MedicalSpecialties();
            $this->setDefaultList($entity,$count,$username,$value);

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);
    }

    public function generateLocationTypeList() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:LocationTypeList')->findAll();

        if( $entities ) {
            return -1;
        }

        $elements = array(
            'Employee Office',
            'Employee Desk',
            'Employee Cubicle',
            'Employee Suite',
            'Employee Mailbox',
            'Employee Home',
            'Conference Room',
            'Sign Out Room',
            'Clinical Laboratory',
            'Research Laboratory',
            'Medical Office',
            'Inpatient Room',
            'Patient Contact Information',
            'Pick Up',
            'Accessioning',
            'Storage',
            'Filing Room',
            'Off Site Slide Storage'
        );

        $username = $this->get('security.context')->getToken()->getUser();

        $count = 1;
        foreach( $elements as $value ) {

            $entity = new LocationTypeList();
            $this->setDefaultList($entity,$count,$username,null);
            $entity->setName( trim($value) );

            $em->persist($entity);
            $em->flush();

            $count = $count + 10;

        } //foreach

        return round($count/10);
    }




    public function generateEquipmentType() {

        $username = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

//        $entities = $em->getRepository('OlegUserdirectoryBundle:EquipmentType')->findAll();
//
//        if( $entities ) {
//            return -1;
//        }

        $types = array(
            'Whole Slide Scanner',
            'Microtome',
            'Centrifuge',
            'Slide Stainer',
            'Microscope Camera',
            'Autopsy Camera',
            'Gross Image Camera',
            'Tissue Processor',
            'Xray Machine',
            'Block Imaging Camera',
            'Requisition Form Scanner'
        );

        $count = 1;
        foreach( $types as $type ) {

            if( $em->getRepository('OlegUserdirectoryBundle:EquipmentType')->findOneByName($type) ) {
                continue;
            }

            $listEntity = new EquipmentType();
            $this->setDefaultList($listEntity,$count,$username,$type);

            $em->persist($listEntity);
            $em->flush();

            $count = $count + 10;
        }

        return round($count/10);
    }

    public function generateEquipment() {

        $username = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

//        $entities = $em->getRepository('OlegUserdirectoryBundle:Equipment')->findAll();
//
//        if( $entities ) {
//            return -1;
//        }

        $types = array(
            'Aperio ScanScope AT' => 'Whole Slide Scanner',
            'Lumix LX5' => 'Autopsy Camera',
            'Canon 60D' => 'Autopsy Camera',
            'Milestone MacroPath D' => 'Gross Image Camera',
            'Block Processing Device' => 'Tissue Processor',
            'Faxitron' => 'Xray Machine',
            'Block Image Device' => 'Block Imaging Camera',
            'Microtome Device' => 'Microtome',
            'Microtome Device' => 'Centrifuge',
            'Slide Stainer Device' => 'Slide Stainer',
            'Olympus Camera' => 'Microscope Camera',
            'Generic Desktop Scanner' => 'Requisition Form Scanner'
        );

        $count = 1;
        foreach( $types as $device => $keytype ) {

            if( $em->getRepository('OlegUserdirectoryBundle:Equipment')->findOneByName($device) ) {
                continue;
            }

            $keytype = $em->getRepository('OlegUserdirectoryBundle:EquipmentType')->findOneByName($keytype);

            if( !$keytype ) {
                //continue;
                //exit('equipment keytype is null');
                throw new \Exception( 'Equipment keytype is null, name="' . $keytype .'"' );
            }

            $listEntity = new Equipment();
            $this->setDefaultList($listEntity,$count,$username,$device);

            $keytype->addEquipment($listEntity);

            $em->persist($listEntity);
            $em->flush();

            $count = $count + 10;
        }

        return round($count/10);
    }


    public function generateLocationPrivacy() {

        $username = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:LocationPrivacyList')->findAll();

        if( $entities ) {
            return -1;
        }

        $types = array(
            "Administration; Those 'on call' can see these phone numbers & email",
            "Administration can see and edit this contact information",
            "Any approved user of Employee Directory can see these phone numbers and email",
            "Any approved user of Employee Directory can see this contact information if logged in",
            "Anyone can see this contact information"
        );

        $count = 1;
        foreach( $types as $type ) {

            $listEntity = new LocationPrivacyList();
            $this->setDefaultList($listEntity,$count,$username,$type);

            $em->persist($listEntity);
            $em->flush();

            $count = $count + 10;
        }

        return round($count/10);
    }


    public function generateResLabs() {

        $username = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:ResearchLab')->findAll();

        if( $entities ) {
            return -1;
        }

        $types = array(
            "Laboratory of Prostate Cancer Research Group",
            "Proteolytic Oncogenesis",
            "Macrophages and Tissue Remodeling",
            "Molecular Gynecologic Pathology",
            "Cancer Biology",
            "Laboratory of Cell Metabolism",
            "Viral Oncogenesis",
            "Center for Vascular Biology",
            "Cell Cycle",
            "Laboratory of Stem Cell Aging and Cancer",
            "Molecular Pathology",
            "Skeletal Biology"
        );

        $count = 1;
        foreach( $types as $type ) {

            $listEntity = new ResearchLab();
            $this->setDefaultList($listEntity,$count,$username,$type);

            $em->persist($listEntity);
            $em->flush();

            $count = $count + 10;
        }

        return round($count/10);
    }


    public function generateBuildings() {

        $username = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:BuildingList')->findAll();

        if( $entities ) {
            return -1;
        }

        $buildings = array(
            array('name'=>"Weill Cornell Medical College", 'street1'=>'1300 York Ave','abbr'=>'C','inst'=>'WCMC'),
            array('name'=>"Belfer Research Building", 'street1'=>'413 East 69th Street','abbr'=>null,'inst'=>'WCMC'),
            array('name'=>"Helmsley Medical Tower", 'street1'=>'1320 York Ave','abbr'=>null,'inst'=>'WCMC'),
            array('name'=>"Weill Greenberg Center",'street1'=>'1305 York Ave','abbr'=>null,'inst'=>'WCMC'),
            array('name'=>"Olin Hall",'street1'=>'445 East 69th Street','abbr'=>null,'inst'=>'WCMC'),
            array('name'=>"",'street1'=>'575 Lexington Ave','abbr'=>null,'inst'=>'WCMC'),                        //WCMC - 575 Lexington Ave
            array('name'=>"",'street1'=>'402 East 67th Street','abbr'=>null,'inst'=>'WCMC'),                     //WCMC - 402 East 67th Street
            array('name'=>"",'street1'=>'425 East 61st Street','abbr'=>null,'inst'=>'WCMC'),                     //WCMC - 425 East 61st Street
            array('name'=>"Starr Pavilion",'street1'=>'520 East 70th Street','abbr'=>'ST','inst'=>'NYP'),
            array('name'=>"J Corridor",'street1'=>'525 East 68th Street','abbr'=>'J','inst'=>'NYP'),
            array('name'=>"L Corridor",'street1'=>'525 East 68th Street','abbr'=>'L','inst'=>'NYP'),
            array('name'=>"K Wing",'street1'=>'525 East 68th Street','abbr'=>'K','inst'=>'NYP'),
            array('name'=>"F Wing, Floors 2-9",'street1'=>'525 East 68th Street','abbr'=>'F','inst'=>'NYP'),
            array('name'=>"Baker Pavilion - F Wing",'street1'=>'525 East 68th Street','abbr'=>'P','inst'=>'NYP'),
            array('name'=>"Payson Pavilion",'street1'=>'425 East 61st Street','abbr'=>null,'inst'=>'NYP'),
            array('name'=>"Whitney Pavilion",'street1'=>'525 East 68th Street','abbr'=>'W','inst'=>'NYP'),
            array('name'=>"M Wing",'street1'=>'530 East 70th Street','abbr'=>'M','inst'=>'NYP'),
            array('name'=>"N Wing",'street1'=>'530 East 70th Street','abbr'=>'N','inst'=>'NYP'),
            array('name'=>"Weill Cornell Medical Assoc. Eastside",'street1'=>'201 East 80th Street','abbr'=>null,'inst'=>'NYP'),
            array('name'=>"Weill Cornell Medical Assoc. Westside",'street1'=>'12 West 72nd Street','abbr'=>null,'inst'=>'NYP'),
            array('name'=>"Iris Cantor Women’s Health Center",'street1'=>'425 East 61st Street','abbr'=>null,'inst'=>'NYP'),
            array('name'=>"Weill Cornell Imaging at NewYork-Presbyterian",'street1'=>'416 East 55th Street','abbr'=>null,'inst'=>'NYP'),    //NYP - Weill Cornell Imaging at NewYork-Presbyterian / 416 East 55th Street
            array('name'=>"Weill Cornell Imaging at NewYork-Presbyterian, 9th Floor",'street1'=>'425 East 61st Street','abbr'=>null,'inst'=>'NYP'),    //NYP - Weill Cornell Imaging at NewYork-Presbyterian / 425 East 61st Street, 9th Floor
            array('name'=>"Weill Cornell Imaging at NewYork-Presbyterian, lobby level",'street1'=>'520 East 70th Street','abbr'=>null,'inst'=>'NYP'),    //NYP - Weill Cornell Imaging at NewYork-Presbyterian / 520 East 70th Street, lobby level
            array('name'=>"Weill Cornell Imaging at NewYork-Presbyterian, 3rd Floor",'street1'=>'1305 York Avenue','abbr'=>null,'inst'=>'NYP'),    //NYP - Weill Cornell Imaging at NewYork-Presbyterian / 1305 York Avenue, 3rd Floor
            array('name'=>"Oxford Medical Offices",'street1'=>'428 East 72nd Street','abbr'=>null,'inst'=>'NYP'),
            array('name'=>"Stich Building",'street1'=>'1315 York Ave','abbr'=>null,'inst'=>'NYP'),
            array('name'=>"Kips Bay Medical Offices",'street1'=>'411 East 69th Street','abbr'=>null,'inst'=>'NYP'),
            array('name'=>"Phipps House Medical Offices",'street1'=>'449 East 68th Street','abbr'=>null,'inst'=>'NYP'),
            array('name'=>"",'street1'=>'333 East 38th Street','abbr'=>null,'inst'=>'NYP')  //NYP - 333 East 38th Street
        );

        $city = "New York";
        $state = $em->getRepository('OlegUserdirectoryBundle:States')->findOneByName("New York");
        $country = $em->getRepository('OlegUserdirectoryBundle:Countries')->findOneByName("United States");
        if( !$country ) {
            //exit('ERROR: country null');
            throw new \Exception( "country is not found by name=" . "United States" );
        }

        $count = 1;
        foreach( $buildings as $building ) {

            $name = $building['name'];

            $listEntity = new BuildingList();
            $this->setDefaultList($listEntity,$count,$username,$name);

            //add buildings attributes
            $street1 = $building['street1'];
            $buildingAbbr = $building['abbr'];

            $geo = new GeoLocation();
            $geo->setStreet1($street1);
            $geo->setCity($city);
            $geo->setState($state);
            $geo->setCountry($country);

            $listEntity->setGeoLocation($geo);
            $listEntity->setAbbreviation($buildingAbbr);

            $instAbbr = $building['inst'];
            $inst = $em->getRepository('OlegUserdirectoryBundle:Institution')->findOneByAbbreviation($instAbbr);
            if( $inst ) {
                $listEntity->addInstitution($inst);
            }

            //echo $count.": name=".$name.", street1=".$street1."<br>";

            $em->persist($listEntity);
            $em->flush();

            $count = $count + 10;
        }

        return round($count/10);
    }


    public function generateLocations() {

        $username = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

//        $entities = $em->getRepository('OlegUserdirectoryBundle:Location')->findAll();
//        if( $entities ) {
//            return -1;
//        }

        $locations = array(
            "Surgical Pathology Filing Room" => array('street1'=>'520 East 70th Street','phone'=>'222-0059','room'=>'ST-1012','inst'=>'NYP'),
        );

        $city = "New York";
        $state = $em->getRepository('OlegUserdirectoryBundle:States')->findOneByName("New York");
        $country = $em->getRepository('OlegUserdirectoryBundle:Countries')->findOneByName("United States");
        $locationType = $em->getRepository('OlegUserdirectoryBundle:LocationTypeList')->findOneByName("Filing Room");
        $locationPrivacy = $em->getRepository('OlegUserdirectoryBundle:LocationPrivacyList')->findOneByName("Anyone can see this contact information");
        $building = $em->getRepository('OlegUserdirectoryBundle:BuildingList')->findOneByName("Starr Pavilion");

        $count = 1;
        foreach( $locations as $location => $attr ) {

            if( $em->getRepository('OlegUserdirectoryBundle:Location')->findOneByName($location) ) {
                continue;
            }

            $listEntity = new Location();
            $this->setDefaultList($listEntity,$count,$username,$location);

            //add buildings attributes
            $street1 = $attr['street1'];
            $phone = $attr['phone'];
            $room = $attr['room'];
            $instAbbr = $attr['inst'];

            $inst = $em->getRepository('OlegUserdirectoryBundle:Institution')->findOneByAbbreviation($instAbbr);
            if( $inst ) {
                $listEntity->setInstitution($inst);
            }

            $geo = new GeoLocation();
            $geo->setStreet1($street1);
            $geo->setCity($city);
            $geo->setState($state);
            $geo->setCountry($country);

            $listEntity->setGeoLocation($geo);
            $listEntity->setPhone($phone);
            $listEntity->setRoom($room);
            $listEntity->setStatus($listEntity::STATUS_VERIFIED);
            $listEntity->addLocationType($locationType);
            $listEntity->setPrivacy($locationPrivacy);
            $listEntity->setBuilding($building);

            //set room object
            $userUtil = new UserUtil();
            $roomObj = $userUtil->getObjectByNameTransformer($room,$username,'RoomList',$em);
            $listEntity->setRoom($roomObj);

            $em->persist($listEntity);
            $em->flush();

            $count = $count + 10;
        }

        return round($count/10);
    }

    public function generateTestUsers() {

        $testusers = array(
            "testplatformadministrator" => array("ROLE_PLATFORM_ADMIN"),
            "testdeputyplatformadministrator" => array("ROLE_PLATFORM_DEPUTY_ADMIN"),

            "testscanadministrator" => array("ROLE_SCANORDER_ADMIN"),
            "testscanprocessor" => array("ROLE_SCANORDER_PROCESSOR"), //TODO: check auth logic: it ask for access request for scan site
            "testscansubmitter" => array("ROLE_SCANORDER_SUBMITTER"),

            "testuseradministrator" => array("ROLE_SCANORDER_SUBMITTER","ROLE_USERDIRECTORY_ADMIN"),
            "testusereditor" => array("ROLE_SCANORDER_SUBMITTER","ROLE_USERDIRECTORY_EDITOR"),  //TODO: check auth logic: it ask for access request for directory site
            "testuserobserver" => array("ROLE_SCANORDER_SUBMITTER","ROLE_USERDIRECTORY_OBSERVER")
        );

        $userSecUtil = $this->container->get('user_security_utility');
        $userUtil = new UserUtil();
        $em = $this->getDoctrine()->getManager();
        $systemuser = $userUtil->createSystemUser($em,null,null);  //$this->get('security.context')->getToken()->getUser();
        $default_time_zone = $this->container->getParameter('default_time_zone');

//        $em = $this->getDoctrine()->getManager();
//        $entities = $em->getRepository('OlegUserdirectoryBundle:ResearchLab')->findAll();
//
//        if( $entities ) {
//            return -1;
//        }

        $count = 1;
        foreach( $testusers as $testusername => $roles ) {

            $user = new User();
            $userkeytype = $userSecUtil->getUsernameType("aperio");
            $user->setKeytype($userkeytype);
            $user->setPrimaryPublicUserId($testusername);

            //echo "username=".$user->getPrimaryPublicUserId()."<br>";
            $found_user = $em->getRepository('OlegUserdirectoryBundle:User')->findOneByPrimaryPublicUserId( $user->getPrimaryPublicUserId() );
            if( $found_user ) {
                //add scanorder Roles
                foreach( $roles as $role ) {
                    $found_user->addRole($role);
                }
                $em->flush();
                continue;
            }

            //set unique username
            $usernameUnique = $user->createUniqueUsername();
            $user->setUsername($usernameUnique);
            $user->setUsernameCanonical($usernameUnique);

            //$user->setEmail($email);
            //$user->setEmailCanonical($email);
            $user->setFirstName($testusername);
            $user->setLastName($testusername);
            $user->setDisplayName($testusername." ".$testusername);
            $user->setPassword("");
            $user->setCreatedby('system');
            $user->getPreferences()->setTimezone($default_time_zone);

            //add default locations
            $user = $userUtil->addDefaultLocations($user,$systemuser,$em,$this->container);

            //phone, fax, office are stored in Location object
            $mainLocation = $user->getMainLocation();
            //$mainLocation->setPhone($phone);
            //$mainLocation->setFax($fax);

            //title is stored in Administrative Title
            $administrativeTitle = new AdministrativeTitle($systemuser);
            $user->addAdministrativeTitle($administrativeTitle);

            //add scanorder Roles
            foreach( $roles as $role ) {
                $user->addRole($role);
            }

            $user->setEnabled(true);
            $user->setLocked(false);
            $user->setExpired(false);

            //record user log create
            $event = "User ".$user." has been created by ".$systemuser."<br>";
            $userSecUtil->createUserEditEvent($this->container->getParameter('employees.sitename'),$event,$systemuser,$user,null,'User Created');

            $em->persist($user);
            $em->flush();

            //**************** create PerSiteSettings for this user **************//
            //TODO: ideally, this should be located on scanorder site
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

            $count++;
        }

        return $count;
    }



    public function generateCompletionReasons() {

        $username = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:CompletionReasonList')->findAll();

        if( $entities ) {
            return -1;
        }

        $types = array(
            "Graduated",
            "Transferred"
        );

        $count = 1;
        foreach( $types as $type ) {

            $listEntity = new CompletionReasonList();
            $this->setDefaultList($listEntity,$count,$username,$type);

            $em->persist($listEntity);
            $em->flush();

            $count = $count + 10;
        }

        return round($count/10);
    }

    public function generateTrainingDegrees() {

        $username = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:TrainingDegreeList')->findAll();

        if( $entities ) {
            return -1;
        }

        $types = array(
            "MD", "DO", "PhD", "JD", "MBA", "MHA", "MA", "MS", "BS", "BA", "MBBS", "MDCM", "MBChB", "BMed",
            "Dr.Med", "Dr.MuD", "Cand.med", "DMD", "BDent", "DDS", "BDS", "BDSc", "BChD", "CD", "Cand.Odont.",
            "Dr.Med.Dent.", "DNP", "DNAP", "DNS", "DNSc", "OTD", "DrOT", "MSOT", "MOT", "OD", "B.Optom", "BEd",
            "BME", "BSE", "BSocSc", "BSc", "BPharm", "BScPhm", "PharmB", "MPharm", "PharmD", "DPT", "DPhysio",
            "MPT", "BSPT", "MPAS", "MPS", "DPM", "DP", "BPod", "PodB", "PodD", "MPA", "MPS", "PsyD",
            "ClinPsyD", "EdS", "BSN", "DVM", "VMD", "BVS", "BVSc", "BVMS", "MLIS", "MLS", "MSLIS", "BSW"
        );

        $count = 1;
        foreach( $types as $type ) {

            $listEntity = new TrainingDegreeList();
            $this->setDefaultList($listEntity,$count,$username,$type);

            $listEntity->setAbbreviation($type);

            //set "MBBS" and "DO" to be synonyms of "MD" in the List Manager for Degrees
            if( $type == "DO" || $type == "MBBS" ) {
                $mdOriginal = $em->getRepository('OlegUserdirectoryBundle:TrainingDegreeList')->findOneByName("MD");
                $listEntity->setOriginal($mdOriginal);
            }

            $em->persist($listEntity);
            $em->flush();

            $count = $count + 10;
        }

        return round($count/10);
    }

    public function generateResidencySpecialties() {

        $username = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

//        $entities = $em->getRepository('OlegUserdirectoryBundle:ResidencySpecialty')->findAll();
//        if( $entities ) {
            //return -1;
//            $query = $em->createQuery('DELETE OlegUserdirectoryBundle:FellowshipSubspecialty c WHERE c.id > 0');
//            $query->execute();
//            $query = $em->createQuery('DELETE OlegUserdirectoryBundle:ResidencySpecialty c WHERE c.id > 0');
//            $query->execute();
//        }

        $inputFileName = __DIR__ . '/../Util/SpecialtiesResidenciesFellowshipsCertified.xlsx';

        try {
            $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $count = 1;
        $subcount = 1;

        //for each row in excel
        for ($row = 2; $row <= $highestRow; $row++){
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);

            //echo $row.": ";
            //var_dump($rowData);
            //echo "<br>";

            //ResidencySpecialty	FellowshipSubspecialty	BoardCertificationAvailable
            $residencySpecialty = $rowData[0][0];
            $fellowshipSubspecialty = $rowData[0][1];
            $boardCertificationAvailable = $rowData[0][2];
            //echo "residencySpecialty=".$residencySpecialty."<br>";
            //echo "fellowshipSubspecialty=".$fellowshipSubspecialty."<br>";
            //echo "boardCertificationAvailable=".$boardCertificationAvailable."<br>";

            if( $residencySpecialty ) {

                //echo "residencySpecialty=".$residencySpecialty."<br>";

                if( $em->getRepository('OlegUserdirectoryBundle:ResidencySpecialty')->findOneByName($residencySpecialty."") ) {
                    continue;
                }


                $listEntity = new ResidencySpecialty();
                $this->setDefaultList($listEntity,$count,$username,$residencySpecialty);


                if( $boardCertificationAvailable && $boardCertificationAvailable == "Yes" ) {
                    $listEntity->setBoardCertificateAvailable(true);
                }

                $em->persist($listEntity);
                $em->flush();

                $count = $count + 10;
            }

            if( $fellowshipSubspecialty ) {

                //echo "fellowshipSubspecialty=".$fellowshipSubspecialty."<br>";
                if( $em->getRepository('OlegUserdirectoryBundle:FellowshipSubspecialty')->findOneByName($fellowshipSubspecialty."") ) {
                    continue;
                }

                $subEntity = new FellowshipSubspecialty();
                $this->setDefaultList($subEntity,$subcount,$username,$fellowshipSubspecialty);


                if( $boardCertificationAvailable && $boardCertificationAvailable == "Yes" ) {
                    $subEntity->setBoardCertificateAvailable(true);
                }

                if( $listEntity ) {
                    $listEntity->addChild($subEntity);
                }

                $em->persist($subEntity);
                $em->flush();

                $subcount = $subcount + 10;
            }

        }

        return round($count/10);
    }


    public function generateHonorTrainings() {

        $username = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:HonorTrainingList')->findAll();

        if( $entities ) {
            return -1;
        }

        $types = array(
            "Magna Cum Laude", "Summa Cum Laude", "Cum Laude", "AOA Member"
        );

        $count = 1;
        foreach( $types as $type ) {

            $listEntity = new HonorTrainingList();
            $this->setDefaultList($listEntity,$count,$username,$type);

            $em->persist($listEntity);
            $em->flush();

            $count = $count + 10;
        }

        return round($count/10);
    }

    //Professional Fellowship Title
    public function generateFellowshipTitles() {

        $username = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:FellowshipTitleList')->findAll();

        if( $entities ) {
            return -1;
        }

        $types = array(
            "F.C.A.P." => "Fellow of the College of American Pathologists",
            "F.A.A.E.M." => "Fellow of the American Academy of Emergency Medicine",
            "F.A.A.F.P." => "Fellow of the American Academy of Family Physicians",
            "F.A.C.C." => "Fellow of the American College of Cardiologists",
            "F.A.C.E." => "Fellow of the American College of Endocrinology",
            "F.A.C.E.P." => "Fellow of the American College of Emergency Physicians",
            "F.A.C.G." => "Fellow of the American College of Gastroenterology",
            "F.A.C.F.A.S." => "Fellow of the American College of Foot and Ankle Surgeons",
            "F.A.C.O.G." => "Fellow of the American College of Obstetrics and Gynecologists",
            "F.A.C.O.S." => "Fellow of the American College of Osteopathic Surgeons",
            "F.A.C.P." => "Fellow of the American College of Physicians",
            "F.A.C.C.P." => "Fellow of the American College of Chest Physicians",
            "F.A.C.S." => "Fellow of the American College of Surgeons",
            "F.A.S.P.S." => "Fellow of the American Society of Podiatric Surgeons",
            "F.H.M." => "Fellow in Hospital Medicine",
            "F.I.C.S." => "Fellow of the International College of Surgeons",
            "F.S.C.A.I." => "Fellow of the Society for Cardiovascular Angiography and Interventions",
            "F.S.T.S." => "Fellow of the Society of Thoracic Surgeons"
        );

        $count = 1;
        foreach( $types as $abbr => $name ) {

            $listEntity = new FellowshipTitleList();
            $this->setDefaultList($listEntity,$count,$username,$name);

            $listEntity->setAbbreviation($abbr);

            $em->persist($listEntity);
            $em->flush();

            $count = $count + 10;
        }

        return round($count/10);
    }


    public function generatesourceOrganizations() {

        $username = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OlegUserdirectoryBundle:SourceOrganization')->findAll();

        if( $entities ) {
            return -1;
        }

        $types = array(
            "National Institutes of Health" => "NIH"
        );

        $count = 1;
        foreach( $types as $name => $abbreviation ) {

            $listEntity = new SourceOrganization();
            $this->setDefaultList($listEntity,$count,$username,$name);

            $listEntity->setAbbreviation($abbreviation);

            $em->persist($listEntity);
            $em->flush();

            $count = $count + 10;
        }

        return round($count/10);
    }

}
