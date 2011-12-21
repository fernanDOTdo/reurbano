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
use Reurbano\DealBundle\Document\Voucher;
use Reurbano\DealBundle\Document\Comission;

use Reurbano\DealBundle\Form\Frontend\DealEditType;

use Reurbano\DealBundle\Util\Upload;

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
     * @Route("/ofertas-em-{city}/{category}/{slug}", name="deal_deal_show", requirements={"_scheme" = "http"})
     * @Template()
     */
    public function showAction($city, $category, Deal $deal)
    {
        // Só por entrar no action já significa que o $deal foi encontrado pelo Slug, mas o if abaixo valida o que está na URL como city e category
        if($deal->getSource()->getCity()->getSlug() != $city || $deal->getSource()->getCategory()->getSlug() != $category){
            throw $this->createNotFoundException('Oferta não encontrada. ');
        }
        // Se o Deal está vendido, inativo ou vencido, procura outro deal semelhante e redireciona
        if($deal->getQuantity() < 1 || $deal->getActive() == false || $deal->getSource()->getExpiresAt()->getTimestamp() < time()){
            if($dealRelated = $this->mongo('ReurbanoDealBundle:Deal')->findRelated($deal)){
                return $this->redirect($this->generateUrl('deal_deal_show', array('city'=>$city, 'category'=>$category, 'slug'=>$dealRelated->getSlug())), 302);
            }
            $ret['dontBuy'] = true;
        }
        // Incrementa views se o user não for admin e se for o primeiro acesso do user à oferta
        if(!$this->hasRole('ROLE_ADMIN') && !$this->get('session')->has('offer_'.$deal->getId())){
            $this->mongo('ReurbanoDealBundle:Deal')->incViews($deal->getId());
            $this->get('session')->set('offer_'.$deal->getId(), 1);
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
    
    /**
     * Edita uma oferta no frontend
     * 
     * @Route("/minha-conta/editar/oferta/{id}", name="deal_deal_edit")
     * @Template()
     */
    public function editAction(Deal $deal)
    {
        $dm = $this->dm();
        $title = "Editar Oferta";
        $request = $this->get('request');
        $form = $this->createForm(new DealEditType(), $deal, array('document_manager' => 'default'));
        $data = $this->get('request')->request->get($form->getName());
        $user = $this->get('security.context')->getToken()->getUser();
        if($user->getId() != $deal->getUser()->getId()){
            return $this->redirectFlash($this->generateUrl('user_dashboard_index').'#mydeals', 'Você não tem permissão para acessar esta página.', 'error');
        }
        // Não deixa editar oferta vencida
        if($deal->getSource()->getExpiresAt()->getTimestamp() < time()){
            return $this->redirectFlash($this->generateUrl('user_dashboard_index').'#mydeals', 'Não é possível editar uma oferta vencida.', 'error');
        }elseif($deal->getQuantity() == 0){
            return $this->redirectFlash($this->generateUrl('user_dashboard_index').'#mydeals', 'Não é possível editar uma oferta vendida.', 'error');
        }
        if($request->getMethod() == 'POST'){
            $mail = $this->get('mastop.mailer');
            //$form->bindRequest($request);
            $source = $deal->getSource();
            //$expiresAt = $data['source']['expiresAt'];
            $category = $data['source']['category'];
            //$expiresDate = new \DateTime(substr($expiresAt, 6, 4).'-'.substr($expiresAt, 3, 2).'-'.substr($expiresAt, 0, 2));
            //if($expiresDate->getTimestamp() < time()){
            //    $mail->notify('Debug: Data inválida', 'O usuário '.$user->getName().' ('.$user->getEmail().') tentou editar uma oferta com uma data inválida: '.$expiresAt.'.<br /><br />Dados técnicos do POST:<br />'.  print_r($data, true));
            //    return $this->redirectFlash($this->generateUrl('deal_deal_edit', array('id' => $deal->getId())), 'A data de validade precisa ser maior que a data de hoje.', 'error');
            //}
            //if($source->getExpiresAt() == '' || $source->getExpiresAt()->format('d/m/Y') != $expiresAt){
                // Seta a validade no Source
            //    $source->setExpiresAt($expiresDate);
            //}
            // Categoria
            $cat = $this->mongo('ReurbanoDealBundle:Category')->find($category);
            if(!$cat){
                $mail->notify('Erro: Categoria não encontrada', 'O usuário '.$user->getName().' ('.$user->getEmail().') tentou editar uma oferta para a categoria ID '.$category.' e ela não foi encontrada no DB.<br /><br />Dados técnicos do POST:<br />'.  print_r($data, true));
                return $this->redirectFlash($this->generateUrl('deal_deal_edit', array('id' => $deal->getId())), 'Categoria não encontrada', 'error');
            }
            $source->setCategory($cat);
            $deal->setSource($source);
            $price = $data['price'];
            $obs = $data['obs'];
            $deal->setObs($obs);
            $deal->setPrice(str_replace(",", ".", $price));
            $deal->setActive(true);
            $deal->setLabel($source->getTitle());
            
            // Comissão
            $comission = new Comission();
            $comission->setSellerpercent($this->get('mastop')->param('deal.all.comsellpercent'));
            $comission->setSellerreal($this->get('mastop')->param('deal.all.comsellreal'));
            $comission->setBuyerpercent($this->get('mastop')->param('deal.all.combuypercent'));
            $comission->setBuyerreal($this->get('mastop')->param('deal.all.combuyreal'));
            $deal->setComission($comission);
            $dm->persist($deal);
            $dm->flush();
            
            return $this->redirectFlash($this->generateUrl('user_dashboard_index').'#mydeals', $this->trans('Oferta editada com sucesso!'));
        }
        return array(
            'form'  => $form->createView(),
            'deal' => $deal,
            'title' => $title,
        );
    }
    
    /**
     * Ativa e Desativa uma oferta
     * 
     * @Route("/minha-conta/ativar/oferta/{id}/{active}", name="deal_deal_active", defaults={"active" = false})
     */
    public function activeAction(Deal $deal, $active = false){
        $user = $this->get('security.context')->getToken()->getUser();
        if($user->getId() != $deal->getUser()->getId()){
            return $this->redirectFlash($this->generateUrl('_home'), 'Você não tem permissão para acessar esta página.', 'error');
        }
        if($deal->getQuantity() == 0 && $active){
            return $this->redirectFlash($this->generateUrl('user_dashboard_index').'#mydeals', 'Não é possível ativar uma oferta vendida.', 'error');
        }
        $dm = $this->dm();
        if($active){
            $deal->setActive(true);
        }else{
            $deal->setActive(false);
            $dm->getRepository('ReurbanoCoreBundle:Banner')->deleteByDeal($deal->getId());
        }
        $dm->persist($deal);
        $dm->flush();
        return $this->redirect($this->generateUrl('user_dashboard_index')."#mydeals");
    }
}