<?php
namespace Reurbano\DealBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Mastop\SystemBundle\Controller\BaseController;

use Reurbano\DealBundle\Form\Backend\DealType;
use Reurbano\DealBundle\Document\Deal;
use Reurbano\DealBundle\Document\Voucher;
use Reurbano\DealBundle\Document\Offer;
use Reurbano\DealBundle\Util\Upload;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller para administrar (CRUD) ofertas.
 */

class DealController extends BaseController
{
    /**
     * @Route("/", name="admin_deal_deal_index")
     * @Template()
     */
    public function indexAction()
    {
        $title = 'Administração de Ofertas';
        //$ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findAll();
        $ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findAllByCreated();
        return array(
            'ofertas' => $ofertas,
            'title'   => $title,
            'current' => 'admin_deal_deal_index',
            );
    }
    /**
     * @Route("/novo", name="admin_deal_deal_new")
     * @Route("/editar/{id}", name="admin_deal_deal_edit")
     * @Route("/salvar/{id}", name="admin_deal_deal_save", defaults={"id" = null})
     * @Template()
     */
    public function dealAction($id = null)
    {
        $dm = $this->dm();
        $title = ($id) ? "Editar Oferta" : "Nova Oferta";
        if($id){
            $deal = $this->mongo('ReurbanoDealBundle:Deal')->find($id);
            if (!$deal) {
                throw $this->createNotFoundException('Nenhuma oferta encontrada com o ID '.$id);
            }
        }else{
            $deal = new Deal();
        }
        $request = $this->get('request');
        $form = $this->createForm(new DealType(), $deal);
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            $formResult = $request->request->get('deal');
            $formDataResult = $request->files->get('deal');
            $source = $this->mongo('ReurbanoDealBundle:Source')->find($formResult['source']);
            $city   = $this->mongo('ReurbanoCoreBundle:City')->find($source->getCity()->getId());
            //var_dump($city->getName());
            $offer = new Offer();
            $offer->setSource($source);
            $offer->setCity($city);
            $deal->setOffer($offer);
            /*echo $deal->getPrice();
            echo "<pre>";
            print_r($formDataResult);
            echo "</pre>";
            exit();*/
            foreach ($formDataResult as $kFile => $vFile){
                if ($vFile){
                    $file = new Upload($formDataResult[$kFile]);
                    $file->setPath($this->get('kernel')->getRootDir() . "/../web/uploads/reurbanodeal");
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
            if ($form->isValid()) {
                $dm->persist($deal);
                $dm->flush();
                $this->get('session')->setFlash('ok', $this->trans(($id) ? "Oferta Editada" : "Oferta Criada" ));
                return $this->redirect($this->generateUrl('admin_deal_deal_index'));
            }
        }
        return array(
            'form'    => $form->createView(),
            'deal'    => $deal,
            'title'   =>  $title,
            'current' => 'admin_deal_deal_index',);
    }
    /**
     * @Route("/deletar", name="admin_deal_deal_delete")
     * @Template()
     */
    public function deleteAction()
    {
        $dm = $this->dm();
        $request = $this->getRequest();
        $id = $request->get('id');
        $deal = $this->mongo('ReurbanoDealBundle:Deal')->find($id);
        if (!$deal) {
            throw $this->createNotFoundException('Nenhuma oferta encontrada com o ID '.$id);
        }
        
        $request = $this->get('request');
        
        if ('POST' == $request->getMethod()) {
            $dm = $this->dm();
            $dm->remove($deal);
            $dm->flush();
            $this->get('session')->setFlash('ok', $this->trans('Oferta Deletada'));
            return $this->redirect($this->generateUrl('admin_deal_deal_index'));
        }
        
        return $this->confirm('Tem certeza de que deseja remover a oferta "' . $deal->getLabel() . '"', array('id' => $deal->getId()));
        
    }
    
    /**
     * @Route("/deal.js", name="admin_deal_script")
     */
    
    public function scriptAction() {

        $script = '
            var ajaxPath = "' . $this->generateUrl('admin_deal_deal_source', array(), true) . '";
            ';
        return new Response($script);
    }


    /**
     * @Route("/source", name="admin_deal_deal_source")
     */
    
    public function getSourceAction(){
        if ($this->get('request')->isXmlHttpRequest()) {
            
            if ($this->get('request')->getMethod() == 'POST') {
                
                $id = $this->get('request')->request->get('id');
                $source = $this->mongo('ReurbanoDealBundle:Source')->find($id);
                $ret = array();
                
                if ($source){
                    $ret['success']=true;
                    $ret['title']   = $source->getTitle();
                    $ret['price']   = $source->getPriceOffer();
                }else{
                     $ret['success']=false;
                }
                 return new Response(json_encode($ret));
            }
        }
    }
    
}