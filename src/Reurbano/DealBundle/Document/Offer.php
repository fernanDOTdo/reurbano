<?php

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa uma Oferta
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\Document(
 *   collection="offer",
 *   repositoryClass="Reurbano\DealBundle\Document\OfferRepository"
 * )
 */
class Offer
{
    /**
     * ID da Oferta
     *
     * @var string
     * @ODM\Id
     */
    protected $id;
    
    /**
     * Preço original da oferta
     *
     * @ODM\ReferenceOne(targetDocument="Reurbano\DealBundle\Document\Source")
     */
    protected $source;
    
    /**
     * Cidade da oferta
     *
     * @ODM\ReferenceOne(targetDocument="Reurbano\CoreBundle\Document\City")
     */
    protected $city;
    
}