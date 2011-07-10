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
     * Nome do Arquivo
     *
     * @var string
     * @ODM\String
     */
    protected $filename;
    
    /**
     * URL da Oferta
     *
     * @var string
     * @ODM\String
     */
    protected $url;
    
    /**
     * Preço original da oferta
     *
     * @var float
     * @ODM\Float
     */
    protected $price;
    
    /**
     * Preço com desconto da oferta
     *
     * @var float
     * @ODM\Float
     */
    protected $priceOffer;
    
    /**
     * Preço original da oferta
     *
     * @ODM\ReferenceOne(targetDocument="Reurbano\CoreBundle\Document\City")
     */
    protected $city;
    
    /**
     * Regulamento da oferta
     *
     * @var string
     * @ODM\String
     */
    protected $rules;
    
    /**
     * Descrição da oferta
     *
     * @var string
     * @ODM\String
     */
    protected $details;
    
    /**
     * Site da empresa
     *
     * @var string
     * @ODM\String
     */
    protected $businessUrl;
    
    /**
     * Nome da empresa
     *
     * @var string
     * @ODM\String
     */
    protected $businessName;
    
    /**
     * Endereço da empresa
     *
     * @var string
     * @ODM\String
     */
    protected $businessAddress;
    
    /**
     * Latitude da empresa
     *
     * @var int
     * @ODM\Int
     */
    protected $businessLatitude;
    
    /**
     * Longitude da empresa
     *
     * @var int
     * @ODM\Int
     */
    protected $businessLongitude;
    

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

    /**
     * Set filename
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Get filename
     *
     * @return string $filename
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set price
     *
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Get price
     *
     * @return float $price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set priceOffer
     *
     * @param float $priceOffer
     */
    public function setPriceOffer($priceOffer)
    {
        $this->priceOffer = $priceOffer;
    }

    /**
     * Get priceOffer
     *
     * @return float $priceOffer
     */
    public function getPriceOffer()
    {
        return $this->priceOffer;
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

    /**
     * Set rules
     *
     * @param string $rules
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
    }

    /**
     * Get rules
     *
     * @return string $rules
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Set details
     *
     * @param string $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * Get details
     *
     * @return string $details
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set businessUrl
     *
     * @param string $businessUrl
     */
    public function setBusinessUrl($businessUrl)
    {
        $this->businessUrl = $businessUrl;
    }

    /**
     * Get businessUrl
     *
     * @return string $businessUrl
     */
    public function getBusinessUrl()
    {
        return $this->businessUrl;
    }

    /**
     * Set businessName
     *
     * @param string $businessName
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;
    }

    /**
     * Get businessName
     *
     * @return string $businessName
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * Set businessAddress
     *
     * @param string $businessAddress
     */
    public function setBusinessAddress($businessAddress)
    {
        $this->businessAddress = $businessAddress;
    }

    /**
     * Get businessAddress
     *
     * @return string $businessAddress
     */
    public function getBusinessAddress()
    {
        return $this->businessAddress;
    }

    /**
     * Set businessLatitude
     *
     * @param int $businessLatitude
     */
    public function setBusinessLatitude($businessLatitude)
    {
        $this->businessLatitude = $businessLatitude;
    }

    /**
     * Get businessLatitude
     *
     * @return int $businessLatitude
     */
    public function getBusinessLatitude()
    {
        return $this->businessLatitude;
    }

    /**
     * Set businessLongitude
     *
     * @param int $businessLongitude
     */
    public function setBusinessLongitude($businessLongitude)
    {
        $this->businessLongitude = $businessLongitude;
    }

    /**
     * Get businessLongitude
     *
     * @return int $businessLongitude
     */
    public function getBusinessLongitude()
    {
        return $this->businessLongitude;
    }
}