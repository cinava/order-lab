<?php
/**
 * Copyright (c) 2017 Cornell University
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace App\UserdirectoryBundle\Controller;



use App\UserdirectoryBundle\Controller\OrderAbstractController;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Fabiang\Sasl\Sasl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use Symfony\Component\Ldap\Adapter\ExtLdap\ConnectionOptions;
//use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends OrderAbstractController
{

    /**
     * @Route("/user-thanks-for-downloading/{id}/{sitename}", name="employees_thankfordownloading", methods={"GET"})
     * @Route("/thanks-for-downloading/{id}/{sitename}", name="common_thankfordownloading", methods={"GET"})
     * @Template("AppUserdirectoryBundle/Default/thanksfordownloading.html.twig")
     */
    public function thankfordownloadingAction(Request $request, $id, $sitename) {
        return array(
            'fileid' => $id,
            'sitename' => $sitename
        );
    }


    /**
     * @Route("/show-system-log", name="employees_show_system_log", methods={"GET"})
     * @Template("AppUserdirectoryBundle/Default/show-system-log.html.twig")
     */
    public function showSystemLogAction(Request $request) {

        if( false === $this->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            return $this->redirect($this->generateUrl('employees-nopermission'));
        }

        $logDir = $this->container->get('kernel')->getProjectDir() . DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "log";

        $systemLogFile = NULL;
        //$files = scandir($logDir, SCANDIR_SORT_DESCENDING);

        $files = glob($logDir.'/prod-*.log');
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        //dump($files);
        //echo "files=".count($files)."<br>";
        if( count($files) > 0 ) {
            $systemLogFile = $files[0];
//            foreach($files as $file) {
//                if( str_contains($file, 'prod-') ) {
//                    $systemLogFile = $file;
//                    break;
//                }
//            }
        }
        //echo "newestLogFile=$newestLogFile <br>";
        //exit('111');

        if( !$systemLogFile ) {
            exit();
            return array();
        }

        //$systemLogFile = $logDir . DIRECTORY_SEPARATOR . $newestLogFile; //"prod.log";

        //echo file_get_contents( $systemLogFile );

        //$orig = file_get_contents($systemLogFile);
        //$a = htmlentities($orig);

        echo $systemLogFile;

        echo '<code>';
        echo '<pre>';

        //echo $a;
        echo file_get_contents( $systemLogFile );

        echo '</pre>';
        echo '</code>';

        exit();
        return array();
    }

    /**
     * @Route("/show-system-test-error-log", name="employees_show_system_test_error_log", methods={"GET"})
     * @Template("AppUserdirectoryBundle/Default/show-system-log.html.twig")
     */
    public function showSystemTestLogAction(Request $request) {

        if( false === $this->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            return $this->redirect($this->generateUrl('employees-nopermission'));
        }

        $logDir = $this->container->get('kernel')->getProjectDir() . DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "log";

        $systemLogFile = $logDir . DIRECTORY_SEPARATOR . "test_ERROR.log";

        //echo file_get_contents( $systemLogFile );

        //$orig = file_get_contents($systemLogFile);
        //$a = htmlentities($orig);

        echo $systemLogFile;

        echo '<code>';
        echo '<pre>';

        //echo $a;
        echo file_get_contents( $systemLogFile );

        echo '</pre>';
        echo '</code>';

        exit();
        return array();
    }

    /**
     * @Route("/run-all-test", name="employees_run_test_all", methods={"GET"})
     * @Template("AppUserdirectoryBundle/Default/show-run-test.html.twig")
     */
    public function runAllTestAction(Request $request) {

        if( false === $this->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            return $this->redirect($this->generateUrl('employees-nopermission'));
        }

        $logDir = $this->container->get('kernel')->getProjectDir();
        echo "logDir=$logDir <br>";

        //$testFolderCwd = $logDir . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "bin";
        //$testCmd = $logDir . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "phpunit";

        //$logDir = $logDir.DIRECTORY_SEPARATOR;

        //$testCmd = "HTTP=1  ./vendor/bin/phpunit -d memory_limit=-1";
        //$testCmd = "vendor/bin/phpunit -d memory_limit=-1";
        //$testCmd = "./vendor/bin/phpunit.bat";

        $userServiceUtil = $this->container->get('user_service_utility');
        if( $userServiceUtil->isWindows() ){
            //Windows
            $testCmd = "./vendor/bin/phpunit.bat";
            //$envArr = array('HTTP' => 1);
            $commandArr = array($testCmd,'-d', 'memory_limit=-1');
        } else {
            //Linux
            $testCmd = "vendor/bin/phpunit";
            //$envArr = array();
            $commandArr = array($testCmd);
        }

        $userUtil = $this->container->get('user_utility');
        $scheme = $userUtil->getScheme();
        //exit("scheme=$scheme");
        if( $scheme ) {
            if( strtolower($scheme) == 'http' ) {
                //echo "HTTP";
                $envArr = array('HTTP' => 1);
            } else {
                //echo "HTTPS";
                //$httpsChannel = true;
            }
        }

        echo "testCmd=$testCmd <br>";

        //array $command, string $cwd = null, array $env = null, mixed $input = null, ?float $timeout = 60
        //$commandArr = array($testCmd);
        $commandArr = array($testCmd,'-d', 'memory_limit=-1');

        $execTime = 6000;
        ini_set('max_execution_time', $execTime);

        $process = new Process($commandArr,$logDir,$envArr,null,$execTime);

        $process->run(function ($type, $buffer) {

            echo '<code>';
            echo '<pre>';

            echo $buffer;

            echo '</pre>';
            echo '</code>';


//            if (Process::ERR === $type) {
//                echo 'ERR > '.$buffer;
//            } else {
//                echo 'OUT > '.$buffer;
//            }
        });

        //return array('testCmd' => $testCmd);
        exit();
    }
    /**
     * @Route("/run-test", name="employees_run_test", methods={"GET"})
     * @Template("AppUserdirectoryBundle/Default/show-run-test.html.twig")
     */
    public function runTestAction(Request $request) {

        if( false === $this->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            return $this->redirect($this->generateUrl('employees-nopermission'));
        }

        //$userServiceUtil = $this->container->get('user_service_utility');

        $projectDir = $this->container->get('kernel')->getProjectDir() ;
        //$tests = $logDir . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'TestBundle';
        //$tests = $tests . DIRECTORY_SEPARATOR . 'UserTest.php';

        $testsDir = $projectDir.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'App'.DIRECTORY_SEPARATOR.'TestBundle';
        //echo "testsDir=$testsDir <br>";

        $files = glob($testsDir.'/*Test.php',GLOB_BRACE);

        $count = 0;
        $testFiles = array();
        foreach($files as $file) {
            //echo "file=".basename($file)."<br>";
            $testFiles[] = basename($file);
            $count++;
//            if( $count > 2 ) {
//                break;
//            }
        }

        $testFilesStr = "";
        if( count($testFiles) > 0 ) {
            $testFilesStr = implode(",",$testFiles);
        }

        //testing
        //$testFilesStr = "CalllogShortTest.php,UserTest.php"; //"TrpTest.php";
        //$testFilesStr = "CalllogTest.php";

        //exit('111');

        return array('testFiles'=>$testFilesStr);
    }
    /**
     * http://127.0.0.1/order/directory/run-test-ajax?testFile=UserTest.php
     *
     * @Route("/run-test-ajax", name="employees_run_test_ajax", methods={"GET"}, options={"expose"=true})
     */
    public function runTestAjaxAction(Request $request) {

        if( false === $this->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            return $this->redirect($this->generateUrl('employees-nopermission'));
        }

        $execTime = 6000; //sec 10 min
        ini_set('max_execution_time', $execTime);

        //$userServiceUtil = $this->container->get('user_service_utility');

        $result = "no testing";

        $testFile = trim((string)$request->get('testFile'));

        $logDir = $this->container->get('kernel')->getProjectDir() ;

        //$tests = $logDir . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'TestBundle';

        //$testFilePath = $tests . DIRECTORY_SEPARATOR . $testFile;
        $testFilePath = 'tests'.DIRECTORY_SEPARATOR.'App'.DIRECTORY_SEPARATOR.'TestBundle'.DIRECTORY_SEPARATOR.$testFile;
        //echo "testFilePath=$testFilePath <br>";
        //testFilePath=C:\Users\ch3\Documents\MyDocs\WCMC\ORDER\order-lab\orderflex\tests\App\TestBundle\UserTest.php

        //$testCmd = "HTTP=1  ./vendor/bin/phpunit -d memory_limit=-1";
        //$testCmd = "vendor/bin/phpunit -d memory_limit=-1";
        //$testCmd = "./vendor/bin/phpunit.bat";

        $envArr = array();

        $userServiceUtil = $this->container->get('user_service_utility');
        if( $userServiceUtil->isWindows() ){
            //Windows
            $testCmd = "./vendor/bin/phpunit.bat";
            //$envArr = array('HTTP' => 1);
            $commandArr = array($testCmd,'-d', 'memory_limit=-1',$testFilePath);
        } else {
            //Linux
            $testCmd = "vendor/bin/phpunit";
            //$envArr = array();
            $commandArr = array($testCmd,$testFilePath);
        }

        $userUtil = $this->container->get('user_utility');
        $scheme = $userUtil->getScheme();
        //exit("scheme=$scheme");
        if( $scheme ) {
            if( strtolower($scheme) == 'http' ) {
                //echo "HTTP";
                $envArr = array('HTTP' => 1);
            } else {
                //echo "HTTPS";
                //$httpsChannel = true;
            }
        }

        //echo "testCmd=$testCmd <br>";

        //array $command, string $cwd = null, array $env = null, mixed $input = null, ?float $timeout = 60

        $execTime = 600; //10 min
        ini_set('max_execution_time', $execTime);

        $process = new Process($commandArr,$logDir,$envArr,null,$execTime);

//        $process->run(function ($type, $buffer) {
//            $response = new Response();
//            $response->headers->set('Content-Type', 'application/json');
//            $response->setContent(json_encode($buffer));
//            return $response;
//
//        });

        try {
            $process->mustRun();
            $buffer = $process->getOutput();
            $buffer = '<code><pre>'.$buffer.'</pre></code>';
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($buffer));
            return $response;
        } catch (ProcessFailedException $exception) {
            $buffer = $exception->getMessage();
            $buffer = '<code><pre>'.$buffer.'</pre></code>';
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($buffer));
            return $response;
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($result));
        return $response;
    }

    /**
     * @Route("/dev-mode", name="employees_dev_mode", methods={"GET"})
     */
    public function runDevModeAction(Request $request) {

        if( false === $this->isGranted('ROLE_PLATFORM_ADMIN') ) {
            return $this->redirect($this->generateUrl('employees-nopermission'));
        }

        $baseUrl = $request->getBaseURL();
        //echo "baseUrl=$baseUrl <br>";
        if( !str_contains($baseUrl, 'index_dev.php') ) {
            $baseUrl = $baseUrl . '/index_dev.php';
            return $this->redirect($baseUrl);
        }
        //exit();

        $request->getSession()->getFlashBag()->add(
            'notice',
            "You are already in the development mode"
        );

        return $this->redirect($baseUrl);
    }

//    /**
//     * @Route("/", name="employees_home")
//     * @Template("AppUserdirectoryBundle/Default/home.html.twig")
//     */
//    public function indexAction()
//    {
//
//        if(
//            false == $this->isGranted('ROLE_USER') ||              // authenticated (might be anonymous)
//            false == $this->isGranted('IS_AUTHENTICATED_FULLY')    // authenticated (NON anonymous)
//        ){
//            return $this->redirect( $this->generateUrl('login') );
//        }
//
//        //$form = $this->createForm(new SearchType(),null);
//
//        //$form->submit($request);  //use bind instead of handleRequest. handleRequest does not get filter data
//        //$search = $form->get('search')->getData();
//
//        //check for active access requests
//        $accessreqs = $this->getActiveAccessReq();
//
//
//        return array(
//            'accessreqs' => count($accessreqs)
//            //'form' => $form->createView(),
//        );
//    }
//
//    //check for active access requests
//    public function getActiveAccessReq() {
//        if( !$this->isGranted('ROLE_USERDIRECTORY_ADMIN') ) {
//            return null;
//        }
//        $userSecUtil = $this->container->get('user_security_utility');
//        $accessreqs = $userSecUtil->getUserAccessRequestsByStatus($this->getParameter('employees.sitename'),AccessRequest::STATUS_ACTIVE);
//        return $accessreqs;
//    }


//    /**
//     * @Route("/admin", name="employees_admin")
//     * @Template("AppUserdirectoryBundle/Default/index.html.twig")
//     */
//    public function adminAction()
//    {
//        $name = "This is an Employee Directory Admin Page!!!";
//        return array('name' => $name);
//    }
//
//
//    /**
//     * @Route("/hello/{name}", name="employees_hello")
//     * @Template()
//     */
//    public function helloAction($name)
//    {
//        return array('name' => $name);
//    }


    /**
     * https://collage.med.cornell.edu/order/directory/fix-author-generated-users/
     *
     * @Route("/fix-author-generated-users/", name="employees_fix-author-generated-users")
     */
    public function fixAuthorGeneratedUsersAction()
    {
        exit("Not allowed. This is one time run script to fix added by for already generated users.");

        if( false === $this->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            return $this->redirect($this->generateUrl('employees-nopermission'));
        }

        $em = $this->getDoctrine()->getManager();

        //get generated users by createdby
        //$createdBy = "manual-".$sitename;
        $repository = $em->getRepository('AppUserdirectoryBundle:User');
        $dql = $repository->createQueryBuilder("user");
        $dql->where("user.createdby LIKE '%manual-%'");
        $query = $em->createQuery($dql);
        $users = $query->getResult();
        echo "Generated users count=".count($users)."<br>";

        foreach($users as $user) {
            echo "user=".$user.": ";

            $author = $this->getAuthorFromLogger($user);
            if( $author ) {
                $user->setAuthor($author);
                $em->flush();
                echo "Update author=".$author."<br>";
            } else {
                echo "Author is not found in logger<br>";
            }
        }

        exit("EOF generated users");
    }
    public function getAuthorFromLogger($user) {
        $em = $this->getDoctrine()->getManager();

        //get the date from event log
        $repository = $em->getRepository('AppUserdirectoryBundle:Logger');
        $dql = $repository->createQueryBuilder("logger");


        $dql->where("logger.entityName = 'User' AND logger.entityId = '".$user->getId()."'");

        //$dql->andWhere("logger.event LIKE '%"."status changed to '/Unpaid/Issued"."%'"); //status changed to 'Unpaid/Issued'
        //$dql->andWhere("logger.event LIKE :eventStr OR logger.event LIKE :eventStr2");
        $dql->andWhere("logger.event LIKE :eventStr OR logger.event LIKE :eventStr2");

        $dql->orderBy("logger.id","DESC");
        $query = $em->createQuery($dql);

        $search = "User account for ";
        $search2 = "has been created by";

        $query->setParameters(
            array(
                'eventStr' => '%'.$search.'%',
                'eventStr2' => '%'.$search2.'%'
            )
        );

        $loggers = $query->getResult();

        if( count($loggers) > 0 ) {
            $logger = $loggers[0];

            $author = $logger->getUser();
            return $author;
        }

        return NULL;
    }

    /**
     * https://collage.med.cornell.edu/order/directory/fix-author-generated-users/
     *
     * @Route("/some-testing/", name="employees_some_testing")
     */
    public function someTestingAction() {

        //exit("disabled");

        if( false === $this->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            return $this->redirect($this->generateUrl('employees-nopermission'));
        }

        $em = $this->getDoctrine()->getManager();

        if(0) {
            echo "############### <br>";
            $comments = $em->getRepository('AppUserdirectoryBundle:FosComment')->findAll();
            echo "Count comments=" . count($comments) . "<br>";
            echo "############### <br><br>";

            echo "<br> ######## Get single comment by find() ####### <br>";
            $comment = $em->getRepository('AppUserdirectoryBundle:FosComment')->find(14576);
            echo $comment->getId() . ": body=[" . $comment->getBody() . "], comment=[" . $comment->getCommentShort() . "], threadId=[" . $comment->getThread()->getId() . "]" .
                ", entityId=" . $comment->getEntityId() .
                "<br>";
            if ($comment->getBody() === NULL) {
                echo "body is NULL <br>";
            }
            if ($comment->getBody() === '') {
                echo "body is '' <br>";
            }
            if (!$comment->getBody()) {
                echo "body is empty <br>";
            }
            echo "Comment:" . get_class($comment) . "<br>";
            echo "####### EOF Get single comment by find() ######## <br><br>";
            //rawBody
            //exit("EOF Test comment");
        }

        $id = 'transres-Project-3358-admin_review';
        //$thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $thread = $this->container->get('user_comment_utility')->findThreadById($id);

        $commentAtStr = "";
        if($thread->getLastCommentAt()) {
            $commentAtStr = $thread->getLastCommentAt()->format('d-m-Y H:i:s');
        }

        echo $thread->getId().
            ": permalink=".$thread->getPermalink().
            ", isCommentable=".$thread->isCommentable().
            ", numComments=".$thread->getNumComments().
            ", lastCommentAt=".$commentAtStr. //($thread->getLastCommentAt()) ? $thread->getLastCommentAt()->format('d-m-Y H:i:s') : ''.
            "<br>";

        //$comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);
        $comments = $this->container->get('user_comment_utility')->findCommentTreeByThread($thread);

        dump($comments);
        exit('111');

        $count = 0;
        foreach($comments as $comment) {
            echo $comment->getId().": body=[".$comment->getBody()."], comment=[".$comment->getCommentShort()."], threadId=[".$comment->getThread()->getId()."]<br>";
            $count++;
        }
        exit("EOF Test manager comments, COUNT=".$count);

        echo "############### <br>";
        $comments = $em->getRepository('AppUserdirectoryBundle:FosComment')->findAll();
        echo "comments=".count($comments)."<br>";
        $count = 0;
        foreach($comments as $comment) {
            echo $comment->getId().": body=[".$comment->getBody()."], comment=[".$comment->getCommentShort()."], threadId=[".$comment->getThread()->getId()."]<br>";
            $count++;
        }
        exit("EOF Test comments, COUNT=".$count);

        //Test 1
        $em = $this->getDoctrine()->getManager();
        $fellappRecLetterUtil = $this->container->get('fellapp_rec_letter_util');
        $letterOne = $em->getRepository('AppUserdirectoryBundle:Document')->findOneById(877);
        $letterOnePath = $letterOne->getServerPath();
        $letterTwo = $em->getRepository('AppUserdirectoryBundle:Document')->findOneById(875);
        $letterTwoPath = $letterTwo->getServerPath();
        $identical = $fellappRecLetterUtil->checkIfFilesIdentical($letterOnePath,$letterTwoPath,$fileTwoHash=null);
        if( $identical ) {
            echo "Files are identical <br>";
        } else {
            echo "Files are diiferent <br>";
        }

        exit("EOF Test 1");

        //Test 2
        $fellappRecLetterUtil = $this->container->get('fellapp_rec_letter_util');
        $googlesheetmanagement = $this->container->get('fellapp_googlesheetmanagement');
        $service = $googlesheetmanagement->getGoogleService();

        $folderId = "1ex5Yh8nJia8WUQ7eTkSnM1OS9Z18J2Oz"; //created 12:48 PM Jul 16
        $files = $googlesheetmanagement->retrieveFilesByFolderId($folderId,$service);

        $count = 0;
        foreach($files as $file) {
            $goolgeDateTime = $fellappRecLetterUtil->getGoogleFileCreationDatetime($service, $file->getId());
            if( $count++ > 3 ) {
                break;
            }
        }

        exit("EOF Test 2");

    }


    /**
     * @Route("/login-testing/{username}/{password}/", name="employees_login_testing")
     */
    public function loginTestingAction( Request $request, $username, $password ) {

        exit("disabled");

        if( false === $this->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            return $this->redirect($this->generateUrl('employees-nopermission'));
        }

        $authUtil = $this->container->get('authenticator_utility');
        $authUtil->laminasBind($username,$password);

        //$this->loginLaminasTest($username,$password);

        //$this->loginTest($username,$password);
        //$this->loginSymfonyTest($username,$password);


        exit("EOF Login Testing");

    }

//    //It might work
//    //remove: fabiang/sasl symfony/ldap
//    public function loginLaminasTest( $thisUser, $password ) {
//
//        echo "username=[$thisUser], password=[$password] <br>";
//
//        $host = 'a.wcmc-ad.net';
//
//        $options = [
//            'host'              => $host,
//            //'username'          => 'xxx',
//            //'password'          => 'xxx',
//            //'bindRequiresDn'    => false,
//            'accountDomainName' => $host,
//            'baseDn'            => 'dc=a,dc=wcmc-ad,dc=net',
//            //'useSsl'            => true,
//            //'useStartTls'      => true
//        ];
//
//        $ldap = new \Laminas\Ldap\Ldap($options);
//        $ldap->bind($thisUser, $password);
//
//        dump($ldap);
//        //exit('EOF');
//
//        //$acctname = $ldap->getCanonicalAccountName($thisUser, \Laminas\Ldap\Ldap::ACCTNAME_FORM_DN);
//
//        $acctname = $ldap->getCanonicalAccountName($thisUser, \Laminas\Ldap\Ldap::ACCTNAME_FORM_DN);
//        echo "acctname=[$acctname] <br>";
//
//        //dump($acctname);
//
//        echo "EOF loginLaminasTest <br>";
//        exit('EOF');
//    }

    public function loginFabiangTest( $thisUser, $password ) {

        echo "username=[$thisUser], password=[$password] <br>";

        $host = 'a.wcmc-ad.net';

        $factory = new Sasl();

        $mechanism = $factory->factory('SCRAM-SHA-1', array(
            'authcid'  => $thisUser,
            'secret'   => $password,
            //'authzid'  => 'authzid', // optional. Username to proxy as
            //'service'  => 'servicename', // optional. Name of the service
            'hostname' => $host, // optional. Hostname of the service
        ));

        $response = $mechanism->createResponse();

        dump($response);

        exit('EOF');
    }

//    public function loginSymfonyTest( $thisUser, $password ) {
//        $host = 'a.wcmc-ad.net';
//
//        //$options = 'X_SASL_MECH'; //array(X_SASL_MECH);
//
//        $encryption = 'none';
//        //$encryption = 'ssl';
//        //$encryption = 'tls';
//
//        $ldap = Ldap::create('ext_ldap', [
//            'host' => $host,
//            'port' => 389,
//            'version' => 3,
//            //'encryption' => $encryption,
//            //'options' => $options
//            //'options' => array(x_sasl_mech)
//            //'x_sasl_mech'
//        ]);
//
//        //$ldap = Ldap::create('ext_ldap', ['connection_string' => 'ldaps://$host:636']);
//
//        //$dn = "OU=NYP Users,OU=External,DC=a,DC=wcmc-ad,DC=net";
//        $dn = "cn=Users,DC=a,DC=wcmc-ad,DC=net";
//        //$dn = "DC=a,DC=wcmc-ad,DC=net";
//
//        $dn = "cn=".$thisUser.",".$dn;
//
//        //$dn = $thisUser;
//        echo "dn: [$dn]<br>";
//        echo "password=[$password]<br>";
//
//        //$dn = "CN=xxx,OU=NYP Users,OU=External,DC=a,DC=wcmc-ad,DC=net";
//        //$dn = "CN=xxx";
//        //$dn = "CN=xxx,DC=a,DC=wcmc-ad,DC=net";
//        //$password = "xxx";
//
//        //$dn = "CN=xxx,OU=NYP Users,OU=External,DC=a,DC=wcmc-ad,DC=net";
//        //$password = "xxx";
//
//        $r = $ldap->bind($dn, $password);
//
//        dump($r);
//
////        if( $r ) {
////            exit('OK');
////        } else {
////            exit('NOT OK');
////        }
//
//        echo "EOF loginSymfonyTest <br>";
//        //exit('EOF loginSymfonyTest');
//    }


    public function loginTest_php( $thisUser, $password ) {

        //$thisUser = $_SERVER['REMOTE_USER'];
        //$thisUser = "nyptestuser1";
        $thisServer = 'a.wcmc-ad.net';

        # Bind to the directory Server
        $ldap = ldap_connect("ldap://$thisServer");
        if($ldap) {
            $r = ldap_bind($ldap);
        } else {
            echo "Unable to connect to $thisServer!";
        }

        # Set an option
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

        echo "<h1>Kerberos Credentials</h1>\n";
        echo "<pre>\n";
        system('klist');
        echo "</pre>\n";

        $dn = "DC=a,DC=wcmc-ad,DC=net";

        $r=ldap_sasl_bind ( $ldap, NULL, $password, 'DIGEST-MD5', NULL, $thisUser, $dn);

        if( $r ) {
            exit('OK');
        } else {
            exit('NOT OK');
        }

        exit('111');

        # Bind using the default Kerberos credentials
        //if (ldap_sasl_bind($ldap,"","","GSSAPI")) {
        if (ldap_sasl_bind($ldap,$thisUser,$password,"GSSAPI")) {

            # Search the Directory
            //$dn = "cn=people,dc=stanford,dc=edu";
            //$filter = "(|(uid=$thisUser)(mail=$thisUser@*))";
            //DistinguishedName="DC=a,DC=wcmc-ad,DC=net" SearchFilter="(objectClass=user)"
            //AttributeNameUUID="objectGuid" AttributeNameUser="sAMAccountName" PageSize="10">
            $dn = "DC=a,DC=wcmc-ad,DC=net";
            $filter = "(objectClass=user)";
            echo "<h1>LDAP Search</h1>\n";
            echo "Host: $thisServer<br />\n";
            echo "Base DN: $dn<br />\n";
            echo "Filter: $filter<br />\n";
            echo "REMOTE_USER: $thisUser<br />\n";

            $result = ldap_search($ldap, $dn, $filter);
            if ($result) {
                echo "<blockquote>\n";
                $cnt = ldap_count_entries($ldap, $result);
                echo "Number of entries returned is $cnt<br />\n";
                $info = ldap_get_entries($ldap,$result);
                echo "Data for " . $info["count"] . " items returned:<p>";
                print("\n");
                for($i=0;$i<$info["count"];$i++) {
                    echo "dn is: " . $info[$i]["dn"] . "<br />";
                    print("\n");
                    echo "first cn entry is: " . $info[$i]["cn"][0] . "<br />";
                    print("\n");
                    echo "first email is: " . $info[$i]["mail"][0] . "<br /> <hr />";
                    print("\n");
                }
                echo "</blockquote>\n";
            }
        } else {
            echo '<font color="red">Bind to the directory failed.</font>'."\n";
        }

        ldap_close($ldap);

    }

    /**
     * @Route("/email-testing/", name="employees_email_testing")
     */
    public function emailTestingAction() {

//        $inputEmails = "e1,e2";
//        $inputEmails = "e1";
//        $inputEmailArr = explode(',',$inputEmails);
//        dump($inputEmailArr);
//        exit("emailTestingAction");

        if( false === $this->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            return $this->redirect($this->generateUrl('employees-nopermission'));
        }

        $em = $this->getDoctrine()->getManager();
        $emailUtil = $this->container->get('user_mailer_utility');

        $ccs = NULL;

        $email = "oli2002@med.cornell.edu,cinava@yahoo.com,cinava@yahoo.com,oli2002@med.cornell.edu, ,,";
        //$email = "oli2002@med.cornell.edu,cinava@yahoo.com";
        //$email = "oli2002@med.cornell.edu";
        $ccs = "cinava@yahoo.com,cinava@yahoo.com,oli2002@med.cornell.edu,oleg_iv@yahoo.com";

        $fromEmail = NULL; //"cinava@yahoo.com"; //NULL;

        $invoice = NULL;
        $userSecUtil = $this->container->get('user_security_utility');
        $environment = $userSecUtil->getSiteSettingParameter('environment');
        if ($environment == "dev") {
            $invoice = $em->getRepository('AppTranslationalResearchBundle:Invoice')->find(4760); //dev
        }
        if ($environment == "test") {
            $invoice = $em->getRepository('AppTranslationalResearchBundle:Invoice')->find(4730); //test
        }
        if ($environment == "live") {
            $invoice = $em->getRepository('AppTranslationalResearchBundle:Invoice')->find(7323); //prod
        }
        if (!$invoice) {
            exit("Invoice not defined for environment=$environment");
        }
        $invoicePDF = $invoice->getRecentPDF();
        $attachmentPath = $invoicePDF->getAttachmentEmailPath();
        $attachmentFilename = null;//"invoiceAttachment"; //null; //"invoiceAttachment";

        $subject = "Test Invoice Subject";
        $body = "Test Invoice Body \r\n New line1 <br> new line 2";

        //$emails, $subject, $body, $ccs=null, $fromEmail=null, $attachmentPath=null, $attachmentFilename=nul

        $resEmail = $emailUtil->sendEmail($email, $subject, $body, $ccs, $fromEmail, $attachmentPath, $attachmentFilename);
        //echo "resEmail=$resEmail <br>";

        //$res = $invoice->getId() . ": attachmentPath=$attachmentPath <br>";

        //echo "res=$res <br>";

        exit("EOF emailTestingAction");

    }

    /**
     * @Route("/email-testing-plain")
     */
    public function sendEmail(MailerInterface $mailer): Response
    {
        //dump($mailer);
        //exit('111');

        $email = (new Email())
            //->from('oli2002@med.cornell.edu')
            ->from('cinava@yahoo.com')
            ->to('oli2002@med.cornell.edu')
            ->to('cinava@yahoo.com')
            //->cc('cinava@yahoo.com')
            //->cc(new Address('cinava@yahoo.com'))
            //->bcc(new Address('cinava@yahoo.com'))
            //->bcc('cinava@yahoo.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $res = $mailer->send($email);

        exit('res='.$res);
    }


    //testing getCronJobFullNameLinux
    /**
     * @Route("/some-other-testing")
     */
    public function someTestAction(MailerInterface $mailer): Response
    {
        //dump($mailer);
        //exit('111');

        $userServiceUtil = $this->container->get('user_service_utility');
        $commandName = 'webmonitor.py'; //"independentmonitor";
        $res = $userServiceUtil->getCronStatus($commandName,false); //getCronStatus -> getCronStatusLinux -> getCronJobFullNameLinux (add cron:)


        exit('res='.$res);
    }

    /**
     * @Route("/get-ad-users")
     */
    public function getADUsersAction()
    {
        if( false === $this->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN') ) {
            return $this->redirect($this->generateUrl('employees-nopermission'));
        }

        $authUtil = $this->container->get('authenticator_utility');

        $ldapType=1;
        $withWarning=true;
        $username = 'oli2002';
        $username = 'aabccc';
        //$username = '*';

        //$cwids = array('petrova_kseniya_621');
        //$res = $authUtil->getADUsers($username, $ldapType, $withWarning, $cwids);
        //('111');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppUserdirectoryBundle:User');
        $dql =  $repository->createQueryBuilder("user");
        $dql->select('user');
        $dql->leftJoin("user.infos","infos");

        $dql->leftJoin("user.employmentStatus", "employmentStatus");
        $dql->leftJoin("employmentStatus.employmentType", "employmentType");
        //$dql->where("employmentType.name != 'Pathology Fellowship Applicant' OR employmentType.id IS NULL");
        //$dql->andWhere("LOWER(infos.email) LIKE '%nyp%'");
        $query = $em->createQuery($dql);
        //$query->setMaxResults(5000);
        $users = $query->getResult();
        echo "usesr count=".count($users)."<br>";

        $cwids = array();
        foreach($users as $user) {
            $cwids[] = $user->getCleanUsername();
        }

        $res = $authUtil->getADUsersByCwids($cwids,$ldapType,$withWarning); //getCronStatus -> getCronStatusLinux -> getCronJobFullNameLinux (add cron:)

        dump($res);

        exit('111');
    }

    /**
     * @Route("/check-ad-users")
     */
    public function checkUsersADAction()
    {
        if (false === $this->isGranted('ROLE_PLATFORM_DEPUTY_ADMIN')) {
            return $this->redirect($this->generateUrl('employees-nopermission'));
        }

        $authUtil = $this->container->get('authenticator_utility');
        $adCount = $authUtil->checkUsersAD(1,true,0);
        
        exit("User count in AD: $adCount");
    }

}
