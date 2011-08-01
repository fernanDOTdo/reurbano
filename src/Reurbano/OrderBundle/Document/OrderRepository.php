<?php

namespace Reurbano\OrderBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class OrderRepository extends BaseRepository
{

    /**
     * Pega todos ordenado por CREATED
     *
     * @return Order ou null
     **/
    public function findAllByCreated()
    {
        return $this->findBy(array(), array('created'=>'asc'));
    }

}