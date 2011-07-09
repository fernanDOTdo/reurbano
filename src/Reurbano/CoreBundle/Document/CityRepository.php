<?php

namespace Reurbano\CoreBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class CityRepository extends BaseRepository
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

    /**
     * Retorna as cidades pelo parâmetro "special"
     *
     * @param string $id
     * @return bool
     */
    public function getBySpecial($special = true)
    {
        return $this->createQueryBuilder()
            ->field('special')->equals($special)
            ->getQuery()->execute();
    }
}