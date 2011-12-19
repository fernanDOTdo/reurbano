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
 * Reurbano/DealBundle/Command/DeactivateDealCommand.php
 *
 * Comando para desativar as ofertas vencidas e avisar o vendedor
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

class DeactivateDealCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('reurbano:cron:deactivatedeal')
            ->setDescription('Desativa ofertas vencidas e avisa os vendedores')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();
        $date = new \DateTime();
        $dm = $container->get('mastop.dm');
        $mail = $container->get('mastop.mailer');
        $dealRepo = $dm->getRepository('ReurbanoDealBundle:Deal');
        $deals = $dealRepo->createQueryBuilder()
                ->field('active')->equals(true) // Ofertas Ativas
                ->field('quantity')->gt(0) // Quantidade maior que zero
                ->field('source.expiresAt')->lte($date) // Data de vencimento menor ou igual ao "date"
                ->sort('user.id', 'asc') // Ordena por usuário
                ->getQuery()
                ->execute();
        if($deals->count()){
            foreach($deals as $deal){
                $output->writeln("Desativando oferta <info>".$deal->getLabel()."</info> do vendedor <info>".$deal->getUser()->getEmail()."</info> com vencimento em <comment>".$deal->getSource()->getExpiresAt()->format('d/m/Y H:i:s')."</comment>");
                // Desativa a oferta
                $dealRepo->updateActive($deal->getId());
                $dm->getRepository('ReurbanoCoreBundle:Banner')->deleteByDeal($deal->getId());
                // Notifica vendedor
                $mail->to($deal->getUser())
                        ->subject('Sua oferta expirou')
                        ->template('oferta_expirada',array(
                            'title'  => 'Oferta Expirada',
                            'user'  => $deal->getUser(),
                            'deal' => $deal,
                        ))
                        ->send();
                // Notifica o administrador
                $mail->notify('[CRON] Oferta Desativada', 'O sistema desativou a oferta '.$deal->getLabel().'<br /><br />
                Vendedor:  <b>'.$deal->getUser()->getName().' ('.$deal->getUser()->getEmail().')</b><br />
                Validade:  '.$deal->getSource()->getExpiresAt()->format('d/m/Y').'<br />
                De: R$ '.number_format($deal->getSource()->getPrice(), 2, ',', '').' <br />
                Por: R$ '.number_format($deal->getPrice(), 2, ',', '').' <br />
                Desconto: '.$deal->getDiscount().'%<br />
                Cupons: '.$deal->getQuantity().'<br />
                Visualizações: '.  $deal->getViews().'<br />');
            }
        }else{
            $output->writeln("<error>Nenhuma Oferta Encontrada</error>");
        }        
    }

}
