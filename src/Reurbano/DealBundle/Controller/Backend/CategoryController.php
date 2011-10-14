<?php

namespace Reurbano\DealBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\DealBundle\Document\Category;
use Reurbano\DealBundle\Form\Backend\CategoryType;

/**
 * Controller para administrar (CRUD) categorias.
 */
class CategoryController extends BaseController {

    /**
     * Lista todas as categorias
     * 
     * @Route("/", name="admin_deal_category_index")
     * @Template()
     */
    public function indexAction() {
        $title = 'Administração de Categorias';
        $categorias = $this->mongo('ReurbanoDealBundle:Category')->findAllByOrder();
        return array(
            'categorias' => $categorias,
            'title' => $title,
            'current' => 'admin_deal_deal_index',
        );
    }

    /**
     * Adiciona um novo, edita um já criado e salva ambos
     * 
     * @Route("/novo", name="admin_deal_category_new")
     * @Route("/editar/{slug}", name="admin_deal_category_edit")
     * @Route("/salvar/{slug}", name="admin_deal_category_save", defaults={"slug" = null})
     * @Template()
     */
    public function categoryAction($slug = null) {
        $dm = $this->dm();
        $title = ($slug) ? "Editar Categoria" : "Nova Categoria";
        $query = array('slug' => $slug);
        if ($slug) {
            $cat = $this->mongo('ReurbanoDealBundle:Category')->findOneBy($query);
            if (!$cat)
                throw $this->createNotFoundException('Nenhuma categoria encontrada com o nome ' . $slug);
        }else {
            $cat = new Category();
        }
        $form = $this->createForm(new CategoryType(), $cat);
        $request = $this->get('request');
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $dm->persist($cat);
                $dm->flush();
                $this->get('session')->setFlash('ok', $this->trans(($slug) ? "Categoria Editada" : "Categoria Criada" ));
                return $this->redirect($this->generateUrl('admin_deal_category_index'));
            }
        }
        return array(
            'form' => $form->createView(),
            'cat' => $cat,
            'title' => $title,
            'breadcrumbs'=>array(1=>array('name'=>$this->trans('Categorias'),'url'=>$this->generateUrl('admin_deal_category_index'))),
            'current' => 'admin_deal_deal_index',);
    }

    /**
     * Exibe um pre delete e deleta se for confirmado
     * 
     * @Route("/deletar/{id}", name="admin_deal_category_delete")
     * @Template()
     */
    public function deleteAction($id) {
        // Adicionado para impedir remoção por causa do crawler
        return $this->redirectFlash($this->generateUrl('admin_deal_category_index'), 'Não é possível remover categorias devido à integração com o Crawler.', 'error');
        $request = $this->get('request');
        $dm = $this->dm();
        $cat = $this->mongo('ReurbanoDealBundle:Category')->find($id);
        if ($request->getMethod() == 'POST') {
            if (!$cat)
                throw $this->createNotFoundException($this->trans('Nenhuma categoria encontrada com o ID %id%'), array("%id$" => $id));
            $dm->remove($cat);
            $dm->flush();
            $this->get('session')->setFlash('ok', $this->trans('Categoria deletada'));
            return $this->redirect($this->generateUrl('admin_deal_category_index'));
        }
        return $this->confirm($this->trans('Tem certeza que deseja remover a categoria %name%?', array("%name%" => $cat->getName())), array('id' => $cat->getId()));
    }

}