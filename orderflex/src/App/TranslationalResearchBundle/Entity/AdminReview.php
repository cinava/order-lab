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

namespace App\TranslationalResearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: 'transres_adminReview')]
#[ORM\Entity]
class AdminReview extends ReviewBase
{

    #[ORM\ManyToOne(targetEntity: 'Project', inversedBy: 'adminReviews')]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: true)]
    private $project;

    /**
     * Separate Admin Review / Admin Review Delegate roles to “Admin Review for Funded Projects” and “Admin Review for Non-Funded Projects” for each specialty/project type
     * Project Type 'string' - 'Funded', 'Non-Funded', 'All' ...
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $reviewProjectType;


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
    public function getReviewProjectType()
    {
        return $this->reviewProjectType;
    }

    /**
     * @param mixed $reviewProjectType
     */
    public function setReviewProjectType($reviewProjectType)
    {
        $this->reviewProjectType = $reviewProjectType;
    }

    

    public function getStateStr() {
        return "admin_review";
    }
    
}