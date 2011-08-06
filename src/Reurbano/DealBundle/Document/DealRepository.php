<?php

namespace Reurbano\DealBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class DealRepository extends BaseRepository
{

    /**
     * Pega todos ordenado por CREATED
     *
     * @return Deal ou null
     **/
    public function findAllByCreated()
    {
        return $this->findBy(array(), array('created'=>'asc'));
    }
    
    public function findByCity($city){
        
        return $this->findBy(array('offer.city.$id'=>$city));
        
    }
    
    public function findBySource($source){
        
        return $this->findBy(array('offer.source.$id'=>$source));
        
    }
    
    public function findByCategory($category, $city = true){
        $city = $this->createQueryBuilder('ReurbanoDealControler:Category')
                ->field('slug')->equals($category)
                ->getQuery()->execute();
    }
    
    /*
     * Retorna o Slug do deal
     */
    public function findBySlug($slug){
        return $this->findOneBy(array('slug' => $slug), array());
    }
    
}