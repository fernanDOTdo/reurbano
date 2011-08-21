<?php
namespace Reurbano\DealBundle\Controller\Widget;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\DealBundle\Document\Deal;

/**
 * Controller que será os dados dentro da aba "Minhas ofertas" no minha-conta
 */

class MyDealsController extends BaseController
{
    /**
     * Dashboard do MyDeals
     * 
     * @Template()
     */
    public function dashboardAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $deal = $this->mongo('ReurbanoDealBundle:Deal')->findByUser($user->getId());
        return array(
            'deal' => $deal,
        );
    }
}