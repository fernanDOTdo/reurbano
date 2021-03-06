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
 * Reurbano/OrderBundle/Controller/Widget/MyBalanceController.php
 *
 * Widget para extrato de transações do usuário
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

namespace Reurbano\OrderBundle\Controller\Widget;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\OrderBundle\Document\Order;

/**
 * Controller que será os dados dentro da aba "Meu Financeiro" no minha-conta
 */
class MyBalanceController extends BaseController {

    /**
     * Dashboard do MyBalance
     * 
     * @Template()
     */
    public function dashboardAction() {
        $user = $this->getUser();
        $transactions = $this->mongo('ReurbanoOrderBundle:Escrow')->findByUser($user->getId());
        $checkouts = $this->mongo('ReurbanoOrderBundle:Checkout')->findByUser($user->getId());
        $inApproved = 0;
        $inPendent = 0;
        $outApproved = 0;
        $outPendent = 0;
        foreach ($transactions as $v) {
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
        if($checkouts){
            $ret['checkouts'] = $checkouts;
        }
        $ret['title'] = "Meu Financeiro";
        $ret['transactions'] = $transactions;
        $ret['inApproved'] = $inApproved;
        $ret['outApproved'] = $outApproved;
        $ret['outPendent'] = $outPendent;
        $ret['inPendent'] = $inPendent;
        $ret['totalCheckout'] = $inApproved - $outApproved - $outPendent;
        $ret['user'] = $user;
        
        return $ret;
    }

}