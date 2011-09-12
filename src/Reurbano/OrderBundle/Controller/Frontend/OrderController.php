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
     * Action para redirecionamento pós pagamento
     * 
     * @Route("/retorno/{gateway}/{status}", name="order_order_return")
     * @Secure(roles="ROLE_USER")
     */
    public function returnAction($gateway, $status)
    {
        $gateway = 'Reurbano\OrderBundle\Payment\\'.$gateway;
        if(class_exists($gateway)){
            $orderId = $gateway::getOrderId($this->getRequest());
            $order = $this->mongo('ReurbanoOrderBundle:Order')->findOneById((int) $orderId);
            if($order && $this->get('security.context')->getToken()->getUser()->getId() == $order->getUser()->getId()){ // Pedido encontrado e pertence ao user atual
                $payment = new $gateway($order, $this->container);
                $ret = $payment->checkStatus();
                if($ret){
                    $pay = $order->getPayment();
                    $pay['data'] = $payment->getData();
                    $order->setPayment($pay);
                    $dm = $this->dm();
                    $dm->persist($order);
                    $dm->flush();
                    return $this->redirectFlash($this->generateUrl('_home'), $ret['msg'], $ret['type']);
                }
            }else{
                throw $this->createNotFoundException('Você não tem permissão para acessar esta página.'); 
            }
        }
        throw $this->createNotFoundException('Compra não encontrada'); 
    }
    /**
     * Action para POST de notificação de mudança de status
     * 
     * @Route("/status/{gateway}", name="order_order_status")
     */
    public function statusAction($gateway)
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
            $dealCom = $comReal;
        }
        $dealTotal = $dealPrice + $dealCom;
        $order->setTotal($dealTotal);
        
        // Forma de pagamento
        $gateway = 'Reurbano\OrderBundle\Payment\\'.$this->mastop()->param('order.all.gateway');
        $payment = new $gateway($order, $this->container);
        $pay['params'] = $payment->getParams();
        $order->setPayment($pay);
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
        // @TODO: Enviar e-mails com notificação de criação de pedido
        $ret['title'] = 'Compra '.$order->getId();
        $ret['content'] = $payment->process();
        return $ret;
    }
}