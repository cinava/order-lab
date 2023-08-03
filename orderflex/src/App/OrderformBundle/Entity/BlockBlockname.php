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

#[ORM\Table(name: 'scan_blockBlockname')]
#[ORM\UniqueConstraint(name: 'block_unique', columns: ['block_id', 'field'])]
#[ORM\Entity]
class BlockBlockname extends BlockArrayFieldAbstract
{

    #[ORM\ManyToOne(targetEntity: 'Block', inversedBy: 'blockname', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'block_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: true)]
    protected $block;

    #[ORM\Column(type: 'string', nullable: true)]
    protected $field;


}