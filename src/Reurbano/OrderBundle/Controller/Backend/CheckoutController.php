<?php
namespace Reurbano\OrderBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\OrderBundle\Document\Checkout;
use Reurbano\OrderBundle\Document\Comment;
use Reurbano\OrderBundle\Document\Escrow;

use Reurbano\OrderBundle\Form\Backend\CommentType;
use Reurbano\OrderBundle\Form\Backend\CheckoutType;

/**
 * Controller para administrar (CRUD) as retiradas (checkouts) solicitadas pelos vendedores
 */

class CheckoutController extends BaseController
{
    /**
     * Action para listar os checkouts dos vendedores.
     * 
     * @Route("/", name="admin_order_checkout_index")
     * @Template()
     */
    public function indexAction()
    {
        $title = 'Administração Solicitações de Resgate';
        $checkout = $this->mongo('ReurbanoOrderBundle:Checkout')->findAllByCreated();
        return array(
            'checkout'   => $checkout,
            'title'   => $title,
            'current' => 'admin_order_order_index',
            );
    }
    /**
     * Action para visualizar um checkout
     * 
     * @Route("/ver/{id}", name="admin_order_checkout_view")
     * @Template()
     */
    public function viewAction(Checkout $checkout)
    {
        $title = "Visualizar Resgate ".$checkout->getId();
        $user = $checkout->getUser();
        $commentForm = $this->createForm(new CommentType());
        return array(
            'title' => $title,
            'checkout' => $checkout,
            'user' => $user,
            'commentForm' => $commentForm->createView(),
            'current' => 'admin_order_order_index',
            );
    }
    /**
     * Action que permite ao administrador enviar um comentário na retirada.
     * 
     * @Route("/comentar/{id}", name="admin_order_checkout_comment")
     * @Method("POST")
     * @Template()
     */
    public function commentAction(Checkout $checkout)
    {
        $request = $this->get('request');
        $dm = $this->dm();
        $form = $this->createForm(new commentType());
        $user = $this->getUser();
        $comment = new Comment();
        $data = $request->request->get($form->getName());
        $checkoutLinkSeller = $this->generateUrl('order_checkout_view', array('id'=>$checkout->getId()), true);

        $comment->setMessage($data['comment']);
        $comment->setUser($user);
        $comment->setSpecial(true);

        $checkout->addComments($comment);
        $dm->persist($checkout);
        $dm->flush();

        // E-mail para o vendedor
        $mail = $this->get('mastop.mailer');
        $mail->to($checkout->getUser())
         ->subject('Novo comentário no resgate '.$checkout->getId())
         ->template('resgate_novocomentario_admin', array('user' => $checkout->getUser(), 'comment' => $comment->getMessage(), 'checkout' => $checkout, 'checkoutLink' => $checkoutLinkSeller, 'title' => 'Novo Comentário'))
         ->send();
        return $this->redirectFlash($this->generateUrl('admin_order_checkout_view', array('id' => $checkout->getId())), $this->trans('Comentado com sucesso!'));
    }
    
    /**
     * Action para cancelar o pedido de resgate.
     * 
     * @Route("/cancelar/{id}", name="admin_order_checkout_cancel")
     * @Template()
     */
    public function cancelAction(Checkout $checkout)
    {
        if($checkout->getStatus() != 1){
            return $this->redirectFlash($this->generateUrl('admin_order_checkout_view', array('id' => $checkout->getId())), 'Este resgate não pode ser cancelado.', 'error');
        }
        $title = 'Cancelar Resgate - ' . $checkout->getId();
        $form = $this->createForm(new CheckoutType());
        return array(
            'title' => $title,
            'form'  => $form->createView(),
            'id'    => $checkout->getId(),
            'current' => 'admin_order_order_index',
        );
    }
    /**
     * Action para aprovar o pedido de resgate.
     * 
     * @Route("/aprovar/{id}", name="admin_order_checkout_approve")
     * @Template()
     */
    public function approveAction(Checkout $checkout)
    {
        if($checkout->getStatus() != 1){
            return $this->redirectFlash($this->generateUrl('admin_order_checkout_view', array('id' => $checkout->getId())), 'Este resgate não pode ser aprovado.', 'error');
        }
        $title = 'Aprovar Resgate - ' . $checkout->getId();
        $form = $this->createForm(new CheckoutType());
        return array(
            'title' => $title,
            'form'  => $form->createView(),
            'id'    => $checkout->getId(),
            'current' => 'admin_order_order_index',
        );
    }
    /**
     * Action para executar a aprovação / reprovação.
     * 
     * @Route("/executar/{id}", name="admin_order_checkout_execute")
     * @Method("POST")
     */
    public function executeAction(Checkout $checkout)
    {
        if($checkout->getStatus() != 1){
            return $this->redirectFlash($this->generateUrl('admin_order_checkout_view', array('id' => $checkout->getId())), 'Houve um erro ao processar sua requisição.', 'error');
        }
        $request = $this->get('request');
        $dm = $this->dm();
        $form = $this->createForm(new CheckoutType());
        $data = $request->request->get($form->getName());
        $newStatus = ($data['status'] == 2) ? 2 : 0; // Valida o input hidden que contém o novo status
        $obs = $data['obs'];
        $checkout->setStatus($newStatus);
        $dm->persist($checkout);
        $dm->flush();
        // Libera o checkout no escrow
        $this->mongo('ReurbanoOrderBundle:Escrow')->releaseCheckout($checkout);
        $checkoutLink = $this->generateUrl('order_checkout_view', array('id'=>$checkout->getId()), true);
        
        if($newStatus == 2){
            // Resgate aprovado, manda e-mail para vendedor
            $nBuyer = $this->get('mastop.mailer');
            $nBuyer->to($checkout->getUser())
                    ->subject('Resgate Aprovado')
                    ->template('pedido_resgate_aprovado',array(
                        'user'  => $checkout->getUser(),
                        'checkout' => $checkout,
                        'msg' => ($data['obs']) ? $data['obs'] : false,
                        'checkoutLink' => $checkoutLink,
                    ))
                    ->send();
        }else{
            // Resgte cancelado, adiciona o valor no escrow e manda e-mail para vendedor
            $escrow = new Escrow();
            $escrow->setUser($checkout->getUser());
            $escrow->setData($checkout);
            $escrow->setValue($checkout->getTotal());
            $escrow->setObs("Cancelamento Resgate #".$checkout->getId());
            $escrow->setApproved(true);
            $dm->persist($escrow);
            $dm->flush();
            
            $nBuyer = $this->get('mastop.mailer');
            $nBuyer->to($checkout->getUser())
                    ->subject('Resgate Cancelado')
                    ->template('pedido_resgate_cancelado',array(
                        'user'  => $checkout->getUser(),
                        'checkout' => $checkout,
                        'msg' => ($data['obs']) ? $data['obs'] : false,
                        'checkoutLink' => $checkoutLink,
                    ))
                    ->send();
        }
        if($data['obs']){
            // Cria a observação como comentário
            $comment = new Comment();
            $comment->setMessage($data['obs']);
            $comment->setUser($this->getUser());
            $comment->setSpecial(true);

            $checkout->addComments($comment);
            $dm->persist($checkout);
            $dm->flush();
        }
        $statusLabel = array(0 => 'cancelado', 2 => 'aprovado');
        return $this->redirectFlash($this->generateUrl('admin_order_checkout_index'), 'Resgate '.$statusLabel[$newStatus].' com sucesso!');
    }
}