<?php
/**
 * Created by PhpStorm.
 * User: DevServer
 * Date: 9/3/15
 * Time: 12:00 PM
 */

namespace Oleg\UserdirectoryBundle\Util;


use Oleg\OrderformBundle\Entity\Educational;
use Oleg\OrderformBundle\Entity\PerSiteSettings;
use Oleg\OrderformBundle\Security\Util\AperioUtil;
use Oleg\UserdirectoryBundle\Entity\AdminComment;
use Oleg\UserdirectoryBundle\Entity\AdministrativeTitle;
use Oleg\UserdirectoryBundle\Entity\AppointmentTitle;
use Oleg\UserdirectoryBundle\Entity\BoardCertification;
use Oleg\UserdirectoryBundle\Entity\CodeNYPH;
use Oleg\UserdirectoryBundle\Entity\Credentials;
use Oleg\UserdirectoryBundle\Entity\EmploymentStatus;
use Oleg\UserdirectoryBundle\Entity\Identifier;
use Oleg\UserdirectoryBundle\Entity\Location;
use Oleg\UserdirectoryBundle\Entity\MedicalTitle;
use Oleg\UserdirectoryBundle\Entity\ResearchLab;
use Oleg\UserdirectoryBundle\Entity\StateLicense;
use Oleg\UserdirectoryBundle\Entity\Training;
use Oleg\UserdirectoryBundle\Entity\User;
use Oleg\UserdirectoryBundle\Form\DataTransformer\GenericTreeTransformer;

class UserGenerator {

    private $em;
    private $sc;
    private $container;

    private $usernamePrefix = 'wcmc-cwid';

    public function __construct( $em, $sc, $container ) {
        $this->em = $em;
        $this->sc = $sc;
        $this->container = $container;
    }



    public function generateUsersExcel() {
        $inputFileName = __DIR__ . '/../Util/UsersFull.xlsx';

        try {
            $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch( Exception $e ) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        //$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        //var_dump($sheetData);

        $assistantsArr = array();

        $count = 0;
        //$serviceCount = 0;

        $default_time_zone = $this->container->getParameter('default_time_zone');

        $userUtil = new UserUtil();

        $userSecUtil = $this->container->get('user_security_utility');
        $userkeytype = $userSecUtil->getUsernameType($this->usernamePrefix);


        ////////////// add system user /////////////////
        $systemuser = $userUtil->createSystemUser($this->em,$userkeytype,$default_time_zone);
        ////////////// end of add system user /////////////////

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $headers = $rowData = $sheet->rangeToArray('A' . 1 . ':' . $highestColumn . 1,
            NULL,
            TRUE,
            FALSE);



        //for each user in excel (start at row 2)
        for( $row = 2; $row <= $highestRow; $row++ ) {

            //Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);

            //Insert row data array into the database
//            echo $row.": ";
//            var_dump($rowData);
//            echo "<br>";


            $cwid = $this->getValueByHeaderName('CWID', $rowData, $headers);
            //echo "cwid=".$cwid."<br>";

            if( !$cwid ) {
                continue; //ignore users without cwid
            }

            $username = $cwid;

            //echo "<br>divisions=".$rowData[0][2]." == ";
            //print_r($services);

            //username: oli2002_@_wcmc-cwid
            $user = $this->em->getRepository('OlegUserdirectoryBundle:User')->findOneByUsername( $username."_@_". $this->usernamePrefix);
            //echo "DB user=".$user."<br>";

            $updateUsers = true;
            if( $user ) {
                if( !$updateUsers ) {
                    continue; //ignore existing users to prevent overwrite
                }
            }

            if( !$user ) {
                //create excel user
                $user = new User();
                $user->setKeytype($userkeytype);
                $user->setPrimaryPublicUserId($username);

                //set unique username
                $usernameUnique = $user->createUniqueUsername();
                $user->setUsername($usernameUnique);
                //echo "before set username canonical usernameUnique=".$usernameUnique."<br>";
                $user->setUsernameCanonical($usernameUnique);
            }

            $email = $this->getValueByHeaderName('E-mail Address', $rowData, $headers);
            $user->setEmail($email);
            $user->setEmailCanonical($email);

            $lastName = $this->getValueByHeaderName('Last Name', $rowData, $headers);
            $firstName = $this->getValueByHeaderName('First Name', $rowData, $headers);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setDisplayName($firstName." ".$lastName);
            $user->setSalutation($this->getValueByHeaderName('Salut.', $rowData, $headers));
            $user->setMiddleName($this->getValueByHeaderName('Middle Name', $rowData, $headers));

            $user->setPassword("");
            $user->setCreatedby('excel');
            $user->getPreferences()->setTimezone($default_time_zone);

            echo "new user=".$user."<br>";

            //Degree: TrainingDegreeList
            $degree = $this->getValueByHeaderName('Degree', $rowData, $headers);
            if( $degree ) {
                $training = new Training($systemuser);
                $training->setStatus($training::STATUS_VERIFIED);
                $degreeObj = $this->getObjectByNameTransformer('TrainingDegreeList',$degree,$systemuser);
                $training->setDegree($degreeObj);
                $user->addTraining($training);
            }

            //Employee Type: user_employmentStatus_0_employmentType: EmploymentType
            $employmentType = $this->getValueByHeaderName('Employee Type', $rowData, $headers);
            if( $employmentType ) {
                $employmentStatus = new EmploymentStatus($systemuser);
                $employmentTypeObj = $this->getObjectByNameTransformer('EmploymentType',$employmentType,$systemuser);
                $employmentStatus->setEmploymentType($employmentTypeObj);
                $user->addEmploymentStatus($employmentStatus);
            }

            //add default locations
            if( count($user->getLocations()) == 0 ) {
                $user = $this->addDefaultLocations($user,$systemuser);
            }

            //phone, fax, office are stored in Location object
            $mainLocation = $user->getMainLocation();
            $mainLocation->setStatus($mainLocation::STATUS_VERIFIED);
            $mainLocation->setPhone($this->getValueByHeaderName('Business Phone', $rowData, $headers));
            $mainLocation->setFax($this->getValueByHeaderName('Fax Number', $rowData, $headers));
            $mainLocation->setIc($this->getValueByHeaderName('Intercom', $rowData, $headers));
            $mainLocation->setPager($this->getValueByHeaderName('Pager', $rowData, $headers));


            //set room object
            $office = $this->getValueByHeaderName('Office Location', $rowData, $headers);
            $roomObj = $this->getObjectByNameTransformer('RoomList',$office,$systemuser);
            $mainLocation->setRoom($roomObj);

            //title is stored in Administrative Title
            $administrativeTitleStr = $this->getValueByHeaderName('Administrative Title', $rowData, $headers);
            if( $administrativeTitleStr ) {
                //Administrative - Institution
                $Institution = $this->getValueByHeaderName('Administrative - Institution', $rowData, $headers);
                $Department = $this->getValueByHeaderName('Administrative - Department', $rowData, $headers);
                $Division = $this->getValueByHeaderName('Administrative - Division', $rowData, $headers);
                $Service = $this->getValueByHeaderName('Administrative - Service', $rowData, $headers);
                //Heads
                $HeadDepartment = $this->getValueByHeaderName('Administrative - Head of this Department', $rowData, $headers);
                $HeadDivision = $this->getValueByHeaderName('Administrative - Head of this Division', $rowData, $headers);
                $HeadService = $this->getValueByHeaderName('Administrative - Head of this Service', $rowData, $headers);
                //set institutional hierarchys
                $administrativeTitles = $this->addInstitutinalTree('AdministrativeTitle',$systemuser,$Institution,$Department,$HeadDepartment,$Division,$HeadDivision,$Service,$HeadService);

                if( count($administrativeTitles) == 0 ) {
                    $administrativeTitles[] = new AdministrativeTitle();
                }

                foreach( $administrativeTitles as $administrativeTitle ) {
                    //set title object: Administrative Title
                    $titleObj = $this->getObjectByNameTransformer('AdminTitleList',$administrativeTitleStr,$systemuser);
                    $administrativeTitle->setName($titleObj);

                    $user->addAdministrativeTitle($administrativeTitle);
                }
            }//if admin title

            //Medical Staff Appointment (MSA) Title
            $msaTitleStr = $this->getValueByHeaderName('Medical Staff Appointment (MSA) Title', $rowData, $headers);
            if( $msaTitleStr ) {

                //Administrative - Institution
                $Institution = $this->getValueByHeaderName('MSA - Institution', $rowData, $headers);
                $Department = $this->getValueByHeaderName('MSA - Department', $rowData, $headers);
                $Division = $this->getValueByHeaderName('MSA - Division', $rowData, $headers);
                $Service = $this->getValueByHeaderName('MSA - Service', $rowData, $headers);
                //Heads
                $HeadDepartment = $this->getValueByHeaderName('MSA - Head of Department', $rowData, $headers);
                $HeadDivision = $this->getValueByHeaderName('MSA - Head of Division', $rowData, $headers);
                $HeadService = $this->getValueByHeaderName('MSA - Head of Service', $rowData, $headers);
                //set institutional hierarchys
                $msaTitles = $this->addInstitutinalTree('MedicalTitle',$systemuser,$Institution,$Department,$HeadDepartment,$Division,$HeadDivision,$Service,$HeadService);

                if( count($msaTitles) == 0 ) {
                    $msaTitles[] = new MedicalTitle();
                }

                foreach( $msaTitles as $msaTitle ) {
                    $titleObj = $this->getObjectByNameTransformer('MedicalTitleList',$msaTitleStr,$systemuser);
                    $msaTitle->setName($titleObj);

                    $user->addMedicalTitle($msaTitle);
                }

            }

            //Academic Title
            $academicTitleStr = $this->getValueByHeaderName('Academic Title', $rowData, $headers);
            if( $academicTitleStr ) {

                //Administrative - Institution
                $Institution = $this->getValueByHeaderName('Academic Appt - Institution', $rowData, $headers);
                $Department = $this->getValueByHeaderName('Academic Appt - Department', $rowData, $headers);
                $Division = $this->getValueByHeaderName('Academic Appt - Division', $rowData, $headers);
                $Service = $this->getValueByHeaderName('Academic Appt - Service', $rowData, $headers);
                //Heads
                $HeadDepartment = $this->getValueByHeaderName('Academic Appt - Head of Department', $rowData, $headers);
                $HeadDivision = $this->getValueByHeaderName('Academic Appt - Head of Division', $rowData, $headers);
                $HeadService = $this->getValueByHeaderName('Academic Appt - Head of Service', $rowData, $headers);
                //set institutional hierarchys
                $academicTitles = $this->addInstitutinalTree('AppointmentTitle',$systemuser,$Institution,$Department,$HeadDepartment,$Division,$HeadDivision,$Service,$HeadService);

                if( count($academicTitles) == 0 ) {
                    $academicTitles[] = new AppointmentTitle();
                }

                foreach( $academicTitles as $academicTitle ) {
                    $titleObj = $this->getObjectByNameTransformer('AppTitleList',$academicTitleStr,$systemuser);
                    $academicTitle->setName($titleObj);

                    $user->addAppointmentTitle($academicTitle);

                    //Academic Appointment - Faculty Track => oleg_userdirectorybundle_user_appointmentTitles_0_position
                    $facultyTrackStr = $this->getValueByHeaderName('Academic Appointment - Faculty Track', $rowData, $headers);
                    if( strpos($facultyTrackStr,'Clinical') !== false ) {
                        $facultyTrackStr = 'Clinical Faculty';
                    }
                    if( strpos($facultyTrackStr,'Research') !== false ) {
                        $facultyTrackStr = 'Research Faculty';
                    }
                    if( strpos($facultyTrackStr,'Clinical') !== false && strpos($facultyTrackStr,'Research') !== false ) {
                        $facultyTrackStr = 'Clinical Faculty, Research Faculty';
                    }
                    $academicTitle->setPosition($facultyTrackStr);

                    //Academic Appointment start date
                    $academicAppointmentStartDateStr = $this->getValueByHeaderName('Academic Appointment start date', $rowData, $headers);
                    $academicAppointmentStartDate = $this->transformDatestrToDate($academicAppointmentStartDateStr);
                    $academicTitle->setStartDate($academicAppointmentStartDate);
                }

            }

            //Research Lab Title : s2id_oleg_userdirectorybundle_user_researchLabs_0_name
            $researchLabTitleStr = $this->getValueByHeaderName('Research Lab Title', $rowData, $headers);
            if( $researchLabTitleStr ) {
                $researchLab = new ResearchLab($systemuser);
                $researchLab->setName($researchLabTitleStr);
                $user->addResearchLab($researchLab);

                //Principle Investigator of this Lab
                $piStr = $this->getValueByHeaderName('Principle Investigator of this Lab', $rowData, $headers);
                if( strtolower($piStr) == 'yes' ) {
                    $researchLab->setPiUser($user);
                }

            }

            //credentials
            $boardCertSpec = $this->getValueByHeaderName('Board Certification - Specialty', $rowData, $headers);
            $nyphCodeStr = $this->getValueByHeaderName('NYPH Code', $rowData, $headers);
            $licenseNumberStr = $this->getValueByHeaderName('License number', $rowData, $headers);

            if( $boardCertSpec || $nyphCodeStr || $licenseNumberStr ) {
                $addobjects = false;
                $credentials = new Credentials($systemuser,$addobjects);
                $user->setCredentials($credentials);
            }

            //Board Certification - Specialty : BoardCertifiedSpecialties
            if( $boardCertSpec ) {
                $this->processBoardCertification($credentials, $systemuser,$rowData, $headers, $boardCertSpec);
            }

            //NYPH Code: oleg_userdirectorybundle_user_credentials_codeNYPH_0_field
            if( $nyphCodeStr ) {
                $nyphCode = new CodeNYPH();
                $nyphCode->setField($nyphCodeStr);
                $credentials->addCodeNYPH($nyphCode);
            }

            //License number
            $licenseNumberStr = $this->getValueByHeaderName('License number', $rowData, $headers);
            if( $licenseNumberStr ) {
                $licenseState = new StateLicense();

                $licenseState->setLicenseNumber($licenseNumberStr);

                $licenseStateStr = $this->getValueByHeaderName('License state', $rowData, $headers);
                $licenseState->setLicenseNumber($licenseStateStr);

                //License expiration
                $expDateStr = $this->getValueByHeaderName('License expiration', $rowData, $headers);
                $expDate = $this->transformDatestrToDate($expDateStr);
                $licenseState->setLicenseExpirationDate($expDate);

                $credentials->addStateLicense($licenseState);
            }

            //Administrative Comment - Category
            $AdministrativeCommentCategory = $this->getValueByHeaderName('Administrative Comment - Category', $rowData, $headers);
            if( $AdministrativeCommentCategory ) {

                $comment = new AdminComment($systemuser);

                //Administrative Comment - Name
                $AdministrativeCommentName = $this->getValueByHeaderName('Administrative Comment - Name', $rowData, $headers);

                //Administrative Comment - Comment
                $AdministrativeCommentComment = $this->getValueByHeaderName('Administrative Comment - Comment', $rowData, $headers);

                //check if Category exists (root)
                $AdministrativeCommentCategoryObj = $this->getObjectByNameTransformer('CommentTypeList',$AdministrativeCommentCategory,$systemuser);

                $mapper = array('prefix'=>'Oleg','bundleName'=>'UserdirectoryBundle','className'=>'CommentTypeList','organizationalGroupType'=>'CommentGroupType');
                $AdministrativeCommentNameObj = $this->em->getRepository('OlegUserdirectoryBundle:CommentTypeList')->findByChildnameAndParent($AdministrativeCommentName,$AdministrativeCommentCategoryObj,$mapper);

                if( !$AdministrativeCommentNameObj ) {
                    //$AdministrativeCommentNameObj = $this->getObjectByNameTransformer('CommentTypeList',$AdministrativeCommentName,$systemuser);
                    $transformer = new GenericTreeTransformer($this->em, $systemuser, 'CommentTypeList', 'UserdirectoryBundle');
                    $AdministrativeCommentNameObj = $transformer->createNewEntity($AdministrativeCommentName,'CommentTypeList',$systemuser);

                    $AdministrativeCommentCategoryObj->addChild($AdministrativeCommentNameObj);
                    $organizationalGroupType = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->getDefaultLevelEntity($mapper, $AdministrativeCommentNameObj->getLevel());
                    $AdministrativeCommentNameObj->setOrganizationalGroupType($organizationalGroupType);
                    $this->em->persist($AdministrativeCommentNameObj);
                }

                //set comment category tree node
                if( $AdministrativeCommentCategoryObj ) {
                    $comment->setCommentType($AdministrativeCommentCategoryObj);
                }

                //overwrite comment category tree node
                if( $AdministrativeCommentNameObj ) {
                    $comment->setCommentType($AdministrativeCommentNameObj);
                }

                $comment->setComment($AdministrativeCommentComment);

                $user->addAdminComment($comment);
            }


            //Identifier: Multi
            $IdentifierNumberStr = $this->getValueByHeaderName('Identifier', $rowData, $headers);
            if( $IdentifierNumberStr ) {

                $IdentifierNumberArr = explode(";", $IdentifierNumberStr);

                $IdentifierTypeStr = $this->getValueByHeaderName('Identifier - Type', $rowData, $headers);
                $IdentifierTypeArr = explode(";", $IdentifierTypeStr);

                $IdentifierLinkStr = $this->getValueByHeaderName('Identifier - link', $rowData, $headers);
                $IdentifierLinkArr = explode(";", $IdentifierLinkStr);

                $IdentifierTypeStr = null;
                $IdentifierLinkStr = null;

                $index = 0;
                foreach( $IdentifierNumberArr as $IdentifierStr ) {

                    if( array_key_exists($index, $IdentifierTypeArr) ) {
                        $IdentifierTypeStr = $IdentifierTypeArr[$index];
                    }
                    if( array_key_exists($index, $IdentifierLinkArr) ) {
                        $IdentifierLinkStr = $IdentifierLinkArr[$index];
                    }

                    $Identifier = new Identifier();
                    $Identifier->setStatus($Identifier::STATUS_VERIFIED);

                    //Identifier
                    $Identifier->setField($IdentifierStr);

                    //Identifier - Type
                    $IdentifierTypeStrObj = $this->getObjectByNameTransformer('IdentifierTypeList',$IdentifierTypeStr,$systemuser);
                    $Identifier->setKeytype($IdentifierTypeStrObj);

                    //Identifier - link
                    $Identifier->setLink($IdentifierLinkStr);

                    $credentials->addIdentifier($Identifier);

                    $index++;
                }
            }

            //Certificate of Qualification - Code
            $CertificateCodeStr = $this->getValueByHeaderName('Certificate of Qualification - Code', $rowData, $headers);
            if( $CertificateCodeStr ) {
                $credentials->setCoqCode($CertificateCodeStr);
            }

            //Certificate of Qualification - Serial Number
            $CertificateSerialNumberStr = $this->getValueByHeaderName('Certificate of Qualification - Serial Number', $rowData, $headers);
            if( $CertificateSerialNumberStr ) {
                $credentials->setNumberCOQ($CertificateSerialNumberStr);
            }

            //Certificate of Qualification - Expiration Date
            $CertificateExpirationDateStr = $this->getValueByHeaderName('Certificate of Qualification - Expiration Date', $rowData, $headers);
            if( $CertificateExpirationDateStr ) {
                $CertificateExpirationDate = $this->transformDatestrToDate($CertificateExpirationDateStr);
                $credentials->setCoqExpirationDate($CertificateExpirationDate);
            }

            //CLIA - Number
            $CLIAStr = $this->getValueByHeaderName('CLIA - Number', $rowData, $headers);
            if( $CLIAStr ) {
                $credentials->setNumberCLIA($CLIAStr);
            }

            //CLIA - Expiration Date
            $CLIAExpDateStr = $this->getValueByHeaderName('CLIA - Expiration Date', $rowData, $headers);
            if( $CLIAExpDateStr ) {
                $CLIAExpDate = $this->transformDatestrToDate($CLIAExpDateStr);
                $credentials->setCliaExpirationDate($CLIAExpDate);
            }

            //PFI
            $PFI = $this->getValueByHeaderName('PFI', $rowData, $headers);
            if( $PFI ) {
                $credentials->setNumberPFI($PFI);
            }

            //POPS Link => Identifier Type:POPS, Identifier:link, Link:link
            $POPS = $this->getValueByHeaderName('POPS Link', $rowData, $headers);
            if( $POPS ) {
                $popsIdentifier = new Identifier();
                $Identifier->setStatus($Identifier::STATUS_VERIFIED);

                $popsIdentifierTypeObj = $this->getObjectByNameTransformer('IdentifierTypeList','POPS',$systemuser);
                $popsIdentifier->setKeytype($popsIdentifierTypeObj);
                $popsIdentifier->setLink($POPS);
                $popsIdentifier->setField($POPS);

                $credentials->addIdentifier($popsIdentifier);
            }

            //Pubmed Link
            $Pubmed = $this->getValueByHeaderName('Pubmed Link', $rowData, $headers);
            if( $Pubmed ) {
                $PubmedIdentifier = new Identifier();
                $PubmedIdentifier->setStatus($Identifier::STATUS_VERIFIED);

                $PubmedIdentifierTypeObj = $this->getObjectByNameTransformer('IdentifierTypeList','Pubmed',$systemuser);
                $PubmedIdentifier->setKeytype($PubmedIdentifierTypeObj);
                $PubmedIdentifier->setLink($Pubmed);
                $PubmedIdentifier->setField($Pubmed);

                $credentials->addIdentifier($PubmedIdentifier);
            }

            //VIVO link
            $VIVO = $this->getValueByHeaderName('VIVO link', $rowData, $headers);
            if( $VIVO ) {
                $VIVOIdentifier = new Identifier();
                $VIVOIdentifier->setStatus($Identifier::STATUS_VERIFIED);

                $VIVOIdentifierTypeObj = $this->getObjectByNameTransformer('IdentifierTypeList','VIVO',$systemuser);
                $VIVOIdentifier->setKeytype($VIVOIdentifierTypeObj);
                $VIVOIdentifier->setLink($VIVO);
                $VIVOIdentifier->setField($VIVO);

                $credentials->addIdentifier($VIVOIdentifier);
            }




            //add scanorder Roles
            $user->addRole('ROLE_SCANORDER_SUBMITTER');

            //add Platform Admin role and WCMC Institution for specific users
            //TODO: remove in prod
            if( $user->getUsername() == "oli2002_@_wcmc-cwid" || $user->getUsername() == "vib9020_@_wcmc-cwid" ) {
                $user->addRole('ROLE_PLATFORM_ADMIN');
            }

            //************** get Aperio group roles and ROLE_SCANORDER_ORDERING_PROVIDER for this user **************//
            //TODO: this should be located on scanorder site
            //TODO: rewrite using Aperio's DB not SOAP functions
            $aperioUtil = new AperioUtil();
            echo "username=".$username."<br>";
            $userid = $aperioUtil->getUserIdByUserName($username);
            if( $userid ) {
                echo "userid=".$userid."<br>";
                $aperioRoles = $aperioUtil->getUserGroupMembership($userid);
                $stats = $aperioUtil->setUserPathologyRolesByAperioRoles( $user, $aperioRoles );
            }
            //************** end of  Aperio group roles **************//

            $user->setEnabled(true);
            $user->setLocked(false);
            $user->setExpired(false);

//            $found_user = $em->getRepository('OlegUserdirectoryBundle:User')->findOneByUsername( $user->getUsername() );
//            if( $found_user ) {
//                //
//            } else {
            //echo $username." not found ";
            $this->em->persist($user);
            $this->em->flush();
            $count++;


            //Assistants : s2id_oleg_userdirectorybundle_user_locations_0_assistant
            $assistants = $this->getValueByHeaderName('Assistants', $rowData, $headers);
            if( $assistants ) {
                $assistantsArr[$user->getId()] = $assistants;
            }


            //**************** create PerSiteSettings for this user **************//
            //TODO: this should be located on scanorder site
            $securityUtil = $this->container->get('order_security_utility');
            $perSiteSettings = $securityUtil->getUserPerSiteSettings($user);
            if( !$perSiteSettings ) {
                $perSiteSettings = new PerSiteSettings($systemuser);
                $perSiteSettings->setUser($user);
            }
            $params = $this->em->getRepository('OlegUserdirectoryBundle:SiteParameters')->findAll();
            if( count($params) != 1 ) {
                throw new \Exception( 'Must have only one parameter object. Found '.count($params).' object(s)' );
            }
            $param = $params[0];
            $institution = $param->getAutoAssignInstitution();
            $perSiteSettings->addPermittedInstitutionalPHIScope($institution);
            $this->em->persist($perSiteSettings);
            $this->em->flush();
            //**************** EOF create PerSiteSettings for this user **************//

            //record user log create
            $event = "User ".$user." has been created by ".$systemuser."<br>";
            $userSecUtil->createUserEditEvent($this->container->getParameter('employees.sitename'),$event,$systemuser,$user,null,'User Created');
//            }

            //exit('eof user');

        }//for each user


        //process assistants
        echo "count ass=".count($assistantsArr)."<br>";
        if( count($assistantsArr) > 0 ) {
            foreach( $assistantsArr as $userid => $assistants ) {

                echo "userid=".$userid."assistants=".$assistants."<br>";
                $user = $this->em->getRepository('OlegUserdirectoryBundle:User')->find($userid);
                $assistantsStrArr = explode(";",$assistants);

                foreach( $assistantsStrArr as $assistantsStr ) {
                    if( strtolower($assistantsStr) != 'null' ) {
                        $assistant = $this->em->getRepository('OlegUserdirectoryBundle:User')->findOneByNameStr($assistantsStr);
                        echo "found assistant=".$assistant."<br>";
                        if( $assistant ) {
                            $mainLocation = $user->getMainLocation();
                            $mainLocation->addAssistant($assistant);
                        }

                    }
                } //foreach

                if( count($assistantsStrArr) > 0 ) {
                    $this->em->flush();
                }

            } //foreach
        } //if


        exit();
        return $count;
    }









    public function getValueByHeaderName($header, $row, $headers) {

        $res = null;

        if( !$header ) {
            return $res;
        }

        //echo "header=".$header."<br>";
        //print_r($headers);
        //print_r($row[0]);

        //echo "cwid=(".$headers[0][39].")<br>";

        $key = array_search($header, $headers[0]);
        //echo "<br>key=".$key."<br>";

        if( $key === false ) {
            //echo "key is false !!!!!!!!!!<br>";
            return $res;
        }

        if( array_key_exists($key, $row[0]) ) {
            $res = $row[0][$key];
        }

        //echo "res=".$res."<br>";
        return $res;
    }


    //add two default locations: Home and Main Office
    public function addDefaultLocations($subjectUser,$creator) {

        $em = $this->em;
        $container = $this->container;

        if( $creator == null ) {
            $userSecUtil = $container->get('user_security_utility');
            $creator = $userSecUtil->findSystemUser();

            if( !$creator ) {
                $creator = $subjectUser;
            }
        }

        //echo "creator=".$creator.", id=".$creator->getId()."<br>";

        //Main Office Location
        $mainLocation = new Location($creator);
        $mainLocation->setName('Main Office');
        $mainLocation->setRemovable(false);
        $mainLocType = $em->getRepository('OlegUserdirectoryBundle:LocationTypeList')->findOneByName("Employee Office");
        $mainLocation->addLocationType($mainLocType);
        $subjectUser->addLocation($mainLocation);

        //Home Location
        $homeLocation = new Location($creator);
        $homeLocation->setName('Home');
        $homeLocation->setRemovable(false);
        $homeLocType = $em->getRepository('OlegUserdirectoryBundle:LocationTypeList')->findOneByName("Employee Home");
        $homeLocation->addLocationType($homeLocType);
        $subjectUser->addLocation($homeLocation);

        return $subjectUser;
    }

    public function getObjectByNameTransformer($className,$nameStr,$systemuser,$params=null) {
        $bundleName = null;
        $transformer = new GenericTreeTransformer($this->em, $systemuser, $className, $bundleName, $params);
        return $transformer->reverseTransform($nameStr);
    }


    //$Institution, $Department, $HeadDepartment, $Division, $HeadDivision, $Service, $HeadService can be separated by ";"
    public function addInstitutinalTree( $holderClassName, $systemuser, $Institution, $Department, $HeadDepartment, $Division, $HeadDivision, $Service, $HeadService ) {

        $holders = array();

        $InstitutionArr = explode(";", $Institution);
        $DepartmentArr = explode(";", $Department);
        $DivisionArr = explode(";", $Division);
        $ServiceArr = explode(";", $Service);

        $HeadDepartmentArr = explode(";", $HeadDepartment);
        $HeadDivisionArr = explode(";", $HeadDivision);
        $HeadServiceArr = explode(";", $HeadService);

        $DepartmentStr = null;
        $DivisionStr = null;
        $ServiceStr = null;

        $HeadDepartmentStr = null;
        $HeadDivisionStr = null;
        $HeadServiceStr = null;

        $index = 0;
        foreach( $InstitutionArr as $InstitutionStr ) {
            $InstitutionStr = trim($InstitutionStr);
            if( array_key_exists($index, $DepartmentArr) ) {
                $DepartmentStr = trim($DepartmentArr[$index]);
            }
            if( array_key_exists($index, $DivisionArr) ) {
                $DivisionStr = trim($DivisionArr[$index]);
            }
            if( array_key_exists($index, $ServiceArr) ) {
                $ServiceStr = trim($ServiceArr[$index]);
            }

            if( array_key_exists($index, $HeadDepartmentArr) ) {
                $HeadDepartmentStr = trim($HeadDepartmentArr[$index]);
            }
            if( array_key_exists($index, $HeadDivisionArr) ) {
                $HeadDivisionStr = trim($HeadDivisionArr[$index]);
            }
            if( array_key_exists($index, $HeadServiceArr) ) {
                $HeadServiceStr = trim($HeadServiceArr[$index]);
            }

            $holder = $this->addSingleInstitutinalTree( $holderClassName,$systemuser,$InstitutionStr,$DepartmentStr,$HeadDepartmentStr,$DivisionStr,$HeadDivisionStr,$ServiceStr,$HeadServiceStr );
            if( $holder ) {
                $holders[] = $holder;
            }

            $index++;
        }

        return $holders;
    }

    public function addSingleInstitutinalTree( $holderClassName,$systemuser,$Institution,$Department,$HeadDepartment,$Division,$HeadDivision,$Service,$HeadService ) {

        $holder = null;

        $InstitutionObj = null;
        $DepartmentObj = null;
        $DivisionObj = null;
        $ServiceObj = null;

        $mapper = array('prefix'=>'Oleg','bundleName'=>'UserdirectoryBundle','className'=>'Institution','organizationalGroupType'=>'OrganizationalGroupType');

        $params = array('type'=>'Medical');

        $transformer = new GenericTreeTransformer($this->em, $systemuser, $mapper['className'], $mapper['bundleName'], $params);

        if( $Institution && strtolower($Institution) != 'null' ) {

            $entityClass = "Oleg\\UserdirectoryBundle\\Entity\\".$holderClassName;
            $holder = new $entityClass($systemuser);
            $holder->setStatus($holder::STATUS_VERIFIED);

            $InstitutionObj = $this->getObjectByNameTransformer('Institution',$Institution,$systemuser,$params);
            //$InstitutionObj = $transformer->createNewEntity($Institution,$mapper['className'],$systemuser);

            if( $InstitutionObj ) {
                //set Institution tree node
                $holder->setInstitution($InstitutionObj);
            }
        }

        //department
        if( $Department && strtolower($Department) != 'null' && $InstitutionObj ) {

            $DepartmentObj = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->findByChildnameAndParent($Department,$InstitutionObj,$mapper);
            if( !$DepartmentObj ) {
                //$DepartmentObj = $this->getObjectByNameTransformer('Institution',$Department,$systemuser,$params);
                $DepartmentObj = $transformer->createNewEntity($Department,$mapper['className'],$systemuser);

                if( !$DepartmentObj->getParent() ) {
                    $InstitutionObj->addChild($DepartmentObj);
                    $organizationalGroupType = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->getDefaultLevelEntity($mapper, $DepartmentObj->getLevel());
                    $DepartmentObj->setOrganizationalGroupType($organizationalGroupType);
                    $this->em->persist($DepartmentObj);
                } else {
                    if( $DepartmentObj->getParent()->getId() != $InstitutionObj->getId() ) {
                        throw new \Exception('Department: Tree node object ' . $DepartmentObj . ' already has a parent, but it is different: existing pid=' . $DepartmentObj->getParent()->getId() . ', new pid='.$InstitutionObj->getId());
                    }
                }
            }

            if( $DepartmentObj ) {
                if( strtolower($HeadDepartment) == 'yes' ) {
                    $HeadDepartmentObj = $this->getObjectByNameTransformer('PositionTypeList','Head of Department',$systemuser);
                    if( method_exists($holder,'addUserPosition') ) {
                        $holder->addUserPosition($HeadDepartmentObj);
                    }
                }
                //overwrite Institution tree node
                $holder->setInstitution($DepartmentObj);
            }
        }

        //division
        if( $Division && strtolower($Division) != 'null' && $DepartmentObj ) {

            $DivisionObj = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->findByChildnameAndParent($Division,$DepartmentObj,$mapper);
            if( !$DivisionObj ) {
                //$DivisionObj = $this->getObjectByNameTransformer('Institution',$Division,$systemuser,$params);
                $DivisionObj = $transformer->createNewEntity($Division,$mapper['className'],$systemuser);

                if( !$DivisionObj->getParent() ) {
                    $DepartmentObj->addChild($DivisionObj);
                    $organizationalGroupType = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->getDefaultLevelEntity($mapper, $DivisionObj->getLevel());
                    $DivisionObj->setOrganizationalGroupType($organizationalGroupType);
                    $this->em->persist($DivisionObj);
                } else {
                    if( $DivisionObj->getParent()->getId() != $DepartmentObj->getId() ) {
                        throw new \Exception('Division: Tree node object ' . $DivisionObj . ' already has a parent, but it is different: existing pid=' . $DivisionObj->getParent()->getId() . ', new pid='.$DepartmentObj->getId());
                    }
                }
            }

            if( $DivisionObj ) {
                if( strtolower($HeadDivision) == 'yes' ) {
                    $HeadDivisionObj = $this->getObjectByNameTransformer('PositionTypeList','Head of Division',$systemuser);
                    if( method_exists($holder,'addUserPosition') ) {
                        $holder->addUserPosition($HeadDivisionObj);
                    }
                }
                //overwrite Institution tree node
                $holder->setInstitution($DivisionObj);
            }
        }

        //service
        if( $Service && strtolower($Service) != 'null' && $DivisionObj ) {

            $ServiceObj = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->findByChildnameAndParent($Service,$DivisionObj,$mapper);
            if( !$ServiceObj ) {
                //$ServiceObj = $this->getObjectByNameTransformer('Institution',$Service,$systemuser,$params);
                $ServiceObj = $transformer->createNewEntity($Service,$mapper['className'],$systemuser);

                if( !$ServiceObj->getParent() ) {
                    $DivisionObj->addChild($ServiceObj);
                    $organizationalGroupType = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->getDefaultLevelEntity($mapper, $ServiceObj->getLevel());
                    $ServiceObj->setOrganizationalGroupType($organizationalGroupType);
                    $this->em->persist($ServiceObj);
                } else {
                    if( $ServiceObj->getParent()->getId() != $DivisionObj->getId() ) {
                        throw new \Exception('Service: Tree node object ' . $ServiceObj . ' already has a parent, but it is different: existing pid=' . $ServiceObj->getParent()->getId() . ', new pid='.$DivisionObj->getId());
                    }
                }
            }

            if( $ServiceObj ) {
                if( strtolower($HeadService) == 'yes' ) {
                    $HeadServiceObj = $this->getObjectByNameTransformer('PositionTypeList','Head of Service',$systemuser);
                    if( method_exists($holder,'addUserPosition') ) {
                        $holder->addUserPosition($HeadServiceObj);
                    }
                }
                //overwrite Institution tree node
                $holder->setInstitution($ServiceObj);
            }
        }

        return $holder;
    }

    public function processBoardCertification($credentials, $systemuser,$rowData, $headers, $boardCertSpec) {
        $boardCertSpecArr = explode(";", $boardCertSpec);

        $issueDateStr = $this->getValueByHeaderName('Board Certification - Specialty', $rowData, $headers);
        $issueDateArr = explode(";", $issueDateStr);

        $expDateStr = $this->getValueByHeaderName('Board Certification - Expiration Date', $rowData, $headers);
        $expDateArr = explode(";", $expDateStr);

        $recertDateStr = $this->getValueByHeaderName('Board Certification - Recertification Date', $rowData, $headers);
        $recertDateArr = explode(";", $recertDateStr);

        $issueDate = null;
        $expDate = null;
        $recertDate = null;

        $index = 0;
        foreach( $boardCertSpecArr as $boardCertSpecStr ) {

            if( array_key_exists($index, $issueDateArr) ) {
                $issueDate = $issueDateArr[$index];
            }
            if( array_key_exists($index, $expDateArr) ) {
                $expDate = $expDateArr[$index];
            }
            if( array_key_exists($index, $recertDateArr) ) {
                $recertDate = $recertDateArr[$index];
            }

            $boardCert = $this->addSingleBoardCertification($systemuser,$rowData, $headers, $boardCertSpecStr, $issueDate, $expDate, $recertDate);
            if( $boardCert ) {
                $credentials->addBoardCertification($boardCert);
            }

            $index++;
        }

    }

    public function addSingleBoardCertification($systemuser,$rowData, $headers, $boardCertSpecStr, $issueDate, $expDate, $recertDate) {
        if( $boardCertSpecStr && strtolower($boardCertSpecStr) != 'null' ) {
            $boardCert = new BoardCertification();
            $boardCertSpecObj = $this->getObjectByNameTransformer('BoardCertifiedSpecialties',$boardCertSpecStr,$systemuser);
            $boardCert->setSpecialty($boardCertSpecObj);

            //Board Certification - Date Issued
            if( strtolower($issueDate) != 'null' ) {
                $issueDate = $this->transformDatestrToDate($issueDate);
                $boardCert->setIssueDate($issueDate);
            }

            //Board Certification - Expiration Date
            if( strtolower($expDate) != 'null' ) {
                $expDate = $this->transformDatestrToDate($expDate);
                $boardCert->setExpirationDate($expDate);
            }

            //Board Certification - Recertification Date
            if( strtolower($recertDate) != 'null' ) {
                $recertDate = $this->transformDatestrToDate($recertDate);
                $boardCert->setRecertificationDate($recertDate);
            }

            return $boardCert;
        }
        return null;
    }


    public function transformDatestrToDate($datestr) {
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
            $event = "Fellowship Applicantions warning: " . $msg;
            $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$systemUser,null,null,'Warning');

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
            $date = new \DateTime($datestr);
        } catch (Exception $e) {
            $msg = 'Failed to convert string'.$datestr.'to DateTime:'.$e->getMessage();
            //throw new \UnexpectedValueException($msg);
            $logger = $this->container->get('logger');
            $logger->error($msg);
        }

        return $date;
    }

} 