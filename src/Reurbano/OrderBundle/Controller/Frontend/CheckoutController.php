<?php
namespace Reurbano\OrderBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\OrderBundle\Document\Checkout;
use Reurbano\OrderBundle\Document\Escrow;
use Reurbano\OrderBundle\Document\Comment;

/**
 * Controller que cuidará do Financeiro do vendedor.
 */

class CheckoutController extends BaseController
{
    /**
     * Action para visualizar um checkout do vendedor
     * 
     * @Route("/ver/{id}", name="order_checkout_view")
     * @Template()
     */
    public function viewAction(Checkout $checkout)
    {
        $user = $this->getUser();
        if($user->getId() != $checkout->getUser()->getId()){
            return $this->redirectFlash($this->generateUrl('user_dashboard_index').'#mybalance', 'Você não tem permissão para acessar esta página.', 'error');
        }
        $ret['title'] = 'Solicitação de Resgate #'.$checkout->getId();
        $ret['checkout'] = $checkout;
        return $ret;
    }
    /**
     * Action para o vendedor comentar um checkout
     * 
     * @Route("/comentar/{id}", name="order_checkout_comment")
     * @Method("POST")
     * @Template()
     */
    public function commentAction(Checkout $checkout)
    {
        $user = $this->getUser();
        if($user->getId() != $checkout->getUser()->getId()){
            return $this->redirectFlash($this->generateUrl('user_dashboard_index'), 'Você não tem permissão para acessar esta página.', 'error');
        }
        if(trim($this->getRequest()->request->get('message')) == null){
            return $this->redirectFlash($this->generateUrl('order_checkout_view', array('id'=>$checkout->getId())), 'Digite uma mensagem para enviar um comentário no resgate.', 'error');
        }
        $dm = $this->dm();
        $comment = new Comment();
        $comment->setUser($user);
        $comment->setMessage($this->getRequest()->request->get('message'));
        $checkout->addComments($comment);
        $dm->persist($checkout);
        $dm->flush();
        // Enviar e-mail de notificação de novo comentário
        $orderLinkAdmin = $this->generateUrl('admin_order_checkout_view', array('id'=>$checkout->getId()), true);
        $mail = $this->get('mastop.mailer');
        $mail->notify('Aviso de novo comentário no resgate', 'O usuário '.$user->getName().' ('.$user->getEmail().') enviou o seguinte comentário no resgate '.$checkout->getId().': <br />'.nl2br($comment->getMessage()).'<br /><br /><a href="'.$orderLinkAdmin.'">'.$orderLinkAdmin.'</a>');
        return $this->redirectFlash($this->generateUrl('order_checkout_view', array('id' => $checkout->getId())), 'Comentário adicionado no resgate '.$checkout->getId());
    }
    /**
     * Action que solicita um checkout pelo vendedor
     * 
     * @Route("/resgatar", name="order_checkout_request")
     * @Template()
     */
    public function requestAction()
    {
        $user = $this->getUser();
        $total = $this->mongo('ReurbanoOrderBundle:Escrow')->totalCheckoutByUser($user->getId());
        $dm = $this->dm();
        $request = $this->get('request');
        // Verifica se o usuário preencheu as informações bancárias
        if($user->getBankData() == ''){
            return $this->redirectFlash($this->generateUrl('user_user_bank'), 'Para solicitar resgate você precisa definir suas informações bancárias.', 'error');
        }
        // Verifica se o usuário pode resgatar alguma coisa
        if(!$total){
            $this->redirectFlash($this->generateUrl('user_dashboard_index').'#mybalance', $this->trans('Você ainda não tem valor liberado para resgate.'), 'error');
        }
        $mail = $this->get('mastop.mailer');
        $mail->notify('Debug: Resgate', 'O usuário '.$user->getName().' ('.$user->getEmail().') entrou em resgate. DEBUG:<br />'.print_r($total, true));
        if($request->getMethod() == 'POST'){
            // Salva a solicitação de Resgate
            $checkout = $this->mongo('ReurbanoOrderBundle:Checkout')->createCheckout();
            $checkout->setUser($user);
            $checkout->setTotal($total);
            $dm->persist($checkout);
            $dm->flush();
            // Salva a saída no escrow
            $escrow = new Escrow();
            $escrow->setUser($user);
            $escrow->setData($checkout);
            $escrow->setValue($total);
            $escrow->setMoneyIn(false);
            $escrow->setObs("Resgate #".$checkout->getId());
            $dm->persist($escrow);
            $dm->flush();
            // Notifica o admin
            $orderLinkAdmin = $this->generateUrl('admin_order_checkout_view', array('id'=>$checkout->getId()), true);
            $mail = $this->get('mastop.mailer');
            $mail->notify('Aviso de Solicitação de Resgate', 'O usuário '.$user->getName().' ('.$user->getEmail().') enviou um solicitação de resgate no valor de <b>'.$total.'</b><br /><br /><a href="'.$orderLinkAdmin.'">'.$orderLinkAdmin.'</a>');
            return $this->redirectFlash($this->generateUrl('order_checkout_view', array('id' => $checkout->getId())), 'Sua solicitação de resgate foi efetuada. Aguarde nosso contato.');
        }
        $ret['total'] = $total;
        $ret['title'] = 'Solicitação de Resgate';
        return $ret;
    }
}