<?php
namespace Reurbano\DealBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\DealBundle\Document\Site;
use Symfony\Component\HttpFoundation\Response;


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
        $siteArray = $this->mongo('ReurbanoDealBundle:Site')->findAll();
        $site[] = '';
        foreach($siteArray as $k => $v){
            $site[$v->getId()] = $v->getName();
        }
        $form = $this->createFormBuilder()
                ->add('site', 'choice',array(
                    'choices' => $site,
                    'attr'    => array(
                        'class'            => 'chzn-select',
                        'data-placeholder' => 'Escolha um site',
                        'style'            => 'width: 200px;',
                    )
                ))
                ->add('cupom', 'text')
                ->add('cupomId', 'hidden')
                ->getForm();
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
                $site = $this->mongo('ReurbanoDealBundle:Site')
                        ->createQueryBuilder()
                        ->sort('createdAt', 'ASC')
                        ->field('name')->equals($regexp)
                        ->field('name')->equals($regexp)
                        ->getQuery()->execute();
                $data = '';
                foreach($site as $k => $v){
                    $data .= $v->getName();
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
        return array();
    }
}