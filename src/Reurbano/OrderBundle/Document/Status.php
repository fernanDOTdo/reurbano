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
}