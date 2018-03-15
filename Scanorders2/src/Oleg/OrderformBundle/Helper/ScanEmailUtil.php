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

namespace Oleg\OrderformBundle\Helper;

use Oleg\UserdirectoryBundle\Util\EmailUtil;
use Oleg\UserdirectoryBundle\Util\UserUtil;

/**
 * Description of EmailUtil
 *
 * @author Cina
 */
class ScanEmailUtil extends EmailUtil {
    
    public function sendEmail( $email, $entity, $orderurl, $text = null, $conflict=null, $submitStatusStr=null ) {

        if( !$email || $email == "" ) {
            return false;
        }

        //get admin email
        $userSecUtil = $this->container->get('user_security_utility');
        $adminemail = $userSecUtil->getSiteSettingParameter('siteEmail');

        if( $text ) {
            $message = $text;
        } else {
            if( $submitStatusStr === null ) {
                $submitStatusStr = "has been received";
            }
            $slideCount = count($entity->getSlide());
            $thanks_txt =
                "Thank you for your order!\r\n\r\n"
                . "Your order #" . $entity->getId() . " to scan " . $slideCount . " slide(s) " . $submitStatusStr . ".\r\n"
                . "To check the current status of this order, to amend or cancel it, or to request the submitted glass slides back, visit: \r\n"
                . $orderurl . "\r\n\r\n"
                . "If you have any additional questions, please don't hesitate to email ".$adminemail." \r\n\r\n"
                . "Thank You! \r\n\r\n"
                . "Sincerely, \r\n"
                . "The WCMC Slide Scanning Service.";
                //. "Confirmation Email was sent to " . $email . "\r\n";
            $message = $thanks_txt;
        }

        if( $conflict ) {
            $message = $message."\r\n\r\n".$conflict;
        }

        // In case any of our lines are larger than 70 characters, we should use wordwrap()
        $message = wordwrap($message, 70, "\r\n");

        // Send
        //mail($email, 'Slide Scan Order #'.$entity->getId().' Confirmation', $message);
        parent::sendEmail($email, 'Slide Scan Order #'.$entity->getId().' Confirmation', $message);

        return true;
    }
    
}

?>
