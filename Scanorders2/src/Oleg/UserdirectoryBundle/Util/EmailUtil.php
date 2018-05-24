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

namespace Oleg\UserdirectoryBundle\Util;
use Crontab\Crontab;
use Crontab\Job;


/**
 * Description of EmailUtil
 *
 * @author Cina
 */
class EmailUtil {

    protected $em;
    protected $container;

    public function __construct( $em, $container ) {
        $this->em = $em;
        $this->container = $container;
    }

    //[2016-06-24 14:20:39] request.CRITICAL: Uncaught PHP Exception Swift_TransportException: "Connection to smtp.med.cornell.edu:25 Timed Out" at E:\Program Files (x86)\Aperio\Spectrum\htdocs\order\scanorder\Scanorders2\vendor\swiftmailer\swiftmailer\lib\classes\Swift\Transport\AbstractSmtpTransport.php line 404 {"exception":"[object] (Swift_TransportException(code: 0): Connection to smtp.med.cornell.edu:25 Timed Out at E:\\Program Files (x86)\\Aperio\\Spectrum\\htdocs\\order\\scanorder\\Scanorders2\\vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\AbstractSmtpTransport.php:404)"} []
    //one possible solution: http://stackoverflow.com/questions/25449496/swiftmailer-gmail-connection-timed-out-110
    //$smtp_host_ip = gethostbyname('smtp.gmail.com');
    //$transport = Swift_SmtpTransport::newInstance($smtp_host_ip,465,'ssl')

    //php bin/console swiftmailer:spool:send --env=prod
    //$emails: single or array of emails
    //$ccs: single or array of emails
    public function sendEmail( $emails, $subject, $body, $ccs=null, $fromEmail=null, $attachmentPath=null ) {

        //testing
        //$emails = "oli2002@med.cornell.edu, cinava@yahoo.com";
        //$emails = "oli2002@med.cornell.edu";
        //$ccs = null;

        $userSecUtil = $this->container->get('user_security_utility');
        $logger = $this->container->get('logger');
        //set_time_limit(0); //set time limit to 600 sec == 10 min

        //echo "emails=".$emails."<br>";
        //echo "ccs=".$ccs."<br>";

//        if( $this->hasConnection() == false ) {
//            $logger->error("sendEmail: connection error");
//            //exit('no connection');
//            return false;
//        }
        //exit('yes connection');

        if( !$emails || $emails == "" ) {
            $logger->error("sendEmail: emails empty=".$emails);
            return false;
        }

        if( !$body || $body == "" ) {
            $logger->error("sendEmail: message body empty=".$body);
            return false;
        }

        if( !$fromEmail ) {
            $fromEmail = $userSecUtil->getSiteSettingParameter('siteEmail');
        }

        $emails = $this->checkEmails($emails);
        $ccs = $this->checkEmails($ccs);

//        if( $this->em ) {
//            $smtpServerAddress = $userSecUtil->getSiteSettingParameter('smtpServerAddress');
//            $smtp_host_ip = gethostbyname($smtpServerAddress);
//            //$logger->notice("smtpServerAddress=".$smtpServerAddress." => smtp_host_ip=".$smtp_host_ip);
//            //$message = \Swift_Message::newInstance($smtp_host_ip);
//            $mailer = $this->getSwiftMailer();
//        } else {
//            $logger->error("this->em is null in sendEmail: use default Swift_Message::newInstance(). subject=".$subject);
//            $message = \Swift_Message::newInstance();
//        }

        $message = \Swift_Message::newInstance();

        $message->setSubject($subject);
        $message->setFrom($fromEmail);

        $message->setBody(
            $body,
            'text/plain'
        );

        $mailerDeliveryAddresses = trim($userSecUtil->getSiteSettingParameter('mailerDeliveryAddresses'));
        if( $mailerDeliveryAddresses ) {
            $mailerDeliveryAddresses = str_replace(" ","",$mailerDeliveryAddresses);
            $message->setTo($mailerDeliveryAddresses);
        } else {
            $message->setTo($emails);
            if( $ccs ) {
                $message->setCc($ccs);
            }
        }

        //send copy email to siteEmail via setBcc
        $userSecUtil = $this->container->get('user_security_utility');
        $siteEmail = $userSecUtil->getSiteSettingParameter('siteEmail');
        if( $siteEmail ) {
            $message->setBcc($siteEmail);
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

        // Optionally add any attachments
        if( $attachmentPath ) {
            $message->attach(\Swift_Attachment::fromPath($attachmentPath));
        }

        $ccStr = "";
        if( $ccs && count($ccs)>0 ) {
            $ccStr = implode("; ",$ccs);
        }
        $emailsStr = "";
        if( $emails && count($emails)>0 ) {
            $emailsStr = implode("; ",$emails);
        }

        $mailer = $this->getSwiftMailer();
        if( !$mailer ) {
            $logger->notice("sendEmail: Email has not been sent: From:".$fromEmail."; To:".$emailsStr."; CC:".$ccStr."; subject=".$subject."; body=".$message);
        }

        //When using send() the message will be sent just like it would be sent if you used your mail client.
        // An integer is returned which includes the number of successful recipients.
        // If none of the recipients could be sent to then zero will be returned, which equates to a boolean false.
        // If you set two To: recipients and three Bcc: recipients in the message and all of the recipients
        // are delivered to successfully then the value 5 will be returned.
        //$emailRes = $this->container->get('mailer')->send($message); //
        $emailRes = $mailer->send($message);

        $logger->notice("sendEmail: Email sent: res=".$emailRes."; From:".$fromEmail."; To:".$emailsStr."; CC:".$ccStr."; subject=".$subject."; body=".$message);

        return $emailRes;
    }

    public function checkEmails($emails) {

        if( !$emails ) {
            return $emails;
        }

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


    //https://ourcodeworld.com/articles/read/14/swiftmailer-send-mails-from-php-easily-and-effortlessly
    public function getSwiftMailer() {
        $userSecUtil = $this->container->get('user_security_utility');

        $useSpool = $userSecUtil->getSiteSettingParameter('mailerSpool');
        if( $useSpool ) {
            $spoolPath = $this->container->get('kernel')->getProjectDir() .
                DIRECTORY_SEPARATOR . "app" .
                DIRECTORY_SEPARATOR . "spool".
                DIRECTORY_SEPARATOR . "default";
            $spool = new \Swift_FileSpool($spoolPath);
            $transport = \Swift_SpoolTransport::newInstance($spool);
        } else {
            $transport = $this->getSmtpTransport();
            if( !$transport ) {
                return null;
            }
        }

        $mailer = \Swift_Mailer::newInstance($transport);

        return $mailer;
    }

    public function getSmtpTransport() {
        $userSecUtil = $this->container->get('user_security_utility');

        $host = $userSecUtil->getSiteSettingParameter('smtpServerAddress');
        if( !$host ) {
            return null;
        }

        $port = $userSecUtil->getSiteSettingParameter('mailerPort');
        $encrypt = $userSecUtil->getSiteSettingParameter('mailerUseSecureConnection');
        $username = $userSecUtil->getSiteSettingParameter('mailerUser');
        //Note for Google email server: use Google App specific password
        //Enable 2-step verification
        //Generate Google App specific password
        $password = $userSecUtil->getSiteSettingParameter('mailerPassword');
        $authMode = $userSecUtil->getSiteSettingParameter('mailerAuthMode');
        //$trans = $userSecUtil->getSiteSettingParameter('mailerTransport');

        $transport = \Swift_SmtpTransport::newInstance();

        $transport->setHost($host);

        if( $port ) {
            $transport->setPort($port);
        }

        if( $username ) {
            $transport->setUsername($username);
        }

        if( $password ) {
            $transport->setPassword($password);
        }

        if( $authMode ) {
            $transport->setAuthMode($authMode);
        }
        
        if( $encrypt ) {
            $transport->setEncryption($encrypt);
        }

        $transport->setStreamOptions(array('ssl' => array('allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false)));

        return $transport;
    }

    public function sendSpooledEmails() {
        $userSecUtil = $this->container->get('user_security_utility');

        $transport = $this->getSmtpTransport();
        if( !$transport ) {
            return null;
        }

        $useSpool = $userSecUtil->getSiteSettingParameter('mailerSpool');
        if( $useSpool ) {
            $spoolPath = $this->container->get('kernel')->getProjectDir() .
                DIRECTORY_SEPARATOR . "app" .
                DIRECTORY_SEPARATOR . "spool".
                DIRECTORY_SEPARATOR . "default";
            $spool = new \Swift_FileSpool($spoolPath);
        }

        $spool->recover();
        $res = $spool->flushQueue($transport);

        return $res;
    }


    //https://github.com/yzalis/Crontab
    //run: php bin/console cron:swift --env=prod
    public function createEmailCronJob() {

        if( $this->isWindows() ){
            return null;
        }

        $userSecUtil = $this->container->get('user_security_utility');

        $projectDir = $this->container->get('kernel')->getProjectDir();
        $cronJobName = "php ".$projectDir.DIRECTORY_SEPARATOR."bin/console cron:swift --env=prod";

        $useSpool = $userSecUtil->getSiteSettingParameter('mailerSpool');
        $mailerFlushQueueFrequency = $userSecUtil->getSiteSettingParameter('mailerFlushQueueFrequency');
        
        //create cron job
        if( $useSpool && $mailerFlushQueueFrequency ) {
            $job = new Job();
            $job
                ->setMinute('*/' . $mailerFlushQueueFrequency)//every $mailerFlushQueueFrequency minutes
                ->setHour('*')
                ->setDayOfMonth('*')
                ->setMonth('*')
                ->setDayOfWeek('*')
                ->setCommand($cronJobName);

            $crontab = new Crontab();
            if( !$this->isCronJobExists($crontab,$cronJobName) ) {
                $crontab->addJob($job);
                //$crontab->write();
                $crontab->getCrontabFileHandler()->write($crontab);
            }

            $res = $crontab->render();
            echo "crontab res=".$res."<br>";
            //exit('111');

            return $res;
        } else {
            //remove cron job
            $crontab = new Crontab();
            //$res = $crontab->render();
            //echo "crontab res=".$res."<br>";
            $res  = $this->removeCronJob($crontab,$cronJobName);

            $session = $this->container->get('session');
            $session->getFlashBag()->add(
                'notice',
                "Removed Cron Job:".$res
            );
        }

        return null;
    }

    public function isCronJobExists($crontab,$commandName) {
        foreach($crontab->getJobs() as $job) {
            echo "job=".$job.", command=".$job->getCommand()."<br>";
            if( $commandName == $job->getCommand() ) {
                echo "remove job ". $job."<br>";
                return true;
            }
        }
        return false;
    }

    public function removeCronJob($crontab,$commandName) {
        $resArr = array();
        foreach($crontab->getJobs() as $job) {
            echo "job=".$job.", command=".$job->getCommand()."<br>";
            if( $commandName == $job->getCommand() ) {
                $resArr[] = $job."";
                $crontab->removeJob($job);
                $crontab->getCrontabFileHandler()->write($crontab);
            }
        }
        return implode("; ",$resArr);
    }

    public function getCronStatus() {
        if( $this->isWindows() ){
            return null;
        }

        $res = '<font color="red">Cron job status: not found.</font>';
        $crontab = new Crontab();
        $crontabRender = $crontab->render();
        if( $crontabRender ) {
            //$res = "Cron job status: " . $crontab->render();
            $res = '<font color="green">Cron job status: '.$crontab->render().'.</font>';
        }
        //exit($res);
        return $res;
    }

    public function isWindows() {
        if( substr(php_uname(), 0, 7) == "Windows" ){
            return true;
        }
        return false;
    }











    

    //NOT USED
    //php bin/console swiftmailer:spool:send --env=prod: Unable to connect with TLS encryption
    public function hasConnection() {

        return true;

        $logger = $this->container->get('logger');
        $userSecUtil = $this->container->get('user_security_utility');

//        $environment = $userSecUtil->getSiteSettingParameter('environment');
//        if( $environment == 'dev'  ) {
//            $logger->notice("SendEmail is disabled for environment '".$environment."'");
//            return false;
//        }

        $smtp = $userSecUtil->getSiteSettingParameter('smtpServerAddress');
        //echo "smtp=" . $smtp . "<br>";
        //exit();

        $fp = fsockopen($smtp, 25, $errno, $errstr, 9) ;

        if (!$fp) {
            $logger->error("SendEmail server=$smtp; ERROR:$errno - $errstr");
            $result = false;
        } else {
            fclose($fp);
            $result = true;
        }

        return $result;
    }

}


//Notes:
// for testing use: swift_delivery_addresses: [oli2002@med.cornell.edu]
// for live: swift_delivery_addresses: []
//to run spool file: then php app/console swiftmailer:spool:send --env=prod > /dev/null 2>>app/logs/swift-error.log
//cmd /c YourProgram.exe >> app/logs/swiftlog.txt 2>&1

//To prevent tmp file not found (http://stackoverflow.com/questions/27323662/symfony2-send-email-warning-mkdir-no-such-file-or-directory-in):
//After comment this:
//if (is_writable($tmpDir = sys_get_temp_dir())) {
//    $preferences->setTempDir($tmpDir)->setCacheType('disk');
//}
//in the /vendor/swiftmailer/swiftmailer/lib/preferences.php everything works fine.
// I think that the problem was in the permission to the directory.
// Swiftmailer uses sys_get_temp_dir() function which trying refer to /tmp directory.


?>
