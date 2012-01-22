<?php

/**
 * Reurbano/UserBundle/Document/BankData.php
 *
 * Informações Bancárias do Usuário
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

namespace Reurbano\UserBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class BankData {

    /**
     * Nome do Banco
     *
     * @var string
     * @ODM\String
     */
    protected $name;

    /**
     * Agência
     *
     * @var string
     * @ODM\String
     */
    protected $agency;

    /**
     * Conta
     *
     * @var string
     * @ODM\String
     */
    protected $account;

    /**
     * Tipo da conta (1 = Corrente, 2 = Poupança
     *
     * @var int
     * @ODM\Int
     */
    protected $type = 1;
    
    /**
     * CPF do Titular
     *
     * @var string
     * @ODM\String
     */
    protected $cpf;

    /**
     * Observações
     *
     * @var string
     * @ODM\String
     */
    protected $obs;
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getAgency() {
        return $this->agency;
    }

    public function setAgency($agency) {
        $this->agency = $agency;
    }

    public function getAccount() {
        return $this->account;
    }

    public function setAccount($account) {
        $this->account = $account;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getObs() {
        return $this->obs;
    }

    public function setObs($obs) {
        $this->obs = $obs;
    }
    public function getCpf() {
        return $this->cpf;
    }

    public function setCpf($cpf) {
        $this->cpf = $cpf;
    }
}