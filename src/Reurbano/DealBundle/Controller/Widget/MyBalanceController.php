<?php
namespace Reurbano\DealBundle\Controller\Widget;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controller que será os dados dentro da aba "Meus Dados" no minha-conta
 */

class MyBalanceController extends BaseController
{
    /**
     * Dashboard do MyBalance
     * 
     * @Template()
     */
    public function dashboardAction(){
        return array();
    }
}