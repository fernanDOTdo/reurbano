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
        return array('cidades' => $cidades,'title'=>$this->trans("Listagem de Cidades"));
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
        $msg = ($city) ? "Cidade Alterada" : "Cidade Criada";
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
                return $this->redirectFlash($this->generateUrl('admin_core_city_index'), $msg);
            }
        }
        return array('form' => $form->createView(), 'city' => $city, 'title'=>$title, 'current'=>'admin_core_city_index');
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
            return $this->redirectFlash($this->generateUrl('admin_core_city_index'), 'Cidade Deletada!');
        }
        return $this->confirm('Tem certeza de que deseja remover a cidade "' . $city->getName() . '"?', array('id' => $city->getId()));
    }
}
