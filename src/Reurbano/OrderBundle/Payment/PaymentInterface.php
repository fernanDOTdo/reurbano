<?php

/**
 * Reurbano/OrderBundle/Payment/PaymentInterface.php
 *
 * Interface para formas de pagamento
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

interface PaymentInterface 
{
    /**
     * Construtor
     */
    function __construct(Order $order, ContainerInterface $container);
    
    /**
     * Seta um Parâmetro
     */
    function setParam($name, $val);
    
    /**
     * Pega um Parâmetro
     */
    function getParam($name);
    
    /**
     * Pega Array com todos os Parâmetros
     */
    function getParams();
    
    /**
     * Seta array com os dados do pagamento
     */
    function setData(array $data);
    
    /**
     * Pega array com os dados do pagamento
     */
    function getData();
    
    /**
     * Valida o pedido
     */
    function validate();
    
    /**
     * Renderiza o botão de pagamento
     */
    function renderPaymentButton();
    
    /**
     * Processa a forma de pagamento e exibe formulário para preenchimento dos dados
     */
    function process();
    
    /**
     * Renderiza as informações sobre o pagamento
     */
    function renderInfo();
    
    /**
     * Verifica o status
     */
    function checkStatus();
    
    /**
     * Altera o status do pagamento
     */
    function changeStatus(Request $request);
    
    /**
     * Retorna o ID do pedido (ideal para o processamento do post de retorno ou mudança de status)
     */
    static function getOrderId(Request $request);
}