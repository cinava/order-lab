<?php

/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 12/10/2019
 * Time: 2:50 PM
 */

//./bin/simple-phpunit tests/Oleg/TranslationalResearchBundle/Util/TransResUtilTest.php

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

//class TransResUtilTest extends KernelTestCase
class TransResUtilTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $container;
    private $client = null;
    private $user = null;

    protected function setUp()
    {
//        $kernel = self::bootKernel();
//
//        $this->container = $kernel->getContainer();
//
//        $this->em = $this->container
//            ->get('doctrine')
//            ->getManager();
//
//        $this->client = static::createClient([], [
//            'HTTP_HOST'       => '127.0.0.1',
//            'HTTP_USER_AGENT' => 'MySuperBrowser/1.0',
//        ]);

        //$this->client = static::createClient();
        $this->client = static::createClient([], [
            'HTTP_HOST'       => '127.0.0.1',
            'HTTP_USER_AGENT' => 'MySuperBrowser/1.0',
        ]);

        $this->container = $this->client->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');

        $this->user = $this->createAuthorizeClient();
    }

    public function testGeneratePackingSlipPdf() {

        return;

        //$transresRequestUtil = $this->container->get('transres_request_util');
        $transresPdfUtil = $this->container->get('transres_pdf_generator');

        $requestsId = 1;
        $transresRequest = $this->em->getRepository('OlegTranslationalResearchBundle:TransResRequest')->find($requestsId);


        echo "transresRequest ID=".$transresRequest->getId().", OID=".$transresRequest->getOid()."\n";

        $oidTest = false;
        if (strpos($transresRequest->getOid(), '-REQ') !== false) {
            $oidTest = true;
        }
        $this->assertTrue($oidTest);


        //Generate Packing Slip
        $authorUser = null;
        $request = null;
        $res = $transresPdfUtil->generatePackingSlipPdf($transresRequest,$authorUser,$request);

        $filename = $res['filename'];
        //$pdf = $res['pdf'];
        $size = $res['size'];

        // assert that size is greater than zero
        $this->assertGreaterThan(100, $size);
    }

    public function testGetAvailableProjects() {

        if(1) {
            $this->logIn();
        } else {
            $this->client = static::createClient([], [
                'HTTP_HOST' => '127.0.0.1',
                'HTTP_USER_AGENT' => 'MySuperBrowser/1.0',
            ]);
        }

        echo "testGetAvailableProjects \r\n";

        echo "User=".$this->user."\r\n";

        //$transresRequestUtil = $this->container->get('transres_request_util');
        $transresUtil = $this->container->get('transres_util');
        $projects = $transresUtil->getAvailableRequesterOrReviewerProjects();

        $this->assertGreaterThan(1000, count($projects));

        $requests = $transresUtil->getTotalRequestCount();
        $this->assertGreaterThan(1000, count($requests));

    }

    public function testAdd()
    {
        $result = 30+12;

        // assert that your calculator added the numbers correctly!
        $this->assertEquals(42, $result);
    }


//    public function testComp()
//    {
//        $this->assertTrue(false);
//    }




    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'external_ldap_firewall';
        // if you don't define multiple connected firewalls, the context defaults to the firewall name
        // See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
        $firewallContext = 'scan_auth';

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new UsernamePasswordToken('administrator', null, $firewallName, array('ROLE_ADMIN','ROLE_PLATFORM_ADMIN'));
        $session->set('_security_'.$firewallContext, serialize($token));
        //$securityContext = "scan_auth";
        //$securityContext = '_security_'.$firewallContext; //_security_scan_auth
        //$securityContext = '_security_scan_auth';
        //$session->set($securityContext, serialize($token));

        //$firewallName = 'ldap_employees_firewall';
        //$token = new UsernamePasswordToken('oli2002', null, 'ldap_employees_firewall', ['ROLE_PLATFORM_ADMIN']);
        //$session->set('_security_scan_auth'.$firewallContext, serialize($token));

        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
    public function createAuthorizeClient()
    {
        $session = $this->container->get('session');
        $user = $this->em->getRepository('OlegUserdirectoryBundle:User')->findOneByUsername('administrator');

        return $user;
        // rest of the class here
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks

        $this->container = null; // avoid memory leaks

        $this->client = null;
    }

}