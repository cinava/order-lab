<?php
namespace Oleg\OrderformBundle\Helper;

/**
 * Description of EmailUtil
 *
 * @author Cina
 */
class EmailUtil {
    
    public function sendEmail( $email, $entity, $text = null ) {
        
        ini_set( 'sendmail_from', "slidescan@med.cornell.edu" ); //My usual e-mail address
        ini_set( "SMTP", "smtp.med.cornell.edu" );  //My usual sender
        //ini_set( 'smtp_port', 25 );

        $thanks_txt =
            "Thank You For Your Order !\r\n"
            . "Order #" . $entity->getId() . " Successfully Submitted.\r\n"
            . "Confirmation Email was sent to " . $email . "\r\n";

        if( $text ) {
            $message = $text;
        } else {
            $message = $thanks_txt;
        }

        // In case any of our lines are larger than 70 characters, we should use wordwrap()
        $message = wordwrap($message, 70, "\r\n");
        // Send
        mail($email, 'Scan Order Confirmation', $message);
        
    }
    
}

?>
