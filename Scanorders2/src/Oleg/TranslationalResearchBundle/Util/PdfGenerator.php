<?php
/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 12/21/2017
 * Time: 3:02 PM
 */

namespace Oleg\TranslationalResearchBundle\Util;


use Oleg\UserdirectoryBundle\Entity\Document;
use Oleg\UserdirectoryBundle\Form\DataTransformer\GenericTreeTransformer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PdfGenerator
{

    protected $container;
    protected $em;
    protected $secTokenStorage;
    protected $secAuth;

    protected $uploadDir;

    public function __construct( $em, $container ) {
        $this->container = $container;
        $this->em = $em;
        $this->secAuth = $container->get('security.authorization_checker'); //$this->secAuth->isGranted("ROLE_USER")
        $this->secTokenStorage = $container->get('security.token_storage'); //$user = $this->secTokenStorage->getToken()->getUser();

        $this->uploadDir = 'Uploaded';
    }


    public function generateInvoicePdf( $entity, $authorUser ) {

        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $logger = $this->container->get('logger');

        $userSecUtil = $this->container->get('user_security_utility');

        if( !$authorUser ) {
            $authorUser = $userSecUtil->findSystemUser();
        }

        //Assume invoice has only one request: one request has many invoices
        //$transresRequest = $entity->getTransresRequests()->first();

        //generate file name
        $fileFullReportUniqueName = $this->constructUniqueFileName($entity,"Invoice-PDF");
        $logger->notice("Start to generate PDF Invoice ID=".$entity->getOid()."; filename=".$fileFullReportUniqueName);

        //check and create Report and temp folders
        $reportsUploadPath = "transres/InvoicePDF";  //$userSecUtil->getSiteSettingParameter('reportsUploadPathFellApp');
        if( !$reportsUploadPath ) {
            $reportsUploadPath = "InvoicePDF";
            $logger->warning('InvoicePDFUploadPath is not defined in Site Parameters. Use default "'.$reportsUploadPath.'" folder.');
        }
        $uploadReportPath = $this->uploadDir.'/'.$reportsUploadPath;

        $reportPath = $this->container->get('kernel')->getRootDir() . '/../web/' . $uploadReportPath;
        echo "reportPath=".$reportPath."<br>";
        //$reportPath = realpath($reportPath);
        //echo "reportPath=".$reportPath."<br>";

        if( !file_exists($reportPath) ) {
            mkdir($reportPath, 0700, true);
            chmod($reportPath, 0700);
        }

        //$outdir = $reportPath.'/temp_'.$entity->getOid().'/';
        //$outdir = $reportPath.'/'.$entity->getOid().'/';
        $outdir = $reportPath.'/';

        //echo "before generateApplicationPdf id=".$id."; outdir=".$outdir."<br>";
        //0) generate application pdf
        //$applicationFilePath = $outdir . "application_ID" . $entity->getOid() . ".pdf";
        $applicationFilePath = $outdir . $fileFullReportUniqueName;

        $this->generatePdf($entity,$applicationFilePath);
        //$logger->notice("Successfully Generated Application PDF from HTML for ID=".$id."; file=".$applicationFilePath);

        //$filenamePdf = $reportPath . '/' . $fileFullReportUniqueName;

        //4) add PDF to invoice DB
        $filesize = filesize($applicationFilePath);
        $documentPdf = $this->createInvoicePdfDB($entity,"document",$authorUser,$fileFullReportUniqueName,$uploadReportPath,$filesize,'Invoice PDF');
        if( $documentPdf ) {
            $documentPdfId = $documentPdf->getId();
        } else {
            $documentPdfId = null;
        }

        $event = "PDF for Invoice with ID".$entity->getOid()." has been successfully created " . $fileFullReportUniqueName . " (PDF document ID".$documentPdfId.")";
        //echo $event."<br>";
        //$logger->notice($event);

        $userSecUtil->createUserEditEvent($this->container->getParameter('translationalresearch.sitename'),$event,$authorUser,$entity,null,'Invoice PDF Created');

        //delete application temp folder
        //$this->deleteDir($outdir);

        $res = array(
            'filename' => $fileFullReportUniqueName,
            'pdf' => $applicationFilePath,
            'size' => $filesize
        );

        $logger->notice($event);

        return $res;
    }

    protected function constructUniqueFileName($entity,$filenameStr) {

        $logger = $this->container->get('logger');

        $currentDate = new \DateTime();
        $subjectUser = $entity->getSubmitter();
        $submitterName = $subjectUser->getUsernameShortest();
        $submitterName = str_replace(" ","-",$submitterName);
        $submitterName = str_replace(".","-",$submitterName);
        if( $submitterName ) {
            $submitterName = "-" . $submitterName;
        }

        $serverTimezone = date_default_timezone_get(); //server timezone

        $filename =
            $filenameStr.
            "-ID".$entity->getOId().
            //"-".$subjectUser->getLastNameUppercase().
            //"-".$subjectUser->getFirstNameUppercase().
            $submitterName.
            "-generated-on-".$currentDate->format('m-d-Y').'-at-'.$currentDate->format('h-i-s-a').'_'.$serverTimezone.
            ".pdf";

        //replace all white spaces to _
        $filename = str_replace(" ","_",$filename);
        $filename = str_replace("/","_",$filename);

        return $filename;
    }

    //use KnpSnappyBundle to convert html to pdf
    //http://wkhtmltopdf.org must be installed on server
    public function generatePdf($invoice,$applicationOutputFilePath) {
        $logger = $this->container->get('logger');
        $logger->notice("Trying to generate PDF in ".$applicationOutputFilePath);
        if( file_exists($applicationOutputFilePath) ) {
            //return;
            $logger->notice("generatePdf: unlink file already exists path=" . $applicationOutputFilePath );
            unlink($applicationOutputFilePath);
        }

        ini_set('max_execution_time', 300); //300 sec

        //generate application URL
        $router = $this->container->get('router');
        $context = $router->getContext();

        //http://192.168.37.128/order/app_dev.php/translational-research/download-invoice-pdf/49
        $context->setHost('localhost');
        $context->setScheme('http');
        $context->setBaseUrl('/order');

        //invoice download
        $pageUrl = $router->generate('translationalresearch_invoice_download',
            array(
                'oid' => $invoice->getOid()
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        ); //this does not work from console: 'order' is missing

        //$logger->notice("### pageUrl=".$pageUrl);
        //echo "pageurl=". $pageUrl . "<br>";
        //exit();

        //$application =
        $this->container->get('knp_snappy.pdf')->generate(
            $pageUrl,
            $applicationOutputFilePath
        //array('cookie' => array($session->getName() => $session->getId()))
        );

        //echo "generated ok! <br>";
    }

    //create invoice report in DB
    protected function createInvoicePdfDB($holderEntity,$holderMethodSingularStr,$author,$uniqueTitle,$path,$filesize,$documentType) {

        $logger = $this->container->get('logger');

        $object = new Document($author);

        $object->setUniqueid($uniqueTitle);
        $object->setCleanOriginalname($uniqueTitle);
        $object->setTitle($uniqueTitle);
        $object->setUniquename($uniqueTitle);

        $object->setUploadDirectory($path);
        $object->setSize($filesize);

        $transformer = new GenericTreeTransformer($this->em, $author, "DocumentTypeList", "UserdirectoryBundle");
        $documentType = trim($documentType);
        $documentTypeObject = $transformer->reverseTransform($documentType);
        if( $documentTypeObject ) {
            $object->setType($documentTypeObject);
        }

        //constructs methods: "getReports", "removeReport", "addReport"
        $getMethod = "get".$holderMethodSingularStr."s";
        $removeMethod = "remove".$holderMethodSingularStr;
        $addMethod = "add".$holderMethodSingularStr;

        //do not remove documents Application PDF
        //move all reports to OldReports
        if( $holderMethodSingularStr == "report" ) {
            foreach ($holderEntity->getReports() as $report) {
                $holderEntity->removeReport($report);
                $holderEntity->addOldReport($report);
            }
        }

        //add report
        $holderEntity->$addMethod($object);

        $this->em->persist($holderEntity);
        $this->em->persist($object);
        $this->em->flush();

        $logger->notice("Document created with ID=".$object->getId()." for Invoice ID=".$holderEntity->getId() . "; documentType=".$documentType);

        return $object;
    }

    protected static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new \InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

}