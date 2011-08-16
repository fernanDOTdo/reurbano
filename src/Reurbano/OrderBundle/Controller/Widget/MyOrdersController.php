<?php
namespace Reurbano\OrderBundle\Controller\Widget;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
    public function dashboardAction(){
        return array();
    }
}