<?php
namespace Reurbano\DealBundle\Controller\Widget;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controller que será os dados dentro da aba "Minhas ofertas" no minha-conta
 */

class MyDealsController extends BaseController
{
    /**
     * Dashboard do MyData
     * 
     * @Template()
     */
    public function dashboardAction(){
        return array();
    }
}