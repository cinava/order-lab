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

        ini_set('max_execution_time', 3600); //3600 seconds = 60 minutes;

        $inputFileName = __DIR__ . '/../Util/UsersFullNew.xlsx';

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

            if( $user ) {

                //Assistants : s2id_oleg_userdirectorybundle_user_locations_0_assistant
                $assistants = $this->getValueByHeaderName('Assistants', $rowData, $headers);
                if( $assistants ) {
                    $assistantsArr[$user->getId()] = $assistants;
                }

                continue; //ignore existing users to prevent overwrite
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

            //Degree: TrainingDegreeList - Multi
            $degreeStr = $this->getValueByHeaderName('Degree', $rowData, $headers);
            if( $degreeStr ) {

                $degreeArr = explode(";",$degreeStr);
                foreach( $degreeArr as $degree ) {
                    $degree = trim($degree);
                    if( $degree ) {
                        $training = new Training($systemuser);
                        $training->setStatus($training::STATUS_VERIFIED);
                        $degreeObj = $this->getObjectByNameTransformer('TrainingDegreeList',$degree,$systemuser);
                        $training->setDegree($degreeObj);
                        $training->setAppendDegreeToName(true);
                        $user->addTraining($training);
                    }
                }
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

            //fax, office are stored in Location object
            $mainLocation = $user->getMainLocation();
            $mainLocation->setStatus($mainLocation::STATUS_VERIFIED);
            $mainLocation->setFax($this->getValueByHeaderName('Fax Number', $rowData, $headers));
            $mainLocation->setIc($this->getValueByHeaderName('Intercom', $rowData, $headers));
            $mainLocation->setPager($this->getValueByHeaderName('Pager', $rowData, $headers));


            //phone(s)
            $BusinessPhones = $this->getValueByHeaderName('Business Phone', $rowData, $headers);
            $BusinessPhonesArr = explode(";",$BusinessPhones);

            if( count($BusinessPhonesArr) > 0 ) {
                $BusinessPhone = array_shift($BusinessPhonesArr);
                $mainLocation->setPhone($BusinessPhone);
            }

            foreach( $BusinessPhonesArr as $BusinessPhone ) {
                $location = new Location();
                $location->setStatus($location::STATUS_VERIFIED);
                $location->setRemovable(true);
                $location->setName('Other Location');
                $otherLocType = $this->em->getRepository('OlegUserdirectoryBundle:LocationTypeList')->findOneByName("Employee Office");
                $location->addLocationType($otherLocType);
                $location->setPhone($BusinessPhone);
                $user->addLocation($location);
            }


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
                $administrativeTitles = $this->addInstitutinalTree('AdministrativeTitle',$user,$systemuser,$administrativeTitleStr,$Institution,$Department,$HeadDepartment,$Division,$HeadDivision,$Service,$HeadService);

//                if( count($administrativeTitles) == 0 ) {
//                    $administrativeTitles[] = new AdministrativeTitle();
//                }
//
//                foreach( $administrativeTitles as $administrativeTitle ) {
//                    //set title object: Administrative Title
//                    $titleObj = $this->getObjectByNameTransformer('AdminTitleList',$administrativeTitleStr,$systemuser);
//                    $administrativeTitle->setName($titleObj);
//
//                    $user->addAdministrativeTitle($administrativeTitle);
//                }
                //echo "count admin titles=".count($administrativeTitles)."<br>";
                //exit('admin title end');
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
                $msaTitles = $this->addInstitutinalTree('MedicalTitle',$user,$systemuser,$msaTitleStr,$Institution,$Department,$HeadDepartment,$Division,$HeadDivision,$Service,$HeadService);

//                if( count($msaTitles) == 0 ) {
//                    $msaTitles[] = new MedicalTitle();
//                }
//
//                foreach( $msaTitles as $msaTitle ) {
//                    $titleObj = $this->getObjectByNameTransformer('MedicalTitleList',$msaTitleStr,$systemuser);
//                    $msaTitle->setName($titleObj);
//
//                    $user->addMedicalTitle($msaTitle);
//                }

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
                $academicTitles = $this->addInstitutinalTree('AppointmentTitle',$user,$systemuser,$academicTitleStr,$Institution,$Department,$HeadDepartment,$Division,$HeadDivision,$Service,$HeadService);

                //if( count($academicTitles) == 0 ) {
                //    $academicTitles[] = new AppointmentTitle();
                //}

                //Academic Appointment - Faculty Track => oleg_userdirectorybundle_user_appointmentTitles_0_positions
                //faculty Track can be multiple but the rest of title singular
                $facultyTrackObjArr = array();
                $facultyTrackStrMulti = $this->getValueByHeaderName('Academic Appointment - Faculty Track', $rowData, $headers);
                $facultyTrackStrArr = explode(";",$facultyTrackStrMulti);
                foreach( $facultyTrackStrArr as $facultyTrackStr ) {
                    $facultyTrackStr = trim($facultyTrackStr);
                    $facultyTrackObj = $this->getObjectByNameTransformer('PositionTrackTypeList',$facultyTrackStr,$systemuser);
                    $facultyTrackObjArr[] = $facultyTrackObj;
                }


                foreach( $academicTitles as $academicTitle ) {

                    foreach( $facultyTrackObjArr as $facultyTrackObj ) {
                        $academicTitle->addPosition($facultyTrackObj);
                    }

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
            $PFI = $this->getValueByHeaderName('PFI', $rowData, $headers);
            $CLIAStr = $this->getValueByHeaderName('CLIA - Number', $rowData, $headers);
            $IdentifierNumberStr = $this->getValueByHeaderName('Identifier', $rowData, $headers);

            if( $boardCertSpec || $nyphCodeStr || $licenseNumberStr || $PFI || $CLIAStr || $IdentifierNumberStr ) {
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
            if( $licenseNumberStr ) {
                $licenseState = new StateLicense();

                $licenseState->setLicenseNumber($licenseNumberStr);

                $licenseStateStr = $this->getValueByHeaderName('License state', $rowData, $headers);
                $licenseStateObj = $this->getObjectByNameTransformer('States',$licenseStateStr,$systemuser);
                $licenseState->setState($licenseStateObj);

                //License expiration
                $expDateStr = $this->getValueByHeaderName('License expiration', $rowData, $headers);
                $expDate = $this->transformDatestrToDate($expDateStr);
                $licenseState->setLicenseExpirationDate($expDate);

                $credentials->addStateLicense($licenseState);
            }

            //Administrative Comment - Category
            $AdministrativeCommentCategory = $this->getValueByHeaderName('Administrative Comment - Category', $rowData, $headers);
            if( $AdministrativeCommentCategory ) {

                $AdministrativeCommentCategory = trim($AdministrativeCommentCategory);

                $comment = new AdminComment($systemuser);

                //Administrative Comment - Name
                $AdministrativeCommentName = $this->getValueByHeaderName('Administrative Comment - Name', $rowData, $headers);

                //Administrative Comment - Comment
                $AdministrativeCommentComment = $this->getValueByHeaderName('Administrative Comment - Comment', $rowData, $headers);

                //check if Category exists (root)
                $transformer = new GenericTreeTransformer($this->em, $systemuser, 'CommentTypeList', 'UserdirectoryBundle');
                $mapper = array('prefix'=>'Oleg','bundleName'=>'UserdirectoryBundle','className'=>'CommentTypeList','organizationalGroupType'=>'CommentGroupType');
                $AdministrativeCommentCategoryObj = $this->getObjectByNameTransformer('CommentTypeList',$AdministrativeCommentCategory,$systemuser);
                //$AdministrativeCommentCategoryObj = $transformer->createNewEntity($AdministrativeCommentCategory,$mapper['className'],$systemuser);
                $this->em->persist($AdministrativeCommentCategoryObj);

                $AdministrativeCommentNameObj = null;
                if( $AdministrativeCommentCategoryObj ) {
                    $AdministrativeCommentNameObj = $this->em->getRepository('OlegUserdirectoryBundle:CommentTypeList')->findByChildnameAndParent($AdministrativeCommentName,$AdministrativeCommentCategoryObj,$mapper);
                }

                if( !$AdministrativeCommentNameObj ) {
                    $AdministrativeCommentNameObj = $transformer->createNewEntity($AdministrativeCommentName,'CommentTypeList',$systemuser);

                    if( !$AdministrativeCommentNameObj->getParent() ) {
                        $AdministrativeCommentCategoryObj->addChild($AdministrativeCommentNameObj);
                        $organizationalGroupType = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->getDefaultLevelEntity($mapper, 1);
                        $AdministrativeCommentNameObj->setOrganizationalGroupType($organizationalGroupType);
                        $this->em->persist($AdministrativeCommentNameObj);
                    } else {
                        if( $AdministrativeCommentNameObj->getParent()->getId() != $AdministrativeCommentCategoryObj->getId() ) {
                            throw new \Exception('Comment Name: Tree node object ' . $AdministrativeCommentNameObj . ' already has a parent, but it is different: existing pid=' . $AdministrativeCommentNameObj->getParent()->getId() . ', new pid='.$AdministrativeCommentCategoryObj->getId());
                        }
                    }

//                    $AdministrativeCommentCategoryObj->addChild($AdministrativeCommentNameObj);
//                    $organizationalGroupType = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->getDefaultLevelEntity($mapper, 1);
//                    $AdministrativeCommentNameObj->setOrganizationalGroupType($organizationalGroupType);
//                    $this->em->persist($AdministrativeCommentNameObj);
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
            if( $IdentifierNumberStr ) {

                $IdentifierNumberArr = explode(";", $IdentifierNumberStr);

                $IdentifierTypeStr = $this->getValueByHeaderName('Identifier - Type', $rowData, $headers);
                $IdentifierTypeArr = explode(";", $IdentifierTypeStr);

                $IdentifierLinkStr = $this->getValueByHeaderName('Identifier - link', $rowData, $headers);
                $IdentifierLinkArr = explode(";", $IdentifierLinkStr);


                $index = 0;
                foreach( $IdentifierNumberArr as $IdentifierStr ) {

                    $IdentifierTypeStr = null;
                    $IdentifierLinkStr = null;

                    if( array_key_exists($index, $IdentifierTypeArr) ) {
                        $IdentifierTypeStr = $IdentifierTypeArr[$index];
                    }
                    if( array_key_exists($index, $IdentifierLinkArr) ) {
                        $IdentifierLinkStr = $IdentifierLinkArr[$index];
                    }

                    $Identifier = new Identifier();
                    $Identifier->setStatus($Identifier::STATUS_VERIFIED);

                    $IdentifierTypeStr = trim($IdentifierTypeStr);
                    $IdentifierLinkStr = trim($IdentifierLinkStr);
                    $IdentifierStr = trim($IdentifierStr);

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
            if( $PFI ) {
                $credentials->setNumberPFI($PFI);
            }

            //POPS Link => Identifier Type:POPS, Identifier:link, Link:link
            $POPS = $this->getValueByHeaderName('POPS Link', $rowData, $headers);
            if( $POPS ) {
                $popsIdentifier = new Identifier();
                $popsIdentifier->setStatus($popsIdentifier::STATUS_VERIFIED);

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
                $PubmedIdentifier->setStatus($PubmedIdentifier::STATUS_VERIFIED);

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
                $VIVOIdentifier->setStatus($VIVOIdentifier::STATUS_VERIFIED);

                $VIVOIdentifierTypeObj = $this->getObjectByNameTransformer('IdentifierTypeList','VIVO',$systemuser);
                $VIVOIdentifier->setKeytype($VIVOIdentifierTypeObj);
                $VIVOIdentifier->setLink($VIVO);
                $VIVOIdentifier->setField($VIVO);

                $credentials->addIdentifier($VIVOIdentifier);
            }



            //add lowest roles for scanorder and userdirectory
            $user->addRole('ROLE_SCANORDER_SUBMITTER');
            $user->addRole('ROLE_USERDIRECTORY_OBSERVER');

            //add Platform Admin role and WCMC Institution for specific users
            //TODO: remove in prod
            if( $user->getUsername() == "oli2002_@_wcmc-cwid" || $user->getUsername() == "vib9020_@_wcmc-cwid" ) {
                $user->addRole('ROLE_PLATFORM_ADMIN');
            }

            if( $user->getUsername() == "jep2018_@_wcmc-cwid" ) {
                $user->addRole('ROLE_USERDIRECTORY_EDITOR');
                $user->addRole('ROLE_FELLAPP_COORDINATOR');
                $user->addRole('ROLE_FELLAPP_COORDINATOR_WCMC_BREASTPATHOLOGY');
                $user->addRole('ROLE_FELLAPP_COORDINATOR_WCMC_CYTOPATHOLOGY');
                $user->addRole('ROLE_FELLAPP_COORDINATOR_WCMC_GYNECOLOGICPATHOLOGY');
                $user->addRole('ROLE_FELLAPP_COORDINATOR_WCMC_GASTROINTESTINALPATHOLOGY');
                $user->addRole('ROLE_FELLAPP_COORDINATOR_WCMC_GENITOURINARYPATHOLOGY');
                $user->addRole('ROLE_FELLAPP_COORDINATOR_WCMC_HEMATOPATHOLOGY');
                $user->addRole('ROLE_FELLAPP_COORDINATOR_WCMC_MOLECULARGENETICPATHOLOGY');
            }


            //GI-Rhonda Yantiss: rhy2001
            if( $user->getUsername() == "rhy2001_@_wcmc-cwid" ) {
                $user->addRole('ROLE_FELLAPP_DIRECTOR');
            }

            //Cyto-Rana Hoda: rhoda
            if( $user->getUsername() == "rhoda_@_wcmc-cwid" ) {
                $user->addRole('ROLE_FELLAPP_DIRECTOR');
            }

            //Heme-Attilio Orazi: ato9002
            if( $user->getUsername() == "ato9002_@_wcmc-cwid" ) {
                $user->addRole('ROLE_FELLAPP_DIRECTOR');
            }

            //Heme-Scott Ely: sae2001
            if( $user->getUsername() == "sae2001_@_wcmc-cwid" ) {
                $user->addRole('ROLE_FELLAPP_DIRECTOR');
            }

            //Mol Gen- Michael Kluk: mik9095
            if( $user->getUsername() == "mik9095_@_wcmc-cwid" ) {
                $user->addRole('ROLE_FELLAPP_DIRECTOR');
            }

            //Gyn- Lora Ellenson lhellens
            if( $user->getUsername() == "lhellens_@_wcmc-cwid" ) {
                $user->addRole('ROLE_FELLAPP_DIRECTOR');
            }

            //Breast- Sandra Shin: sjshin
            if( $user->getUsername() == "sjshin_@_wcmc-cwid" ) {
                $user->addRole('ROLE_FELLAPP_DIRECTOR');
            }

            //Timothy D'Alfonso <tid9007@med.cornell.edu>
            if( $user->getUsername() == "tid9007_@_wcmc-cwid" ) {
                $user->addRole('ROLE_FELLAPP_DIRECTOR');
            }

            //Syed A F Hoda <sahoda@med.cornell.edu>
            if( $user->getUsername() == "sahoda_@_wcmc-cwid" ) {
                $user->addRole('ROLE_FELLAPP_DIRECTOR');
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
                        $assistant = $this->em->getRepository('OlegUserdirectoryBundle:User')->findOneByNameStr($assistantsStr,"AND");
                        if( !$assistant ) {
                            //try again with "last name OR first name"
                            $assistant = $this->em->getRepository('OlegUserdirectoryBundle:User')->findOneByNameStr($assistantsStr,"OR");
                        }
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


        //exit();
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
        $nameStr = trim($nameStr);
        return $transformer->reverseTransform($nameStr);
    }


    //$Institution, $Department, $HeadDepartment, $Division, $HeadDivision, $Service, $HeadService can be separated by ";"
    public function addInstitutinalTree( $holderClassName, $subjectUser, $systemuser, $titles, $Institution, $Department, $HeadDepartment, $Division, $HeadDivision, $Service, $HeadService ) {

        $holders = array();

        //echo "titles=".$titles."<br>";
        //echo "Institution=".$Institution."<br>";

        $titleArr = explode(";", $titles);

        $InstitutionArr = explode(";", $Institution);
        $DepartmentArr = explode(";", $Department);
        $DivisionArr = explode(";", $Division);
        $ServiceArr = explode(";", $Service);

        $HeadDepartmentArr = explode(";", $HeadDepartment);
        $HeadDivisionArr = explode(";", $HeadDivision);
        $HeadServiceArr = explode(";", $HeadService);

        //remove empty from array
        for( $i=0; $i<count($titleArr); $i++ ) {
            echo "el=".$titleArr[$i]."<br>";
            if( trim($titleArr[$i]) == "" ) {
                unset($titleArr[$i]);
            }
        }
        for( $i=0; $i<count($InstitutionArr); $i++ ) {
            echo "el=(".$InstitutionArr[$i].")<br>";
            if( trim($InstitutionArr[$i]) == "" ) {
                //echo "remove el=".$InstitutionArr[$i]."<br>";
                unset($InstitutionArr[$i]);
            }
        }
        //exit('1');

        if( count($InstitutionArr) != 0 && count($InstitutionArr) != count($titleArr) ) {
            throw new \Exception('Title count='.count($titleArr).' is not equal to Institution count=' . count($InstitutionArr));
        }

//        //lead can be title or institution
//        if( count($InstitutionArr) > count($titleArr) ) {
//            //lead inst
//            $leadArr = $InstitutionArr;
//            $leadInst = true;
//            //echo "leadArr Inst<br>";
//        } else {
//            $leadArr = $titleArr;
//            $leadInst = false;
//            //echo "leadArr Title<br>";
//        }

        //echo "leadArr count=".count($leadArr)."<br>";

        //$lastInstitutionStr = null;
        //$lastDepartmentStr = null;
        //$lastDivisionStr = null;
        //$lastServiceStr = null;
        //$lastTitleStr = null;

        $index = 0;
        foreach( $titleArr as $titleStr ) {

            //echo "index=".$index."<br>";

            $titleStr = trim($titleStr);

            if( !$titleStr ) {
                continue;
            }

//            $InstitutionStr = null;
//            $titleStr = null;
//            if( $leadInst ) {
//                if( array_key_exists($index, $titleArr) ) {
//                    $titleStr = trim($titleArr[$index]);
//                    $lastTitleStr = $titleStr;
//                } else {
//                    $titleStr = $lastTitleStr;
//                }
//                $InstitutionStr = $leadStr;
//            } else {
//                if( array_key_exists($index, $InstitutionArr) ) {
//                    $InstitutionStr = trim($InstitutionArr[$index]);
//                    $lastInstitutionStr = $InstitutionStr;
//                } else {
//                    $InstitutionStr = $lastInstitutionStr;
//                }
//                $titleStr = $leadStr;
//            }


            $InstitutionStr = null;
            $DepartmentStr = null;
            $DivisionStr = null;
            $ServiceStr = null;

            $HeadDepartmentStr = null;
            $HeadDivisionStr = null;
            $HeadServiceStr = null;


            if( array_key_exists($index, $InstitutionArr) ) {
                $InstitutionStr = trim($InstitutionArr[$index]);
            }

            if( array_key_exists($index, $DepartmentArr) ) {
                $DepartmentStr = trim($DepartmentArr[$index]);
                //$lastDepartmentStr = $DepartmentStr;
            } else {
                //$DepartmentStr = $lastDepartmentStr;
            }

            if( array_key_exists($index, $DivisionArr) ) {
                $DivisionStr = trim($DivisionArr[$index]);
                //$lastDivisionStr = $DivisionStr;
            } else {
                //$DivisionStr = $lastDivisionStr;
            }

            if( array_key_exists($index, $ServiceArr) ) {
                $ServiceStr = trim($ServiceArr[$index]);
                //$lastServiceStr = $ServiceStr;
            } else {
                //$ServiceStr = $lastServiceStr;
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

            //echo "holders < leadArr=".count($holders)." < ".count($leadArr)."<br>";
            if( !$holder && count($holders) < count($titleArr)-1 ) {
                $entityClass = "Oleg\\UserdirectoryBundle\\Entity\\".$holderClassName;
                $holder = new $entityClass($systemuser);
                $holder->setStatus($holder::STATUS_VERIFIED);
            }

            if( $holder ) {

                $holders[] = $holder;

                //$setMethod = "setName";

                //set title object: Administrative Title
                if( $holderClassName == 'AdministrativeTitle' ) {
                    $titleClassName = 'AdminTitleList';
                }
                if( $holderClassName == 'MedicalTitle' ) {
                    $titleClassName = 'MedicalTitleList';
                }
                if( $holderClassName == 'AppointmentTitle' ) {
                    $titleClassName = 'AppTitleList';
                    //$setMethod = "addPosition";
                }

                $titleObj = $this->getObjectByNameTransformer($titleClassName,$titleStr,$systemuser);
                //$holder->$setMethod($titleObj);
                $holder->setName($titleObj);
                $addMethod = "add".$holderClassName;
                $subjectUser->$addMethod($holder);

            }


            $index++;

        } //foreach

        return $holders;
    }

    public function addSingleInstitutinalTree( $holderClassName,$systemuser,$Institution,$Department,$HeadDepartment,$Division,$HeadDivision,$Service,$HeadService ) {

        $holder = null;

        $Institution = trim($Institution);
        $Department = trim($Department);
        $Division = trim($Division);
        $Service = trim($Service);

        $HeadDepartment = trim($HeadDepartment);
        $HeadDivision = trim($HeadDivision);
        $HeadService = trim($HeadService);

        //echo "Institution=(".$Institution.")<br>";
        if( !$Institution ) {
            //exit('no inst');
            return $holder;
        } else {
            //exit('inst ok');
        }

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
            //$levelInstitution = $this->em->getRepository('OlegUserdirectoryBundle:OrganizationalGroupType')->findOneByName('Institution');
            //$InstitutionObj->setOrganizationalGroupType($levelInstitution);

            if( $InstitutionObj ) {
                //set Institution tree node
                $holder->setInstitution($InstitutionObj);
            }
        }

        //department
        if( $Institution && $Department && strtolower($Department) != 'null' && $InstitutionObj ) {

            $DepartmentObj = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->findByChildnameAndParent($Department,$InstitutionObj,$mapper);
            if( !$DepartmentObj ) {
                //$DepartmentObj = $this->getObjectByNameTransformer('Institution',$Department,$systemuser,$params);
                $DepartmentObj = $transformer->createNewEntity($Department,$mapper['className'],$systemuser);

                if( !$DepartmentObj->getParent() ) {
                    $InstitutionObj->addChild($DepartmentObj);
                    $organizationalGroupType = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->getDefaultLevelEntity($mapper, 1);
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
        if( $Institution && $Department && $Division && strtolower($Division) != 'null' && $DepartmentObj ) {

            $DivisionObj = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->findByChildnameAndParent($Division,$DepartmentObj,$mapper);
            if( !$DivisionObj ) {
                //$DivisionObj = $this->getObjectByNameTransformer('Institution',$Division,$systemuser,$params);
                $DivisionObj = $transformer->createNewEntity($Division,$mapper['className'],$systemuser);

                if( !$DivisionObj->getParent() ) {
                    $DepartmentObj->addChild($DivisionObj);
                    $organizationalGroupType = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->getDefaultLevelEntity($mapper, 2);
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
        if( $Institution && $Department && $Division && $Service && strtolower($Service) != 'null' && $DivisionObj ) {

            $ServiceObj = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->findByChildnameAndParent($Service,$DivisionObj,$mapper);
            if( !$ServiceObj ) {
                //$ServiceObj = $this->getObjectByNameTransformer('Institution',$Service,$systemuser,$params);
                $ServiceObj = $transformer->createNewEntity($Service,$mapper['className'],$systemuser);

                if( !$ServiceObj->getParent() ) {
                    $DivisionObj->addChild($ServiceObj);
                    $organizationalGroupType = $this->em->getRepository('OlegUserdirectoryBundle:Institution')->getDefaultLevelEntity($mapper, 3);
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

//        echo "inst level title=".$InstitutionObj->getOrganizationalGroupType().", level=".$InstitutionObj->getLevel()."<br>";
//        echo "dep level title=".$DepartmentObj->getOrganizationalGroupType().", level=".$DepartmentObj->getLevel()."<br>";
//        echo "div level title=".$DivisionObj->getOrganizationalGroupType().", level=".$DivisionObj->getLevel()."<br>";
//        echo "ser level title=".$ServiceObj->getOrganizationalGroupType().", level=".$ServiceObj->getLevel()."<br>";
//        //exit();

        return $holder;
    }

    public function processBoardCertification($credentials, $systemuser, $rowData, $headers, $boardCertSpec) {
        $boardCertSpecArr = explode(";", $boardCertSpec);

        $CertifyingBoardOrganizationStr = $this->getValueByHeaderName('Certifying Board Organization', $rowData, $headers);
        $CertifyingBoardOrganizationArr = explode(";", $CertifyingBoardOrganizationStr);

        $issueDateStr = $this->getValueByHeaderName('Board Certification - Date Issued', $rowData, $headers);
        $issueDateArr = explode(";", $issueDateStr);

        $expDateStr = $this->getValueByHeaderName('Board Certification - Expiration Date', $rowData, $headers);
        $expDateArr = explode(";", $expDateStr);

        $recertDateStr = $this->getValueByHeaderName('Board Certification - Recertification Date', $rowData, $headers);
        $recertDateArr = explode(";", $recertDateStr);

        $index = 0;
        foreach( $boardCertSpecArr as $boardCertSpecStr ) {

            $issueDate = null;
            $expDate = null;
            $recertDate = null;
            $CertifyingBoardOrganization = null;

            if( array_key_exists($index, $issueDateArr) ) {
                $issueDate = $issueDateArr[$index];
            }
            if( array_key_exists($index, $expDateArr) ) {
                $expDate = $expDateArr[$index];
            }
            if( array_key_exists($index, $recertDateArr) ) {
                $recertDate = $recertDateArr[$index];
            }
            if( array_key_exists($index, $CertifyingBoardOrganizationArr) ) {
                $CertifyingBoardOrganization = $CertifyingBoardOrganizationArr[$index];
            }

            $boardCert = $this->addSingleBoardCertification($systemuser, $boardCertSpecStr, $issueDate, $expDate, $recertDate, $CertifyingBoardOrganization);
            if( $boardCert ) {
                $credentials->addBoardCertification($boardCert);
            }

            $index++;
        }

    }

    public function addSingleBoardCertification($systemuser, $boardCertSpecStr, $issueDate, $expDate, $recertDate, $CertifyingBoardOrganization) {
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

            //Certifying Board Organization
            $CertifyingBoardOrganization = 'American Board of Pathology'; //temporary fix => add to user excel
            if( strtolower($CertifyingBoardOrganization) != 'null' ) {
                $CertifyingBoardOrganizationObj = $this->getObjectByNameTransformer('CertifyingBoardOrganization',$CertifyingBoardOrganization,$systemuser);
                $boardCert->setCertifyingBoardOrganization($CertifyingBoardOrganizationObj);
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