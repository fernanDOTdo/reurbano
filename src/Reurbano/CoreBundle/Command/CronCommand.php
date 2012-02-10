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
 * Reurbano/CoreBundle/Command/CronCommand.php
 *
 * Base para o Cronjob
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

namespace Reurbano\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('reurbano:cron')
                ->setDescription('Executa tarefas do Cron')
                ->addArgument('period', InputArgument::OPTIONAL, 'Qual período? (day:week:month)', 'day')
        //->addOption('opcao', null, InputOption::VALUE_NONE, 'Descrição da opção')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $period = $input->getArgument('period');
        switch ($period) {
            case 'month':
                break;
            case 'week':
                // Gerar o sitemap
                $command = $this->getApplication()->find('reurbano:sitemap');
                $arguments = array(
                    'command' => 'reurbano:sitemap',
                );
                $input = new ArrayInput($arguments);
                $returnCode = $command->run($input, $output);
                break;
            case 'day':
            default :
                // Cancelar todos os pedidos que foram feitos há 1 dia atrás e estão com o status "pendente" ainda.
                $output->writeln("<question>Cancelando pedidos não pagos há 24 horas</question>");
                $command = $this->getApplication()->find('reurbano:cron:cancelorders');
                $arguments = array(
                    'command' => 'reurbano:cron:cancelorders',
                    'days' => 2,
                );
                $input = new ArrayInput($arguments);
                $returnCode = $command->run($input, $output);
                // Desativar ofertas vencidas
                $output->writeln("<question>Desativando ofertas vencidas</question>");
                $command = $this->getApplication()->find('reurbano:cron:deactivatedeal');
                $arguments = array(
                    'command' => 'reurbano:cron:deactivatedeal',
                );
                $input = new ArrayInput($arguments);
                $returnCode = $command->run($input, $output);
                $output->writeln("<question>Notificando vendedores de ofertas que vencerão em 3 dias</question>");
                // Envia aviso aos vendedores de ofertas que vencerão em X dias
                $command = $this->getApplication()->find('reurbano:cron:expiresnotify');
                $arguments = array(
                    'command' => 'reurbano:cron:expiresnotify',
                    'days' => 3,
                );
                $input = new ArrayInput($arguments);
                $returnCode = $command->run($input, $output);
                
                // Finalizar os pedidos aprovados depois de 30 dias da venda concluída
                $output->writeln("<question>Notificando os vendedores para efetuarem seus check-out</question>");
                $command = $this->getApplication()->find('reurbano:cron:finalizeorders');
                $arguments = array(
                    'command' => 'reurbano:cron:finalizeorders',
                    'days' => 30,
                );
                $input = new ArrayInput($arguments);
                $returnCode = $command->run($input, $output);
                
                // avisar o comprador depois de X dias que a negociação foi aprovada, 
 								// que ele tem mais X dias na garantia se o cupom ainda for válido
                $output->writeln("<question>Notificando os compradores sobre sua garantia</question>");
                $command = $this->getApplication()->find('reurbano:cron:finalizeorders');
                $arguments = array(
                    'command' => 'reurbano:cron:noticeclient',
                    'days' => 20,
                );
                $input = new ArrayInput($arguments);
                $returnCode = $command->run($input, $output);                
                
                break;
        }
    }

}
