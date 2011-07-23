<?php
namespace Reurbano\OrderBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\OrderBundle\Document\Order;

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
        return array('title' => $title);
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
        return array();
    }
    /**
     * Action que permite ao administrador enviar um comentário no pedido.
     * 
     * @Route("/comentar/{id}", name="admin_order_order_comment")
     * @Template()
     */
    public function commentAction($id)
    {
        return array();
    }
    
    /**
     * @Route("/novo", name="admin_order_order_")
     */
    public function newAction(){
        $dm = $this->dm();
        $order = $this->mongo('ReurbanoOrderBundle:Order')->createOrder();
        $dm->persist($order);
        $dm->flush();
        return $this->redirect($this->generateUrl('admin_order_order_index'));
    }
}