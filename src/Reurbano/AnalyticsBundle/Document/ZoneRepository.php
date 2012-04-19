<?php
/**
 * Reurbano/AnalyticsBundle/Document/ZoneRepository.php
 *
 * Repositório de zona
 *  
 * 
 * @copyright 2011 Reurbano.
 * @link http://www.reurbano.com.br
 * @author Saulo Lima <saulo@gubn.com.br>
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

namespace Reurbano\AnalyticsBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class ZoneRepository extends BaseRepository
{

    /**
     * Retorna todos as zonas ordenados pelo parâmetro "name"
     *
     * @param string $id
     * @return bool
     */
    public function findAll()
    {
        return $this->createQueryBuilder()
                ->sort('name', 'asc')
                ->getQuery()->execute();
    }
    
    /**
     * Localiza uma zona à partir do 'slug'
     *
     * @param int $slug
     *
     * @return bool
     */
    public function findBySlug($slug){
    	return $this->createQueryBuilder()
    	->field('slug')->equals($slug)
    	->getQuery()
    	->getSingleResult();
    }
    
    /**
     * Atualiza os cliques da zona à partir do 'slug'
     * 
     * @param string $slug
     * 
     */
    public function updateClick($slug){
        return $this->createQueryBuilder()
                ->update()
                ->field('slug')->equals($slug)
                ->field('click')->inc(1)
                ->getQuery()
                ->execute();
    }
}