<?php
namespace Reurbano\OrderBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Reurbano\OrderBundle\Document\Order;
use Reurbano\DealBundle\Document\Deal;

/**
 * Controller que cuidará das compras em Frontend
 * (fechar pedido, carrinho de compras, etc)
 */
class OrderController extends BaseController
{
    /**
     * Action para exibir o carrinho de compras
     * 
     * @Route("/comprar/{id}", name="order_order_index")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function indexAction(Deal $deal)
    {
        // Se a oferta está inativa ou a quantidade for menor que 1, manda para a home
        if($deal->getQuantity() < 1 || $deal->getActive() == false){
            return $this->redirectFlash($this->generateUrl('_home'), 'Esta oferta já foi vendida.', 'notice');
        }
        return array('deal'=>$deal);
    }
    /**
     * Action incluir oferta no carrinho de compras
     * 
     * @Route("/adicionar/{oferta}", name="order_order_inc")
     */
    public function incAction($oferta)
    {
        return array();
    }
    /**
     * Action remover oferta do carrinho de compras
     * 
     * @Route("/remover/{oferta}", name="order_order_del")
     */
    public function delAction($oferta)
    {
        return array();
    }
    /**
     * Action atualizar oferta no carrinho de compras (mudando quantidade)
     * 
     * @Route("/atualizar", name="order_order_upd")
     */
    public function updAction()
    {
        return array();
    }
    /**
     * Action fechar pedido
     * 
     * @Route("/pagar", name="order_order_finish")
     * @Template()
     */
    public function finishAction()
    {
        return array();
    }
}