<?php

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Representa uma Oferta
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\Document(
 *   collection="deal",
 *   repositoryClass="Reurbano\DealBundle\Document\DealRepository"
 * )
 */
class Deal
{
    /**
     * ID da Oferta
     *
     * @var string
     * @ODM\Id
     */
    protected $id;
    
    /**
     * Usuário que fez o anuncio
     *
     * @ODM\ReferenceOne(targetDocument="Reurbano\UserBundle\Document\User")
     */
    protected $user;
    
    /**
     * Source da Oferta
     *
     * @var array
     * @ODM\EmbedOne(targetDocument="Reurbano\DealBundle\Document\Source")
     */
    protected $source;
    
    /**
     * Preço com desconto da oferta
     *
     * @var float
     * @ODM\Float
     */
    protected $price;
    
    /**
     * Porcentagem do desconto (sobre o preço original)
     *
     * @var int
     * @ODM\Int
     * @ODM\Index
     */
    protected $discount;
    
    /**
     * Quantidade disponivel
     *
     * @var int
     * @ODM\Int
     */
    protected $quantity;
    
    /**
     * Vouchers
     *
     * @var array
     * @ODM\EmbedMany(targetDocument="Reurbano\DealBundle\Document\Voucher")
     */
    protected $voucher = array();
    
    /** @ODM\Collection */
    protected $tags = array();
    
    /**
     * Se o produto está ativo ou não
     *
     * @var boolean
     * @ODM\Boolean
     */
    protected $active;
    
    /**
     * Rotulação do produto
     *
     * @var string
     * @Gedmo\Sluggable
     * @ODM\String
     */
    protected $label;
    
    /**
     * Campo Slug
     *
     * @var string
     * @Gedmo\Slug
     * @ODM\UniqueIndex
     * @ODM\String
     */
    
    protected $slug;
    
    /**
     * Visualizações do pedido
     *
     * @var int
     * @ODM\Int
     */
    protected $views = 0;
    
    /**
     * Verifica se a oferta está correta
     *
     * @var boolean
     * @ODM\Boolean
     */
    protected $checked;
    
    /**
     * Verifica se a oferta é destaque
     *
     * @var boolean
     * @ODM\Boolean
     */
    protected $special;
    
    /**
     * Data de edição da oferta
     *
     * @var date
     * @ODM\Date
     */
    protected $updatedAt;
    
    /**
     * Data de criação da oferta
     *
     * @var date
     * @ODM\Date
     */
    protected $createdAt;
    
    /** @ODM\PrePersist */
    public function doPrePersist()
    {
        $this->setCreatedAt(new \DateTime);
        $count1 = $this->getPrice() / $this->getSource()->getPrice();
        $count2 = $count1 * 100;
        $count3 = 100 - $count2;
        $count = number_format($count3, 0);
        $this->setDiscount($count);
    }

    /** @ODM\PreUpdate */
    public function doPreUpdate()
    {
        $this->setUpdatedAt(new \DateTime);
        $count1 = $this->getPrice() / $this->getSource()->getPrice();
        $count2 = $count1 * 100;
        $count3 = 100 - $count2;
        $count = number_format($count3, 0);
        $this->setDiscount($count);
    }

    public function __construct()
    {
        $this->voucher = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set price
     *
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Get price
     *
     * @return float $price
     */
    public function getPrice()
    {
        return $this->price;
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
     * Set quantity
     *
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Get quantity
     *
     * @return int $quantity
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Add voucher
     *
     * @param Reurbano\DealBundle\Document\Voucher $voucher
     */
    public function addVoucher(\Reurbano\DealBundle\Document\Voucher $voucher)
    {
        $this->voucher[] = $voucher;
    }

    /**
     * Get voucher
     *
     * @return Doctrine\Common\Collections\Collection $voucher
     */
    public function getVoucher()
    {
        return $this->voucher;
    }

    /**
     * Set tags
     *
     * @param collection $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * Get tags
     *
     * @return collection $tags
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean $active
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get label
     *
     * @return string $label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set views
     *
     * @param int $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * Get views
     *
     * @return int $views
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set checked
     *
     * @param boolean $checked
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;
    }

    /**
     * Get checked
     *
     * @return boolean $checked
     */
    public function getChecked()
    {
        return $this->checked;
    }

    /**
     * Set special
     *
     * @param boolean $special
     */
    public function setSpecial($special)
    {
        $this->special = $special;
    }

    /**
     * Get special
     *
     * @return boolean $special
     */
    public function getSpecial()
    {
        return $this->special;
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
     * Set source
     *
     * @param Reurbano\DealBundle\Document\Source $source
     */
    public function setSource(\Reurbano\DealBundle\Document\Source $source)
    {
        $this->source = $source;
    }

    /**
     * Get source
     *
     * @return Reurbano\DealBundle\Document\Source $source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set discount
     *
     * @param int $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * Get discount
     *
     * @return int $discount
     */
    public function getDiscount()
    {
        return $this->discount;
    }
}