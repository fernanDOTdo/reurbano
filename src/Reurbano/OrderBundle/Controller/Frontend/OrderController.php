<?php
namespace Reurbano\OrderBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Reurbano\OrderBundle\Document\Order;
use Reurbano\OrderBundle\Document\StatusLog;
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
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function finishAction()
    {
        $request = $this->getRequest();
        $post = $request->request;
        $deal = $this->mongo('ReurbanoDealBundle:Deal')->findOneById($post->get('deal'));
        $qtd = $post->get('qtd');
        if(!$deal){
            // Se a oferta não foi encontrada
            throw $this->createNotFoundException('Oferta não encontrada');
        }elseif($deal->getActive() == false){
            // Se a oferta está inativa
            return $this->redirectFlash($this->generateUrl('_home'), 'A oferta escolhida já foi vendida. Seu pedido não foi gerado.', 'error');
        }elseif($deal->getQuantity() < $qtd){
            // Se a quantidade for menor que a disponível
            return $this->redirectFlash($this->generateUrl('_home'), 'A quantidade escolhida não está mais disponível para esta oferta.', 'notice');
        }
        $newQtd = $deal->getQuantity() - $qtd;
        $dm = $this->dm();
        $user = $this->get('security.context')->getToken()->getUser();
        $order = $this->mongo('ReurbanoOrderBundle:Order')->createOrder();
        $status = $this->mongo('ReurbanoOrderBundle:Status')->findOneById(1);
        $statusLog = new StatusLog();
        $statusLog->setStatus($status);
        $order->setStatus($status);
        $order->addStatusLog($statusLog);
        $order->setUser($user);
        $order->setDeal($deal);
        $order->setQuantity($qtd);
        // Seta o valor total
        $comPercent = $deal->getComission()->getBuyerpercent();
        $comReal = $deal->getComission()->getBuyerreal();
        $dealPrice = $deal->getPrice() * $qtd;
        if($comPercent > 0){
            $dealCom = $dealPrice * ($comPercent / 100) + $comReal;
        }else{
            $dealCom = 0;
        }
        $dealTotal = $dealPrice + $dealCom;
        $order->setTotal($dealTotal);
        $dm->persist($order);
        $dm->flush();
        // Define a nova quantidade da Oferta
        $this->mongo('ReurbanoDealBundle:Deal')->updateQuantity($deal->getId(), $newQtd);
        if($newQtd == 0){
            // Se a quantidade restante da oferta for 0, desativa a oferta
            $this->mongo('ReurbanoDealBundle:Deal')->updateActive($deal->getId());
        }
        // Seta os vouchers
        $vouchers = $deal->getVoucher();
        $vCount = 0;
        foreach ($vouchers as $k => $voucher) {
            if($vCount < $qtd){
                if($voucher->getOrder() == null){
                    $voucher->setOrder($order);
                    $vCount++;
                }
            }
        }
        $dm->persist($deal);
        $dm->flush();
        exit(var_dump($post));
        return array();
    }
}