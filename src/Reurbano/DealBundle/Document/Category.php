<?php

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa uma Categoria
 *
 * @author   Fernando Santos <o@fernan.do>
 *
 * @ODM\Document(
 *   collection="category",
 *   repositoryClass="Reurbano\DealBundle\Document\CategoryRepository"
 * )
 */
class Category
{
    /**
     * ID da Categoria
     *
     * @var string
     * @ODM\Id(strategy="none")
     */
    protected $id;

    /**
     * Nome da Categoria
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
     * Evento para setar o ID antes de inserir
     *
     * @var string
     * @ODM\prePersist
     * @ODM\preUpdate
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