<?php
namespace Reurbano\OrderBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\OrderBundle\Document\Order;

/**
 * Controller que cuidará das vendas do vendedor.
 */

class MySalesController extends BaseController
{
    /**
     * Action que lista as vendas do vendedor
     * 
     * @Route("/", name="deal_mysales_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    /**
     * Action que permite ao vendedor visualizar uma venda
     * 
     * @Route("/ver/{id}", name="deal_mysales_view")
     * @Template()
     */
    public function viewAction($id)
    {
        return array();
    }
    /**
     * Action que permite ao vendedor comentar uma venda
     * 
     * @Route("/comentar/{id}", name="deal_mysales_comment")
     * @Template()
     */
    public function commentAction($id)
    {
        return array();
    }
    /**
     * Action que permite ao vendedor comentar um reembolso solicitado pelo comprador
     * 
     * @Route("/reembolso/comentar/{id}", name="deal_mysales_refundcomment")
     * @Template()
     */
    public function refundCommentAction($id)
    {
        return array();
    }
}