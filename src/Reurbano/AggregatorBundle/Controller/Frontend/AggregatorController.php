<?php

namespace Reurbano\AggregatorBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class AggregatorController extends BaseController
{
    
    /**
     * @Route("/agregador", name="aggregator_aggregator_index", requirements={"_scheme" = "http"})
     * @Template()
     */
    public function indexAction()
    {
        return array('Teste' => 'Teste');
    }
    
    /**
     * Action que busca ofertas
     *
     * @Route("/agregador/busca", name="aggregator_aggregator_search")
     * @Method("GET")
     * @Template()
     */
    public function  searchAction(){
    	$request = $this->getRequest();
    	$q = $request->query->get('q');
    	if(trim($q) == null || trim($q) == "Encontre cupons de compra coletiva"){
    		return $this->redirectFlash($this->generateUrl('aggregator_aggregator_index'), 'Digite um termo para a busca', 'error');
    	}
    	$ret['q'] = $q;
    	$ret['breadcrumbs'][]['title'] = 'Resultado da busca por "'.$q.'" em '.$this->get('session')->get('reurbano.user.cityName');
    	return $ret;
    }
    
    /**
     * Action que lista as ofertas via ajax no agregador
     * 
     * @Route("/agregador/ajax")
     * @Method("POST")
     * @Template()
     */
    public function  ajaxAction(){
        $request = $this->getRequest();
        $cat = $request->request->get('cat');
        $pg = $request->request->get('pg');
        $q = $request->request->get('q');
        $sort = $request->request->get('sort');
        if(!$request->isXmlHttpRequest() || !$pg || !$cat){
            throw new AccessDeniedHttpException('Você não tem permissão para acessar esta página.');
        }
        $ret['search'] = (empty ($q)) ? null : $q;
        $ret['cat'] = ($cat == 'all') ? null : $cat;
        if($pg != 1){
            list($field, $pg) = explode('_', $pg);
        }
        $ret['pg'] = $pg;
        $ret['sort'] = $sort;
        return $ret;
    }
}
