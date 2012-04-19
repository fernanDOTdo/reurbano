<?php

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Representa uma SubCategoria
 *
 * @author   Saulo Lima <saulo@gubn.com.br>
 *
 * @ODM\Document(
 *   collection="subcategory",
 *   repositoryClass="Reurbano\DealBundle\Document\SubCategoryRepository"
 * )
 * @ODM\Indexes({
 *   @ODM\Index(keys={"order"="asc", "name"="asc"})
 * })
 */
class SubCategory
{
    /**
     * ID da SubCategoria
     *
     * @var string
     * @ODM\Id
     */
    protected $id;

    /**
     * Nome da SubCategoria
     *
     * @var string
     * @ODM\String
     */
    protected $name;
    
    /**
     * Campo Slug
     *
     * @var string
     * @Gedmo\Slug(fields={"name"})
     * @ODM\UniqueIndex
     * @ODM\String
     */
    protected $slug;

    /**
     * Ordem
     *
     * @var string
     * @ODM\Int
     */
    protected $order = 0;
    
    /**
     * Categoria da SubCategoria
     *
     * @ODM\ReferenceOne(targetDocument="Reurbano\DealBundle\Document\Category")
     * @ODM\Index
     */
    protected $category;

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

    /**
     * Set order
     *
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Get order
     *
     * @return int $order
     */
    public function getOrder()
    {
        return $this->order;
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
}
