<?php
namespace Reurbano\DealBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\OrderBundle\Document\Checkout;

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
        $title = 'Administração de Retiradas';
        return array('title' => $title);
    }
    /**
     * Action para visualizar um checkout
     * 
     * @Route("/ver/{id}", name="admin_order_checkout_view")
     * @Template()
     */
    public function viewAction($id)
    {
        $title = "Visualizar Retirada";
        return array('title'=>  $title);
    }
    /**
     * Action para mudar o status da Retirada.
     * 
     * @Route("/status/{id}", name="admin_order_checkout_status")
     * @Template()
     */
    public function statusAction($id)
    {
        return array();
    }
    /**
     * Action que permite ao administrador enviar um comentário na retirada.
     * 
     * @Route("/comentar/{id}", name="admin_order_checkout_comment")
     * @Template()
     */
    public function commentAction($id)
    {
        return array();
    }
}