<?php

namespace Reurbano\CoreBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\CoreBundle\Form\BannerType;
use Reurbano\CoreBundle\Document\Banner;
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
            'current' => 'admin_deal_deal_index',);
    }
    
    /**
     * Adiciona um novo, edita um já criado e salva ambos
     * 
     * @Route("/novo", name="admin_core_banner_new")
     * @Route("/editar/{id}", name="admin_core_banner_edit")
     * @Route("/salvar/{id}", name="admin_core_banner_save", defaults={"id" = null})
     * @Template()
     */
    public function bannerAction($id = null)
    {
        $dm = $this->dm();
        $title = ($id) ? "Editar Banner" : "Novo Banner";
        if($id){
            $banner = $this->mongo('ReurbanoDealBundle:Banner')->find((int)$id);
            if(!$banner) throw $this->createNotFoundException ('Nenhum banner encontrado com o ID' . $id);
        }else{
            $banner = new Banner();
        }
        $form = $this->createForm(new BannerType(), $banner);
        $request = $this->get('request');
        if('POST' == $request->getMethod()){
            $form->bindRequest($request);
            $data = $request->request->get($form->getName());
            $fileData = $request->files->get($form->getName());
            
            if($fileData['logo'] != null){
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
}