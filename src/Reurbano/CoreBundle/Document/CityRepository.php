<?php

namespace Reurbano\CoreBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class CityRepository extends BaseRepository
{

    /**
     * Pega todos ordenado por ORDER
     *
     * @return City ou null
     **/
    public function findAllByOrder()
    {
        return $this->findBy(array(), array('order'=>'asc'));
    }

    public function findBySlug($slug){
        return $this->findOneBy(array('slug' => $slug), array());
    }
    
    /**
     * Retorna todas as cidades ordenadas pelo parâmetro "special", "order" e "name"
     *
     * @param string $id
     * @return bool
     */
    public function findAll()
    {
        return $this->createQueryBuilder()
                ->sort('special', 'desc')
                ->sort('order', 'asc')
                ->sort('name', 'asc')
                ->getQuery()->execute();
    }
    
    public function hasId($id){
        return $this->findOneById($id);
    }
    
}