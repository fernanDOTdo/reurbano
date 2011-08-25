<?php

/**
 * Reurbano/CoreBundle/Controller/Backend/ContentController.php
 *
 * Controller para CRUD de páginas estáticas
 *  
 * 
 * @copyright 2011 Mastop Internet Development.
 * @link http://www.mastop.com.br
 * @author Fernando Santos <o@fernan.do>
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace Reurbano\CoreBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\CoreBundle\Document\Content;
use Reurbano\CoreBundle\Form\ContentType;


class ContentController extends BaseController
{
    /**
     * Lista todas as páginas
     * 
     * @Route("/", name="admin_core_content_index")
     * @Template()
     */
    public function indexAction()
    {
        $content = $this->mongo('ReurbanoCoreBundle:Content')->findAllByCreated();
        return array('contents' => $content,'title'=>$this->trans("Listagem de Páginas Estáticas"));
    }
    /**
     * Adiciona um novo, edita um já criado e salva ambos
     * 
     * @Route("/form/{slug}", name="admin_core_content_form", defaults={"slug" = null})
     * @Template()
     */
    public function formAction(Content $content = null)
    {
        $dm = $this->dm();
        $title = ($content) ? "Editar Página" : "Nova Página";
        $msg = ($content) ? "Página Alterada" : "Página Criada";
        if(!$content){
            $content = new Content();
        }
        $form = $this->createForm(new ContentType(), $content);
        $request = $this->get('request');
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $dm->persist($content);
                $dm->flush();
                return $this->redirectFlash($this->generateUrl('admin_core_content_index'), $msg);
            }
        }
        return array('form' => $form->createView(), 'content' => $content, 'title'=>$title, 'current' => 'admin_core_content_index');
    }
    /**
     * Exibe um pre delete e deleta se for confirmado
     * 
     * @Route("/deletar/{id}", name="admin_core_content_delete")
     * @Template()
     */
    public function deleteAction(Content $content)
    {
        if($this->get('request')->getMethod() == 'POST'){
            $this->dm()->remove($content);
            $this->dm()->flush();
            return $this->redirectFlash($this->generateUrl('admin_core_content_index'), 'Página Deletada!');
        }
        return $this->confirm('Tem certeza de que deseja remover a página "' . $content->getTitle() . '"?', array('id' => $content->getId()));
    }
}