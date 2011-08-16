<?php
namespace Reurbano\UserBundle\Controller\Widget;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controller que será os dados dentro da aba "Meus Dados" no minha-conta
 */

class MyDataController extends BaseController
{
    /**
     * Dashboard do MyData
     * 
     * @template()
     */
    public function dashboardAction(){
        return array();
    }
}