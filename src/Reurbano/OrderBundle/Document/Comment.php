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
     * Usuário
     *
     * @ODM\ReferenceOne(targetDocument="Reurbano\UserBundle\Document\User")
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

    /**
     * Prepersist para setar o created
     * 
     * @ODM\prePersist
     */
    public function prePersist()
    {
        $this->setCreated(new \DateTime());
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
     * Set message
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Get message
     *
     * @return string $message
     */
    public function getMessage()
    {
        return $this->message;
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
     * Set special
     *
     * @param boolean $special
     */
    public function setSpecial($special)
    {
        $this->special = $special;
    }

    /**
     * Get special
     *
     * @return boolean $special
     */
    public function getSpecial()
    {
        return $this->special;
    }
}