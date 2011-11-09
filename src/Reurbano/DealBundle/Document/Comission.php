<?php

/**
 * Reurbano/DealBundle/Document/Comission.php
 *
 * Dados para comissão da oferta
 *  
 * 
 * @copyright 2011 Mastop Internet Development.
 * @link http://www.mastop.com.br
 * @author Fernando Santos <o@fernan.do>
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Comission
{
    /**
     * Comissão a cobrar do vendedor em %
     *
     * @var int
     * @ODM\Int
     */
    protected $sellerpercent;
    
    /**
     * Comissão a cobrar do vendedor em R$
     *
     * @var float
     * @ODM\Float
     */
    protected $sellerreal;
    
    /**
     * Comissão a cobrar do comprador em %
     *
     * @var int
     * @ODM\Int
     */
    protected $buyerpercent;
    
    /**
     * Comissão a cobrar do comprador em R$
     *
     * @var float
     * @ODM\Float
     */
    protected $buyerreal;

    /**
     * Set sellerpercent
     *
     * @param int $sellerpercent
     */
    public function setSellerpercent($sellerpercent)
    {
        $this->sellerpercent = $sellerpercent;
    }

    /**
     * Get sellerpercent
     *
     * @return int $sellerpercent
     */
    public function getSellerpercent()
    {
        return $this->sellerpercent;
    }

    /**
     * Set sellerreal
     *
     * @param float $sellerreal
     */
    public function setSellerreal($sellerreal)
    {
        $this->sellerreal = $sellerreal;
    }

    /**
     * Get sellerreal
     *
     * @return float $sellerreal
     */
    public function getSellerreal()
    {
        return $this->sellerreal;
    }

    /**
     * Set buyerpercent
     *
     * @param int $buyerpercent
     */
    public function setBuyerpercent($buyerpercent)
    {
        $this->buyerpercent = $buyerpercent;
    }

    /**
     * Get buyerpercent
     *
     * @return int $buyerpercent
     */
    public function getBuyerpercent()
    {
        return $this->buyerpercent;
    }

    /**
     * Set buyerreal
     *
     * @param float $buyerreal
     */
    public function setBuyerreal($buyerreal)
    {
        $this->buyerreal = $buyerreal;
    }

    /**
     * Get buyerreal
     *
     * @return float $buyerreal
     */
    public function getBuyerreal()
    {
        return $this->buyerreal;
    }
}
