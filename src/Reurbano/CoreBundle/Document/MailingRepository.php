<?php

namespace Reurbano\CoreBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class MailingRepository extends BaseRepository
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
}