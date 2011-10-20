<?php

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa um Voucher de Oferta
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\EmbeddedDocument
 */
class Voucher
{
    /**
     * Título
     *
     * @var string
     * @ODM\String
     */
    protected $title;
    
    /**
     * Nome do Arquivo
     *
     * @var string
     * @ODM\String
     */
    protected $filename;
    
    /**
     * Tamanho do Arquivo
     *
     * @var string
     * @ODM\String
     */
    protected $filesize;
    
    /**
     * Path
     *
     * @var string
     * @ODM\String
     */
    protected $path;
    
    /**
     * Data de criação da oferta
     *
     * @var date
     * @ODM\Date
     */
    protected $createdAt;
    
    /**
     * Pedido do Voucher
     * 
     * @var object
     * @ODM\ReferenceOne(targetDocument="Reurbano\OrderBundle\Document\Order")
     */
    protected $order;


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
     * Set filename
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Get filename
     *
     * @return string $filename
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set filesize
     *
     * @param string $filesize
     */
    public function setFilesize($filesize)
    {
        $this->filesize = $filesize;
    }

    /**
     * Get filesize
     *
     * @return string $filesize
     */
    public function getFilesize()
    {
        return $this->filesize;
    }

    /**
     * Set path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get path
     *
     * @return string $path
     */
    public function getPath()
    {
        //return $this->path;
        return __DIR__ . "/../web/uploads/reurbanodeal/voucher";
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
     * Set order
     *
     * @param Reurbano\OrderBundle\Document\Order $order
     */
    public function setOrder(\Reurbano\OrderBundle\Document\Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get order
     *
     * @return Reurbano\OrderBundle\Document\Order $order
     */
    public function getOrder()
    {
        return $this->order;
    }
    /**
     * Remove order
     */
    public function removeOrder()
    {
        unset($this->order);
    }
    
}