<?php

namespace Reurbano\CoreBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * E-mail fornecido pelo usuário
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\Document(
 *   collection="mail",
 *   repositoryClass="Reurbano\CoreBundle\Document\MailRepository"
 * )
 */
class Mailing
{
    /**
     * ID do e-mail
     *
     * @var string
     * @ODM\Id
     */
    protected $id;
    
    /**
     * Nome da Cidade
     *
     * @var string
     * @ODM\String
     */
    protected $city;
    
    /**
     * E-mail
     * 
     * @var string
     * @ODM\String
     */
    protected $mail;
    
    /**
     * Data de criação da oferta
     *
     * @var date
     * @ODM\Date
     * @ODM\Index(order="desc")
     */
    protected $createdAt;

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
     * Set city
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set mail
     *
     * @param string $mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * Get mail
     *
     * @return string $mail
     */
    public function getMail()
    {
        return $this->mail;
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
    
    /** @ODM\PrePersist */
    public function doPrePersist()
    {
        $this->setCreatedAt(new \DateTime);
    }
}