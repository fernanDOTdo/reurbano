<?php
namespace Reurbano\DealBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

use Reurbano\DealBundle\Document\Site;
use Reurbano\DealBundle\Document\Source;
use Reurbano\DealBundle\Document\Deal;
use Reurbano\DealBundle\Document\Offer;
use Reurbano\DealBundle\Document\Voucher;

use Reurbano\DealBundle\Util\Upload;

use Reurbano\DealBundle\Form\Frontend\SellType;
use Reurbano\DealBundle\Form\Frontend\DealType;



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
            $sourceForm = $this->createForm(new DealType());
        }
        return array(
            'title'  => $title,
            'source' => $source,
            'form'   => $sourceForm->createView(),
        );
    }
    
    /**
     * Salva o deal
     * 
     * @Route("/salvar", name="deal_sell_save")
     */
    public function saveAction(){
        $dm = $this->dm();
        $request = $this->get('request');
        $form = $this->createForm(new DealType());
        $user = $this->get('security.context')->getToken()->getUser();
        $data = $this->get('request')->request->get($form->getName());
        if($request->getMethod() == 'POST'){
            $deal = new Deal();
            $offer = new Offer();
            $source = $this->mongo('ReurbanoDealBundle:Source')->find($data['sourceId']);
            $formDataResult = $request->files->get($form->getName());
            foreach ($formDataResult as $kFile => $vFile){
                if ($vFile){
                    $file = new Upload($formDataResult[$kFile]);
                    $path = $this->get('kernel')->getRootDir() . "/../web/uploads/reurbanodeal";
                    $file->setPath($path);
                    $fileUploaded = $file->upload();
                    $voucher = new Voucher();
                    $voucher->setFilename($fileUploaded->getFileName());
                    $voucher->setFilesize($fileUploaded->getFileUploaded()->getClientSize());
                    if ($file->getPath() != ""){
                        $voucher->setPath($fileUploaded->getPath());
                    }else {
                        $voucher->setPath($fileUploaded->getDeafaultPath());
                    }
                    $deal->addVoucher($voucher);
                }
            }
            $offer->setSource($source);
            $offer->setCity($source->getCity());
            $price = $data['price'];
            $quantity = $data['quantity'];
            
            
            $deal->setUser($user);
            $deal->setOffer($offer);
            $deal->setPrice($price);
            $deal->setQuantity($quantity);
            $deal->setActive(true);
            $deal->setLabel($source->getTitle());
            
            $dm->persist($deal);
            $dm->flush();
            
            $this->get('session')->setFlash('ok', $this->trans('Oferta cadastrada com sucesso!'));
            return $this->redirect($this->generateUrl('_home'));
        }
    }
}