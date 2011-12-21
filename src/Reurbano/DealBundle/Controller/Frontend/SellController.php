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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

use Reurbano\DealBundle\Document\Site;
use Reurbano\DealBundle\Document\Source;
use Reurbano\DealBundle\Document\SourceEmbed;
use Reurbano\DealBundle\Document\Deal;
use Reurbano\DealBundle\Document\Voucher;
use Reurbano\DealBundle\Document\Comission;

use Reurbano\DealBundle\Util\Upload;

use Reurbano\DealBundle\Form\Frontend\SellType;
use Reurbano\DealBundle\Form\Frontend\ContactType;
use Reurbano\DealBundle\Form\Frontend\DealType;
use Reurbano\DealBundle\Form\Frontend\DealAdminType;



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
     * @Method("GET");
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
                if(strpos($cupom, 'http://') !== false){
                    // Se a busca é por uma URL, remove o query string
                    $cupom = preg_replace('/\?.*/', '', $cupom);
                }
                $limit = $this->get('request')->query->get('limit');
                $city = $this->get('request')->query->get('city');
                if($city == $this->get('session')->get('reurbano.user.city')){
                    $cityId = $this->get('session')->get('reurbano.user.cityId');
                }else{
                    $cityDB = $this->mongo('ReurbanoCoreBundle:City', 'crawler')->findBySlug($city);
                    $cityId = ($cityDB) ? $cityDB->getId() : $this->get('session')->get('reurbano.user.cityId');
                }
                $siteId = $this->get('request')->query->get('siteid');
                $regexp = new \MongoRegex('/' . preg_quote($cupom) . '/i');
                $qb = $this->mongo('ReurbanoDealBundle:Source', 'crawler')->createQueryBuilder();
                $cityNacionalId = $this->get('session')->get('reurbano.user.nacional');
                // Adicionado para buscar apenas na cidade do usuário + oferta nacional
                if ($city == 'oferta-nacional') {
                    $qb->field('city.$id')->equals(new \MongoId($cityId));
                } else {
                    $qb->field('city.$id')->in(array(new \MongoId($cityId), new \MongoId($cityNacionalId)));
                }
                $source = $qb->sort('city.id', 'desc')->sort('dateRegister', 'desc')->limit($limit)
                        ->field('site.id')->equals((int)$siteId)
                        // Linha abaixo foi comentada porque o crawler não pega data de validade de todas as ofertas
                        //->field('expiresAt')->gt(new \DateTime())
                        ->addOr($qb->expr()->field('url')->equals($regexp))->addOr($qb->expr()->field('title')->equals($regexp))
                        ->getQuery()->execute();
                $data = '';
                foreach($source as $k => $v){
                    if($v->getExpiresAt() && $v->getExpiresAt()->getTimestamp() < time()){
                        continue; // Se a oferta tem data e está vencida, não mostra
                    }
                    $title = $v->getTitle();
                    $title = preg_replace("'\s+'", ' ', $title);
                    $title = trim($title, ' -');
                    // Coloca a data da oferta antes do título
                    $title = ($v->getDateRegister() != '') ? $v->getDateRegister()->format('d/m/Y').' - '.$title : $title;
                    $data .= "<table class='m0'><tr><td><div style='margin:3px; position:relative'><img src='".$this->mastop()->param('deal.all.dealurl').$v->getThumb()."' width='80' height='60' />".(($v->getCity()->getId() == $cityNacionalId) ? "<div class='dealCity' style='position:absolute; top:0'><span></span></div>" : "")."</div></td><td>|".$title."|</td></tr></table>";
                    $data .= '|';
                    $data .= $v->getId();
                    $data .= " \n";
                }
                if(empty ($data)){
                    // Se não houve resultados, retorna mensagem avisando
                    $data .= "<table class='m0'><tr><td>|<span style='color:red !important'>Nenhuma oferta encontrada contendo ".$cupom."</span>|</td></tr></table>|0";
                }
                return new Response($data);
            }
        }
        throw $this->createNotFoundException('Uepa!');
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
        $crawlerDM = $this->mastop()->getDocumentManager('crawler');
        if($request->getMethod() == 'POST'){
            $data = $this->get('request')->request->get($form->getName());
            $source = $this->mongo('ReurbanoDealBundle:Source', 'crawler')->find($data['cupomId']);
            $sourceEmbed = new SourceEmbed();
            $sourceEmbed->populate($source);
            if(!$source){
                return $this->redirectFlash($this->generateUrl('deal_sell_index'), 'Oferta não encontrada', 'error');
            }
            $deal=new Deal();
            $deal->setPrice($source->getPriceOffer());
            $deal->setQuantity(1);
            if($sourceEmbed->getCategory()->getSlug() == 'outros'){
                $sourceEmbed->setCategory(null);
            }
            $deal->setSource($sourceEmbed);
            $admin = false;
            if($this->hasRole('ROLE_ADMIN') || $this->hasRole('ROLE_SUPERADMIN')){
                $sourceForm = $this->createForm(new DealAdminType(),$deal, array('document_manager' => 'crawler'));
                $admin = true;
            }else{
                $sourceForm = $this->createForm(new DealType(),$deal, array('document_manager' => 'crawler'));
            }
        }else{
            return $this->redirectFlash($this->generateUrl('deal_sell_index'), 'Selecione uma oferta', 'notice');
        }
        return array(
            'title'  => $title,
            'admin'  => $admin,
            'source' => $source,
            'form'   => $sourceForm->createView(),
        );
    }
    
    /**
     * Salva o deal
     * 
     * @Route("/salvar", name="deal_sell_save")
     * @Secure(roles="ROLE_USER")
     * @Method("POST")
     */
    public function saveAction(){
        $dm = $this->dm();
        $crawlerDM = $this->mastop()->getDocumentManager('crawler');
        $request = $this->get('request');
        $form = $this->createForm(new DealType(),null, array('document_manager' => 'crawler'));
        $data = $this->get('request')->request->get($form->getName());
        if(isset($data['user'])){
            $user = $this->mongo('ReurbanoUserBundle:User')->find($data['user']);
        }else{
            $user = $this->get('security.context')->getToken()->getUser();
        }
        if($request->getMethod() == 'POST'){
            $mail = $this->get('mastop.mailer');
            $deal = new Deal();
            $source = $crawlerDM->getRepository('ReurbanoDealBundle:Source')->find($data['sourceId']);
            $formDataResult = $request->files->get($form->getName());
            if(count($formDataResult) != $data['quantity']){
                return $this->redirectFlash($this->generateUrl('deal_sell_index'), 'É preciso enviar '.$data['quantity'].' vouchers', 'error');
            }
            
            // Data de Validade e Categoria
            $expiresAt = $data['source']['expiresAt'];
            $category = $data['source']['category'];
            $expiresDate = new \DateTime(substr($expiresAt, 6, 4).'-'.substr($expiresAt, 3, 2).'-'.substr($expiresAt, 0, 2));
            if($expiresDate->getTimestamp() < time()){
                $mail->notify('Debug: Data inválida', 'O usuário '.$user->getName().' ('.$user->getEmail().') tentou enviar uma oferta com uma data inválida: '.$expiresAt.'.<br /><br />Dados técnicos do POST:<br />'.  print_r($data, true));
                return $this->redirectFlash($this->generateUrl('deal_sell_index'), 'A data de validade precisa ser maior que a data de hoje.', 'error');
            }
            if($source->getExpiresAt() == '' || $source->getExpiresAt()->format('d/m/Y') != $expiresAt){
                // Seta a validade no Source
                $source->setExpiresAt($expiresDate);
                $crawlerDM->persist($source);
                $crawlerDM->flush();
            }
            // Categoria
            $cat = $this->mongo('ReurbanoDealBundle:Category')->find($category);
            if(!$cat){
                $mail->notify('Erro: Categoria não encontrada', 'O usuário '.$user->getName().' ('.$user->getEmail().') tentou enviar uma oferta para a categoria ID '.$category.' e ela não foi encontrada no DB.<br /><br />Dados técnicos do POST:<br />'.  print_r($data, true));
                return $this->redirectFlash($this->generateUrl('deal_sell_index'), 'Categoria não encontrada', 'error');
            }
            $source->setCategory($cat);
            // Site
            $siteId = $source->getSite()->getId();
            $site = $this->mongo('ReurbanoDealBundle:Site')->find($siteId);
            if(!$site){
                $mail->notify('Erro: Site não encontrado', 'O usuário '.$user->getName().' ('.$user->getEmail().') tentou enviar uma oferta do site ID '.$siteId.' e ele não foi encontrado no DB.<br /><br />Dados técnicos do POST:<br />'.  print_r($data, true));
                return $this->redirectFlash($this->generateUrl('deal_sell_index'), 'Site de compra coletiva não encontrado', 'error');
            }
            $source->setSite($site);
            
            // City
            $cityId = $source->getCity()->getId();
            $city = $this->mongo('ReurbanoCoreBundle:City')->find($cityId);
            if(!$city){
                $mail->notify('Erro: Cidade não encontrada', 'O usuário '.$user->getName().' ('.$user->getEmail().') tentou enviar uma oferta para a cidade ID '.$cityId.' e ela não foi encontrada no DB.<br /><br />Dados técnicos do POST:<br />'.  print_r($data, true));
                return $this->redirectFlash($this->generateUrl('deal_sell_index'), 'Site de compra coletiva não encontrado', 'error');
            }
            $source->setCity($city);
            
            // Upload
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
            $sourceEmbed = new SourceEmbed();
            $sourceEmbed->populate($source);
            $deal->setSource($sourceEmbed);
            $price = $data['price'];
            $quantity = $data['quantity'];
            $obs = $data['obs'];
            $price = str_replace('.', '', $price);
            $price = str_replace(',', '.', $price);
            if($price < 1.50){
                return $this->redirectFlash($this->generateUrl('deal_sell_index'), $this->trans('Valor minimo para uma oferta é R$ 1,50' ), 'error');
            }
            
            
            $deal->setUser($user);
            $deal->setPrice($price);
            $deal->setChecked(false);
            $deal->setSpecial(false);
            $deal->setQuantity($quantity);
            $deal->setActive(true);
            if($this->hasRole('ROLE_ADMIN')){
                // Se o usuário que enviou a oferta é administrador, marca como conferida
                $deal->setChecked(true);
            }
            $deal->setObs($obs);
            
            // Limpa o title
            $label = $source->getTitle();
            $label = preg_replace("'\s+'", ' ', $label);
            $label = trim($label, ' -');
            $deal->setLabel($label);
            
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
            $mail->to($user)
             ->subject('Sua oferta foi cadastrada')
             ->template('oferta_novaoferta', array('user' => $user, 'deal' => $deal, 'dealLink' => $dealLink, 'title' => 'Confirmação de Oferta'))
             ->send();
            //Está dando pau na hora de vender o cupom por causa da formatação do preço do deal que vem como string, mas na verdade deveria ser float
            $mail->notify('Aviso de nova oferta', 'O usuário '.$user->getName().' ('.$user->getEmail().') enviou a seguinte oferta: <br />'.$quantity.'x - '.$deal->getLabel().'<br /> Preço: R$ '.  number_format($deal->getPrice(), 2, ',', '').'<br /><a href="'.$dealLink.'">'.$dealLink.'</a>');
            
            return $this->redirectFlash($this->generateUrl('user_dashboard_index').'#mydeals', $this->trans('Oferta cadastrada com sucesso!'));
        }
    }
    
    /**
     * Formulario de contato caso o usuário não consiga inserir uma oferta
     * 
     * @route("/contato", name="deal_sell_contact")
     * @Secure(roles="ROLE_USER")
     * @Template() 
     */
    public function contactAction(){
        $form = $this->createForm(new ContactType());
        $request = $this->get('request');
        $data = $this->get('request')->request->get($form->getName());
        $user = $this->get('security.context')->getToken()->getUser();
        if($request->getMethod() == 'POST'){
            $formDataResult = $request->files->get($form->getName());
            if(count($formDataResult) != $data['quantity']){
                return $this->redirectFlash($this->generateUrl('deal_sell_index'), 'É preciso enviar '.$data['quantity'].' vouchers', 'error');
            }
            $mail = $this->get('mastop.mailer');
            $site = $this->mongo('ReurbanoDealBundle:Site')->find((int)$data['site']);
            $city = $this->mongo('ReurbanoCoreBundle:City')->findBySlug($request->request->get('city'));
            $mail->to('contato@reurbano.com.br')
             ->subject('Nova oferta para cadastro no site')
             ->template('oferta_contatooferta', array(
                 'user' => $user, 
                 'data' => $data,
                 'site' => $site,
                 'city' => $city,
                 'title' => 'Nova oferta para cadastro'));
            foreach ($formDataResult as $kFile => $vFile){
                if ($vFile){
                    $file = new Upload($formDataResult[$kFile]);
                    $path = $this->get('kernel')->getRootDir() . "/../web/uploads/reurbanodeal/voucher";
                    $file->setPath($path);
                    $fileUploaded = $file->upload();
                    
                    if ($file->getPath() != ""){
                        $filePath = $fileUploaded->getPath();
                    }else {
                        $filePath = $fileUploaded->getDeafaultPath();
                    }
                    $mail->attach($filePath . "/" . $fileUploaded->getFileName());
                }
            }
            $mail->send();
            return $this->redirectFlash($this->generateUrl('user_dashboard_index').'#mydeals', $this->trans('Sua oferta foi enviada! Aguarde nosso contato.'));
        }
        return array(
            'title' => "Envie sua oferta por e-mail",
            'form' => $form->createView(),
        );
    }
}