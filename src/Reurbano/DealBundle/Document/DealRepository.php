<?php

/**
 * Reurbano/DealBundle/Document/DealRepository.php
 *
 * Repositório de Ofertas
 *  
 * 
 * @copyright 2011 Mastop Internet Development.
 * @link http://www.mastop.com.br
 * @author Fernando Santos <o@fernan.do>
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

namespace Reurbano\DealBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class DealRepository extends BaseRepository
{
    /**
     * Adiciona +1 nos views
     */
    public function incViews($id){
        return $this->createQueryBuilder()
                ->update()
                ->field('id')->equals($id)
                ->field('views')->inc(1)
                ->getQuery()
                ->execute();
    }
    public function findOneByCityCat($city, $cat = null, $special = false, $sort = 'special', $order = 'desc', $notId = null){
        $deal = $this->createQueryBuilder()
                ->field('source.city.$id')->equals(new \MongoId($city))
                ->field('active')->equals(true);
        if($cat){
            $deal->field('source.category.$id')->equals(new \MongoId($cat));
        }
        if($special){
            $deal->field('special')->equals(true);
        }
        if($notId){
            $deal->field('id')->notEqual(new \MongoId($notId));
        }
        $deal->sort($sort, $order);
        return $deal->getQuery()
                ->getSingleResult();
    }
    /**
     * Pega todos ordenado por CREATED
     *
     * @return Deal ou null
     **/
    public function findAllByCreated()
    {
        return $this->findBy(array(), array('createdAt'=>'desc'));
    }
    
    public function findByCity($id){
        
        return $this->findBy(array('source.city.$id'=>new \MongoId($id)));
        
    }
    
    public function findByUser($id){
        
        return $this->findBy(array('user.$id'=>new \MongoId($id)));
        
    }
    
    public function findBySource($id){
        
        return $this->findBy(array('source.id'=>new \MongoId($id)));
        
    }
    
    public function findByCategory($category, $city = true){
        $city = $this->createQueryBuilder('ReurbanoDealControler:Category')
                ->field('slug')->equals($category)
                ->getQuery()->execute();
    }
    
    /*
     * Retorna o Slug do deal
     */
    public function findBySlug($slug){
        return $this->findOneBy(array('slug' => $slug), array());
    }
    
}