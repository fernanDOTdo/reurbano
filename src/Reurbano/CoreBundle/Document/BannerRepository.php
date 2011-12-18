<?php
/**
 * Reurbano/CoreBundle/Document/BannerRepository.php
 *
 * Repositório dos banners
 *  
 * 
 * @copyright 2011 Mastop Internet Development.
 * @link http://www.mastop.com.br
 * @author Rafael Basquens <rafael@basquens.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */


namespace Reurbano\CoreBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class BannerRepository extends BaseRepository
{

    /**
     * Retorna todos os banners ordenados pelo parâmetro "order" e "name"
     *
     * @param string $id
     * @return bool
     */
    public function findAll()
    {
        return $this->createQueryBuilder()
                ->sort('order', 'asc')
                ->sort('name', 'asc')
                ->getQuery()->execute();
    }
    
    /**
     * Ativa / Desativa um banner à partir de um ID de Oferta
     */
    public function updateActiveByDeal($dealid, $active = false){
        return $this->createQueryBuilder()
                ->update()
                ->field('deal.id')->equals($dealid)
                ->field('active')->set($active)
                ->getQuery()
                ->execute();
    }
    
    /**
     * Deleta banner apartir da oferta
     */
    public function deleteByDeal($dealid){
        return $this->createQueryBuilder()
                ->field('deal.id')->equals($dealid)
                ->remove()
                ->getQuery()
                ->execute();
                
    }
    
    /**
     * Retorna um número específico de banners ativos por cidade
     *
     * @param int $limit quantidade de banners
     * @param string $city Id da cidade
     * @return object or null
     */
    public function findByCity($limit, $city){
        $banner = $this->createQueryBuilder()
                ->field('active')->equals(true);
        $banner->addOr($banner->expr()->field('city')->exists(false))->addOr($banner->expr()->field('city.$id')->equals(new \MongoId($city)));
        $banner->limit($limit);
        $banner->sort('order', 'asc');
        return $banner->getQuery()
                ->execute();
    }
}