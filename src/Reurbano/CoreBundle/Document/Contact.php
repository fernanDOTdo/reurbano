<?php

namespace Reurbano\CoreBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Representa um contato
 *
 * @author   Rafael Basquens <rafael@basquens.com>
 *
 * @ODM\Document (
 * collection="contact",
 * ) 
 */
class Contact
{
    /**
     * ID do contato
     *
     * @var mongoId
     * @ODM\Id
     */
    protected $id;
    
    /**
     * Nome do contato
     * 
     * @var string
     * @ODM\string
     */
    protected $name;
    
    /**
     * Telefone informado pelo usuário
     * 
     * @var string
     * @ODM\string
     */
    protected $phone;
    
    /**
     * E-mail informado pelo usuário
     * 
     * @var string
     * @ODM\ string
     */
    protected $mail;
    
    /**
     * Mensagem enviado pelo usuário
     * 
     * @var string
     * @ODM\string
     */
    protected $msg;
    
    /**
     * Data de criação da oferta
     *
     * @var date
     * @ODM\Date
     * @ODM\Index
     */
    protected $createdAt;
    
    /** @ODM\PreUpdate */
    public function doPreUpdate()
    {
        $this->setCreatedAt(new \DateTime);
    }
}