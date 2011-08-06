<?php
namespace Reurbano\DealBundle\Controller\Widget;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Mastop\SystemBundle\Controller\BaseController;

/*
 * Controller para os Widgets do Deal
 */

class DealController extends BaseController
{
    /**
     * @Route("/renderDeal/{categoria}/{quantidade}", name="widget_deal_render_deals")
     * @Template()
     */
    public function renderDealsAction($categoria = null, $quantidade = null)
    {
        
        $userCity = $this->get('session')->get('reurbano.user.city');
        $mongoCity = $this->mongo("ReurbanoCoreBundle:City");
        $mongoDeal = $this->mongo("ReurbanoDealBundle:Deal");
        $mongoSource = $this->mongo("ReurbanoDealBundle:Source");
        $mongoCategory = $this->mongo("ReurbanoDealBundle:Category");
        
        $city = $mongoCity->findBySlug($userCity);
        $categoria = (!is_null($categoria))? $categoria : "beleza-e-saude" ;
        $quantidade = (!is_null($quantidade))? $quantidade : 4 ;
        $title = 'Administração de Ofertas';
        
        if (count($city) == 0){
            $city = $mongoCity->findBySlug("oferta-nacional");
            $cityId = $city->getId();
        }
        
        $categoria = $mongoCategory->findBySlug($categoria);
        
        $cityId = $city->getId();
        $categoryId = $categoria->getId();
        
        echo count($categoria);
        echo "<br /><br />";
        echo $categoria->getId();
        echo "<br /><br />";
        $source = $mongoSource->findByCategoryCity($categoryId, $cityId);
        //$source = $this->mongo('ReurbanoDealBundle:Source')->findAllByCreated();
        $deals = array();
        $i = 1;
        foreach($source as $k => $v){
            $deal = $mongoDeal->findBySource(new \MongoId($v->getId()));
            if ($i <= $quantidade){
                foreach($deal as $kDeal => $vDeal){
                    if ($i <= $quantidade){
                        $voucher = $vDeal->getVoucher();
                        $deals[$kDeal]['label'] = $vDeal->getLabel();
                        $deals[$kDeal]['price'] = $v->getPrice();
                        $deals[$kDeal]['offerPrice'] = $vDeal->getPrice();
                        $deals[$kDeal]['slug'] = $vDeal->getSlug();
                        $deals[$kDeal]['category'] = $categoria;
                        $deals[$kDeal]['image'] = (count($voucher) > 0) ? $voucher[0]->getFilename() : 'sem-imagem.jpg';
                        //echo $voucher[0]->getFilename();
                        echo $vDeal->getSlug()."<br />";
                    }
                    $i++;
                }
            }
        }
        echo count($source);
        //$ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findByCategory($categoria);
        return array('title' => $title);
    }
    
}