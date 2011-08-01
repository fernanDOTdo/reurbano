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
     * Retorna as cidades pelo parâmetro "special"
     *
     * @param string $id
     * @return bool
     */
    public function getBySpecial($special = true)
    {
        return $this->createQueryBuilder()
            ->field('special')->equals($special)
            ->getQuery()->execute();
    }
    
    public function hasId($id){
        return $this->findOneById($id);
    }
    
}