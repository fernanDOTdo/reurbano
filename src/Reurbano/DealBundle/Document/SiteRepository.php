<?php

namespace Reurbano\DealBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class SiteRepository extends BaseRepository
{

    /**
     * Pega todos ordenado por ORDER
     *
     * @return Site ou null
     **/
    public function findAllByOrder()
    {
        return $this->findBy(array(), array('order'=>'asc'));
    }

}