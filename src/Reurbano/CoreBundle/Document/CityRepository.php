<?php

namespace Reurbano\CoreBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

class CityRepository extends DocumentRepository
{

    /**
     * Encontra uma cidade pelo seu ID
     *
     * @param string $id
     * @return City ou null
     **/
    public function findOneById($id)
    {
        return $this->find($id);
    }
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
     * Verifica se a cidade existe pelo ID
     *
     * @param string $id
     * @return bool
     */
    public function existsById($id)
    {
        return 1 === $this->createQueryBuilder()
            ->field('id')->equals($id)
            ->getQuery()->count();
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
    public function hello()
    {
        return "Hello!";
    }
}
