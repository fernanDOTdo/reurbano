<?php

namespace Reurbano\DealBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\DealBundle\Document\Site;
use Reurbano\DealBundle\Form\Backend\SiteType;
use Reurbano\DealBundle\Util\Upload;

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
        return array(
            'site'    => $site,
            'title'   => $title,
            'current' => 'admin_deal_deal_index',);
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
            $data = $request->request->get($form->getName());
            $fileData = $request->files->get($form->getName());
            
            if($fileData['logo'] != null){
                $file = new Upload($fileData['logo']);
                $file->setPath($this->get('kernel')->getRootDir() . "/../web/uploads/reurbanodeal");
                $fileUploaded = $file->upload();
                $site->setFilename($fileUploaded->getFileName());
                $site->setFilesize($fileUploaded->getFileUploaded()->getClientSize());
                if ($file->getPath() != ""){
                    $site->setPath($fileUploaded->getPath());
                }else {
                    $site->setPath($fileUploaded->getDeafaultPath());
                }
            }
            
            
            $dm->persist($site);
            $dm->flush();
            $this->get('session')->setFlash('ok', $this->trans(($id) ? "Site Editado" : "Site Criado" ));
            return $this->redirect($this->generateUrl('admin_deal_site_index'));
            
        }
        return array(
            'form'    => $form->createView(),
            'site'    => $site,
            'title'   => $title,
            'breadcrumbs'=>array(1=>array('name'=>$this->trans('Sites'),'url'=>$this->generateUrl('admin_deal_site_index'))),
            'current' => 'admin_deal_deal_index',
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
        $request = $this->get('request');
        $dm = $this->dm();
        $site = $this->mongo('ReurbanoDealBundle:Site')->find((int)$id);
        if($request->getMethod() == 'POST'){
            if(!$site) throw $this->createNotFoundException ('Nenhum site encontrado com o ID' . $id);
            $dm->remove($site);
            $dm->flush();
            $this->get('session')->setFlash('ok', $this->trans('Site deletado'));
            return $this->redirect($this->generateUrl('admin_deal_site_index'));
        }
        return array(
            'name'    => $site->getName(),
            'id'      => $site->getId(),
            'current' => 'admin_deal_deal_index',
        );
    }
}