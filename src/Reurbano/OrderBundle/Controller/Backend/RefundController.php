<?php
namespace Reurbano\OrderBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\OrderBundle\Document\Refund;

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
        return array('title' => $title);
    }
    /**
     * Action para visualizar um pedido de reembolso
     * 
     * @Route("/ver/{id}", name="admin_order_refund_view")
     * @Template()
     */
    public function viewAction($id)
    {
        $title = "Visualizar Pedido de Reembolso";
        return array('title'=>  $title);
    }
    /**
     * Action para mudar o status do pedido de reembolso.
     * 
     * @Route("/status/{id}", name="admin_order_refund_status")
     * @Template()
     */
    public function statusAction($id)
    {
        return array();
    }
    /**
     * Action que permite ao administrador enviar um comentário no pedido de reembolso.
     * 
     * @Route("/comentar/{id}", name="admin_order_refund_comment")
     * @Template()
     */
    public function commentAction($id)
    {
        return array();
    }
}