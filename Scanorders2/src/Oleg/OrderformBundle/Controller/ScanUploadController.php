<?php
/**
 * Created by PhpStorm.
 * User: oli2002
 * Date: 10/15/14
 * Time: 11:57 AM
 */

namespace Oleg\OrderformBundle\Controller;

use Oleg\UserdirectoryBundle\Util\LargeFileDownloader;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Oleg\UserdirectoryBundle\Controller\UploadController;
use Symfony\Component\HttpFoundation\Response;

//error_reporting(E_ALL);
//include_once '\DatabaseRoutines.php';
//include_once '\cImageFile.php';

class ScanUploadController extends UploadController {

    /**
     * @Route("/file-delete", name="scan_file_delete")
     * @Method("DELETE")
     */
    public function deleteFileAction(Request $request) {
        return $this->deleteFileMethod($request);
    }

    /**
     * @Route("/file-download/{id}", name="scan_file_download", requirements={"id" = "\d+"})
     * @Method("GET")
     */
    public function downloadFileAction($id) {
        return $this->downloadFileMethod($id);
    }


    //Aperio API for authenticated user: http://c.med.cornell.edu/imageserver/@73956?GETPATH => \\collage\images\2015-05-14\73956.svs

    //Aperio communicate to DB by using "soap call" http://www.aperio.com/webservices/
    //$res = $client->__soapCall(	'GetRecordImages',		//SOAP Method Name
    //                              $ParamsArray); 			//Parameters

    //images are stores in a single svs file.
    //cTable.php GetImages function Show the list of record images

    //There are two ways to show slide image:
    //1) using 'Aperio Image Scope' with generated 'sis' file
    //2) using 'Web Scope': http://192.168.37.128/imageserver/@@Y4XGX_n725b-quq6RExmLlOJHFwi8MvoiWTyPOMAcSE6lO1I16q5fg==/@23/view.apml
    //Note: for the (2) way, Aperio authentication is required providing token Y4XGX_n725b-quq6RExmLlOJHFwi8MvoiWTyPOMAcSE6lO1I16q5fg==


    /**
     * @Route("/image-viewer/{system}/{type}/{tablename}/{imageid}", name="scan_image_viewer", requirements={"imageid" = "\d+"})
     * @Method("GET")
     */
    public function imageFileAction($system,$type,$tablename,$imageid) {

        //1) get image url info by imageid

        ////////////////// aperio DB ////////////////////////////
        $aperioEm = $this->getDoctrine()->getManager('aperio');

        $aperioConnection = $aperioEm->getConnection();

        $statement = $aperioConnection->prepare(
            'SELECT * FROM [Aperio].[dbo].[Image] a WHERE a.ImageId = :imageId'
        );
        $statement->bindValue('imageId', intval($imageid));
        $statement->execute();

        // for SELECT queries
        $results = $statement->fetchAll();  // note: !== $connection->fetchAll()!

        // for INSERT, UPDATE, DELETE queries
        $affected_rows = $statement->rowCount();

        //echo "Affected Rows=".$affected_rows."<br>";

        //echo "<br>Result:<br>";
        //print_r($results);
        //echo "<br><br>";

        if( $affected_rows != 1 && count($results) != 1 ) {
            throw $this->createNotFoundException('Unable to find unique image with id='.$imageid);
        }

        $compressedFileLocation = $results[0]['CompressedFileLocation'];
        //$compressedFileLocation = "C://Images/SampleData/1376592216_rat_liver_tox.jpg";
        //echo "compressedFileLocation Rows=".$compressedFileLocation."<br>";
        //////////////////////////////////////////////////////////

        //////////////// testing: order memory usage ////////////////
        //$mem = memory_get_usage(true);
        //echo "order mem = ".$mem. " => " .round($mem/1000000,2)." Mb<br>";
        //exit('1');
        //////////////// EOF order memory usage ////////////////

        //2) show image in Aperio's image viewer http://c.med.cornell.edu/imageserver/@@_DGjlRH2SJIRkb9ZOOr1sJEuLZRwLUhWzDSDb-sG0U61NzwQ4a8Byw==/@73660/view.apml

        $response = new Response();

        if( $compressedFileLocation ) {

            $fileLocArr = explode("\\",$compressedFileLocation);
            //$fileLocArr = explode("/",$compressedFileLocation);
            $originalFileName = $fileLocArr[ count($fileLocArr)-1 ];
            //echo "originalFileName=".$originalFileName."<br>";
            $originalname = $tablename."_Image_ID_" . $originalFileName;
            $size = 1;

            $contentFile = 'Not implemented link type ' . $type;
            $contentFlagOk = false;

            if( $type == 'Via ImageScope' ) {
                $contentFile = "<SIS>".
                    "<CloseAllImages/>".
                    "<ViewingMode></ViewingMode>".
                    "<Image>".
                    "<URL>".$compressedFileLocation."</URL>".
                    "<Title>".$originalname."</Title>".
                    "</Image>".
                    "</SIS>";

                $originalname = $tablename."_Image_ID_" . $imageid.".sis";

                $contentFlagOk = true;
            }

//            if( $type == 'Download-SmallFile' ) {
//
//                //ini_set('memory_limit', '2048M'); //128M
//
//                $compressedFileLocationConverted = str_replace("\\","/",$compressedFileLocation);
//                $remoteFile = $compressedFileLocationConverted;
//                //echo "remoteFile=".$remoteFile."<br>";
//
//                //$urlTest = '<a href="'.$remoteFile.'">Test Download</a>';
//                //echo $urlTest."<br>";
//
//                $contentFile = file_get_contents($remoteFile);
//                //echo "contentFile=".$contentFile."<br>";
//                //exit();
//
//                $size = filesize($remoteFile);
//                //echo "size=".$size."<br>";
//
//                $contentFlagOk = true;
//            }

            if( $type == 'Download' ) {

                //$compressedFileLocation = "C:/Images/GIID-153-001.svs"; //testing file on dev

                $downloader = new LargeFileDownloader();
                $downloader->downloadLargeFile($compressedFileLocation);

                exit;
            }

            if( $contentFlagOk ) {
                $response->headers->set('Content-Type', 'application/unknown');
                $response->headers->set('Content-Description', 'File Transfer');
                $response->headers->set('Content-Disposition', 'attachment; filename="'.$originalname.'"');
                $response->headers->set('Content-Length', $size);
                $response->headers->set('Content-Transfer-Encoding', 'binary');
            }


        } else {
            $contentFile = 'error';
        }

        $response->setContent($contentFile);

        //exit('exit imageFileAction');

        return $response;
    }


} 