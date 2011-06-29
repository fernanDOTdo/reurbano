<?php

namespace Reurbano\CoreBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa uma cidade
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\Document(
 *   collection="city",
 *   repositoryClass="Reurbano\CoreBundle\Document\CityRepository"
 * )
 */
class City
{
    /**
     * ID da Cidade
     *
     * @var string
     * @ODM\Id(strategy="none")
     */
    protected $id;

    /**
     * Nome da Cidade
     *
     * @var string
     * @ODM\String
     */
    protected $name;

    /**
     * Ordem
     *
     * @var string
     * @ODM\Int
     */
    protected $order = 0;
    
    /**
     * Destaque
     *
     * @var string
     * @ODM\Boolean
     */
    protected $special = false;

    public function isSpecial()
    {
        return ($this->getSpecial()) ? "Sim" : "Não";
    }

    /**
     * Set id
     *
     * @param custom_id $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

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
    public function getSpecial()
    {
        return $this->special;
    }

    /**
     * Set order
     *
     * @param increment $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Get order
     *
     * @return increment $order
     */
    public function getOrder()
    {
        return $this->order;
    }
    /**
     * Evento para setar o ID antes de inserir
     *
     * @var string
     * @ODM\prePersist
     */
    public function prePersist(){
        $this->setId($this->slugify($this->getName()));
    }
    public function slugify($str, $replace=array(), $delimiter='-') {
        if (!empty($replace)) {
            $str = str_replace((array) $replace, ' ', $str);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }
}