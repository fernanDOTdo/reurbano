<?php
namespace Reurbano\OrderBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\OrderBundle\Document\Order;

/**
 * Controller que cuidará das compras em Frontend
 * (fechar pedido, carrinho de compras, etc)
 */

class OrderController extends BaseController
{
    /**
     * Action para exibir o carrinho de compras
     * 
     * @Route("/", name="deal_order_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    /**
     * Action incluir oferta no carrinho de compras
     * 
     * @Route("/adicionar/{oferta}", name="deal_order_inc")
     */
    public function incAction($oferta)
    {
        return array();
    }
    /**
     * Action remover oferta do carrinho de compras
     * 
     * @Route("/remover/{oferta}", name="deal_order_del")
     */
    public function delAction($oferta)
    {
        return array();
    }
    /**
     * Action atualizar oferta no carrinho de compras (mudando quantidade)
     * 
     * @Route("/atualizar", name="deal_order_upd")
     */
    public function updAction()
    {
        return array();
    }
    /**
     * Action fechar pedido
     * 
     * @Route("/fechar-pedido", name="deal_order_finish")
     * @Template()
     */
    public function finishAction()
    {
        return array();
    }
}