<?php
namespace Reurbano\DealBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\DealBundle\Document\Source;
use Reurbano\DealBundle\Form\Backend\SourceType;

/**
 * Controller para administrar (CRUD) o "Banco de Ofertas"
 * (collection que guardará todas as ofertas dos sites de compras coletivas, para facilitar a inserção pelo usuário).
 */

class SourceController extends BaseController
{
    /**
     * @Route("/", name="admin_deal_source_index")
     * @Template()
     */
    public function indexAction()
    {
        $title = 'Administração do Banco de Ofertas';
        $ofertas = $this->mongo('ReurbanoDealBundle:Source')->findAll();
        //$ofertas = $this->mongo('ReurbanoDealBundle:Source')->findAllByCreated();
        return array('ofertas' => $ofertas, 'title' => $title);
    }
    /**
     * @Route("/novo", name="admin_deal_source_new")
     * @Route("/editar/{id}", name="admin_deal_source_edit")
     * @Route("/salvar/{id}", name="admin_deal_source_save", defaults={"id" = null})
     * @Template()
     */
    public function dealAction($id = null)
    {
        $dm = $this->dm();
        $title = ($id) ? "Editar Oferta" : "Nova Oferta";
        if($id){
            $deal = $this->mongo('ReurbanoDealBundle:Source')->find($id);
            if (!$deal) {
                throw $this->createNotFoundException('Nenhuma oferta encontrada com o ID '.$id);
            }
        }else{
            $deal = new Source();
        }
        $request = $this->get('request');
        $form = $this->createForm(new SourceType(), $deal);
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            $formResult = $request->request->get('source');
            $expiresAt = explode('/',$formResult['expiresAt']);
            $dataAtual = new \DateTime($expiresAt[2].'-'.$expiresAt[1].'-'.$expiresAt[0]);
            $deal->setExpiresAt($dataAtual);
            if ($form->isValid()) {
                $dm->persist($deal);
                $dm->flush();
                $this->get('session')->setFlash('ok', $this->trans(($id) ? "Oferta Editada" : "Oferta Criada" ));
                return $this->redirect($this->generateUrl('admin_deal_source_index'));
            }
        }
        return array(
            'form' => $form->createView(),
            'deal' => $deal,
            'title'=>  $title);
    }
    /**
     * @Route("/deletar/{id}", name="admin_deal_source_delete")
     * @Template()
     */
    public function deleteAction($id)
    {
        $dm = $this->dm();
        $deal = $this->mongo('ReurbanoDealBundle:Source')->find($id);
        if (!$deal) {
            throw $this->createNotFoundException('Nenhuma oferta encontrada com o ID '.$id);
        }
        
        $request = $this->get('request');
        
        if ('POST' == $request->getMethod()) {
            $dm = $this->dm();
            $dm->remove($deal);
            $dm->flush();
            $this->get('session')->setFlash('ok', $this->trans('Oferta Deletada'));
            return $this->redirect($this->generateUrl('admin_deal_source_index'));
        }

        $deal = $this->mongo('ReurbanoDealBundle:Source')->find($id);
        return array(
            'deal' => $deal,
            'id' => $id
        );
        
    }
}