<?php

namespace Oleg\OrderformBundle\Helper;

class FormHelper {
    
    public function getStains() {
        $arr = array(
            'H&E','2-Oct','4-Oct','A103 (Melan-A)'
        );
        
        return $arr;
    }
    
    public function getMags() {        
        $arr = array( '20X', '40X' );
        
        return $arr;
    }
    
}
?>
