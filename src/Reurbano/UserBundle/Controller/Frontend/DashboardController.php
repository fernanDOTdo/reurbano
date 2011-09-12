<?php
namespace Reurbano\UserBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Controller que será o DashBoard do usúario.
 */

class DashboardController extends BaseController
{
    /**
     * Index do Dasboard
     * 
     * @Route("minha-conta/", name="user_dashboard_index")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function indexAction(){
        $title = 'Minha Conta';
        $tabs = array(
            'mydata' => 'Meus Dados',
            'myorders' => 'Minhas Compras',
            'mydeals' => 'Minhas Ofertas',
            'mysell' => 'Minhas Vendas',
            'mybalance' => 'Meu Financeiro',
        );
        $panes = array(
            'ReurbanoUserBundle:Widget\\MyData:dashboard',
            'ReurbanoOrderBundle:Widget\\MyOrders:dashboard',
            'ReurbanoDealBundle:Widget\\MyDeals:dashboard',
            'ReurbanoDealBundle:Widget\\MySell:dashboard',
            'ReurbanoDealBundle:Widget\\MyBalance:dashboard',
        );
        return array(
            'title' => $title,
            'tabs'  => $tabs,
            'panes' => $panes,
        );
    }
}