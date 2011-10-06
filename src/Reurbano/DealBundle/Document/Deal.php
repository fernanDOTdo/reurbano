<?php

/**
 * Reurbano/DealBundle/Document/Deal.php
 *
 * Representa uma oferta
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
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ODM\Document(
 *   collection="deal",
 *   repositoryClass="Reurbano\DealBundle\Document\DealRepository"
 * )
 * @ODM\Indexes({
 *   @ODM\Index(keys={"source.city.$id"="desc", "active"="asc", "quantity"="asc", "special"="desc"}),
 *   @ODM\Index(keys={"source.city.$id"="desc", "active"="asc", "quantity"="asc", "tags"="desc"}),
 *   @ODM\Index(keys={"source.city.$id"="desc", "active"="asc", "source.category.$id"="desc", "special"="desc"})
 * })
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
     * Usuário dono da Oferta
     *
     * @ODM\ReferenceOne(targetDocument="Reurbano\UserBundle\Document\User")
     */
    protected $user;
    
    /**
     * Source da Oferta
     *
     * @var array
     * @ODM\EmbedOne(targetDocument="Reurbano\DealBundle\Document\SourceEmbed")
     */
    protected $source;
    
    /**
     * Dados da Comissão
     *
     * @var array
     * @ODM\EmbedOne(targetDocument="Reurbano\DealBundle\Document\Comission")
     */
    protected $comission;
    
    /**
     * Preço com desconto da oferta
     *
     * @var float
     * @ODM\Float
     * @ODM\Index
     */
    protected $price;
    
    /**
     * Porcentagem do desconto (sobre o preço original da oferta)
     *
     * @var int
     * @ODM\Int
     * @ODM\Index(order="desc")
     */
    protected $discount;
    
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
    
    /**
     * Tags (usadas na busca do site)
     * @var array
     * @ODM\Collection
     */
    protected $tags = array();
    
    /**
     * Se a oferta está ativa ou não
     *
     * @var boolean
     * @ODM\Boolean
     * @ODM\Index(order="desc")
     */
    protected $active;
    
    /**
     * Label da Oferta (Título)
     *
     * @var string
     * @Gedmo\Sluggable
     * @ODM\String
     * @ODM\Index
     */
    protected $label;
    
    /**
     * Campo Slug
     *
     * @var string
     * @Gedmo\Slug
     * @ODM\UniqueIndex
     * @ODM\String
     */
    protected $slug;
    
    /**
     * Visualizações da oferta
     *
     * @var int
     * @ODM\Int
     */
    protected $views = 0;
    
    /**
     * Verifica se a oferta foi verificada
     *
     * @var boolean
     * @ODM\Boolean
     */
    protected $checked = false;
    
    /**
     * Oferta em destaque?
     *
     * @var boolean
     * @ODM\Boolean
     * @ODM\Index(order="desc")
     */
    protected $special;
    
    /**
     * Data de última atualização da oferta
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
     * @ODM\Index(order="desc")
     */
    protected $createdAt;
    
    public function __construct()
    {
        $this->voucher = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set user
     *
     * @param Reurbano\UserBundle\Document\User $user
     */
    public function setUser(\Reurbano\UserBundle\Document\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Reurbano\UserBundle\Document\User $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set source
     *
     * @param Reurbano\DealBundle\Document\SourceEmbed $source
     */
    public function setSource(\Reurbano\DealBundle\Document\SourceEmbed $source)
    {
        $this->source = $source;
    }

    /**
     * Get source
     *
     * @return Reurbano\DealBundle\Document\SourceEmbed $source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set comission
     *
     * @param Reurbano\DealBundle\Document\Comission $comission
     */
    public function setComission(\Reurbano\DealBundle\Document\Comission $comission)
    {
        $this->comission = $comission;
    }

    /**
     * Get comission
     *
     * @return Reurbano\DealBundle\Document\Comission $comission
     */
    public function getComission()
    {
        return $this->comission;
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
     * Set discount
     *
     * @param int $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * Get discount
     *
     * @return int $discount
     */
    public function getDiscount()
    {
        return $this->discount;
    }
    /**
     * Pega o tipo de desconto (para class no css)
     *
     * @return string
     */
    public function getDiscountType()
    {
        $discount = $this->discount;
        if($discount >= 85){
            return 'orange'; // desconto alto
        }  elseif ($discount >= 60) {
            return 'green'; // desconto médio
        }  elseif ($discount >= 46) {
            return 'blue'; // desconto baixo
        }else{
            return 'gray'; // desconto muito baixo
        }
    }

    /**
     * Set quantity
     *
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Get quantity
     *
     * @return int $quantity
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Add voucher
     *
     * @param Reurbano\DealBundle\Document\Voucher $voucher
     */
    public function addVoucher(\Reurbano\DealBundle\Document\Voucher $voucher)
    {
        $this->voucher[] = $voucher;
    }

    /**
     * Get voucher
     *
     * @return Doctrine\Common\Collections\Collection $voucher
     */
    public function getVoucher()
    {
        return $this->voucher;
    }

    /**
     * Set tags
     *
     * @param collection $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * Get tags
     *
     * @return collection $tags
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean $active
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get label
     *
     * @return string $label
     */
    public function getLabel($limit=0)
    {
        if($limit>0){
            return substr($this->label,0,$limit).(strlen($this->label)>$limit?"...":"");
        }else{
            return $this->label;
        }
    }

    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string $slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set views
     *
     * @param int $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * Get views
     *
     * @return int $views
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set checked
     *
     * @param boolean $checked
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;
    }

    /**
     * Get checked
     *
     * @return boolean $checked
     */
    public function getChecked()
    {
        return $this->checked;
    }

    /**
     * Set special
     *
     * @param boolean $special
     */
    public function setSpecial($special)
    {
        $this->special = $special;
    }

    /**
     * Get special
     *
     * @return boolean $special
     */
    public function getSpecial($format=false)
    {
        if($format){
        return $this->special==true?"Sim":"Não";
        }else{
            return $this->special;
        }
    }

    /**
     * Set updatedAt
     *
     * @param date $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return date $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdAt
     *
     * @param date $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return date $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    /** @ODM\PrePersist */
    public function doPrePersist()
    {
        // Seta data de criação
        $this->setCreatedAt(new \DateTime);
        // Seta o desconto
        $count1 = $this->getPrice() / $this->getSource()->getPrice();
        $count2 = $count1 * 100;
        $count3 = 100 - $count2;
        $count = floor($count3);
        $this->setDiscount($count);
        // Gera as tags
        $this->generateTags();
    }
    
    /** @ODM\PreUpdate */
    public function doPreUpdate()
    {
        // Seta data de atualização
        $this->setUpdatedAt(new \DateTime);
        // Reseta o desconto
        $count1 = $this->getPrice() / $this->getSource()->getPrice();
        $count2 = $count1 * 100;
        $count3 = 100 - $count2;
        $count = floor($count3);
        $this->setDiscount($count);
        // Gera as tags
        $this->generateTags();
    }
    protected function generateTags()
    {
        ###  ATENÇÃO!  No código abaixo há mágica de mana preto.  ###
        
        // Separa o label por espaços
        $tags = explode(' ', $this->getLabel());
        // Adiciona nas tags o mesmo label sem acento
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $this->getLabel());
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
        $tags2 = explode('-', $clean);
        // Junta o label sem acento com o label com acento
        $tags = array_merge($tags, $tags2);
        // Roda a função cleanTags nas tags
        $tags = array_map(array(&$this, "cleanTag"), $tags);
        // Remove itens repetidos das tags
        $tags = array_unique($tags);
        // Remove itens nulos das tags
        $tags = array_filter($tags, 'strlen');
        // Salva as tags
        $this->setTags($tags);
    }
    static function cleanTag($tag){
        # Remove os caracteres (),$%.*!+
        $v = preg_replace('/[(),$%.*!+]/', '', $tag);
        if(strlen($v) < 3 || is_numeric($v)){
            # Se $v for número ou tiver menos de 3 caracteres, retorna nulo
            return null;
        }else{
            # Retorna $v minúsculo
            return strtolower($v);
        }
    }
    
    /**
     * Retorna um array com os vouchers do Deal.
     * 
     * @return array
     */
    public function getAllVoucher(){
        $ret = array();
        foreach($this->getVoucher() as $k => $v){
            $ret[] = $v;
        }
        return $ret;
    }
}