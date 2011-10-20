<?php
/**
 *                                              ,d                              
 *                                              88                              
 * 88,dPYba,,adPYba,   ,adPPYYba,  ,adPPYba,  MM88MMM  ,adPPYba,   8b,dPPYba,   
 * 88P'   "88"    "8a  ""     `Y8  I8[    ""    88    a8"     "8a  88P'    "8a  
 * 88      88      88  ,adPPPPP88   `"Y8ba,     88    8b       d8  88       d8  
 * 88      88      88  88,    ,88  aa    ]8I    88,   "8a,   ,a8"  88b,   ,a8"  
 * 88      88      88  `"8bbdP"Y8  `"YbbdP"'    "Y888  `"YbbdP"'   88`YbbdP"'   
 *                                                                 88           
 *                                                                 88           
 * 
 * Reurbano/OrderBundle/Controller/Frontend/MyOrdersController.php
 *
 * Controller que cuidará das compras do usuário.
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
use Reurbano\OrderBundle\Document\Comment;
use Reurbano\OrderBundle\Document\Refund;
use Reurbano\OrderBundle\Form\Frontend\RefundType;
use Reurbano\OrderBundle\Document\StatusLog;

class MyOrdersController extends BaseController
{
    /**
     * Action que lista os pedidos feitos pelo comprador
     * 
     * @Route("/", name="order_myorders_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    /**
     * Action que vizualiza um pedido pelo comprador
     * 
     * @Route("/ver/{id}", name="order_myorders_view")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function viewAction(Order $order)
    {
        $user = $this->getUser();
        if($user->getId() != $order->getUser()->getId()){
            return $this->redirectFlash($this->generateUrl('user_dashboard_index'), 'Você não tem permissão para acessar esta página.', 'error');
        }
        $pay = $order->getPayment();
        $payButton = null;
        $gateway = 'Reurbano\OrderBundle\Payment\\' . $pay['gateway'];
        $payment = new $gateway($order, $this->container);
        if (!isset($pay['data']) || $order->getStatus()->getId() == 1) {
            $payButton = $payment->renderPaymentButton();
        }
        // Vouchers
        $ret['voucher'] = false;
        $statusVoucher = array_merge(explode(',', $this->get('mastop')->param('order.all.voucherstatus')), explode(',', $this->get('mastop')->param('order.all.releasestatus')));
        if($order->getStatus()){
            if(count($statusVoucher) > 0){
                if(in_array($order->getStatus()->getId(), $statusVoucher)){
                    $ret['voucher'] = $order->getDealVoucher();
                }
            }
        }
        // Reembolso
        $refundStatus = explode(',', $this->get('mastop')->param('order.all.refundstatus'));
        $ret['refund'] = false;
        $ret['refundButton'] = null;
        if($this->mongo('ReurbanoOrderBundle:Refund')->hasId($order->getId())){
            $ret['refund'] = $this->mongo('ReurbanoOrderBundle:Refund')->findOneById($order->getId());
        }else{
            if($order->getStatus()){
                if(count($refundStatus) > 0 && in_array($order->getStatus()->getId(), $refundStatus)){
                    $ret['refundButton'] = '<a href="'.$this->generateUrl('order_myorders_refund', array('id'=>$order->getId())).'" class="button big red">Solicitar Reembolso</a>';
                }
            }
        }
        $ret['title'] = 'Informações da Compra';
        $ret['order'] = $order;
        $ret['payment'] = $payment;
        $ret['payButton'] = $payButton;
        return $ret;
    }
    /**
     * Action que permite ao comprador enviar comentários
     * 
     * @Route("/comentar/{id}", name="order_myorders_comment", requirements={"_method" = "POST"})
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function commentAction(Order $order)
    {
        $user = $this->getUser();
        if($user->getId() != $order->getUser()->getId()){
            return $this->redirectFlash($this->generateUrl('user_dashboard_index'), 'Você não tem permissão para acessar esta página.', 'error');
        }
        if($this->getRequest()->request->get('message') == null){
            return $this->redirectFlash($this->generateUrl('order_myorders_view', array('id' => $order->getId())), 'Digite uma mensagem para enviar um comentário no pedido.', 'error');
        }
        $dm = $this->dm();
        $comment = new Comment();
        $comment->setUser($user);
        $comment->setMessage($this->getRequest()->request->get('message'));
        $order->addComments($comment);
        $dm->persist($order);
        $dm->flush();
        // Enviar e-mail de notificação de novo comentário
        $orderLink = $this->generateUrl('order_mysales_view', array('id'=>$order->getId()), true);
        $orderLinkAdmin = $this->generateUrl('admin_order_order_view', array('id'=>$order->getId()), true);
        $mail = $this->get('mastop.mailer');
        $mail->to($order->getSeller())
             ->subject('Novo comentário no pedido '.$order->getId())
             ->template('oferta_novocomentario_vendedor', array('user' => $order->getSeller(), 'comment' => $comment->getMessage(), 'order' => $order, 'orderLink' => $orderLink, 'title' => 'Novo Comentário'))
             ->send();
            $mail->notify('Aviso de novo comentário', 'O usuário '.$user->getName().' ('.$user->getEmail().') enviou o seguinte comentário no pedido '.$order->getId().': <br />'.nl2br($comment->getMessage()).'<br /><a href="'.$orderLinkAdmin.'">'.$orderLinkAdmin.'</a>');
        return $this->redirectFlash($this->generateUrl('order_myorders_view', array('id' => $order->getId())), 'Comentário adicionado no pedido '.$order->getId());
    }
    /**
     * Action que permite ao comprador solicitar um reembolso
     * 
     * @Route("/reembolso/{id}", name="order_myorders_refund")
     * @Template()
     */
    public function refundAction(Order $order)
    {
        $user = $this->getUser();
        if($user->getId() != $order->getUser()->getId()){
            return $this->redirectFlash($this->generateUrl('user_dashboard_index'), 'Você não tem permissão para acessar esta página.', 'error');
        }
        // Verifica se este pedido pode ter Reembolso
        $refundStatus = explode(',', $this->get('mastop')->param('order.all.refundstatus'));
        if($this->mongo('ReurbanoOrderBundle:Refund')->hasId($order->getId())){
            return $this->redirectFlash($this->generateUrl('order_myorders_view', array('id' => $order->getId())), 'Esta compra já tem uma solicitação de reembolso.', 'error');
        }elseif(!$order->getStatus() || !in_array($order->getStatus()->getId(), $refundStatus)){
            return $this->redirectFlash($this->generateUrl('order_myorders_view', array('id' => $order->getId())), 'Esta compra não está apta a solicitação de reembolso.', 'error');
        }
        // Verifica se o usuário preencheu as informações bancárias
        if($user->getBankData() == ''){
            return $this->redirectFlash($this->generateUrl('user_user_bank'), 'Para solicitar reembolso você precisa definir suas informações bancárias.', 'error');
        }
        $dm = $this->dm();
        $request = $this->get('request');
        $form = $this->createForm(new RefundType());
        if($request->getMethod() == 'POST'){
            // Salva a solicitação de Reembolso
            $data = $request->request->get($form->getName());
            $refund = new Refund();
            $refund->setId($order->getId());
            $refund->setUser($user);
            $refund->setReason($data['reason']);
            $refund->setOrder($order);
            $dm->persist($refund);
            $dm->flush();
            // Coloca o pedido como Status = 5 = Em Análise
            $status = $this->mongo('ReurbanoOrderBundle:Status')->find(5);
            $statusLog = new StatusLog();
            $statusLog->setStatus($status);
            $statusLog->setObs('Solicitação de reembolso pelo comprador: '.$refund->getReason());
            $statusLog->setUser($user);
            
            $order->setStatus($status);
            $order->addStatusLog($statusLog);
            
            $dm->persist($order);
            $dm->flush();
            // Envia e-mail para comprador
            $orderLink = $this->generateUrl('order_myorders_view', array('id'=>$order->getId()), true);
            $mail = $this->get('mastop.mailer');
            $mail->to($user)
             ->subject('Recebemos sua solicitação de reembolso')
             ->template('pedido_reembolso_solicitado', array('user' => $user, 'order' => $order, 'orderLink' => $orderLink, 'title' => 'Solicitação de Reembolso'))
             ->send();
            
            $orderLinkAdmin = $this->generateUrl('admin_order_refund_view', array('id'=>$order->getId()), true);
            $mail->notify('Aviso de Solicitação de Reembolso', 'O usuário '.$user->getName().' ('.$user->getEmail().') enviou um solicitação de reembolso para a compra #'.$order->getId().'. <br />Motivo:<br />'.nl2br($refund->getReason()).'<br /><a href="'.$orderLinkAdmin.'">'.$orderLinkAdmin.'</a>');
            return $this->redirectFlash($this->generateUrl('order_myorders_view', array('id' => $order->getId())), 'Sua solicitação de reembolso foi efetuada. Aguarde nosso contato.');
        }
        $ret['title'] = 'Solicitação de Reembolso da Compra #'.$order->getId();
        $ret['order'] = $order;
        $ret['form']  = $form->createView();
        return $ret;
    }
    /**
     * Action que permite ao comprador comentar um reembolso
     * 
     * @Route("/reembolso/comentar/{id}", name="order_myorders_refundcomment")
     * @Template()
     */
    public function refundCommentAction()
    {
        return array();
    }
}