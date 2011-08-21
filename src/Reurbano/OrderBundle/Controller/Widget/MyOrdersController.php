<?php
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
        $order = $this->mongo('ReurbanoOrderBundle:Order')->findByUser($user->getId());
        return array(
            'order' => $order,
        );
    }
}