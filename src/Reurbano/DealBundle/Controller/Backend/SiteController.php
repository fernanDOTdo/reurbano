<?php

namespace Reurbano\DealBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\DealBundle\Document\Site;
use Reurbano\DealBundle\Form\Backend\SiteType;

/**
 * Controller para administrar (CRUD) sites de compra coletiva.
 *
 * @author Rafael Basquens <rafael@basquens.com>
 */
class SiteController extends BaseController {
    
    /**
     * Lista todas os Sites
     * 
     * @Route("/", name="admin_deal_site_index")
     * @Template()
     */
    public function indexAction()
    {
        $title = 'Administração de Sites';
        $site = $this->mongo('ReurbanoDealBundle:Site')->findAllByOrder();
        return array('site' => $site, 'title' => $title);
    }
    
    /**
     * Adiciona um novo, edita um já criado e salva ambos
     * 
     * @Route("/novo", name="admin_deal_site_new")
     * @Route("/editar/{id}", name="admin_deal_site_edit")
     * @Route("/salvar/{id}", name="admin_deal_site_save", defaults={"id" = null})
     * @Template()
     */
    public function siteAction($id = null)
    {
        $dm = $this->dm();
        $title = ($id) ? "Editar Site" : "Novo Site";
        if($id){
            $site = $this->mongo('ReurbanoDealBundle:Site')->find((int)$id);
            if(!$site) throw $this->createNotFoundException ('Nenhum site encontrado com o ID' . $id);
        }else{
            $site = new Site();
        }
        $form = $this->createForm(new SiteType(), $site);
        $request = $this->get('request');
        if('POST' == $request->getMethod()){
            $form->bindRequest($request);
            if($form->isValid()){
                $dm->persist($site);
                $dm->flush();
                $this->get('session')->setFlash('ok', $this->trans(($id) ? "Site Editado" : "Site Criado" ));
                return $this->redirect($this->generateUrl('admin_deal_site_index'));
            }
        }
        return array(
            'form' => $form->createView(),
            'site' => $site,
            'title'=> $title,
        );
    }
    
    /**
     * Exibe um pre delete e deleta se for confirmado
     *
     * @Route("/deletar/{id}", name="admin_deal_site_delete")
     * @Template()
     */
    public function deleteAction($id)
    {
        return array();
    }
}