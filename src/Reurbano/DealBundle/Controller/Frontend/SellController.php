<?php
/**
 *                                              ,d                              
 *                                              88                              
 * 88,dPYba,,adPYba,   ,adPPYYba,  ,adPPYba,  MM88MMM  ,adPPYba,   8b,dPPYba,   
 * 88P'   "88"    "8a  ""     `Y8  I8[    ""    88    a8"     "8a  88P'    "8a  
 * 88      88      88  ,adPPPPP88   `"Y8ba,     88    8b       d8  88       d8  
 * 88      88      88  88,    ,88  aa    ]8I    88,   "8a,   ,a8"  88b,   ,a8"  
 * 88      88      88  `"8bbdP"Y8  `"YbbdP"'    "Y888  `"YbbdP"'   88`YbbdP"'   
 *                                                                 88           
 *                                                                 88           
 * 
 * Reurbano/DealBundle/Controller/Frontend/SellController.php
 *
 * Controller para venda de cupom
 *  
 * 
 * @copyright 2011 Mastop Internet Development.
 * @link http://www.mastop.com.br
 * @author Fernando Santos <o@fernan.do>
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

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
     * @Route("/scriptjs", name="deal_sell_script")
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
            if(!$source){
                return $this->redirectFlash($this->generateUrl('deal_sell_index'), 'Oferta não encontrada', 'error');
            }
            $deal=new Deal();
            $deal->setPrice($source->getPriceOffer());
            $deal->setQuantity(1);
            $sourceForm = $this->createForm(new DealType(),$deal);
        }else{
            return $this->redirectFlash($this->generateUrl('deal_sell_index'), 'Selecione uma oferta', 'notice');
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
            if(count($formDataResult) != $data['quantity']){
                return $this->redirectFlash($this->generateUrl('deal_sell_index'), 'É preciso enviar '.$data['quantity'].' vouchers', 'error');
            }
            foreach ($formDataResult as $kFile => $vFile){
                if ($vFile){
                    $file = new Upload($formDataResult[$kFile]);
                    $path = $this->get('kernel')->getRootDir() . "/../web/uploads/reurbanodeal/voucher";
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
            $deal->setPrice(str_replace(",", ".", $price));
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
            
            // Envia notificações por e-mail
            $dealLink = $this->generateUrl('deal_deal_show', array('city'=>$deal->getSource()->getCity()->getSlug(), 'category' => $deal->getSource()->getCategory()->getSlug(), 'slug' => $deal->getSlug()), true);
            $mail = $this->get('mastop.mailer');
            $mail->to($user)
             ->subject('Sua oferta foi cadastrada')
             ->template('oferta_novaoferta', array('user' => $user, 'deal' => $deal, 'dealLink' => $dealLink, 'title' => 'Confirmação de Oferta'))
             ->send();
            //Está dando pau na hora de vender o cupom por causa da formatação do preço do deal que vem como string, mas na verdade deveria ser float
            $mail->notify('Aviso de nova oferta', 'O usuário '.$user->getName().' ('.$user->getEmail().') enviou a seguinte oferta: <br />'.$quantity.'x - '.$deal->getLabel().'<br /> Preço: R$ '.  number_format($dela->getPrice(), 2, ',', '').'<br /><a href="'.$dealLink.'">'.$dealLink.'</a>');
            
            $this->get('session')->setFlash('ok', $this->trans('Oferta cadastrada com sucesso!'));
            return $this->redirect($this->generateUrl('_home'));
        }
    }
}