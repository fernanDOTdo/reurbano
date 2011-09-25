<?php

namespace Reurbano\OrderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Description of StatusLog
 *
 * @author Rafael Basquens <rafael@basquens.com>
 * 
 * @ODM\EmbeddedDocument
 */
class StatusLog
{
    /**
     * Mongo id
     * 
     * @var MongoId
     * @ODM\Id
     */
    protected $id;
    
    /**
     * Usuário que editou o status
     * 
     * @var object
     * @ODM\ReferenceOne(targetDocument="Reurbano\UserBundle\Document\User")
     */
    protected $user;
    
    /**
     * Status
     * 
     * @var object
     * @ODM\ReferenceOne(targetDocument="Reurbano\OrderBundle\Document\Status")
     */
    protected $status;
    
    /**
     * Data de Criação
     *
     * @var object
     * @ODM\Date
     */
    protected $created;
    
    /**
     * Observação de quando o status foi mudado
     * 
     * @var string
     * @ODM\String
     */
    protected $obs;
    
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
     * Set obs
     *
     * @param string $obs
     */
    public function setObs($obs)
    {
        $this->obs = $obs;
    }

    /**
     * Get obs
     *
     * @return string $obs
     */
    public function getObs()
    {
        return $this->obs;
    }
}