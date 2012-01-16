<?php
/**
 * Reurbano/OrderBundle/Command/FinalizeOrdersCommand.php
 *
 * Comando para finalizar pedidos aprovados depois de X dias da venda concluída
 *  
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

class FinalizeOrdersCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('reurbano:cron:finalizeorders')
            ->setDescription('Finaliza os pedidos aprovados')
            ->addArgument('days', InputArgument::OPTIONAL, 'Quantos dias para finalizar?', 2)
            ->setHelp(<<<EOT
O <info>reurbano:cron:finalizeorders</info> para finalizar os pedidos aprovados depois de X dias da venda concluída

<info>./app/console reurbano:cron:finalizeorders 1</info>

EOT
        );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();
        $days = $input->getArgument('days');
        $date = new \DateTime();
        $date->setTimestamp(strtotime('-'.$days.' days'));
        $dm = $container->get('mastop.dm');
        $mail = $container->get('mastop.mailer');
        
        $dealRepo = $dm->getRepository('ReurbanoDealBundle:Deal');
        $statusRepo = $dm->getRepository('ReurbanoOrderBundle:Status');
        $orderRepo = $dm->getRepository('ReurbanoOrderBundle:Order');
        
        $orders = $orderRepo->createQueryBuilder()
                ->field('created')->lte($date) // Data de criação for menor ou igual ao definido na opção "days"
                ->field('status.id')->equals(4) // Status for igual a 4 (Aprovado)
                ->field('payment.data.status_description')->notEqual("in_process") // pedidos que não estejam "processando" pelo Mercado Pago
                ->getQuery()
                ->execute();
                
        $status = $statusRepo->find(7);// Status for igual a 7 (Finalizado)
        if($orders->count()){
            foreach($orders as $order){
            	
                // Verifica se é para liberar o dinheiro para o vendedor
		            $releaseMoney = explode(',', $container->get('mastop')->param('order.all.releasestatus'));
		            if(count($releaseMoney) > 0 && in_array($status->getId(), $releaseMoney)){
		            		$orderLinkSeller = $container->get('router')->generate('order_mysales_view', array('id'=>$order->getId()), true);
		                $orderLinkAdmin = $container->get('router')->generate('admin_order_order_view', array('id'=>$order->getId()), true);
		                $output->writeln("Pedido Finalizado ".$order->getId());
		                
		                // Adiciona o status em StatusLog           
		                $statusLog = new StatusLog();
		                $statusLog->setUser($order->getUser());
		                $statusLog->setStatus($status);
										
		                $order->setStatus($status);
		            		$order->addStatusLog($statusLog);
		            		$dm->persist($order);
		            		
		            		// Libera o checkout para o vendedor no escrow
		                $dm->getRepository('ReurbanoOrderBundle:Escrow')->releaseOrder($order);
		                
		                $dm->flush();
		            }
                 
                //Notifica vendedor para efetuar o check-out do dinheiro
                $nSeller = $container->get('mastop.mailer');
                $nSeller->to($order->getSeller())
                        ->subject('Venda Finalizada')
                        ->template('pedido_finalizado',array(
                            'user'  => $order->getSeller(),
                            'order' => $order,
                            'msg' => false,
                            'orderLink' => $orderLinkSeller,
                        ))
                        ->send();

                // Notifica o administrador do cancelamento
                $mail->notify('[CRON] Venda Finalizada', 'O sistema cancelou a venda Cód.  '.$order->getId().'<br /><br />
	                Comprador: '.$order->getUser()->getName().' ('.$order->getUser()->getEmail().')<br />
	                Vendedor:  '.$order->getSeller()->getName().' ('.$order->getSeller()->getEmail().')<br />
	                Cód. Venda: '.$order->getId().' <br />
	                Oferta: <b>'.$order->getQuantity().'x - '.$order->getDeal()->getLabel().'</b><br />
	                Total: R$ '.  number_format($order->getTotal(), 2, ',', '').'<br />
	                Link para a venda: <a href="'.$orderLinkAdmin.'">'.$orderLinkAdmin.'</a><br />');
            }
        }else{
            $output->writeln("<error>Nenhum Pedido Encontrado</error>");
        }   
    }
}
