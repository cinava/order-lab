<?php
/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 3/21/2016
 * Time: 10:43 AM
 */

namespace Oleg\FellAppBundle\Util;

//use https://github.com/asimlqt/php-google-spreadsheet-client/blob/master/README.md
//install:
//1) composer.phar install
//2) composer.phar update

//TODO: implement
// "Delete successfully imported applications from Google Drive",
// "deletion of rows from the spreadsheet on Google Drive upon successful import"
// "Automatically delete downloaded applications that are older than [X] year(s)".


use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\Spreadsheet;
use Google\Spreadsheet\SpreadsheetService;
use Oleg\UserdirectoryBundle\Util\UserUtil;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Exception\IOException;

class GoogleSheetManagement {

    protected $em;
    protected $sc;
    protected $container;

    public function __construct( $em, $sc, $container ) {

        $this->em = $em;
        $this->sc = $sc;
        $this->container = $container;
    }

    //delete: imported rows from the sheet on Google Drive and associated uploaded files from the Google Drive.
    //1) find spreadsheet
    //2) find worksheet
    //3) find row in worksheet by rowid (don't use '@'. In google GS '@' is replaced by '_') cinava_yahoo.com_Doe1_Linda1_2016-03-22_17_30_04
    //4) foreach file in the row => delete this file from Google Drive
    //5) delete this row
    public function deleteImportedApplicationAndUploadsFromGoogleDrive($rowId) {

        $logger = $this->container->get('logger');

        //cinava7_yahoo.com_Doe_Linda_2016-03-15_17_59_53
        //$rowId = "cinava_yahoo.com_Doe1_Linda1_2016-03-22_17_30_04";
        if( !$rowId ) {
            $logger->warning('Fellowship Application Google Form ID does not exists. rowId='.$rowId);
        }

        $fellappUtil = $this->container->get('fellapp_util');
        $userSecUtil = $this->container->get('user_security_utility');
        $systemUser = $userSecUtil->findSystemUser();
        $userUtil = new UserUtil();

        //get Google access token
        $accessToken = $this->getGoogleToken();

        if( !$accessToken ) {
            $event = "Google API access Token empty";
            $logger->warning($event);
            $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$systemUser,null,null,'Error');
            $fellappUtil->sendEmailToSystemEmail($event, $event);
            return null;
        }
        //exit('service ok');

        //https://drive.google.com/open?id=1DN1BEbONKNmFpHU6xBo69YSLjXCnhRy0IbyXrwMzEzc
        $excelId = $userUtil->getSiteSetting($this->em,'excelIdFellApp');
        //$excelId = "1hNJUm-EWC33tEyvgkcBJQ7lO1PcFwxfi3vMuB96etno";
        if( !$excelId ) {
            $logger = $this->container->get('logger');
            $logger->warning('Sheet ID is not defined in Site Parameters. excelIdFellApp='.$excelId);
        }

        //testing revision
        //$revisions = $this->retrieveRevisions($excelId);
        //exit('1');

        //0 initialize ServiceRequestFactory
        $serviceRequest = new CustomDefaultServiceRequest($accessToken); //use my custom class to set CURLOPT_SSL_VERIFYPEER to false in DefaultServiceRequest
        ServiceRequestFactory::setInstance($serviceRequest);
        $spreadsheetService = new SpreadsheetService();

        //1) find spreadsheet
        $spreadsheet = $spreadsheetService->getSpreadsheetById($excelId);
        if( !$spreadsheet ) {
            throw new IOException('Spreadsheet not found by key='.$excelId);
        }

        //2) find worksheet by name
        $worksheetFeed = $spreadsheet->getWorksheets();
        $worksheet = $worksheetFeed->getByTitle('Form Responses 1');

        //3) find row in worksheet by rowid (don't use '@'. In google GS '@' is replaced by '_') cinava_yahoo.com_Doe1_Linda1_2016-03-22_17_30_04
        $listFeed = $worksheet->getListFeed( array("sq" => "id = " . $rowId) ); //it's a row

        //4) foreach file in the row => delete this file from Google Drive

        $deletedRows = 0;

        //identify file by presence of string 'drive.google.com/a/pathologysystems.org/file/d/'
        $fileStrFlag = 'drive.google.com/a/pathologysystems.org/file/d/';

        foreach( $listFeed->getEntries() as $entry ) {
            $values = $entry->getValues();
            //echo "list:<br>";
            //print_r($values );
            //echo "<br>";
            //echo "lastname=".$values['lastname']."<br>";

            //4.a) foreach file in the row => delete this file from Google Drive
            foreach( $values as $cellValue ) {

                if( strpos($cellValue, $fileStrFlag) !== false ) {
                    //echo 'this is file = '.$cellValue." => ";
                    //get Google Drive file ID from https://drive.google.com/a/pathologysystems.org/file/d/0B2FwyaXvFk1eWGJQQ29CbjVvNms/view?usp=drivesdk
                    $fileID = $this->getFileId($cellValue);
                    //echo 'fileID = '.$fileID."<br>";
                    $res = $this->removeFile($fileID);
                    if( $res ) {
                        //echo 'File was deleted with fileID = '.$fileID."<br>";
                    } else {
                        //echo 'Failed to delete file with fileID = '.$fileID."<br>";
                        $logger->warning('Failed to delete file with fileID = '.$fileID);
                    }
                }
            }


            //5) delete this row (entry)
            $entry->delete();

            $deletedRows++;
        }


        //exit(1);
        return $deletedRows;
    }


    function getFileId($str) {
        // https://drive.google.com/a/pathologysystems.org/file/d/0B2FwyaXvFk1eWGJQQ29CbjVvNms/view?usp=drivesdk
        $parts = explode("/",$str);
        //echo "count=".count($parts)."<br>";
        if( count($parts) == 9 ) {
            //$keyId = $parts[7];
            //echo "keyId=".$keyId."<br>";
            return $parts[7];
        }
        return null;
    }

    public function removeFile($fileId) {

        $logger = $this->container->get('logger');
        $service = $this->getGoogleService();

        try {
            $service->files->delete($fileId);
        } catch (\Exception $e) {
            $logger->error("Error deleting file from Google Drive with fileId=".$fileId."; Error: " . $e->getMessage());
            return false;
        }
        return true;
    }


    /**
     * Retrieve a list of revisions.
     *
     * @param String $fileId ID of the file to retrieve revisions for.
     * @return Array List of Google_Servie_Drive_Revision resources.
     */
    function retrieveRevisions($fileId) {
        $service = $this->getGoogleService();
        $revisionItems = null;

        try {
            $revisions = $service->revisions->listRevisions($fileId);
            $revisionItems = $revisions->getItems();
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }

        if( $revisionItems ) {
            //$lastRevision = null;
            $revcount = 0;
            foreach ($revisions as $revision) { //revision is Google_Service_Drive_Revision object
                echo "<br>";
                print_r($revision);
                echo "<br>";
                $revisionId = $revision->getId();
                echo "revisionId=".$revisionId."<br>";
                //$lastRevision = $revision;
                $revcount++;
            }
            echo "revcount=" . $revcount . "<br>";

            echo "delete revision id=".$revisionId."; time=".$revision->getModifiedDate()."<br>";
            //https://developers.google.com/drive/v3/reference/revisions/delete?authuser=1#try-it
            $revision = $service->revisions->get($fileId, $revisionId);
            //$revision->delete();
            //$revision->setPublished(true);
            $revision->setPinned(false);
            $revision->setDownloadUrl('www.yahoo.com');
            $revision->setMimeType('testTTT');
            $revision->setExportLinks(null);
            $service->revisions->update($fileId, $revisionId, $revision);

            $revision = $service->revisions->get($fileId, $revisionId);
            echo "<br>";
            print_r($revision);
            echo "<br>";

            //$this->removeRevision($service, $fileId, $revisionId);
        }

        return NULL;
    }

    /**
     * https://developers.google.com/drive/v2/reference/revisions/delete#try-it
     * Remove a revision.
     *
     * @param Google_Service_Drive $service Drive API service instance.
     * @param String $fileId ID of the file to remove the revision for.
     * @param String $revisionId ID of the revision to remove.
     */
    function removeRevision($service, $fileId, $revisionId) {
        try {
            $service->revisions->delete($fileId, $revisionId);
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
    }



    function searchSheet() {
        $accessToken = $this->getGoogleToken();

        //use my custom class to set CURLOPT_SSL_VERIFYPEER to false in DefaultServiceRequest
        $serviceRequest = new CustomDefaultServiceRequest($accessToken);
        ServiceRequestFactory::setInstance($serviceRequest);

        $spreadsheetService = new SpreadsheetService();
        //$spreadsheetFeed = $spreadsheetService->getSpreadsheets();
        //$spreadsheet = $spreadsheetFeed->getByTitle('Fellapp-test');
        $key = '1hNJUm-EWC33tEyvgkcBJQ7lO1PcFwxfi3vMuB96etno';
        //$spreadsheet = $this->getByKey($spreadsheetFeed,$key);

        $spreadsheet = $spreadsheetService->getSpreadsheetById($key);
        if( !$spreadsheet ) {
            throw new IOException('Spreadsheet not found by key='.$key);
        }

        $worksheetFeed = $spreadsheet->getWorksheets();
        $worksheet = $worksheetFeed->getByTitle('Form Responses 1');
        $listFeed = $worksheet->getListFeed();

        $rowTitle = "cinava7@yahoo.com_Doe_Linda_2016-03-15_17_59_53";
        $rowTitle = "cinava7@";
        $rowTitle = "testid1";
        $rowTitle = "test1emailcinava@yahoo.com";
        $rowTitle = "test1emailcinava_yahoo.com_Doe_Linda_2016-03-15_17_59_5";
        $rowTitle = "cinava7_yahoo.com_Doe_Linda_2016-03-15_17_59_53";

        //$rowTitle = urlencode($rowTitle);
        //echo "rowTitle=".$rowTitle."<br>";

        $listFeed = $worksheet->getListFeed(array("sq" => "id = $rowTitle", "reverse" => "true"));

        $entries = $listFeed->getEntries();
        foreach( $entries as $entry ) {
            echo "list:<br>";
            $values = $entry->getValues();
            print_r($values );
            echo "<br>";
            echo "lastname=".$values['lastname']."<br>";
        }
        echo "eof list<br><br>";

        //echo "<br><br>full list:<br>";
        //print_r($listFeed);
    }

    /**
     * Gets a spreadhseet from the feed by its key . i.e. the id of
     * the spreadsheet in google drive. This method will return only the
     * first spreadsheet found with the specified title.
     *
     * https://drive.google.com/open?id=1hNJUm-EWC33tEyvgkcBJQ7lO1PcFwxfi3vMuB96etno
     * key=1hNJUm-EWC33tEyvgkcBJQ7lO1PcFwxfi3vMuB96etno
     *
     * @param string $title
     *
     * @return \Google\Spreadsheet\Spreadsheet|null
     */
    public function getByKey($spreadsheetFeed,$key)
    {
        foreach( $spreadsheetFeed->getXml()->entry as $entry ) {
            //full id: https://spreadsheets.google.com/feeds/spreadsheets/private/full/1hNJUm-EWC33tEyvgkcBJQ7lO1PcFwxfi3vMuB96etno
            $id = $entry->id->__toString();
            //echo "id=".$id."<br>";
            $parts = explode("/",$id);
            //echo "count=".count($parts)."<br>";
            if( count($parts) == 8 ) {
                $keyId = $parts[7];
                //echo "keyId=".$keyId."<br>";
                if( $keyId == $key) {
                    return new Spreadsheet($entry);
                }
            }
        }
        return null;
    }












    public function getGoogleToken() {
        $res = $this->authenticationP12Key();
        $obj_client_auth = $res['client'];
        $obj_token  = json_decode($obj_client_auth->getAccessToken() );
        return $obj_token->access_token;
    }

    public function getGoogleService() {
        $res = $this->authenticationP12Key();
        return $res['service'];
    }

    //Using OAuth 2.0 for Server to Server Applications: using PKCS12 certificate file
    //Security page: https://admin.google.com/pathologysystems.org/AdminHome?fral=1#SecuritySettings:
    //Credentials page: https://console.developers.google.com/apis/credentials?project=turnkey-delight-103315&authuser=1
    //https://developers.google.com/api-client-library/php/auth/service-accounts
    //1) Create a service account by Google Developers Console.
    //2) Delegate domain-wide authority to the service account.
    //3) Impersonate a user account.
    public function authenticationP12Key() {

        $logger = $this->container->get('logger');

        //$client_email = '1040591934373-1sjcosdt66bmani0kdrr5qmc5fibmvk5@developer.gserviceaccount.com';
        $userUtil = new UserUtil();
        $client_email = $userUtil->getSiteSetting($this->em,'clientEmailFellApp');

        //$pkey = __DIR__ . '/../Util/FellowshipApplication-f1d9f98353e5.p12';
        $pkey = $userUtil->getSiteSetting($this->em,'p12KeyPathFellApp');
        if( !$pkey ) {
            $logger->warning('p12KeyPathFellApp is not defined in Site Parameters. p12KeyPathFellApp='.$pkey);
        }

        //$user_to_impersonate = 'olegivanov@pathologysystems.org';
        $user_to_impersonate = $userUtil->getSiteSetting($this->em,'userImpersonateEmailFellApp');

        //echo "pkey=".$pkey."<br>";
        $private_key = file_get_contents($pkey); //notasecret

        $userUtil = new UserUtil();
        $googleDriveApiUrlFellApp = $userUtil->getSiteSetting($this->em,'googleDriveApiUrlFellApp');
        if( !$googleDriveApiUrlFellApp ) {
            throw new \InvalidArgumentException('googleDriveApiUrlFellApp is not defined in Site Parameters.');
        }

        //PHP API scopes: https://developers.google.com/api-client-library/php/auth/service-accounts#creatinganaccount
        //set scopes on developer console:
        // https://admin.google.com/pathologysystems.org/AdminHome?chromeless=1#OGX:ManageOauthClients
        //scopes: example: "https://spreadsheets.google.com/feeds https://docs.google.com/feeds"
        //$scopes = array($googleDriveApiUrlFellApp); //'https://www.googleapis.com/auth/drive'
        $scopes = $googleDriveApiUrlFellApp;

        try {

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

            if( $client->getAuth()->isAccessTokenExpired() ) {
                //echo 'before refreshTokenWithAssertion<br>';
                //print_r($credentials);
                //exit('before');
                $client->getAuth()->refreshTokenWithAssertion($credentials); //causes timeout on localhost: OAuth ERR_CONNECTION_RESET
                exit('after');
            }

            $service = new \Google_Service_Drive($client);

        } catch(Exception $e) {
            $subject = "Failed to authenticate to Google using P12";
            $event = "Failed to authenticate to Google using P12: " . $e->getMessage();
            $logger->error($event);
            //$userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$systemUser,null,null,'Error');
            $fellappUtil = $this->container->get('fellapp_util');
            $fellappUtil->sendEmailToSystemEmail($subject, $event);
        }

        $res = array(
            'client' => $client,
            'credentials' => $credentials,
            'service' => $service
        );

        return $res;
    }


    /**
     * Download a file's content.
     *
     * @param Google_Servie_Drive $service Drive API service instance.
     * @param Google_Servie_Drive_DriveFile $file Drive File instance.
     * @return String The file's content if successful, null otherwise.
     */
    function downloadFile($service, $file, $type=null) {
        if( $type && $type == 'Fellowship Application Spreadsheet' ) {
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


    //Automatically delete downloaded applications that are older than [X] year(s)
    // X - yearsOldAplicationsFellApp
    public function deleteOldSheetFellApp() {

        $userUtil = new UserUtil();

        //deleteOldAplicationsFellApp
        $deleteOldAplicationsFellApp = $userUtil->getSiteSetting($this->em,'deleteOldAplicationsFellApp');
        if( !$deleteOldAplicationsFellApp ) {
            $logger = $this->container->get('logger');
            $logger->notice('deleteOldAplicationsFellApp is FALSE or not defined in Site Parameters. deleteOldAplicationsFellApp='.$deleteOldAplicationsFellApp);
            return false;
        }

        $yearsOldAplicationsFellApp = $userUtil->getSiteSetting($this->em,'yearsOldAplicationsFellApp');
        if( !$yearsOldAplicationsFellApp ) {
            $logger = $this->container->get('logger');
            $logger->warning('yearsOldAplicationsFellApp is not defined in Site Parameters. yearsOldAplicationsFellApp='.$yearsOldAplicationsFellApp);
            return false;
        }

        //delete old sheets
        $userSecUtil = $this->container->get('user_security_utility');
        $days = $yearsOldAplicationsFellApp * 365;
        $result = $userSecUtil->deleteOrphanFiles( $days, 'Fellowship Application Spreadsheet', 'only' );
        return $result;

//        //$beforeDate = $startDate->format('Y');
//        //DB date format: 2015-09-29 20:26:13.000000
//        $nowDate = new \DateTime('now');
//        $dateCorrectionStr = '-'.$yearsOldAplicationsFellApp.' years';
//
//        //testing
//        //$yearsOldAplicationsFellApp = 90;
//        //$dateCorrectionStr = '-'.$yearsOldAplicationsFellApp.' days';
//        //echo "dateCorrectionStr=".$dateCorrectionStr."<br>";
//
//        $beforeDate = $nowDate->modify($dateCorrectionStr)->format('Y-m-d');
//        //echo "beforeDate=".$beforeDate."<br>";
//
//        //get spreadsheets older than X year
//        $repository = $this->em->getRepository('OlegUserdirectoryBundle:Document');
//        $dql =  $repository->createQueryBuilder("document");
//        $dql->select('document');
//        $dql->leftJoin('document.type','documentType');
//
//        $dql->where("documentType.name = :documentType OR documentType.abbreviation = :documentType");
//        $dql->andWhere("document.createdate < :beforeDate");
//
//        $query = $this->em->createQuery($dql);
//
//        //echo "query=".$query->getSql()."<br>";
//
//        $query->setParameters(array(
//            'documentType' => 'Fellowship Application Spreadsheet',
//            'beforeDate' => $beforeDate
//        ));
//
//        $documents = $query->getResult();
//
//        //echo "documents count=".count($documents)."<br>";
//
//        $deletedDocumentIdsArr = array();
//
//        //foreach documents unlink and delete from DB
//        foreach( $documents as $document ) {
//            $deletedDocumentIdsArr[] = $document->getId();
//
//            //document absolute path
//            //$documentPath = $document->getAbsoluteUploadFullPath();
//            $documentPath = $this->container->get('kernel')->getRootDir() . '/../web/' . $document->getUploadDirectory().'/'.$document->getUniquename();
//            //$documentPath = "Uploaded/scan-order/documents/test.jpeg";
//            //echo "documentPath=".$documentPath."<br>";
//
//            //continue; //testing
//
//            $this->em->remove($document);
//            $this->em->flush();
//
//            //remove file from folder
//            if( is_file($documentPath) ) {
//                //echo "file exists!!! ";
//                unlink($documentPath);
//            } else {
//                //echo "file does exists??? ";
//            }
//
//            //break; //testing
//        }
//
//        return implode(",",$deletedDocumentIdsArr);
    }












    ///////////////////////////////// OLD not used methods ////////////////////////////////////////////
    //delete: imported rows from the sheet on Google Drive and associated uploaded files from the Google Drive.
    public function deleteImportedApplicationAndUploadsFromGoogleDrive_orig($fellowshipApplication) {

        $logger = $this->container->get('logger');
        $fellappUtil = $this->container->get('fellapp_util');
        $userSecUtil = $this->container->get('user_security_utility');
        $systemUser = $userSecUtil->findSystemUser();
        $userUtil = new UserUtil();

        //cinava7@yahoo.com_Doe_Linda_2016-03-15_17_59_53
        //$rowId = $fellowshipApplication->getGoogleFormId();

        $service = $this->getGoogleService();

        if( !$service ) {
            $event = "Google API service failed!";
            $logger->warning($event);
            $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$systemUser,null,null,'Error');
            $fellappUtil->sendEmailToSystemEmail($event, $event);
            return null;
        }
        //exit('service ok');

        //https://drive.google.com/open?id=1DN1BEbONKNmFpHU6xBo69YSLjXCnhRy0IbyXrwMzEzc
        //$excelId = "1DN1BEbONKNmFpHU6xBo69YSLjXCnhRy0IbyXrwMzEzc";
        $excelId = $userUtil->getSiteSetting($this->em,'excelIdFellApp');

        //http://stackoverflow.com/questions/23400842/delete-row-in-google-spredsheet-with-php-curl?

        //https://api.kdyby.org/class-Google_Http_Request.html
        //https://developers.google.com/google-apps/spreadsheets/#deleting_a_list_row:
        // DELETE https://spreadsheets.google.com/feeds/list/key/worksheetId/private/full/rowId/rowVersion

        //https://docs.google.com/a/pathologysystems.org/spreadsheets/d/1hNJUm-EWC33tEyvgkcBJQ7lO1PcFwxfi3vMuB96etno/edit?usp=sharing
        $excelId = "1hNJUm-EWC33tEyvgkcBJQ7lO1PcFwxfi3vMuB96etno";
        $rowId = 3;

        ///////////////////excel download test
        if(0) {
            $file = null;
            try {
                $file = $service->files->get($excelId);
            } catch (Exception $e) {
                throw new IOException('Google API: Unable to get file by file id=' . $excelId . ". An error occurred: " . $e->getMessage());
            }
            //download file test
            $response = $this->downloadFile($service, $file, 'Fellowship Application Spreadsheet');
            echo "response=" . $response . "<br>";
            if (!$response) {
                throw new IOException('Error file response is empty: file id=' . $excelId);
            }
            echo 'response ok <br>';
        }
        //////////////////////////////

        $deleteUrl = "https://spreadsheets.google.com/feeds/list/key/".$excelId."/private/full/".$rowId;
        echo 'deleteUrl='.$deleteUrl.'<br>';

        $request = new \Google_Http_Request($deleteUrl, 'DELETE', null, null);
        $httpRequest = $service->getClient()->getAuth()->authenticatedRequest($request);
        echo "res code=".$httpRequest->getResponseHttpCode()."<br>";

        print_r($httpRequest);

        if ($httpRequest->getResponseHttpCode() == 200) {
            return $httpRequest->getResponseBody();
        } else {
            // An error occurred.
            return null;
        }


        exit(1);
        return true;
    }

    //delete: imported rows from the sheet on Google Drive and associated uploaded files from the Google Drive.
    public function deleteImportedApplicationAndUploadsFromGoogleDrive_2($fellowshipApplication) {

        $logger = $this->container->get('logger');
        $fellappUtil = $this->container->get('fellapp_util');
        $userSecUtil = $this->container->get('user_security_utility');
        $systemUser = $userSecUtil->findSystemUser();
        $userUtil = new UserUtil();

        //cinava7@yahoo.com_Doe_Linda_2016-03-15_17_59_53
        //$rowId = $fellowshipApplication->getGoogleFormId();

        $service = $this->getGoogleService();

        if( !$service ) {
            $event = "Google API service failed!";
            $logger->warning($event);
            $userSecUtil->createUserEditEvent($this->container->getParameter('fellapp.sitename'),$event,$systemUser,null,null,'Error');
            $fellappUtil->sendEmailToSystemEmail($event, $event);
            return null;
        }
        //exit('service ok');

        //https://drive.google.com/open?id=1DN1BEbONKNmFpHU6xBo69YSLjXCnhRy0IbyXrwMzEzc
        //$excelId = "1DN1BEbONKNmFpHU6xBo69YSLjXCnhRy0IbyXrwMzEzc";
        $excelId = $userUtil->getSiteSetting($this->em,'excelIdFellApp');

        //http://stackoverflow.com/questions/23400842/delete-row-in-google-spredsheet-with-php-curl?

        //https://api.kdyby.org/class-Google_Http_Request.html
        //https://developers.google.com/google-apps/spreadsheets/#deleting_a_list_row:
        // DELETE https://spreadsheets.google.com/feeds/list/key/worksheetId/private/full/rowId/rowVersion

        //https://docs.google.com/a/pathologysystems.org/spreadsheets/d/1hNJUm-EWC33tEyvgkcBJQ7lO1PcFwxfi3vMuB96etno/edit?usp=sharing
        $excelId = "1hNJUm-EWC33tEyvgkcBJQ7lO1PcFwxfi3vMuB96etno";
        $rowId = 3;

        //excel download test
        if(0) {
            $file = null;
            try {
                $file = $service->files->get($excelId);
            } catch (Exception $e) {
                throw new IOException('Google API: Unable to get file by file id=' . $excelId . ". An error occurred: " . $e->getMessage());
            }
            //download file test
            $response = $this->downloadFile($service, $file, 'Fellowship Application Spreadsheet');
            echo "response=" . $response . "<br>";
            if (!$response) {
                throw new IOException('Error file response is empty: file id=' . $excelId);
            }
            echo 'response ok <br><br><br>';
            //exit('1');
        }

        $worksheetId = "odjpdbo";
        $rowId = 'cpzh4'; //'cokwr';

        $method = 'DELETE';
        $deleteUrl = "https://spreadsheets.google.com/feeds/list/key/".$worksheetId."/private/full/".$rowId."/v=3.0";

        //GET https://spreadsheets.google.com/feeds/cells/key/worksheetId/private/full?min-row=2&min-col=4&max-col=4
        $method = 'GET';
        $deleteUrl = "https://spreadsheets.google.com/feeds/cells/key/".$worksheetId."/private/full?min-row=2&min-col=4&max-col=4";
        $deleteUrl = "https://spreadsheets.google.com/feeds/cells/key/$excelId/private/full";
        $deleteUrl = "https://spreadsheets.google.com/feeds/worksheets/$excelId/private/full";
        $deleteUrl = "https://spreadsheets.google.com/feeds/spreadsheets/private/full"; //get info about spreadsheet
        $deleteUrl = "https://spreadsheets.google.com/feeds/worksheets/$excelId/private/full"; //get info about this spreadsheet => get worksheetId='odjpdbo'
        //$deleteUrl = "https://spreadsheets.google.com/feeds/list/key/$worksheetId/private/full";

        //1hNJUm-EWC33tEyvgkcBJQ7lO1PcFwxfi3vMuB96etno
        $deleteUrl = "https://spreadsheets.google.com/feeds/worksheets/1hNJUm-EWC33tEyvgkcBJQ7lO1PcFwxfi3vMuB96etno/private/full/odjpdbo"; //get info about this worksheet

        $deleteUrl = "https://spreadsheets.google.com/feeds/list/$excelId/$worksheetId/private/full"; //get worksheet => get rowId='cokwr'

        //$deleteUrl = "https://spreadsheets.google.com/feeds/list/$excelId/$worksheetId/private/full/$rowId";

        //GET https://spreadsheets.google.com/feeds/cells/key/worksheetId/private/full?min-row=2&min-col=4&max-col=4
        //R3C1 => cinava7@yahoo.com_Doe_Linda_2016-03-15_17_59_53cinava7@yahoo.com_Doe_Linda_2016-03-15_17_59_53
        //R3C2 => 3/15/2016 17:59:533/15/2016 17:59:53
        //R3C3 => DoeDoe
        //R3C4 => LindaLinda
        $deleteUrl = "https://spreadsheets.google.com/feeds/cells/$excelId/$worksheetId/private/full?min-row=3&min-col=1&max-col=4";

        //$id = "cinava7@yahoo.com_Doe_Linda_2016-03-15_17_59_53";
        //$deleteUrl = "https://spreadsheets.google.com/feeds/list/$excelId/$worksheetId/private/full?sq=id=cinava7";

        //try to use: http://stackoverflow.com/questions/19362703/how-to-update-google-spreadsheet-cell-using-php-api
        //https://github.com/asimlqt/php-google-spreadsheet-client

        echo 'deleteUrl='.$deleteUrl.'<br>';

        $request = new \Google_Http_Request($deleteUrl, $method, null, null);
        $httpRequest = $service->getClient()->getAuth()->authenticatedRequest($request);
        echo "res code=".$httpRequest->getResponseHttpCode()."<br>";

        print_r($httpRequest);
        echo "<br><br><br>";

        if ($httpRequest->getResponseHttpCode() == 200) {
            return $httpRequest->getResponseBody();
        } else {
            // An error occurred.
            return null;
        }


        exit(1);
        return true;
    }




}