<?php

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa um Embed do Source
 *
 * @author   Fernando Santos <o@fernan.do>
 * @ODM\EmbeddedDocument
 * @ODM\Index(keys={"coordinates"="2d"})
 */
class SourceEmbed
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
     * Thumbnail
     *
     * @var string
     * @ODM\String
     */
    protected $thumb;
    
    /**
     * URL da Oferta
     *
     * @var string
     * @ODM\String
     */
    protected $url;
    
    /**
     * Site do source
     * 
     * @var object
     * @ODM\ReferenceOne(targetDocument="Reurbano\DealBundle\Document\Site")
     * @ODM\Index
     */
    protected $site;


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
     * Cidade da oferta
     *
     * @ODM\ReferenceOne(targetDocument="Reurbano\CoreBundle\Document\City")
     * @ODM\Index
     */
    protected $city;
    
    /**
     * Categoria da oferta
     *
     * @ODM\ReferenceOne(targetDocument="Reurbano\DealBundle\Document\Category")
     * @ODM\Index
     */
    protected $category;
    
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
     * CEP da empresa
     *
     * @var string
     * @ODM\String
     */
    protected $businessCep;
    
    /** @ODM\EmbedOne(targetDocument="Reurbano\CoreBundle\Document\Coordinates") */
    protected $coordinates;
    
    /**
     * Data de expiração da oferta
     *
     * @var date
     * @ODM\Date
     */
    protected $expiresAt;
    
    /**
     * Total de cupons vendidos
     *
     * @var int
     * @ODM\Int
     */
    protected $totalcoupons = 0;
    
    /**
     * Valor total vendido
     *
     * @var float
     * @ODM\Float
     */
    protected $totalsell = 0;
    
    /**
     * Data de registro da oferta na base
     *
     * @var date
     * @ODM\Date
     */
    protected $dateRegister;
    
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
     * Set id
     *
     * @param id $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getTitle($limit=0)
    {
        if($limit>0){
            return substr($this->title,0,$limit).(strlen($this->title)>$limit?"...":"");
        }else{
            return $this->title;
        }
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
     * Set site
     *
     * @param Reurbano\DealBundle\Document\Site $site
     */
    public function setSite(\Reurbano\DealBundle\Document\Site $site)
    {
        $this->site = $site;
    }

    /**
     * Get site
     *
     * @return Reurbano\DealBundle\Document\Site $site
     */
    public function getSite()
    {
        return $this->site;
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
     * Set category
     *
     * @param Reurbano\DealBundle\Document\Category $category
     */
    public function setCategory(\Reurbano\DealBundle\Document\Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return Reurbano\DealBundle\Document\Category $category
     */
    public function getCategory()
    {
        return $this->category;
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
     * Set businessCep
     *
     * @param string $businessCep
     */
    public function setBusinessCep($businessCep)
    {
        $this->businessCep = $businessCep;
    }

    /**
     * Get businessCep
     *
     * @return string $businessCep
     */
    public function getBusinessCep()
    {
        return $this->businessCep;
    }

    /**
     * Set expiresAt
     *
     * @param date $expiresAt
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * Get expiresAt
     *
     * @return date $expiresAt
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
    
    public function getTitleFormat(){
        return substr($this->title, 0, 50)."...";
    }
    
    /**
     * Set thumb
     *
     * @param string $thumb
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
    }

    /**
     * Get thumb
     *
     * @return string $thumb
     */
    public function getThumb()
    {
        return $this->thumb;
    }

    /**
     * Set coordinates
     *
     * @param Reurbano\CoreBundle\Document\Coordinates $coordinates
     */
    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;
    }

    /**
     * Get coordinates
     *
     * @return Reurbano\CoreBundle\Document\Coordinates $coordinates
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Set totalcoupons
     *
     * @param int $totalcoupons
     */
    public function setTotalcoupons($totalcoupons)
    {
        $this->totalcoupons = $totalcoupons;
    }

    /**
     * Get totalcoupons
     *
     * @return int $totalcoupons
     */
    public function getTotalcoupons()
    {
        return $this->totalcoupons;
    }

    /**
     * Set totalsell
     *
     * @param float $totalsell
     */
    public function setTotalsell($totalsell)
    {
        $this->totalsell = $totalsell;
    }

    /**
     * Get totalsell
     *
     * @return float $totalsell
     */
    public function getTotalsell()
    {
        return $this->totalsell;
    }
    
    /**
     * Set dateRegister
     *
     * @param date $dateRegister
     */
    public function setDateRegister($dateRegister)
    {
        $this->dateRegister = $dateRegister;
    }

    /**
     * Get dateRegister
     *
     * @return date $dateRegister
     */
    public function getDateRegister()
    {
        return $this->dateRegister;
    }
    public function populate($source){
        $methods = get_class_methods($source);
        foreach ($methods as $m) {
            if($m != 'getTitleFormat' && substr($m, 0, 3) == 'get') $this->{'set'.substr ($m, 3)}($source->$m());
        }
    }
}