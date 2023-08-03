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

use Doctrine\ORM\Mapping as ORM;

//use disident (disease identify) as diagnosis, because diagnosis causes problem with symfony2&doctrine(?)
#[ORM\Table(name: 'scan_partDisident')]
#[ORM\Entity]
class PartDisident extends PartArrayFieldAbstract
{

    #[ORM\ManyToOne(targetEntity: 'Part', inversedBy: 'disident', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'part_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: true)]
    protected $part;

    #[ORM\Column(type: 'text', nullable: true)]
    protected $field;

}