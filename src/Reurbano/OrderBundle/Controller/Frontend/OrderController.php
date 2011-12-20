<?php
/**
 * Reurbano/OrderBundle/Controller/Frontend/OrderController.php
 *
 * Controller para as ações de vendas
 *  
 * 
 * @copyright 2011 Mastop Internet Development.
 * @link http://www.mastop.com.br
 * @author Fernando Santos <o@fernan.do>
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace Reurbano\OrderBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Reurbano\OrderBundle\Document\Order;
use Reurbano\OrderBundle\Document\StatusLog;
use Reurbano\OrderBundle\Document\Escrow;
use Reurbano\DealBundle\Document\Deal;
use Symfony\Component\HttpFoundation\Response;

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
        // Não deixa comprar oferta sem aprovação do admin
        if($deal->getChecked() == false){
            return $this->redirectFlash($this->generateUrl('deal_deal_show', array('city' => $deal->getSource()->getCity()->getSlug(), 'category' => $deal->getSource()->getCategory()->getSlug(), 'slug' => $deal->getSlug())), 'Não é possível comprar uma oferta que está aguardando aprovação dos administradores.', 'notice');
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
            if($order && $this->getUser()->getId() == $order->getUser()->getId()){ // Pedido encontrado e pertence ao user atual
                $payment = new $gateway($order, $this->container);
                $ret = $payment->checkStatus();
                if($ret){
                    $pay = $order->getPayment();
                    $pay['data'] = $payment->getData();
                    $order->setPayment($pay);
                    $dm = $this->dm();
                    $dm->persist($order);
                    $dm->flush();
                    if($ret['type'] == 'ok' && $status == 'sucesso'){
                        // Se $ret['type'] for "ok" quer dizer que o pagamento está aprovado
                        $status = $this->mongo('ReurbanoOrderBundle:Status')->findByName('Aprovado');
                        $statusLog = new StatusLog();
                        $statusLog->setStatus($status);
                        $statusLog->setUser($order->getUser());

                        $order->setStatus($status);
                        $order->addStatusLog($statusLog);

                        $dm->persist($order);
                        $dm->flush();
                        $orderLinkBuyer = $this->generateUrl('order_myorders_view', array('id'=>$order->getId()), true);
                        $orderLinkSeller = $this->generateUrl('order_mysales_view', array('id'=>$order->getId()), true);
                        // Verifica se é para liberar o Voucher
                        $statusVoucher = explode(',', $this->get('mastop')->param('order.all.voucherstatus'));
                        if(count($statusVoucher) > 0 && in_array($status->getId(), $statusVoucher)){
                            $mail = $this->get('mastop.mailer');
                            $mail->to($order->getUser())
                                 ->subject('Cupom liberado')
                                 ->template('pedido_voucher',array(
                                        'title'  => 'Cupom Liberado',
                                        'user'  => $order->getUser(),
                                        'order' => $order,
                                        'orderLink' => $orderLinkBuyer,
                                    ));
                            foreach($order->getDealVoucher() as $k => $v){
                                $mail->attach($v->getPath() . "/" . $v->getFileName());
                            }
                            $mail->send();
                        }
                        // Envia e-mail para comprador
                        $mail = $this->get('mastop.mailer');
                        $mail->to($order->getUser())
                             ->subject('Status da Compra '.$order->getId().': '.$status->getName())
                             ->template('pedido_status_comprador',array(
                                    'title'  => 'Status: '.$status->getName(),
                                    'user'  => $order->getUser(),
                                    'order' => $order,
                                    'orderLink' => $orderLinkBuyer,
                                    'obs' => false,
                                ));
                        $mail->send();
                        // Envia e-mail para vendedor
                        $mail = $this->get('mastop.mailer');
                        $mail->to($order->getSeller())
                             ->subject('Status da Venda '.$order->getId().': '.$status->getName())
                             ->template('pedido_status_vendedor',array(
                                    'title'  => 'Status: '.$status->getName(),
                                    'user'  => $order->getSeller(),
                                    'order' => $order,
                                    'orderLink' => $orderLinkSeller,
                                    'obs' => false,
                                ));
                        $mail->send();
                        
                    }
                    return $this->redirectFlash($this->generateUrl('user_dashboard_index').'#myorders', $ret['msg'], $ret['type']);
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
        $gateway = 'Reurbano\OrderBundle\Payment\\'.$gateway;
        $mail = $this->get('mastop.mailer');
        $mail->notify('Debug: Mudança de Status', 'POST:<br /><br />'.  print_r($_POST, true).'<br /><br />GET:<br /><br />'.  print_r($_GET, true));
        if(class_exists($gateway)){
            $orderId = $gateway::getOrderId($this->getRequest());
            $order = $this->mongo('ReurbanoOrderBundle:Order')->findOneById((int) $orderId);
            if($order && $order->getStatus()){ // Pedido encontrado e não tá cancelado
                $user = $order->getUser();
                $seller = $order->getSeller();
                $payment = new $gateway($order, $this->container);
                $ret = $payment->checkStatus();
                if($ret){
                    $pay = $order->getPayment();
                    $pay['data'] = $payment->getData();
                    $order->setPayment($pay);
                    $dm = $this->dm();
                    $dm->persist($order);
                    $dm->flush();
                    if($ret['type'] == 'ok'){
                        // Se $ret['type'] for "ok" quer dizer que o pagamento está aprovado
                        $status = $this->mongo('ReurbanoOrderBundle:Status')->findByName('Aprovado');
                        if($status && $status->getId() == $order->getStatus()->getId()){
                            return new Response('OK'); // Status do pedido já está como aprovado
                        }
                        $statusLog = new StatusLog();
                        $statusLog->setStatus($status);
                        $statusLog->setUser($user);

                        $order->setStatus($status);
                        $order->addStatusLog($statusLog);

                        $dm->persist($order);
                        $dm->flush();
                        $orderLinkBuyer = $this->generateUrl('order_myorders_view', array('id'=>$order->getId()), true);
                        $orderLinkSeller = $this->generateUrl('order_mysales_view', array('id'=>$order->getId()), true);
                        $orderLinkAdmin = $this->generateUrl('admin_order_order_view', array('id'=>$order->getId()), true);
                        // Verifica se é para liberar o Voucher
                        $statusVoucher = explode(',', $this->get('mastop')->param('order.all.voucherstatus'));
                        if(count($statusVoucher) > 0 && in_array($status->getId(), $statusVoucher)){
                            $mail = $this->get('mastop.mailer');
                            $mail->to($user)
                                 ->subject('Cupom liberado')
                                 ->template('pedido_voucher',array(
                                        'title'  => 'Cupom Liberado',
                                        'user'  => $user,
                                        'order' => $order,
                                        'orderLink' => $orderLinkBuyer,
                                    ));
                            foreach($order->getDealVoucher() as $k => $v){
                                $mail->attach($v->getPath() . "/" . $v->getFileName());
                            }
                            $mail->send();
                        }else{
                            // Debug
                            $mail->notify('Debug: Status não encontrado', '$statusVoucher:<br /><br />'.  print_r($statusVoucher, true).'<br /><br />Mastop Param:<br /><br />'.  print_r($this->get('mastop')->param('order.all.voucherstatus'), true));
                        }
                        // Envia e-mail para comprador
                        $mail = $this->get('mastop.mailer');
                        $mail->to($user)
                             ->subject('Status da Compra '.$order->getId().': '.$status->getName())
                             ->template('pedido_status_comprador',array(
                                    'title'  => 'Status: '.$status->getName(),
                                    'user'  => $user,
                                    'order' => $order,
                                    'orderLink' => $orderLinkBuyer,
                                    'obs' => false,
                                ));
                        $mail->send();
                        $mail->to($order->getSeller())
                             ->subject('Sua oferta foi vendida')
                             ->template('oferta_novavenda_vendedor', array('user' => $seller, 'order' => $order, 'orderLink' => $orderLinkSeller, 'title' => 'Venda #'.$order->getId()))
                             ->send();
                        // Envia e-mail para vendedor
                        //$mail = $this->get('mastop.mailer');
                        //$mail->to($seller)
                        //     ->subject('Status da Venda '.$order->getId().': '.$status->getName())
                        //     ->template('pedido_status_vendedor',array(
                        //            'title'  => 'Status: '.$status->getName(),
                        //            'user'  => $seller,
                        //            'order' => $order,
                        //            'orderLink' => $orderLinkSeller,
                        //            'obs' => false,
                        //        ));
                        //$mail->send();
                        $mail->notify('Aviso de pagamento aprovado', 'O pagamento do usuário '.$user->getName().' ('.$user->getEmail().') no pedido pedido '.$order->getId().' foi aprovado.<br /><br /><a href="'.$orderLinkAdmin.'">'.$orderLinkAdmin.'</a>');
                    }elseif($ret['type'] == 'error'){
                        $orderLinkAdmin = $this->generateUrl('admin_order_order_view', array('id'=>$order->getId()), true);
                        $orderLinkBuyer = $this->generateUrl('order_myorders_view', array('id'=>$order->getId()), true);
                        // Pagamento foi cancelado / recusado
                        // Envia e-mail para comprador
                        $mail = $this->get('mastop.mailer');
                        $mail->to($order->getUser())
                             ->subject('Seu Pagamento foi Recusado'.$order->getId())
                             ->template('pedido_pagamento_recusado',array(
                                    'title'  => 'Pagamento Recusado',
                                    'user'  => $order->getUser(),
                                    'order' => $order,
                                    'orderLink' => $orderLinkBuyer,
                                    'obs' => false,
                                ));
                        $mail->send();
                        $mail->notify('Aviso de pagamento recusado', 'O pagamento do usuário '.$user->getName().' ('.$user->getEmail().') no pedido pedido '.$order->getId().' foi recusado.<br /><br /><a href="'.$orderLinkAdmin.'">'.$orderLinkAdmin.'</a>');
                    }
                     return new Response('OK');
                }
            }
            
        }
        return new Response('OK'); 
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
        }elseif($deal->getChecked() == false){
            // Se a oferta não foi aprovada pelos administradores
            return $this->redirectFlash($this->generateUrl('_home'), 'A oferta escolhida ainda não foi aprovada pelos administradores.', 'error');
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
        $order->setSeller($deal->getUser());
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
        $pay['gateway'] = $this->mastop()->param('order.all.gateway');
        $order->setPayment($pay);
        $dm->persist($order);
        $dm->flush();
        // Define a nova quantidade da Oferta
        $this->mongo('ReurbanoDealBundle:Deal')->updateQuantity($deal->getId(), $newQtd);
        if($newQtd == 0){
            // Se a quantidade restante da oferta for 0, desativa a oferta
            $this->mongo('ReurbanoDealBundle:Deal')->updateActive($deal->getId());
            // ... e desativa o banner desta oferta
            $this->mongo('ReurbanoCoreBundle:Banner')->updateActiveByDeal($deal->getId());
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
        // Cria o extrato do vendedor
        $escrow = new Escrow();
        $escrow->setUser($order->getSeller());
        $escrow->setData($order);
        $escrow->setValue($order->getTotal(true));
        $escrow->setObs("Venda #".$order->getId());
        $dm->persist($escrow);
        $dm->flush();
        // Enviar e-mails para comprador, vendedor e notificação administrativa de criação de pedido
        $orderLinkBuyer = $this->generateUrl('order_myorders_view', array('id'=>$order->getId()), true);
        $orderLinkSeller = $this->generateUrl('order_mysales_view', array('id'=>$order->getId()), true);
        $orderLinkAdmin = $this->generateUrl('admin_order_order_view', array('id'=>$order->getId()), true);
        $mail = $this->get('mastop.mailer');
        $mail->to($order->getUser())
             ->subject('Sua compra foi criada ')
             ->template('oferta_novavenda_comprador', array('user' => $order->getUser(), 'order' => $order, 'orderLink' => $orderLinkBuyer, 'title' => 'Compra #'.$order->getId()))
             ->send();
        $mail->newMessage();
        // Linhas abaixo foram escrotamente comentadas para o vendedor não ser notificado quando houver nova venda. Pode isso, Arnaldo? 
        //$mail->to($order->getSeller())
        //     ->subject('Sua oferta foi vendida')
        //     ->template('oferta_novavenda_vendedor', array('user' => $order->getSeller(), 'order' => $order, 'orderLink' => $orderLinkSeller, 'title' => 'Venda #'.$order->getId()))
        //     ->send();
        $mail->notify('Aviso de nova venda', 'O usuário '.$user->getName().' ('.$user->getEmail().') comprou <b>'.$qtd.'x - '.$deal->getLabel().'</b> do usuário '.$order->getSeller()->getName().' ('.$order->getSeller()->getEmail().').<br /><br />
            Cód. Venda: '.$order->getId().': <br />
            Total: R$ '.  number_format($order->getTotal(), 2, ',', '').'<br />
            Link para a venda: <a href="'.$orderLinkAdmin.'">'.$orderLinkAdmin.'</a><br />');
        $ret['title'] = 'Compra '.$order->getId();
        $ret['content'] = $payment->process();
        $ret['order'] = $order;
        return $ret;
    }
}