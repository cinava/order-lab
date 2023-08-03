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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Table(name: 'user_usernameType')]
#[ORM\Entity]
class UsernameType extends ListAbstract
{

    #[ORM\OneToMany(targetEntity: 'EventTypeList', mappedBy: 'original', cascade: ['persist'])]
    protected $synonyms;

    #[ORM\ManyToOne(targetEntity: 'EventTypeList', inversedBy: 'synonyms', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'original_id', referencedColumnName: 'id', nullable: true)]
    protected $original;

    #[ORM\OneToMany(targetEntity: 'User', mappedBy: 'keytype')]
    protected $users;



    public function __construct() {
        $this->synonyms = new ArrayCollection();
        $this->users = new ArrayCollection();
    }




    public function addUser(\App\UserdirectoryBundle\Entity\User $user)
    {
        if( !$this->users->contains($user) ) {
            $this->users->add($user);
        }
        return $this;
    }

    public function removeUser(\App\UserdirectoryBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    public function getUsers()
    {
        return $this->users;
    }
    
    public function setName($name)
    {
        $this->name = $name;

        $this->setEmptyAbbreviation();

        return $this;
    }

    public function setEmptyAbbreviation() {
        if( $this->getName() && !$this->getAbbreviation() ) {
            $abbreviation = $this->getName();
            $abbreviation = strtolower($abbreviation);
            $abbreviation = str_replace(" ","-",$abbreviation);
            $this->setAbbreviation($abbreviation);
        }
    }
}