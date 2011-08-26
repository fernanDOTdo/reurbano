<?php

namespace Reurbano\CoreBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\CoreBundle\Form\BannerType;
use Reurbano\CoreBundle\Form\BannerOfferType;
use Reurbano\CoreBundle\Document\Banner;
use Reurbano\DealBundle\Document\Deal;
use Reurbano\CoreBundle\Util\Upload;

/**
 * Controller para administrar (CRUD) Banners.
 */

class BannerController extends BaseController
{
    /**
     * Lista todas os Banners
     * 
     * @Route("/", name="admin_core_banner_index")
     * @Template()
     */
    public function indexAction()
    {
        $title = 'Administração de Banners';
        $banner = $this->mongo('ReurbanoCoreBundle:Banner')->findAll();
        return array(
            'banner'    => $banner,
            'title'   => $title,
            'current' => 'admin_core_banner_index',);
    }
    
    /**
     * Adiciona um novo, edita um já criado e salva ambos
     * control = false Banner normal
     * control = true Oferta
     * 
     * @Route("/novo/{control}", name="admin_core_banner_new", defaults={"control" = false})
     * @Route("/editar/{id}/{control}", name="admin_core_banner_edit", defaults={"control" = false})
     * @Route("/salvar/{id}", name="admin_core_banner_save", defaults={"id" = null})
     * @Template()
     */
    public function bannerAction($id = null, $control = false)
    {
        $dm = $this->dm();
        $title = ($id) ? "Editar Banner" : "Novo Banner";
        if($id){
            $banner = $this->mongo('ReurbanoCoreBundle:Banner')->find($id);
            if(!$banner) throw $this->createNotFoundException ('Nenhum banner encontrado com o ID: "' . $id . '"');
        }else{
            $banner = new Banner();
            $banner->setUrl('http://');
        }
        $formType = (!$control) ? new BannerType() : new BannerOfferType();
        $form = $this->createForm($formType, $banner);
        $request = $this->get('request');
        if('POST' == $request->getMethod()){
            $form->bindRequest($request);
            $query = $request->request->get($form->getName());
            if(isset($query['deal'])){
                $deal = $this->mongo('ReurbanoDealBundle:Deal')->find($query['deal']);
                $banner->setDeal($deal);
            }
            $data = $request->request->get($form->getName());
            $fileData = $request->files->get($form->getName());
            if($fileData['logo'] != null){
                if($id){
                    @unlink($banner->getPath() . "/" . $banner->getFileName());
                }
                $file = new Upload($fileData['logo']);
                $file->setPath($this->get('kernel')->getRootDir() . "/../web/uploads/reurbanocore");
                $fileUploaded = $file->upload();
                $banner->setFilename($fileUploaded->getFileName());
                $banner->setFilesize($fileUploaded->getFileUploaded()->getClientSize());
                if ($file->getPath() != ""){
                    $banner->setPath($fileUploaded->getPath());
                }else {
                    $banner->setPath($fileUploaded->getDeafaultPath());
                }
            }
            $dm->persist($banner);
            $dm->flush();
            $this->get('session')->setFlash('ok', $this->trans(($id) ? "Banner Editado" : "Banner Criado" ));
            return $this->redirect($this->generateUrl('admin_core_banner_index'));
            
        }
        return array(
            'form'     => $form->createView(),
            'banner'   => $banner,
            'title'    => $title,
            'current'  => 'admin_core_banner_index',
            'breadcrumbs' =>array(1=>array('name'=>$this->trans('Banner'),'url'=>$this->generateUrl('admin_core_banner_index'))),
        );
    }
    
    /**
     * Exibe um pre delete e deleta se for confirmado
     *
     * @Route("/deletar/{id}", name="admin_core_banner_delete")
     * @Template()
     */
    public function deleteAction($id)
    {
        $request = $this->get('request');
        $dm = $this->dm();
        $banner = $this->mongo('ReurbanoCoreBundle:Banner')->find($id);
        if($request->getMethod() == 'POST'){
            if(!$banner) throw $this->createNotFoundException ('Nenhum banner encontrado com o ID' . $id);
            @unlink($banner->getPath() . "/" . $banner->getFileName());
            $dm->remove($banner);
            $dm->flush();
            $this->get('session')->setFlash('ok', $this->trans('Banner deletado'));
            return $this->redirect($this->generateUrl('admin_core_banner_index'));
        }
        return array(
            'title'    => $banner->getTitle(),
            'id'      => $banner->getId(),
            'current' => 'admin_core_banner_index',
        );
    }
    
    /**
     * Ativa e Desativa um banner
     * 
     * @Route("/ativar/{id}/{active}", name="admin_core_banner_active", defaults={"active" = false})
     */
    public function activeAction($id, $active = false){
        $dm = $this->dm();
        $banner = $this->mongo('ReurbanoCoreBundle:Banner')->find($id);
        ($active) ? $banner->setActive(true) : $banner->setActive(false);
        $dm->persist($banner);
        $dm->flush();
        return $this->redirect($this->generateUrl('admin_core_banner_index'));
    }
}