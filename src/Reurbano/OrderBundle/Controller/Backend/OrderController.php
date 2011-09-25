<?php
namespace Reurbano\OrderBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Reurbano\OrderBundle\Form\Backend\StatusChangeType;
use Reurbano\OrderBundle\Form\Backend\CommentType;

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
        $order = $this->mongo('ReurbanoOrderBundle:Order')->FindAll();
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
        $title = "Venda";
        
        $status = $order->getStatus();
        $statusForm = $this->createForm(new StatusChangeType());
        $commentForm = $this->createForm(new CommentType());
        $pay = $order->getPayment();
        $gateway = 'Reurbano\OrderBundle\Payment\\' . $pay['gateway'];
        $payment = new $gateway($order, $this->container);
        
        return array(
            'title' => $title,
            'order' => $order,
            'statusForm' => $statusForm->createView(),
            'commentForm' => $commentForm->createView(),
            'payment' => $payment,
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
            $user = $this->get('security.context')->getToken()->getUser();
            $data = $request->request->get($form->getName());
            $status = $this->mongo('ReurbanoOrderBundle:Status')->find((int)$data['status']);
            $statusLog = new StatusLog();
            $statusLog->setStatus($status);
            $statusLog->setObs($data['obs']);
            $statusLog->setUser($user);
            
            $order->setStatus($status);
            $order->addStatusLog($statusLog);
            
            $dm->persist($order);
            $dm->flush();
            
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
                $mail = $this->get('mastop.mailer');
                $mail->to($order->getUser()->getEmail())
                        ->subject('Comentário na sua compra')
                        ->template('pedido_comentario',array(
                            'user'  => $order->getUser(),
                            'order' => $order,
                            'msg' => $data['comment'],
                        ))
                        ->send();
            
            $comment->setMessage($data['comment']);
            $comment->setUser($user);
            $comment->setSpecial(true);
            
            $order->addComments($comment);
            $dm->persist($order);
            $dm->flush();
            
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
        $request = $this->get('request');
        $formResult = $request->request;
        $dm = $this->dm();
        if($request->getMethod() == 'POST'){
            $this->mongo('ReurbanoOrderBundle:Order')->cancelOrder($order->getId());
            $statusLog = new StatusLog();
            $statusLog->setObs($data['obs']);
            $statusLog->setUser($user);
            
            $order->addStatusLog($statusLog);
            return $this->redirectFlash($this->generateUrl('admin_order_order_index'), 'Venda cancelada com sucesso!');
        }
        return $this->confirm($this->trans('Tem certeza que deseja cancelar o pedido numero: %id%?', array("%id%" => $order->getId())), array('id' => $order->getId()));
    }
}