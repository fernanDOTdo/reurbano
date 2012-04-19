<?php

namespace Reurbano\AnalyticsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Representa um tracking de pre venda de usuário de sites externos
 *
 * @author Saulo Lima <saulo@gubn.com.br>
 *
 * @ODM\Document(
 *   collection="ad_tracking_presell",
 *   repositoryClass="Reurbano\AnalyticsBundle\Document\TrackingPreSellRepository"
 * )
 */

class TrackingPreSell
{
	/**
	 * ID do TrackingPreSell
	 *
	 * @var string
	 * @ODM\Id
	 */
	protected $id;
	
	/**
	 * Tracking
	 *
	 * @var object
	 * @ODM\ReferenceOne(targetDocument="Reurbano\AnalyticsBundle\Document\Tracking")
	 * @ODM\Index
	 */
	protected $tracking;
	
	/**
	 * Dados do usuário, exemplo: Ip. Origem, OS, etc
	 *
	 * @var array
	 * @ODM\EmbedMany(targetDocument="Reurbano\AnalyticsBundle\Document\UserData")
	 */
	protected $userData = array();
		
	/**
	 * Cliques da oferta do tracking
	 *
	 * @var int
	 * @ODM\Int
	 */
	protected $click = 0;
	
	/**
	 * Data da útima atualização do tracking
	 *
	 * @var date
	 * @ODM\Date
	 */
	protected $updatedAt;
	
	/**
	 * Data de criação do tracking
	 *
	 * @var date
	 * @ODM\Date
	 * @ODM\Index(order="desc")
	 */
	protected $createdAt;
    
    /**
     * Se a oferta que o usuário clicou para comprar estava no cookie do Tracking
     * Caso não esteja, significa que o parceiro gerou venda de outra oferta, não da que o usuário clicou
     *
     * @var string
     * @ODM\Boolean
     */
    protected $inCookie = false;
    
    public function __construct()
    {
    	$this->userData = new \Doctrine\Common\Collections\ArrayCollection();
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
    
    
    public function isInCookie()
    {
    	return ($this->getInCookie()) ? "Sim" : "Não";
    }
    
	/**
	 * Set tracking
	 *
	 * @param Reurbano\AnalyticsBundle\Document\Tracking $tracking
	 */
	public function setTracking(\Reurbano\AnalyticsBundle\Document\Tracking $tracking)
	{
		$this->tracking = $tracking;
	}
	
	/**
	 * Get tracking
	 *
	 * @return Reurbano\AnalyticsBundle\Document\Tracking $tracking
	 */
	public function getTracking()
	{
		return $this->tracking;
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
	
	/**
	 * Set inCookie
	 *
	 * @param boolean $inCookie
	 */
	public function setInCookie($inCookie)
	{
		$this->inCookie = $inCookie;
	}
	
	/**
	 * Get inCookie
	 *
	 * @return boolean $inCookie
	 */
	public function getInCookie()
	{
		return $this->inCookie;
	}
	
	/**
	 * Add addUserData
	 *
	 * @param Reurbano\AnalyticsBundle\Document\UserData $userData
	 */
	public function addUserData(\Reurbano\AnalyticsBundle\Document\UserData $userData)
	{
		$this->userData[] = $userData;
	}
	
	/**
	 * Get userData
	 *
	 * @return Doctrine\Common\Collections\Collection $userData
	 */
	public function getUserData()
	{
		return $this->userData;
	}
		
	/** @ODM\PrePersist */
	public function doPrePersist()
	{
		// Seta data de cria��o
		$this->setCreatedAt(new \DateTime);
		$this->setUpdatedAt(new \DateTime);
	}
	
	/** @ODM\PreUpdate */
	public function doPreUpdate()
	{
		// Seta data de atualiza��o
		$this->setUpdatedAt(new \DateTime);
	}
}