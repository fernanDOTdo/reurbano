<?php

namespace Reurbano\AnalyticsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Representa uma publicação dentra da área de uma zona
 *
 * @author   Saulo Lima <saulo@gubn.com.br>
 *
 * @ODM\Document(
 *   collection="ad_publication",
 *   repositoryClass="Reurbano\AnalyticsBundle\Document\PublicationRepository"
 * )
 */
class Publication {
	
	/**
	 * ID da Publicação
	 *
	 * @var string
	 * @ODM\Id
	 */
	protected $id;
	
	/**
	 * Area
	 * 
	 * @var object
     * @ODM\ReferenceOne(targetDocument="Reurbano\AnalyticsBundle\Document\Area")
     * @ODM\Index
     */	
	protected $area;
	
	/**
	 * Deal
	 *
	 * @var object
	 * @ODM\ReferenceOne(targetDocument="Reurbano\DealBundle\Document\Deal")
	 * @ODM\Index
	 */
	protected $deal;
		
	/**
	 * Cliques na área
	 *
	 * @var int
	 * @ODM\Int
	 */
	protected $click = 0;
	
	/**
	 * Data da útima atualização da area
	 *
	 * @var date
	 * @ODM\Date
	 */
	protected $updatedAt;
	
	/**
	 * Data de criação da area
	 *
	 * @var date
	 * @ODM\Date
	 * @ODM\Index(order="desc")
	 */
	protected $createdAt;

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
     * Set area
     *
     * @param Reurbano\AnalyticsBundle\Document\Area $area
     */
    public function setArea(\Reurbano\AnalyticsBundle\Document\Area $area)
    {
        $this->area = $area;
    }

    /**
     * Get area
     *
     * @return Reurbano\AnalyticsBundle\Document\Area $area
     */
    public function getArea()
    {
        return $this->area;
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
