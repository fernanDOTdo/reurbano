<?php
/**
 *                                              ,d                              
 *                                              88                              
 * 88,dPYba,,adPYba,   ,adPPYYba,  ,adPPYba,  MM88MMM  ,adPPYba,   8b,dPPYba,   
 * 88P'   "88"    "8a  ""     `Y8  I8[    ""    88    a8"     "8a  88P'    "8a  
 * 88      88      88  ,adPPPPP88   `"Y8ba,     88    8b       d8  88       d8  
 * 88      88      88  88,    ,88  aa    ]8I    88,   "8a,   ,a8"  88b,   ,a8"  
 * 88      88      88  `"8bbdP"Y8  `"YbbdP"'    "Y888  `"YbbdP"'   88`YbbdP"'   
 *                                                                 88           
 *                                                                 88           
 * 
 * Reurbano/OrderBundle/Document/EscrowRepository.php
 *
 * Repositório de extrato
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
use Reurbano\OrderBundle\Document\Escrow;

class EscrowRepository extends BaseRepository {

    public function findByUser($id) {
        return $this->findBy(array('user.id' => $id), array('created' => 'desc'));
    }
    public function totalCheckoutByUser($id){
        $escrows = $this->findByUser($id);
        $inApproved = 0;
        $inPendent = 0;
        $outApproved = 0;
        $outPendent = 0;
        foreach ($escrows as $v) {
            if($v->getApproved()){
                if($v->getMoneyIn()){
                    $inApproved += $v->getValue();
                    continue;
                }
                $outApproved += $v->getValue();
                continue;
            }
            ($v->getMoneyIn()) ? $inPendent += $v->getValue() : $outPendent += $v->getValue();
        }
        $totalCheckout = $inApproved - $outApproved - $outPendent;
        return ($totalCheckout > 0) ? $totalCheckout : 0;        
    }


    public function cancelOrder($order){
        $this->createQueryBuilder()
                ->update()
                ->field('data.$id')->equals($order->getId())
                ->field('data.type')->equals('order')
                ->field('approved')->set(true)
                ->getQuery()
                ->execute();
        $escrow = new Escrow();
        $escrow->setUser($order->getSeller());
        $escrow->setData($order);
        $escrow->setValue($order->getTotal(true));
        $escrow->setApproved(true);
        $escrow->setMoneyIn(false);
        $escrow->setObs("Cancelamento da Venda #".$order->getId());
        $this->getDocumentManager()->persist($escrow);
        $this->getDocumentManager()->flush();
    }
    public function releaseOrder($order){
        return $this->createQueryBuilder()
                ->update()
                ->field('data.$id')->equals($order->getId())
                ->field('data.type')->equals('order')
                ->field('approved')->set(true)
                ->getQuery()
                ->execute();
    }
    public function releaseCheckout($checkout){
        return $this->createQueryBuilder()
                ->update()
                ->field('data.$id')->equals($checkout->getId())
                ->field('data.type')->equals('checkout')
                ->field('approved')->set(true)
                ->getQuery()
                ->execute();
    }

}