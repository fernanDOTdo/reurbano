<?php

namespace Reurbano\DealBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mastop\SystemBundle\Controller\BaseController;
use Reurbano\DealBundle\Form\Backend\DealType;
use Reurbano\DealBundle\Document\Deal;
use Reurbano\DealBundle\Document\Voucher;
use Reurbano\DealBundle\Util\Upload;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller para administrar (CRUD) ofertas.
 */
class DealController extends BaseController {

    /**
     * @Route("/", name="admin_deal_deal_index")
     * @Template()
     */
    public function indexAction() {
        $title = 'Administração de Ofertas';
        //$ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findAll();
        $ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findAllChecked(true);
        return array(
            'ofertas' => $ofertas,
            'title' => $title,
            'current' => 'admin_deal_deal_index',
        );
    }

    /**
     * @Route("/a-conferir", name="admin_deal_deal_checked")
     * @Template()
     */
    public function checkedAction() {
        $title = 'Administração de Ofertas a Conferir';
        //$ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findAll();
        $ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findAllChecked(false);
        return array(
            'ofertas' => $ofertas,
            'title' => $title,
            'current' => 'admin_deal_deal_index',
        );
    }
    
    
    /**
     * @Route("/ultima-chance/{days}", name="admin_deal_deal_last_chance", defaults={"days" = 10})
     * @Template()
     */
    public function lastChanceAction($days = 10) {
        $title = 'Administração de Ofertas que vencem em menos de '.$days.' dias';
        $ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findLastChance($days);
        return array(
            'ofertas' => $ofertas,
            'title' => $title,
            'current' => 'admin_deal_deal_index',
        );
    }
    
    /**
     * @Route("/novo", name="admin_deal_deal_new")
     * @Route("/editar/{id}", name="admin_deal_deal_edit")
     * @Route("/salvar/{id}", name="admin_deal_deal_save", defaults={"id" = null})
     * @Template()
     */
    public function dealAction($id = null) {
        $dm = $this->dm();
        $title = ($id) ? "Editar Oferta" : "Nova Oferta";
        if ($id) {
            $deal = $this->mongo('ReurbanoDealBundle:Deal')->find($id);
            if (!$deal) {
                throw $this->createNotFoundException($this->trans('Nenhuma oferta encontrada com o ID %id%', array("%id%" => $id)));
            }
        } else {
            $deal = new Deal();
        }
        $request = $this->get('request');
        $form = $this->createForm(new DealType(), $deal);
        $dadosPost = $request->request->get($form->getName());
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            $formResult = $request->request->get('deal');
            $formDataResult = $request->files->get('deal');
            $formResult = $dadosPost['expiresAt'];
            $expiresAt = explode('/',$formResult);
            $dataAtual = new \DateTime($expiresAt[2].'-'.$expiresAt[1].'-'.$expiresAt[0]);
            $deal->getSource()->setExpiresAt($dataAtual);
            
            $tags = $dadosPost['tags'];
            $deal->generateTags($tags);
            
            //var_dump($city->getName());
            /* echo $deal->getPrice();
              echo "<pre>";
              print_r($formDataResult);
              echo "</pre>";
              exit(); */
            if ($formDataResult) {
                foreach ($formDataResult as $kFile => $vFile) {
                    if ($vFile) {
                        $file = new Upload($formDataResult[$kFile]);
                        $file->setPath($this->get('kernel')->getRootDir() . "/../web/uploads/reurbanodeal/voucher");
                        $fileUploaded = $file->upload();
                        $voucher = new Voucher();
                        $oldName = explode("-", $kFile);
                        foreach($deal->getAllVoucher() as $k => $v){
                            if($v->getFilename() == $oldName[1]){
                                $v->setFilename($fileUploaded->getFileName());
                                $v->setFilesize($fileUploaded->getFileUploaded()->getClientSize());
                                if ($file->getPath() != "") {
                                    $v->setPath($fileUploaded->getPath());
                                } else {
                                    $v->setPath($fileUploaded->getDeafaultPath());
                                }
                                //unlink($v->getPath() . $v->getFilename());
                            }
                        }
                    }
                }
            }
            $dm->persist($deal);
            $dm->flush();
            $this->get('session')->setFlash('ok', $this->trans(($id) ? "Oferta Editada" : "Oferta Criada" ));
            return $this->redirect($this->generateUrl('admin_deal_deal_index'));
        }
        return array(
            'form' => $form->createView(),
            'deal' => $deal,
            'title' => $title,
            'voucher' => $deal->getAllVoucher(),
            'current' => 'admin_deal_deal_index');
    }

    /**
     * @Route("/deletar", name="admin_deal_deal_delete")
     * @Template()
     */
    public function deleteAction() {
        $dm = $this->dm();
        $request = $this->getRequest();
        $id = $request->get('id');
        $deal = $this->mongo('ReurbanoDealBundle:Deal')->find($id);
        if (!$deal) {
            throw $this->createNotFoundException('Nenhuma oferta encontrada com o ID ' . $id);
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
     * @Route("/dealjs", name="admin_deal_script")
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
    public function getSourceAction() {
        if ($this->get('request')->isXmlHttpRequest()) {

            if ($this->get('request')->getMethod() == 'POST') {

                $id = $this->get('request')->request->get('id');
                $source = $this->mongo('ReurbanoDealBundle:Source')->find($id);
                $ret = array();

                if ($source) {
                    $ret['success'] = true;
                    $ret['title'] = $source->getTitle();
                    $ret['price'] = $source->getPriceOffer();
                } else {
                    $ret['success'] = false;
                }
                return new Response(json_encode($ret));
            }
        }
    }
    
    /**
     * Ativa e Desativa uma oferta
     * 
     * @Route("/ativar/{id}/{active}", name="admin_deal_deal_active", defaults={"active" = false})
     */
    public function activeAction($id, $active = false){
        $dm = $this->dm();
        $banner = $this->mongo('ReurbanoDealBundle:Deal')->find($id);
        ($active) ? $banner->setActive(true) : $banner->setActive(false);
        $dm->persist($banner);
        $dm->flush();
        return $this->redirect($this->generateUrl('admin_deal_deal_index'));
    }
    
    /**
     * Checked Ativo
     * 
     * @Route("/checado/{id}", name="admin_deal_deal_set_checked")
     */
    public function setCheckedAction(Deal $deal){
        $user = $deal->getUser();
        $dm = $this->dm();
        $deal->setChecked(true);
        $dm->flush();
        // Notifica o vendedor da liberação da oferta
        $dealLink = $this->generateUrl('deal_deal_show', array('city'=>$deal->getSource()->getCity()->getSlug(), 'category' => $deal->getSource()->getCategory()->getSlug(), 'slug' => $deal->getSlug()), true);
        $mail = $this->get('mastop.mailer');
        $mail->to($user)
             ->subject('Oferta Aprovada')
             ->template('oferta_aprovada', array('user' => $user, 'deal' => $deal, 'dealLink' => $dealLink, 'title' => 'Oferta Aprovada'))
             ->send();
        return $this->redirectFlash($this->generateUrl('admin_deal_deal_checked'), 'Oferta aprovada!');
    }
    
    /**
     * @Route("/export", name="admin_deal_deal_export")
     */
    public function exportAction()
    {
        $deal = $this->mongo('ReurbanoDealBundle:Deal')->findAllByCreated();
        $data = "Cidade;Site de origem;Categoria;Oferta;URL;Nome Vendedor;E-mail Vendedor;No estabelecimento;Na compra coletiva;No Reurbano;Data;Expirado;Data Vencimento;Conferido;Destaque;Ativo;Cupons Disponíveis;Visualizações\n";
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
                    ";" . (($deal->getSource()->getExpiresAt()->getTimestamp() < time()) ? "Sim" : "Não") .
                    ";" . $deal->getSource()->getExpiresAt()->format('d/m/Y') . 
                    ";" . (($deal->getChecked()) ? "Sim" : "Não") .
                    ";" . (($deal->getSpecial()) ? "Sim" : "Não") .
                    ";" . (($deal->getActive()) ? "Sim" : "Não") .
                    ";" . $deal->getQuantity() .
                    ";" . $deal->getViews() . "\n";
        }
        return new Response($data, 200, array(
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename= ofertas_' . date('d_m_Y') . '.csv',
        ));
    }
}