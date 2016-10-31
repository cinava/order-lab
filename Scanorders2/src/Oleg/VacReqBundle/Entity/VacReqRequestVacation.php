<?php

/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 4/11/2016
 * Time: 11:35 AM
 */


namespace Oleg\VacReqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;


/**
 * @ORM\Entity
 * @ORM\Table(name="vacreq_vacation")
 */
class VacReqRequestVacation extends VacReqRequestBase
{


    public function __toString()
    {
        $break = "\r\n";
        $transformer = new DateTimeToStringTransformer(null,null,'m/d/Y');

        $res = "### Vacation Request ###".$break;
        $res .= "Status: ".$this->getStatus().$break;
        $res .= "Vacation - First Day Away: ".$transformer->transform($this->getStartDate()).$break;
        $res .= "Vacation - Last Day Away: ".$transformer->transform($this->getEndDate()).$break;
        $res .= "Vacation Days Requested: ".$this->getNumberOfDays().$break;
        //$res .= "First Day Back in Office: ".$transformer->transform($this->getFirstDayBackInOffice()).$break;

        if( $this->getApproverComment() ) {
            $res .= "Approver Comment: ".$this->getApproverComment().$break;
        }

        return $res;
    }

}