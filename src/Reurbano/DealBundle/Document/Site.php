<?php

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Description of Sites
 *
 * @author Rafael Basquens <rafael@basquens.com>
 * 
 * @ODM\Document(
 *   collection="site",
 *   repositoryClass="Reurbano\DealBundle\Document\SiteRepository"
 * )
 */
class Site
{   
    /**
     * ID do Site
     *
     * @var string
     * @ODM\Id(strategy="increment")
     */
    protected $id;
    
    /**
     * Nome do Site
     * 
     * @var string
     * @ODM\String
     */
    protected $name;
    
    /**
     * Dominio do site
     * 
     * @var string
     * @ODM\String
     */
    protected $url;
    
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
     * Get id
     *
     * @return custom_id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
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
     * Set filesize
     *
     * @param string $filesize
     */
    public function setFilesize($filesize)
    {
        $this->filesize = $filesize;
    }

    /**
     * Get filesize
     *
     * @return string $filesize
     */
    public function getFilesize()
    {
        return $this->filesize;
    }

    /**
     * Set path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get path
     *
     * @return string $path
     */
    public function getPath()
    {
        return $this->path;
    }
}
