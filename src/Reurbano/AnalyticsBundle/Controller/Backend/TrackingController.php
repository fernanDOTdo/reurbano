<?php
namespace Reurbano\AnalyticsBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\AnalyticsBundle\Document\Tracking;

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
     * Lista todas os tracking de venda
     *
     * @Route("/venda", name="admin_analytics_tracking_sell")
     * @Template()
     */
    public function sellAction()
    {
    	$traces = $this->mongo('ReurbanoAnalyticsBundle:TrackingSell')->findAllByOrder();
    
    	return array('traces' => $traces,'title'=>$this->trans("Listagem de Tracking de vendas"));
    }
    
    /**
     * Lista todas os tracking de pré-venda
     *
     * @Route("/pre-venda", name="admin_analytics_tracking_presell")
     * @Template()
     */
    public function presellAction()
    {
    	$traces = $this->mongo('ReurbanoAnalyticsBundle:TrackingPreSell')->findAllByOrder();
    
    	return array('traces' => $traces,'title'=>$this->trans("Listagem de Tracking de pré-venda"));
    }
    
    
    /**
     * @Route("/export", name="admin_analytics_tracking_export")
     */
    public function exportAction()
    {
    	$traces = $this->mongo('ReurbanoAnalyticsBundle:Tracking')->findAllByOrder();
    	/*$data = "Cidade;Site de origem;Categoria;Oferta;URL;Nome Vendedor;E-mail Vendedor;No estabelecimento;Na compra coletiva;No Reurbano;Data;Expirado;Data Vencimento;Conferido;Destaque;Ativo;Cupons DisponÃ­veis;VisualizaÃ§Ãµes\n";
    	foreach($deal as $deal){
    		$label = $deal->getLabel();
    		$label = preg_replace("'\s+'", ' ', $label);
    		$label = trim($label, ' -');
    		$data .= $deal->getSource()->getCity()->getName() .
    		";" .$deal->getSource()->getSite()->getName() .
    		";" . $deal->getSource()->getCategory()->getName() .
    		";" . $label .
    		";" . $this->generateUrl('deal_deal_show', array('city' => $deal->getSource()->getCity()->getSlug(), 'category' => $deal->getSource()->getCategory()->getSlug(), 'slug' => $deal->getSlug())) .
    		";" . $deal->getUser()->getName() .
    		";" . $deal->getUser()->getEmail() .
    		";" . $deal->getSource()->getPrice() .
    		";" . $deal->getSource()->getPriceOffer() .
    		";" . $deal->getPrice() .
    		";" . $deal->getCreatedAt()->format('d/m/Y') .
    		";" . (($deal->getSource()->getExpiresAt()->getTimestamp() < time()) ? "Sim" : "NÃ£o") .
    		";" . $deal->getSource()->getExpiresAt()->format('d/m/Y') .
    		";" . (($deal->getChecked()) ? "Sim" : "NÃ£o") .
    		";" . (($deal->getSpecial()) ? "Sim" : "NÃ£o") .
    		";" . (($deal->getActive()) ? "Sim" : "NÃ£o") .
    		";" . $deal->getQuantity() .
    		";" . $deal->getViews() . "\n";
    	}
    	*/
    	return new Response($data, 200, array(
    			'Content-Type'        => 'text/csv',
    			'Content-Disposition' => 'attachment; filename=parceiros-tracking_' . date('d_m_Y') . '.csv',
    	));
    }
    
}
