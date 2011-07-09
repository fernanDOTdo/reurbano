<?php
namespace Reurbano\DealBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\DealBundle\Document\Deal;
use Reurbano\DealBundle\Form\Backend\DealType;

/**
 * Controller para administrar (CRUD) ofertas.
 */

class DealController extends BaseController
{
    /**
     * @Route("/", name="admin_deal_deal_index")
     * @Template()
     */
    public function indexAction()
    {
        $title = 'Administração de Ofertas';
        //$ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findAll();
        $ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findAllByCreated();
        return array('ofertas' => $ofertas, 'title' => $title);
    }
    /**
     * @Route("/novo", name="admin_deal_deal_new")
     * @Route("/editar/{id}", name="admin_deal_deal_edit")
     * @Route("/salvar/{id}", name="admin_deal_deal_save", defaults={"id" = null})
     * @Template()
     */
    public function dealAction($id = null)
    {
        $dm = $this->dm();
        $title = ($id) ? "Editar Oferta" : "Nova Oferta";
        if($id){
            $deal = $this->mongo('ReurbanoDealBundle:Deal')->find($id);
            if (!$deal) throw $this->createNotFoundException('Nenhuma oferta encontrada com o ID '.$id);
        }else{
            $deal = new Deal();
        }
        $form = $this->createForm(new DealType(), $deal);
        $request = $this->get('request');
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $dm->persist($deal);
                $dm->flush();
                $this->get('session')->setFlash('ok', $this->trans(($id) ? "Oferta Editada" : "Oferta Criada" ));
                return $this->redirect($this->generateUrl('admin_deal_deal_index'));
            }
        }
        return array('form' => $form->createView(), 'deal' => $deal, 'title'=>  $title);
    }
    /**
     * @Route("/deletar/{id}", name="admin_deal_deal_delete")
     */
    public function deleteAction($id)
    {
        $dm = $this->dm();
        $deal = $this->mongo('ReurbanoDealBundle:Deal')->find($id);
        if (!$deal) throw $this->createNotFoundException('Nenhuma oferta encontrada com o ID '.$id);
        $dm->remove($deal);
        $dm->flush();
        $this->get('session')->setFlash('ok', $this->trans('Oferta Deletada'));
        return $this->redirect($this->generateUrl('admin_deal_deal_index'));
    }
}