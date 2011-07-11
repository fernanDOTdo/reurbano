<?php

namespace Reurbano\OrderBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class CheckoutRepository extends BaseRepository
{

    /**
     * Pega todos ordenado por CREATED
     *
     * @return Checkout ou null
     **/
    public function findAllByCreated()
    {
        return $this->findBy(array(), array('created'=>'asc'));
    }

}