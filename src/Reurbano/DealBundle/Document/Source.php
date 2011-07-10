<?php

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa uma Oferta do Banco de Ofertas
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\Document(
 *   collection="source",
 *   repositoryClass="Reurbano\DealBundle\Document\SourceRepository"
 * )
 */
class Source
{
    /**
     * ID da Oferta
     *
     * @var string
     * @ODM\Id
     */
    protected $id;

    /**
     * Título da Oferta
     *
     * @var string
     * @ODM\String
     */
    protected $title;
    

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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }
}