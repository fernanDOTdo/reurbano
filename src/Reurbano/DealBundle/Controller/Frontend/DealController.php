<?php
namespace Reurbano\DealBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\DealBundle\Documents\Source;

/**
 * Controller que cuidará das Ofertas em Frontend.
 */

class DealController extends BaseController
{
    /**
     * Action que lista as ofertas na cidade do usuário
     * 
     * @Route("/ofertas", name="deal_deal_index")
     * @Template()
     */
    public function indexAction()
    {
        $userCity = $this->get('session')->get('reurbano.user.city');
        $mongoCity = $this->mongo("ReurbanoCoreBundle:City");
        $mongoDeal = $this->mongo("ReurbanoDealBundle:Deal");
        $city = $mongoCity->findBySlug($userCity);
        $cityId = $city->getId();
        $title = "Lista de Ofertas em ". $city->getName();
        // Verificação se a cidade do usuário existe
        if (count($city) > 0){
            // Verificação se existe alguma oferta na cidade do usuário
            
            $deal = $mongoDeal->findByCity(new \MongoId($cityId));

            foreach ($deal as $k => $v){
                $precoDe = str_ireplace(',', '', $v->getSource()->getPrice())/100;
                $precoPor = $v->getPrice()/100;
                $v->getSource()->setPrice(number_format($precoDe, 2, ',', '.'));
                $v->setPrice(number_format($precoPor, 2, ',', '.'));
            }
            
            if (count($deal) == 0){
                $title = "Lista de Ofertas Nacionais";
                // Caso não exista, exibe as ofertas nacionais
                $city = $mongoCity->findBySlug("oferta-nacional");
                $cityId = $city->getId();
                $deal = $mongoDeal->findByCity(new \MongoId($cityId));
                
                foreach($deal as $k => $v){
                    $this->mastop()->log('Slug de '.$v->getLabel().':'.$v->getSlug().'');
                    //echo $v->getLabel();
                }
                
            }
        }else {
            // Caso não exista, exibe as ofertas nacionais
            $title = "Lista de Ofertas Nacionais";
            
            $city = $mongoCity->findBySlug("oferta-nacional");
            $cityId = $city->getId();
            $deal = $mongoDeal->findByCity(new \MongoId($cityId));
            
        }
        
        
        return array('ofertas' => $deal, 'title' => $title);
        
        /*echo "<pre>";
        print_r($city->getName());
        echo "</pre>";*/
        
        $title = 'Listagem de Ofertas ' . $userCity;
        //$ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findAll();
        
        $ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findAllByCreated();
        return array('ofertas' => $ofertas, 'title' => $title);
        //return array();
    }
    /**
     * Action que exibe uma oferta
     * 
     * @Route("/ofertas-em-{city}/{category}/{slug}", name="deal_deal_show")
     * @Template()
     */
    public function showAction($city, $category, $slug)
    {
        $deal = $this->mongo("ReurbanoDealBundle:Deal")->findBySlug($slug);
        
        if (!$deal){
            $this->get('session')->setFlash('notice', 'Oferta não encontrada');
            return $this->redirect($this->generateUrl('deal_deal_index'));
        }
        $precoDe = str_ireplace(',', '', $deal->getSource()->getPrice())/100;
        $precoPor = $deal->getPrice()/100;
        $deal->getSource()->setPrice(number_format($precoDe, 2, ',', '.'));
        $deal->setPrice(number_format($precoPor, 2, ',', '.'));
        $title = $deal->getLabel();
        
        return array('oferta' => $deal, 
                     'title'  => $title);
    }
}