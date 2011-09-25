<?php

namespace Reurbano\OrderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa um Pedido
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\Document(
 *   collection="order",
 *   repositoryClass="Reurbano\OrderBundle\Document\OrderRepository"
 * )
 */
class Order
{
    /**
     * ID do Pedido
     *
     * @var string
     * @ODM\Id(strategy="NONE")
     */
    protected $id;

    /**
     * Usuário
     *
     * @var object
     * @ODM\ReferenceOne(targetDocument="Reurbano\UserBundle\Document\User")
     */
    protected $user;

    /**
     * Vendedor
     *
     * @var object
     * @ODM\ReferenceOne(targetDocument="Reurbano\UserBundle\Document\User")
     */
    protected $seller;
    
    /**
     * Array com as informações sobre o pagamento
     * 
     * @var array
     * @ODM\Hash
     */
    protected $payment = array();

    /**
     * Total do valor gasto no pedido
     * 
     * @var float
     * @ODM\Float
     */
    protected $total;
    
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
    
    /**
     * Status
     * 
     * @var object
     * @ODM\ReferenceOne(targetDocument="Reurbano\OrderBundle\Document\Status")
     */
    protected $status;
    
    /**
     * Guarda o histórico dos status do pedido
     * 
     * @var object
     * @ODM\EmbedMany(targetDocument="Reurbano\OrderBundle\Document\StatusLog")
     */
    protected $statusLog;
    
    /**
     * Referência para a Oferta vendida
     * 
     * @var object
     * @ODM\ReferenceOne(targetDocument="Reurbano\DealBundle\Document\Deal")
     */
    protected $deal;
    
    /**
     * Quantidade vendida
     *
     * @var int
     * @ODM\Int
     */
    protected $quantity;
    
    /**
     * Comentários
     *
     * @var array
     * @ODM\EmbedMany(targetDocument="Reurbano\OrderBundle\Document\Comment")
     */
    protected $comments = array();
    
    /**
     * Dados do usuário, exemplo: Ip. Origem, OS, etc
     * 
     * @var array
     * @ODM\Hash
     */
    protected $userData;
    
    /**
     * Guarda as informações de SEO
     * 
     * @var object
     * @ODM\EmbedOne(targetDocument="Reurbano\CoreBundle\Document\Seo")
     */
    protected $seo;

    /** 
     * @ODM\PrePersist 
     */
    public function doPrePersist()
    {
        $this->setCreated(new \DateTime);
    }
    /** 
     * @ODM\PreUpdate
     */
    public function doPreUpdate()
    {
        $this->setUpdated(new \DateTime);
    }
    public function __construct()
    {
        $this->statusLog = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set id
     *
     * @param custom_id $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return custom_id $id
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
     * Set seller
     *
     * @param Reurbano\UserBundle\Document\User $seller
     */
    public function setSeller(\Reurbano\UserBundle\Document\User $seller)
    {
        $this->seller = $seller;
    }

    /**
     * Get seller
     *
     * @return Reurbano\UserBundle\Document\User $seller
     */
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * Set payment
     *
     * @param hash $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get payment
     *
     * @return hash $payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set total
     *
     * @param float $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * Get total
     *
     * @return float $total
     */
    public function getTotal()
    {
        return $this->total;
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
     * Get Status name
     * 
     * @return string
     */
    public function getStatusName(){
        if($this->getStatus()){
            return $this->getStatus()->getName();
        }else{
            return 'Cancelado';
        }
    }

    /**
     * Add statusLog
     *
     * @param Reurbano\OrderBundle\Document\StatusLog $statusLog
     */
    public function addStatusLog(\Reurbano\OrderBundle\Document\StatusLog $statusLog)
    {
        $this->statusLog[] = $statusLog;
    }

    /**
     * Get statusLog
     *
     * @return Doctrine\Common\Collections\Collection $statusLog
     */
    public function getStatusLog()
    {
        return $this->statusLog;
    }

    /**
     * Set deal
     *
     * @param Reurbano\DealBundle\Document\Deal $deal
     */
    public function setDeal(\Reurbano\DealBundle\Document\Deal $deal)
    {
        $this->deal = $deal;
    }

    /**
     * Get deal
     *
     * @return Reurbano\DealBundle\Document\Deal $deal
     */
    public function getDeal()
    {
        return $this->deal;
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
     * Set userData
     *
     * @param hash $userData
     */
    public function setUserData($userData)
    {
        $this->userData = $userData;
    }

    /**
     * Get userData
     *
     * @return hash $userData
     */
    public function getUserData()
    {
        return $this->userData;
    }

    /**
     * Set seo
     *
     * @param Reurbano\CoreBundle\Document\Seo $seo
     */
    public function setSeo(\Reurbano\CoreBundle\Document\Seo $seo)
    {
        $this->seo = $seo;
    }

    /**
     * Get seo
     *
     * @return Reurbano\CoreBundle\Document\Seo $seo
     */
    public function getSeo()
    {
        return $this->seo;
    }
}