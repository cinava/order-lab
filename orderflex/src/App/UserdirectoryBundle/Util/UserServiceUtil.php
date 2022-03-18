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

/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 10/4/13
 * Time: 12:56 PM
 * To change this template use File | Settings | File Templates.
 */

namespace App\UserdirectoryBundle\Util;




use App\ResAppBundle\Entity\ResappSiteParameter;
use App\UserdirectoryBundle\Entity\Permission;
use App\UserdirectoryBundle\Entity\SiteParameters;
use App\UserdirectoryBundle\Form\DataTransformer\GenericTreeTransformer;
use App\VacReqBundle\Entity\VacReqSiteParameter;
use Doctrine\ORM\EntityManagerInterface;
//use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sinergi\BrowserDetector\Browser;
use Sinergi\BrowserDetector\Os;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\UserdirectoryBundle\Entity\User;
use App\UserdirectoryBundle\Util\UserUtil;
use App\UserdirectoryBundle\Entity\Logger;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Twilio\Rest\Client;

//use Crontab\Crontab;
//use Crontab\Job;

class UserServiceUtil {

    protected $em;
    protected $security;
    protected $container;
    protected $m3;

    public function __construct( EntityManagerInterface $em, Security $security, ContainerInterface $container ) {
        $this->em = $em;
        $this->security = $security;
        $this->container = $container;
    }

    public function convertFromUserTimezonetoUTC($datetime,$user) {

        //$user_tz = 'America/New_York';
        $user_tz = $user->getPreferences()->getTimezone();

        //echo "input datetime=".$datetime->format('Y-m-d H:i')."<br>";
        $datetimeTz = new \DateTime($datetime->format('Y-m-d H:i:s'), new \DateTimeZone($user_tz) );
        $datetimeUTC = $datetimeTz->setTimeZone(new \DateTimeZone('UTC'));
        //echo "output datetime=".$datetimeUTC->format('Y-m-d H:i')."<br>";

        return $datetimeUTC;
    }

    public function convertFromUtcToUserTimezone($datetime,$user=null) {

        if( !$user ) {
            $user = $this->security->getUser();
        }

        //$user_tz = 'America/New_York';
        //$user_tz = $user->getPreferences()->getTimezone();
        $user_tz = null;
        $preferences = $user->getPreferences();
        if( $preferences ) {
            $user_tz = $preferences->getTimezone();
        }
        if( !$user_tz ) {
            return $datetime;
        }

        //echo "input datetime=".$datetime->format('Y-m-d H:i')."<br>";
        $datetimeUTC = new \DateTime($datetime->format('Y-m-d H:i:s'), new \DateTimeZone('UTC') );
        $datetimeTz = $datetimeUTC->setTimeZone(new \DateTimeZone($user_tz));

        //echo "output datetime=".$datetimeUTC->format('Y-m-d H:i')."<br>";

        return $datetimeTz;
    }

    public function convertToUserTimezone($datetime,$user=null) {

        if( !$user ) {
            $user = $this->security->getUser();
        }

        //$user_tz = 'America/New_York';
        $user_tz = $user->getPreferences()->getTimezone();
        if( !$user_tz ) {
            $user_tz = "America/New_York";
        }

        //echo "input datetime=".$datetime->format('Y-m-d H:i')."<br>";
        //$datetimeUTC = new \DateTime($datetime->format('Y-m-d H:i'), new \DateTimeZone('UTC') );
        $datetimeUserTz = $datetime->setTimeZone(new \DateTimeZone($user_tz));

        //echo "output datetime=".$datetimeUTC->format('Y-m-d H:i')."<br>";

        return $datetimeUserTz;
    }

    public function convertToTimezone($datetime,$tz) {
        //echo "input datetime=".$datetime->format('Y-m-d H:i')."<br>";
        //$datetimeUTC = new \DateTime($datetime->format('Y-m-d H:i'), new \DateTimeZone('UTC') );
        $datetimeTz = $datetime->setTimeZone(new \DateTimeZone($tz));
        //echo "output datetime=".$datetimeUTC->format('Y-m-d H:i')."<br>";

        return $datetimeTz;
    }

    //user1 - submitter, user2 - viewing user
    public function convertFromUserTzToUserTz($datetime,$user1,$user2) {

        //$user_tz = 'America/New_York';
        $user1_tz = $user1->getPreferences()->getTimezone();
        $user2_tz = $user2->getPreferences()->getTimezone();

        //echo "input datetime=".$datetime->format('Y-m-d H:i')."<br>";
        $datetimeUser1 = new \DateTime($datetime->format('Y-m-d H:i'), new \DateTimeZone($user1_tz));
        $datetimeUser2 = $datetimeUser1->setTimeZone(new \DateTimeZone($user2_tz));

        //echo "output datetime=".$datetimeUTC->format('Y-m-d H:i')."<br>";

        return $datetimeUser2;
    }

    //the timestamp must change based on the timezone set in Global User Preferences > TimeZone of the currently logged in user's profile
    public function getSubmitterInfo( $message, $user=null ) {
        if( !$user ) {
            $user = $this->security->getUser();
        }
        $info = $this->getOrderDateStr($message,$user);
        if( $message && $message->getProvider() ) {
            $info = $info . " by ".$message->getProvider()->getUsernameOptimal();
        }
        return $info;
    }
    //DB datetime is UTC. Convert to the user's timezone.
    public function getOrderDateStr( $message, $user=null ) {
        //echo "getOrderDateStr <br>";
        if( !$message ) {
            return null;
        }

        $info = "";
        if( $message->getOrderdate() ) {
            if( !$user ) {
                $user = $this->security->getUser();
            }
            $orderDate = $message->getOrderdate();
            //$orderDate = $this->convertFromUserTzToUserTz($orderDate,$message->getProvider(),$user);
            //$info = $message->getOrderdate()->format('m/d/Y') . " at " . $message->getOrderdate()->format('h:i a (T)');
            $orderDateUserTz = $this->convertToUserTimezone($orderDate,$user);
            //$viewingUserTz = $user->getPreferences()->getTimezone();
            $viewingUserTz = $orderDateUserTz->format('T');
            $info = $orderDateUserTz->format('m/d/Y') . " at " . $orderDateUserTz->format('h:i a') . " (" . $viewingUserTz . ")";
        }
        return $info;
    }
    public function getOrderDateTzStr( $message, $tz=null ) {
        //echo "getOrderDateTzStr <br>";
        $info = "";
        if( $message->getOrderdate() ) {
            $orderDate = $message->getOrderdate();
            //$orderDateTz = $this->convertToTimezone($orderDate,$tz);
            //$info = $orderDateTz->format('m/d/Y') . " at " . $orderDateTz->format('h:i a') . " (" . $tz . ")";
            $info = $this->getDatetimeTzStr($orderDate,$tz);
        }
        return $info;
    }
    // 05/25/2017 at 3:25pm (Americas/New_York)
    public function getDatetimeTzStr( $datetime, $tz ) {
        //echo "getDatetimeTzStr <br>";
        //echo "input datetime=".$datetime->format('m/d/Y') . " at " . $datetime->format('h:i a') . " (" . $tz . ")"."<br>";
        $info = "";
        if( $datetime ) {
            $datetimeTz = $this->convertToTimezone($datetime,$tz);
            $info = $datetimeTz->format('m/d/Y') . " at " . $datetimeTz->format('h:i a') . " (" . $tz . ")";
        }
        //echo "output datetime=".$info."<br>";
        //exit('1');

        return $info;
    }

    // 05/25/2017 at 3:25pm (Americas/New_York)
    public function getSeparateDateTimeTzStr( $date, $time, $tz, $convertDate=true, $convertTime=true ) {
        //echo "getOrderDateStr <br>";
        //echo "input datetime=".$date->format('m/d/Y') . " at " . $time->format('h:i a') . " (" . $tz . ")"."<br>";
        //echo "date tz=".$date->getTimezone()->getName()."<br>";
        //echo "time tz=".$time->getTimezone()->getName()."<br>";

        $dateTz = $date;
        $timeTz = $time;
        if( $date && $convertDate ) {
            $dateTz = $this->convertToTimezone($date,$tz);
        }
        if( $time && $convertTime ) {
            $timeTz = $this->convertToTimezone($time,$tz);
        }
        $info = $dateTz->format('m/d/Y') . " at " . $timeTz->format('h:i a') . " (" . $tz . ")";
        
        //TODO: add timezone in the user's timezone
        //$user = $this->security->getUser();
        //$dateTime = new \DateTime();
        //$dateTime->setDate($date);
        //$dateTime->setTime($time);
        //$dateTime->setTimezone($tz);
        //$datetimeTz = $userServiceUtil->convertToTimezone($dateTimeObject,$formValueTimezone);
        //$modifiedOnUserTz = $this->convertToUserTimezone($dateTime,$user);
        //$info = $info . " (" . $modifiedOnUserTz->format("m/d/Y at h:i (T)") . ")";
//                    $formValueStr = $formValueStr . " (".$modifiedOnUserTz->format("m/d/Y").")";
//                    exit($formValueStr);
        
        //echo "output datetime=".$info."<br>";
        //exit('1');

        return $info;
    }


    //$field - field with the raw string (i.e. "lastname.field")
    //$fieldMetaphone - field with the metaphone key string (i.e. "lastname.fieldMetaphone")
    //$search - search string (i.e "McMastar")
    //$dql - pointer to the $dql object to modify
    //$queryParameters - pointer to $queryParameters array to modify
    public function getMetaphoneLike( $field, $fieldMetaphone, $search, &$dql, &$queryParameters ) {

        if( !($field && $search) ) {
            return null;
        }

//        $metaphoneKey = $this->getMetaphoneKey($search);
//        //echo "metaphoneKey:".$search."=>".$metaphoneKey."<br>";
//
//        if( $metaphoneKey ) {
//            $dql->andWhere("(".$field." LIKE :search"." OR ".$fieldMetaphone." LIKE :metaphoneKey".")");
//            $queryParameters['search'] = "%".$search."%";
//            $queryParameters['metaphoneKey'] = "%".$metaphoneKey."%";
//        } else {
//            $dql->andWhere($field." LIKE :search");
//            $queryParameters['search'] = "%".$search."%";
//            //echo "dql=".$dql->getSql()."<br>";
//        }

        $criterionStr = $this->getMetaphoneStrLike($field,$fieldMetaphone,$search,$queryParameters);
        if( $criterionStr ) {
            $dql->andWhere($criterionStr);
        }
    }

    public function getMetaphoneStrLike( $field, $fieldMetaphone, $search, &$queryParameters, $fieldIndex=null ) {
        $criterionStr = null;

        if( !($field && $search) ) {
            return null;
        }

        $metaphoneKey = $this->getMetaphoneKey($search);
        //echo "metaphoneKey:".$search."=>".$metaphoneKey."<br>";

        if( !$fieldIndex ) {
            $fieldIndex = "metaphoneKey";
        }

        if( $metaphoneKey ) {
            $criterionStr = "(".$field." LIKE :search".$fieldIndex." OR ".$fieldMetaphone." LIKE :".$fieldIndex.")";
            $queryParameters['search'.$fieldIndex] = "%".$search."%";
            $queryParameters[$fieldIndex] = "%".$metaphoneKey."%";
        } else {
            $criterionStr = $field." LIKE :search".$fieldIndex;
            $queryParameters['search'.$fieldIndex] = "%".$search."%";
        }

        return $criterionStr;
    }

    //Assistance => ASSTN
    //Assistants => ASSTN
    //Therefore: DB must have ASSTN in order to find Assistance
    public function getMetaphoneKey( $word ) {

        $this->initMetaphone();

        if( !$this->m3 ) {
            //$logger = $this->container->get('logger');
            //$logger->notice("m3 is null => return null");
            return null;
        }

        $this->m3->SetWord($word);

        //Encodes input string to one or two key values according to Metaphone 3 rules.
        $this->m3->Encode();

        if( $this->m3->m_primary ) {
            return $this->m3->m_primary;
        }

        if( $this->m3->m_secondary ) {
            return $this->m3->m_secondary;
        }

        return null;
    }

    //1) copy metaphone to the folder (i.e. "my folder")
    //2 enable metaphone in site setting
    //3) set the path to metaphone php file: i.e. "C:/my folder/metaphone3.php"
    public function initMetaphone() {

        //$logger = $this->container->get('logger');

        if( $this->m3 ) {
            //$logger->notice("Metaphone already initialized => return m3");
            return $this->m3;
        }

        $userSecUtil = $this->container->get('user_security_utility');
        $enableMetaphone = $userSecUtil->getSiteSettingParameter('enableMetaphone');
        $pathMetaphone = $userSecUtil->getSiteSettingParameter('pathMetaphone');

        if( !($enableMetaphone && $pathMetaphone) ) {
            //$logger->notice("Metaphone enable or path are null => return null");
            $this->m3 = null;
            return null;
        }

        //testing
        //$logger->notice("init Metaphone");

        //C:\Users\ch3\Documents\MyDocs\WCMC\ORDER\scanorder\Scanorders2\vendor\olegutil\Metaphone3\metaphone3.php
        //require_once('"'.$pathMetaphone.'"');
        //$pathMetaphone = "'".$pathMetaphone."'";
        //$pathMetaphone = '"'.$pathMetaphone.'"';
        //$pathMetaphone = str_replace(" ", "\\ ", $pathMetaphone);

        if( file_exists($pathMetaphone) ) {
            //echo "The file $pathMetaphone exists";
        } else {
            //echo "The file $pathMetaphone does not exist";
            $this->m3 = null;
            return null;
        }

        require_once($pathMetaphone);

        $m3 = new \Metaphone3();

        $m3->SetEncodeVowels(TRUE);
        $m3->SetEncodeExact(TRUE);

        $this->m3 = $m3;

        return $m3;
    }

    public function metaphoneTest() {
        $this->metaphoneSingleTest("Jackson");
        $this->metaphoneSingleTest("Jacksa");
        $this->metaphoneSingleTest("Jaksa");

        $this->metaphoneSingleTest("mcmaster");
        $this->metaphoneSingleTest("macmaste");
        $this->metaphoneSingleTest("master");

        $this->metaphoneSingleTest("Michael Jackson");

        $this->metaphonePhpSingleTest("mcmaster");
        $this->metaphonePhpSingleTest("macmaste");
        $this->metaphonePhpSingleTest("master");
    }
    public function metaphoneSingleTest($input) {
        $output = $this->getMetaphoneKey($input);
        echo $input."=>".$output."<br>";
    }
    public function metaphonePhpSingleTest($input) {
        $output = metaphone($input);
        echo $input."=>".$output." (php)<br>";
    }

    public function isWinOs() {
        /* Some possible outputs:
        Linux localhost 2.4.21-0.13mdk #1 Fri Mar 14 15:08:06 EST 2003 i686
        Linux

        FreeBSD localhost 3.2-RELEASE #15: Mon Dec 17 08:46:02 GMT 2001
        FreeBSD

        Windows NT XN1 5.1 build 2600
        WINNT
        */

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            //echo 'This is a server using Windows!';
            return true;
        } else {
            //echo 'This is a server not using Windows!';
        }

        return false;
    }

    public function browserCheck( $asString=false ) {
        //echo "start browserCheck<br>";
        //https://github.com/sinergi/php-browser-detector with MIT license
        $browser = new Browser();
        $name = $browser->getName();
        $version = $browser->getVersion();

        $os = new Os();
        $platform = $os->getName();

        //$logger = $this->container->get('logger');
        //$logger->notice("$name $version browser on $platform");

        $msg = "You appear to be using the <strong>outdated $name $version browser on $platform</strong>
        and it is not able to show you this site properly.<br>
        Please use Chrome, Firefox, Internet Explorer 9, Internet Explorer 10, Internet Explorer 11,
        or the Edge browser instead and visit this page again.<br>
        You can copy the URL of this page and paste it into the
        address bar of the other browser once you switch to it.";

        //Select2:
        //        IE 8+       >8
        //        Chrome 8+   >48
        //        Firefox 10+ >45
        //        Safari 3+
        //        Opera 10.6+ >12
        //Bootstrap: Safari on Windows not supported

        if( $asString ) {
            $browserInfo = $name . " " . $version . " on " . $platform;
            //echo "Your browser: " . $browserInfo . "<br>";
            return $browserInfo;
        }

        if( $name == Browser::IE ) {
            //Bootstrap IE 8+
            //Select2 IE 8+
            if( $version < 9 ) {
                return $msg;
            }
        }

        if( $name == Browser::SAFARI ) {
            //Bootstrap: Safari on Windows not supported
            if( $platform == Os::WINDOWS || $platform == Os::WINDOWS_PHONE ) {
                return $msg;
            }
        }

        if( $name == Browser::CHROME ) {
            if( $version < 48 ) {
                return $msg;
            }
        }

        if( $name == Browser::FIREFOX ) {
            if( $version < 45 ) {
                return $msg;
            }
        }

        if( $name == Browser::OPERA || $name == Browser::OPERA_MINI ) {
            if( $version < 12 ) {
                return $msg;
            }
        }

        return null;
    }

    //use it for deprecated choices secletion for Symfony>2.7
    public function flipArrayLabelValue( $keyLabelArr ) {
        if( !$keyLabelArr ) {
            return $keyLabelArr;
        }
        $labelValueArr = array();
        foreach( $keyLabelArr as $key=>$label ) {
            //echo "[$key] => [$label] <br>";
            if( $label ) {
                $labelValueArr[$label.""] = $key;
            }
        }
        return $labelValueArr;
    }
    

    public function getUniqueRegistrationLinkId( $className, $sometxt, $count=0 ) {
        if( $count > 100 ) { //limit: trying limit
            $limitRegistrationLinkId = uniqid($sometxt,true);
            $limitRegistrationLinkId = md5($limitRegistrationLinkId);
            //echo "limit return: $limitRegistrationLinkId<br>";
            return $limitRegistrationLinkId;
        }
        $registrationLinkId = uniqid(mt_rand(),true);
        //echo "registrationLinkId=$registrationLinkId<br>";
        $registrationLinkId = md5($registrationLinkId);
        //find if already exists
        $existedSignup = $this->em->getRepository('AppUserdirectoryBundle:'.$className)->findByRegistrationLinkID($registrationLinkId);
        if( $existedSignup ) {
            $count++;
            //echo "try gen: existedLinkId=$registrationLinkId; count=$count<br>";
            $registrationLinkId = $this->getUniqueRegistrationLinkId($className,$sometxt,$count);
        }
        //echo "return gen: existedLinkId=$registrationLinkId; count=$count<br>";
        return $registrationLinkId;
    }

    public function findOneCommentByThreadBodyAuthor($thread, $bodyText, $author)
    {
        $repository = $this->em->getRepository('AppUserdirectoryBundle:FosComment');
        $dql =  $repository->createQueryBuilder("comment");
        $dql->select('comment');

        $dql->leftJoin("comment.thread", "thread");
        $dql->leftJoin("comment.author", "author");

        $dql->where("thread.id = :threadId AND author.id = :authorId AND comment.body = :body");

        $parameters = array(
            "threadId" => $thread->getId(),
            "authorId" => $author->getId(),
            "body" => $bodyText
        );

        $query = $dql->getQuery();

        $query->setParameters($parameters);

        $comments = $query->getResult();

        if( count($comments) > 0 ) {
            $comment = $comments[0];
            //echo "Comment found ID=".$comment->getId()."<br>";
            return $comment;
        }

        //echo "Comment Not found by threadID=".$thread->getId()."; bodyText=".$bodyText."<br>";
        return null;
    }

    public function getListUserFilter($pathlink, $pathlinkLoc, $hasRoleSimpleView) {
        $userSecUtil = $this->container->get('user_security_utility');

        $res = array();
        $inst1 = null;
        $inst2 = null;

        $institution1 = $userSecUtil->getSiteSettingParameter("navbarFilterInstitution1");
        if( $institution1 ) {
            $inst1 = $institution1->getAbbreviation();
        }
        $institution2 = $userSecUtil->getSiteSettingParameter("navbarFilterInstitution2");
        if( $institution2 ) {
            $inst2 = $institution2->getAbbreviation();
        }

        $instTypes = array(
            //'hr' => 'all',

            '[inst1] Pathology Employees' => 'all',
            '[inst1] Pathology Faculty' => 'all',
            '[inst1] Pathology Clinical Faculty' => 'all',
            '[inst1] Pathology Physicians' => 'notSimpleView',

            'hr' => 'all',

            '[inst1] Pathology Research Faculty' => 'all',
            '- [inst1] Pathology Principal Investigators of Research Labs' => 'all',
            '- [inst1] Pathology Faculty in Research Labs' => 'all',

            'hr' => 'all',

            '[inst1] Pathology Staff' => 'notSimpleView',
            '[inst2] Pathology Staff' => 'notSimpleView',
            '- [inst1] or [inst2] Pathology Staff in Research Labs' => 'all',

            'hr' => 'all',

            '[inst1] Anatomic Pathology Faculty' => 'all',
            '[inst2] Laboratory Medicine Faculty' => 'all',

            'hr' => 'all',

            '[inst1] or [inst2] Pathology Residents' => 'all',
            '- [inst1] or [inst2] AP/CP Residents' => 'notSimpleView',
            '- [inst1] or [inst2] AP Residents' => 'notSimpleView',
            '- [inst1] or [inst2] AP Only Residents' => 'notSimpleView',
            '- [inst1] or [inst2] CP Residents' => 'notSimpleView',
            '- [inst1] or [inst2] CP Only Residents' => 'notSimpleView',

            '[inst1] or [inst2] Pathology Fellows' => 'all',
            '[inst1] Non-academic Faculty' => 'all',
        );

        //first common element
        $linkUrl = $this->container->get('router')->generate(
            $pathlink,
            array(
                //no filter
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $href = '<li><a href="'.$linkUrl.'">'.'Employees'.'</a></li>';
        $res[] = $href;

        //second common element (all in one page)
        $linkUrl = $this->container->get('router')->generate(
            $pathlink,
            array(
                'filter'=>'one-page',
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $href = '<li><a href="'.$linkUrl.'">'.'Employees in one page'.'</a></li>';
        $res[] = $href;


        foreach($instTypes as $name=>$flag) {
            if( $name == 'hr' ) {
                $res[] = '<hr style="margin-bottom:0; margin-top:0;">';
                continue;
            }
            if( !$hasRoleSimpleView || !($hasRoleSimpleView && $flag == 'notSimpleView') ) {

                $href = $this->replaceInstFilter($name,$pathlink,$inst1,$inst2);
                if( $href ) {
                    $res[] = $href;
                }

            }
        }

        if( $pathlinkLoc ) {
            $locTypes = array(
                '[inst1] or [inst2] Pathology Common Locations' => 'all',
                '[inst1] Pathology Common Locations' => 'all',
                '[inst2] Pathology Common Locations' => 'all',
            );

            //first common element
            $res[] = '<hr style="margin-bottom:0; margin-top:0;">';

            $linkUrl = $this->container->get('router')->generate(
                $pathlink,
                array(
                    //no filter
                ),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $href = '<li><a href="'.$linkUrl.'">'.'Common Locations'.'</a></li>';
            $res[] = $href;

            foreach($locTypes as $name=>$flag) {
                if( $name == 'hr' ) {
                    $res[] = '<hr style="margin-bottom:0; margin-top:0;">';
                    continue;
                }

                $href = $this->replaceInstFilter($name,$pathlinkLoc,$inst1,$inst2);
                if( $href ) {
                    $res[] = $href;
                }
            }
        }

        return $res;
    }
    public function replaceInstFilter($name,$pathlink,$inst1,$inst2) {
        $href = null;
        $nameInst = null;

        if( $inst1 && $inst2 ) {
            $nameInst = str_replace('[inst1]',$inst1,$name);
            $nameInst = str_replace('[inst2]',$inst2,$nameInst);
        }

        if( $inst1 && !$inst2 ) {
            if( strpos((string)$name, '[inst1]') !== false ) {
                if( strpos((string)$name, '[inst1]') !== false && strpos((string)$name, '[inst2]') === false ) {
                    $nameInst = str_replace('[inst1]',$inst1,$name);
                }
            }
        }

        if( $inst2 && !$inst1 ) {
            if( strpos((string)$name, '[inst2]') !== false ) {
                if( strpos((string)$name, '[inst2]') !== false && strpos((string)$name, '[inst1]') === false ) {
                    $nameInst = str_replace('[inst2]',$inst2,$name);
                }
            }
        }

        if( $nameInst ) {
            $linkUrl = $this->container->get('router')->generate(
                $pathlink,
                array(
                    'filter'=>str_replace('- ','',$nameInst),
                ),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $href = '<li><a href="'.$linkUrl.'">'.$nameInst.'</a></li>';
        }
        //$href = '<li><a href="'.$linkUrl.'">'.$nameInst.'</a></li>';
        //$res[] = $href;

        return $href;
    }


    public function generateSiteParameters() {

        $logger = $this->container->get('logger');
        $userSecUtil = $this->container->get('user_security_utility');
        $em = $this->em;

        $entities = $em->getRepository('AppUserdirectoryBundle:SiteParameters')->findAll();

        if( count($entities) > 0 ) {
            $logger->notice("Exit generateSiteParameters: SiteParameters has been already generated.");
            $resappCount = $this->generateSubSiteParameters();
            if( $resappCount ) {
                return $resappCount;
            }
            return -1;
        }

        $logger->notice("Start generating SiteParameters");

        $defaultSystemEmail = $this->container->getParameter('default_system_email');

        $types = array(
            "connectionChannel" => "http",

            "maxIdleTime" => "30",
            "environment" => "dev",
            "siteEmail" => "email@email.com",
            "loginInstruction" => 'Please use your <a href="https://its.weill.cornell.edu/services/accounts-and-access/center-wide-id">CWID</a> to log in.',
            "remoteAccessUrl" => "https://its.weill.cornell.edu/services/wifi-networks/remote-access",
            
            "enableAutoAssignmentInstitutionalScope" => true,

            "smtpServerAddress" => "smtp.gmail.com",
            "mailerPort" => "587",
            "mailerTransport" => "smtp",
            "mailerAuthMode" => "login",
            "mailerUseSecureConnection" => "tls",
            "mailerUser" => null,
            "mailerPassword" => null,
            "mailerSpool" => false,
            "mailerFlushQueueFrequency" => 15, //minuts
            "mailerDeliveryAddresses" => null,

            "aDLDAPServerAddress" => "ldap.forumsys.com",
            "aDLDAPServerPort" => "389",
            "aDLDAPServerOu" => "dc=example,dc=com",    //used for DC
            "aDLDAPServerAccountUserName" => null,
            "aDLDAPServerAccountPassword" => null,
            "ldapExePath" => "../src/App/UserdirectoryBundle/Util/",
            "ldapExeFilename" => "LdapSaslCustom.exe",

            "dbServerAddress" => "127.0.0.1",
            "dbServerPort" => "null",
            "dbServerAccountUserName" => "null",
            "dbServerAccountPassword" => "null",
            "dbDatabaseName" => "null",

            "pacsvendorSlideManagerDBServerAddress" => "127.0.0.1",
            "pacsvendorSlideManagerDBServerPort" => "null",
            "pacsvendorSlideManagerDBUserName" => "null",
            "pacsvendorSlideManagerDBPassword" => "null",
            "pacsvendorSlideManagerDBName" => "null",

            "institutionurl" => "http://www.cornell.edu/",
            "institutionname" => "Cornell University",
            "subinstitutionurl" => "http://weill.cornell.edu",
            "subinstitutionname" => "Weill Cornell Medicine",
            "departmenturl" => "http://www.cornellpathology.com",
            "departmentname" => "Pathology and Laboratory Medicine Department",
            "showCopyrightOnFooter" => true,

            ///////////////////// FELLAPP /////////////////////
            "codeGoogleFormFellApp" => "",
            "confirmationEmailFellApp" => "",
            "confirmationSubjectFellApp" => "Your WCM/NYP fellowship application has been succesfully received",
            "confirmationBodyFellApp" => "Thank You for submitting the fellowship application to Weill Cornell Medical College/NewYork Presbyterian Hospital. ".
                "Once we receive the associated recommendation letters, your application will be reviewed and considered. ".
                "If You have any questions, please do not hesitate to contact me by phone or via email. ".
                "Sincerely, Jessica Misner Fellowship Program Coordinator Weill Cornell Medicine Pathology and Laboratory Medicine 1300 York Avenue, Room C-302 T 212.746.6464 F 212.746.8192",
            "clientEmailFellApp" => '',
            "p12KeyPathFellApp" => 'E:\Program Files (x86)\pacsvendor\pacsname\htdocs\order\scanorder\Scanorders2\src\App\FellAppBundle\Util',
            "googleDriveApiUrlFellApp" => "https://www.googleapis.com/auth/drive https://spreadsheets.google.com/feeds",
            "userImpersonateEmailFellApp" => "olegivanov@pathologysystems.org",
            "templateIdFellApp" => "",
            "backupFileIdFellApp" => "",
            "folderIdFellApp" => "",
            "localInstitutionFellApp" => "Pathology Fellowship Programs (WCM)",
            "deleteImportedAplicationsFellApp" => false,
            "deleteOldAplicationsFellApp" => false,
            "yearsOldAplicationsFellApp" => 2,
            "spreadsheetsPathFellApp" => "fellapp/Spreadsheets",
            "applicantsUploadPathFellApp" => "fellapp/FellowshipApplicantUploads",
            "reportsUploadPathFellApp" => "fellapp/Reports",
            "applicationPageLinkFellApp" => "http://wcmc.pathologysystems.org/fellowship-application",
            "libreOfficeConvertToPDFPathFellApp" => 'C:\Program Files (x86)\LibreOffice 5\program',
            "libreOfficeConvertToPDFFilenameFellApp" => "soffice",
            "libreOfficeConvertToPDFArgumentsdFellApp" => "--headless -convert-to pdf -outdir",
            "pdftkPathFellApp" => 'C:\Program Files (x86)\pacsvendor\pacsname\htdocs\order\scanorder\Scanorders2\vendor\olegutil\PDFTKBuilderPortable\App\pdftkbuilder',
            "pdftkFilenameFellApp" => "pdftk",
            "pdftkArgumentsFellApp" => "###inputFiles### cat output ###outputFile### dont_ask",
            "gsPathFellApp" => 'C:\Program Files (x86)\pacsvendor\pacsname\htdocs\order\scanorder\Scanorders2\vendor\olegutil\Ghostscript\bin',
            "gsFilenameFellApp"=>"gswin64c.exe",
            "gsArgumentsFellApp"=>"-q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile= ###outputFile###  -c .setpdfwrite -f ###inputFiles###",
            //"libreOfficeConvertToPDFPathFellAppLinux" => "/usr/lib/libreoffice/program",
            //"libreOfficeConvertToPDFFilenameFellAppLinux" => "soffice",
            ///////////////////// EOF FELLAPP /////////////////////

            //VacReq
//            "vacationAccruedDaysPerMonth" => '2',
//            "academicYearStart" => new \DateTime('2017-07-01'),
//            "academicYearEnd" => new \DateTime('2017-06-30'),
//            "holidaysUrl" => "http://intranet.med.cornell.edu/hr/",

            "initialConfigurationCompleted" => false,

            "maintenance" => false,
            //"maintenanceenddate" => null,
            "maintenancelogoutmsg" =>   'The scheduled maintenance of this software has begun.'.
                ' The administrators are planning to return this site to a fully functional state on or before [[datetime]].',
                //'If you were in the middle of entering order information, it was saved as an "Unsubmitted" order '.
                //'and you should be able to submit that order after the maintenance is complete.',
            "maintenanceloginmsg" =>    'The scheduled maintenance of this software has begun.'.
                ' The administrators are planning to return this site to a fully functional state on or before [[datetime]].',
                //'If you were in the middle of entering order information, '.
                //'it was saved as an "Unsubmitted" order and you should be able to submit that order after the maintenance is complete.',

            //uploads
            "avataruploadpath" => "directory/avatars",
            "employeesuploadpath" => "directory/documents",
            "scanuploadpath" => "scan-order/documents",
            "fellappuploadpath" => "fellapp/documents",
            "resappuploadpath" => "resapp/documents",
            "vacrequploadpath" => "directory/vacreq",
            "transresuploadpath" => "transres/documents",
            "callloguploadpath" => "calllog/documents",
            "crnuploadpath" => "crn/documents",

            "mainHomeTitle" => "Welcome to the O R D E R platform!",
            "listManagerTitle" => "List Manager",
            "eventLogTitle" => "Event Log",
            "siteSettingsTitle" => "Site Settings",

            ////////////////////////// LDAP notice messages /////////////////////////
            "noticeAttemptingPasswordResetLDAP" => "The password for your [[CWID]] can only be changed or reset by visiting the enterprise password management page or by calling the help desk at ‭1 (212) 746-4878‬.",
            //"noticeUseCwidLogin" => "Please use your CWID to log in",
            "noticeSignUpNoCwid" => "Sign up for an account if you have no CWID",
            "noticeHasLdapAccount" => 'Do you (the person for whom the account is being requested) have a <a href=\"https://its.weill.cornell.edu/services/accounts-and-access/center-wide-id\">CWID</a> username?',
            "noticeLdapName" => "Active Directory (LDAP)",
            ////////////////////////// EOF LDAP notice messages /////////////////////////

            ////////////////////// Global TRP parameters //////////////////
            "transresProjectSelectionNote" => 'If your project request involves collaboration with any
                                                <a target="_blank" href="https://pathology.weill.cornell.edu/clinical-services/hematopathology"
                                                >Weill Cornell Hematopathology faculty members</a>,<br>
                                                please press the "New Hematopathology Project Request" button.<br>
                                                For all other project requests, please press the "New AP/CP Project Request" button.',

            "transresBusinessEntityName" => "Center for Translational Pathology",

            "transresBusinessEntityAbbreviation" => "CTP",
            ////////////////////// EOF Global TRP parameters //////////////////

            ////////////////////// EOF Third-Party Software //////////////////

            "contentAboutPage" => '
                <p>
                    This site is built on the platform titled "O R D E R" (as in the opposite of disorder).
                </p>

                <p>
                    Designers: Victor Brodsky, App Ivanov
                </p>

                <p>
                    Developer: App Ivanov
                </p>

                <p>
                    Quality Assurance Testers: App Ivanov, Steven Bowe, Emilio Madrigal
                </p>

                <p>
                    We are continuing to improve this software. If you have a suggestion or believe you have encountered an issue, please don\'t hesitate to email
                <a href="mailto:'.$defaultSystemEmail.'" target="_top">'.$defaultSystemEmail.'</a> and attach relevant screenshots.
                </p>

                <br>

                <p>
                O R D E R is made possible by:
                </p>

                <br>

                <p>

                        <ul>


                    <li>
                        <a href="http://php.net">PHP</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://symfony.com">Symfony</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://doctrine-project.org">Doctrine</a>
                    </li>

                    <br>                  
					
					<li>
                        <a href="https://msdn.microsoft.com/en-us/library/aa366156.aspx">MSDN library: ldap_bind_s</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/symfony/SwiftmailerBundle">SwiftmailerBundle</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/symfony/AsseticBundle">AsseticBundle</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/FriendsOfSymfony/FOSUserBundle">FOSUserBundle</a>
                    </li>

                    <br>

                    <li>

                        <a href="https://github.com/1up-lab/OneupUploaderBundle">OneupUploaderBundle</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://www.dropzonejs.com/">Dropzone JS</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://www.jstree.com/">jsTree</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/KnpLabs/KnpPaginatorBundle">KnpPaginatorBundle</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://twig.sensiolabs.org/doc/advanced.html">Twig</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://getbootstrap.com/">Bootstrap</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/kriskowal/q">JS promises Q</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://jquery.com">jQuery</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://jqueryui.com/">jQuery UI</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/RobinHerbots/jquery.inputmask">jQuery Inputmask</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://ivaynberg.github.io/select2/">Select2</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://www.eyecon.ro/bootstrap-datepicker/">Bootstrap Datepicker</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://www.malot.fr/bootstrap-datetimepicker/demo.php">Bootstrap DateTime Picker</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/twitter/typeahead.js/">Typeahead with Bloodhound</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://fengyuanchen.github.io/cropper/">Image Cropper</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://handsontable.com/">Handsontable</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/KnpLabs/KnpSnappyBundle">KnpSnappyBundle with wkhtmltopdf</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/myokyawhtun/PDFMerger">PDFMerger</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/bermi/password-generator">Password Generator</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/andreausu/UsuScryptPasswordEncoderBundle">Password Encoder</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://github.com/adesigns/calendar-bundle">jQuery FullCalendar bundle</a>
                    </li>

                    <br>

                    <li>
                        <a href="https://sciactive.com/pnotify/">PNotify JavaScript notifications</a>
                    </li>

                    <br>

                    <li>
                        <a href="http://casperjs.org/">CasperJS</a>
                    </li>

                </ul>
                </p>
            '
            //"underLoginMsgUser" => "",
            //"underLoginMsgScan => ""

        );

        //set default Third-Party Software Dependencies for Linux not used in container
        if( !$this->isWindows() ) {
            //set the same value as in setparameters.php run on deploy $wkhtmltopdfpath = "/usr/bin/xvfb-run /usr/bin/wkhtmltopdf";
            $types['wkhtmltopdfpathLinux'] = $wkhtmltopdfpath = "/usr/bin/xvfb-run wkhtmltopdf";
            //$types['wkhtmltopdfpathLinux'] = "/usr/bin/xvfb-run /usr/bin/wkhtmltopdf";
            //$types['wkhtmltopdfpathLinux'] = "xvfb-run wkhtmltopdf";

            //set other Linux parameters
            $types['libreOfficeConvertToPDFPathFellAppLinux'] = "/usr/lib/libreoffice/program";
            $types['libreOfficeConvertToPDFFilenameFellAppLinux'] = "soffice";
            $types['libreOfficeConvertToPDFArgumentsdFellAppLinux'] = "--headless -convert-to pdf -outdir";
            $types['pdftkPathFellAppLinux'] = "/usr/bin";
            $types['pdftkFilenameFellAppLinux'] = "pdftk";
            $types['pdftkArgumentsFellAppLinux'] = "###inputFiles### cat output ###outputFile### dont_ask";
            $types['gsPathFellAppLinux'] = "/usr/bin";
            $types['gsFilenameFellAppLinux'] = "gs";
            $types['gsArgumentsFellAppLinux'] = "-q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile= ###outputFile###  -c .setpdfwrite -f ###inputFiles###";
            $types['phantomjsLinux'] = "/opt/phantomjs-2.1.1-linux-x86_64/bin/phantomjs";
            $types['rasterizeLinux'] = "/usr/local/bin/order-lab/packer/rasterize.js";
            //$types[''] = "";
            //$types[''] = "";
        }

        $params = new SiteParameters();

        $count = 0;
        foreach( $types as $key => $value ) {
            $method = "set".$key;
            $params->$method( $value );
            $count = $count + 10;
            $logger->notice("setter: $method");
        }

        //auto assign Institution
        $autoAssignInstitution = $userSecUtil->getAutoAssignInstitution();
        if( $autoAssignInstitution ) {
            $params->setAutoAssignInstitution($autoAssignInstitution);
            $logger->notice("Auto Assign Institution: $autoAssignInstitution");
        } else {
//            $institutionName = 'Weill Cornell Medical College';
//            $institution = $em->getRepository('AppUserdirectoryBundle:Institution')->findOneByName($institutionName);
//            if (!$institution) {
//                //throw new \Exception( 'Institution was not found for name='.$institutionName );
//            } else {
//                $params->setAutoAssignInstitution($institution);
//            }
            $wcmc = $em->getRepository('AppUserdirectoryBundle:Institution')->findOneByAbbreviation("WCM");
            if( $wcmc ) {
                //exit('generateSiteParameters: No Institution: "WCM"');
                $mapper = array(
                    'prefix' => 'App',
                    'bundleName' => 'UserdirectoryBundle',
                    'className' => 'Institution'
                );
                $autoAssignInstitution = $em->getRepository('AppUserdirectoryBundle:Institution')->findByChildnameAndParent(
                    "Pathology and Laboratory Medicine",
                    $wcmc,
                    $mapper
                );
                if( $autoAssignInstitution ) {
                    $params->setAutoAssignInstitution($autoAssignInstitution);
                    $logger->notice("Auto Assign Generated Institution: $autoAssignInstitution");
                }
            }
        }
        $logger->notice("Finished with Auto Assign Institution");

        //set AllowPopulateFellApp to false
        $params->setAllowPopulateFellApp(false);

        $em->persist($params);
        $em->flush();

//        if( $this->isWindows() ) {
//            $emailUtil = $this->container->get('user_mailer_utility');
//            $emailUtil->createEmailCronJob();
//            $logger->notice("Created email cron job");
//        } else {
//            $this->createCronsLinux();
//        }
        $this->createCrons();

        $resappCount = $this->generateSubSiteParameters();
        $count = $count + $resappCount;

        $logger->notice("Finished generateSiteParameters: count=".$count/10);

        return round($count/10);
    }

    public function generateSubSiteParameters() {
        $count = 0;
        $count = $count + $this->generateVacReqSiteParameters();
        $count = $count + $this->generateResAppSiteParameters();
        return $count;
    }

    public function generateVacReqSiteParameters() {
        $logger = $this->container->get('logger');
        //$userSecUtil = $this->container->get('user_security_utility');
        $em = $this->em;

        $entities = $em->getRepository('AppUserdirectoryBundle:SiteParameters')->findAll();

        $siteParameters = null;
        if( count($entities) > 0 ) {
            $siteParameters = $entities[0];
        }

        if( !$siteParameters ) {
            $logger->notice("generateVacReqSiteParameters failed: SiteParameters does not exist.");
            return 0;
        }

        if( $siteParameters->getVacreqSiteParameter() ) {
            $logger->notice("VacReqSiteParameter already exists.");
            return 0;
        }

        $logger->notice("Start generating VacReqSiteParameter");

        $nowDate = new \DateTime();
        $floatingDayNote =  "The Juneteenth Holiday may be used as a floating holiday ".
                            "only if you have an NYPH appointment. You can request a floating holiday however, ".
                            "it must be used in the same fiscal year ending June 30, ".$nowDate->format('Y').". ".
                            "It cannot be carried over.";

        $types = array(
            //"academicYearStart" => null,
            //"academicYearEnd" => null,
            "academicYearStart" => new \DateTime('2017-07-01'),
            "academicYearEnd" => new \DateTime('2017-06-30'),
            "holidaysUrl" => "http://intranet.med.cornell.edu/hr/",
            "vacationAccruedDaysPerMonth" => 2,
            "maxVacationDays" => 24,
            "maxCarryOverVacationDays" => 15,
            "noteForVacationDays" => null,
            "noteForCarryOverDays" => "As per policy, the number of days that can be carried over to the following year is limited to the maximum of 15",
            "floatingDayName" => "Floating Day",
            "floatingDayNote" => $floatingDayNote,
            "floatingRestrictDateRange" => true,
        );

        $params = new VacReqSiteParameter();

        $count = 0;
        foreach( $types as $key => $value ) {
            $method = "set".$key;
            $params->$method( $value );
            $count = $count + 10;
            $logger->notice("setter: $method");
        }


        if( $count > 0 ) {
            $siteParameters->setVacreqSiteParameter($params);

            $em->persist($params);
            $em->flush();
        }

        $logger->notice("Finished generateVacReqSiteParameters: count=".$count/10);

        return round($count/10);
    }

    public function generateResAppSiteParameters() {
        $logger = $this->container->get('logger');
        //$userSecUtil = $this->container->get('user_security_utility');
        $em = $this->em;

        $entities = $em->getRepository('AppUserdirectoryBundle:SiteParameters')->findAll();

        $siteParameters = null;
        if( count($entities) > 0 ) {
            $siteParameters = $entities[0];
        }

        if( !$siteParameters ) {
            $logger->notice("generateResAppSiteParameters failed: SiteParameters does not exist.");
            return 0;
        }

        if( $siteParameters->getResappSiteParameter() ) {
            $logger->notice("ResappSiteParameters already exists.");
            return 0;
        }

        $logger->notice("Start generating SiteParameters");


        $types = array(
            "acceptedEmailSubject" => "Congratulations on your acceptance to the [[RESIDENCY TYPE]] [[START YEAR]] residency at Weill Cornell Medicine",
            "acceptedEmailBody" => "Dear [[APPLICANT NAME]],

We are looking forward to having you join us as a [[RESIDENCY TYPE]] fellow in [[START YEAR]]!

Weill Cornell Medicine",

            "rejectedEmailSubject" => "Thank you for applying to the [[RESIDENCY TYPE]] [[START YEAR]] residency at Weill Cornell Medicine",

            "rejectedEmailBody" => "Dear [[APPLICANT NAME]],

We have reviewed your application to the [[RESIDENCY TYPE]] residency for [[START YEAR]], and we regret to inform you that we are unable to offer you a position at this time. Please contact us if you have any questions.

Weill Cornell Medicine",

            "confirmationSubjectResApp" => "Your WCM/NYP residency application has been successfully received",

            "confirmationBodyResApp" => "Thank You for submitting the residency application to Weill Cornell Medicine/NewYork Presbyterian Hospital.

Once we receive the associated recommendation letters, your application will be reviewed and considered.

If You have any questions, please do not hesitate to contact me by phone or via email.


Sincerely,

Residency Program Coordinator
Weill Cornell Medicine
Pathology and Laboratory Medicine",

            "localInstitutionResApp" => "Pathology Residency Programs (WCM)",
            "spreadsheetsPathResApp" => "resapp/Spreadsheets",
            "applicantsUploadPathResApp" => "resapp/ResidencyApplicantUploads",
            "reportsUploadPathResApp" => "resapp/Reports"
        );

        //testing
//        $params = $siteParameters->getResappSiteParameter();
//        if( !$params ) {
//            $params = new ResappSiteParameter();
//        }

        $params = new ResappSiteParameter();

        $count = 0;
        foreach( $types as $key => $value ) {
            $method = "set".$key;
            $params->$method( $value );
            $count = $count + 10;
            $logger->notice("setter: $method");
        }


        if( $count > 0 ) {
            $siteParameters->setResappSiteParameter($params);

            $em->persist($params);
            $em->flush();
        }

        $logger->notice("Finished generateResAppSiteParameters: count=".$count/10);

        return round($count/10);
    }

    public function isWindows() {
        if( substr(php_uname(), 0, 7) == "Windows" ){
            return true;
        }
        return false;
    }

    public function getGitVersionDate()
    {
        $ver = $this->getCurrentGitCommit();
        return $ver;


        $commitHash = $this->runProcess('git log --pretty="%h" -n1 HEAD');
        $commitDate = $this->runProcess('git log -n1 --pretty=%ci HEAD');
        $commitDateStr = null;
        if( $commitDate ) {
            $commitDateStr = $commitDate->format('Y-m-d H:m:s');
        }
        $ver = $commitHash . " (" . $commitDateStr . ")";
        //echo "ver=".$ver."<br>";
        //print_r($ver);
        return $ver;

        $MAJOR = 1;
        $MINOR = 2;
        $PATCH = 3;

        $commitHash = trim(exec('git log --pretty="%h" -n1 HEAD'));
        echo "hash=".$commitHash."<br>";

        $commitDate = new \DateTime(trim(exec('git log -n1 --pretty=%ci HEAD')));
        $commitDate->setTimezone(new \DateTimeZone('UTC'));

        return $commitHash . " (" . $commitDate->format('Y-m-d H:m:s') . ")";
        //return sprintf('v%s.%s.%s-dev.%s (%s)', $MAJOR, $MINOR, $PATCH, $commitHash, $commitDate->format('Y-m-d H:m:s'));
    }

    /**
     * Get all branches: the hash of the current git HEAD
     */
    function getCurrentGitCommit() {
        $projectDir = $this->container->get('kernel')->getProjectDir();
        $path = $projectDir.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.".git"
            .DIRECTORY_SEPARATOR."refs".DIRECTORY_SEPARATOR."heads";

        $resArr = array();
        $res = "";

        if( $handle = opendir($path) ) {

            while (false !== ($entry = readdir($handle))) {

                if( $entry != "." && $entry != ".." ) {

                    //echo "$entry\n";
                    $branch = trim((string)$entry);
                    $resArr[] = $this->getBranchGitCommit($branch,$path);
                }
            }

            closedir($handle);
        }

        if( count($resArr) > 0 ) {
            $res = implode("<br>",$resArr);
        }

        return $res;
    }
    /**
     * Get the hash of the current git HEAD
     * @param str $branch The git branch to check
     * @return mixed Either the hash or a boolean false
     */
    function getBranchGitCommit( $branch='master', $path=NULL ) {
        $projectDir = $this->container->get('kernel')->getProjectDir(); //C:\Users\ch3\Documents\MyDocs\WCMC\ORDER\scanorder\Scanorders2
        //echo "projectDir=$projectDir<br>";
        //$projectDir = str_replace("Scanorders2","",$projectDir);

        if( !$path ) {
            $path = $projectDir . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".git" . DIRECTORY_SEPARATOR .
                "refs" . DIRECTORY_SEPARATOR . "heads";
        }

        $filename = $path.DIRECTORY_SEPARATOR.$branch;
        //echo $filename."<br>";

        if( file_exists($filename) ) {
            //OK
        } else {
            return false;
        }

        //$filename = sprintf('.git/refs/heads/%s',$branch);
        $hash = file_get_contents($filename);
        $hash = trim((string)$hash);

        $timestamp = filemtime($filename);
        if( $timestamp ) {
            $user = $this->security->getUser();
            //$timestamp = date("F d Y H:i:s.",$timestamp);
            //$dateTime = new \DateTime($timestamp);
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($timestamp);
            //$dateTimeUtc = \DateTime::createFromFormat('F d Y H:i:s',$timestamp);
            $dateTimeUser = $this->convertFromUtcToUserTimezone($dateTime,$user);
            $timestamp = $dateTimeUser->format("F d Y H:i");
        }

        if ( $hash ) {
            return "Current Version for branch $branch: " . $hash . "; " . $timestamp;
        } else {
            return false;
        }
    }

    /**
     * Get installed software (apache, php)
     */
    function getInstalledSoftware() {
        $res = NULL;
        $apacheVersion = NULL;
//        if(!function_exists('apache_get_version')){
//            function apache_get_version(){
//                if(!isset($_SERVER['SERVER_SOFTWARE']) || strlen($_SERVER['SERVER_SOFTWARE']) == 0){
//                    return false;
//                }
//                return $_SERVER["SERVER_SOFTWARE"];
//            }
//        }
        //$apacheVersion = $_SERVER["SERVER_SOFTWARE"];
        //$apacheVersion = apache_get_version();

        if( !$apacheVersion ) {
            if (function_exists('apache_get_version')) {
                $apacheVersion = apache_get_version();
            }
        }
        if( !$apacheVersion ) {
            if (!isset($_SERVER['SERVER_SOFTWARE']) || strlen($_SERVER['SERVER_SOFTWARE']) == 0) {
            } else {
                $apacheVersion = $_SERVER["SERVER_SOFTWARE"];
            }
        }

        if( $apacheVersion ) {
            $res = "Apache: " . $apacheVersion;
        }

        $phpVersion = phpversion();
        $res = $res . "<br>" . "PHP: ".$phpVersion;
        $phpVersion2 = PHP_VERSION;
        $res = $res . "<br>" . "PHP_VERSION: ".$phpVersion2;

        return $res;
    }

    function getFrameworkInfo() {
        $res = null;

        $projectRoot = $this->container->get('kernel')->getProjectDir();

        $phpPath = $this->getPhpPath();

        $command = $phpPath . " " . $projectRoot . "/bin/console about";

        //$process = new Process($command);
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(1800); //sec; 1800 sec => 30 min
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $info = $process->getOutput();

        //$divider = "-------------------- ---------------------------------------------------------------------------------------";
        $divider = "\n";

        $replace = "$divider<br>";

        $info = str_replace($divider, $replace, $info);

        $res = $res . $info;

        return $res;
    }

    public function getPhpPath() {
        $phpPath = "php";

        if( $this->isWinOs() ) {
            $phpPath = "php";
        } else {
//            $process = new Process("which php");
//            $process->setTimeout(1800); //sec; 1800 sec => 30 min
//            $process->run();
//            if (!$process->isSuccessful()) {
//                throw new ProcessFailedException($process);
//            }
//            $phpPath = $process->getOutput();
            //$phpPath = "/opt/remi/php74/root/usr/bin/php";
            $phpPath = "/opt/remi/php81/root/usr/bin/php";
            $phpPath = "php";

            if( !file_exists($phpPath) ) {
                $phpPath = "/bin/php";
            }

            if( !file_exists($phpPath) ) {
                $phpPath = "php";
            }
        }

        return $phpPath;
    }

//    public function gitVersion() {
//        //exec('git describe --always',$version_mini_hash);
//        $version_mini_hash = $this->runProcess('git describe --always');
//        echo "version_mini_hash=".$version_mini_hash."<br>";
//        print_r($version_mini_hash);
//        exec('git rev-list HEAD | wc -l',$version_number);
//        exec('git log -1',$line);
//        $version['short'] = "v1.".trim((string)$version_number[0]).".".$version_mini_hash[0];
//        $version['full'] = "v1.".trim((string)$version_number[0]).".$version_mini_hash[0] (".str_replace('commit ','',$line[0]).")";
//        return $version;
//    }
    public function runProcess($command) {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            //echo 'This is a server using Windows!';
            $windows = true;
            $linux = false;
        } else {
            //echo 'This is a server not using Windows! Assume Linux';
            $windows = false;
            $linux = true;
        }

        $old_path = getcwd();
        //echo "webPath=$old_path<br>";

        $deploy_path = str_replace("public","",$old_path);
        //echo "deploy_path=$deploy_path<br>";
        //exit('111');

        if( is_dir($deploy_path) ) {
            //echo "deploy path exists! <br>";
        } else {
            //echo "not deploy path exists: $deploy_path <br>";
            exit('No deploy path exists in the filesystem; deploy_path=: '.$deploy_path);
        }

        //switch to deploy folder
        chdir($deploy_path);
        //echo "pwd=[".exec("pwd")."]<br>";

        if( $linux ) {
            //$process = new Process($command);
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(1800); //sec; 1800 sec => 30 min
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            $res = $process->getOutput();
        }

        if( $windows ) {
            $res = exec($command);
            //echo "res=".$res."<br>";
        }

        chdir($old_path);

        return $res;
    }

    public function classNameUrlMapper($className) {

        $mapArr = array(
            "SiteList"                  => "admin/list/sites",
            "User"                      => "user",
            "Patient"                   => "patient",
            "Message"                   => "entry/view",
            "Roles"                     => "admin/list-manager/id/4",
            "VacReqRequest"             => "show",
            "Document"                  => "file-view",
            "Institution"               => "admin/list/institutions",
            "FellowshipApplication"     => "show",
            "ResidencyApplication"      => "show",
            "SiteParameters"            => "settings/settings-id", //"settings",
            "VacReqUserCarryOver"       => "show",
            "Project"                   => "project/show",
            "TransResRequest"           => "work-request/show",
            "DefaultReviewer"           => "default-reviewers/show",
            "Invoice"                   => "invoice/show",
        );

        if (array_key_exists($className,$mapArr))
        {
            $url = $mapArr[$className];
        } else {
            $url = null;
        }

        return $url;
    }

    public function getSiteNameByAbbreviation($abbreviation) {
        $siteObject = $this->em->getRepository('AppUserdirectoryBundle:SiteList')->findOneByAbbreviation($abbreviation);
        return $siteObject->getSiteName();
    }

    //TODO: generate two thumbnails: small and medium
    //get small thumbnail - i.e. used for the fellowship application list
    //get small thumbnail - i.e. used for the fellowship application view
    public function generateTwoThumbnails($document) {
        $res = NULL;
        $documentTypeObject = $document->getType();
        if( $documentTypeObject) {
            if( $documentTypeObject->getName() == "Fellowship Photo" || $documentTypeObject->getName() == "Avatar Image" ) {

                //$dest = $document->getAbsoluteUploadFullPath();
                //$dest = $document->getServerPath();
                //$dest = $document->getFullServerPath();

                $src = $document->getServerPath();
                $uniquename = $document->getUniquename();

//                if (file_exists($src)) {
//                    echo "The file $src exists <br>";
//                }
//                else {
//                    echo "The file $src does not exists <br>";
//                }

                //Small
                $desired_width = 65;
                $uniquenameSmall = "small" . "-" . $uniquename;
                $dest = str_replace($uniquename,$uniquenameSmall,$src);
                //echo $desired_width.": dest=".$dest."<br>";
                $destSmall = $this->makeThumb($src, $dest, $desired_width);

                //Medium
                $desired_width = 260;
                $uniquename = $document->getUniquename();
                $uniquenameSmall = "medium" . "-" . $uniquename;
                $dest = str_replace($uniquename,$uniquenameSmall,$src);
                //echo $desired_width.": dest=".$dest."<br>";
                $destMedium = $this->makeThumb($src, $dest, $desired_width);

                //exit(111);
                if( $destSmall || $destMedium ) {
                    $res = $destSmall . ", " . $destMedium;
                }
            }
        }
        return $res;
    }
    public function makeThumb($src, $dest, $desired_width) {

        if (file_exists($dest)) {
            //echo "The file $dest exists <br>";
            //$logger = $this->container->get('logger');
            //$logger->notice("$desired_width thumbnail already exists. dest=" . $dest);
            return null;
        }
        else {
            //echo "The file $dest does not exists <br>";
        }

        if( strpos((string)$src, '.jpg') !== false || strpos((string)$src, '.jpeg') !== false ) {
            //ok, file is jpeg
        } else {
            return null;
        }

        /* read the source image */
        $source_image = imagecreatefromjpeg($src);
        $width = imagesx($source_image);
        $height = imagesy($source_image);

        /* find the "desired height" of this thumbnail, relative to the desired width  */
        $desired_height = floor($height * ($desired_width / $width));

        $desired_width = floor($desired_width);

        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        if( !$virtual_image ) {
            return null;
        }

        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

        if( !$virtual_image ) {
            return null;
        }

        /* create the physical thumbnail image to its destination */
        imagejpeg($virtual_image, $dest);

        return $dest;
    }

    public function createCrons() {
        if( $this->isWindows() ) {
            //Windows
            $this->createCronsWindows();
        } else {
            //Linux
            $this->createCronsLinux();
        }
    }
    public function createCronsWindows() {

        $projectDir = $this->container->get('kernel')->getProjectDir();
        $console = $projectDir.DIRECTORY_SEPARATOR."bin".DIRECTORY_SEPARATOR."console";

        ////////////////////// 1) swiftMailer (implemented on email util (EmailUtil->createEmailCronJob)) //////////////////////
        //$emailUtil = $this->container->get('user_mailer_utility');
        //$emailUtil->createEmailCronJobWindows();

        $cronJobName = "swift";
        if( $this->getCronStatusWindows($cronJobName,true) === false ) {

            $frequencyMinutes = 15;

            $cronJobCommand = 'php \"' . $console . '\" cron:swift --env=prod';
            $cronJobCommand = '"' . $cronJobCommand . '"';

            $command = 'SchTasks /Create /SC MINUTE /MO ' . $frequencyMinutes .
                ' /IT ' .
                //' /RU system'.
                ' /TN ' . $cronJobName .
                ' /TR ' . $cronJobCommand . '';
            //echo "SchTasks add: ".$command."<br>";
            //$logger->notice("SchTasks:".$command);
            $resEmail = exec($command);
        }
        ////////////////////// EOF 1) swiftMailer (implemented on email util (EmailUtil->createEmailCronJob)) //////////////////////

        ////////////////////// 2) importFellowshipApplications (every hour) //////////////////////
        //command:    php
        //arguments(working): "E:\Program Files (x86)\pacsvendor\pacsname\htdocs\order\scanorder\Scanorders2\bin\console" cron:importfellapp --env=prod
        $cronJobName = "importfellapp";
        if( $this->getCronStatusWindows($cronJobName,true) === false ) {
            $frequencyMinutes = 60;

            $cronJobCommand = 'php \"' . $console . '\" cron:importfellapp --env=prod';
            $cronJobCommand = '"' . $cronJobCommand . '"';

            $command = 'SchTasks /Create /SC MINUTE /MO ' . $frequencyMinutes .
                ' /IT ' .
                //' /RU system'.
                ' /TN ' . $cronJobName .
                ' /TR ' . $cronJobCommand . '';
            //echo "SchTasks add: ".$command."<br>";
            //$logger->notice("SchTasks:".$command);
            $resFellapp = exec($command);
        }
        ////////////////////// EOF 2) importFellowshipApplications (every hour) //////////////////////

        ////////////////////// 3) UnpaidInvoiceReminder (at 6 am every Monday) //////////////////////
        //cron:invoice-reminder-emails --env=prod
        $cronJobName = "invoice-reminder-emails";
        if( $this->getCronStatusWindows($cronJobName,true) === false ) {

            $cronJobCommand = 'php \"' . $console . '\" cron:invoice-reminder-emails --env=prod';
            $cronJobCommand = '"' . $cronJobCommand . '"';

            $command = 'SchTasks /Create /SC WEEKLY /D MON /MO 1 /ST 6:00' .
                ' /IT ' .
                //' /RU system'.
                ' /TN ' . $cronJobName .
                ' /TR ' . $cronJobCommand . '';
            //echo "SchTasks add: ".$command."<br>";
            //$logger->notice("SchTasks:".$command);
            $resFellapp = exec($command);
        }
        ////////////////////// EOF 3) UnpaidInvoiceReminder (at 6 am every Monday) //////////////////////

//        ////////////////////// 3b) Expiration Reminder (at 5 am every Monday) //////////////////////
//        $cronJobName = "expiration-reminder-emails";
//        if( $this->getCronStatusWindows($cronJobName,true) === false ) {
//
//            $cronJobCommand = 'php \"' . $console . '\" cron:expiration-reminder-emails --env=prod';
//            $cronJobCommand = '"' . $cronJobCommand . '"';
//
//            $command = 'SchTasks /Create /SC WEEKLY /D MON /MO 1 /ST 5:00' .
//                ' /IT ' .
//                //' /RU system'.
//                ' /TN ' . $cronJobName .
//                ' /TR ' . $cronJobCommand . '';
//            //echo "SchTasks add: ".$command."<br>";
//            //$logger->notice("SchTasks:".$command);
//            $resFellapp = exec($command);
//        }
//        ////////////////////// EOF 3b) Expiration Reminder (at 5 am every Monday) //////////////////////

    }

    //Can use package: https://packagist.org/packages/hellogerard/jobby
    //Show for specific user: crontab -u apache -l
    //Remove for specific user: crontab -u apache -r
    //Create cron jobs:
    //1) swiftMailer (implemented on email util (EmailUtil->createEmailCronJob))
    //2) importFellowshipApplications (every hour)
    //3) UnpaidInvoiceReminder (at 6 am every Monday)
    //TODO: auto generation adds ^M at the end of new line
    public function createCronsLinux() {
        $logger = $this->container->get('logger');
        $logger->notice("Creating cron jobs for Linux");
        $projectDir = $this->container->get('kernel')->getProjectDir();

        //////////////////// 1) swiftMailer (implemented on email util (EmailUtil->createEmailCronJob)) ////////////////////
        //$this->createEmailCronLinux();
        //////////////////// EOF 1) swiftMailer (implemented on email util (EmailUtil->createEmailCronJob)) ////////////////////

        //////////////////// 2) ImportFellowshipApplications (every hour) ////////////////////

        //first delete existing cron job
        //$this->removeCronJob($crontab,$fellappCronJobCommand);

        $cronJobName = "cron:importfellapp --env=prod";

        $phpPath = $this->getPhpPath();
        $fellappCronJobCommand = $phpPath." ".$projectDir.DIRECTORY_SEPARATOR."bin/console $cronJobName";

        $fellappCronJob = "00 * * * *" . " " . $fellappCronJobCommand; //0 minutes - every hour

        if( $this->getCronJobFullNameLinux($cronJobName) === false ) {

            $res = $this->addCronJobLinux($fellappCronJob);

            $res = "Created $cronJobName cron job";
        } else {
            $res = "$cronJobName already exists";
        }

        $logger->notice($res);
        //////////////////// EOF ImportFellowshipApplications ////////////////////

        //////////////////// 2a) Verify Import Fellowship Applications (every 6 hours) ////////////////////

        $cronJobName = "cron:verifyimport --env=prod";

        $phpPath = $this->getPhpPath();
        $fellappVerifyImportCronJobCommand = $phpPath." ".$projectDir.DIRECTORY_SEPARATOR."bin/console $cronJobName";

        $fellappVerifyImportCronJob = "0 */6 * * *" . " " . $fellappVerifyImportCronJobCommand; //every 6 hours

        if( $this->getCronJobFullNameLinux($cronJobName) === false ) {

            $res = $this->addCronJobLinux($fellappVerifyImportCronJob);

            $res = "Created $cronJobName cron job: $fellappVerifyImportCronJob";
        } else {
            $res = "$cronJobName already exists";
        }

        $logger->notice($res);
        //////////////////// EOF ImportFellowshipApplications ////////////////////

        //////////////////// 3) UnpaidInvoiceReminder (at 6 am every Monday) ////////////////////
        $cronJobName = "cron:invoice-reminder-emails --env=prod";

        $phpPath = $this->getPhpPath();
        $trpCronJobCommand = $phpPath." ".$projectDir.DIRECTORY_SEPARATOR."bin/console $cronJobName";

        $trpCronJob = "00 06 * * Mon" . " " . $trpCronJobCommand; //every monday (0 - 6) (Sunday=0 or 7) OR sun,mon,tue,wed,thu,fri,sat
        //$trpCronJob = "0 6 * * 1" . " " . $trpCronJobCommand; //run every monday at 6am (https://stackoverflow.com/questions/25676475/run-every-monday-at-5am?rq=1)
        //$trpCronJob = "41 16 * * 2" . " " . $trpCronJobCommand; //testing: run every tuesday at 17:32
        //$trpCronJob = "*/10 * * * *" . " " . $trpCronJobCommand; //testing: At minute 10

        if( $this->getCronJobFullNameLinux($cronJobName) === false ) {
            $this->addCronJobLinux($trpCronJob);
            $res = "Created $cronJobName cron job";
        } else {
            $res = "$cronJobName already exists";
        }

        $logger->notice($res);
        //////////////////// EOF 3) UnpaidInvoiceReminder (at 6 am every Monday) ////////////////////

        //////////////////// 3b) Expiration Reminder (at 5 am every Monday) ////////////////////
        $cronJobName = "cron:expiration-reminder-emails --env=prod";

        $phpPath = $this->getPhpPath();
        $trpCronJobCommand = $phpPath." ".$projectDir.DIRECTORY_SEPARATOR."bin/console $cronJobName";

        $trpCronJob = "00 05 * * Mon" . " " . $trpCronJobCommand; //every monday (0 - 6) (Sunday=0 or 7) OR sun,mon,tue,wed,thu,fri,sat

        if( $this->getCronJobFullNameLinux($cronJobName) === false ) {
            $this->addCronJobLinux($trpCronJob);
            $res = "Created $cronJobName cron job";
        } else {
            $res = "$cronJobName already exists";
        }

        $logger->notice($res);
        //////////////////// EOF 3b) Expiration Reminder (at 5 am every Monday) ////////////////////

        //////////////////// 4) Status (every 30 minutes) ////////////////////
//        $cronJobName = "cron:status --env=prod";
//
//        $phpPath = $this->getPhpPath();
//        $statusCronJobCommand = $phpPath." ".$projectDir.DIRECTORY_SEPARATOR."bin/console $cronJobName";
//
//        $statusFrequency = 30;
//        $statusFrequency = 5;
//        $statusCronJob = "*/$statusFrequency * * * *" . " " . $statusCronJobCommand;
//
//        if( $this->getCronJobFullNameLinux($cronJobName) === false ) {
//            $this->addCronJobLinux($statusCronJob);
//            $res = "Created $cronJobName cron job";
//        } else {
//            $res = "$cronJobName already exists";
//        }
//
//        $logger->notice($res);
        $res = $this->createStatusCronLinux();
        //$logger->notice($res);
        //////////////////// EOF 4) Status ////////////////////

        return $res;
    }
    public function createStatusCronLinux( $statusFrequency = 30 ) {
        $logger = $this->container->get('logger');
        $logger->notice("Creating status cron job for Linux");
        $projectDir = $this->container->get('kernel')->getProjectDir();

        $cronJobName = "cron:status --env=prod";

        $phpPath = $this->getPhpPath();
        $statusCronJobCommand = $phpPath." ".$projectDir.DIRECTORY_SEPARATOR."bin/console $cronJobName";

        $statusFrequency = 30;
        //$statusFrequency = 5; //testing
        $statusCronJob = "*/$statusFrequency * * * *" . " " . $statusCronJobCommand;

        if( $this->getCronJobFullNameLinux($cronJobName) === false ) {
            $this->addCronJobLinux($statusCronJob);
            $res = "Created $cronJobName cron job";
        } else {
            $res = "$cronJobName already exists";
        }

        $logger->notice($res);
    }
    //Dummy test cron job to check new line for multiple jobs
    public function createTestStatusCronLinux( $statusFrequency = 30 ) {
        $logger = $this->container->get('logger');
        $logger->notice("Creating statustest cron job for Linux");
        $projectDir = $this->container->get('kernel')->getProjectDir();

        $cronJobName = "cron:statustest --env=prod";

        $phpPath = $this->getPhpPath();
        $statusCronJobCommand = $phpPath." ".$projectDir.DIRECTORY_SEPARATOR."bin/console $cronJobName";

        $statusFrequency = 30;
        //$statusFrequency = 5; //testing
        $statusCronJob = "*/$statusFrequency * * * *" . " " . $statusCronJobCommand;

        if( $this->getCronJobFullNameLinux($cronJobName) === false ) {
            $this->addCronJobLinux($statusCronJob);
            $res = "Created $cronJobName cron job";
        } else {
            $res = "$cronJobName already exists";
        }

        $logger->notice($res);
    }
    public function createEmailCronLinux( $mailerFlushQueueFrequency = null ) {
        $userSecUtil = $this->container->get('user_security_utility');

        $useSpool = $userSecUtil->getSiteSettingParameter('mailerSpool');

        if( !$mailerFlushQueueFrequency ) {
            $mailerFlushQueueFrequency = $userSecUtil->getSiteSettingParameter('mailerFlushQueueFrequency');
        }

        if( $useSpool && $mailerFlushQueueFrequency ) {
            //OK create email cron
        } else {
            return false;
        }

        $logger = $this->container->get('logger');
        $logger->notice("Creating cron jobs for Linux");
        $projectDir = $this->container->get('kernel')->getProjectDir();

        $phpPath = $this->getPhpPath();
        $emailCronJobCommand = $phpPath." ".$projectDir.DIRECTORY_SEPARATOR."bin/console cron:swift --env=prod";

        $emailCronJob = "*/$mailerFlushQueueFrequency * * * *" . " " . $emailCronJobCommand;

        $cronJobName = "cron:swift";
        if( $this->getCronJobFullNameLinux($cronJobName) === false ) {
            $this->addCronJobLinux($emailCronJob);
            $res = "Created $cronJobName cron job";
        } else {
            $res = "$cronJobName already exists";
        }

        $logger->notice($res);

        return $res;
    }

    public function addCronJobLinux( $fullCommand ) {
        $crontab = new Crontab();
        $res = $crontab->addJob($fullCommand);
        return $res;
    }
    public function removeCronJobLinuxByCommandName( $commandName ) {
        $res = false;
        $cronJobFullName = $this->getCronJobFullNameLinux($commandName);
        if( $cronJobFullName ) {
            //echo "removeCronJobLinuxByCommandName: cronJobFullName=[$cronJobFullName] <br>";
            $crontab = new Crontab();
            $res = $crontab->removeJob($cronJobFullName);
        }

        return $res;
    }

    public function getCronStatus($cronJobName) {
        if( $this->isWindows() ){
            return $this->getCronStatusWindows($cronJobName);
        } else {
            return $this->getCronStatusLinux($cronJobName);
        }
    }

    public function getCronJobFullNameLinux($cronJobName) {
        $crontab = new Crontab();

        //$jobs = $crontab->getJobsAsSimpleArray();
        $jobs = $crontab->getJobs();

        if( isset($jobs) && is_array($jobs) ) {

            foreach ($jobs as $job) {
                if (strpos((string)$job, $cronJobName) !== false) {
                    return $job."";
                    break;
                }
            }
        }

        return false;
    }
    public function getCronStatusLinux($cronJobName, $asBoolean=false) {
        $cronJobFullName = $this->getCronJobFullNameLinux($cronJobName);

        if( $asBoolean ) {
            return $cronJobFullName;
        } else {
            if( $cronJobFullName ) {
                $resStr = '<font color="green">'.$cronJobName.' cron job status: '.$cronJobFullName.'.</font>';
            } else {
                $resStr = '<font color="red">'.$cronJobName.' cron job status: not found.</font>';
            }
            return $resStr;
        }

        return false;
    }

    public function getCronStatusWindows($cronJobName, $asBoolean=false) {
        //$cronJobName = "Swiftmailer";
        $command = 'SchTasks | FINDSTR "'.$cronJobName.'"';
        $res = exec($command);

        if( $res ) {
            if( $asBoolean ) {
                $res = true;
            } else {
                //$res = "Cron job status: " . $crontab->render();
                $res = '<font color="green">Cron job status: '.$res.'.</font>';
            }
        } else {
            if( $asBoolean ) {
                $res = false;
            } else {
                //$res = "Cron job status: " . $crontab->render();
                $res = '<font color="red">Cron job status: not found.</font>';
            }
        }
        //exit($res);
        return $res;
    }

    //https://www.php.net/manual/en/function.realpath.php
    //Will convert /path/to/test/.././..//..///..///../one/two/../three/filename to ../../one/three/filename
    function normalizePath($path)
    {
        $parts = array();// Array to build a new path from the good parts
        $path = str_replace('\\', '/', $path);// Replace backslashes with forwardslashes
        $path = preg_replace('/\/+/', '/', $path);// Combine multiple slashes into a single slash
        $segments = explode('/', $path);// Collect path segments
        $test = '';// Initialize testing variable
        foreach($segments as $segment)
        {
            if($segment != '.')
            {
                $test = array_pop($parts);
                if(is_null($test))
                    $parts[] = $segment;
                else if($segment == '..')
                {
                    if($test == '..')
                        $parts[] = $test;

                    if($test == '..' || $test == '')
                        $parts[] = $segment;
                }
                else
                {
                    $parts[] = $test;
                    $parts[] = $segment;
                }
            }
        }
        return implode('/', $parts);
    }

    function execInBackground($cmd) {
        if( $this->isWinOs() ){
            //pclose(popen("start /B ". $cmd, "r"));
            $oExec = pclose(popen("start /B ". $cmd, "r"));
        }
        else {
            //$phppath = "/opt/remi/php74/root/usr/bin/php";
            $phppath = $this->getPhpPath();
            $cmd = str_replace("php ", $phppath . " ", $cmd);
            $logger = $this->container->get('logger');
            $logger->notice("execInBackground cmd=" . $cmd);
            //echo exec($cmd, $oExec);
            $oExec = exec($cmd . " > /dev/null &");
        }

        return $oExec;
    }

    public function getPublicFolderName() {
        $projectDir = $this->container->getParameter('kernel.project_dir');
        exit("projectDir=$projectDir");
    }

    public function getUserEncoder($user=null) {
//        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
//        $encoders = [
//            User::class => $defaultEncoder, // Your user class. This line specify you ant sha512 encoder for this user class
//        ];
//
//        $encoderFactory = new EncoderFactory($encoders);
//        $encoder = $encoderFactory->getEncoder($user);

        $encoder = $this->container->get('security.password_encoder');
        return $encoder;
    }

    //check system status. Used by StatusCronCommand (php bin/console cron:status --env=prod)
    public function checkStatus() {

        $userSecUtil = $this->container->get('user_security_utility');
        $emailUtil = $this->container->get('user_mailer_utility');

        $msg = "checkStatus";

        $maintenance = $userSecUtil->getSiteSettingParameter('maintenance');
        if( !$maintenance ) {
            return "Maintenance is off";
        }

        //1) check event log for
        // "Site Settings parameter [maintenance] has been updated by" and
        // "updated value: 1"

        $repository = $this->em->getRepository('AppUserdirectoryBundle:Logger');
        $dql = $repository->createQueryBuilder("logger");

        $dql->leftJoin('logger.eventType', 'eventType');

        $queryParameters = array();

        //Site Settings Parameter Updated
        $dql->andWhere("eventType.name = :eventTypeName");
        $queryParameters['eventTypeName'] = 'Site Settings Parameter Updated';

        //Site Settings parameter [maintenance] has been updated by
        $eventStr1 = "Site Settings parameter [maintenance] has been updated by";
        $dql->andWhere("logger.event LIKE :eventStr1");
        $queryParameters['eventStr1'] = '%'.$eventStr1.'%';

        //updated value:<br>1
        $eventStr2 = "updated value:<br>1";
        $dql->andWhere("logger.event LIKE :eventStr2");
        $queryParameters['eventStr2'] = '%'.$eventStr2.'%';

        $dql->orderBy("logger.id","DESC");

        $query = $this->em->createQuery($dql);
        $query->setParameters( $queryParameters );

        $query->setMaxResults(1);
        $log = $query->getOneOrNullResult();

        if( !$log ) {
            return "Maintenance is off";
        }

        //2) get latest date
        //echo "log ID=".$log->getId()."<br>";
        $latestDate = $log->getCreationdate();
        //echo "latestDate=".$latestDate->format('Y-m-d H:i:s')."<br>";

        //3) check if currentDate is more latestDate by 30 min
        $currentDate = new \DateTime();
        //echo "currentDate=".$currentDate->format('Y-m-d H:i:s')."<br>";

        $maxTime = '30';
        //$maxTime = '1'; //testing

        $currentDate = $currentDate->modify("-$maxTime minutes");

        if( $currentDate > $latestDate ) {
            //$msg = "more than $maxTime min";
            //send email to admin
            $emails = $userSecUtil->getUserEmailsByRole(null,"Platform Administrator");

            //except these users
            $exceptionUsers = $userSecUtil->getSiteSettingParameter('emailCriticalErrorExceptionUsers');
            $exceptionUsersEmails = array();
            foreach($exceptionUsers as $exceptionUser) {
                // echo "exceptionUser=".$exceptionUser."<br>";
                $exceptionUsersEmails[] = $exceptionUser->getSingleEmail();
            }

            if( count($exceptionUsersEmails) > 0 ) {
                $emails = array_diff($emails, $exceptionUsersEmails);
            }

            $emails = array_unique($emails);

            //echo "emails: <br>";
            //print_r($emails);
            //exit('111');

            $subject = "Maintenance Mode On Longer than $maxTime minutes";
            $msg = "Maintenance Mode has been turned on for longer than $maxTime minutes. Please turn it off to allow users to log in:";

            //employees_siteparameters_edit
            //@Route("/{id}/edit", name="employees_siteparameters_edit", methods={"GET"})
            //$param = trim((string)$request->get('param') );

            $router = $userSecUtil->getRequestContextRouter();

            $url = $router->generate(
                'employees_siteparameters_edit',
                array(
                    'id' => 1,
                    'param' => 'maintenance'
                ),
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $msg = $msg . " " . $url;

            $emailUtil->sendEmail($emails,$subject,$msg);

        } else {
            $msg = "Max time is ok";
        }

        //4) send warning email

        return $msg;
    }
    
    ///////////////////////////// TELEPHONY ////////////////////////////////////
    public function assignVerificationCode($user,$phoneNumber) {
        //$text = random_int(100000, 999999);
        $code = $this->generateVerificationCode();

        $userInfo = $user->getUserInfoByPreferredMobilePhone($phoneNumber);

        if( $userInfo ) {
            $userInfo->setMobilePhoneVerifyCode($code);
            $userInfo->setPreferredMobilePhoneVerified(false); //should it be unchanged?
            $userInfo->setMobilePhoneVerifyCodeDate(new \DateTime());
            $this->em->flush();
        }

        return $code;
    }
    public function generateVerificationCode($counter=0) {
        $code = random_int(100000, 999999);
        //$code = 111;

        $repository = $this->em->getRepository('AppUserdirectoryBundle:UserInfo');
        $dql =  $repository->createQueryBuilder("userinfo");
        $dql->select('userinfo');

        $dql->where("userinfo.mobilePhoneVerifyCode = :mobilePhoneVerifyCode");
        //$queryParameters = array('mobilePhoneVerifyCode'=>$code);

        $dql->andWhere("userinfo.mobilePhoneVerifyCodeDate >= :expireDate");
        $expireDate = new \DateTime();
        $expireDate->modify("-2 day");

        $queryParameters = array(
            'mobilePhoneVerifyCode'=>$code,
            'expireDate'=>$expireDate->format('Y-m-d')
        );

        $query = $this->em->createQuery($dql);
        $query->setParameters( $queryParameters );

        $userinfos = $query->getResult();

        if( count($userinfos) > 0 ) {
            if( $counter > 100 ) {
                throw new \Exception( 'Possible error in generateVerificationCode: counter='.$counter );
            }

            $counter++;
            $code = $this->generateVerificationCode($counter);
        }

        return $code;
    }
    //https://www.twilio.com/docs/sms/tutorials/how-to-send-sms-messages-php
    public function sendText( $phoneNumber, $textToSend ) {
        // Find your Account Sid and Auth Token at twilio.com/console
        // DANGER! This is insecure. See http://twil.io/secure
        //$sid    = "ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
        //$token  = "your_auth_token";
        //$twilio = new Client($sid, $token);
//        $message = $twilio->messages
//            ->create("+1xxx", // to
//                [
//                    "body" => "This is the ship that made the Kessel Run in fourteen parsecs?",
//                    "from" => "+1xxx"
//                ]
//            );

        $userSecUtil = $this->container->get('user_security_utility');

        $phoneNumberVerification = $userSecUtil->getSiteSettingParameter('phoneNumberVerification','Telephony');
        if( !$phoneNumberVerification ) {
            $message = (object) [
                'errorMessage' => "Phone number verification is disabled.",
            ];
            return $message;
        }

        $twilioSid = $userSecUtil->getSiteSettingParameter('twilioSid','Telephony');
        $twilioApiKey = $userSecUtil->getSiteSettingParameter('twilioApiKey','Telephony');
        $fromPhoneNumber = $userSecUtil->getSiteSettingParameter('fromPhoneNumber','Telephony');

        //$twilioSid = "xxxxx";
        //$twilioApiKey = "xxxxx";
        //$fromPhoneNumber = "xxxxx";

        $twilio = new Client($twilioSid, $twilioApiKey);

        $message = $twilio->messages
            ->create($phoneNumber, // to
                [
                    "body" => $textToSend,      //"This is the test telephony message",
                    "from" => $fromPhoneNumber //"+11234567890"
                ]
            );

        //print($message->sid);

        return $message;
    }
    public function userHasPhoneNumber($phoneNumber) {
        $user = $this->security->getUser();

        $userInfo = $user->getUserInfoByPreferredMobilePhone($phoneNumber);
        //$userInfo = $user->getUserInfo();
        
        if( $userInfo ) {
            //exit($userInfo->getId());
            $userPreferredMobilePhone = $userInfo->getPreferredMobilePhone();
            //echo "[$phoneNumber] =? [$userPreferredMobilePhone]<br>";
            //exit();
            //exit("phoneNumber=[$phoneNumber] ?= userPreferredMobilePhone=[$userPreferredMobilePhone]");
            if( $phoneNumber && $userPreferredMobilePhone && $phoneNumber == $userPreferredMobilePhone ) {
                return true;
            }

            //additional canonical check (without '+')
            $phoneNumber = str_replace('+','',$phoneNumber);
            $userPreferredMobilePhone = str_replace('+','',$userPreferredMobilePhone);
            //echo "[$phoneNumber] =? [$userPreferredMobilePhone]<br>";
            //exit();
            if( $phoneNumber && $userPreferredMobilePhone && $phoneNumber == $userPreferredMobilePhone ) {
                return true;
            }
        } else {
            //exit("userInfo not found by phoneNumber=".$phoneNumber);
        }

        return false;
    }
    public function getVerificationUrl( $verificationCode ) {
        //$user = $this->security->getUser();
        //employees_verify_mobile_code
        $url = $this->container->get('router')->generate(
            'employees_verify_mobile_code',
            array(
                'verificationCode' => $verificationCode,
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        //$urlFull = " <a data-toggle='tooltip' title='Verification Link' href=".$url.">Verify Mobile Phone Number</a>";

        return $url;
    }
    public function getUserByVerificationCode( $verificationCode ) {
        if( !$verificationCode ) {
            return null;
        }

        $repository = $this->em->getRepository('AppUserdirectoryBundle:User');
        $dql =  $repository->createQueryBuilder("user");
        $dql->select('user');
        $dql->leftJoin('user.infos','infos');

        $dql->where("infos.mobilePhoneVerifyCode = :mobilePhoneVerifyCode");
        $queryParameters = array('mobilePhoneVerifyCode'=>$verificationCode);

        $query = $this->em->createQuery($dql);
        $query->setParameters( $queryParameters );

        $users = $query->getResult();
        //echo "users count=".count($users)."<br>";
        //exit('111');

        if( count($users) > 0 ) {
            return $users[0];
        }

        return null;
    }


    public function assignAccountRequestVerificationCode($userRequest,$objectName,$phoneNumber) {
        //$text = random_int(100000, 999999);
        $code = $this->generateAccountRequestVerificationCode($objectName);

        //$userInfo = $userRequest->getMobilePhone($phoneNumber);

        if( $userRequest ) {
            $userRequest->setMobilePhoneVerifyCode($code);
            $userRequest->setMobilePhoneVerifyCodeDate(new \DateTime());
            $userRequest->setMobilePhoneVerified(false); //should it be unchanged?
            $this->em->flush();
        }

        return $code;
    }
    public function generateAccountRequestVerificationCode($objectName,$counter=0) {
        $code = random_int(100000, 999999);

        $repository = $this->em->getRepository('AppUserdirectoryBundle:'.$objectName);
        $dql =  $repository->createQueryBuilder("userrequest");
        $dql->select('userrequest');

        $dql->where("userrequest.mobilePhoneVerifyCode = :mobilePhoneVerifyCode");
        //$queryParameters = array('mobilePhoneVerifyCode'=>$code);

        $dql->andWhere("userrequest.mobilePhoneVerifyCodeDate >= :expireDate");
        $expireDate = new \DateTime();
        $expireDate->modify("-2 day");

        $queryParameters = array(
            'mobilePhoneVerifyCode'=>$code,
            'expireDate'=>$expireDate->format('Y-m-d')
        );

        $query = $this->em->createQuery($dql);
        $query->setParameters( $queryParameters );

        $userrequests = $query->getResult();

        if( count($userrequests) > 0 ) {
            if( $counter > 100 ) {
                throw new \Exception( 'Possible error in generateVerificationCode: counter='.$counter );
            }

            $counter++;
            $code = $this->generateAccountRequestVerificationCode($objectName,$counter);
        }

        return $code;
    }

    public function setVerificationEventLog($eventType, $event, $testing=false) {
        $user = null;
        if( $this->security ) {
            $user = $this->security->getUser();
        } 
        $userSecUtil = $this->container->get('user_security_utility');
        if( !$testing ) {
            //            createUserEditEvent($sitename,$event,$user,$subjectEntities,$request,$action='Unknown Event')
            $userSecUtil->createUserEditEvent($this->container->getParameter('employees.sitename'), $event, $user, $user, null, $eventType);
        }
    }

//    public function verificationCodeIsNotExpired( $mobilePhoneHolder ) {
//        $expireDate = new \DateTime();
//        $expireDate->modify("-2 day");
//        $verificationCodeCreationDate = $mobilePhoneHolder->getMobilePhoneVerifyCodeDate();
//        if( !$verificationCodeCreationDate ) {
//            return true;
//        }
//
//        if( $mobilePhoneHolder && $expireDate && $verificationCodeCreationDate >= $expireDate ) {
//            return true;
//        }
//
//        return false;
//    }

    ///////////////////////////// EOF TELEPHONY ////////////////////////////////////


    public function findCountryByIsoAlpha3( $alpha3 ) {

        $iso_array = array(
            'ABW'=>'Aruba',
            'AFG'=>'Afghanistan',
            'AGO'=>'Angola',
            'AIA'=>'Anguilla',
            'ALA'=>'Åland Islands',
            'ALB'=>'Albania',
            'AND'=>'Andorra',
            'ARE'=>'United Arab Emirates',
            'ARG'=>'Argentina',
            'ARM'=>'Armenia',
            'ASM'=>'American Samoa',
            'ATA'=>'Antarctica',
            'ATF'=>'French Southern Territories',
            'ATG'=>'Antigua and Barbuda',
            'AUS'=>'Australia',
            'AUT'=>'Austria',
            'AZE'=>'Azerbaijan',
            'BDI'=>'Burundi',
            'BEL'=>'Belgium',
            'BEN'=>'Benin',
            'BES'=>'Bonaire, Sint Eustatius and Saba',
            'BFA'=>'Burkina Faso',
            'BGD'=>'Bangladesh',
            'BGR'=>'Bulgaria',
            'BHR'=>'Bahrain',
            'BHS'=>'Bahamas',
            'BIH'=>'Bosnia and Herzegovina',
            'BLM'=>'Saint Barthélemy',
            'BLR'=>'Belarus',
            'BLZ'=>'Belize',
            'BMU'=>'Bermuda',
            'BOL'=>'Bolivia, Plurinational State of',
            'BRA'=>'Brazil',
            'BRB'=>'Barbados',
            'BRN'=>'Brunei Darussalam',
            'BTN'=>'Bhutan',
            'BVT'=>'Bouvet Island',
            'BWA'=>'Botswana',
            'CAF'=>'Central African Republic',
            'CAN'=>'Canada',
            'CCK'=>'Cocos (Keeling) Islands',
            'CHE'=>'Switzerland',
            'CHL'=>'Chile',
            'CHN'=>'China',
            'CIV'=>'Côte d\'Ivoire',
            'CMR'=>'Cameroon',
            'COD'=>'Congo, the Democratic Republic of the',
            'COG'=>'Congo',
            'COK'=>'Cook Islands',
            'COL'=>'Colombia',
            'COM'=>'Comoros',
            'CPV'=>'Cape Verde',
            'CRI'=>'Costa Rica',
            'CUB'=>'Cuba',
            'CUW'=>'Curaçao',
            'CXR'=>'Christmas Island',
            'CYM'=>'Cayman Islands',
            'CYP'=>'Cyprus',
            'CZE'=>'Czech Republic',
            'DEU'=>'Germany',
            'DJI'=>'Djibouti',
            'DMA'=>'Dominica',
            'DNK'=>'Denmark',
            'DOM'=>'Dominican Republic',
            'DZA'=>'Algeria',
            'ECU'=>'Ecuador',
            'EGY'=>'Egypt',
            'ERI'=>'Eritrea',
            'ESH'=>'Western Sahara',
            'ESP'=>'Spain',
            'EST'=>'Estonia',
            'ETH'=>'Ethiopia',
            'FIN'=>'Finland',
            'FJI'=>'Fiji',
            'FLK'=>'Falkland Islands (Malvinas)',
            'FRA'=>'France',
            'FRO'=>'Faroe Islands',
            'FSM'=>'Micronesia, Federated States of',
            'GAB'=>'Gabon',
            'GBR'=>'United Kingdom',
            'GEO'=>'Georgia',
            'GGY'=>'Guernsey',
            'GHA'=>'Ghana',
            'GIB'=>'Gibraltar',
            'GIN'=>'Guinea',
            'GLP'=>'Guadeloupe',
            'GMB'=>'Gambia',
            'GNB'=>'Guinea-Bissau',
            'GNQ'=>'Equatorial Guinea',
            'GRC'=>'Greece',
            'GRD'=>'Grenada',
            'GRL'=>'Greenland',
            'GTM'=>'Guatemala',
            'GUF'=>'French Guiana',
            'GUM'=>'Guam',
            'GUY'=>'Guyana',
            'HKG'=>'Hong Kong',
            'HMD'=>'Heard Island and McDonald Islands',
            'HND'=>'Honduras',
            'HRV'=>'Croatia',
            'HTI'=>'Haiti',
            'HUN'=>'Hungary',
            'IDN'=>'Indonesia',
            'IMN'=>'Isle of Man',
            'IND'=>'India',
            'IOT'=>'British Indian Ocean Territory',
            'IRL'=>'Ireland',
            'IRN'=>'Iran, Islamic Republic of',
            'IRQ'=>'Iraq',
            'ISL'=>'Iceland',
            'ISR'=>'Israel',
            'ITA'=>'Italy',
            'JAM'=>'Jamaica',
            'JEY'=>'Jersey',
            'JOR'=>'Jordan',
            'JPN'=>'Japan',
            'KAZ'=>'Kazakhstan',
            'KEN'=>'Kenya',
            'KGZ'=>'Kyrgyzstan',
            'KHM'=>'Cambodia',
            'KIR'=>'Kiribati',
            'KNA'=>'Saint Kitts and Nevis',
            'KOR'=>'Korea, Republic of',
            'KWT'=>'Kuwait',
            'LAO'=>'Lao People\'s Democratic Republic',
            'LBN'=>'Lebanon',
            'LBR'=>'Liberia',
            'LBY'=>'Libya',
            'LCA'=>'Saint Lucia',
            'LIE'=>'Liechtenstein',
            'LKA'=>'Sri Lanka',
            'LSO'=>'Lesotho',
            'LTU'=>'Lithuania',
            'LUX'=>'Luxembourg',
            'LVA'=>'Latvia',
            'MAC'=>'Macao',
            'MAF'=>'Saint Martin (French part)',
            'MAR'=>'Morocco',
            'MCO'=>'Monaco',
            'MDA'=>'Moldova, Republic of',
            'MDG'=>'Madagascar',
            'MDV'=>'Maldives',
            'MEX'=>'Mexico',
            'MHL'=>'Marshall Islands',
            'MKD'=>'Macedonia, the former Yugoslav Republic of',
            'MLI'=>'Mali',
            'MLT'=>'Malta',
            'MMR'=>'Myanmar',
            'MNE'=>'Montenegro',
            'MNG'=>'Mongolia',
            'MNP'=>'Northern Mariana Islands',
            'MOZ'=>'Mozambique',
            'MRT'=>'Mauritania',
            'MSR'=>'Montserrat',
            'MTQ'=>'Martinique',
            'MUS'=>'Mauritius',
            'MWI'=>'Malawi',
            'MYS'=>'Malaysia',
            'MYT'=>'Mayotte',
            'NAM'=>'Namibia',
            'NCL'=>'New Caledonia',
            'NER'=>'Niger',
            'NFK'=>'Norfolk Island',
            'NGA'=>'Nigeria',
            'NIC'=>'Nicaragua',
            'NIU'=>'Niue',
            'NLD'=>'Netherlands',
            'NOR'=>'Norway',
            'NPL'=>'Nepal',
            'NRU'=>'Nauru',
            'NZL'=>'New Zealand',
            'OMN'=>'Oman',
            'PAK'=>'Pakistan',
            'PAN'=>'Panama',
            'PCN'=>'Pitcairn',
            'PER'=>'Peru',
            'PHL'=>'Philippines',
            'PLW'=>'Palau',
            'PNG'=>'Papua New Guinea',
            'POL'=>'Poland',
            'PRI'=>'Puerto Rico',
            'PRK'=>'Korea, Democratic People\'s Republic of',
            'PRT'=>'Portugal',
            'PRY'=>'Paraguay',
            'PSE'=>'Palestinian Territory, Occupied',
            'PYF'=>'French Polynesia',
            'QAT'=>'Qatar',
            'REU'=>'Réunion',
            'ROU'=>'Romania',
            'RUS'=>'Russian Federation',
            'RWA'=>'Rwanda',
            'SAU'=>'Saudi Arabia',
            'SDN'=>'Sudan',
            'SEN'=>'Senegal',
            'SGP'=>'Singapore',
            'SGS'=>'South Georgia and the South Sandwich Islands',
            'SHN'=>'Saint Helena, Ascension and Tristan da Cunha',
            'SJM'=>'Svalbard and Jan Mayen',
            'SLB'=>'Solomon Islands',
            'SLE'=>'Sierra Leone',
            'SLV'=>'El Salvador',
            'SMR'=>'San Marino',
            'SOM'=>'Somalia',
            'SPM'=>'Saint Pierre and Miquelon',
            'SRB'=>'Serbia',
            'SSD'=>'South Sudan',
            'STP'=>'Sao Tome and Principe',
            'SUR'=>'Suriname',
            'SVK'=>'Slovakia',
            'SVN'=>'Slovenia',
            'SWE'=>'Sweden',
            'SWZ'=>'Swaziland',
            'SXM'=>'Sint Maarten (Dutch part)',
            'SYC'=>'Seychelles',
            'SYR'=>'Syrian Arab Republic',
            'TCA'=>'Turks and Caicos Islands',
            'TCD'=>'Chad',
            'TGO'=>'Togo',
            'THA'=>'Thailand',
            'TJK'=>'Tajikistan',
            'TKL'=>'Tokelau',
            'TKM'=>'Turkmenistan',
            'TLS'=>'Timor-Leste',
            'TON'=>'Tonga',
            'TTO'=>'Trinidad and Tobago',
            'TUN'=>'Tunisia',
            'TUR'=>'Turkey',
            'TUV'=>'Tuvalu',
            'TWN'=>'Taiwan, Province of China',
            'TZA'=>'Tanzania, United Republic of',
            'UGA'=>'Uganda',
            'UKR'=>'Ukraine',
            'UMI'=>'United States Minor Outlying Islands',
            'URY'=>'Uruguay',
            'USA'=>'United States',
            'UZB'=>'Uzbekistan',
            'VAT'=>'Holy See (Vatican City State)',
            'VCT'=>'Saint Vincent and the Grenadines',
            'VEN'=>'Venezuela, Bolivarian Republic of',
            'VGB'=>'Virgin Islands, British',
            'VIR'=>'Virgin Islands, U.S.',
            'VNM'=>'Viet Nam',
            'VUT'=>'Vanuatu',
            'WLF'=>'Wallis and Futuna',
            'WSM'=>'Samoa',
            'YEM'=>'Yemen',
            'ZAF'=>'South Africa',
            'ZMB'=>'Zambia',
            'ZWE'=>'Zimbabwe'
        );

        if( isset($iso_array[$alpha3]) ) {
            return $iso_array[$alpha3];
        }

        return NULL;
    }

//    //$yearOffset: 0=>current year, -1=>previous year, +1=>next year
//    //return format: Y-m-d
//    public function getAcademicYearStartEndDates_ORIG( $currentYear=null, $asDateTimeObject=false, $yearOffset=null, $sitename=null ) {
//        $userSecUtil = $this->container->get('user_security_utility');
//        //academicYearStart: July 01
//        $academicYearStart = $userSecUtil->getSiteSettingParameter('academicYearStart',$sitename);
//        if( !$academicYearStart ) {
//            throw new \InvalidArgumentException('academicYearStart is not defined in Site Parameters.');
//            //$startDate = NULL;
//        }
//        //academicYearEnd: June 30
//        $academicYearEnd = $userSecUtil->getSiteSettingParameter('academicYearEnd',$sitename);
//        if( !$academicYearEnd ) {
//            throw new \InvalidArgumentException('academicYearEnd is not defined in Site Parameters.');
//            //$endDate = NULL;
//        }
//
//        $startDateMD = $academicYearStart->format('m-d');
//        $endDateMD = $academicYearEnd->format('m-d');
//
//        if( !$currentYear ) {
//            $currentYear = $this->getDefaultAcademicStartYear();
//        }
//
//        $nextYear = $currentYear + 1;
//
//        if( $yearOffset ) {
//            $currentYear = $currentYear + $yearOffset;
//            $nextYear = $nextYear + $yearOffset;
//        }
//
//        $startDate = $currentYear."-".$startDateMD;
//        $endDate = $nextYear."-".$endDateMD;
//        //exit('<br> exit: startDate='.$startDate.'; endDate='.$endDate); //testing
//
//        if( $asDateTimeObject ) {
//            $startDate = \DateTime::createFromFormat('Y-m-d', $startDate);
//            $endDate = \DateTime::createFromFormat('Y-m-d', $endDate);
//        }
//
//        return array(
//            //'currentYear' => $currentYear,
//            'startDate'=> $startDate,
//            'endDate'=> $endDate,
//        );
//    }
    //$yearOffset: 0=>current year, -1=>previous year, +1=>next year
    //return format: Y-m-d
    public function getAcademicYearStartEndDates(
        $currentYear=null,
        $asDateTimeObject=false,
        $yearOffset=null,
        $sitename=null,
        $startfieldname='academicYearStart',
        $endfieldname='academicYearEnd'
    ) {
        $userSecUtil = $this->container->get('user_security_utility');

        if( !$currentYear ) {
            $currentYear = $this->getDefaultAcademicStartYear();
        }
        $nextYear = $currentYear + 1;

        //academicYearStart: July 01
        $academicYearStart = $userSecUtil->getSiteSettingParameter($startfieldname,$sitename);
        if( $academicYearStart ) {
            $startDateMD = $academicYearStart->format('m-d');

            if( $yearOffset ) {
                if( $currentYear ) {
                    $currentYear = $currentYear + $yearOffset;
                }
            }

            $startDate = $currentYear."-".$startDateMD;

            if( $asDateTimeObject ) {
                $startDate = \DateTime::createFromFormat('Y-m-d', $startDate);
            }
        } else {
            $startDate = NULL;
        }

        //academicYearEnd: June 30
        $academicYearEnd = $userSecUtil->getSiteSettingParameter($endfieldname,$sitename);
        if( $academicYearEnd ) {
            $endDateMD = $academicYearEnd->format('m-d');

            if( $yearOffset ) {
                if( $nextYear ) {
                    $nextYear = $nextYear + $yearOffset;
                }
            }

            $endDate = $nextYear."-".$endDateMD;

            if( $asDateTimeObject ) {
                $endDate = \DateTime::createFromFormat('Y-m-d', $endDate);
            }
        } else {
            $endDate = NULL;
        }

        //exit('<br> exit: startDate='.$startDate.'; endDate='.$endDate); //testing

        return array(
            //'currentYear' => $currentYear,
            'startDate'=> $startDate,
            'endDate'=> $endDate,
        );
    }
    //Get default academic year (if 2021 it means 2021-2022 academic year) according to the academicYearStart in the site settings
    public function getDefaultAcademicStartYear( $sitename=null, $startfieldname='academicYearStart' ) {

        $userSecUtil = $this->container->get('user_security_utility');
        $academicYearStart = $userSecUtil->getSiteSettingParameter($startfieldname,$sitename);
        $currentYear = $this->getAcademicStartYear( $academicYearStart );
        return $currentYear;

        //Moved to getAcademicStartYear
        $currentYear = intval(date("Y"));
        $currentDate = new \DateTime();

        //2011-03-26 (year-month-day)
        $january1 = new \DateTime($currentYear."-01-01");
        //$june30 = new \DateTime($currentYear."-06-30");

        //start date of the academic year
        //$july1 = new \DateTime($currentYear."-07-01"); //get from site setting

        //start date
        $academicYearStart = $userSecUtil->getSiteSettingParameter($startfieldname,$sitename);
        if( $academicYearStart ) {
            $startDateMD = $academicYearStart->format('m-d');
            $july1 = new \DateTime($currentYear."-".$startDateMD);
        } else {
            //throw new \InvalidArgumentException('academicYearStart is not defined in Site Parameters.');
            //assume start date July 1st
            $july1 = new \DateTime($currentYear."-07-01");
        }
        //echo "july1=".$july1->format("d-m-Y")."<br>";

        //end date of the year, always December 31
        $december31 = new \DateTime($currentYear."-12-31");

        //Application Season Start Year (applicationSeasonStartDates) set to:
        //current year if current date is between July 1st and December 31st (inclusive) or
        //previous year (current year-1) if current date is between January 1st and June 30th (inclusive)
        // 1January---(current year-1)---1July---(current year)---31December---

        //Residency Start Year (startDates)
        //next year (current year+1) if current date is between July 1st and December 31st (inclusive) or
        //current year if current date is between January 1st and June 30th (inclusive)
        // 1July---(current year+1)---31December---(current year)---30June---

        //set "Application Season Start Year" to current year and "Residency Start Year" to next year if
        // current date is between July 1st and December 31st (inclusive) or
        if( $currentDate >= $july1 && $currentDate <= $december31 ) {
            //$applicationSeasonStartDate = $currentYear;
            //$startDate = $currentYear + 1;
        }

        //set "Application Season Start Year" to previous year and and "Residency Start Year" to current year if
        // current date is between January 1st and June 30th (inclusive)
        if( $currentDate >= $january1 && $currentDate < $july1 ) {
            $currentYear = $currentYear - 1;
            //$startDate = $currentYear;
        }

        //echo "currentYear=$currentYear <br>";

        return $currentYear;
    }
    //Get academic year (if 2021 it means 2021-2022 academic year) according to the $stardate and end of year (december 31st)
    public function getAcademicStartYear( $stardate ) {

        //$userSecUtil = $this->container->get('user_security_utility');

        $currentYear = intval(date("Y"));
        $currentDate = new \DateTime();

        //2011-03-26 (year-month-day)
        $january1 = new \DateTime($currentYear."-01-01");
        //$june30 = new \DateTime($currentYear."-06-30");

        //start date of the academic year
        //$july1 = new \DateTime($currentYear."-07-01"); //get from site setting

        //start date
        //$academicYearStart = $userSecUtil->getSiteSettingParameter($startfieldname,$sitename);
        if( $stardate ) {
            $startDateMD = $stardate->format('m-d');
            $july1 = new \DateTime($currentYear."-".$startDateMD);
        } else {
            //throw new \InvalidArgumentException('academicYearStart is not defined in Site Parameters.');
            //assume start date July 1st
            $july1 = new \DateTime($currentYear."-07-01");
        }
        //echo "july1=".$july1->format("d-m-Y")."<br>";

        //end date of the year, always December 31
        $december31 = new \DateTime($currentYear."-12-31");

        //Application Season Start Year (applicationSeasonStartDates) set to:
        //current year if current date is between July 1st and December 31st (inclusive) or
        //previous year (current year-1) if current date is between January 1st and June 30th (inclusive)
        // 1January---(current year-1)---1July---(current year)---31December---

        //Residency Start Year (startDates)
        //next year (current year+1) if current date is between July 1st and December 31st (inclusive) or
        //current year if current date is between January 1st and June 30th (inclusive)
        // 1July---(current year+1)---31December---(current year)---30June---

        //set "Application Season Start Year" to current year and "Residency Start Year" to next year if
        // current date is between July 1st and December 31st (inclusive) or
        if( $currentDate >= $july1 && $currentDate <= $december31 ) {
            //$applicationSeasonStartDate = $currentYear;
            //$startDate = $currentYear + 1;
        }

        //set "Application Season Start Year" to previous year and and "Residency Start Year" to current year if
        // current date is between January 1st and June 30th (inclusive)
        if( $currentDate >= $january1 && $currentDate < $july1 ) {
            $currentYear = $currentYear - 1;
            //$startDate = $currentYear;
        }

        //echo "currentYear=$currentYear <br>";

        return $currentYear;
    }

    public function getAcademicStartEndDayMonth( $formatStr="m-d" )
    {
        $userSecUtil = $this->container->get('user_security_utility');
        //academicYearStart: July 01
        $academicYearStart = $userSecUtil->getSiteSettingParameter('academicYearStart');
        if (!$academicYearStart) {
            throw new \InvalidArgumentException('academicYearStart is not defined in Site Parameters.');
        }
        //academicYearEnd: June 30
        $academicYearEnd = $userSecUtil->getSiteSettingParameter('academicYearEnd');
        if (!$academicYearEnd) {
            throw new \InvalidArgumentException('academicYearEnd is not defined in Site Parameters.');
        }

        $startDayMonth = $academicYearStart->format($formatStr);
        $endDayMonth = $academicYearEnd->format($formatStr);

        return array(
            'startDayMonth'=> $startDayMonth,
            'endDayMonth'=> $endDayMonth,
        );
    }
    
    public function getLinkToListIdByClassName($listName) {
        $listEntity = $this->em->getRepository('AppUserdirectoryBundle:PlatformListManagerRootList')->findOneByListName($listName);
        if( !$listEntity ) {
            return NULL;
        }

        $linkToListId = $listEntity->getLinkToListId();

        if( !$linkToListId ) {
            return NULL;
        }

        return $linkToListId;

        //platformlistmanager_edit
        $url = $this->container->get('router')->generate(
            'platformlistmanager_edit',
            array(
                'id' => $linkToListId,
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $url;
    }




    /////////////// NOT USED ///////////////////
    //NOT USED
    //MSSQL error: [Microsoft][ODBC Driver 11 for SQL Server][SQL Server]'LEVENSHTEIN' is not a recognized built-in function name
    //try: http://stackoverflow.com/questions/41218952/is-not-a-recognized-built-in-function-name
    public function getFuzzyLike( $field, $search, &$dql, &$queryParameters ) {
        if( !($field && $search) ) {
            return null;
        }

//        $dql->andWhere($field." LIKE :search");
//        $queryParameters['search'] = "%".$search."%";

        $tolerance = 4;
        $dql->andWhere("LEVENSHTEIN(lastname.field,:search) <= :tolerance");
        $queryParameters['search'] = "%".$search."%";
        $queryParameters['tolerance'] = $tolerance;
    }

    //TODO: or https://packagist.org/packages/glanchow/doctrine-fuzzy (cons: different DB requires different implementation of LEVENSHTEIN function)
    public function getFuzzyTest() {
        $em = $this->em;
        $tolerance = 4;
        //$dql->andWhere("LEVENSHTEIN(lastname.field,:search) <= :tolerance");
        //$queryParameters['search'] = "%".$search."%";
        //$queryParameters['tolerance'] = $tolerance;

        $search = "last";

        //1)
        $sql = "
          SELECT id, field
          FROM scan_patientlastname
          WHERE field LIKE '%".$search."%'
        ";
        echo "sql=$sql<br>";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        foreach( $results as $result ) {
            echo "res=".$result['id'].": ".$result['field']."<br>";
        }

        if(1) {
            $repository = $em->getRepository('AppOrderformBundle:PatientLastName');
            $dql = $repository->createQueryBuilder("list");
            $dql->select("list.id as id, LEVENSHTEIN(list.field, '".$search."') AS d");
            $dql->orderBy("d","ASC");
            $query = $em->createQuery($dql);

            //$query = $em
            //->createQueryBuilder('list')
            //->select('id, LEVENSHTEIN(list.field, :q) AS d')
            //->from($this->_entityName, 'g')
            //->orderby('d', 'ASC')
            //->setFirstResult($offset)
            //->setMaxResults($limit)
            //->setParameter('q', $search)
            //->getQuery();

            $results = $query->getResult();

            echo "<br>";
            foreach( $results as $result ) {
                echo "res=".$result['id'].": ".$result['d']."<br>";
            }

//            $repository = $this->em->getRepository('AppUserdirectoryBundle:PermissionObjectList');
//            $dql =  $repository->createQueryBuilder("list");
//            $dql->select('list');
//            $dql->leftJoin('list.sites','sites');
//            $dql->where("(list.name = :objectname OR list.abbreviation = :objectname) AND (sites.name = :sitename OR sites.abbreviation = :sitename)");
//            $query = $this->em->createQuery($dql);

            //return $query->getResult();
        }

        //2)
        if(0){
            $sql = "SELECT id, field FROM scan_patientlastname WHERE ( LEVENSHTEIN(field,'".$search."') <= 4 )";
            echo "sql=$sql<br>";

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();

            foreach( $results as $result ) {
                echo "res=".$result['id'].": ".$result['field']."<br>";
            }
        }
        return $results;
    }
    //Assistance => ASSTN
    //Assistants => ASSTN
    //Therefore: DB must have ASSTN in order to find Assistance
    public function getMetaphoneStrArr( $word, $primary=true ) {
        $outputArr = array();
        $outputArr[] = $word;

        $userSecUtil = $this->container->get('user_security_utility');
        $enableMetaphone = $userSecUtil->getSiteSettingParameter('enableMetaphone');
        $pathMetaphone = $userSecUtil->getSiteSettingParameter('pathMetaphone');

        if( !($enableMetaphone && $pathMetaphone) ) {
            return $outputArr;
        }

        //C:\Users\ch3\Documents\MyDocs\WCMC\ORDER\scanorder\Scanorders2\vendor\olegutil\Metaphone3\metaphone3.php
        //require_once('"'.$pathMetaphone.'"');
        //$pathMetaphone = "'".$pathMetaphone."'";
        require_once($pathMetaphone);

        $m3 = new \Metaphone3();

        $m3->SetEncodeVowels(TRUE);
        $m3->SetEncodeExact(TRUE);

        //test_word($m3, 'iron', 'ARN', '');
        $m3->SetWord($word);
        //Encodes input string to one or two key values according to Metaphone 3 rules.
        $m3->Encode();

        if( $primary ) {
            return $m3->m_primary;
        }

        $outputArr[] = $m3->m_primary;
        $outputArr[] = $m3->m_secondary;
        return $outputArr;
    }
    /////////////// EOF NOT USED ///////////////////

}