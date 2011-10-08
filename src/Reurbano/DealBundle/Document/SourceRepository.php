<?php

namespace Reurbano\DealBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class SourceRepository extends BaseRepository
{

    /**
     * Pega todos ordenado por CREATED
     *
     * @return Deal ou null
     **/
    public function findAllByCreated()
    {
        return $this->findBy(array(), array('dateRegister' => 'DESC'), 3000);
    }
    
    public function findByCategoryCity($category, $city){
        
        return $this->findBy(array('category.$id'=> new \MongoId($category), 'city.$id'=> new \MongoId($city)), array());
    }
    
}