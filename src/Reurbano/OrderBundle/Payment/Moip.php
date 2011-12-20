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
 * Reurbano/OrderBundle/Payment/Moip.php
 *
 * Pagamento por MOIP
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


namespace Reurbano\OrderBundle\Payment;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Reurbano\OrderBundle\Document\Order;

class Moip implements PaymentInterface {

    protected $order;
    protected $container;
    protected $params = array(); // Parâmetros do Moip
    protected $data = array(); // Dados específicos do Pagamento
    protected $url = 'https://www.moip.com.br/PagamentoMoIP.do';

    /**
     * Construtor.
     *
     * @param Order $order
     * @param ContainerInterface $container 
     */
    public function __construct(Order $order, ContainerInterface $container) {
        $this->order = $order;
        $this->container = $container;
        $payment = $order->getPayment();
        if (isset($payment['params'])) {
            $this->params = $payment['params'];
        } else {
            $this->setParam('id_carteira', 'reurbanoMOIP');
            $this->setParam('id_transacao', $order->getId());
            $this->setParam('nome', $order->getQuantity() . 'x ' . $order->getDeal()->getLabel(60));
            $this->setParam('descricao', 'Compra de '.$order->getQuantity().' '.(($order->getQuantity() > 1) ? 'cupons' : 'cupom').' no site Reurbano - www.reurbano.com.br');
            $this->setParam('valor', ($order->getTotal() * 100));
            $user = $order->getUser();
            $this->setParam('pagador_nome', $user->getName());
            $this->setParam('pagador_email', $user->getEmail());
            $this->setParam('pagador_cidade', $user->getCity()->getName());
            
        }
        /**
         * @var Request 
         */
        $request = $container->get('request');
        if($request->request->get('id_transacao') == $order->getId()){
            $this->setData($request->request->all());
        }elseif (isset($payment['data'])) {
            $this->setData($payment['data']);
        }
    }

    /**
     * Seta um parâmetro.
     *
     * @param string $name
     * @param string $val 
     */
    public function setParam($name, $val) {
        $this->params[$name] = $val;
    }

    /**
     * Retorna um parâmetro específico, null se o parâmetro não existir.
     *
     * @param string $name
     * @return string|null
     */
    public function getParam($name) {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        } else {
            return null;
        }
    }

    /**
     * Retorna um array contendo todos os parâmetros deste pagamento.
     *
     * @return array
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * Seta o array com os dados específicos deste pagamento.
     *
     * @param array $data 
     */
    public function setData(array $data) {
        $this->data = $data;
    }

    /**
     * Retorna o array com os dados específicos deste pagamento.
     *
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Valida a forma de pagamento atual.
     *
     * @return bool
     */
    public function validate() {
        return true;
    }

    /**
     * Retorna o botão para efetuar o pagamento.
     * O botão geralmente será em um formulário.
     * @param string $text
     * @return string 
     */
    public function renderPaymentButton($text = 'Pagar') {
        if(!$this->order->getStatus() || $this->order->getStatus()->getId() != 1){
            return null;
        }
        $ret = '<form action="' . $this->url . '" method="post"><input type="submit" value="' . $text . '" class="button ' . $this->order->getDeal()->getDiscountType() . '">';

        foreach ($this->getParams() as $name => $value) {
            if($name == 'nome' || $name == 'descricao'){
                $ret .= '<input type="hidden" name="' . $name . '" value="' . iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) . '" />'; // Tira os acentos
            }else{
                $ret .= '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
            }
        }
        $ret .= '</form>';

        return $ret;
    }

    /**
     * Processa o pagamento, ou retorna formulário para o processamento.
     * @TODO: Talvez será preciso outra função para trabalhar os dados quando a implementação for por webservice.
     *
     * @return string
     */
    public function process() {
        $ret = '<form id="paymentForm" action="' . $this->url . '" method="post">
         <h2>Sua compra foi criada com o código <span>' . $this->order->getId() . '</span> e você será redirecionado para a tela de pagamento.</h2>
         <img src="/bundles/mastopsystem/images/load.gif" alt="Carregando" />
         <div class="info mT10 mB10">Se você não for redirecionado em 5 segundos, clique no botão abaixo para pagar sua compra.</div>
         <input type="submit" class="button big ' . $this->order->getDeal()->getDiscountType() . '" value="Ir para tela de pagamento">';

        foreach ($this->getParams() as $name => $value) {
            if($name == 'nome' || $name == 'descricao'){
                $ret .= '<input type="hidden" name="' . $name . '" value="' . iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) . '" />'; // Tira os acentos
            }else{
                $ret .= '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
            }
        }
        $ret .= '</form>';
        $ret .= '
            <script type="text/javascript">
            function goToPayment() { document.getElementById("paymentForm").submit(); }
            setTimeout(goToPayment, 5000);
            </script>';
        return $ret;
    }

    /**
     * Retorna as informações deste pagamento renderizadas em HTML.
     *
     * @return array
     */
    public function renderInfo() {
        $data = $this->getData();
        $ret = array();
        $status = array(
            1 => 'Autorizado',
            2 => 'Iniciado',
            3 => 'Boleto Impresso',
            4 => 'Concluído',
            5 => 'Cancelado',
            6 => 'Em Análise',
            7 => 'Estornado',
        );
        $statusDesc = array(
            1 => 'O pagamento já foi realizado, porém ainda não foi creditado na conta do site (devido ao floating da forma de pagamento).',
            2 => 'O pagamento está sendo realizado ou janela do navegador foi fechada (pagamento abandonado).',
            3 => 'O boleto foi impresso e ainda não foi pago.',
            4 => 'O pagamento já foi realizado e dinheiro já foi creditado na conta do site.',
            5 => 'O pagamento foi cancelado pelo pagador, instituição de pagamento, MoIP ou recebedor antes de ser concluído.',
            6 => 'O pagamento foi realizado com cartão de crédito e autorizado, porém está em análise pela Equipe MoIP. Não existe garantia de que será concluído.',
            7 => 'O pagamento foi estornado pelo pagador, recebedor, instituição de pagamento ou MoIP.',
        );
        $paymentType = array(
          'DebitoBancario'         =>  'Débito em Conta',
          'FinanciamentoBancario'  =>  'Financiamento Bancário',
          'BoletoBancario'         =>  'Boleto Bancário',
          'CartaoDeCredito'        =>  'Cartão de Crédito',
          'CartaoDeDebito'         =>  'Cartão de Débito Visa Electron',
          'CarteiraMoIP'           =>  'Diretamente da Carteira MoIP',
          'NaoDefinida'            =>  'Ainda não definida pelo pagador',
        );
        if (isset($data['status_pagamento'])) {
                    $ret[] = 'Status do Pagamento: <strong>'.$status[$data['status_pagamento']].'</strong> ('.$statusDesc[$data['status_pagamento']].')';
        }
        if (isset($data['tipo_pagamento'])) {
            $ret[] = 'Forma de Pagamento: <strong>'.$paymentType[$data['tipo_pagamento']].'</strong>';
            if(isset ($data['valor'])){
                $ret[] = 'Total: <strong>R$ '.  number_format(($data['valor'] * 0.01), 2, ',', '').'</strong>';
            }
        }
        if (isset($data['cod_moip'])) {
                    $ret[] = 'Código MOIP: <strong>'.$data['cod_moip'].'</strong>';
        }
        return $ret;
    }

    /**
     * Verifica o status do pedido na operadora.
     *
     * @return array contendo mensagem e tipo de mensagem para redirecionamento.
     */
    public function checkStatus() {
        $data = $this->getData();
        if (isset($data['status_pagamento'])) {
            switch ($data['status_pagamento']) {
                case 1:
                case 4:
                    return array('type' => 'ok', 'msg' => 'Pagamento efetuado com sucesso!');
                    break;
                case 5:
                case 7:
                    return array('type' => 'error', 'msg' => 'Pagamento cancelado ou estornado. Acesse a área de compras e tente pagar novamente.');
                    break;
                default:
                    return array('type' => 'notice', 'msg' => 'Seu pagamento está pendente ou aguardando aprovação da instituição financeira.');
                    break;
            }
        }else{
            return array('type' => 'notice', 'msg' => 'Seu pagamento está pendente ou aguardando aprovação da instituição financeira.');
        }
        return false;
    }

    /**
     * Processamento de troca de status
     *
     * @param Request $request
     * @return bool
     */
    public function changeStatus(Request $request) {
        return false;
    }

    /**
     * Pega o ID do pedido baseado em algo passado por request (que varia em cada gateway).
     * Retorna nulo se não encontrar o ID do pedido.
     * Esta função é usada para trocas de status ou retorno do usuário vindo da página de pagamento.
     *
     * @param Request $request
     * @return string|null
     */
    public static function getOrderId(Request $request) {
        if ($request->get('id_transacao') != '') {
            return $request->get('id_transacao');
        }
        return null;
    }

}