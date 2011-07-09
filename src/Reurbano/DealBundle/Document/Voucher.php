<?php

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa um Voucher de Oferta
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\EmbeddedDocument
 */
class Voucher
{
    /**
     * Título
     *
     * @var string
     * @ODM\String
     */
    protected $title;
    
    /**
     * Nome do Arquivo
     *
     * @var string
     * @ODM\String
     */
    protected $filename;
    
    /**
     * Tamanho do Arquivo
     *
     * @var string
     * @ODM\String
     */
    protected $filesize;
    
    /**
     * Path
     *
     * @var string
     * @ODM\String
     */
    protected $path;
    
    /**
     * URL
     *
     * @var string
     * @ODM\String
     */
    protected $url;
    
    /**
     * Data de Envio
     *
     * @var string
     * @ODM\Timestamp
     */
    protected $created;
}