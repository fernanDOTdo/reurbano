<?php

/**
 * Reurbano/OrderBundle/Payment/MercadoPago.php
 *
 * Forma de pagamento com Mercado Pago
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

class MercadoPago implements PaymentInterface {

    protected $order;
    protected $params = array(); // Parâmetros do MercadoPago
    protected $data = array(); // Dados específicos do Pagamento
    protected $url = 'https://www.mercadopago.com/mlb/buybutton';
    //protected $url = 'http://www.mastop.com.br/fernando/post.php';
    protected $sonda_url = 'https://www.mercadopago.com/mlb/sonda';
    protected $sonda_key = 'zG9TysmxKLzviqLRsKE0CXrhxww%3D';

    /**
     * Construtor.
     *
     * @param Order $order
     * @param ContainerInterface $container 
     */
    public function __construct(Order $order, ContainerInterface $container) {
        $this->order = $order;
        $payment = $order->getPayment();
        if (isset($payment['params'])) {
            $this->params = $payment['params'];
        } else {
            $this->setParam('acc_id', '26474029');
            $this->setParam('enc', 'pyQC9zG%2FjM71lL2%2FTIyHstsATM0%3D');
            $this->setParam('url_sucessfull', $container->get('router')->generate('order_order_return', array('gateway' => 'MercadoPago', 'status' => 'sucesso'), true));
            $this->setParam('url_process', $container->get('router')->generate('order_order_return', array('gateway' => 'MercadoPago', 'status' => 'analise'), true));
            $this->setParam('url_cancel', $container->get('router')->generate('order_order_return', array('gateway' => 'MercadoPago', 'status' => 'nao-autorizado'), true));
            $this->setParam('seller_op_id', $order->getId());
            $this->setParam('extra_part', $order->getId());
            $this->setParam('item_id', $order->getDeal()->getId());
            $this->setParam('name', $order->getQuantity() . 'x ' . $order->getDeal()->getLabel(90));
            $this->setParam('price', number_format($order->getTotal(), 2, '.', ''));
            $this->setParam('currency', 'REA');
            $user = $order->getUser();
            $nameArray = explode(' ', $user->getName());
            $this->setParam('cart_name', $nameArray[0]);
            unset($nameArray[0]);
            if (count($nameArray) > 0) {
                $this->setParam('cart_surname', implode(' ', $nameArray));
            }
            $this->setParam('cart_email', $user->getEmail());
        }
        if (isset($payment['data'])) {
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
            $ret .= '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
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
            $ret .= '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
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
        if (isset($data['status'])) {
            switch ($data['status']) {
                case 'A':
                    $ret[] = 'Status do Pagamento: <strong>Aprovado</strong>';
                    break;
                case 'C':
                    $ret[] = 'Status do Pagamento: <strong>Recusado</strong>';
                    break;
                case 'P':
                default:
                    $ret[] = 'Status do Pagamento: <strong>Aguardando Aprovação</strong>';
                    break;
            }
        }
        if (isset($data['payment_method'])) {
            switch ($data['payment_method']) {
                case 'CC':
                    $ret[] = 'Forma de Pagamento: <strong>Cartão de Crédito</strong>';
                    break;
                case 'BTR':
                    $ret[] = 'Forma de Pagamento: <strong>Transferência Bancária</strong>';
                    break;
                case 'BTI':
                default:
                    $ret[] = 'Forma de Pagamento: <strong>Boleto Bancário</strong>';
                    break;
            }
            if(isset ($data['total_amount'])){
                $ret[] = 'Total: <strong>R$ '.  number_format($data['total_amount'], 2, ',', '').'</strong>';
            }
        }
        return $ret;
    }

    /**
     * Verifica o status do pedido na operadora.
     *
     * @return array contendo mensagem e tipo de mensagem para redirecionamento.
     */
    public function checkStatus() {
        $url = $this->sonda_url;
        $postData = array("acc_id" => $this->getParam('acc_id'),
            "sonda_key" => $this->sonda_key,
            "seller_op_id" => $this->getParam('seller_op_id'));
        //"seller_op_id" => '4e6b0933940cfe3c62000047');
        $elements = array();
        foreach ($postData as $name => $value) {
            $elements[] = "{$name}=" . urlencode($value);
        }
        $postData = implode("&", $elements);

        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $url);
        curl_setopt($handler, CURLOPT_POST, true);
        curl_setopt($handler, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($handler, CURLOPT_FAILONERROR, 1);
        curl_setopt($handler, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($handler);
        curl_close($handler);
        //die($response);

        $XML = new \SimpleXMLElement((string) $response);
        $json = json_encode($XML);
        $array = json_decode($json, TRUE);
        if (isset($array['message']) && $array['message'] == 'OK') {
            if (isset($array['operation'])) {
                $this->setData($array['operation']);
                switch ($array['operation']['status']) {
                    case 'A':
                        return array('type' => 'ok', 'msg' => 'Pagamento efetuado e aprovado com sucesso!');
                        break;
                    case 'C':
                        return array('type' => 'error', 'msg' => 'Pagamento recusado. Acesse a área de compras e tente pagar novamente.');
                        break;
                    case 'P':
                    default:
                        return array('type' => 'notice', 'msg' => 'Seu pagamento está pendente ou aguardando aprovação da instituição financeira.');
                        break;
                }
            }
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
        if ($request->get('extra_part') != '') {
            return $request->get('extra_part');
        }elseif($request->get('seller_op_id') != ''){
            return $request->get('seller_op_id');
        }
        return null;
    }

}