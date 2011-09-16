<?php
/**
 * Reurbano/OrderBundle/Controller/Widget/MyOrdersController.php
 *
 * Widget das compras do usuário
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
 * Controller que será os dados dentro da aba "Minhas Compras" no minha-conta
 */

class MyOrdersController extends BaseController
{
    /**
     * Dashboard do MyOrders
     * 
     * @Template()
     */
    public function dashboardAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $orders = $this->mongo('ReurbanoOrderBundle:Order')->findByUser($user->getId());
        $lastOrder = $this->mongo('ReurbanoOrderBundle:Order')->findLastOrder($user->getId());
        $pay = $lastOrder->getPayment();
        if(!isset ($pay['data'])){
            $gateway = 'Reurbano\OrderBundle\Payment\\'.$pay['gateway'];
            $payment = new $gateway($lastOrder, $this->container);
            $payButton = $payment->renderPaymentButton();
        }else{
            $payButton = null;
        }
        return array(
            'user' => $user,
            'lastOrder' => $lastOrder,
            'payButton' => $payButton,
            'orders' => $orders,
        );
    }
}