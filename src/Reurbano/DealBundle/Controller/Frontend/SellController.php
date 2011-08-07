<?php
namespace Reurbano\DealBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

use Reurbano\DealBundle\Document\Site;
use Reurbano\DealBundle\Document\Source;

use Reurbano\DealBundle\Form\Frontend\SellType;
use Reurbano\DealBundle\Form\Backend\SourceType;


/**
 * Controller que cuidará das Ofertas em Frontend.
 */

class SellController extends BaseController
{
    /**
     * Action para vender cupom
     * 
     * @Route("/", name="deal_sell_index")
     * @Template()
     */
    public function indexAction()
    {
        $title = "Venda cupons de qualquer site de compras coletivas aqui";
        $form = $this->createForm(new SellType());
        return array(
            'title' => $title,
            'form'  => $form->createView(),
        );
    }
    
    /**
     * Url dinamica do script
     * 
     * @Route("/script.js", name="deal_sell_script")
     */
    public function scriptAction() {
        $script = "
            var ajaxPath = '" . $this->generateUrl('deal_sell_ajax', array(), true) . "';
            ";
        return new Response($script);
    }
    
    /**
     * Ajax dos sites de compra coletiva.
     * 
     * @route("/ajax", name="deal_sell_ajax")
     */
    public function ajaxAction()
    {
        if ($this->get('request')->isXmlHttpRequest()) {
            if ($this->get('request')->getMethod() == 'GET') {
                $cupom = $this->get('request')->query->get('q');
                $siteId = $this->get('request')->query->get('siteid');
                $regexp = new \MongoRegex('/' . $cupom . '/i');
                $source = $this->mongo('ReurbanoDealBundle:Source')
                        ->createQueryBuilder()
                        ->sort('createdAt', 'ASC')
                        ->field('site.$id')->equals((int)$siteId)
                        ->field('title')->equals($regexp)
                        ->getQuery()->execute();
                $data = '';
                foreach($source as $k => $v){
                    $data .= $v->getTitle();
                    $data .= '|';
                    $data .= $v->getId();
                    $data .= " \n";
                }
                return new Response($data);
            }
        }
        //return new Response(json_encode(array()));
    }
    
    /**
     * Detalhes da oferta
     * 
     * @route("/detalhes", name="deal_sell_details")
     * @Template()
     */
    public function detailsAction()
    {
        $title = "Venda cupons de qualquer site de compras coletivas aqui";
        $form = $this->createForm(new SellType());
        $request = $this->get('request');
        if($request->getMethod() == 'POST'){
            $data = $this->get('request')->request->get($form->getName());
            $source = $this->mongo('ReurbanoDealBundle:Source')->find($data['cupomId']);
            $sourceForm = $this->createForm(new SourceType(), $source);
            
        }
        return array(
            'title' => $title,
            'form'  => $sourceForm->createView(),
        );
    }
}