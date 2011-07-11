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
     * Título da Oferta
     *
     * @var string
     * @ODM\String
     */
    protected $title;
    
    /**
     * Vouchers
     *
     * @var array
     * @ODM\EmbedMany(targetDocument="Reurbano\DealBundle\Document\Voucher")
     */
    protected $voucher = array();
    
    /** @ODM\Collection */
    protected $tags = array();

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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
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
}