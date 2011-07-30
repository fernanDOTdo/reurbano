<?php

namespace Reurbano\OrderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa uma Retirada
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\Document(
 *   collection="checkout",
 *   repositoryClass="Reurbano\OrderBundle\Document\CheckoutRepository"
 * )
 */
class Checkout
{
    /**
     * ID da Retirada
     *
     * @var string
     * @ODM\Id
     */
    protected $id;

    /**
     * Usuário (mudar para referenceOne)
     *
     * @var string
     * @ODM\String
     */
    protected $user;
    
    /**
     * Comentários
     *
     * @var array
     * @ODM\EmbedMany(targetDocument="Reurbano\OrderBundle\Document\Comment")
     */
    protected $comments = array();
    
    /**
     * Status
     * 
     * @ODM\ReferenceOne(targetDocument="Reurbano\OrderBundle\Document\Status")
     */
    private $status;
    
    /**
     * Data de Criação
     *
     * @var object
     * @ODM\Date
     */
    protected $created;
    
    /**
     * Data de Atualização
     *
     * @var object
     * @ODM\Date
     */
    protected $updated;

    public function __construct()
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return string $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add comments
     *
     * @param Reurbano\OrderBundle\Document\Comment $comments
     */
    public function addComments(\Reurbano\OrderBundle\Document\Comment $comments)
    {
        $this->comments[] = $comments;
    }

    /**
     * Get comments
     *
     * @return Doctrine\Common\Collections\Collection $comments
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set status
     *
     * @param Reurbano\OrderBundle\Document\Status $status
     */
    public function setStatus(\Reurbano\OrderBundle\Document\Status $status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return Reurbano\OrderBundle\Document\Status $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set created
     *
     * @param date $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return date $created
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param date $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Get updated
     *
     * @return date $updated
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}