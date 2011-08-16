<?php
namespace Reurbano\DealBundle\Controller\Widget;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controller que será os dados dentro da aba "Minhas Vendas" no minha-conta
 */

class MySellController extends BaseController
{
    /**
     * Dashboard do MySell
     * 
     * @Template()
     */
    public function dashboardAction(){
        return array();
    }
}