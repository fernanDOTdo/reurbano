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
        $city = $this->mongo("ReurbanoCoreBundle:City")->hasId($userCity);
        
        // Verificação se a cidade do usuário existe
        if ($city){
            // Verificação se existe alguma oferta na cidade do usuário
            $deal = $this->mongo('ReurbanoDealBundle:Deal')->findByCity($userCity);
            if ($deal){
                // Exibe as da cidade do usuário
                return array("deal" => $deal);
            }else {
                // Exibe as ofertas nacionais
                /*$deal = $this->mongo('ReurbanoDealBundle:Deal')->findByCity("oferta-nacional");*/
            }
        }else {
            // Exibição das ofertas nacionais
            /*$deal = $this->mongo('ReurbanoDealBundle:Deal')->findByCity("oferta-nacional");*/
        }
        
        
        /*echo "<pre>";
        print_r($city->getName());
        echo "</pre>";*/
        
        $title = 'Listagem de Ofertas';
        //$ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findAll();
        
        $ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findAllByCreated();
        return array('ofertas' => $ofertas, 'title' => $title);
        //return array();
    }
    /**
     * Action que exibe uma oferta
     * 
     * @Route("/{ofera}", name="deal_deal_show")
     * @Template()
     */
    public function showAction($oferta)
    {
        return array('oferta' => $ofera);
    }
}