<?php
/**
 * Created by PhpStorm.
 * User: DevServer
 * Date: 8/20/15
 * Time: 4:21 PM
 */

namespace Oleg\FellAppBundle\Util;


use Doctrine\ORM\EntityNotFoundException;
use Oleg\FellAppBundle\Entity\Interview;
use Oleg\UserdirectoryBundle\Entity\AccessRequest;
use Oleg\UserdirectoryBundle\Entity\BoardCertification;
use Oleg\UserdirectoryBundle\Entity\Citizenship;
use Oleg\UserdirectoryBundle\Entity\Document;
use Oleg\UserdirectoryBundle\Entity\EmploymentStatus;
use Oleg\UserdirectoryBundle\Entity\Examination;
use Oleg\FellAppBundle\Entity\FellowshipApplication;
use Oleg\UserdirectoryBundle\Entity\GeoLocation;
use Oleg\UserdirectoryBundle\Entity\JobTitleList;
use Oleg\UserdirectoryBundle\Entity\Location;
use Oleg\FellAppBundle\Entity\Reference;
use Oleg\UserdirectoryBundle\Entity\StateLicense;
use Oleg\UserdirectoryBundle\Entity\Training;
use Oleg\UserdirectoryBundle\Entity\User;
use Oleg\UserdirectoryBundle\Form\DataTransformer\GenericTreeTransformer;
use Oleg\UserdirectoryBundle\Util\EmailUtil;
use Oleg\UserdirectoryBundle\Util\UserUtil;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;


/*
 * 1) importFellApp
 * 2) populateFellApp
 */
class FellAppUtil {

    protected $em;
    protected $sc;
    protected $container;
    protected $uploadDir;
    protected $systemEmail;


    public function __construct( $em, $sc, $container ) {

        $this->em = $em;
        $this->sc = $sc;
        $this->container = $container;

        //fellapp.uploadpath = fellapp
        $this->uploadDir = 'Uploaded/'.$this->container->getParameter('fellapp.uploadpath');

        $userutil = new UserUtil();
        $this->systemEmail = $userutil->getSiteSetting($this->em,'siteEmail');
    }


    //1) Import google form spreadsheet and download it on the server; create Document object
    public function importFellApp() {

        $userUtil = new UserUtil();
        $allowPopulateFellApp = $userUtil->getSiteSetting($this->em,'AllowPopulateFellApp');
        if( !$allowPopulateFellApp ) {
            return null;
        }
        
        $maintenance = $userUtil->getSiteSetting($this->em,'maintenance');
        if( $maintenance ) {
            return null;
        }

        //echo "fellapp import <br>";

        $res = null;
        $logger = $this->container->get('logger');
        $userSecUtil = $this->container->get('user_security_utility');
        $systemUser = $userSecUtil->findSystemUser();
        $service = $this->getGoogleService();

        if( !$service ) {
            $event = "Google API service failed!";
            $logger->warning($event);
            $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$systemUser,null,null,'Error');
            $this->sendEmailToSystemEmail($event, $event);
        }

        if( $service ) {

            //echo "service ok <br>";

            //https://drive.google.com/open?id=1DN1BEbONKNmFpHU6xBo69YSLjXCnhRy0IbyXrwMzEzc
            $excelId = "1DN1BEbONKNmFpHU6xBo69YSLjXCnhRy0IbyXrwMzEzc";

            $path = $this->uploadDir.'/Spreadsheets';
            $fileDb = $this->downloadFileToServer($systemUser, $service, $excelId, 'excel', $path);

            if( $fileDb ) {
                $this->em->flush($fileDb);
                $event = "Fellowship Application Spreadsheet file has been successful downloaded to the server with id=" . $fileDb->getId().", title=".$fileDb->getUniquename();
                $logger->notice($event);
            } else {
                $event = "Fellowship Application Spreadsheet download failed!";
                $logger->warning($event);
                $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$systemUser,null,null,'Error');
                $this->sendEmailToSystemEmail($event, $event);
            }

            $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$systemUser,null,null,'Import of Fellowship Applications Spreadsheet');

        }

        //echo "import ok <br>";

        return $fileDb;
    }

    //2) populate fellowship applications from spreadsheet to DB (using uploaded files from Google Drive)
    public function populateFellApp( $path=null ) {

        $userUtil = new UserUtil();
        $allowPopulateFellApp = $userUtil->getSiteSetting($this->em,'AllowPopulateFellApp');
        if( !$allowPopulateFellApp ) {
            return;
        }
        
        $maintenance = $userUtil->getSiteSetting($this->em,'maintenance');
        if( $maintenance ) {
            return null;
        }

        //echo "fellapp populate Spreadsheet <br>";

        //1) get latest spreadsheet file from Uploaded/fellapp/Spreadsheets
        $fellappSpreadsheetType = $this->em->getRepository('OlegUserdirectoryBundle:DocumentTypeList')->findOneByName('Fellowship Application Spreadsheet');
        $documents = $this->em->getRepository('OlegUserdirectoryBundle:Document')->findBy(
            array('type' => $fellappSpreadsheetType),
            array('createdate'=>'desc'),
            1   //limit to one
        );

        if( count($documents) == 1 ) {
            $document = $documents[0];
        }

        //2a) get spreadsheet path
        $inputFileName = $document->getServerPath();    //'Uploaded/fellapp/Spreadsheets/Pathology Fellowships Application Form (Responses).xlsx';

        if( $path ) {
            $inputFileName = $path . "/" . $inputFileName;
        }

        //2b) populate applicants
        $populatedCount = $this->populateSpreadsheet($inputFileName);

        $userSecUtil = $this->container->get('user_security_utility');
        $systemUser = $userSecUtil->findSystemUser();
        $event = "Populated ".$populatedCount." Fellowship Applications from Spreadsheets to DB.";
        $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$systemUser,null,null,'Import of Fellowship Application data to DB');

        //call tryRun() asynchronous
        $fellappRepGen = $this->container->get('fellapp_reportgenerator');
        $cmd = 'php ../app/console fellapp:generatereportrun --env=prod';
        $fellappRepGen->windowsCmdRunAsync($cmd);

        return $populatedCount;
    }





    public function getGoogleService() {
        $client_email = '1040591934373-1sjcosdt66bmani0kdrr5qmc5fibmvk5@developer.gserviceaccount.com';
        $pkey = __DIR__ . '/../Util/FellowshipApplication-f1d9f98353e5.p12';
        $user_to_impersonate = 'olegivanov@pathologysystems.org';
        $res = $this->authenticationP12Key($pkey,$client_email,$user_to_impersonate);
        return $res['service'];
    }

    //Using OAuth 2.0 for Server to Server Applications: using PKCS12 certificate file
    //https://developers.google.com/api-client-library/php/auth/service-accounts
    //1) Create a service account by Google Developers Console.
    //2) Delegate domain-wide authority to the service account.
    //3) Impersonate a user account.
    public function authenticationP12Key($pkey,$client_email,$user_to_impersonate) {
        $private_key = file_get_contents($pkey); //notasecret
        $scopes = array('https://www.googleapis.com/auth/drive');
        $credentials = new \Google_Auth_AssertionCredentials(
            $client_email,
            $scopes,
            $private_key,
            'notasecret',                                 // Default P12 password
            'http://oauth.net/grant_type/jwt/1.0/bearer', // Default grant type
            $user_to_impersonate
        );

        $client = new \Google_Client();
        $client->setAssertionCredentials($credentials);
        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion();
        }

        $service = new \Google_Service_Drive($client);

        $res = array(
            'client' => $client,
            'credentials' => $credentials,
            'service' => $service
        );

        return $res;
    }

    public function downloadFileToServer($author, $service, $fileId, $type, $path) {
        $file = null;
        try {
            $file = $service->files->get($fileId);
        } catch (Exception $e) {
            throw new IOException('Google API: Unable to get file by file id='.$fileId.". An error occurred: " . $e->getMessage());
        }

        if( $file ) {

            //check if file already exists by file id
            $documentDb = $this->em->getRepository('OlegUserdirectoryBundle:Document')->findOneByUniqueid($file->getId());
            if( $documentDb && $type != 'excel' ) {
                //echo "already exists file ID=".$file->getId()."<br>";
                return $documentDb;
            }

            $response = $this->downloadFile($service, $file, $type);
            //echo "response=".$response."<br>";
            if( !$response ) {
                throw new IOException('Error file response is empty: file id='.$fileId);
            }

            //create unique file name
            $currentDatetime = new \DateTime();
            $currentDatetimeTimestamp = $currentDatetime->getTimestamp();

            //$fileTitle = trim($file->getTitle());
            //$fileTitle = str_replace(" ","",$fileTitle);
            //$fileTitle = str_replace("-","_",$fileTitle);
            //$fileTitle = 'testfile.jpg';
            $fileExt = pathinfo($file->getTitle(), PATHINFO_EXTENSION);
            $fileExtStr = "";
            if( $fileExt ) {
                $fileExtStr = ".".$fileExt;
            }

            $fileUniqueName = $currentDatetimeTimestamp.'ID'.$file->getId().$fileExtStr;  //.'_title='.$fileTitle;
            //echo "fileUniqueName=".$fileUniqueName."<br>";

            $filesize = $file->getFileSize();
            if( !$filesize ) {
                $filesize = mb_strlen($response) / 1024; //KBs,
            }

            $object = new Document($author);
            $object->setUniqueid($file->getId());
            $object->setOriginalname($file->getTitle());
            $object->setUniquename($fileUniqueName);
            $object->setUploadDirectory($path);
            $object->setSize($filesize);

            if( $type && $type == 'excel' ) {
                $fellappSpreadsheetType = $this->em->getRepository('OlegUserdirectoryBundle:DocumentTypeList')->findOneByName('Fellowship Application Spreadsheet');
            } else {
                $fellappSpreadsheetType = $this->em->getRepository('OlegUserdirectoryBundle:DocumentTypeList')->findOneByName('Fellowship Application Upload');
            }
            if( $fellappSpreadsheetType ) {
                $object->setType($fellappSpreadsheetType);
            }

            $this->em->persist($object);

            $root = $this->container->get('kernel')->getRootDir();
            //echo "root=".$root."<br>";
            //$fullpath = $this->get('kernel')->getRootDir() . '/../web/'.$path;
            $fullpath = $root . '/../web/'.$path;
            $target_file = $fullpath . "/" . $fileUniqueName;

            //$target_file = $fullpath . 'uploadtestfile.jpg';
            //echo "target_file=".$target_file."<br>";
            if( !file_exists($fullpath) ) {
                // 0600 - Read/write/execute for owner, nothing for everybody else
                mkdir($fullpath, 0700, true);
                chmod($fullpath, 0700);
            }

            file_put_contents($target_file, $response);

            return $object;
        }

        return null;
    }


    /**
     * Download a file's content.
     *
     * @param Google_Servie_Drive $service Drive API service instance.
     * @param Google_Servie_Drive_DriveFile $file Drive File instance.
     * @return String The file's content if successful, null otherwise.
     */
    function downloadFile($service, $file, $type=null) {
        if( $type && $type == 'excel' ) {
            $downloadUrl = $file->getExportLinks()['text/csv'];
        } else {
            $downloadUrl = $file->getDownloadUrl();
        }
        //echo "downloadUrl=".$downloadUrl."<br>";
        if ($downloadUrl) {
            $request = new \Google_Http_Request($downloadUrl, 'GET', null, null);
            $httpRequest = $service->getClient()->getAuth()->authenticatedRequest($request);
            //echo "res code=".$httpRequest->getResponseHttpCode()."<br>";
            if ($httpRequest->getResponseHttpCode() == 200) {
                return $httpRequest->getResponseBody();
            } else {
                // An error occurred.
                return null;
            }
        } else {
            // The file doesn't have any content stored on Drive.
            return null;
        }
    }





    /////////////// populate methods /////////////////
    public function populateSpreadsheet( $inputFileName ) {

        //echo "inputFileName=".$inputFileName."<br>";
        $logger = $this->container->get('logger');
        ini_set('max_execution_time', 3000); //30000 seconds = 50 minutes

        $service = $this->getGoogleService();
        if( !$service ) {
            $event = "Google API service failed!";
            $logger->warning($event);
            $this->sendEmailToSystemEmail($event, $event);
            return -1;
        }

        $uploadPath = $this->uploadDir.'/FellowshipApplicantUploads';

        try {
            $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
            $event = 'Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage();
            $this->sendEmailToSystemEmail($event, $event);
            throw new IOException($event);
        }

        //$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        //var_dump($sheetData);

        $fellappUtil = $this->container->get('fellapp_util');
        $em = $this->em;
        $default_time_zone = $this->container->getParameter('default_time_zone');
        $emailUtil = $this->container->get('user_mailer_utility');
        $userUtil = new UserUtil();

        $userSecUtil = $this->container->get('user_security_utility');
        $userkeytype = $userSecUtil->getUsernameType('local-user');
        if( !$userkeytype ) {
            throw new EntityNotFoundException('Unable to find local user keytype');
        }

        $employmentType = $em->getRepository('OlegUserdirectoryBundle:EmploymentType')->findOneByName("Pathology Fellowship Applicant");
        if( !$employmentType ) {
            throw new EntityNotFoundException('Unable to find entity by name='."Pathology Fellowship Applicant");
        }
        $presentLocationType = $em->getRepository('OlegUserdirectoryBundle:LocationTypeList')->findOneByName("Present Address");
        if( !$presentLocationType ) {
            throw new EntityNotFoundException('Unable to find entity by name='."Present Address");
        }
        $permanentLocationType = $em->getRepository('OlegUserdirectoryBundle:LocationTypeList')->findOneByName("Permanent Address");
        if( !$permanentLocationType ) {
            throw new EntityNotFoundException('Unable to find entity by name='."Permanent Address");
        }
        $workLocationType = $em->getRepository('OlegUserdirectoryBundle:LocationTypeList')->findOneByName("Work Address");
        if( !$workLocationType ) {
            throw new EntityNotFoundException('Unable to find entity by name='."Work Address");
        }

        $activeStatus = $em->getRepository('OlegFellAppBundle:FellAppStatus')->findOneByName("active");
        if( !$activeStatus ) {
            throw new EntityNotFoundException('Unable to find entity by name='."active");
        }


        ////////////// add system user /////////////////
        $systemUser = $userSecUtil->findSystemUser();
        ////////////// end of add system user /////////////////

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $headers = $rowData = $sheet->rangeToArray('A' . 1 . ':' . $highestColumn . 1,
            NULL,
            TRUE,
            FALSE);
        //print_r($headers);

        $count = 0;

        //for each user in excel
        for ($row = 3; $row <= $highestRow; $row++){

            
    
            
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);

            //print_r($rowData);



            //$googleFormId = $rowData[0][0];
            $googleFormId = $this->getValueByHeaderName('ID',$rowData,$headers);
            
            try {

    //            //reopen em after DBALException
    //            if( !$em->isOpen() ) {
    //                echo 'em is closed; ID=' . $googleFormId."<br>";
    //                $em = $em->create(
    //                $em->getConnection(), $em->getConfiguration());
    //                $this->em = $em; 
    //                // reset the EM and all aias
    ////                $container = $this->container;
    ////                $container->set('doctrine.orm.entity_manager', null);
    ////                $container->set('doctrine.orm.default_entity_manager', null);
    ////                // get a fresh EM
    ////                $em = $this->container->getDoctrine()->getManager();
    ////                $this->em = $em;
    //            }


    //            if( !$em->isOpen() ) {
    //                exit('em is still closed; ID=' . $googleFormId);            
    //            }

                //echo "row=".$row.": id=".$googleFormId."<br>";

                $googleForm = $em->getRepository('OlegFellAppBundle:FellowshipApplication')->findOneByGoogleFormId($googleFormId);
                if( $googleForm ) {
                    continue; //skip this fell application, because it already exists in DB
                }


                $email = $this->getValueByHeaderName('email',$rowData,$headers);
                $lastName = $this->getValueByHeaderName('lastName',$rowData,$headers);
                $firstName = $this->getValueByHeaderName('firstName',$rowData,$headers);
                $middleName = $this->getValueByHeaderName('middleName',$rowData,$headers);

                $lastNameCap = $this->capitalizeIfNotAllCapital($lastName);
                $firstNameCap = $this->capitalizeIfNotAllCapital($firstName);
                $middleNameCap = $this->capitalizeIfNotAllCapital($middleName);

                $lastNameCap = preg_replace('/\s+/', '_', $lastNameCap);
                $firstNameCap = preg_replace('/\s+/', '_', $firstNameCap);

                //Last Name + First Name + Email
                $username = $lastNameCap."_".$firstNameCap."_".$email;

                $displayName = $firstName." ".$lastName;
                if( $middleName ) {
                    $displayName = $firstName." ".$middleName." ".$lastName;
                }

                //create logger which must be deleted on successefull creation of application
                $eventAttempt = "Attempt of creating Fellowship Applicant ".$displayName." with unique Google Applicant ID=".$googleFormId;
                $eventLogAttempt =  $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$eventAttempt,$systemUser,null,null,'Fellowship Application Creation Failed');


                //check if the user already exists in DB by $googleFormId
                $user = $em->getRepository('OlegUserdirectoryBundle:User')->findOneByPrimaryPublicUserId($username);

                if( !$user ) {
                    //create excel user
                    $addobjects = false;
                    $user = new User($addobjects);
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
                    $user->setMiddleName($middleName);
                    $user->setDisplayName($displayName);
                    $user->setPassword("");
                    $user->setCreatedby('googleapi');
                    $user->getPreferences()->setTimezone($default_time_zone);
                    $user->setLocked(true);

                    //Pathology Fellowship Applicant in EmploymentStatus
                    $employmentStatus = new EmploymentStatus($systemUser);
                    $employmentStatus->setEmploymentType($employmentType);
                    $user->addEmploymentStatus($employmentStatus);
                }

                //create new Fellowship Applicantion
                $fellowshipApplication = new FellowshipApplication($systemUser);
                $fellowshipApplication->setAppStatus($activeStatus);
                $fellowshipApplication->setGoogleFormId($googleFormId);
                $user->addFellowshipApplication($fellowshipApplication);

                //timestamp
                $fellowshipApplication->setTimestamp($this->transformDatestrToDate($this->getValueByHeaderName('timestamp',$rowData,$headers)));

                //fellowshipType
                $fellowshipType = $this->getValueByHeaderName('fellowshipType',$rowData,$headers);
                if( $fellowshipType ) {
                    $fellowshipType = trim($fellowshipType);
                    $fellowshipType = $this->capitalizeIfNotAllCapital($fellowshipType);
                    $transformer = new GenericTreeTransformer($em, $systemUser, 'FellowshipSubspecialty');
                    $fellowshipTypeEntity = $transformer->reverseTransform($fellowshipType);
                    $fellowshipApplication->setFellowshipSubspecialty($fellowshipTypeEntity);
                }

                //institution "Pathology Fellowship Programs"
                $wcmc = $em->getRepository('OlegUserdirectoryBundle:Institution')->findOneByAbbreviation("WCMC");
                $instPathologyFellowshipProgram = $em->getRepository('OlegUserdirectoryBundle:Institution')->findNodeByNameAndRoot($wcmc->getId(),"Pathology Fellowship Programs");
                if( !$instPathologyFellowshipProgram ) {
                    throw new EntityNotFoundException('Unable to find Institution by name='."Pathology Fellowship Programs");
                }
                $fellowshipApplication->setInstitution($instPathologyFellowshipProgram);

                //trainingPeriodStart
                $fellowshipApplication->setStartDate($this->transformDatestrToDate($this->getValueByHeaderName('trainingPeriodStart',$rowData,$headers)));

                //trainingPeriodEnd
                $fellowshipApplication->setEndDate($this->transformDatestrToDate($this->getValueByHeaderName('trainingPeriodEnd',$rowData,$headers)));

                //uploadedPhotoUrl
                $uploadedPhotoUrl = $this->getValueByHeaderName('uploadedPhotoUrl',$rowData,$headers);
                $uploadedPhotoId = $this->getFileIdByUrl( $uploadedPhotoUrl );
                if( $uploadedPhotoId ) {
                    $uploadedPhotoDb = $fellappUtil->downloadFileToServer($systemUser, $service, $uploadedPhotoId, null, $uploadPath);
                    if( !$uploadedPhotoDb ) {
                        throw new IOException('Unable to download file to server: uploadedPhotoUrl='.$uploadedPhotoUrl.', fileDB='.$uploadedPhotoDb);
                    }
                    //$user->setAvatar($uploadedPhotoDb); //set this file as Avatar
                    $fellowshipApplication->addAvatar($uploadedPhotoDb);
                }

                //uploadedCVUrl
                $uploadedCVUrl = $this->getValueByHeaderName('uploadedCVUrl',$rowData,$headers);
                $uploadedCVUrlId = $this->getFileIdByUrl( $uploadedCVUrl );
                if( $uploadedCVUrlId ) {
                    $uploadedCVUrlDb = $fellappUtil->downloadFileToServer($systemUser, $service, $uploadedCVUrlId, null, $uploadPath);
                    if( !$uploadedCVUrlDb ) {
                        throw new IOException('Unable to download file to server: uploadedCVUrl='.$uploadedCVUrl.', fileDB='.$uploadedCVUrlDb);
                    }
                    $fellowshipApplication->addCv($uploadedCVUrlDb);
                }

                //uploadedCoverLetterUrl
                $uploadedCoverLetterUrl = $this->getValueByHeaderName('uploadedCoverLetterUrl',$rowData,$headers);
                $uploadedCoverLetterUrlId = $this->getFileIdByUrl( $uploadedCoverLetterUrl );
                if( $uploadedCoverLetterUrlId ) {
                    $uploadedCoverLetterUrlDb = $fellappUtil->downloadFileToServer($systemUser, $service, $uploadedCoverLetterUrlId, null, $uploadPath);
                    if( !$uploadedCoverLetterUrlDb ) {
                        throw new IOException('Unable to download file to server: uploadedCoverLetterUrl='.$uploadedCoverLetterUrl.', fileDB='.$uploadedCoverLetterUrlDb);
                    }
                    $fellowshipApplication->addCoverLetter($uploadedCoverLetterUrlDb);
                }

                $examination = new Examination($systemUser);
                //$user->getCredentials()->addExamination($examination);
                $fellowshipApplication->addExamination($examination);
                //uploadedUSMLEScoresUrl
                $uploadedUSMLEScoresUrl = $this->getValueByHeaderName('uploadedUSMLEScoresUrl',$rowData,$headers);
                $uploadedUSMLEScoresUrlId = $this->getFileIdByUrl( $uploadedUSMLEScoresUrl );
                if( $uploadedUSMLEScoresUrlId ) {
                    $uploadedUSMLEScoresUrlDb = $fellappUtil->downloadFileToServer($systemUser, $service, $uploadedUSMLEScoresUrlId, null, $uploadPath);
                    if( !$uploadedUSMLEScoresUrlDb ) {
                        throw new IOException('Unable to download file to server: uploadedUSMLEScoresUrl='.$uploadedUSMLEScoresUrl.', fileDB='.$uploadedUSMLEScoresUrlDb);
                    }
                    $examination->addScore($uploadedUSMLEScoresUrlDb);
                }

                //presentAddress
                $presentLocation = new Location($systemUser);
                $presentLocation->setName('Fellowship Applicant Present Address');
                $presentLocation->addLocationType($presentLocationType);
                $geoLocation = $this->createGeoLocation($em,$systemUser,'presentAddress',$rowData,$headers);
                if( $geoLocation ) {
                    $presentLocation->setGeoLocation($geoLocation);
                }
                $user->addLocation($presentLocation);
                $fellowshipApplication->addLocation($presentLocation);

                //telephoneHome
                //telephoneMobile
                //telephoneFax
                $presentLocation->setPhone($this->getValueByHeaderName('telephoneHome',$rowData,$headers)."");
                $presentLocation->setMobile($this->getValueByHeaderName('telephoneMobile',$rowData,$headers)."");
                $presentLocation->setFax($this->getValueByHeaderName('telephoneFax',$rowData,$headers)."");

                //permanentAddress
                $permanentLocation = new Location($systemUser);
                $permanentLocation->setName('Fellowship Applicant Permanent Address');
                $permanentLocation->addLocationType($permanentLocationType);
                $geoLocation = $this->createGeoLocation($em,$systemUser,'permanentAddress',$rowData,$headers);
                if( $geoLocation ) {
                    $permanentLocation->setGeoLocation($geoLocation);
                }
                $user->addLocation($permanentLocation);
                $fellowshipApplication->addLocation($permanentLocation);

                //telephoneWork
                $telephoneWork = $this->getValueByHeaderName('telephoneWork',$rowData,$headers);
                if( $telephoneWork ) {
                    $workLocation = new Location($systemUser);
                    $workLocation->setName('Fellowship Applicant Work Address');
                    $workLocation->addLocationType($workLocationType);
                    $workLocation->setPhone($telephoneWork."");
                    $user->addLocation($workLocation);
                    $fellowshipApplication->addLocation($workLocation);
                }


                $citizenship = new Citizenship($systemUser);
                //$user->getCredentials()->addCitizenship($citizenship);
                $fellowshipApplication->addCitizenship($citizenship);
                //visaStatus
                $citizenship->setVisa($this->getValueByHeaderName('visaStatus',$rowData,$headers));
                //citizenshipCountry
                $citizenshipCountry = $this->getValueByHeaderName('citizenshipCountry',$rowData,$headers);
                if( $citizenshipCountry ) {
                    $citizenshipCountry = trim($citizenshipCountry);
                    $transformer = new GenericTreeTransformer($em, $systemUser, 'Countries');
                    $citizenshipCountryEntity = $transformer->reverseTransform($citizenshipCountry);
                    $citizenship->setCountry($citizenshipCountryEntity);
                }

                //undergraduate
                $this->createFellAppTraining($em,$fellowshipApplication,$systemUser,"undergraduateSchool",$rowData,$headers,1);

                //graduate
                $this->createFellAppTraining($em,$fellowshipApplication,$systemUser,"graduateSchool",$rowData,$headers,2);

                //medical
                $this->createFellAppTraining($em,$fellowshipApplication,$systemUser,"medicalSchool",$rowData,$headers,3);

                //residency: residencyStart	residencyEnd	residencyName	residencyArea
                $this->createFellAppTraining($em,$fellowshipApplication,$systemUser,"residency",$rowData,$headers,4);

                //gme1: gme1Start, gme1End, gme1Name, gme1Area => Major
                $this->createFellAppTraining($em,$fellowshipApplication,$systemUser,"gme1",$rowData,$headers,5);

                //gme2: gme2Start, gme2End, gme2Name, gme2Area => Major
                $this->createFellAppTraining($em,$fellowshipApplication,$systemUser,"gme2",$rowData,$headers,6);

                //otherExperience1Start	otherExperience1End	otherExperience1Name=>Major
                $this->createFellAppTraining($em,$fellowshipApplication,$systemUser,"otherExperience1",$rowData,$headers,7);

                //otherExperience2Start	otherExperience2End	otherExperience2Name=>Major
                $this->createFellAppTraining($em,$fellowshipApplication,$systemUser,"otherExperience2",$rowData,$headers,8);

                //otherExperience3Start	otherExperience3End	otherExperience3Name=>Major
                $this->createFellAppTraining($em,$fellowshipApplication,$systemUser,"otherExperience3",$rowData,$headers,9);

                //USMLEStep1DatePassed	USMLEStep1Score
                $examination->setUSMLEStep1DatePassed($this->transformDatestrToDate($this->getValueByHeaderName('USMLEStep1DatePassed',$rowData,$headers)));
                $examination->setUSMLEStep1Score($this->getValueByHeaderName('USMLEStep1Score',$rowData,$headers));
                $examination->setUSMLEStep1Percentile($this->getValueByHeaderName('USMLEStep1Percentile',$rowData,$headers));

                //USMLEStep2CKDatePassed	USMLEStep2CKScore	USMLEStep2CSDatePassed	USMLEStep2CSScore
                $examination->setUSMLEStep2CKDatePassed($this->transformDatestrToDate($this->getValueByHeaderName('USMLEStep2CKDatePassed',$rowData,$headers)));
                $examination->setUSMLEStep2CKScore($this->getValueByHeaderName('USMLEStep2CKScore',$rowData,$headers));
                $examination->setUSMLEStep2CKPercentile($this->getValueByHeaderName('USMLEStep2CKPercentile',$rowData,$headers));
                $examination->setUSMLEStep2CSDatePassed($this->transformDatestrToDate($this->getValueByHeaderName('USMLEStep2CSDatePassed',$rowData,$headers)));
                $examination->setUSMLEStep2CSScore($this->getValueByHeaderName('USMLEStep2CSScore',$rowData,$headers));
                $examination->setUSMLEStep2CSPercentile($this->getValueByHeaderName('USMLEStep2CSPercentile',$rowData,$headers));

                //USMLEStep3DatePassed	USMLEStep3Score
                $examination->setUSMLEStep3DatePassed($this->transformDatestrToDate($this->getValueByHeaderName('USMLEStep3DatePassed',$rowData,$headers)));
                $examination->setUSMLEStep3Score($this->getValueByHeaderName('USMLEStep3Score',$rowData,$headers));
                $examination->setUSMLEStep3Percentile($this->getValueByHeaderName('USMLEStep3Percentile',$rowData,$headers));

                //ECFMGCertificate
                $ECFMGCertificateStr = $this->getValueByHeaderName('ECFMGCertificate',$rowData,$headers);
                $ECFMGCertificate = false;
                if( $ECFMGCertificateStr == 'Yes' ) {
                    $ECFMGCertificate = true;
                }
                $examination->setECFMGCertificate($ECFMGCertificate);

                //ECFMGCertificateNumber	ECFMGCertificateDate
                $examination->setECFMGCertificateNumber($this->getValueByHeaderName('ECFMGCertificateNumber',$rowData,$headers));
                $examination->setECFMGCertificateDate($this->transformDatestrToDate($this->getValueByHeaderName('ECFMGCertificateDate',$rowData,$headers)));

                //COMLEXLevel1DatePassed	COMLEXLevel1Score	COMLEXLevel2DatePassed	COMLEXLevel2Score	COMLEXLevel3DatePassed	COMLEXLevel3Score
                $examination->setCOMLEXLevel1Score($this->getValueByHeaderName('COMLEXLevel1Score',$rowData,$headers));
                $examination->setCOMLEXLevel1Percentile($this->getValueByHeaderName('COMLEXLevel1Percentile',$rowData,$headers));
                $examination->setCOMLEXLevel1DatePassed($this->transformDatestrToDate($this->getValueByHeaderName('COMLEXLevel1DatePassed',$rowData,$headers)));
                $examination->setCOMLEXLevel2Score($this->getValueByHeaderName('COMLEXLevel2Score',$rowData,$headers));
                $examination->setCOMLEXLevel2Percentile($this->getValueByHeaderName('COMLEXLevel2Percentile',$rowData,$headers));
                $examination->setCOMLEXLevel2DatePassed($this->transformDatestrToDate($this->getValueByHeaderName('COMLEXLevel2DatePassed',$rowData,$headers)));
                $examination->setCOMLEXLevel3Score($this->getValueByHeaderName('COMLEXLevel3Score',$rowData,$headers));
                $examination->setCOMLEXLevel3Percentile($this->getValueByHeaderName('COMLEXLevel3Percentile',$rowData,$headers));
                $examination->setCOMLEXLevel3DatePassed($this->transformDatestrToDate($this->getValueByHeaderName('COMLEXLevel3DatePassed',$rowData,$headers)));

                //medicalLicensure1Country	medicalLicensure1State	medicalLicensure1DateIssued	medicalLicensure1Number	medicalLicensure1Active
                $this->createFellAppMedicalLicense($em,$fellowshipApplication,$systemUser,"medicalLicensure1",$rowData,$headers);

                //medicalLicensure2
                $this->createFellAppMedicalLicense($em,$fellowshipApplication,$systemUser,"medicalLicensure2",$rowData,$headers);

                //suspendedLicensure
                $fellowshipApplication->setReprimand($this->getValueByHeaderName('suspendedLicensure',$rowData,$headers));
                //uploadedReprimandExplanationUrl
                $uploadedReprimandExplanationUrl = $this->getValueByHeaderName('uploadedReprimandExplanationUrl',$rowData,$headers);
                $uploadedReprimandExplanationUrlId = $this->getFileIdByUrl( $uploadedReprimandExplanationUrl );
                if( $uploadedReprimandExplanationUrlId ) {
                    $uploadedReprimandExplanationUrlDb = $fellappUtil->downloadFileToServer($systemUser, $service, $uploadedReprimandExplanationUrlId, null, $uploadPath);
                    if( !$uploadedReprimandExplanationUrlDb ) {
                        throw new IOException('Unable to download file to server: uploadedReprimandExplanationUrl='.$uploadedReprimandExplanationUrl.', fileID='.$uploadedReprimandExplanationUrlDb->getId());
                    }
                    $fellowshipApplication->addReprimandDocument($uploadedReprimandExplanationUrlDb);
                }

                //legalSuit
                $fellowshipApplication->setLawsuit($this->getValueByHeaderName('legalSuit',$rowData,$headers));
                //uploadedLegalExplanationUrl
                $uploadedLegalExplanationUrl = $this->getValueByHeaderName('uploadedLegalExplanationUrl',$rowData,$headers);
                $uploadedLegalExplanationUrlId = $this->getFileIdByUrl( $uploadedLegalExplanationUrl );
                if( $uploadedLegalExplanationUrlId ) {
                    $uploadedLegalExplanationUrlDb = $fellappUtil->downloadFileToServer($systemUser, $service, $uploadedLegalExplanationUrlId, null, $uploadPath);
                    if( !$uploadedLegalExplanationUrlDb ) {
                        throw new IOException('Unable to download file to server: uploadedLegalExplanationUrl='.$uploadedLegalExplanationUrl.', fileID='.$uploadedLegalExplanationUrlDb->getId());
                    }
                    $fellowshipApplication->addReprimandDocument($uploadedLegalExplanationUrlDb);
                }

                //boardCertification1Board	boardCertification1Area	boardCertification1Date
                $this->createFellAppBoardCertification($em,$fellowshipApplication,$systemUser,"boardCertification1",$rowData,$headers);
                //boardCertification2
                $this->createFellAppBoardCertification($em,$fellowshipApplication,$systemUser,"boardCertification2",$rowData,$headers);
                //boardCertification3
                $this->createFellAppBoardCertification($em,$fellowshipApplication,$systemUser,"boardCertification3",$rowData,$headers);

                //recommendation1Name	recommendation1Title	recommendation1Institution	recommendation1AddressStreet1	recommendation1AddressStreet2	recommendation1AddressCity	recommendation1AddressState	recommendation1AddressZip	recommendation1AddressCountry
                $ref1 = $this->createFellAppReference($em,$systemUser,'recommendation1',$rowData,$headers);
                if( $ref1 ) {
                    $fellowshipApplication->addReference($ref1);
                }
                $ref2 = $this->createFellAppReference($em,$systemUser,'recommendation2',$rowData,$headers);
                if( $ref2 ) {
                    $fellowshipApplication->addReference($ref2);
                }
                $ref3 = $this->createFellAppReference($em,$systemUser,'recommendation3',$rowData,$headers);
                if( $ref3 ) {
                    $fellowshipApplication->addReference($ref3);
                }
                $ref4 = $this->createFellAppReference($em,$systemUser,'recommendation4',$rowData,$headers);
                if( $ref4 ) {
                    $fellowshipApplication->addReference($ref4);
                }

                //honors
                $fellowshipApplication->setHonors($this->getValueByHeaderName('honors',$rowData,$headers));
                //publications
                $fellowshipApplication->setPublications($this->getValueByHeaderName('publications',$rowData,$headers));
                //memberships
                $fellowshipApplication->setMemberships($this->getValueByHeaderName('memberships',$rowData,$headers));

                //signatureName
                $fellowshipApplication->setSignatureName($this->getValueByHeaderName('signatureName',$rowData,$headers));
                //signatureDate
                $signatureDate = $this->transformDatestrToDate($this->getValueByHeaderName('signatureDate',$rowData,$headers));
                $fellowshipApplication->setSignatureDate($signatureDate);

                //getFellowshipSubspecialty
                if( !$fellowshipApplication->getFellowshipSubspecialty() ) { //getSignatureName() - not reliable - some applicants managed to submit the form without signature
                    $event = "Error: Fellowship Type is null after populating Fellowship Applicant " . $displayName . " with Google Applicant ID=".$googleFormId."; Application ID " . $fellowshipApplication->getId();
                    $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$systemUser,$fellowshipApplication,null,'Fellowship Application Creation Failed');
                    $logger->error($event);

                    //send email                   
                    $userSecUtil = $this->container->get('user_security_utility');
                    $emails = $userSecUtil->getUserEmailsByRole($this->container->getParameter('fellapp.sitename'),"Administrator");
                    $ccs = $userSecUtil->getUserEmailsByRole($this->container->getParameter('fellapp.sitename'),"Platform Administrator");
                    if( !$emails ) {
                        $emails = $ccs;
                        $ccs = null;
                    }
                    $emailUtil->sendEmail( $emails, "Failed to create fellowship applicant with unique Google Applicant ID=".$googleFormId, $event, $ccs );
                    $this->sendEmailToSystemEmail("Failed to create fellowship applicant with unique Google Applicant ID=".$googleFormId, $event);
                }

                //exit('end applicant');

                $em->persist($user);
                $em->flush();

                //everything looks fine => remove creation attempt log
                $em->remove($eventLogAttempt);
                $em->flush();

                $event = "Populated fellowship applicant " . $displayName . "; Application ID " . $fellowshipApplication->getId();
                $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$systemUser,$fellowshipApplication,null,'Fellowship Application Created');

                //add application pdf generation to queue
                $fellappRepGen = $this->container->get('fellapp_reportgenerator');
                $fellappRepGen->addFellAppReportToQueue( $fellowshipApplication->getId() );

                $logger->notice($event);
                
                //send confirmation email to this applicant for prod server
                $environment = $userUtil->getSiteSetting($this->em,'environment');
                if( $environment == "live" ) {
                    $confirmationEmailFellApp = $userUtil->getSiteSetting($this->em,'confirmationEmailFellApp');
                    $confirmationSubjectFellApp = $userUtil->getSiteSetting($this->em,'confirmationSubjectFellApp');
                    $confirmationBodyFellApp = $userUtil->getSiteSetting($this->em,'confirmationBodyFellApp');
                    //$logger->notice("Before Send confirmation email to " . $email . " from " . $confirmationEmailFellApp);
                    if( $email && $confirmationEmailFellApp && $confirmationSubjectFellApp && $confirmationBodyFellApp ) {
                        $logger->notice("Send confirmation email to " . $email . " from " . $confirmationEmailFellApp);
                        $emailUtil->sendEmail( $email, $confirmationSubjectFellApp, $confirmationBodyFellApp, null, $confirmationEmailFellApp );
                    }
                }

                $count++;

                //exit( 'Test: end of fellowship applicant id='.$fellowshipApplication->getId() );

            } catch( \Doctrine\DBAL\DBALException $e ) {
            //} catch( \Exception $e ) {

        //        //reopen em after DBALException
        //        if( !$em->isOpen() ) {
        //            echo 'em is closed; ID=' . $googleFormId."<br>";
        //            $em = $em->create( $em->getConnection(), $em->getConfiguration() );
        //            $this->em = $em; 
        //            // reset the EM and all aias
        ////                $container = $this->container;
        ////                $container->set('doctrine.orm.entity_manager', null);
        ////                $container->set('doctrine.orm.default_entity_manager', null);
        ////                // get a fresh EM
        ////                $em = $this->container->getDoctrine()->getManager();
        ////                $this->em = $em;
        //        }

                //email                    
                //$emails = "oli2002@med.cornell.edu";
                //$userutil = new UserUtil();
                //$emails = $userutil->getSiteSetting($this->em,'siteEmail');
                //$event = "Error creating fellowship applicant with unique Google Applicant ID=".$googleFormId."; Exception=".$e->getMessage();
                //$emailUtil->sendEmail( $emails, "Failed to create fellowship applicant with unique Google Applicant ID=".$googleFormId, $event );
                $this->sendEmailToSystemEmail("Failed to create fellowship applicant with unique Google Applicant ID=".$googleFormId, $event);

                //logger
                $logger->error($event);

                //flash
                $this->container->get('session')->getFlashBag()->add(
                    'warning',
                    $event
                );             
            } //try/catch
            

        } //for


        //echo "count=".$count."<br>";
        //exit('end populate');

        return $count;
    }

    public function createFellAppReference($em,$author,$typeStr,$rowData,$headers) {

        //recommendation1Name	recommendation1Title	recommendation1Institution	recommendation1AddressStreet1
        //recommendation1AddressStreet2	recommendation1AddressCity	recommendation1AddressState	recommendation1AddressZip	recommendation1AddressCountry

        $recommendationFirstName = $this->getValueByHeaderName($typeStr."FirstName",$rowData,$headers);
        $recommendationLastName = $this->getValueByHeaderName($typeStr."LastName",$rowData,$headers);

        //echo "recommendationFirstName=".$recommendationFirstName."<br>";
        //echo "recommendationLastName=".$recommendationLastName."<br>";

        if( !$recommendationFirstName && !$recommendationLastName ) {
            //echo "no ref<br>";
            return null;
        }

        $reference = new Reference($author);

        //recommendation1FirstName
        $reference->setFirstName($recommendationFirstName);

        //recommendation1LastName
        $reference->setName($recommendationLastName);

        //recommendation1Degree
        $recommendationDegree = $this->getValueByHeaderName($typeStr."Degree",$rowData,$headers);
        if( $recommendationDegree ) {
            $reference->setDegree($recommendationDegree);
        }

        //recommendation1Title
        $recommendationTitle = $this->getValueByHeaderName($typeStr."Title",$rowData,$headers);
        if( $recommendationTitle ) {
            $reference->setTitle($recommendationTitle);
        }

        //recommendation1Email
        $recommendationEmail = $this->getValueByHeaderName($typeStr."Email",$rowData,$headers);
        if( $recommendationEmail ) {
            $reference->setEmail($recommendationEmail);
        }

        //recommendation1Phone
        $recommendationPhone = $this->getValueByHeaderName($typeStr."Phone",$rowData,$headers);
        if( $recommendationPhone ) {
            $reference->setPhone($recommendationPhone);
        }

        $instStr = $this->getValueByHeaderName($typeStr."Institution",$rowData,$headers);
        if( $instStr ) {
            $params = array('type'=>'Educational');
            $instStr = trim($instStr);
            $instStr = $this->capitalizeIfNotAllCapital($instStr);
            $transformer = new GenericTreeTransformer($em, $author, 'Institution', null, $params);
            $instEntity = $transformer->reverseTransform($instStr);
            $reference->setInstitution($instEntity);
        }

        $geoLocation = $this->createGeoLocation($em,$author,$typeStr."Address",$rowData,$headers);
        if( $geoLocation ) {
            $reference->setGeoLocation($geoLocation);
        }

        return $reference;
    }

    public function createGeoLocation($em,$author,$typeStr,$rowData,$headers) {

        $geoLocationStreet1 = $this->getValueByHeaderName($typeStr.'Street1',$rowData,$headers);
        $geoLocationStreet2 = $this->getValueByHeaderName($typeStr.'Street2',$rowData,$headers);
        //echo "geoLocationStreet1=".$geoLocationStreet1."<br>";
        //echo "geoLocationStreet2=".$geoLocationStreet2."<br>";

        if( !$geoLocationStreet1 && !$geoLocationStreet2 ) {
            //echo "no geoLocation<br>";
            return null;
        }

        $geoLocation = new GeoLocation();
        //popuilate geoLocation
        $geoLocation->setStreet1($this->getValueByHeaderName($typeStr.'Street1',$rowData,$headers));
        $geoLocation->setStreet2($this->getValueByHeaderName($typeStr.'Street2',$rowData,$headers));
        $geoLocation->setZip($this->getValueByHeaderName($typeStr.'Zip',$rowData,$headers));
        //presentAddressCity
        $presentAddressCity = $this->getValueByHeaderName($typeStr.'City',$rowData,$headers);
        if( $presentAddressCity ) {
            $presentAddressCity = trim($presentAddressCity);
            $transformer = new GenericTreeTransformer($em, $author, 'CityList');
            $presentAddressCityEntity = $transformer->reverseTransform($presentAddressCity);
            $geoLocation->setCity($presentAddressCityEntity);
        }
        //presentAddressState
        $presentAddressState = $this->getValueByHeaderName($typeStr.'State',$rowData,$headers);
        if( $presentAddressState ) {
            $presentAddressState = trim($presentAddressState);
            $transformer = new GenericTreeTransformer($em, $author, 'States');
            $presentAddressStateEntity = $transformer->reverseTransform($presentAddressState);
            $geoLocation->setState($presentAddressStateEntity);
        }
        //presentAddressCountry
        $presentAddressCountry = $this->getValueByHeaderName($typeStr.'Country',$rowData,$headers);
        if( $presentAddressCountry ) {
            $presentAddressCountry = trim($presentAddressCountry);
            $transformer = new GenericTreeTransformer($em, $author, 'Countries');
            $presentAddressCountryEntity = $transformer->reverseTransform($presentAddressCountry);
            $geoLocation->setCountry($presentAddressCountryEntity);
        }

        return $geoLocation;
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
            //$this->sendEmailToSystemEmail("Bad format of datetime string", $msg);

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
            $this->sendEmailToSystemEmail("Bad format of datetime string", $msg);
        }

        return $date;
    }
//    function valid_date($date) {
//        return (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date));
//    }

    public function createFellAppBoardCertification($em,$fellowshipApplication,$author,$typeStr,$rowData,$headers) {

        $boardCertificationIssueDate = $this->getValueByHeaderName($typeStr.'Date',$rowData,$headers);
        if( !$boardCertificationIssueDate ) {
            return null;
        }

        $boardCertification = new BoardCertification($author);
        $fellowshipApplication->addBoardCertification($boardCertification);
        $fellowshipApplication->getUser()->getCredentials()->addBoardCertification($boardCertification);

        //boardCertification1Board
        $boardCertificationBoard = $this->getValueByHeaderName($typeStr.'Board',$rowData,$headers);
        if( $boardCertificationBoard ) {
            $boardCertificationBoard = trim($boardCertificationBoard);
            $transformer = new GenericTreeTransformer($em, $author, 'CertifyingBoardOrganization');
            $CertifyingBoardOrganizationEntity = $transformer->reverseTransform($boardCertificationBoard);
            $boardCertification->setCertifyingBoardOrganization($CertifyingBoardOrganizationEntity);
        }

        //boardCertification1Area => BoardCertifiedSpecialties
        $boardCertificationArea = $this->getValueByHeaderName($typeStr.'Area',$rowData,$headers);
        if( $boardCertificationArea ) {
            $boardCertificationArea = trim($boardCertificationArea);
            $transformer = new GenericTreeTransformer($em, $author, 'BoardCertifiedSpecialties');
            $boardCertificationAreaEntity = $transformer->reverseTransform($boardCertificationArea);
            $boardCertification->setSpecialty($boardCertificationAreaEntity);
        }

        //boardCertification1Date
        $boardCertification->setIssueDate($this->transformDatestrToDate($boardCertificationIssueDate));

        return $boardCertification;
    }

    public function createFellAppMedicalLicense($em,$fellowshipApplication,$author,$typeStr,$rowData,$headers) {

        //medicalLicensure1Country	medicalLicensure1State	medicalLicensure1DateIssued	medicalLicensure1Number	medicalLicensure1Active

        $licenseNumber = $this->getValueByHeaderName($typeStr.'Number',$rowData,$headers);
        $licenseIssuedDate = $this->getValueByHeaderName($typeStr.'DateIssued',$rowData,$headers);

        if( !$licenseNumber && !$licenseIssuedDate ) {
            return null;
        }

        $license = new StateLicense($author);
        $fellowshipApplication->addStateLicense($license);
        $fellowshipApplication->getUser()->getCredentials()->addStateLicense($license);

        //medicalLicensure1DateIssued
        $license->setLicenseIssuedDate($this->transformDatestrToDate($licenseIssuedDate));

        //medicalLicensure1Active
        $medicalLicensureActive = $this->getValueByHeaderName($typeStr.'Active',$rowData,$headers);
        if( $medicalLicensureActive ) {
            $transformer = new GenericTreeTransformer($em, $author, 'MedicalLicenseStatus');
            $medicalLicensureActiveEntity = $transformer->reverseTransform($medicalLicensureActive);
            $license->setActive($medicalLicensureActiveEntity);
        }

        //medicalLicensure1Country
        $medicalLicensureCountry = $this->getValueByHeaderName($typeStr.'Country',$rowData,$headers);
        if( $medicalLicensureCountry ) {
            $medicalLicensureCountry = trim($medicalLicensureCountry);
            $transformer = new GenericTreeTransformer($em, $author, 'Countries');
            $medicalLicensureCountryEntity = $transformer->reverseTransform($medicalLicensureCountry);
            //echo "MedCountry=".$medicalLicensureCountryEntity.", ID+".$medicalLicensureCountryEntity->getId()."<br>";
            $license->setCountry($medicalLicensureCountryEntity);
        }

        //medicalLicensure1State
        $medicalLicensureState = $this->getValueByHeaderName($typeStr.'State',$rowData,$headers);
        if( $medicalLicensureState ) {
            $medicalLicensureState = trim($medicalLicensureState);
            $transformer = new GenericTreeTransformer($em, $author, 'States');
            $medicalLicensureStateEntity = $transformer->reverseTransform($medicalLicensureState);
            //echo "MedState=".$medicalLicensureStateEntity."<br>";
            $license->setState($medicalLicensureStateEntity);
        }

        //medicalLicensure1Number
        $license->setLicenseNumber($licenseNumber);

        return $license;
    }

    public function createFellAppTraining($em,$fellowshipApplication,$author,$typeStr,$rowData,$headers,$orderinlist) {

        //Start
        $trainingStart = $this->getValueByHeaderName($typeStr.'Start',$rowData,$headers);
        //End
        $trainingEnd = $this->getValueByHeaderName($typeStr.'End',$rowData,$headers);

        if( !$trainingStart && !$trainingEnd ) {
            return null;
        }

        $training = new Training($author);
        $training->setOrderinlist($orderinlist);
        $fellowshipApplication->addTraining($training);
        $fellowshipApplication->getUser()->addTraining($training);

        //set TrainingType
        if( $typeStr == 'undergraduateSchool' ) {
            $trainingType = $em->getRepository('OlegUserdirectoryBundle:TrainingTypeList')->findOneByName('Undergraduate');
            $training->setTrainingType($trainingType);
        }
        if( $typeStr == 'graduateSchool' ) {
            $trainingType = $em->getRepository('OlegUserdirectoryBundle:TrainingTypeList')->findOneByName('Graduate');
            $training->setTrainingType($trainingType);
        }
        if( strpos($typeStr,'medical') !== false ) {
            $trainingType = $em->getRepository('OlegUserdirectoryBundle:TrainingTypeList')->findOneByName('Medical');
            $training->setTrainingType($trainingType);
        }
        if( strpos($typeStr,'residency') !== false ) {
            $trainingType = $em->getRepository('OlegUserdirectoryBundle:TrainingTypeList')->findOneByName('Residency');
            $training->setTrainingType($trainingType);
        }
        if( strpos($typeStr,'gme') !== false ) {
            $trainingType = $em->getRepository('OlegUserdirectoryBundle:TrainingTypeList')->findOneByName('GME');
            $training->setTrainingType($trainingType);
        }
        if( strpos($typeStr,'other') !== false ) {
            $trainingType = $em->getRepository('OlegUserdirectoryBundle:TrainingTypeList')->findOneByName('Other');
            $training->setTrainingType($trainingType);
        }

        $majorMatchString = $typeStr.'Major';
        $nameMatchString = $typeStr.'Name';

        if( strpos($typeStr,'otherExperience') !== false ) {
            //otherExperience1Name => jobTitle
            $nameMatchString = null;
            $majorMatchString = null;
            $jobTitle = $this->getValueByHeaderName($typeStr.'Name',$rowData,$headers);
            $jobTitle = trim($jobTitle);
            $transformer = new GenericTreeTransformer($em, $author, 'JobTitleList');
            $jobTitleEntity = $transformer->reverseTransform($jobTitle);
            $training->setJobTitle($jobTitleEntity);
        }

        if( strpos($typeStr,'gme') !== false ) {
            //gme1Start	gme1End	gme1Name gme1Area
            //exception for Area: gmeArea => Major
            $majorMatchString = $typeStr.'Area';
        }

        if( strpos($typeStr,'residency') !== false ) {
            //residencyStart	residencyEnd	residencyName	residencyArea
            //residencyArea => ResidencySpecialty
            $residencyArea = $this->getValueByHeaderName('residencyArea',$rowData,$headers);
            $transformer = new GenericTreeTransformer($em, $author, 'ResidencySpecialty');
            $residencyArea = trim($residencyArea);
            $residencyAreaEntity = $transformer->reverseTransform($residencyArea);
            $training->setResidencySpecialty($residencyAreaEntity);
        }

        //Start
        $training->setStartDate($this->transformDatestrToDate($this->getValueByHeaderName($typeStr.'Start',$rowData,$headers)));

        //End
        $training->setCompletionDate($this->transformDatestrToDate($this->getValueByHeaderName($typeStr.'End',$rowData,$headers)));

        //City, Country, State
        $city = $this->getValueByHeaderName($typeStr.'City',$rowData,$headers);
        $country = $this->getValueByHeaderName($typeStr.'Country',$rowData,$headers);
        $state = $this->getValueByHeaderName($typeStr.'State',$rowData,$headers);

        if( $city || $country || $state ) {
            $trainingGeo = new GeoLocation();
            $training->setGeoLocation($trainingGeo);

            if( $city ) {
                $city = trim($city);
                $transformer = new GenericTreeTransformer($em, $author, 'CityList');
                $cityEntity = $transformer->reverseTransform($city);
                $trainingGeo->setCity($cityEntity);
            }

            if( $country ) {
                $country = trim($country);
                $transformer = new GenericTreeTransformer($em, $author, 'Countries');
                $countryEntity = $transformer->reverseTransform($country);
                $trainingGeo->setCountry($countryEntity);
            }

            if( $state ) {
                $state = trim($state);
                $transformer = new GenericTreeTransformer($em, $author, 'States');
                $stateEntity = $transformer->reverseTransform($state);
                $trainingGeo->setState($stateEntity);
            }
        }

        //Name
        $schoolName = $this->getValueByHeaderName($nameMatchString,$rowData,$headers);
        if( $schoolName ) {
            $params = array('type'=>'Educational');
            $schoolName = trim($schoolName);
            $schoolName = $this->capitalizeIfNotAllCapital($schoolName);
            $transformer = new GenericTreeTransformer($em, $author, 'Institution', null, $params);
            $schoolNameEntity = $transformer->reverseTransform($schoolName);
            $training->setInstitution($schoolNameEntity);
        }

        //Description
        $schoolDescription = $this->getValueByHeaderName($typeStr.'Description',$rowData,$headers);
        if( $schoolDescription ) {
            $schoolDescription = trim($schoolDescription);
            $training->setDescription($schoolDescription);
        }

        //Major
        $schoolMajor = $this->getValueByHeaderName($majorMatchString,$rowData,$headers);
        if( $schoolMajor ) {
            $schoolMajor = trim($schoolMajor);
            $transformer = new GenericTreeTransformer($em, $author, 'MajorTrainingList');
            $schoolMajorEntity = $transformer->reverseTransform($schoolMajor);
            $training->addMajor($schoolMajorEntity);
        }

        //Degree
        $schoolDegree = $this->getValueByHeaderName($typeStr.'Degree',$rowData,$headers);
        if( $schoolDegree ) {
            $schoolDegree = trim($schoolDegree);
            $transformer = new GenericTreeTransformer($em, $author, 'TrainingDegreeList');
            $schoolDegreeEntity = $transformer->reverseTransform($schoolDegree);
            $training->setDegree($schoolDegreeEntity);
        }

        return $training;
    }


    public function getValueByHeaderName($header, $row, $headers) {

        $res = null;

        if( !$header ) {
            return $res;
        }

        //echo "header=".$header."<br>";
        //print_r($headers);
        //print_r($row[0]);

        $key = array_search($header, $headers[0]);
        //echo "key=".$key."<br>";

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


    //parse url and get file id
    public function getFileIdByUrl( $url ) {
        if( !$url ) {
            return null;
        }
        //https://drive.google.com/a/pathologysystems.org/file/d/0B2FwyaXvFk1eSDQ0MkJKSjhLN1U/view?usp=drivesdk
        $urlArr = explode("/d/", $url);
        $urlSecond = $urlArr[1];
        $urlSecondArr = explode("/", $urlSecond);
        $fileId = $urlSecondArr[0];
        return $fileId;
    }






    //check for active access requests
    public function getActiveAccessReq() {
        if( !$this->sc->isGranted('ROLE_FELLAPP_COORDINATOR') ) {
            //exit('not granted ROLE_FELLAPP_COORDINATOR ???!!!'); //testing
            return null;
        } else {
            //exit('granted ROLE_FELLAPP_COORDINATOR !!!'); //testing
        }
        $userSecUtil = $this->container->get('user_security_utility');
        $accessreqs = $userSecUtil->getUserAccessRequestsByStatus($this->container->getParameter('fellapp.sitename'),AccessRequest::STATUS_ACTIVE);
        return $accessreqs;
    }

    public function getFellAppByStatusAndYear($status,$fellSubspecId,$year=null,$interviewer=null) {

        $repository = $this->em->getRepository('OlegFellAppBundle:FellowshipApplication');
        $dql =  $repository->createQueryBuilder("fellapp");
        $dql->select('fellapp');
        $dql->leftJoin("fellapp.appStatus", "appStatus");

        if( strpos($status, "-") !== false ) {
            $statusArr = explode("-", $status);
            $statusStr = $statusArr[0];
            $statusNot = $statusArr[1];
            if( $statusNot && $statusNot == 'not' ) {
                //'interviewee-not' is dummy status which is all statuses but not
                $dql->where("appStatus.name != '" . $statusStr . "'");
            }
        } else {
            $dql->where("appStatus.name = '" . $status . "'");
        }

        if( $fellSubspecId ) {
            $dql->leftJoin("fellapp.fellowshipSubspecialty","fellowshipSubspecialty");
            $dql->andWhere("fellowshipSubspecialty.id=".$fellSubspecId);
        }

        if( $year ) {
            $bottomDate = $year."-01-01";
            $topDate = $year."-12-31";
            $dql->andWhere("fellapp.startDate BETWEEN '" . $bottomDate . "'" . " AND " . "'" . $topDate . "'" );
        }

        if( $interviewer ) {
            $dql->leftJoin("fellapp.interviews", "interviews");
            $dql->leftJoin("interviews.interviewer", "interviewer");
            $dql->andWhere("interviewer.id=".$interviewer->getId());
        }

        //echo "dql=".$dql."<br>";

        $query = $this->em->createQuery($dql);
        $applicants = $query->getResult();

        return $applicants;
    }

//    public function getFellAppByUserAndStatusAndYear($subjectUser, $status,$fellSubspecId,$year=null) {
//
//        $repository = $this->em->getRepository('OlegFellAppBundle:FellowshipApplication');
//        $dql =  $repository->createQueryBuilder("fellapp");
//        $dql->select('fellapp');
//        $dql->leftJoin("fellapp.appStatus", "appStatus");
//        $dql->where("appStatus.name = '" . $status . "'");
//
//        if( $fellSubspecId ) {
//            $dql->leftJoin("fellapp.fellowshipSubspecialty","fellowshipSubspecialty");
//            $dql->andWhere("fellowshipSubspecialty.id=".$fellSubspecId);
//        }
//
//        if( $year ) {
//            $bottomDate = "01-01-".$year;
//            $topDate = "12-31-".$year;
//            $dql->andWhere("fellapp.startDate BETWEEN '" . $bottomDate . "'" . " AND " . "'" . $topDate . "'" );
//        }
//
//        if( $subjectUser ) {
//            $dql->leftJoin("fellapp.interviews", "interviews");
//            $dql->andWhere("interviews.interviewer=".$subjectUser);
//        }
//
//        $query = $this->em->createQuery($dql);
//        $applicants = $query->getResult();
//
//        return $applicants;
//    }

    //get fellowship types based on the user roles
    public function getFellowshipTypesByUser( $user ) {
        $em = $this->em;
        $userSecUtil = $this->container->get('user_security_utility');

        if( $userSecUtil->hasGlobalUserRole( "ROLE_FELLAPP_ADMIN", $user ) ) {
            return $this->getFellowshipTypesByInstitution();
        }

        $filterTypes = array();
        $filterTypeIds = array();

        foreach( $user->getRoles() as $rolename ) {
            $roleObject = $em->getRepository('OlegUserdirectoryBundle:Roles')->findOneByName($rolename);
            if( $roleObject ) {
                $fellowshipSubspecialty = $roleObject->getFellowshipSubspecialty();
                if( $fellowshipSubspecialty ) {
                    $filterTypes[$fellowshipSubspecialty->getId()] = $fellowshipSubspecialty->getName();
                    $filterTypeIds[] = $fellowshipSubspecialty->getId();
                }
            }
        }

//        if( count($filterTypes) > 1 ) {
//            $filterTypes[implode(";",$filterTypeIds)] = "ALL";
//        }

        //$filterTypes = array_reverse($filterTypes);

        return $filterTypes;
    }

    //get all fellowship application types using role
    public function getFellowshipTypesByInstitution($asEntities=false) {
        $em = $this->em;

        $mapper = array(
            'prefix' => 'Oleg',
            'bundleName' => 'UserdirectoryBundle',
            'className' => 'Institution'
        );

        $wcmc = $em->getRepository('OlegUserdirectoryBundle:Institution')->findOneByAbbreviation("WCMC");
        $pathology = $em->getRepository('OlegUserdirectoryBundle:Institution')->findByChildnameAndParent(
            "Pathology and Laboratory Medicine",
            $wcmc,
            $mapper
        );

        //get list of fellowship type with extra "ALL"
        $repository = $em->getRepository('OlegUserdirectoryBundle:FellowshipSubspecialty');
        $dql = $repository->createQueryBuilder('list');
        $dql->leftJoin("list.institution","institution");
        $dql->where("institution.id = ".$pathology->getId());
        $dql->orderBy("list.orderinlist","ASC");

        $query = $em->createQuery($dql);

        $fellTypes = $query->getResult();
        //echo "fellTypes count=".count($fellTypes)."<br>";

        if( $asEntities ) {
            return $fellTypes;
        }

        //add statuses
        foreach( $fellTypes as $type ) {
            //echo "type: id=".$status->getId().", name=".$status->getName()."<br>";
            $filterType[$type->getId()] = $type->getName();
        }

        return $filterType;
    }

//    public function getFellowshipTypesWithSpecials_OLD() {
//        $em = $this->em;
//
//        //get list of fellowship type with extra "ALL"
//        $repository = $em->getRepository('OlegUserdirectoryBundle:FellowshipSubspecialty');
//        $dql = $repository->createQueryBuilder('list');
//        //$dql->select("list.id as id, list.name as text")
//        $dql->leftJoin("list.parent","parent");
//        $dql->where("list.type = :typedef OR list.type = :typeadd");
//        $dql->andWhere("parent.name LIKE '%Pathology%' OR parent.name LIKE '%Clinical Molecular Genetics%' OR parent IS NULL");
//        //$dql->andWhere("parent.name LIKE '%Pathology%'");
//        $dql->orderBy("list.orderinlist","ASC");
//
//        $query = $em->createQuery($dql);
//
//        $query->setParameters( array(
//            'typedef' => 'default',
//            'typeadd' => 'user-added',
//            //'parentName' => 'Pathology'
//        ));
//
//        $fellTypes = $query->getResult();
//
//        //add special cases
////        $specials = array(
////            "ALL" => "ALL",
////        );
//
////        $filterType = array();
////        foreach( $specials as $key => $value ) {
////            $filterType[$key] = $value;
////        }
//
//        //add statuses
//        foreach( $fellTypes as $type ) {
//            //echo "type: id=".$status->getId().", name=".$status->getName()."<br>";
//            $filterType[$type->getId()] = $type->getName();
//        }
//
//        return $filterType;
//    }


    //check if the user can view this fellapp application
    public function hasFellappPermission( $user, $fellapp ) {

        $res = false;

        //if user is observer of this fellapp
        if( $fellapp->getObservers()->contains($user) ) {
            $res = true;
        }

        //echo "res=".$res."<br>";

        //if user has the same fellapp type as this fellapp
        if( $this->hasSameFellowshipTypeId($user, $fellapp->getFellowshipSubspecialty()->getId()) ) {
            $res = true;
        }

        //echo "res=".$res."<br>";
        //exit('1');

        return $res;
    }

    //check fellowship types based on the user roles
    public function hasSameFellowshipTypeId( $user, $felltypeid ) {
        $em = $this->em;
        $userSecUtil = $this->container->get('user_security_utility');

        if( $userSecUtil->hasGlobalUserRole( "ROLE_FELLAPP_ADMIN", $user ) ) {
            return true;
        }

        //echo "felltypeid=".$felltypeid."<br>";

        foreach( $user->getRoles() as $rolename ) {
            $roleObject = $em->getRepository('OlegUserdirectoryBundle:Roles')->findOneByName($rolename);
            if( $roleObject ) {
                $fellowshipSubspecialty = $roleObject->getFellowshipSubspecialty();
                if( $fellowshipSubspecialty ) {
                    if( $felltypeid == $fellowshipSubspecialty->getId() ) {
                        //it is safer to check also for fellowshipSubspecialty's institution is under roleObject's institution
                        if( $em->getRepository('OlegUserdirectoryBundle:Institution')->isNodeUnderParentnode( $roleObject->getInstitution(), $fellowshipSubspecialty->getInstitution() ) ) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    //get coordinator of given fellapp
    public function getCoordinatorsOfFellApp( $fellapp ) {

        if( !$fellapp ) {
            return null;
        }

        $em = $this->em;

        $fellowshipSubspecialty = $fellapp->getFellowshipSubspecialty();
        //echo "fellowshipSubspecialty=".$fellowshipSubspecialty."<br>";

        if( !$fellowshipSubspecialty ) {
            return null;
        }

        $coordinatorFellTypeRole = null;

        $roles = $em->getRepository('OlegUserdirectoryBundle:Roles')->findByFellowshipSubspecialty($fellowshipSubspecialty);
        foreach( $roles as $role ) {
            if( strpos($role,'_COORDINATOR_') !== false ) {
                $coordinatorFellTypeRole = $role;
                break;
            }
        }

        $users = $em->getRepository('OlegUserdirectoryBundle:User')->findUserByRole($coordinatorFellTypeRole);

        return $users;
    }

    public function getCoordinatorsOfFellAppEmails($fellapp) {

        $coordinators = $this->getCoordinatorsOfFellApp( $fellapp );

        $emails = array();
        if( $coordinators && count($coordinators) > 0 ) {
            foreach( $coordinators as $coordinator ) {
                $emails[] = $coordinator->getEmail();
            }
        }

        //echo "coordinator emails<br>";
        //print_r($emails);
        //exit('1');

        return $emails;
    }

    public function addDefaultInterviewers( $fellapp ) {

        $fellowshipSubspecialty = $fellapp->getFellowshipSubspecialty();

        foreach( $fellowshipSubspecialty->getInterviewers() as $interviewer ) {

            if( $this->isInterviewerExist($fellapp,$interviewer) == false ) {
                $interview = new Interview();
                $interview->setInterviewer($interviewer);
                $interview->setLocation($interviewer->getMainLocation());
                $interview->setInterviewDate($fellapp->getInterviewDate());
                $fellapp->addInterview($interview);
            }

        }

    }

    public function isInterviewerExist( $fellapp, $interviewer ) {
        foreach( $fellapp->getInterviews() as $interview ) {
            if( $interview->getInterviewer()->getId() == $interviewer->getId() ) {
                return true;
            }
        }
        return false;
    }


    function capitalizeIfNotAllCapital($s) {
        if( strlen(preg_replace('![^A-Z]+!', '', $s)) == strlen($s) ) {
            $s = ucfirst(strtolower($s));
        }
        return $s;
    }






    public function addEmptyFellAppFields($fellowshipApplication) {

        $em = $this->em;
        //$userSecUtil = $this->container->get('user_security_utility');
        //$systemUser = $userSecUtil->findSystemUser();
        $user = $fellowshipApplication->getUser();
        $author = $this->sc->getToken()->getUser();

        //Pathology Fellowship Applicant in EmploymentStatus
        $employmentType = $em->getRepository('OlegUserdirectoryBundle:EmploymentType')->findOneByName("Pathology Fellowship Applicant");
        if( !$employmentType ) {
            throw new EntityNotFoundException('Unable to find entity by name='."Pathology Fellowship Applicant");
        }
        if( count($user->getEmploymentStatus()) == 0 ) {
            $employmentStatus = new EmploymentStatus($author);
            $employmentStatus->setEmploymentType($employmentType);
            $user->addEmploymentStatus($employmentStatus);
        }

        //locations
        $this->addEmptyLocations($fellowshipApplication);

        //Education
        $this->addEmptyTrainings($fellowshipApplication);

        //National Boards: oleg_fellappbundle_fellowshipapplication_examinations_0_USMLEStep1DatePassed
        $this->addEmptyNationalBoards($fellowshipApplication);

        //Medical Licensure: oleg_fellappbundle_fellowshipapplication[stateLicenses][0][licenseNumber]
        $this->addEmptyStateLicenses($fellowshipApplication);

        //Board Certification
        $this->addEmptyBoardCertifications($fellowshipApplication);

        //References
        $this->addEmptyReferences($fellowshipApplication);

    }


    //oleg_fellappbundle_fellowshipapplication_references_0_name
    public function addEmptyReferences($fellowshipApplication) {

        $author = $this->sc->getToken()->getUser();
        $references = $fellowshipApplication->getReferences();
        $count = count($references);

        //must be 4
        for( $count; $count < 4; $count++  ) {

            $reference = new Reference($author);
            $fellowshipApplication->addReference($reference);

        }

    }

    public function addEmptyBoardCertifications($fellowshipApplication) {

        $author = $this->sc->getToken()->getUser();
        $boardCertifications = $fellowshipApplication->getBoardCertifications();
        $count = count($boardCertifications);

        //must be 3
        for( $count; $count < 3; $count++  ) {

            $boardCertification = new BoardCertification($author);
            $fellowshipApplication->addBoardCertification($boardCertification);
            $fellowshipApplication->getUser()->getCredentials()->addBoardCertification($boardCertification);

        }

    }

    //oleg_fellappbundle_fellowshipapplication[stateLicenses][0][licenseNumber]
    public function addEmptyStateLicenses($fellowshipApplication) {

        $author = $this->sc->getToken()->getUser();

        $stateLicenses = $fellowshipApplication->getStateLicenses();

        $count = count($stateLicenses);

        //must be 2
        for( $count; $count < 2; $count++  ) {

            $license = new StateLicense($author);
            $fellowshipApplication->addStateLicense($license);
            $fellowshipApplication->getUser()->getCredentials()->addStateLicense($license);

        }

    }

    public function addEmptyNationalBoards($fellowshipApplication) {

        $author = $this->sc->getToken()->getUser();

        $examinations = $fellowshipApplication->getExaminations();

        if( count($examinations) == 0 ) {
            $examination = new Examination($author);
            $fellowshipApplication->addExamination($examination);
        } else {
            //$examination = $examinations[0];
        }

    }


    public function addEmptyLocations($fellowshipApplication) {

        $this->addLocationByType($fellowshipApplication,"Present Address");
        $this->addLocationByType($fellowshipApplication,"Permanent Address");
        $this->addLocationByType($fellowshipApplication,"Work Address");

    }
    public function addLocationByType($fellowshipApplication,$typeName) {

        $user = $fellowshipApplication->getUser();

        $specificLocation = null;

        foreach( $user->getLocations() as $location ) {
            if( $location->hasLocationTypeName($typeName) ) {
                $specificLocation = $location;
                break;
            }
        }

        if( !$specificLocation ) {

            $locationType = $this->em->getRepository('OlegUserdirectoryBundle:LocationTypeList')->findOneByName($typeName);
            if( !$locationType ) {
                throw new EntityNotFoundException('Unable to find entity by name='.$typeName);
            }

            $specificLocation = new Location();
            $specificLocation->setName('Fellowship Applicant '.$typeName);
            $specificLocation->addLocationType($locationType);
            $user->addLocation($specificLocation);
            $fellowshipApplication->addLocation($specificLocation);
        }

    }

    public function addEmptyTrainings($fellowshipApplication) {

        //set TrainingType
        $this->addTrainingByType($fellowshipApplication,"Undergraduate",1);
        $this->addTrainingByType($fellowshipApplication,"Graduate",2);
        $this->addTrainingByType($fellowshipApplication,"Medical",3);
        $this->addTrainingByType($fellowshipApplication,"Residency",4);

        $maxNumber = 2;
        $this->addTrainingByType($fellowshipApplication,"GME",5,$maxNumber);
        //$this->addTrainingByType($fellowshipApplication,"GME",6,$maxNumber);

        $maxNumber = 3;
        $this->addTrainingByType($fellowshipApplication,"Other",7,$maxNumber);
        //$this->addTrainingByType($fellowshipApplication,"Other",8,$maxNumber);
        //$this->addTrainingByType($fellowshipApplication,"Other",9,$maxNumber);

    }
    public function addTrainingByType($fellowshipApplication,$typeName,$orderinlist,$maxNumber=1) {

        $user = $fellowshipApplication->getUser();

        $specificTraining = null;

        $trainings = $user->getTrainings();

        $count = 0;

        foreach( $trainings as $training ) {
            if( $training->getTrainingType()->getName()."" == $typeName ) {
                $count++;
            }
        }

        //add up to maxNumber
        for( $count; $count < $maxNumber; $count++ ) {
            //echo "maxNumber=".$maxNumber.", count=".$count."<br>";
            $this->addSingleTraining($fellowshipApplication,$typeName,$orderinlist);
        }

    }
    public function addSingleTraining($fellowshipApplication,$typeName,$orderinlist) {

        //echo "!!!!!!!!!! add single training with type=".$typeName."<br>";

        $author = $this->sc->getToken()->getUser();
        $training = new Training($author);
        $training->setOrderinlist($orderinlist);

        $trainingType = $this->em->getRepository('OlegUserdirectoryBundle:TrainingTypeList')->findOneByName($typeName);
        $training->setTrainingType($trainingType);

        //s2id_oleg_fellappbundle_fellowshipapplication_trainings_1_jobTitle
        if( $typeName == 'Other' ) {
            //otherExperience1Name => jobTitle
            //if( !$training->getJobTitle() ) {
                $jobTitleEntity = new JobTitleList();
                $training->setJobTitle($jobTitleEntity);
            //}
        }

        $fellowshipApplication->addTraining($training);
        $fellowshipApplication->getUser()->addTraining($training);

    }


    public function createApplicantListExcel( $fellappids ) {
        
        $author = $this->sc->getToken()->getUser();
        $transformer = new DateTimeToStringTransformer(null,null,'d/m/Y');
        
        $ea = new \PHPExcel(); // ea is short for Excel Application
               
        $ea->getProperties()
            ->setCreator($author."")
            ->setTitle('Fellowship Applicants')
            ->setLastModifiedBy($author."")
            ->setDescription('Fellowship Applicants list in Excel format')
            ->setSubject('PHP Excel manipulation')
            ->setKeywords('excel php office phpexcel lakers')
            ->setCategory('programming')
            ;
        
        $ews = $ea->getSheet(0);
        $ews->setTitle('Fellowship Applicants');
        
        //align all cells to left
        $style = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            )
        );
        $ews->getDefaultStyle()->applyFromArray($style);
        
        $ews->setCellValue('A1', 'ID'); // Sets cell 'a1' to value 'ID 
        $ews->setCellValue('B1', 'Last Name');
        $ews->setCellValue('C1', 'First Name');
        $ews->setCellValue('D1', 'Medical Degree');
        $ews->setCellValue('E1', 'Medical School');
        $ews->setCellValue('F1', 'Residency Institution');
        $ews->setCellValue('G1', 'References');
        $ews->setCellValue('H1', 'Interview Score');
        $ews->setCellValue('I1', 'Interview Date');
        
        $ews->setCellValue('J1', 'Interviewer');
        $ews->setCellValue('K1', 'Date');
        $ews->setCellValue('L1', 'Academic Rank');
        $ews->setCellValue('M1', 'Personality Rank');
        $ews->setCellValue('N1', 'Potential Rank');
        $ews->setCellValue('O1', 'Total Rank');
        $ews->setCellValue('P1', 'Language Proficiency');
        $ews->setCellValue('Q1', 'Comments');
        

        
        $row = 2;
        foreach( explode("-",$fellappids) as $fellappId ) {
        
            $fellapp = $this->em->getRepository('OlegFellAppBundle:FellowshipApplication')->find($fellappId);
            if( !$fellapp ) {
                continue;
            }
            
            //check if author can have access to view this applicant
            //user who has the same fell type can view or edit
            $fellappUtil = $this->container->get('fellapp_util');
            if( $fellappUtil->hasFellappPermission($author,$fellapp) == false ) {
                continue; //skip this applicant because the current user does not permission to view this applicant
            }
            
            $ews->setCellValue('A'.$row, $fellapp->getId());  
            $ews->setCellValue('B'.$row, $fellapp->getUser()->getLastNameUppercase());
            $ews->setCellValue('C'.$row, $fellapp->getUser()->getFirstNameUppercase());
            
            //Medical Degree
            $ews->setCellValue('D'.$row, $fellapp->getDegreeByTrainingTypeName('Medical'));
            
            //Medical School
            $ews->setCellValue('E'.$row, $fellapp->getSchoolByTrainingTypeName('Medical'));
            
            //Residency Institution
            $ews->setCellValue('F'.$row, $fellapp->getSchoolByTrainingTypeName('Residency'));
            
            //References
            $ews->setCellValue('G'.$row, $fellapp->getAllReferences());
            
            //Interview Score
            $totalScore = "";
            if( $fellapp->getInterviewScore() ) {
                $totalScore = $fellapp->getInterviewScore();
            }
            $ews->setCellValue('H'.$row, $totalScore );
	       
            //Interview Date                   
            $ews->setCellValue('I'.$row, $transformer->transform($fellapp->getInterviewDate()));
            
            $allTotalRanks = 0;
            
            foreach( $fellapp->getInterviews() as $interview ) {
            
                //Interviewer
                if( $interview->getInterviewer() ) {
                    $ews->setCellValue('J'.$row, $interview->getInterviewer()->getUsernameOptimal());
                }
                
                //Date
                $ews->setCellValue('K'.$row, $transformer->transform($interview->getInterviewDate()));
                
                //Academic Rank
                if( $interview->getAcademicRank() ) {
                    $ews->setCellValue('L'.$row, $interview->getAcademicRank()->getValue());
                }
                
                //Personality Rank
                if( $interview->getPersonalityRank() ) {
                    $ews->setCellValue('M'.$row, $interview->getPersonalityRank()->getValue());
                }
                
                //Potential Rank
                if( $interview->getPotentialRank() ) {
                    $ews->setCellValue('N'.$row, $interview->getPotentialRank()->getValue());
                }
                
                //Total Rank
                $ews->setCellValue('O'.$row, $interview->getTotalRank());
                $allTotalRanks = $allTotalRanks + $interview->getTotalRank();
                
                //Language Proficiency
                if( $interview->getLanguageProficiency() ) {
                    $ews->setCellValue('P'.$row, $interview->getLanguageProficiency()->getName());
                }
                
                //Comments
                $ews->setCellValue('Q'.$row, $interview->getComment());   
                
                $row++;
            
            } //for each interview
            
            //space in case if there is no interviewers 
            if( count($fellapp->getInterviews()) == 0 ) {
                $row++;
            }
            
            //All Total Ranks:           
            $ews->setCellValue('A'.$row, "All Total Ranks:");
            $ews->setCellValue('B'.$row, $allTotalRanks);
            
            //Avg Rank:
            $row++;
            $ews->setCellValue('A'.$row, "Avg Rank:");
            $ews->setCellValue('B'.$row, $totalScore);
            
            $row = $row + 2;
        }
        
        //exit("ids=".$fellappids);
        
        
        // Auto size columns for each worksheet
        \PHPExcel_Shared_Font::setAutoSizeMethod(\PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        foreach ($ea->getWorksheetIterator() as $worksheet) {

            $ea->setActiveSheetIndex($ea->getIndex($worksheet));

            $sheet = $ea->getActiveSheet();
            $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            /** @var PHPExcel_Cell $cell */
            foreach ($cellIterator as $cell) {
                $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
            }
        }
               
        
        return $ea;
    }


    public function createInterviewApplicantList( $fellappids ) {

        $author = $this->sc->getToken()->getUser();

        $fellapps = array();

        foreach( explode("-",$fellappids) as $fellappId ) {

            $fellapp = $this->em->getRepository('OlegFellAppBundle:FellowshipApplication')->find($fellappId);
            if( !$fellapp ) {
                continue;
            }

            //check if author can have access to view this applicant
            //user who has the same fell type can view or edit
            $fellappUtil = $this->container->get('fellapp_util');
            if( $fellappUtil->hasFellappPermission($author,$fellapp) == false ) {
                continue; //skip this applicant because the current user does not permission to view this applicant
            }

            //only include the people who have an interview date (not the status of the interviewer)
            if( !$fellapp->getInterviewDate() ) {
                continue;
            }

            $fellapps[] = $fellapp;
        }

        //exit("ids=".$fellappids);
        return $fellapps;
    }

    public function sendEmailToSystemEmail($subject, $message) {
        $emailUtil = $this->container->get('user_mailer_utility');
        $emailUtil->sendEmail( $this->systemEmail, $subject, $message );
    }

} 