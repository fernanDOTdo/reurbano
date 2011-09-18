<?php

namespace Reurbano\CoreBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Representa um contato
 *
 * @author   Rafael Basquens <rafael@basquens.com>
 *
 * @ODM\Document(
 * collection="contact"
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
     * Ip do usuário
     * 
     * @var string
     * @ODM\String
     */
    protected $ip;


    /**
     * Data de criação da oferta
     *
     * @var date
     * @ODM\Date
     * @ODM\Index
     */
    protected $createdAt;
    
    /** 
     * @ODM\PrePersist 
     */
    public function doPreUpdate()
    {
        $this->setCreatedAt(new \DateTime);
    }
    
    /**
     * Get Id
     * 
     * @return id $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set Id
     *
     * @param id $id 
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Get Name
     *
     * @return string $name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set Name
     *
     * @param string $name 
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get Phone
     *
     * @return string $phone
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * Set Phone
     *
     * @param string $phone
     */
    public function setPhone($phone) {
        $this->phone = $phone;
    }

    /**
     * Get Mail
     *
     * @return string $mail
     */
    public function getMail() {
        return $this->mail;
    }

    /**
     * Set Mail
     *
     * @param string $mail 
     */
    public function setMail($mail) {
        $this->mail = $mail;
    }

    /**
     * Get Mail
     *
     * @return string $msg
     */
    public function getMsg() {
        return $this->msg;
    }

    /**
     * Set Msg
     *
     * @param string $msg 
     */
    public function setMsg($msg) {
        $this->msg = $msg;
    }
    
    /**
     * Get Ip
     *
     * @return string $ip
     */
    public function getIp() {
        return $this->ip;
    }

    /**
     * Set Ip
     * 
     * @param string $ip 
     */
    public function setIp($ip) {
        $this->ip = $ip;
    }

    
    /**
     * Get CreatedAt
     *
     * @return date $createdAt
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set $createdAt
     *
     * @param date $createdAt 
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }
}