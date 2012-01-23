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
 * Reurbano/CoreBundle/Controller/Frontend/XMLController.php
 *
 * Exportações em XML
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

namespace Reurbano\CoreBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller que cadastrará o usuário na mailing list
 * @Route("/xmlexport")
 */

class XMLController extends BaseController
{
    /**
     * @Route("/orders", name="core_xml_orders")
     */
    public function ordersAction()
    {
        $orders = $this->mongo('ReurbanoOrderBundle:Order')->findAllByCreated();
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $xml = $dom->createElement('vendas');
        $xml->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        
        foreach($orders as $order){
            $payment = $order->getPayment();
            $label = $order->getDeal()->getLabel();
            $label = preg_replace("'\s+'", ' ', $label);
            $label = trim($label, ' -');
            
            $venda = $dom->createElement('venda');
            $venda->appendChild($dom->createElement('cidade', $order->getDeal()->getSource()->getCity()->getName()));
            $venda->appendChild($dom->createElement('site', $order->getDeal()->getSource()->getSite()->getName()));
            
            // A categoria e o label são criados desta forma para o XML não dar pau com o caractere "&"
            $categoria = $dom->createElement('categoria');
            $categoria->appendChild($dom->createCDATASection($order->getDeal()->getSource()->getCategory()->getName()));
            $venda->appendChild($categoria);
            $titulo = $dom->createElement('oferta');
            $titulo->appendChild($dom->createCDATASection($label));
            $venda->appendChild($titulo);
            $venda->appendChild($dom->createElement('quantidade', $order->getQuantity()));
            $venda->appendChild($dom->createElement('vendedor_nome', $order->getSeller()->getName()));
            $venda->appendChild($dom->createElement('vendedor_email', $order->getSeller()->getEmail()));
            $venda->appendChild($dom->createElement('comprador_nome', $order->getUser()->getName()));
            $venda->appendChild($dom->createElement('comprador_email', $order->getUser()->getEmail()));
            $venda->appendChild($dom->createElement('preco_original', $order->getDeal()->getSource()->getPrice()));
            $venda->appendChild($dom->createElement('preco_reurbano', $order->getDeal()->getPrice()));
            $venda->appendChild($dom->createElement('data_cadastro', $order->getDeal()->getCreatedAt()->format('d/m/Y')));
            $venda->appendChild($dom->createElement('data_venda', $order->getCreated()->format('d/m/Y')));
            $venda->appendChild($dom->createElement('views', $order->getDeal()->getViews()));
            $venda->appendChild($dom->createElement('status', (($order->getStatus() != null) ? $order->getStatus()->getName() : "Cancelado")));
            $venda->appendChild($dom->createElement('pagamento', $payment['gateway']));
            $xml->appendChild($venda);
        }
        $dom->appendChild($xml);
        //return new Response($dom->saveXml());
        return new Response($dom->saveXml(), 200, array(
            'Content-Type'        => 'text/xml',
        ));
    }
    
    /**
     * @Route("/deals", name="core_xml_deals")
     */
    public function dealsAction()
    {
        $deals = $this->mongo('ReurbanoDealBundle:Deal')->findAllByCreated();
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $xml = $dom->createElement('ofertas');
        $xml->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        
        foreach($deals as $deal){
            $label = $deal->getLabel();
            $label = preg_replace("'\s+'", ' ', $label);
            $label = trim($label, ' -');
            
            $oferta = $dom->createElement('oferta');
            $oferta->appendChild($dom->createElement('cidade', $deal->getSource()->getCity()->getName()));
            $oferta->appendChild($dom->createElement('site', $deal->getSource()->getSite()->getName()));
            // A categoria e o label são criados desta forma para o XML não dar pau com o caractere "&"
            $categoria = $dom->createElement('categoria');
            $categoria->appendChild($dom->createCDATASection($deal->getSource()->getCategory()->getName()));
            $oferta->appendChild($categoria);
            $titulo = $dom->createElement('oferta');
            $titulo->appendChild($dom->createCDATASection($label));
            $oferta->appendChild($titulo);
            $oferta->appendChild($dom->createElement('url', $this->generateUrl('deal_deal_show', array('city' => $deal->getSource()->getCity()->getSlug(), 'category' => $deal->getSource()->getCategory()->getSlug(), 'slug' => $deal->getSlug()), true)));
            $oferta->appendChild($dom->createElement('vendedor_nome', $deal->getUser()->getName()));
            $oferta->appendChild($dom->createElement('vendedor_email', $deal->getUser()->getEmail()));
            $oferta->appendChild($dom->createElement('valor_estabelecimeto', $deal->getSource()->getPrice()));
            $oferta->appendChild($dom->createElement('valor_compra_coletiva', $deal->getSource()->getPriceOffer()));
            $oferta->appendChild($dom->createElement('valor_reurbano', $deal->getPrice()));
            $oferta->appendChild($dom->createElement('data_enviado', $deal->getCreatedAt()->format('d/m/Y')));
            $oferta->appendChild($dom->createElement('data_vencimento', $deal->getSource()->getExpiresAt()->format('d/m/Y')));
            $oferta->appendChild($dom->createElement('expirado', (($deal->getSource()->getExpiresAt()->getTimestamp() < time()) ? "Sim" : "Não")));
            $oferta->appendChild($dom->createElement('conferido', (($deal->getChecked()) ? "Sim" : "Não")));
            $oferta->appendChild($dom->createElement('destaque', (($deal->getSpecial()) ? "Sim" : "Não")));
            $oferta->appendChild($dom->createElement('ativo', (($deal->getActive()) ? "Sim" : "Não")));
            $oferta->appendChild($dom->createElement('quantidade', $deal->getQuantity()));
            $oferta->appendChild($dom->createElement('views', $deal->getViews()));
            $xml->appendChild($oferta);
        }
        $dom->appendChild($xml);
        //return new Response($dom->saveXml());
        return new Response($dom->saveXml(), 200, array(
            'Content-Type'        => 'text/xml',
        ));
    }
    
    /**
     * @Route("/mailing", name="core_xml_mailing")
     */
    public function mailingAction()
    {
        $mails = $this->mongo('ReurbanoCoreBundle:Mailing')->findAllByOrder();
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $xml = $dom->createElement('emails');
        $xml->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        
        foreach($mails as $mail){
            $email = $dom->createElement('email');
            $email->appendChild($dom->createElement('mail', $mail->getMail()));
            $email->appendChild($dom->createElement('cidade', $mail->getCity()));
            $email->appendChild($dom->createElement('data', $mail->getCreatedAt()->format('d/m/Y')));
            $xml->appendChild($email);
        }
        $dom->appendChild($xml);
        //return new Response($dom->saveXml());
        return new Response($dom->saveXml(), 200, array(
            'Content-Type'        => 'text/xml',
        ));
    }
    
    /**
     * @Route("/users", name="core_xml_users")
     */
    public function usersAction()
    {
        $users = $this->mongo('ReurbanoUserBundle:User')->findAllByCreated();
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $xml = $dom->createElement('usuarios');
        $xml->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        
        foreach($users as $user){
            $usuario = $dom->createElement('usuario');
            // O nome é criado desta forma para o XML não dar pau com o caractere "&"
            $nome = $dom->createElement('nome');
            $nome->appendChild($dom->createCDATASection($user->getName()));
            $usuario->appendChild($nome);
            $usuario->appendChild($dom->createElement('email', $user->getEmail()));
            $usuario->appendChild($dom->createElement('sexo', strtoupper($user->getGender())));
            $usuario->appendChild($dom->createElement('cidade', $user->getCity()->getName()));
            $usuario->appendChild($dom->createElement('data_cadastro', $user->getCreated()));
            $xml->appendChild($usuario);
        }
        $dom->appendChild($xml);
        //return new Response($dom->saveXml());
        return new Response($dom->saveXml(), 200, array(
            'Content-Type'        => 'text/xml',
        ));
    }
    
    /**
     * @Route("/checkouts", name="core_xml_checkout")
     */
    public function checkoutsAction()
    {
        $checkouts = $this->mongo('ReurbanoOrderBundle:Checkout')->findAllByCreated();
        $checkoutStatus = array(0 => "Cancelado", 1 => "Pendente", 2 => "Finalizado");
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $xml = $dom->createElement('checkouts');
        $xml->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        
        foreach($checkouts as $checkout){
            $bankData = $checkout->getUser()->getBankData();
            
            
            $check = $dom->createElement('checkout');
            $check->appendChild($dom->createElement('codigo', $checkout->getId()));
            // O nome é criado desta forma para o XML não dar pau com o caractere "&"
            $nome = $dom->createElement('usuario');
            $nome->appendChild($dom->createCDATASection($checkout->getUser()->getName()));
            $check->appendChild($nome);
            
            $check->appendChild($dom->createElement('data', $checkout->getCreated()->format('d/m/Y')));
            $check->appendChild($dom->createElement('total', $checkout->getTotal()));
            $check->appendChild($dom->createElement('status', $checkoutStatus[$checkout->getStatus()]));
            
            $banco = $dom->createElement('banco');
            $banco->appendChild($dom->createCDATASection($bankData->getName()));
            $check->appendChild($banco);
            
            $agencia = $dom->createElement('agencia');
            $agencia->appendChild($dom->createCDATASection($bankData->getAgency()));
            $check->appendChild($agencia);
            
            $conta = $dom->createElement('conta');
            $conta->appendChild($dom->createCDATASection($bankData->getAccount()));
            $check->appendChild($conta);
            
            $check->appendChild($dom->createElement('tipo', (($bankData->getType() == 1) ? 'Conta Corrente' : 'Conta Poupança')));
            
            $cpf = $dom->createElement('cpf');
            $cpf->appendChild($dom->createCDATASection($bankData->getCpf()));
            $check->appendChild($cpf);
            
            $obs = $dom->createElement('obs');
            $obs->appendChild($dom->createCDATASection($bankData->getObs()));
            $check->appendChild($obs);
            $xml->appendChild($check);
        }
        $dom->appendChild($xml);
        //return new Response($dom->saveXml());
        return new Response($dom->saveXml(), 200, array(
            'Content-Type'        => 'text/xml',
        ));
    }
    
}
