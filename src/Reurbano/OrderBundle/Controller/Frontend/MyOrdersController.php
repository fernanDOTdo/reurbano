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
            return $this->redirectFlash($this->generateUrl('user_dashboard_index'), 'Digite uma mensagem para enviar um comentário no pedido.', 'error');
        }
        $dm = $this->dm();
        $comment = new Comment();
        $comment->setUser($user);
        $comment->setMessage($this->getRequest()->request->get('message'));
        $order->addComments($comment);
        $dm->persist($order);
        $dm->flush();
        // Enviar e-mail de notificação de novo comentário
        $orderLink = $this->generateUrl('order_myorders_view', array('id'=>$order->getId()), true);
        $orderLinkAdmin = $this->generateUrl('admin_order_order_view', array('id'=>$order->getId()), true);
        $mail = $this->get('mastop.mailer');
        $mail->to($order->getSeller())
             ->subject('Novo comentário no pedido '.$order->getId())
             ->template('oferta_novocomentario_comprador', array('user' => $order->getSeller(), 'comment' => $comment->getMessage(), 'order' => $order, 'orderLink' => $orderLink, 'title' => 'Novo Comentário'))
             ->send();
            $mail->notify('Aviso de novo comentário', 'O usuário '.$user->getName().' ('.$user->getEmail().') enviou o seguinte comentário no pedido '.$order->getId().': <br />'.nl2br($comment->getMessage()).'<br /><a href="'.$orderLinkAdmin.'">'.$orderLinkAdmin.'</a>');
        return $this->redirectFlash($this->generateUrl('user_dashboard_index'). '#myorders', 'Comentário adicionado no pedido '.$order->getId());
    }
    /**
     * Action que permite ao comprador solicitar um reembolso
     * 
     * @Route("/reembolso/{id}", name="order_myorders_refund")
     * @Template()
     */
    public function refundAction()
    {
        return array();
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