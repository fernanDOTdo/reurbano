<?php
namespace Reurbano\OrderBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\OrderBundle\Document\Refund;
use Reurbano\OrderBundle\Form\Backend\RefundType;
use Reurbano\OrderBundle\Document\StatusLog;

/**
 * Controller para administrar (CRUD) os reembolsos
 */

class RefundController extends BaseController
{
    /**
     * Action para listar os reembolsos.
     * 
     * @Route("/", name="admin_order_refund_index")
     * @Template()
     */
    public function indexAction()
    {
        $title = 'Administração Pedidos de Reembolso';
        $refund = $this->mongo('ReurbanoOrderBundle:Refund')->findAllByCreated();
        return array(
            'refund'   => $refund,
            'title'   => $title,
            'current' => 'admin_order_order_index',
            );
    }
    /**
     * Action para visualizar um pedido de reembolso
     * 
     * @Route("/ver/{id}", name="admin_order_refund_view")
     * @Template()
     */
    public function viewAction(Refund $refund)
    {
        $title = "Visualizar Reembolso ".$refund->getId();
        $order = $refund->getOrder();
        $user = $refund->getUser();
        return array(
            'title' => $title,
            'order' => $order,
            'refund' => $refund,
            'user' => $user,
            'current' => 'admin_order_refund_index',
            );
    }
    /**
     * Action para cancelar o pedido de reembolso.
     * 
     * @Route("/cancelar/{id}", name="admin_order_refund_cancel")
     * @Template()
     */
    public function cancelAction(Refund $refund)
    {
        if($refund->getStatus() != 1){
            return $this->redirectFlash($this->generateUrl('admin_order_refund_view', array('id' => $refund->getId())), 'Este reembolso não pode ser cancelado.', 'erro');
        }
        $title = 'Cancelar Reembolso - ' . $refund->getId();
        $form = $this->createForm(new RefundType());
        return array(
            'title' => $title,
            'form'  => $form->createView(),
            'id'    => $refund->getId(),
            'current' => 'admin_order_refund_index',
        );
    }
    /**
     * Action para aprovar o pedido de reembolso.
     * 
     * @Route("/aprovar/{id}", name="admin_order_refund_approve")
     * @Template()
     */
    public function approveAction(Refund $refund)
    {
        if($refund->getStatus() != 1){
            return $this->redirectFlash($this->generateUrl('admin_order_refund_view', array('id' => $refund->getId())), 'Este reembolso não pode ser aprovado.', 'erro');
        }
        $title = 'Aprovar Reembolso - ' . $refund->getId();
        $form = $this->createForm(new RefundType());
        return array(
            'title' => $title,
            'form'  => $form->createView(),
            'id'    => $refund->getId(),
            'current' => 'admin_order_refund_index',
        );
    }
    /**
     * Action para executar a aprovação / reprovação.
     * 
     * @Route("/executar/{id}", name="admin_order_refund_execute")
     * @Method("POST")
     */
    public function executeAction(Refund $refund)
    {
        if($refund->getStatus() != 1){
            return $this->redirectFlash($this->generateUrl('admin_order_refund_view', array('id' => $refund->getId())), 'Houve um erro ao processar sua requisição.', 'erro');
        }
        $request = $this->get('request');
        $dm = $this->dm();
        $form = $this->createForm(new RefundType());
        $data = $request->request->get($form->getName());
        $newStatus = ($data['status'] == 2) ? 2 : 0; // Valida o input hidden que contém o novo status
        $obs = $data['obs'];
        $refund->setStatus($newStatus);
        $dm->persist($refund);
        $dm->flush();
        $order = $refund->getOrder();
        $user = $this->getUser();
        $orderLinkBuyer = $this->generateUrl('order_myorders_view', array('id'=>$order->getId()), true);
        if($newStatus == 2){
            // Envia e-mail para o comprador sobre reembolso aprovado
            $nBuyer = $this->get('mastop.mailer');
                $nBuyer->to($order->getUser())
                        ->subject('Reembolso Aprovado')
                        ->template('pedido_reembolso_aprovado',array(
                            'user'  => $order->getUser(),
                            'order' => $order,
                            'msg' => ($data['obs']) ? $data['obs'] : false,
                            'orderLink' => $orderLinkBuyer,
                        ))
                        ->send();
        }else{
            // Envia e-mail para o comprador sobre reembolso aprovado
            $nBuyer = $this->get('mastop.mailer');
            $nBuyer->to($order->getUser())
                    ->subject('Reembolso Cancelado')
                    ->template('pedido_reembolso_cancelado',array(
                        'user'  => $order->getUser(),
                        'order' => $order,
                        'msg' => ($data['obs']) ? $data['obs'] : false,
                        'orderLink' => $orderLinkBuyer,
                    ))
                    ->send();
        }
        if(isset($data['cancelOrder']) && $order->getStatus() != null){
            // Coloca a quantidade de cupons do pedido de volta na oferta
            $this->mongo('ReurbanoDealBundle:Deal')->updateQuantity($order->getDeal()->getId(), $order->getDeal()->getQuantity() + $order->getQuantity());
            // Reativa a oferta
            $this->mongo('ReurbanoDealBundle:Deal')->updateActive($order->getDeal()->getId(), true);
            // Cancela a venda
            $this->mongo('ReurbanoOrderBundle:Order')->cancelOrder($order);
            // Adiciona o status em StatusLog
            $statusLog = new StatusLog();
            $statusLog->setUser($user);
            $order->addStatusLog($statusLog);
            $dm->persist($order);
            $dm->flush();
            // Notifica comprador do cancelamento
            $nBuyer = $this->get('mastop.mailer');
            $nBuyer->to($order->getUser())
                    ->subject('Compra Cancelada')
                    ->template('oferta_cancelada_comprador',array(
                        'user'  => $order->getUser(),
                        'order' => $order,
                        'msg' => false,
                        'orderLink' => $orderLinkBuyer,
                    ))
                    ->send();
            // Notifica vendedor do cancelamento
            $orderLinkSeller = $this->generateUrl('order_mysales_view', array('id'=>$order->getId()), true);
            $nSeller = $this->get('mastop.mailer');
            $nSeller->to($order->getSeller())
                    ->subject('Venda Cancelada')
                    ->template('oferta_cancelada_vendedor',array(
                        'user'  => $order->getSeller(),
                        'order' => $order,
                        'msg' => false,
                        'orderLink' => $orderLinkSeller,
                    ))
                    ->send();
        }
        $statusLabel = array(0 => 'cancelado', 2 => 'aprovado');
        return $this->redirectFlash($this->generateUrl('admin_order_refund_index'), 'Reembolso '.$statusLabel[$newStatus].' com sucesso!');
    }
    
}