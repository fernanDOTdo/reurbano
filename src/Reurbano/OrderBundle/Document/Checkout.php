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

}