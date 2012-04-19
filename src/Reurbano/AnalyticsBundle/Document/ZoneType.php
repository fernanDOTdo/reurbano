<?php

namespace Reurbano\AnalyticsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Representa um tipo de zona do site
 *
 * @author   Saulo Lima <saulo@gubn.com.br>
 *
 * @ODM\Document(
 *   collection="ad_zonetype",
 *   repositoryClass="Reurbano\AnalyticsBundle\Document\ZoneTypeRepository"
 * )
 * @ODM\Indexes({
 *   @ODM\Index(keys={"name"="asc"})
 * })
 */
class ZoneType {
	
	/**
	 * ID do tipo de zona
	 *
	 * @var string
	 * @ODM\Id(strategy="increment")
	 * 
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
     * Get id
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
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
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string $slug
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
