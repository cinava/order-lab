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

/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 9/6/2017
 * Time: 4:43 PM
 */

namespace Oleg\TranslationalResearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="transres_irbReview")
 */
class IrbReview extends ReviewBase
{

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="irbReviews")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $project;


    //Claim/Assignment
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $assignment;


    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return mixed
     */
    public function getAssignment()
    {
        return $this->assignment;
    }

    /**
     * @param mixed $assignment
     */
    public function setAssignment($assignment)
    {
        $this->assignment = $assignment;
    }

    public function getStateStr() {
        return "irb_review";
    }

}