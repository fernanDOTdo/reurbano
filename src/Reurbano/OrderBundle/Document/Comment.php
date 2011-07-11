<?php

namespace Reurbano\OrderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa um Comentário
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\EmbeddedDocument
 */
class Comment
{
    /**
     * Usuário (mudar para referenceOne)
     *
     * @var string
     * @ODM\String
     */
    protected $user;
    
    /**
     * Mensagem
     *
     * @var string
     * @ODM\String
     */
    protected $message;
    
    /**
     * Data
     *
     * @var object
     * @ODM\Date
     */
    protected $created;
    
    /**
     * Especial (uso genérico)
     *
     * @var bool
     * @ODM\Boolean
     */
    protected $special;
}