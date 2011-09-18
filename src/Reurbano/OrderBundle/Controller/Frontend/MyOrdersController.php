<?php
namespace Reurbano\OrderBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Reurbano\OrderBundle\Document\Order;
use Reurbano\OrderBundle\Document\Comment;

/**
 * Controller que cuidará dos pedidos do comprador.
 */

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
     * @Template()
     */
    public function viewAction()
    {
        return array();
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
        $user = $this->get('security.context')->getToken()->getUser();
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
        // @TODO: Enviar e-mail de notificação de novo comentário
        return $this->redirectFlash($this->generateUrl('user_dashboard_index'), 'Comentário adicionado no pedido '.$order->getId());
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