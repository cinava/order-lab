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

namespace App\OrderformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use App\UserdirectoryBundle\Entity\ListAbstract;

#[ORM\Table(name: 'scan_account')]
#[ORM\Entity]
class Account extends ListAbstract
{
    #[ORM\OneToMany(targetEntity: 'Account', mappedBy: 'original')]
    protected $synonyms;

    #[ORM\ManyToOne(targetEntity: 'Account', inversedBy: 'synonyms')]
    #[ORM\JoinColumn(name: 'original_id', referencedColumnName: 'id')]
    protected $original;

    #[ORM\OneToMany(targetEntity: 'Message', mappedBy: 'account')]
    protected $message;


    public function __construct() {
        $this->synonyms = new ArrayCollection();
        $this->message = new ArrayCollection();
    }


    /**
     * Add message
     *
     * @param \App\OrderformBundle\Entity\Message $message
     * @return Account
     */
    public function addMessage(\App\OrderformBundle\Entity\Message $message)
    {
        if( !$this->message->contains($message) ) {
            $this->message->add($message);
        }
    }

    /**
     * Remove message
     *
     * @param \App\OrderformBundle\Entity\Message $message
     */
    public function removeMessage(\App\OrderformBundle\Entity\Message $message)
    {
        $this->message->removeElement($message);
    }

    /**
     * Get message
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessage()
    {
        return $this->message;
    }
}