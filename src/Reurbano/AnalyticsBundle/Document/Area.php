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
 *   collection="ad_area",
 *   repositoryClass="Reurbano\AnalyticsBundle\Document\AreaRepository"
 * )
 * @ODM\Indexes({
 *   @ODM\Index(keys={"click"="asc"})
 * })
 */
class Area {
	/**
	 * ID da Area
	 *
	 * @var string
	 * @ODM\Id(strategy="increment")
	 */
	protected $id;
	
	/**
	 * Zona
	 *
	 * @var object
	 * @ODM\ReferenceOne(targetDocument="Reurbano\AnalyticsBundle\Document\Zone")
	 * @ODM\Index
	 */
	protected $zone;
	
	/** @ODM\EmbedOne(targetDocument="Reurbano\AnalyticsBundle\Document\Coordinates") */
	protected $coordinates;
	
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
     * Set zone
     *
     * @param Reurbano\AnalyticsBundle\Document\Zone $zone
     */
    public function setZone(\Reurbano\AnalyticsBundle\Document\Zone $zone)
    {
        $this->zone = $zone;
    }

    /**
     * Get zone
     *
     * @return Reurbano\AnalyticsBundle\Document\Zone $zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Set coordinates
     *
     * @param Reurbano\AnalyticsBundle\Document\Coordinates $coordinates
     */
    public function setCoordinates(\Reurbano\AnalyticsBundle\Document\Coordinates $coordinates)
    {
        $this->coordinates = $coordinates;
    }

    /**
     * Get coordinates
     *
     * @return Reurbano\AnalyticsBundle\Document\Coordinates $coordinates
     */
    public function getCoordinates()
    {
        return $this->coordinates;
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
}
