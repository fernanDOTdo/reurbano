<?php

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Representa uma Categoria
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\Document(
 *   collection="category",
 *   repositoryClass="Reurbano\DealBundle\Document\CategoryRepository"
 * )
 * @ODM\Indexes({
 *   @ODM\Index(keys={"order"="asc", "name"="asc"})
 * })
 */
class Category
{
    /**
     * ID da Categoria
     *
     * @var string
     * @ODM\Id
     */
    protected $id;

    /**
     * Nome da Categoria
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
}
