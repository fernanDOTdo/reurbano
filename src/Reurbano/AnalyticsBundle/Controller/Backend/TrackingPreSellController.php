<?php
namespace Reurbano\AnalyticsBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\AnalyticsBundle\Document\TrackingPreSell;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller para administrar (CRUD) os tracking do parceiros.
 */

class TrackingPreSellController extends BaseController
{
    /**
     * Lista todas os tracking de pré-venda
     *
     * @Route("/", name="admin_analytics_tracking_presell_index")
     * @Template()
     */
    public function indexAction()
    {
    	$traces = $this->mongo('ReurbanoAnalyticsBundle:TrackingPreSell')->findAllByOrder();
    
    	return array('traces' => $traces,'title'=>$this->trans("Listagem de Tracking de pré-venda"));
    }
        
    /**
     * Action de view das informações do Tracking
     *
     * @Route("/informacoes/{id}", name="admin_analytics_tracking_pre_sell_info")
     * @Template()
     */
    public function infoAction(TrackingPreSell $trackingPreSell){
    	$title = $this->trans("Informações do Tracking pré-venda");
    	$taxaPrevendas = 0;
    	$taxaVendas = 0;
    	$trackingSell = array();
    	
   		$trackingSell = $this->mongo('ReurbanoAnalyticsBundle:TrackingSell')->getByFindSingleResult('trackingPreSell.$id', new \MongoId($trackingPreSell->getId()) );
   		$tracking = $this->mongo('ReurbanoAnalyticsBundle:Tracking')->getByFindSingleResult('_id', new \MongoId($trackingPreSell->getTracking()->getId()) );
    	
    	$prevendas = (sizeof($trackingPreSell) > 0) ? $trackingPreSell->getClick() : 0;
    	$vendas = (sizeof($trackingSell) > 0) ? $trackingSell->getClick() : 0;
    
    	if ($tracking->getClick() > 0){
    		$taxaPrevendas = ( $prevendas / $tracking->getClick()) * 100.00;
    		$taxaVendas = ( $vendas / $tracking->getClick()) * 100.00;
    	}
    	 
    	return array(
    			'title' => $title,
    			'tracking' => $tracking,
    			'trackingPreSell' => $trackingPreSell,
    			'prevendas' => $prevendas,
    			'vendas' => $vendas,
    			'taxaPrevendas' => $taxaPrevendas,
    			'taxaVendas' => $taxaVendas,
    	);
    }
    
    /**
     * @Route("/export", name="admin_analytics_tracking_pre_sell_export")
     */
    public function exportAction()
    {
    	$traces = $this->mongo('ReurbanoAnalyticsBundle:TrackingPreSell')->findAllByOrder();
    	
    	$data = "Parceiro;Oferta;Categoria;Cidade;URL;Visualizações;Pré-venda;Venda;Em Cookie;Criação;Atualização;\n";
    	
    	foreach($traces as $trace){
    		$trackingSell = $this->mongo('ReurbanoAnalyticsBundle:TrackingSell')->getByFindSingleResult('trackingPreSell.$id', new \MongoId($trace->getId()) );
    		$tracking = $trace->getTracking();
    		
    		$label = $tracking->getDeal()->getLabel();
    		$label = preg_replace("'\s+'", ' ', $label);
    		$label = trim($label, ' -');
    		
    		$associate = $tracking->getAssociate()->getFancyName();
    		$associate = preg_replace("'\s+'", ' ', $associate);
    		$associate = trim($associate, ' -');
    		
    		$data .= $associate .
    		";" . $label .
    		";" . $tracking->getDeal()->getSource()->getCategory()->getName() .
    		";" . $tracking->getDeal()->getSource()->getCity()->getName() .
    		";" . $this->generateUrl('deal_deal_show', array('city' => $tracking->getDeal()->getSource()->getCity()->getSlug(), 'category' => $tracking->getDeal()->getSource()->getCategory()->getSlug(), 'slug' => $tracking->getDeal()->getSlug())) .
    		";" . $tracking->getClick() .
    		";" . $trace->getClick() .
    		";" . ( (sizeof($trackingSell) > 0) ? $trackingSell->getClick() : 0)  .
    		";" . $trace->isInCookie() .
    		";" . $trace->getCreatedAt()->format('d/m/Y') .
    		";" . $trace->getUpdatedAt()->format('d/m/Y H:i:s') . "\n";
    	}
    	
    	return new Response(utf8_decode($data), 200, array(
    			'Content-Type'        => 'text/csv; charset=UTF-8',
    			'Content-Disposition' => 'attachment; filename=rastreamento-pre-venda_' . date('d_m_Y') . '.csv',
    	));
    }
    
}
