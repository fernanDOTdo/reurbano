<?php

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa uma Oferta
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\Document(
 *   collection="deal",
 *   repositoryClass="Reurbano\DealBundle\Document\DealRepository"
 * )
 */
class Deal
{
    /**
     * ID da Oferta
     *
     * @var string
     * @ODM\Id
     */
    protected $id;
    
    /**
     * Usuário que fez o anuncio
     *
     * @todo Implementar ReferenceOne para o bundle de usuários
     * @var int
     * @ODM\Int
     */
    protected $idUser;
    
    /**
     * Preço original da oferta
     *
     * @ReferenceOne(targetDocument="Reurbano\DealBundle\Document\Source")
     */
    protected $offer;
    
    /**
     * Preço com desconto da oferta
     *
     * @var float
     * @ODM\Float
     */
    protected $price;
    
    /**
     * Quantidade disponivel
     *
     * @var int
     * @ODM\Int
     */
    protected $quantity;
    
    /**
     * Vouchers
     *
     * @var array
     * @ODM\EmbedMany(targetDocument="Reurbano\DealBundle\Document\Voucher")
     */
    protected $voucher = array();
    
    /** @ODM\Collection */
    protected $tags = array();
    
    /**
     * Se o produto está ativo ou não
     *
     * @var boolean
     * @ODM\Boolean
     */
    protected $active;
    
    /**
     * Rotulação do produto
     *
     * @var string
     * @ODM\String
     */
    protected $label;
    
    /**
     * Visualizações do pedido
     *
     * @var int
     * @ODM\Int
     */
    protected $views = 0;
    
    /**
     * Data de expiração da oferta
     *
     * @var date
     * @ODM\Date
     */
    protected $expiresAt;
    
    /**
     * Data de edição da oferta
     *
     * @var date
     * @ODM\Date
     */
    protected $updatedAt;
    
    /**
     * Data de criação da oferta
     *
     * @var date
     * @ODM\Date
     */
    protected $createdAt;

}