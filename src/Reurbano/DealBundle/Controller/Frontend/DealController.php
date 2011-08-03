<?php
namespace Reurbano\DealBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controller que cuidará das Ofertas em Frontend.
 */

class DealController extends BaseController
{
    /**
     * Action que lista as ofertas na cidade do usuário
     * 
     * @Route("/", name="deal_deal_index")
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
            foreach($city as $k => $v){
                $cityId = $k;
            }
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
     * @Route("/oferta/{category}/{oferta}", name="deal_deal_show")
     * @Template()
     */
    public function showAction($category, $oferta)
    {
        $deal = $this->mongo("ReurbanoDealBundle:Deal")->findBySlug($oferta);
        $title = $deal->getLabel();
        
        return array('oferta' => $deal, 
                     'title'  => $title);
    }
}