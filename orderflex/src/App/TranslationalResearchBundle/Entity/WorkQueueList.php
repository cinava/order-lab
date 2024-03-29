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

namespace App\TranslationalResearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use App\UserdirectoryBundle\Entity\ListAbstract;

#[ORM\Table(name: 'transres_workqueuelist')]
#[ORM\Entity]
class WorkQueueList extends ListAbstract
{

    #[ORM\OneToMany(targetEntity: 'WorkQueueList', mappedBy: 'original', cascade: ['persist'])]
    protected $synonyms;

    #[ORM\ManyToOne(targetEntity: 'WorkQueueList', inversedBy: 'synonyms', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'original_id', referencedColumnName: 'id', nullable: true)]
    protected $original;

    //Use abbreviation for Role postfix:
    // "CTP Lab" => "QUEUECTP"
    // "MISI Lab" => "QUEUEMISI"

//    /**
//     * @ORM\Column(type="string", nullable=true)
//     */
//    protected $rolename;


//    /**
//     * @return mixed
//     */
//    public function getRolename()
//    {
//        return $this->rolename;
//    }
//
//    /**
//     * @param mixed $rolename
//     */
//    public function setRolename($rolename)
//    {
//        $this->rolename = $rolename;
//    }

}
