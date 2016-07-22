<?php
namespace Oleg\UserdirectoryBundle\Util;

use Oleg\UserdirectoryBundle\Util\UserUtil;

/**
 * Description of EmailUtil
 *
 * @author Cina
 */
class EmailUtil {

    protected $em;
    protected $sc;
    protected $container;

    public function __construct( $em, $sc, $container ) {
        $this->em = $em;
        $this->sc = $sc;
        $this->container = $container;
    }

    //[2016-06-24 14:20:39] request.CRITICAL: Uncaught PHP Exception Swift_TransportException: "Connection to smtp.med.cornell.edu:25 Timed Out" at E:\Program Files (x86)\Aperio\Spectrum\htdocs\order\scanorder\Scanorders2\vendor\swiftmailer\swiftmailer\lib\classes\Swift\Transport\AbstractSmtpTransport.php line 404 {"exception":"[object] (Swift_TransportException(code: 0): Connection to smtp.med.cornell.edu:25 Timed Out at E:\\Program Files (x86)\\Aperio\\Spectrum\\htdocs\\order\\scanorder\\Scanorders2\\vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\AbstractSmtpTransport.php:404)"} []
    //one possible solution: http://stackoverflow.com/questions/25449496/swiftmailer-gmail-connection-timed-out-110
    //$smtp_host_ip = gethostbyname('smtp.gmail.com');
    //$transport = Swift_SmtpTransport::newInstance($smtp_host_ip,465,'ssl')

    //$emails: single or array of emails
    //$ccs: single or array of emails
    public function sendEmail( $emails, $subject, $message, $ccs=null, $fromEmail=null ) {

        //testing
        //$emails = "oli2002@med.cornell.edu, cinava@yahoo.com";
        //$emails = "oli2002@med.cornell.edu";
        //$ccs = null;

        $logger = $this->container->get('logger');
        //set_time_limit(0); //set time limit to 600 sec == 10 min
        $userutil = new UserUtil();

        //echo "emails=".$emails."<br>";
        //echo "ccs=".$ccs."<br>";

        if( $this->hasConnection() == false ) {
            //exit('no connection');
            return false;
        }
        //exit('yes connection');

        if( !$emails || $emails == "" ) {
            return false;
        }

        if( !$message || $message == "" ) {
            return false;
        }

        if( !$fromEmail ) {
            $fromEmail = $userutil->getSiteSetting($this->em,'siteEmail');
        }

        $emails = $this->checkEmails($emails);
        $ccs = $this->checkEmails($ccs);

        if( $this->em ) {
            $smtpServerAddress = $userutil->getSiteSetting($this->em,'smtpServerAddress');
            $smtp_host_ip = gethostbyname($smtpServerAddress);
            //$logger->notice("smtpServerAddress=".$smtpServerAddress." => smtp_host_ip=".$smtp_host_ip);
            $transport = \Swift_Message::newInstance($smtp_host_ip);
        } else {
            $logger->error("this->em is null in sendEmail: use default Swift_Message::newInstance(). subject=".$subject);
            $transport = \Swift_Message::newInstance();
        }

        $transport->setSubject($subject);
        $transport->setFrom($fromEmail);
        $transport->setTo($emails);
        $transport->setBody(
            $message,
            'text/plain'
        );

        if( $ccs ) {
            $transport->setCc($ccs);
        }

        //send copy email to siteEmail via setBcc
        $userSecUtil = $this->container->get('user_security_utility');
        $siteEmail = $userSecUtil->getSiteSettingParameter('siteEmail');
        if( $siteEmail ) {
            $transport->setBcc($siteEmail);
        }

            /*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'Emails/registration.txt.twig',
                    array('name' => $name)
                ),
                'text/plain'
            )
            */

        //When using send() the message will be sent just like it would be sent if you used your mail client.
        // An integer is returned which includes the number of successful recipients.
        // If none of the recipients could be sent to then zero will be returned, which equates to a boolean false.
        // If you set two To: recipients and three Bcc: recipients in the message and all of the recipients
        // are delivered to successfully then the value 5 will be returned.
        $emailRes = $this->container->get('mailer')->send($transport); //

        $ccStr = "";
        if( $ccs && count($ccs)>0 ) {
            $ccStr = implode("; ",$ccs);
        }
        $emailsStr = "";
        if( $emails && count($emails)>0 ) {
            $emailsStr = implode("; ",$emails);
        }
        $logger->notice("sendEmail res=".$emailRes."; From:".$fromEmail."; To:".$emailsStr."; CC:".$ccStr."; subject=".$subject."; body=".$message);

        return $emailRes;
    }

    public function checkEmails($emails) {

        if( is_array($emails) ) {
            return $emails;
        }

        //$logger = $this->container->get('logger');
        //$logger->notice("checkEmails: input emails=".print_r($emails));
        if( strpos($emails, ',') !== false ) {
            return explode(',', $emails);
        } else {
            if( $emails ) {
                return array($emails);
            }
        }
        //$logger->notice("checkEmails: output emails=".implode(";",$emails));
        return $emails;
    }


    public function hasConnection() {

        $result = false;

        $userutil = new UserUtil();
        $smtp = $userutil->getSiteSetting($this->em,'smtpServerAddress');
        //echo "smtp=" . $smtp . "<br>";

        $fp = fsockopen($smtp, 25, $errno, $errstr, 9) ;

        if (!$fp) {
            $logger = $this->container->get('logger');
            $logger->error("SendEmail server=$smtp; ERROR:$errno - $errstr");
            $result = false;
        } else {
            fclose($fp);
            $result = true;
        }

        return $result;
    }


    ///////////////// NOT USED: using original php mail  /////////////////
    public function sendEmail_orig( $email, $subject, $message, $em, $ccs=null, $fromEmail=null ) {

        if( !$email || $email == "" ) {
            return false;
        }

        if( !$message || $message == "" ) {
            return false;
        }

        $this->initEmail($em,$fromEmail);

        $headers = null;
        if( $ccs ) {
            $headers = 'Cc: ' . $ccs . "\r\n";
        }

        //echo "email=".$email."<br>";
        //echo "headers=".$headers."<br>";
        //exit('1');

        // Send
        mail($email, $subject, $message, $headers);

        return true;
    }

    public function initEmail($em,$fromEmail=null) {
        $userutil = new UserUtil();
        $smtp = $userutil->getSiteSetting($em,'smtpServerAddress');

        if( !$fromEmail ) {
            $fromEmail = $userutil->getSiteSetting($em,'siteEmail');
        }

        //exit("smtp=".$smtp);

        ini_set( 'sendmail_from', $fromEmail );
        ini_set( "SMTP", $smtp );
    }
    ///////////////// EOF NOT USED: using original php mail  /////////////////
    
}


//Notes:
// for testing use: swift_delivery_addresses: [oli2002@med.cornell.edu]
// for live: swift_delivery_addresses: []
// 1) make a command with
// public function boot() {
//$tmpDir = $this->container->getParameter('my_website.swift_tmp_dir');
//
//\Swift_Preferences::getInstance()->setTempDir($tmpDir);
//}
//2) then php app/console swiftmailer:spool:send --env=prod > /dev/null 2>>app/logs/swift-error.log

?>
