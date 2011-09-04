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
     * Retorna uma outra oferta do mesmo Source, ordenado por Destaque e Views
     *
     * @param object $deal
     * @return object or null
     */
    public function findRelated($deal){
        return $this->createQueryBuilder()
                ->field('id')->notEqual($deal->getId())
                ->field('active')->equals(true)
                ->field('quantity')->gt(0)
                ->field('source._id')->equals(new \MongoId($deal->getSource()->getId()))
                ->sort('special', 'desc')
                ->sort('views', 'desc')
                ->getQuery()
                ->getSingleResult();
    }
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
    /**
     * Atualiza a quantidade
     */
    public function updateQuantity($id, $qtd){
        return $this->createQueryBuilder()
                ->update()
                ->field('id')->equals($id)
                ->field('quantity')->set($qtd)
                ->getQuery()
                ->execute();
    }
    /**
     * Ativa / Desativa a Oferta
     */
    public function updateActive($id, $active = false){
        return $this->createQueryBuilder()
                ->update()
                ->field('id')->equals($id)
                ->field('active')->set($active)
                ->getQuery()
                ->execute();
    }
    
    
    /**
     * Retorna uma oferta de mesma Cidade e Categoria
     *
     * @param string $city Id da cidade
     * @param string $cat Id da categoria
     * @param bool $special Apenas destaques?
     * @param string $sort Campo para ordenação
     * @param string $order Asc ou Desc
     * @param string $notId Se definido, certifica-se de que a Oferta retornada é diferente do Id fornecido aqui
     * @return object or null
     */
    public function findOneByCityCat($city, $cat = null, $special = false, $sort = 'special', $order = 'desc', $notId = null){
        $deal = $this->createQueryBuilder()
                ->field('source.city.$id')->equals(new \MongoId($city))
                ->field('active')->equals(true)
                ->field('quantity')->gt(0);
        if($cat){
            $deal->field('source.category.$id')->equals(new \MongoId($cat));
        }
        if($special){
            $deal->field('special')->equals(true);
        }
        if($notId){
            $deal->field('id')->notEqual($notId);
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
    
    
    public function findByUser($id){
        
        return $this->findBy(array('user.id'=>$id));
        
    }
    
    /*
     * Retorna o Slug do deal
     */
    public function findBySlug($slug){
        return $this->findOneBy(array('slug' => $slug));
    }
    
}