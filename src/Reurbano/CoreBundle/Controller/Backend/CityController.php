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
     * @Route("/form/{slug}", name="admin_core_city_form", defaults={"slug" = null})
     * @Template()
     */
    public function formAction(City $city = null)
    {
        $dm = $this->dm();
        $title = ($city) ? "Editar Cidade" : "Nova Cidade";
        if(!$city){
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
    public function deleteAction(City $city)
    {
        if($this->get('request')->getMethod() == 'POST'){
            $this->dm()->remove($city);
            $this->dm()->flush();
            $this->get('session')->setFlash('ok', 'Cidade Deletada!');
            return $this->redirect($this->generateUrl('admin_core_city_index'));
        }
        return $this->confirm('Tem certeza de que deseja remover a cidade "' . $city->getName() . '"?', array('id' => $city->getId()));
    }
}
