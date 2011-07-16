<?php
namespace Reurbano\OrderBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\OrderBundle\Document\Order;

/**
 * Controller que cuidará dos pedidos do comprador.
 */

class MyOrdersController extends BaseController
{
    /**
     * Action que lista os pedidos feitos pelo comprador
     * 
     * @Route("/", name="deal_myorders_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    /**
     * Action que vizualiza um pedido pelo comprador
     * 
     * @Route("/ver/{id}", name="deal_myorders_view")
     * @Template()
     */
    public function viewAction()
    {
        return array();
    }
    /**
     * Action que permite ao comprador enviar comentários
     * 
     * @Route("/comentar/{id}", name="deal_myorders_comment")
     * @Template()
     */
    public function commentAction()
    {
        return array();
    }
    /**
     * Action que permite ao comprador solicitar um reembolso
     * 
     * @Route("/reembolso/{id}", name="deal_myorders_refund")
     * @Template()
     */
    public function refundAction()
    {
        return array();
    }
    /**
     * Action que permite ao comprador comentar um reembolso
     * 
     * @Route("/reembolso/comentar/{id}", name="deal_myorders_refundcomment")
     * @Template()
     */
    public function refundCommentAction()
    {
        return array();
    }
}