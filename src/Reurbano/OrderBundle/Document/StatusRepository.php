<?php

namespace Reurbano\OrderBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class StatusRepository extends BaseRepository
{

    /**
     * Pega todos ordenado por ORDER
     *
     * @return Status ou null
     **/
    public function findAllByOrder()
    {
        return $this->findBy(array(), array('order'=>'asc'));
    }
    public function findByName($name){
        return $this->findOneBy(array('name' => $name), array());
    }
}