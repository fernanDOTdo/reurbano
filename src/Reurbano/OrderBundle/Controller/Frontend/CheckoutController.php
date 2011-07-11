<?php
namespace Reurbano\OrderBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\OrderBundle\Document\Checkout;

/**
 * Controller que cuidará do Financeiro do vendedor.
 */

class CheckoutController extends BaseController
{
    /**
     * Action que lista as transações do vendedor
     * 
     * @Route("/", name="deal_checkout_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    /**
     * Action para visualizar uma transação do vendedor
     * 
     * @Route("/ver/{id}", name="deal_checkout_view")
     * @Template()
     */
    public function viewAction($id)
    {
        return array();
    }
    /**
     * Action para o vendedor comentar uma transação
     * 
     * @Route("/comentar/{id}", name="deal_checkout_comment")
     * @Template()
     */
    public function commentAction($id)
    {
        return array();
    }
    /**
     * Action que solicita um checkout pelo vendedor
     * 
     * @Route("/receber", name="deal_checkout_request")
     * @Template()
     */
    public function requestAction()
    {
        return array();
    }
}