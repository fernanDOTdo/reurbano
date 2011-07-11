<?php

namespace Reurbano\OrderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa um Reembolso
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\Document(
 *   collection="refund",
 *   repositoryClass="Reurbano\OrderBundle\Document\RefundRepository"
 * )
 */
class Refund
{
    /**
     * ID do Reembolso
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