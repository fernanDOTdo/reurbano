<?php

namespace Reurbano\AnalyticsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ODM\EmbeddedDocument
 * @ODM\Indexes({
 *   @ODM\Index(keys={"slug"="desc"}),
 *   @ODM\Index(keys={"createdAt"="desc"})
 * })
 */
class AssociateEmbed
{
    /**
     * ID da Parceito
     *
     * @var string
     * @ODM\Id
     */
    protected $id;

    /**
     * Nome Fantasia
     *
     * @var string
     * @ODM\String
     * @ODM\Index
     */
    protected $fancyName;

    /**
     * Campo Slug
     *
     * @var string
     * @ODM\String
     * @ODM\UniqueIndex
     */
    protected $slug;
    
    /**
     * Razão Social
     *
     * @var string
     * @ODM\String
     */
    protected $corporateName;
    
    /**
     * CNPJ da Empresa
     *
     * @var string
     * @ODM\String
     */
    protected $cnpj;

    /**
     * Nome para Contato
     *
     * @var string
     * @ODM\String
     */
    protected $contactName;
    
    /**
     * Telefone para Contato
     *
     * @var string
     * @ODM\String
     */
    protected $contactphone;
    
    /**
     * Endereço
     *
     * @var string
     * @ODM\String
     */
    protected $address;

    /**
     * Telefone
     *
     * @var string
     * @ODM\String
     */
    protected $telefone;
    
    /**
     * Email
     *
     * @var string
     * @ODM\String
     */
    protected $email;
    
    /**
     * Link para o site
     * 
     * @var string
     * @ODM\String
     */
    protected $site;
    
    /**
     * Logomarca
     *
     * @var array
     * @ODM\EmbedOne(targetDocument="Reurbano\AnalyticsBundle\Document\Archive")
     */
    protected $logo;
    
    /**
     * Data de criação
     *
     * @var date
     * @ODM\Date
     * @ODM\Index(order="desc")
     */
    protected $createdAt;
    
    /**
     * Observação
     * 
     * @var string
     * @ODM\String
     */
    protected $obs;
    
    /**
     * Selo para o parceiro
     *
     * @var string
     * @ODM\String
     */
    protected $selo;
    
    /**
     * Link para o seu feed XML de ofertas
     * 
     * @var string
     * @ODM\String
     */
    protected $feedXML;
    
    /**
     * Selo para o parceiro
     *
     * @var string
     * @ODM\Boolean
     */
    protected $aggregator;
    
    public function __construct()
    {
    }

    public function isAggregator()
    {
    	return ($this->getAggregator()) ? "Sim" : "N�o";
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
     * Set id
     *
     * @param id $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }    
    
    /**
     * Set selo
     *
     * @param string $selo
     */
    public function setSelo($selo)
    {
    	$this->selo = $selo;
    }
    
    /**
     * Get selo
     *
     * @return string $selo
     */
    public function getSelo()
    {
    	return $this->selo;
    }

    /**
     * Set fancyName
     *
     * @param string $fancyName
     */
    public function setFancyName($fancyName)
    {
        $this->fancyName = $fancyName;
    }

    /**
     * Get fancyName
     *
     * @return string $fancyName
     */
    public function getFancyName()
    {
        return $this->fancyName;
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
     * Set corporateName
     *
     * @param string $corporateName
     */
    public function setCorporateName($corporateName)
    {
        $this->corporateName = $corporateName;
    }

    /**
     * Get corporateName
     *
     * @return string $corporateName
     */
    public function getCorporateName()
    {
        return $this->corporateName;
    }

    /**
     * Set cnpj
     *
     * @param string $cnpj
     */
    public function setCnpj($cnpj)
    {
        $this->cnpj = $cnpj;
    }

    /**
     * Get cnpj
     *
     * @return string $cnpj
     */
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * Set contactName
     *
     * @param string $contactName
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;
    }

    /**
     * Get contactName
     *
     * @return string $contactName
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set address
     *
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Get address
     *
     * @return string $address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set telefone
     *
     * @param string $telefone
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
    }

    /**
     * Get telefone
     *
     * @return string $telefone
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set site
     *
     * @param string $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     * Get site
     *
     * @return string $site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set logo
     *
     * @param Reurbano\AnalyticsBundle\Document\Archive $logo
     */
    public function setLogo(\Reurbano\AnalyticsBundle\Document\Archive $logo)
    {
        $this->logo = $logo;
    }

    /**
     * Get logo
     *
     * @return Reurbano\AnalyticsBundle\Document\Archive $logo
     */
    public function getLogo()
    {
        return $this->logo;
    }
    
    /**
     * Retorna um array com os vouchers do Deal.
     * 
     * @return array
     */
    public function getAllLogo(){
        $ret = array();
        foreach($this->getLogo() as $k => $v){
            $ret[] = $v;
        }
        return $ret;
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

    /**
     * Set obs
     *
     * @param string $obs
     */
    public function setObs($obs)
    {
        $this->obs = $obs;
    }

    /**
     * Get obs
     *
     * @return string $obs
     */
    public function getObs()
    {
        return $this->obs;
    }

    /**
     * Set feedXML
     *
     * @param string $feedXML
     */
    public function setFeedXML($feedXML)
    {
        $this->feedXML = $feedXML;
    }

    /**
     * Get feedXML
     *
     * @return string $feedXML
     */
    public function getFeedXML()
    {
        return $this->feedXML;
    }

    /**
     * Set contactphone
     *
     * @param string $contactphone
     */
    public function setContactphone($contactphone)
    {
        $this->contactphone = $contactphone;
    }

    /**
     * Get contactphone
     *
     * @return string $contactphone
     */
    public function getContactphone()
    {
        return $this->contactphone;
    }
    
    /**
     * Set aggregator
     *
     * @param string $aggregator
     */
    public function setAggregator($aggregator)
    {
    	$this->aggregator = $aggregator;
    }
    
    /**
     * Get aggregator
     *
     * @return string $aggregator
     */
    public function getAggregator()
    {
    	return $this->aggregator;
    }
    
    public function populate($source){
    	$methods = get_class_methods($source);
    	foreach ($methods as $m) {
    		if($m != '__construct' && $m != 'getAllLogo' && $m != 'isAggregator' && substr($m, 0, 3) == 'get') $this->{'set'.substr ($m, 3)}($source->$m());
    	}
    }
}
