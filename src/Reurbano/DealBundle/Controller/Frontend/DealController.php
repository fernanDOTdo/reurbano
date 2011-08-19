<?php
/**
 * src/Reurbano/DealBundle/Controller/Frontend/DealController.php
 * 
 * Controller que cuidará das Ofertas em Frontend.
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

namespace Reurbano\DealBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Reurbano\DealBundle\Document\Deal;

class DealController extends BaseController
{
    
    
    /**
     * Action que lista as ofertas via ajax
     * 
     * @Route("/ofertas/ajax")
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
    /**
     * Action que busca ofertas
     * 
     * @Route("/busca", name="deal_deal_search")
     * @Method("GET")
     * @Template()
     */
    public function  searchAction(){
        $request = $this->getRequest();
        $q = $request->query->get('q');
        if(trim($q) == null || trim($q) == "Encontre cupons de compra coletiva"){
            return $this->redirectFlash($this->generateUrl('_home'), 'Digite um termo para a busca', 'error');
        }
        $ret['q'] = $q;
        $ret['breadcrumbs'][]['title'] = 'Resultado da busca por "'.$q.'" em '.$this->get('session')->get('reurbano.user.cityName');
        return $ret;
    }
    
    /**
     * Action que exibe uma oferta
     * 
     * @Route("/ofertas-em-{city}/{category}/{slug}", name="deal_deal_show")
     * @Template()
     */
    public function showAction($city, $category, Deal $deal)
    {
        // Só por entrar no action já significa que o $deal foi encontrado pelo Slug, mas o if abaixo valida o que está na URL como city e category
        if($deal->getSource()->getCity()->getSlug() != $city || $deal->getSource()->getCategory()->getSlug() != $category){
            throw $this->createNotFoundException('Oferta não encontrada.');
        }
        $title = $deal->getLabel();
        $breadcrumbs[] = array('title'=>$deal->getSource()->getCity()->getName(), 'url' => $this->generateUrl('core_city_index', array('slug' => $deal->getSource()->getCity()->getSlug())));
        $breadcrumbs[] = array('title'=>$deal->getSource()->getCategory()->getName(), 'url' => $this->generateUrl('deal_category_index', array('city' => $deal->getSource()->getCity()->getSlug(), 'slug'=>$deal->getSource()->getCategory()->getSlug())));
        $breadcrumbs[] = array('title' => (isset ($title[90])) ? substr($title, 0, 90).'...' : $title);
        $ret['deal'] = $deal;
        $ret['city'] = $city;
        $ret['cat'] = $deal->getSource()->getCategory();
        $ret['title'] = $deal->getLabel();
        $ret['rules'] = ($deal->getSource()->getRules() != '') ? preg_split( '/\r\n|\r|\n/', $deal->getSource()->getRules()) : array();
        $ret['breadcrumbs'] = $breadcrumbs;
        $ret['keywords'] = implode(', ', explode(' ', $deal->getLabel()));
        return $ret;
    }
}