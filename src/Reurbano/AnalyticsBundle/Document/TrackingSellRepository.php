<?php

/**
 * Reurbano/AnalyticsBundle/Document/TrackingSellRepository.php
 *
 * Repositório de tracking.
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

use Doctrine\ODM\MongoDB\Mapping\Annotations\Boolean;

use Mastop\SystemBundle\Document\BaseRepository;

class TrackingSellRepository extends BaseRepository
{

    /**
     * Pega todos ordenado por ORDER
     *
     * @return Associate ou null
     **/
    public function findAllByOrder()
    {
        return $this->findBy(array(), array('createdAt'=>'asc'));
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
     * Retorna todas ordenadas pelo parâmetro "name"
     *
     * @param string $sort, parâmetro de ordenação, default "createdAt"
     * @param string $by, ordem "asc" ou "desc" para a ordenação, default, "asc"
     * @return bool
     */
    public function findAll($sort = 'createdAt', $by = 'asc')
    {
        return $this->createQueryBuilder()
                ->sort($sort, $by)
                ->getQuery()->execute();
    }
    
    /**
     * Retorna um registro
     *
     * @param string $id, identificação da empresa
     * @return Associate ou null
     */
    public function hasId($id){
        return $this->findOneById($id);
    }
    
    /**
     * Atualiza os cliques do Tracking
     *
     * @param Associate $associate, parceiro
     * @param TrackingPreSell $trackingPreSell, trackingPreSel
     * 
     */
    public function updateClick($associate, $trackingPreSelly){
    	return $this->createQueryBuilder()
	    	->update()
	    	->field('associate._id')->equals(new \MongoId($associate->getId()))
	    	->field('trackingPreSell.$id')->equals(new \MongoId($trackingPreSelly->getId()))
	    	->field('updatedAt')->set(new \DateTime)
	    	->getQuery()->execute();
    }
        
    /**
     * Localiza um Tracking
     *
     * @param Associate $associate, parceiro
     * @param TrackingPreSell $trackingPreSell, trackingPreSell
     * @param Boolean $inCookie, contém ou não em cookie
     *
     * @return bool
     */
    public function findByTracking($associate, $trackingPreSell, $inCookie = false){
    	return $this->createQueryBuilder()
    	->field('associate._id')->equals(new \MongoId($associate->getId()))
    	->field('trackingPreSell.$id')->equals(new \MongoId($trackingPreSell->getId()))
    	->field('inCookie')->equals($inCookie) // contem ou não em cookie
    	->sort('createdAt', 'desc') // Primeiro registro em banco do click
    	->sort('updatedAt', 'asc')  // Ultima atualização que teve nesse registro
    	->getQuery()
    	->getSingleResult();
    }
}