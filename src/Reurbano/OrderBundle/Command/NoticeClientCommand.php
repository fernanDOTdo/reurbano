<?php
/**
 * Reurbano/OrderBundle/Command/NoticeClientCommand.php
 *
 * Comando para avisar o comprador depois de 20 ou N dias que a negociação foi aprovada, 
 * que ele tem mais 10 ou N dias na garantia se o cupom ainda for válido.
 * 
 * @copyright 2012 GUBN
 * @link http://www.gubn.com.br
 * @author Saulo Lima <saulo@gubn.com.br>
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

namespace Reurbano\OrderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Reurbano\OrderBundle\Document\StatusLog;

class NoticeClientCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('reurbano:cron:noticeclient')
            ->setDescription('Avisa o comprador sobre sua garantia restante')
            ->addArgument('days', InputArgument::OPTIONAL, 'Quantos dias para avisar?', 2)
            ->setHelp(<<<EOT
O <info>reurbano:cron:noticeclient</info> avisa o comprador sobre sua garantia restante em X dias

<info>./app/console reurbano:cron:noticeclient 20</info>

EOT
        );
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();
        
        $days = $input->getArgument('days'); //dias que do negociação foi aprovada
        
        $date = new \DateTime();
        $dateNow = new \DateTime();
        $date->setTimestamp(strtotime('-'.$days.' days'));
        
        $dm = $container->get('mastop.dm');
        $mail = $container->get('mastop.mailer');
        
        $orderRepo = $dm->getRepository('ReurbanoOrderBundle:Order');
        $orders = $orderRepo->createQueryBuilder()
                ->field('created')->lte($date) // Data de criação for menor ou igual ao definido na opção "days"
                ->field('status.id')->equals(4) // Status for igual a 4 (Aprovado)
                ->field('payment.data.status_description')->notEqual("in_process") // pedidos que não estejam "processando" pelo Mercado Pago
                ->getQuery()
                ->execute();              
        if($orders->count()){
            foreach($orders as $order){
            		//Default para dias de Garantia
	            	$orderLinkBuyer = $container->get('router')->generate('order_myorders_view', array('id'=>$order->getId()), true);
                $orderLinkAdmin = $container->get('router')->generate('admin_order_order_view', array('id'=>$order->getId()), true);
                $output->writeln("Comprador do pedido ".$order->getId()." notificado");

								//Obtem a VALIDADE DA OFERTA e a DATA DA VENDA
								$orderCreated = $order->getCreated()->getTimestamp();
								$orderExpiresAt = $order->getDeal()->getSource()->getExpiresAt()->getTimestamp();
								
								//Diferença em dias = validade da oferta - data da venda
								$orderDiffSeg = $orderExpiresAt - $orderCreated;
								
								//Converte para Dias essa diferença entre as duas datas
								$orderDiffDay = $this->converToDay($orderDiffSeg);
								
								//Calcula os dias já corridos da garantia
								$diffSegCreated = $dateNow->getTimestamp() - $orderCreated;
								$diffSegExpiresAt = $orderExpiresAt - $dateNow->getTimestamp();
									
								// Se essa diferença for superior a 30 dias, 
								// significa que a oferta tem uma validade grande
								// e o comprador poderá desfrutar dos 30 dias de garantia
								// senão, ele só tem alguns dias de garantia
								$daysGarantia = ($orderDiffDay > 30) ? ( 30 - $this->converToDay($diffSegCreated) ) : $this->converToDay($diffSegExpiresAt);
								
								//$output->writeln($orderDiffDay);
								//$output->writeln($daysGarantia);

								//Se o nº de dias de garantia for maior que zero
								if ( $daysGarantia > 0){
									// Notifica comprador sobre os dias restantes de sua garantia
									$subject = 'Seu cupom tem '.$daysGarantia.' dias ainda na Garantia Reurbano';
	                $nBuyer = $container->get('mastop.mailer');
	                $nBuyer->to($order->getUser())
	                        ->subject($subject)
	                        ->template('usuario_aviso_garantia',array(
	                            'user'  => $order->getUser(),
	                            'order' => $order,
	                            'msg' => $daysGarantia,
	                            'orderLink' => $orderLinkBuyer,
	                        ))
	                        ->send();
	                        
	                // Notifica o administrador da finalização
	                $mail->notify('[CRON] Garantia Reurbano', 'O sistema avisou o comprador do pedido Cód.  '.$order->getId().', que seu cupom tem '.$daysGarantia.' dias ainda na Garantia Reurbano<br /><br />
		                Comprador: '.$order->getUser()->getName().' ('.$order->getUser()->getEmail().')<br />
		                Vendedor:  '.$order->getSeller()->getName().' ('.$order->getSeller()->getEmail().')<br />
		                Cód. Venda: '.$order->getId().' <br />
		                Oferta: <b>'.$order->getQuantity().'x - '.$order->getDeal()->getLabel().'</b><br />
		                Total: R$ '.  number_format($order->getTotal(), 2, ',', '').'<br />
		                Link para a venda: <a href="'.$orderLinkAdmin.'">'.$orderLinkAdmin.'</a><br />');
								}
            }
        }else{
            $output->writeln("<error>Nenhum Pedido Encontrado</error>");
        }
    }
    
    /**
     * Converta pra dias a data em segundos
    */
    protected function converToDay($value = 0){
    	return floor($value / (60 * 60 * 24));
    }
}