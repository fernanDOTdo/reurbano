<?php
namespace Reurbano\OrderBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\OrderBundle\Document\Order;
use Reurbano\OrderBundle\Document\StatusLog;
use Reurbano\OrderBundle\Document\Comment;
use Reurbano\UserBundle\Document\User;
use Reurbano\OrderBundle\Form\Backend\StatusChangeType;

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
            'order' => $order,
            'title' => $title,
            );
    }
    /**
     * Action para visualizar um pedido
     * 
     * @Route("/ver/{id}", name="admin_order_order_view")
     * @Template()
     */
    public function viewAction($id)
    {
        $title = "Visualizar Pedido";
        return array('title'=>  $title);
    }
    /**
     * Action para mudar o status do pedido.
     * 
     * @Route("/status/{id}", name="admin_order_order_status")
     * @Template()
     */
    public function statusAction($id)
    {
        $title = "Alterar status do pedido";
        $dm = $this->dm();
        $request = $this->get('request');
        $order = $this->mongo('ReurbanoOrderBundle:Order')->find((int)$id);
        $form = $this->createForm(new StatusChangeType());
        if($request->getMethod() == 'POST'){
            $user = $this->get('security.context')->getToken()->getUser();
            $form = 
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
            
            $this->get('session')->setFlash('ok', $this->trans('Status atualizado com sucesso!'));
            return $this->redirect($this->generateUrl('admin_order_order_index'));
        }
        $form = $this->createForm(new StatusChangeType());
        return array(
            'title' => $title,
            'form'  => $form->createView(),
            'id'    => $id,
        );
    }
    /**
     * Action que permite ao administrador enviar um comentário no pedido.
     * 
     * @Route("/comentar/{id}", name="admin_order_order_comment")
     * @Template()
     */
    public function commentAction($id)
    {
        $title = 'Cometário';
        $request = $this->get('request');
        $dm = $this->dm();
        $order = $this->mongo('ReurbanoOrderBundle:Order')->find((int)$id);
        if($request->getMethod() == 'POST'){
            $comment = new Comment();
            $user = $this->get('security.context')->getToken()->getUser();
            $data = $request->request->get('form');
            $special = isset($data['Especial'])? true : false;
            
            $comment->setMessage($data['Comentario']);
            $comment->setUser($user);
            $comment->setSpecial($special);
            
            $order->addComments($comment);
            $dm->persist($order);
            $dm->flush();
            
            $this->get('session')->setFlash('ok', $this->trans('Comentado com sucesso!'));
            return $this->redirect($this->generateUrl('admin_order_order_index'));
            
        }
        $form = $this->createFormBuilder()
                ->add('Comentario', 'textarea')
                ->add('Especial', 'checkbox', array(
                    'label'    => 'Especial?',
                    'required' => false,
                ))
                ->getForm();
                
        return array(
            'title' => $title,
            'form'  => $form->createView(),
            'id'    => $id,
        );
    }
    
    /**
     * Criar um novo
     * 
     * @Route("/novo", name="admin_order_order_novo")
     */
    public function newAction()
    {
        $dm = $this->dm();
        $order = $this->mongo('ReurbanoOrderBundle:Order')->createOrder();
        $status = $this->mongo('ReurbanoOrderBundle:Status')->findOneById(1);
        $statusLog = new StatusLog();
        $statusLog->setStatus($status);
        $order->setStatus($status);
        $order->addStatusLog($statusLog);
        $dm->persist($order);
        $dm->flush();
        return $this->redirect($this->generateUrl('admin_order_order_index'));
    }
}