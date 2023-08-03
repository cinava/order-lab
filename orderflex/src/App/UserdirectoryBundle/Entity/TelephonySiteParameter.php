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

namespace App\UserdirectoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'user_telephonysiteparameter')]
#[ORM\Entity]
class TelephonySiteParameter
{

    /**
     * @var integer
     */
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;


    /**
     * TWILIO_AUTHY_API_KEY (TWILIO Auth Token)
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $twilioApiKey;

    /**
     * TWILIO Account Sid
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $twilioSid;

    /**
     * From phone number
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $fromPhoneNumber;

    /**
     * Phone number verification = enabled/disabled
     */
    #[ORM\Column(type: 'boolean', nullable: true)]
    private $phoneNumberVerification;


    public function __construct() {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTwilioApiKey()
    {
        return $this->twilioApiKey;
    }

    /**
     * @param mixed $twilioApiKey
     */
    public function setTwilioApiKey($twilioApiKey)
    {
        $this->twilioApiKey = $twilioApiKey;
    }

    

    /**
     * @return mixed
     */
    public function getPhoneNumberVerification()
    {
        return $this->phoneNumberVerification;
    }

    /**
     * @param mixed $phoneNumberVerification
     */
    public function setPhoneNumberVerification($phoneNumberVerification)
    {
        $this->phoneNumberVerification = $phoneNumberVerification;
    }

    /**
     * @return mixed
     */
    public function getTwilioSid()
    {
        return $this->twilioSid;
    }

    /**
     * @param mixed $twilioSid
     */
    public function setTwilioSid($twilioSid)
    {
        $this->twilioSid = $twilioSid;
    }

    /**
     * @return mixed
     */
    public function getFromPhoneNumber()
    {
        return $this->fromPhoneNumber;
    }

    /**
     * @param mixed $fromPhoneNumber
     */
    public function setFromPhoneNumber($fromPhoneNumber)
    {
        $this->fromPhoneNumber = $fromPhoneNumber;
    }
    





    public function __toString() {
        return "Telephony Site Parameter";
    }

}