<?php
namespace Reurbano\CoreBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\CoreBundle\Document\City;
use Reurbano\CoreBundle\Form\CityType;

/**
 * Controller para administrar (CRUD) cidades.
 */

class CityController extends BaseController
{
    /**
     * Lista todas as cidades
     * 
     * @Route("/", name="admin_core_city_index")
     * @Template()
     */
    public function indexAction()
    {
        //$cidades = $this->mongo('ReurbanoCoreBundle:City')->findAll();
        $cidades = $this->mongo('ReurbanoCoreBundle:City')->findAllByOrder();
        return array('cidades' => $cidades);
    }
    /**
     * Adiciona um novo, edita um já criado e salva ambos
     * 
     * @Route("/novo", name="admin_core_city_novo")
     * @Route("/editar/{slug}", name="admin_core_city_edit")
     * @Route("/salvar/{slug}", name="admin_core_city_save", defaults={"slug" = null})
     * @Template()
     */
    public function formAction($slug = null)
    {
        $dm = $this->dm();
        $title = ($slug) ? "Editar Cidade" : "Nova Cidade";
        $query = array('slug' => $slug);
        if($slug){
            $city = $this->mongo('ReurbanoCoreBundle:City')->findOneBy($query);
            if (!$city) throw $this->createNotFoundException('Nenhuma cidade encontrada com o nome '.$slug);
        }else{
            $city = new City();
        }
        $form = $this->createForm(new CityType(), $city);
        $request = $this->get('request');
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $dm->persist($city);
                $dm->flush();
                $this->get('session')->setFlash('ok', 'Cidade Criada!');
                return $this->redirect($this->generateUrl('admin_core_city_index'));
            }
        }
        return array('form' => $form->createView(), 'city' => $city, 'title'=>$title);
    }
    /**
     * Exibe um pre delete e deleta se for confirmado
     * 
     * @Route("/deletar/{id}", name="admin_core_city_delete")
     * @Template()
     */
    public function deleteAction($id)
    {
        $request = $this->get('request');
        $formResult = $request->request;
        $dm = $this->dm();
        $city = $this->mongo('ReurbanoCoreBundle:City')->find($id);
        if($request->getMethod() == 'POST'){
            if (!$city) 
                throw $this->createNotFoundException('Nenhuma cidade encontrada com o ID '.$id);
            $dm->remove($city);
            $dm->flush();
            $this->get('session')->setFlash('ok', 'Cidade Deletada!');
            return $this->redirect($this->generateUrl('admin_core_city_index'));
        }
        return array(
            'name' => $city->getName(),
            'id'   => $city->getId(),
        );
    }
}
