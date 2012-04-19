<?php

namespace Reurbano\AnalyticsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Representa um tracking de usuário de sites externos
 *
 * @author Saulo Lima <saulo@gubn.com.br>
 *
 * @ODM\Document(
 *   collection="ad_tracking_sell",
 *   repositoryClass="Reurbano\AnalyticsBundle\Document\TrackingSellRepository"
 * )
 */

class TrackingSell
{
	/**
	 * ID do TrackingSell
	 *
	 * @var string
	 * @ODM\Id
	 */
	protected $id;
	
	/**
	 * TrackingPreSell
	 *
	 * @var object
	 * @ODM\ReferenceOne(targetDocument="Reurbano\AnalyticsBundle\Document\TrackingPreSell")
	 * @ODM\Index
	 */
	protected $trackingPreSell;
	
	/**
	 * Representa um Pedido
	 *
	 * @var array
	 * @ODM\ReferenceMany(targetDocument="Reurbano\OrderBundle\Document\Order")
	 */
	protected $order = array();
	
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
     * Caso não esteja, significa que o parceiro gerou venda de outra oferta
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
     * Set trackingPreSell
     *
     * @param Reurbano\AnalyticsBundle\Document\TrackingPreSell $trackingPreSell
     */
    public function setTrackingPreSell(\Reurbano\AnalyticsBundle\Document\TrackingPreSell $trackingPreSell)
    {
    	$this->trackingPreSell = $trackingPreSell;
    }
    
    /**
     * Get trackingPreSell
     *
     * @return Reurbano\AnalyticsBundle\Document\TrackingPreSell $tracking
     */
    public function getTrackingPreSell()
    {
    	return $this->trackingPreSell;
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
	 * Add userData
	 *
	 * @param Reurbano\AnalyticsBundle\Document\UserData $userData
	 */
	public function addOrder(\Reurbano\OrderBundle\Document\Order $order)
	{
		$this->order[] = $order;
	}
	
	/**
	 * Get order
	 *
	 * @return Reurbano\OrderBundle\Document\Order $order
	 */
	public function getOrder()
	{
		return $this->order;
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