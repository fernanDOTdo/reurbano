<?php
/**
 * Reurbano/CoreBundle/Document/Content.php
 *
 * Representa uma página de conteúdo.
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

namespace Reurbano\CoreBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ODM\Document(
 *   collection="content",
 *   repositoryClass="Reurbano\CoreBundle\Document\ContentRepository"
 * )
 */
class Content
{
    /**
     * ID da página
     *
     * @var string
     * @ODM\Id
     */
    protected $id;

    /**
     * Título da Página
     *
     * @var string
     * @ODM\String
     */
    protected $title;

    /**
     * Campo Slug
     *
     * @var string
     * @Gedmo\Slug(fields={"title"})
     * @ODM\UniqueIndex
     * @ODM\String
     */
    protected $slug;
    
    /**
     * Conteúdo da Página
     *
     * @var string
     * @ODM\String
     */
    protected $content;
    
    /**
     * Título da página (SEO)
     *
     * @var string
     * @ODM\String
     */
    protected $seoTitle;
    
    /**
     * Keywords (SEO)
     *
     * @var string
     * @ODM\String
     */
    protected $seoKeywords;
    
    /**
     * Description (SEO)
     *
     * @var string
     * @ODM\String
     */
    protected $seoDescription;
    
    
    /**
     * Data de última atualização da oferta
     *
     * @var date
     * @ODM\Date
     */
    protected $updated;
    
    /**
     * Data de criação da oferta
     *
     * @var date
     * @ODM\Date
     * @ODM\Index(order="desc")
     */
    protected $created;
    
    
    /** @ODM\PrePersist */
    public function doPrePersist()
    {
        // Seta data de criação
        $this->setCreated(new \DateTime);
        // Seta SEO Title
        if($this->getSeoTitle() == ''){
            $this->setSeoTitle($this->getTitle());
        }
    }
    
    /** @ODM\PreUpdate */
    public function doPreUpdate()
    {
        // Seta data de atualização
        $this->setUpdated(new \DateTime);
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
    public function getTitle()
    {
        return $this->title;
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
     * Set content
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return string $content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set seoTitle
     *
     * @param string $seoTitle
     */
    public function setSeoTitle($seoTitle)
    {
        $this->seoTitle = $seoTitle;
    }

    /**
     * Get seoTitle
     *
     * @return string $seoTitle
     */
    public function getSeoTitle()
    {
        return $this->seoTitle;
    }

    /**
     * Set seoKeywords
     *
     * @param string $seoKeywords
     */
    public function setSeoKeywords($seoKeywords)
    {
        $this->seoKeywords = $seoKeywords;
    }

    /**
     * Get seoKeywords
     *
     * @return string $seoKeywords
     */
    public function getSeoKeywords()
    {
        return $this->seoKeywords;
    }

    /**
     * Set seoDescription
     *
     * @param string $seoDescription
     */
    public function setSeoDescription($seoDescription)
    {
        $this->seoDescription = $seoDescription;
    }

    /**
     * Get seoDescription
     *
     * @return string $seoDescription
     */
    public function getSeoDescription()
    {
        return $this->seoDescription;
    }

    /**
     * Set updated
     *
     * @param date $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Get updated
     *
     * @return date $updated
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set created
     *
     * @param date $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return date $created
     */
    public function getCreated()
    {
        return $this->created;
    }
}