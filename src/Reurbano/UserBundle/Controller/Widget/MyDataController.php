<?php
namespace Reurbano\UserBundle\Controller\Widget;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\UserBundle\Document\User;

/**
 * Controller que será os dados dentro da aba "Meus Dados" no minha-conta
 */

class MyDataController extends BaseController
{
    /**
     * Dashboard do MyData
     * 
     * @Template()
     */
    public function dashboardAction($userId = null){
        $user = $this->get('security.context')->getToken()->getUser();
        return array(
            'user' => $user,
        );
    }
}