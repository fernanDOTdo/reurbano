<?php

namespace Reurbano\DealBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class CategoryRepository extends BaseRepository
{

    /**
     * Pega todos ordenado por ORDER
     *
     * @return Category ou null
     **/
    public function findAllByOrder()
    {
        return $this->findBy(array(), array('order'=>'asc'));
    }
    
    public function findBySlug($slug)
    {
        return $this->findOneBy(array('slug' => $slug), array());
    }

}