<?php

namespace Reurbano\AnalyticsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Representa um click do usuario
 *
 * @author   Saulo Lima <saulo@gubn.com.br>
 *
 * @ODM\Document(
 *   collection="ad_doubleClick",
 *   repositoryClass="Reurbano\AnalyticsBundle\Document\DoubleClickRepository"
 * )
 */
class DoubleClick {
	
	/**
	 * ID da DoubleClick
	 *
	 * @var string
	 * @ODM\Id
	 */
	protected $id;
	
	/**
	 * Usuario dono dos clicks
	 *
	 * @ODM\ReferenceOne(targetDocument="Reurbano\UserBundle\Document\User")
	 */
	protected $user;

	/**
	 * Clicks
	 *
	 * @var array
	 * @ODM\EmbedMany(targetDocument="Reurbano\AnalyticsBundle\Document\Click")
	 */
	protected $click = array();
		
	/**
	 * Cliques no site
	 *
	 * @var int
	 * @ODM\Int
	 */
	protected $view = 1;
	
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
	 * @ODM\Index(order="desc")
	 */
	protected $createdAt;
	
	public function __construct()
	{
		$this->click = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add click
     *
     * @param Reurbano\AnalyticsBundle\Document\Click $click
     */
    public function addClick(\Reurbano\AnalyticsBundle\Document\Click $click)
    {
    	$this->click[] = $click;
    }

    /**
     * Get click
     *
     * @return Doctrine\Common\Collections\Collection $click
     */
    public function getClick()
    {
        return $this->click;
    }
    
    /**
     * Set view
     *
     * @param int $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * Get view
     *
     * @return int $view
     */
    public function getView()
    {
        return $this->view;
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
    	return $this->getView()+1;
    }
}
