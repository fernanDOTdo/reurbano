<?php
namespace Reurbano\OrderBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

use Reurbano\OrderBundle\Form\Backend\StatusChangeType;
use Reurbano\OrderBundle\Form\Backend\CommentType;
use Reurbano\OrderBundle\Form\Backend\CancelType;

use Reurbano\OrderBundle\Document\Order;
use Reurbano\OrderBundle\Document\StatusLog;
use Reurbano\OrderBundle\Document\Comment;
use Reurbano\UserBundle\Document\User;

/**
 * Controller para administrar (CRUD) os pedidos
 */

class OrderController extends BaseController
{
    /**
     * Action para listar os pedidos.
     * 
     * @Route("/", name="admin_order_order_index")
     * @Template()
     */
    public function indexAction()
    {
        $title = 'Administração de Pedidos';
        $order = $this->mongo('ReurbanoOrderBundle:Order')->findAllByCreated();
        return array(
            'order'   => $order,
            'title'   => $title,
            'current' => 'admin_order_order_index',
            );
    }
    /**
     * Action para visualizar um pedido
     * 
     * @Route("/ver/{id}", name="admin_order_order_view")
     * @Template()
     */
    public function viewAction(Order $order)
    {
        $title = "Visualizar Venda ".$order->getId();
        $status = $order->getStatus();
        $statusLog = new StatusLog();
        if($status){
            $statusLog->setStatus($status);
            $statusForm = $this->createForm(new StatusChangeType(), $statusLog);
        }
        // Reembolso
        $refund = false;
        if($this->mongo('ReurbanoOrderBundle:Refund')->hasId($order->getId())){
            $refund = $this->mongo('ReurbanoOrderBundle:Refund')->findOneById($order->getId());
        }
        $commentForm = $this->createForm(new CommentType());
        $pay = $order->getPayment();
        $gateway = 'Reurbano\OrderBundle\Payment\\' . $pay['gateway'];
        $payment = new $gateway($order, $this->container);
        return array(
            'title' => $title,
            'order' => $order,
            'status' => ($status) ? $order->getStatus() : false,
            'statusForm' => ($status) ? $statusForm->createView() : false,
            'commentForm' => $commentForm->createView(),
            'refund' => $refund,
            'payment' => $payment,
            'voucher' => $order->getDealVoucher(),
            'current' => 'admin_order_order_index',
            );
    }
    /**
     * Action para mudar o status do pedido.
     * 
     * @Route("/status/{id}", name="admin_order_order_status")
     * @Template()
     */
    public function statusAction(Order $order, $id)
    {
        $title = "Alterar status do pedido";
        $dm = $this->dm();
        $request = $this->get('request');
        $form = $this->createForm(new StatusChangeType());
        if($request->getMethod() == 'POST'){
            $user = $this->getUser();
            $data = $request->request->get($form->getName());
            if($data['status'] == $order->getStatus()->getId()){
                return $this->redirectFlash($this->generateUrl('admin_order_order_view', array('id' => $id)), $this->trans('O status atual deste pedido já é '.$order->getStatus()->getName()), 'error');
            }
            $status = $this->mongo('ReurbanoOrderBundle:Status')->find((int)$data['status']);
            $statusLog = new StatusLog();
            $statusLog->setStatus($status);
            $statusLog->setObs($data['obs']);
            $statusLog->setUser($user);
            
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
            // Verifica se é para liberar o dinheiro para o vendedor
            $releaseMoney = explode(',', $this->get('mastop')->param('order.all.releasestatus'));
            if(count($releaseMoney) > 0 && in_array($status->getId(), $releaseMoney)){
                $mail = $this->get('mastop.mailer');
                $mail->to($order->getSeller())
                     ->subject('Pagamento liberado')
                     ->template('pedido_valor_liberado',array(
                            'title'  => 'Pagamento Liberado',
                            'user'  => $order->getSeller(),
                            'order' => $order,
                            'orderLink' => $this->generateUrl('user_dashboard_index', array(), true). '#mybalance',
                        ));
                $mail->send();
                $this->mongo('ReurbanoOrderBundle:Escrow')->releaseOrder($order);
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
                        'obs' => $data['obs'],
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
                        'obs' => $data['obs'],
                    ));
            $mail->send();
            return $this->redirectFlash($this->generateUrl('admin_order_order_view', array('id' => $id)), $this->trans('Status atualizado com sucesso!'));
        }
        $form = $this->createForm(new StatusChangeType());
        return array(
            'title'   => $title,
            'form'    => $form->createView(),
            'id'      => $id,
            'current' => 'admin_order_order_index',
        );
    }
    /**
     * Action que permite ao administrador enviar um comentário no pedido.
     * 
     * @Route("/comentar/{id}", name="admin_order_order_comment")
     * @Template()
     */
    public function commentAction(Order $order, $id)
    {
        $title = 'Cometário';
        $request = $this->get('request');
        $dm = $this->dm();
        $form = $this->createForm(new commentType());
        if($request->getMethod() == 'POST'){
            $comment = new Comment();
            $user = $this->get('security.context')->getToken()->getUser();
            $data = $request->request->get($form->getName());
            $orderLinkBuyer = $this->generateUrl('order_myorders_view', array('id'=>$order->getId()), true);
            $orderLinkSeller = $this->generateUrl('order_mysales_view', array('id'=>$order->getId()), true);
            
            $comment->setMessage($data['comment']);
            $comment->setUser($user);
            $comment->setSpecial(true);
            
            $order->addComments($comment);
            $dm->persist($order);
            $dm->flush();
            
            // E-mail para o comprador
            $mail = $this->get('mastop.mailer');
            $mail->to($order->getUser())
             ->subject('Novo comentário no pedido '.$order->getId())
             ->template('oferta_novocomentario_admin', array('user' => $order->getUser(), 'comment' => $comment->getMessage(), 'order' => $order, 'orderLink' => $orderLinkBuyer, 'title' => 'Novo Comentário'))
             ->send();
            // E-mail para o vendedor
            $mail = $this->get('mastop.mailer');
            $mail->to($order->getSeller())
             ->subject('Novo comentário no pedido '.$order->getId())
             ->template('oferta_novocomentario_admin', array('user' => $order->getSeller(), 'comment' => $comment->getMessage(), 'order' => $order, 'orderLink' => $orderLinkSeller, 'title' => 'Novo Comentário'))
             ->send();
            return $this->redirectFlash($this->generateUrl('admin_order_order_view', array('id' => $id)), $this->trans('Comentado com sucesso!'));
            
        }
        $form = $this->createFormBuilder()
                ->add('Comentario', 'textarea')
                ->add('Especial', 'checkbox', array(
                    'label'    => 'Especial?',
                    'required' => false,
                ))
                ->getForm();
                
        return array(
            'title'   => $title,
            'form'    => $form->createView(),
            'id'      => $id,
            'current' => 'admin_order_order_index',
        );
    }
    
    /**
     * Action para cancelar um pedido.
     * 
     * @Route("/cancelar/{id}", name="admin_order_order_cancel")
     * @Template()
     */
    public function cancelAction(Order $order){
        $title = 'Cancelar pedido - ' . $order->getId();
        $request = $this->get('request');
        $dm = $this->dm();
        $form = $this->createForm(new CancelType());
        if(!$order->getStatus()){
            // Não deixa cancelar o cancelado
            return $this->redirectFlash($this->generateUrl('admin_order_order_view', array('id' => $order->getId())), $this->trans('Este pedido já está cancelado'), 'error');
        }
        if($request->getMethod() == 'POST'){
            $data = $request->request->get($form->getName());
            if(isset($data['notifyBuyer'])){
                $orderLinkBuyer = $this->generateUrl('order_myorders_view', array('id'=>$order->getId()), true);
                $nBuyer = $this->get('mastop.mailer');
                $nBuyer->to($order->getUser()->getEmail())
                        ->subject('Compra Cancelada')
                        ->template('oferta_cancelada_comprador',array(
                            'user'  => $order->getUser(),
                            'order' => $order,
                            'msg' => ($data['obs']) ? $data['obs'] : false,
                            'orderLink' => $orderLinkBuyer,
                        ))
                        ->send();
            }
            if(isset($data['notifySeller'])){
                $orderLinkSeller = $this->generateUrl('order_mysales_view', array('id'=>$order->getId()), true);
                $nSeller = $this->get('mastop.mailer');
                $nSeller->to($order->getDeal()->getUser()->getEmail())
                        ->subject('Venda Cancelada')
                        ->template('oferta_cancelada_vendedor',array(
                            'user'  => $order->getDeal()->getUser(),
                            'order' => $order,
                            'msg' => ($data['obs']) ? $data['obs'] : false,
                            'orderLink' => $orderLinkSeller,
                        ))
                        ->send();
            }
            if(isset($data['returnDeal'])){
                $this->mongo('ReurbanoDealBundle:Deal')->updateQuantity($order->getDeal()->getId(), $order->getDeal()->getQuantity() + $order->getQuantity());
                $this->mongo('ReurbanoDealBundle:Deal')->updateActive($order->getDeal()->getId(), true);
            }
            $user = $this->getUser();
            $this->mongo('ReurbanoOrderBundle:Order')->cancelOrder($order);
            $statusLog = new StatusLog();
            $statusLog->setObs($data['obs']);
            $statusLog->setUser($user);
            
            $order->addStatusLog($statusLog);
            $dm->persist($order);
            $dm->flush();
            return $this->redirectFlash($this->generateUrl('admin_order_order_index'), 'Venda cancelada com sucesso!');
        }
        return array(
            'title' => $title,
            'form'  => $form->createView(),
            'id'    => $order->getId(),
        ); 
    }
    /**
     * @Route("/export", name="admin_order_order_export")
     */
    public function exportAction()
    {
        $order = $this->mongo('ReurbanoOrderBundle:Order')->findAllByCreated();
        $data = "Cidade,Site de origem,Categoria,Nome Vendedor,E-mail Vendedor,Nome Comprador,E-mail Comprador,Preço Original,Preço no site,Data Inclusão,Data Venda,Visualizações,Status,Forma de Pagamento\n";
        foreach($deal as $deal){
            $data .= $deal->getSource()->getCity()->getName() .  
                    "," .$deal->getSource()->getSite()->getName() . 
                    "," . $deal->getSource()->getCategory()->getName() . 
                    "," . $deal->getUser()->getName() . 
                    "," . $deal->getUser()->getEmail() . 
                    "," . $deal->getSource()->getPrice() . 
                    "," . $deal->getPrice() . 
                    "," . $deal->getCreatedAt()->format('d/m/Y') .
                    "," . $deal->getViews() . "\n";
        }
        return new Response($data, 200, array(
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename= mailing_' . date('d_m_Y') . '.csv',
        ));
    }
}