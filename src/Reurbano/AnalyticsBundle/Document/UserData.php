<?php

/**
 * Reurbano/AnalyticsBundle/Document/Coordinates.php
 *
 * Dados dos usuario
 *  
 * 
 * @copyright 2011 Reurbano.
 * @link http://www.reurbano.com.br
 * @author Saulo Lima <saulo@gubn.com.br>
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

namespace Reurbano\AnalyticsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class UserData
{	
	/**
	 * Usuário
	 *
	 * @var object
	 * @ODM\ReferenceOne(targetDocument="Reurbano\UserBundle\Document\User")
	 */
	protected $user;
	
    /**
     * ip do usuário
     *
	 * @var string
     * @ODM\String
     */
    protected $ip;
        
    /**
     * Data de criação
     *
     * @var date
     * @ODM\Date
     * @ODM\Index(order="desc")
     */
    protected $createdAt;

    /**
     * Set ip
     *
     * @param int $linha
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get ip
     *
     * @return int $ip
     */
    public function getIp()
    {
        return $this->ip;
    }
    
    /**
     * Set createdAt
     *
     * @param date $createdAt
     */
    public function setCreatedAt($createdAt)
    {
    	$this->createdAt = $createdAt;
    }
    
    /**
     * Get createdAt
     *
     * @return date $createdAt
     */
    public function getCreatedAt()
    {
    	return $this->createdAt;
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
    
    /** @ODM\PrePersist */
    public function doPrePersist()
    {
    	// Seta data de criação
    	$this->setCreatedAt(new \DateTime);
    }    
}
