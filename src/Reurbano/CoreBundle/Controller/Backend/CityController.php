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
     * @Route("/novo", name="admin_core_city_novo")
     * @Route("/editar/{id}", name="admin_core_city_edit")
     * @Route("/salvar/{id}", name="admin_core_city_save", defaults={"id" = null})
     * @Template()
     */
    public function formAction($id = null)
    {
        $dm = $this->dm();
        $title = ($id) ? "Editar Cidade" : "Nova Cidade";
        if($id){
            $city = $this->mongo('ReurbanoCoreBundle:City')->find($id);
            if (!$city) throw $this->createNotFoundException('Nenhuma cidade encontrada com o ID '.$id);
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
     * @Route("/deletar/{id}", name="admin_core_city_delete")
     */
    public function deleteAction($id)
    {
        $dm = $this->dm();
        $city = $this->mongo('ReurbanoCoreBundle:City')->find($id);
        if (!$city) throw $this->createNotFoundException('Nenhuma cidade encontrada com o ID '.$id);
        $dm->remove($city);
        $dm->flush();
        $this->get('session')->setFlash('ok', 'Cidade Deletada!');
        return $this->redirect($this->generateUrl('admin_core_city_index'));
    }
}
