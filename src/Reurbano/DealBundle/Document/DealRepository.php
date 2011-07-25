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
        return $this->createQueryBuilder('ReurbanoDealController:Deal')
                ->field('offer.$city.$id')->equals($city)
                ->getQuery()->execute();
        
    }
    

}