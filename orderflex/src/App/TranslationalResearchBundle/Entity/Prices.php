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
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 1/5/16
 * Time: 5:00 PM
 * To change this template use File | Settings | File Templates.
 */

namespace App\TranslationalResearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="transres_prices")
 */
class Prices
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="RequestCategoryTypeList", inversedBy="prices")
     * @ORM\JoinColumn(name="requestCategoryType_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $requestCategoryType;


    /**
     * Price of Product or Service
     * External fee - "Fee for one"
     *
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $fee;

    /**
     * Price of Product or Service
     * External fee - "Fee per additional item"
     *
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $feeAdditionalItem;

    
    /**
     * Utilize the following price list
     *
     * @ORM\ManyToOne(targetEntity="PriceTypeList")
     */
    private $priceList;


//    /**
//     * Indicates the order in the list
//     * @ORM\Column(type="integer", nullable=true)
//     */
//    private $orderinlist;


    public function __construct() {
        
    }



    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getRequestCategoryType()
    {
        return $this->requestCategoryType;
    }

    /**
     * @param mixed $requestCategoryType
     */
    public function setRequestCategoryType($requestCategoryType)
    {
        $this->requestCategoryType = $requestCategoryType;
    }

    /**
     * @return string
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * @param string $fee
     */
    public function setFee($fee)
    {
        $this->fee = $fee;
    }

    /**
     * @return string
     */
    public function getFeeAdditionalItem()
    {
        if( !$this->feeAdditionalItem && $this->fee ) {
            return $this->fee;
        }
        return $this->feeAdditionalItem;
    }

    /**
     * @param string $feeAdditionalItem
     */
    public function setFeeAdditionalItem($feeAdditionalItem)
    {
        $this->feeAdditionalItem = $feeAdditionalItem;
    }

    /**
     * @return mixed
     */
    public function getPriceList()
    {
        return $this->priceList;
    }

    /**
     * @param mixed $priceList
     */
    public function setPriceList($priceList)
    {
        $this->priceList = $priceList;
    }

    public function getPriceInfo()
    {
        return $this->getPriceList().": ".$this->getFee()." (".$this->getFeeAdditionalItem().")";
    }


    public function __toString() {
        $res = "Price ID " . $this->getId();

        $res = $res . ", priceList=".$this->getPriceList().", fee=".$this->getFee().", feeAdditionalItem=".$this->getFeeAdditionalItem();
        
        return $res;
    }
}