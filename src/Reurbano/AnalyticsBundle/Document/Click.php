<?php

namespace Reurbano\AnalyticsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ODM\EmbeddedDocument
 */
class Click {
	
	/**
	 * Dados do usuário, exemplo: Ip. Origem, OS, etc
	 *
	 * @var array
	 * @ODM\EmbedOne(targetDocument="Reurbano\AnalyticsBundle\Document\UserData")
	 */
	protected $userData;
	
	/**
	 * Publicação
	 * 
	 * @var object
     * @ODM\ReferenceOne(targetDocument="Reurbano\AnalyticsBundle\Document\Publication")
     */
	protected $publication;
		
	/**
	 * Cliques no site
	 *
	 * @var int
	 * @ODM\Int
	 */
	protected $click = 1;
	
	/**
	 * Data do útimo click
	 *
	 * @var date
	 * @ODM\Date
	 */
	protected $updatedAt;
	
	/**
	 * Data de criação do registro de click
	 *
	 * @var date
	 * @ODM\Date
	 */
	protected $createdAt;
	
	public function __construct()
	{
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
     * Set userData
     *
     * @param Reurbano\AnalyticsBundle\Document\UserData $userData
     */
    public function setUserData(\Reurbano\AnalyticsBundle\Document\UserData $userData)
    {
    	$this->userData = $userData;
    }
    
    /**
     * Get userData
     *
     * @return Reurbano\AnalyticsBundle\Document\UserData $userData
     */
    public function getUserData()
    {
    	return $this->userData;
    }

    /**
     * Set publication
     *
     * @param Reurbano\AnalyticsBundle\Document\Publication $publication
     */
    public function setPublication(\Reurbano\AnalyticsBundle\Document\Publication $publication)
    {
    	$this->publication = $publication;
    }

    /**
     * Get publication
     *
     * @return Reurbano\AnalyticsBundle\Document\Publication $publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Set click
     *
     * @param int $click
     */
    public function setClick($click)
    {
        $this->click = $click;
    }

    /**
     * Get click
     *
     * @return int $click
     */
    public function getClick()
    {
        return $this->click;
    }
    
    /**
     * Set updatedAt
     *
     * @param date $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
    	$this->updatedAt = $updatedAt;
    }
    
    /**
     * Get updatedAt
     *
     * @return date $updatedAt
     */
    public function getUpdatedAt()
    {
    	return $this->updatedAt;
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
    
    /** @ODM\PrePersist */
    public function doPrePersist()
    {
    	// Seta data de criação
    	$this->setCreatedAt(new \DateTime);
    	$this->setUpdatedAt(new \DateTime);
    }
    
    /** @ODM\PreUpdate */
    public function doPreUpdate()
    {
    	// Seta data de atualização
    	$this->setUpdatedAt(new \DateTime);
    }
    
    /**
     * incClick
     *
     * @return int
     */
    public function incClick()
    {
    	return $this->getClick()+1;
    }
}
