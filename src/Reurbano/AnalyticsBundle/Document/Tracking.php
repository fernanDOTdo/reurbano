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
 *   collection="ad_tracking",
 *   repositoryClass="Reurbano\AnalyticsBundle\Document\TrackingRepository"
 * )
 */

class Tracking
{
	/**
	 * ID do Tracking
	 *
	 * @var string
	 * @ODM\Id
	 */
	protected $id;
	
	/**
	 * Parceiro de origem do tracking
	 *
	 * @var object
	 * @ODM\EmbedOne(targetDocument="Reurbano\AnalyticsBundle\Document\AssociateEmbed")
	 */
	protected $associate;
	
	/**
	 * Deal do tracking 
	 *
	 * @var object
	 * @ODM\ReferenceOne(targetDocument="Reurbano\DealBundle\Document\Deal")
	 * @ODM\Index
	 */
	protected $deal;
	
	/**
	 * Cidade da oferta do tracking
	 *
	 * @ODM\ReferenceOne(targetDocument="Reurbano\CoreBundle\Document\City")
	 * @ODM\Index
	 */
	protected $city;
	
	/**
	 * Categoria da oferta do tracking
	 *
	 * @ODM\ReferenceOne(targetDocument="Reurbano\DealBundle\Document\Category")
	 * @ODM\Index
	 */
	protected $category;
		
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
	 * Set associate
	 *
	 * @param Reurbano\AnalyticsBundle\Document\AssociateEmbed $associate
	 */
	public function setAssociate(\Reurbano\AnalyticsBundle\Document\AssociateEmbed $associate)
	{
		$this->associate = $associate;
	}
	
	/**
	 * Get associate
	 *
	 * @return Reurbano\AnalyticsBundle\Document\AssociateEmbed $associate
	 */
	public function getAssociate()
	{
		return $this->associate;
	}
	
	/**
	 * Set deal
	 *
	 * @param Reurbano\DealBundle\Document\Deal $deal
	 */
	public function setDeal(\Reurbano\DealBundle\Document\Deal $deal)
	{
		$this->deal = $deal;
	}
	
	/**
	 * Get deal
	 *
	 * @return Reurbano\DealBundle\Document\Deal $deal
	 */
	public function getDeal()
	{
		return $this->deal;
	}	
	
	/**
	 * Set city
	 *
	 * @param Reurbano\CoreBundle\Document\City $city
	 */
	public function setCity(\Reurbano\CoreBundle\Document\City $city)
	{
		$this->city = $city;
	}
	
	/**
	 * Get city
	 *
	 * @return Reurbano\CoreBundle\Document\City $city
	 */
	public function getCity()
	{
		return $this->city;
	}
	
	/**
	 * Set category
	 *
	 * @param Reurbano\DealBundle\Document\Category $category
	 */
	public function setCategory(\Reurbano\DealBundle\Document\Category $category)
	{
		$this->category = $category;
	}
	
	/**
	 * Get category
	 *
	 * @return Reurbano\DealBundle\Document\Category $category
	 */
	public function getCategory()
	{
		return $this->category;
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