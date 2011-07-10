<?php

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

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
     * @todo Implementar ReferenceOne para o bundle de usuários
     * @var int
     * @ODM\Int
     */
    protected $idUser;
    
    /**
     * Preço original da oferta
     *
     * @ODM\ReferenceOne(targetDocument="Reurbano\DealBundle\Document\Source")
     */
    protected $offer;
    
    /**
     * Preço com desconto da oferta
     *
     * @var float
     * @ODM\Float
     */
    protected $price;
    
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
     * @ODM\String
     */
    protected $label;
    
    /**
     * Visualizações do pedido
     *
     * @var int
     * @ODM\Int
     */
    protected $views = 0;
    
    /**
     * Data de expiração da oferta
     *
     * @var date
     * @ODM\Date
     */
    protected $expiresAt;
    
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
     * Set idUser
     *
     * @param int $idUser
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    /**
     * Get idUser
     *
     * @return int $idUser
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set offer
     *
     * @param Reurbano\DealBundle\Document\Source $offer
     */
    public function setOffer(\Reurbano\DealBundle\Document\Source $offer)
    {
        $this->offer = $offer;
    }

    /**
     * Get offer
     *
     * @return Reurbano\DealBundle\Document\Source $offer
     */
    public function getOffer()
    {
        return $this->offer;
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
     * Set expiresAt
     *
     * @param date $expiresAt
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * Get expiresAt
     *
     * @return date $expiresAt
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
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
}