<?php

/**
 * Reurbano/OrderBundle/Document/OrderRepository.php
 *
 * Repositório de compras
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

namespace Reurbano\OrderBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class OrderRepository extends BaseRepository {

    /**
     * Cancela o pedido, deletando o status
     */
    public function cancelOrder($id) {
        return $this->createQueryBuilder()
                ->update()
                ->field('id')->equals($id)
                ->field('status')->unsetField()
                ->getQuery()
                ->execute();
    }
    
    /**
     *
     * @param type $user
     * @param type $seller
     * @return Reurbano\OrderBundle\Document\Order $order
     */
    public function findLastOrder($user = null, $seller = null) {
        $query = $this->createQueryBuilder();
        if ($user) {
            $query->field('user.id')->equals($user);
        }else{
            $query->field('seller.id')->equals($seller);
        }
        return $query->sort('created', 'desc')
                        ->getQuery()
                        ->getSingleResult();
    }

    /**
     * Pega todos ordenado por CREATED
     *
     * @return Order ou null
     * */
    public function findAllByCreated() {
        return $this->findBy(array(), array('created' => 'desc'));
    }

    /**
     * Cria um novo pedido e setar um id para o pedido
     * 
     * @return Object order
     */
    public function createOrder() {
        $control = true;
        $count = 0;
        while ($control) {
            if ($count < 3) {
                $id = round(abs(crc32(uniqid(rand(), true)) / 1000));
                if (!$this->hasId($id)) {
                    $control = false;
                } else {
                    $count++;
                }
            } else {
                $id = round(abs(crc32(uniqid(rand(), true))));
                while ($this->hasId($id)) {
                    $id = round(abs(crc32(uniqid(rand(), true))));
                }
                $control = false;
            }
        }
        $order = new order();
        $order->setId($id);
        return $order;
    }

    public function findByUser($id) {
        return $this->findBy(array('user.id' => $id), array('created' => 'desc'));
    }

    public function findBySeller($id) {
        return $this->findBy(array('seller.id' => $id), array('created' => 'desc'));
    }

}