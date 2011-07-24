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
        
        if ($city){
            echo "<pre>";
            $deal = $this->mongo('ReurbanoDealBundle:Deal')->findByCity($userCity);
            foreach($deal as $k => $v){
                echo $v->getPrice();
            }
            echo "</pre>";
        }else {
            echo "Não tem a cidade";
        }
        
        echo "<pre>";
        print_r($city->getName());
        echo "</pre>";
        
        $title = 'Administração de Ofertas';
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