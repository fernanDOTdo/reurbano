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
    
}