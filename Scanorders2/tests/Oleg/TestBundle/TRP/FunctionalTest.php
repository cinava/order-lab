<?php

namespace Tests\Oleg\TestBundle\TRP;

use Tests\Oleg\TestBundle\WebTestBase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

//./bin/simple-phpunit tests/Oleg/TranslationalResearchBundle/Controller/TranslationalResearchControllerTest.php

class FunctionalTest extends WebTestBase
{

    public function testHomeAction() {
        $this->getClient();
        $crawler = $this->client->request('GET', '/translational-research/login');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Please use your CWID to log in")')->count()
        );
    }

    public function testPackingSlip() {
        //return;

        if(1) {
            $this->logIn();
        } else {
            $this->client = static::createClient([], [
                'HTTP_HOST' => '127.0.0.1',
                'HTTP_USER_AGENT' => 'MySuperBrowser/1.0',
            ]);
        }

        //http://localhost/order/directory/login
        //$crawler = $this->client->request('GET', 'directory/');
        //$crawler = $this->client->request('GET', '/directory/under-construction');
        //$crawler = $this->client->request('GET', 'http://127.0.0.1/order/directory/under-construction');

        //http://127.0.0.1/order/translational-research/work-request/download-packing-slip-pdf/1
        //$crawler = $this->client->request('GET', 'http://127.0.0.1/translational-research/work-request/download-packing-slip-pdf/1');
        //$crawler = $this->client->request('GET', '/translational-research/work-request/download-packing-slip-pdf/1');

        $transresUtil = $this->container->get('transres_util');
        $requests = $transresUtil->getTotalRequests();
        if( count($requests) > 0 ) {

            $transRequest = end($requests);
            $requestId = $transRequest->getId();

            $crawler = $this->client->request('GET', '/translational-research/work-request/download-packing-slip-pdf/'.$requestId);

            $this->assertGreaterThan(
                0,
                $crawler->filter('html:contains("Packing Slip")')->count()
            );


//            //Generate Packing Slip
//            $authorUser = $this->user;
//            $request = $this->client->getRequest();
//            $transresPdfUtil = $this->container->get('transres_pdf_generator');
//            $res = $transresPdfUtil->generatePackingSlipPdf($transRequest,$authorUser,$request);
//            $size = $res['size'];
//            // assert that size is greater than zero
//            $this->assertGreaterThan(100, $size);

        }

        //$uri = $this->client->getRequest()->getUri();
        //echo "uri=$uri \r\n";
        //exit("uri=$uri");

        //$content = $this->client->getResponse()->getContent();
        //exit("home content=$content");

//        $this->assertGreaterThan(
//            0,
//            $crawler->filter('html:contains("Packing Slip")')->count()
//        );

    }

    public function testAboutAction() {
        $this->logIn();
        $crawler = $this->client->request('GET', '/translational-research/about');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Current Version")')->count()
        );
        //$linkName = '/translational-research/about';
        //$this->testGetLink($linkName,"Current Version");
        //$this->testGetLink($linkName);
    }

    public function testProjectAction() {
        $this->logIn();
        $crawler = $this->client->request('GET', '/translational-research/projects/');
        //$content = $this->client->getResponse()->getContent();
        //exit("content=$content");
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Project Requests")')->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("New Project Request")')->count()
        );
    }

    public function testNewProjectAction() {
        $this->logIn();
        $crawler = $this->client->request('GET', '/translational-research/project/new/ap-cp');
        //$content = $this->client->getResponse()->getContent();
        //exit("content=$content");
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("AP/CP Project Request")')->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Save Draft Project Request")')->count()
        );
    }

    public function testRequestAction() {
        $this->logIn();
        $crawler = $this->client->request('GET', '/translational-research/work-requests/list/?filter[progressState][0]=All-except-Drafts-and-Canceled&title=All Work Requests');
        //$content = $this->client->getResponse()->getContent();
        //exit("content=$content");
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("All Work Requests")')->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Products/Services")')->count()
        );
    }

    public function testNewRequestAction() {
        $this->logIn();
        $crawler = $this->client->request('GET', '/translational-research/work-request/new/');
        //$content = $this->client->getResponse()->getContent();
        //exit("content=$content");
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("New Work Request")')->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Save Work Request as Draft")')->count()
        );
    }

    public function testNewInvoiceAction() {
        $this->logIn();
        //$this->client->followRedirects();

        $transresUtil = $this->container->get('transres_util');
        $requests = $transresUtil->getTotalRequests();
        if( count($requests) > 0 ) {
            $request = end($requests);
            $requestId = $request->getId();
            //echo "requestID=$requestId \n\r";

            $crawler = $this->client->request('GET', '/translational-research/invoice/new/'.$requestId);
            //$content = $this->client->getResponse()->getContent();
            //exit("content=$content");

            $this->assertGreaterThan(
                0,
                $crawler->filter('html:contains("New Invoice for the Request")')->count()
            );

            $this->assertGreaterThan(
                0,
                $crawler->filter('html:contains("Show associated invoices for the same work request")')->count()
            );
        }

//        $crawler = $this->client->request('GET', '/translational-research/invoice/new/1');
//        $content = $this->client->getResponse()->getContent();
//        exit("content=$content");
//        $this->assertGreaterThan(
//            0,
//            $crawler->filter('html:contains("List of Invoices")')->count()
//        );
//        $this->assertGreaterThan(
//            0,
//            $crawler->filter('html:contains("New Project Request")')->count()
//        );
    }


//    public function testUnderConstruction() {
//        //under-construction
//        $this->logIn();
//        //$client = static::createClient();
//
//        //http://localhost/order/directory/login
//        //$crawler = $this->client->request('GET', '/translational-research/login');
//        $crawler = $this->client->request('GET', '/order/directory/under-construction');
//
//        $uri = $this->client->getRequest()->getUri();
//        echo "under-construction uri=$uri \r\n";
//        //exit("uri=$uri");
//
//        //$content = $this->client->getResponse()->getContent();
//        //exit("content=$content");
//
//        $this->assertGreaterThan(
//            0,
//            $crawler->filter('html:contains("Currently Undergoing Maintenance")')->count()
//        //$crawler->filter('html:contains("The following sites are available")')->count()
//        //$crawler->filter('html:contains("Please use your")')->count()
//        );
//
//        exit("exit under-construction");
//    }

//    public function testLoginProcess()
//    {
//        return;
//
//        $client = static::createClient();
//        $client->followRedirects();
//
//        //$cookie = new Cookie('locale2', 'en', time() + 3600 * 24 * 7, '/', null, false, false);
//        //$client->getCookieJar()->set($cookie);
//
//        $_SERVER['HTTP_USER_AGENT'] = 'phpunit test';
//
//        // Visit user login page and login
//        $crawler = $client->request('GET', '/order/directory/login');
//
//        echo "\n\n\nclient response:\n\n\n";
//        //echo $crawler->html();
//        //var_dump($crawler->links());
//        print_r($client->getResponse()->getContent());
//        echo "\n\n\n";
//        exit('Exit on login page');
//
//        $uri = $client->getRequest()->getUri();
//        echo "login uri=$uri \r\n";
//        //exit('000 crawler');
//        //test if login page is opened
//        //$this->assertTrue($client->getResponse()->isSuccessful());
//        //exit('000 assertTrue');
//        // Select based on button value, or id or name for buttons
//        $form = $crawler->selectButton('Log In')->form();
//
//        // set some values
//        $form['_username'] = 'username';
//        $form['_password'] = 'pa$$word';
//
//        //$form['_username'] = '';
//        // $form['_password'] = '';
//
//        //$client->insulate();
//
//        // submit the form
//        $crawler = $client->submit($form);
//
//        //$this->assertTrue($client->getResponse()->isSuccessful());
//        exit('000');
//        echo "\n\n\nclient response:\n\n\n";
//        //echo $crawler->html();
//        //var_dump($crawler->links());
//        print_r($client->getResponse()->getContent());
//        echo "\n\n\n";
//        exit('111');
//
//        $this->assertGreaterThan(
//            0,
//            $crawler->filter('html:contains("Welcome to the Scan Order System")')->count()
//        );
//
//        $crawler = $client->request('GET', '/order/directory/');
//
//        $this->assertTrue($client->getResponse()->isSuccessful());
//
//
//        //$this->assertEquals('Hello', 'Hello');
//
////        echo "client response:<br>";
////        var_dump($client->getResponse()->getContent());
////        echo "<br>";
//
//        $this->assertGreaterThan(
//            0,
//            $crawler->filter('html:contains("Welcome to the Employee Directory!")')->count()
//        );
//
//
//        //test form submit
//        $crawler = $client->request('GET', '/order/directory/new');
//
////        echo "client response:<br>";
////        var_dump($client->getResponse()->getContent());
////        echo "<br>";
//
//        $this->assertTrue($client->getResponse()->isSuccessful());
//
//        $this->assertGreaterThan(
//            0,
//            $crawler->filter('html:contains("Create New User")')->count()
//        );
//
//
////        //$next = $crawler2->selectButton('Next')->link();
////        //$next = $crawler->filter('button:contains("Next")')->eq(1)->link();
////        //$crawler2 = $client->click($next);
////
////        $form = $crawler->selectButton('btnSubmit')->form();
////
////        $form['oleg_orderformbundle_messagetype[patient][0][encounter][0][procedure][0][accession][0][part][0][block][0][slide][0][title]'] = 'Slide submitted by phpunit test';
////
////        $form['oleg_orderformbundle_messagetype[patient][0][encounter][0][procedure][0][accession][0][part][0][block][0][slide][0][slidetype]'] = 7;
////
////        $form['oleg_orderformbundle_messagetype[patient][0][clinicalHistory][0][field]'] = 'clinical history test';
////
////        $form['oleg_orderformbundle_messagetype[patient][0][mrn][0][field]'] = '0000000';
////
////
////
////        $_POST['btnSubmit'] = "btnSubmit";
////
////        //sleep(10);
////
////        $crawler = $client->submit($form);
////
//////        echo "client response:<br>";
//////        var_dump($client->getResponse()->getContent());
//////        echo "<br>";
////        //exit();
////
////        $this->assertTrue($client->getResponse()->isSuccessful());
////
////        $this->assertGreaterThan(
////            0,
////            $crawler->filter('html:contains("Thank you for your order")')->count()
////        );
//    }







}
