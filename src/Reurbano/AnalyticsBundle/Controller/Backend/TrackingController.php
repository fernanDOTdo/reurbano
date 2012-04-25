<?php
namespace Reurbano\AnalyticsBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\AnalyticsBundle\Document\Tracking;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller para administrar (CRUD) os tracking do parceiros.
 */

class TrackingController extends BaseController
{
    /**
     * Lista todas os tracking
     * 
     * @Route("/", name="admin_analytics_tracking_index")
     * @Template()
     */
    public function indexAction()
    {
        $traces = $this->mongo('ReurbanoAnalyticsBundle:Tracking')->findAllByOrder();
        
        return array('traces' => $traces,'title'=>$this->trans("Listagem de Tracking"));
    }
    
    /**
     * Action de view das informações do Tracking
     *
     * @Route("/informacoes/{id}", name="admin_analytics_tracking_info")
     * @Template()
     */
    public function infoAction(Tracking $tracking){
    	$title = $this->trans("Informações do Tracking");
    	$taxaPrevendas = 0;
    	$taxaVendas = 0;
    	
    	
    	$traces = $this->mongo('ReurbanoAnalyticsBundle:Tracking')->hasId($tracking->getId());
    	$trackingPreSell = $this->mongo('ReurbanoAnalyticsBundle:TrackingPreSell')->getByFindSingleResult('tracking.$id', new \MongoId($tracking->getId()) );
    	$trackingSell = array();
    	if (sizeof($trackingPreSell) > 0) $trackingSell = $this->mongo('ReurbanoAnalyticsBundle:TrackingSell')->getByFindSingleResult('trackingPreSell.$id', new \MongoId($trackingPreSell->getId()) );
    	
    	$prevendas = (sizeof($trackingPreSell) > 0) ? $trackingPreSell->getClick() : 0;
    	$vendas = (sizeof($trackingSell) > 0) ? $trackingSell->getClick() : 0;

    	if ($tracking->getClick() > 0){
	    	$taxaPrevendas = ( $prevendas / $tracking->getClick()) * 100.00;
	    	$taxaVendas = ( $vendas / $tracking->getClick()) * 100.00;
    	}
    	
    	return array(
    			'title' => $title,
    			'tracking' => $traces,
    			'prevendas' => $prevendas,
    			'vendas' => $vendas,
    			'taxaPrevendas' => $taxaPrevendas,
    			'taxaVendas' => $taxaVendas,
    	);
    }
        
    /**
     * @Route("/export", name="admin_analytics_tracking_export")
     */
	public function exportAction()
    {
    	$traces = $this->mongo('ReurbanoAnalyticsBundle:Tracking')->findAllByOrder();
    	
    	$data = "Parceiro;Oferta;Categoria;Cidade;URL;Visualizações;Pré-venda;Venda;Em Cookie;Criação;Atualização;\n";
    	
    	foreach($traces as $tracking){
    		$clickPreSell = 0;
    		$clickSell = 0;
    		$trackingPreSell = $this->mongo('ReurbanoAnalyticsBundle:TrackingPreSell')->getByFindSingleResult('trackingPreSell.$id', new \MongoId($tracking->getId()) );
    		if (sizeof($trackingPreSell) > 0){
    			$clickPreSell = $trackingPreSell->getClick();
    			$trackingSell = $this->mongo('ReurbanoAnalyticsBundle:TrackingSell')->getByFindSingleResult('trackingPreSell.$id', new \MongoId($trackingPreSell->getId()) );
    			if (sizeof($trackingSell) > 0) { $clickSell = $trackingSell->getClick();}
    		}
    		
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
    		";" . $clickPreSell .
    		";" . $clickSell  .
    		";" . $tracking->isInCookie() .
    		";" . $tracking->getCreatedAt()->format('d/m/Y') .
    		";" . $tracking->getUpdatedAt()->format('d/m/Y H:i:s') . "\n";
    	}
    	
    	return new Response(utf8_decode($data), 200, array(
    			'Content-Type'        => 'text/csv; charset=UTF-8',
    			'Content-Disposition' => 'attachment; filename=rastreamento-visualizacoes_' . date('d_m_Y') . '.csv',
    	));
    }    
}
