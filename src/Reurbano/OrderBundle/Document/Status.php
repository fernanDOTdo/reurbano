<?php

namespace Reurbano\OrderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa um Status
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\Document(
 *   collection="status",
 *   repositoryClass="Reurbano\OrderBundle\Document\StatusRepository"
 * )
 */
class Status
{
    /**
     * ID do Status
     *
     * @var string
     * @ODM\Id(strategy="increment")
     */
    protected $id;

    /**
     * Nome do Status
     *
     * @var string
     * @ODM\String
     */
    protected $name;

    /**
     * Ordem
     *
     * @var string
     * @ODM\Int
     */
    protected $order = 0;
    
    /**
     * Set id
     *
     * @return custom_id $id
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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set order
     *
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Get order
     *
     * @return int $order
     */
    public function getOrder()
    {
        return $this->order;
    }
}