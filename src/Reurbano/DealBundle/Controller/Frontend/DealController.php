<?php
namespace Reurbano\DealBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controller que cuidará das Ofertas em Frontend.
 */

class DealController extends BaseController
{
    /**
     * Action que lista as ofertas na cidade do usuário
     * 
     * @Route("/", name="deal_deal_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    /**
     * Action que exibe uma oferta
     * 
     * @Route("/{ofera}", name="deal_deal_show")
     * @Template()
     */
    public function showAction($oferta)
    {
        return array('oferta' => $ofera);
    }
}