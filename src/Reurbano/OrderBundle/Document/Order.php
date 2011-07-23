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
     * Usuário (mudar para referenceOne)
     *
     * @ODM\ReferenceOne(targetDocument="Reurbano\UserBundle\Document\User")
     */
    protected $user;
    
    /**
     * Array com 
     */
    
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
     * @ODM\ReferenceOne(targetDocument="Reurbano\OrderBundle\Document\Status")
     */
    private $status;
    
    /**
     * Comentários
     *
     * @var array
     * @ODM\EmbedMany(targetDocument="Reurbano\OrderBundle\Document\Comment")
     */
    protected $comments = array();
    
    public function __construct()
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
    }
}