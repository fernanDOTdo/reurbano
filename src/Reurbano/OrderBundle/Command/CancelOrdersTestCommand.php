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
 * Reurbano/OrderBundle/Command/CancelOrdersTestCommand.php
 *
 * Teste de Comando para cancelar pedidos que estão pendentes há X dias
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

namespace Reurbano\OrderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Reurbano\OrderBundle\Document\StatusLog;

class CancelOrdersTestCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('reurbano:cron:cancelorders:test')
            ->setDescription('Testa o cancelamento dos pedidos pendentes')
            ->addArgument('days', InputArgument::OPTIONAL, 'Quantos dias atrás?', 1)
            //->addOption('opcao', null, InputOption::VALUE_NONE, 'Descrição da opção')
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
        $orderRepo = $dm->getRepository('ReurbanoOrderBundle:Order');
        $orders = $orderRepo->createQueryBuilder()
                ->field('created')->lte($date) // Data de criação for menor ou igual ao definido na opção "days"
                ->field('status.id')->equals(1) // Status for igual a 1 (Pendente)
                ->getQuery()
                ->execute();
        $orders1 = $orderRepo->createQueryBuilder()
                ->field('created')->lte($date) // Data de criação for menor ou igual ao definido na opção "days"
                ->field('status.id')->equals(1) // Status for igual a 1 (Pendente)
                ->field('payment.data.status_description')->notEqual("in_process") // pedidos que não estejam "processando" pelo Mercado Pago
                ->getQuery()
                ->execute();
        if($orders->count()){
            $output->writeln("<info>[0] ".$orders->count()." Pedidos</info>");
            foreach($orders as $order){
                $output->writeln("[0] Pedido ".$order->getId());
            }
        }else{
            $output->writeln("<error>[0] Nenhum Pedido Encontrado</error>");
        }
        if($orders1->count()){
            $output->writeln("<info>[1] ".$orders1->count()." Pedidos</info>");
            foreach($orders1 as $order){
                $output->writeln("[1] Pedido ".$order->getId());
            }
        }else{
            $output->writeln("<error>[1] Nenhum Pedido Encontrado</error>");
        }
        
        
    }

}
