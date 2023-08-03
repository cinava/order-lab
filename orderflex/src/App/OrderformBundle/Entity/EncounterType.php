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

#[ORM\Table(name: 'scan_encounterType')]
#[ORM\Entity]
class EncounterType extends ListAbstract
{

    #[ORM\OneToMany(targetEntity: 'EncounterType', mappedBy: 'original', cascade: ['persist'])]
    protected $synonyms;

    #[ORM\ManyToOne(targetEntity: 'EncounterType', inversedBy: 'synonyms', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'original_id', referencedColumnName: 'id', nullable: true)]
    protected $original;

    #[ORM\OneToMany(targetEntity: 'EncounterNumber', mappedBy: 'keytype')]
    protected $encounternumber;


    public function __construct() {
        $this->synonyms = new ArrayCollection();
        $this->encounternumber = new ArrayCollection();
    }



    public function addEncounternumber(\App\OrderformBundle\Entity\EncounterNumber $encounternumber)
    {
        if( !$this->encounternumber->contains($encounternumber) ) {
            $this->encounternumber->add($encounternumber);
            $encounternumber->setKeytype($this);
        }
        return $this;
    }

    public function removeEncounternumber(\App\OrderformBundle\Entity\EncounterNumber $encounternumber)
    {
        $this->encounternumber->removeElement($encounternumber);
    }

    public function getEncounternumber()
    {
        return $this->encounternumber;
    }


}