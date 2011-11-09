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
 * Reurbano/DealBundle/Command/ExpiresNotifyCommand.php
 *
 * Comando para avisar ao vendedor que a oferta vai vencer
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

namespace Reurbano\DealBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExpiresNotifyCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('reurbano:cron:expiresnotify')
            ->setDescription('Envia aviso aos vendedores de ofertas que vencerão em X dias')
            ->addArgument('days', InputArgument::OPTIONAL, 'Quantos dias para vencer a oferta?', 3)
            //->addOption('opcao', null, InputOption::VALUE_NONE, 'Descrição da opção')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();
        $days = $input->getArgument('days');
        $date = new \DateTime();
        $date->setTimestamp(strtotime('+'.$days.' days'));
        $date->setTime(0, 0, 0);
        $dm = $container->get('mastop.dm');
        $mail = $container->get('mastop.mailer');
        $output->writeln("Consultando ofertas com vencimento em ".date('d/m/Y H:i:s', $date->getTimestamp()));
        $dealRepo = $dm->getRepository('ReurbanoDealBundle:Deal');
        $deals = $dealRepo->createQueryBuilder()
                ->field('active')->equals(true) // Ofertas Ativas
                ->field('quantity')->gt(0) // Quantidade maior que zero
                ->field('source.expiresAt')->equals($date) // Data de vencimento igual ao "date"
                ->sort('user.id', 'asc') // Ordena por usuário
                ->getQuery()
                ->execute();
        if($deals->count()){
            foreach($deals as $deal){
                $output->writeln("Notificando vendedor <info>".$deal->getUser()->getEmail()."</info> da oferta <info>".$deal->getLabel()."</info>");
                // Notifica vendedor
                $dealEditLink = $container->get('router')->generate('deal_deal_edit', array('id'=>$deal->getId()), true);
                $dealLink = $container->get('router')->generate('deal_deal_show', array('city'=>$deal->getSource()->getCity()->getSlug(), 'category' => $deal->getSource()->getCategory()->getSlug(), 'slug' => $deal->getSlug()), true);
                $mail->to($deal->getUser())
                        ->subject('Sua oferta vai expirar em '.$days.' dias')
                        ->template('oferta_expirando_aviso',array(
                            'title'  => 'Oferta Expirando',
                            'days'  => $days,
                            'user'  => $deal->getUser(),
                            'deal' => $deal,
                            'dealEditLink' => $dealEditLink,
                            'dealLink' => $dealLink,
                        ))
                        ->send();
            }
        }else{
            $output->writeln("<error>Nenhuma Oferta Encontrada</error>");
        }
    }

}
