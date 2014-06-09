<?php
namespace Oleg\OrderformBundle\Helper;

/**
 * Description of EmailUtil
 *
 * @author Cina
 */
class EmailUtil {
    
    public function sendEmail( $email, $adminemail, $entity, $orderurl, $text = null, $conflict=null, $submitStatusStr=null ) {

        if( !$email || $email == "" ) {
            return false;
        }

        ini_set( 'sendmail_from', $adminemail ); //My usual e-mail address
        ini_set( "SMTP", "smtp.med.cornell.edu" );  //My usual sender
        //ini_set( 'smtp_port', 25 );

        if( $text ) {
            $message = $text;
        } else {
            if( $submitStatusStr === null ) {
                $submitStatusStr = "has been received";
            }
            $slideCount = count($entity->getSlide());
            $thanks_txt =
                "Thank You For Your Order !\r\n\r\n"
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
        mail($email, 'Slide Scan Order #'.$entity->getId().' Confirmation', $message);

        return true;
    }
    
}

?>
