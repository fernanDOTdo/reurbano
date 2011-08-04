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
                $site = 'ou';
                $regexp = new \MongoRegex('/' . $site . '/i');
                $site = $this->mongo('ReurbanoDealBundle:Site')
                        ->createQueryBuilder()
                        ->sort('createdAt', 'ASC')
                        ->field('')->equals($regexp)
                        ->getQuery()->execute();
        $title = "Venda cupons de qualquer site de compras coletivas aqui";
        $form = $this->createFormBuilder()
                ->add('site', 'text')
                ->add('cupom', 'text')
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
        /*if ($this->get('request')->isXmlHttpRequest()) {
            if ($this->get('request')->getMethod() == 'POST') {*/
                $site = $this->get('request')->request->get('site');
                /*var_dump($this->get('request')->request);
                var_dump($site);
                exit();*/
                //var_dump($site);
                //$site = 'p';
                $regexp = new \MongoRegex('/' . $site . '/i');
                $site = $this->mongo('ReurbanoDealBundle:Site')
                        ->createQueryBuilder()
                        ->sort('createdAt', 'ASC')
                        ->field('name')->equals($regexp)
                        ->getQuery()->execute();
                $retArr = array();
                $retArr[]['titulo'] = "Andre";
                $retArr[]['titulo'] = "Uohshitu";
                $retArr[]['titulo'] = "Craudomira";
                $retArr[]['titulo'] = "Felizberta";
                $i = 0;
                foreach($site as $k => $v){
                    $retArr[$i]['titulo'] = $v->getName();
                    $retArr[$i]['id'] = (string)$v->getId();
                    //echo $v->getId()."<br />";
                }
                /*echo "<pre>";
                var_dump(count($site));
                echo "</pre>";
                exit();*/
                /*echo json_encode($retArr);
                exit();*/
                return new Response(json_encode($retArr));
                return new Response(json_encode($data));
            /*}
        }*/
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