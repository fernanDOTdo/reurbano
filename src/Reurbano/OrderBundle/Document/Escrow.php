<?php
/**
 *                                              ,d                              
 *                                              88                              
 * 88,dPYba,,adPYba,   ,adPPYYba,  ,adPPYba,  MM88MMM  ,adPPYba,   8b,dPPYba,   
 * 88P'   "88"    "8a  ""     `Y8  I8[    ""    88    a8"     "8a  88P'    "8a  
 * 88      88      88  ,adPPPPP88   `"Y8ba,     88    8b       d8  88       d8  
 * 88      88      88  88,    ,88  aa    ]8I    88,   "8a,   ,a8"  88b,   ,a8"  
 * 88      88      88  `"8bbdP"Y8  `"YbbdP"'    "Y888  `"YbbdP"'   88`YbbdP"'   
 *                                                                 88           
 *                                                                 88           
 * 
 * Reurbano/OrderBundle/Document/Escrow.php
 *
 * Document para extrato de transações do vendedor.
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


namespace Reurbano\OrderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(
 *   collection="escrow",
 *   repositoryClass="Reurbano\OrderBundle\Document\EscrowRepository"
 * )
 * @ODM\Indexes({
 *   @ODM\Index(keys={"user.$id"="desc", "moneyIn"="desc", "approved"="desc"})
 * })
 */
class Escrow
{
    /**
     * Mongo id
     * 
     * @var MongoId
     * @ODM\Id
     */
    protected $id;

    /**
     * Usuário
     *
     * @var object
     * @ODM\ReferenceOne(targetDocument="Reurbano\UserBundle\Document\User")
     */
    protected $user;

    /**
     * Objeto em que o extrato está relacionado
     *
     * @var object
     * @ODM\ReferenceOne(
     *  discriminatorMap={
     *     "order"="Reurbano\OrderBundle\Document\Order",
     *     "refund"="Reurbano\OrderBundle\Document\Refund",
     *     "checkout"="Reurbano\OrderBundle\Document\Checkout"
     *   },
     *   discriminatorField="type"
     * )
     */
    protected $data;
    
    /**
     * MoneyIn = Verdadeiro se o valor está entrando e falso se está saindo.
     * 
     * @var bool
     * @ODM\Boolean
     */
    protected $moneyIn = true;
    
    /**
     * Approved = Verdadeiro se está liberada e falso se está pendente.
     * 
     * @var bool
     * @ODM\Boolean
     */
    protected $approved = false;

    /**
     * Valor monetário
     * 
     * @var float
     * @ODM\Float
     */
    protected $value;
    
    /**
     * Data de Criação
     *
     * @var object
     * @ODM\Date
     */
    protected $created;
    
    /**
     * Data de Atualização
     *
     * @var object
     * @ODM\Date
     */
    protected $updated;
    
    /**
     * Observação
     * 
     * @var string
     * @ODM\String
     */
    protected $obs;

    /** 
     * @ODM\PrePersist 
     */
    public function doPrePersist()
    {
        $this->setCreated(new \DateTime);
    }
    /** 
     * @ODM\PreUpdate
     */
    public function doPreUpdate()
    {
        $this->setUpdated(new \DateTime);
    }

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param Reurbano\UserBundle\Document\User $user
     */
    public function setUser(\Reurbano\UserBundle\Document\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Reurbano\UserBundle\Document\User $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set data
     *
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get data
     *
     * @return $data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set moneyIn
     *
     * @param boolean $moneyIn
     */
    public function setMoneyIn($moneyIn)
    {
        $this->moneyIn = $moneyIn;
    }

    /**
     * Get moneyIn
     *
     * @return boolean $moneyIn
     */
    public function getMoneyIn()
    {
        return $this->moneyIn;
    }

    /**
     * Set approved
     *
     * @param boolean $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * Get approved
     *
     * @return boolean $approved
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * Set value
     *
     * @param float $value
     */
    public function setValue($value)
    {
        $this->value = number_format($value, 2, '.', '');
    }

    /**
     * Get value
     *
     * @return float $value
     */
    public function getValue()
    {
        return number_format($this->value, 2, '.', '');
    }
    
    /**
     * Set created
     *
     * @param date $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return date $created
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param date $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Get updated
     *
     * @return date $updated
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set obs
     *
     * @param string $obs
     */
    public function setObs($obs)
    {
        $this->obs = $obs;
    }

    /**
     * Get obs
     *
     * @return string $obs
     */
    public function getObs()
    {
        return $this->obs;
    }
}