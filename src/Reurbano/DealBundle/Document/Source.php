<?php
/**
 *                                              ,d                              
 *                                              88                              
 * 88,dPYba,,adPYba,   ,adPPYYba,  ,adPPYba,  MM88MMM  ,adPPYba,   8b,dPPYba,   
 * 88P'   "88"    "8a  ""     `Y8  I8[    ""    88    a8"     "8a  88P'    "8a  
 * 88      88      88  ,adPPPPP88   `"Y8ba,     88    8b       d8  88       d8  
 * 88      88      88  88,    ,88  aa    ]8I    88,   "8a,   ,a8"  88b,   ,a8"  
 * 88      88      88  `"8bbdP"Y8  `"YbbdP"'    "Y888  `"YbbdP"'   88`YbbdP"'   
 *                                                                 88           
 *                                                                 88           
 * 
 * Reurbano/DealBundle/Document/Source.php
 *
 * Document que representa uma oferta no banco de ofertas
 *  
 * 
 * @copyright 2011 Mastop Internet Development.
 * @link http://www.mastop.com.br
 * @author Fernando Santos <o@fernan.do>
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(
 *   collection="source",
 *   repositoryClass="Reurbano\DealBundle\Document\SourceRepository"
 * )
 * @ODM\Indexes({
 *   @ODM\Index(keys={"city.$id"="desc", "site.$id"="desc", "expiresAt"="asc"}),
 *   @ODM\Index(keys={"expiresAt"="asc", "totalsell"="desc"}),
 *   @ODM\Index(keys={"dateRegister"="desc"}),
 *   @ODM\Index(keys={"coordinates"="2d"})
 * })
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
     * Data do fim das negociações
     *
     * @var date
     * @ODM\Date
     */
    protected $expiresDeal;
    
    /** @ODM\EmbedOne(targetDocument="Reurbano\DealBundle\Document\Billing") */
    protected $billing;
    
    /** @ODM\PrePersist */
    public function doPrePersist()
    {
        // Seta data de criação
        $this->setDateRegister(new \DateTime);
    }

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
    public function setCoordinates(\Reurbano\CoreBundle\Document\Coordinates $coordinates)
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

    /**
     * Set expiresDeal
     *
     * @param date $expiresDeal
     */
    public function setExpiresDeal($expiresDeal)
    {
        $this->expiresDeal = $expiresDeal;
    }

    /**
     * Get expiresDeal
     *
     * @return date $expiresDeal
     */
    public function getExpiresDeal()
    {
        return $this->expiresDeal;
    }

    /**
     * Set billing
     *
     * @param Reurbano\DealBundle\Document\Billing $billing
     */
    public function setBilling(\Reurbano\DealBundle\Document\Billing $billing)
    {
        $this->billing = $billing;
    }

    /**
     * Get billing
     *
     * @return Reurbano\DealBundle\Document\Billing $billing
     */
    public function getBilling()
    {
        return $this->billing;
    }
}
