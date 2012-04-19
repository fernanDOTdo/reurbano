<?php

namespace Reurbano\AnalyticsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Representa uma zona do site
 *
 * @author   Saulo Lima <saulo@gubn.com.br>
 *
 * @ODM\Document(
 *   collection="ad_zone",
 *   repositoryClass="Reurbano\AnalyticsBundle\Document\ZoneRepository"
 * )
 * @ODM\Indexes({
 *   @ODM\Index(keys={"special"="desc", "order"="asc", "name"="asc"})
 * })
 */
class Zone {
	
	/**
	 * ID da Zona
	 *
	 * @var string
	 * @ODM\Id(strategy="increment")
	 */
	protected $id;
	
	/**
	 * Nome da Zona
	 *
     * @var string
     * @ODM\String
     * @ODM\Index
     */
	protected $name;
	
 	/**
     * Campo Slug
     *
     * @var string
     * @ODM\String
     * @ODM\UniqueIndex
     * @Gedmo\Slug(fields={"name"})
     */
	protected $slug;
	
	/**
	 * Descrição da Zona
	 *
	 * @var string
	 * @ODM\String
	 */
	protected $description;
	
	/**
	 * Tipo da zona
	 *
	 * @var object
     * @ODM\ReferenceOne(targetDocument="Reurbano\AnalyticsBundle\Document\ZoneType")
     * @ODM\Index
     */
    protected $zoneType;
    
    /**
     * Zona Restrita
     *
     * @var string
     * @ODM\Boolean
     */
    protected $restricted = false;
    
    /**
     * Cliques na zona
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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
    	return $this->name = $name;
    }
    
    /**
     * Get name
     *
     * @return string $name
     *
     */
    public function getName()
    {
    	return $this->name;
    }
    
    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
    	return $this->slug = $slug;
    }
    
    /**
     * Get slug
     *
     * @return string $slug
     *
     */
    public function getSlug()
    {
    	return $this->slug;
    }
    
    /**
     * Set click
     *
     * @param int $click
     */
    public function setClick($click)
    {
    	return $this->click = $click;
    }
    
    /**
     * Get click
     *
     * @return int $click
     *
     */
    public function getClick()
    {
    	return $this->click;
    }
    
    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
    	return $this->description = $description;
    }
    
    /**
     * Get description
     *
     * @return string $description
     *
     */
    public function getDescription()
    {
    	return $this->description;
    }
    
    
    public function isRestricted()
    {
    	return ($this->getRestricted()) ? "Sim" : "Não";
    }
    
    /**
     * Set restricted
     *
     * @param boolean $restricted
     */
    public function setRestricted($restricted)
    {
    	return $this->restricted = $restricted;
    }
    
    /**
     * Get restricted
     *
     * @return boolean $restricted
     *
     */
    public function getRestricted()
    {
    	return $this->restricted;
    }
    
    /**
     * Set zoneType
     *
     * @param Reurbano\AnalyticsBundle\Document\ZoneType $zoneType
     */
    public function setZoneType(\Reurbano\AnalyticsBundle\Document\ZoneType $zoneType)
    {
    	$this->zoneType = $zoneType;
    }
    
    /**
     * Get zoneType
     *
     * @return Reurbano\AnalyticsBundle\Document\ZoneType $source
     */
    public function getZoneType()
    {
    	return $this->zoneType;
    }

    /**
     * Get id
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
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
}
