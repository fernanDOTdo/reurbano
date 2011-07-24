<?php

namespace Reurbano\DealBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class OfferRepository extends BaseRepository
{

    /**
     * Pega todos ordenado por CREATED
     *
     * @return Deal ou null
     **/
    public function findAll()
    {
        return $this->findBy(array(), array());
    }
    

}