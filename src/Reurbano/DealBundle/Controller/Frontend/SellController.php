<?php
namespace Reurbano\DealBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

use Reurbano\DealBundle\Document\Site;
use Reurbano\DealBundle\Document\Source;
use Reurbano\DealBundle\Document\Deal;
use Reurbano\DealBundle\Document\Voucher;
use Reurbano\DealBundle\Document\Comission;

use Reurbano\DealBundle\Util\Upload;

use Reurbano\DealBundle\Form\Frontend\SellType;
use Reurbano\DealBundle\Form\Frontend\DealType;



/**
 * Controller para enviar uma Ofertapara venda.
 * @Route("/vender", requirements={"_scheme" = "https"})
 */

class SellController extends BaseController
{
    /**
     * Action para vender cupom
     * 
     * @Route("/", name="deal_sell_index")
     * @Secure(roles="ROLE_USER")
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
                $qb = $this->mongo('ReurbanoDealBundle:Source')->createQueryBuilder();
                $source = $qb->sort('createdAt', 'ASC')
                        ->field('site.$id')->equals((int)$siteId)
                        ->field('expiresAt')->gt(new \DateTime())
                        ->addOr($qb->expr()->field('url')->equals($regexp))->addOr($qb->expr()->field('title')->equals($regexp))
                        ->getQuery()->execute();
                $data = '';
                foreach($source as $k => $v){
                    $data .= "<table><tr><td><div style='margin:3px'><img src='".$v->getThumb()."' width='80' height='60' /></div></td><td>|".$v->getTitle()."|</td></tr></table>";
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
     * @Secure(roles="ROLE_USER")
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
            $deal=new Deal();
            $deal->setPrice($source->getPriceOffer());
            $deal->setQuantity(1);
            $sourceForm = $this->createForm(new DealType(),$deal);
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
     * @Secure(roles="ROLE_USER")
     */
    public function saveAction(){
        $dm = $this->dm();
        $request = $this->get('request');
        $form = $this->createForm(new DealType());
        $user = $this->get('security.context')->getToken()->getUser();
        $data = $this->get('request')->request->get($form->getName());
        if($request->getMethod() == 'POST'){
            $deal = new Deal();
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
            $deal->setSource($source);
            $price = $data['price'];
            $quantity = $data['quantity'];
            
            
            $deal->setUser($user);
            $deal->setPrice($price);
            $deal->setChecked(false);
            $deal->setSpecial(false);
            $deal->setQuantity($quantity);
            $deal->setActive(true);
            $deal->setLabel($source->getTitle());
            
            // Comissão
            $comission = new Comission();
            $comission->setSellerpercent($this->get('mastop')->param('deal.all.comsellpercent'));
            $comission->setSellerreal($this->get('mastop')->param('deal.all.comsellreal'));
            $comission->setBuyerpercent($this->get('mastop')->param('deal.all.combuypercent'));
            $comission->setBuyerreal($this->get('mastop')->param('deal.all.combuyreal'));
            $deal->setComission($comission);
            $dm->persist($deal);
            $dm->flush();
            
            $this->get('session')->setFlash('ok', $this->trans('Oferta cadastrada com sucesso!'));
            return $this->redirect($this->generateUrl('_home'));
        }
    }
}