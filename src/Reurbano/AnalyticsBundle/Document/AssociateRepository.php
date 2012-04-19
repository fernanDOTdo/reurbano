<?php

/**
 * Reurbano/AnalyticsBundle/Document/AssociateRepository.php
 *
 * Reposit�rio de parceiro.
 *  
 * 
 * @copyright 2012 Reurano
 * @link http://www.reurano.com.br
 * @author Saulo Lima <sauloa@gubn.com.br>
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

class AssociateRepository extends BaseRepository
{

    /**
     * Pega todos ordenado por ORDER
     *
     * @return Associate ou null
     **/
    public function findAllByOrder()
    {
        return $this->findBy(array(), array('name'=>'asc'));
    }

    public function findBySlug($slug){
        return $this->findOneBy(array('slug' => $slug), array());
    }
    
    /**
     * Retorna os associados conforme os parâmetros de busca passados
     *
     * @param string $document, qual documento que será buscado na coleção de dados
     * @param string $value, qual valor documento que será buscado na coleção de dados
     * @return bool
     */
    public function getByFind($document, $value)
    {
    	return $this->createQueryBuilder()
    	->field($document)->equals($value)
    	->getQuery()->execute();
    }
    
    /**
     * Retorna todas as empresas ordenadas pelo parâmetro "name"
     *
     * @param string $sort, parâmetro de ordenação, default "name"
     * @param string $by, ordem "asc" ou "desc" para a ordenação, default, "asc"
     * @return bool
     */
    public function findAll($sort = 'name', $by = 'asc')
    {
        return $this->createQueryBuilder()
                ->sort($sort, $by)
                ->getQuery()->execute();
    }
    
    /**
     * Retorna uma empresas
     *
     * @param string $id, identificação da empresa
     * @return Associate ou null
     */
    public function hasId($id){
        return $this->findOneById($id);
    }
    
}