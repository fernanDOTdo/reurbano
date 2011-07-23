<?php

namespace Reurbano\OrderBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\OrderBundle\Document\Status;
use Reurbano\OrderBundle\Form\Backend\StatusType;

/**
 * Controller para administrar (CRUD) status de pedidos.
 */

class StatusController extends BaseController
{
    /**
     * Action para listar todos os status.
     * 
     * @Route("/", name="admin_order_status_index")
     * @Template()
     */
    public function indexAction()
    {
        $title = 'Administração de Status';
        $status = $this->mongo('ReurbanoOrderBundle:Status')->findAll();
        return array('status' => $status, 'title' => $title);
    }
    /**
     * Adiciona um novo, edita um já criado e salva ambos
     * 
     * @Route("/novo", name="admin_order_status_new")
     * @Route("/editar/{id}", name="admin_order_status_edit")
     * @Route("/salvar/{id}", name="admin_order_status_save", defaults={"id" = null})
     * @Template()
     */
    public function statusAction($id = null)
    {
        $dm = $this->dm();
        $title = ($id) ? "Editar Status" : "Novo Status";
        if($id){
            $stat = $this->mongo('ReurbanoOrderBundle:Status')->find((int)$id);
            if (!$stat) throw $this->createNotFoundException('Nenhum status encontrado com o ID '.$id);
        }else{
            $stat = new Status();
        }
        $form = $this->createForm(new StatusType(), $stat);
        $request = $this->get('request');
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $dm->persist($stat);
                $dm->flush();
                $this->get('session')->setFlash('ok', $this->trans(($id) ? "Status Editado" : "Status Criado" ));
                return $this->redirect($this->generateUrl('admin_order_status_index'));
            }
        }
        return array('form' => $form->createView(), 'stat' => $stat, 'title'=>  $title);
    }
    /**
     * Exibe um pre delete e deleta se for confirmado
     * 
     * @Route("/deletar/{id}", name="admin_order_status_delete")
     * @Template()
     */
    public function deleteAction($id)
    {
        $request = $this->get('request');
        $formResult = $request->request;
        $dm = $this->dm();
        $stat = $this->mongo('ReurbanoOrderBundle:Status')->find((int)$id);
        if($request->getMethod() == 'POST'){
            if (!$stat) throw $this->createNotFoundException('Nenhum status encontrado com o ID '.$id);
            $dm->remove($stat);
            $dm->flush();
            $this->get('session')->setFlash('ok', $this->trans('Status Deletado'));
            return $this->redirect($this->generateUrl('admin_order_status_index'));
        }
        return array(
            'name' => $stat->getName(),
            'id'   => $stat->getId(),
        );
    }
}