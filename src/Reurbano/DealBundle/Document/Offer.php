<?php

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa uma Oferta
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\EmbeddedDocument
 */
class Offer
{
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
    

    /**
     * Set source
     *
     * @param Reurbano\DealBundle\Document\Source $source
     */
    public function setSource(\Reurbano\DealBundle\Document\Source $source)
    {
        $this->source = $source;
    }

    /**
     * Get source
     *
     * @return Reurbano\DealBundle\Document\Source $source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set city
     *
     * @param Reurbano\CoreBundle\Document\City $city
     */
    public function setCity(\Reurbano\CoreBundle\Document\City $city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return Reurbano\CoreBundle\Document\City $city
     */
    public function getCity()
    {
        return $this->city;
    }
}