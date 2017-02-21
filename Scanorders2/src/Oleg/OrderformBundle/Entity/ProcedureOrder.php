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

namespace Oleg\OrderformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Oleg\UserdirectoryBundle\Entity\DocumentContainer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="scan_procedureOrder")
 */
class ProcedureOrder extends OrderBase {

    /**
     * @ORM\OneToOne(targetEntity="Message", mappedBy="procedureorder")
     **/
    protected $message;

    /**
     * @ORM\ManyToOne(targetEntity="ProcedureList", cascade={"persist"})
     * @ORM\JoinColumn(name="procedurelist_id", referencedColumnName="id", nullable=true)
     */
    protected $type;




    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }




    public function __toString() {
        $res = "Procedure Order";
        if( $this->getId() ) {
            $res = $res . " with ID=" . $this->getId() . ", type=" . $this->getType();
        }
        return $res;
    }

}