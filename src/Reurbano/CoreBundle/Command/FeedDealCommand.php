<?php

/**
 * 
 * Reurbano/CoreBundle/Command/FeedDealCommand.php
 *
 * Gerador de feed das ofertas do Reurbano
 *  
 * 
 * @copyright 2011 Mastop Internet Development.
 * @link http://www.mastop.com.br
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

namespace Reurbano\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Filesystem;

class FeedDealCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('reurbano:cron:feedDeal')
                ->setDescription('Gera feed das ofertas do sistema Reurbano');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();
        $dm = $container->get('mastop.dm');
        
        $dom = new \DOMDocument('1.0', 'UTF-8');
        
        $filesystem = new Filesystem();
                
        // agregadores parceiros
        $associatesDB = $container->get('mastop')->getDocumentManager("associate")->getRepository('ReurbanoAnalyticsBundle:Associate')->getByFind('aggregator', true);
        $parceiros = array();
        $feedealFile = array();
        $index = 0;
        if($associatesDB->count()){
        	foreach($associatesDB as $associate){
        		$xml[$index] = $dom->createElement('xml');
        		$xml[$index]->setAttribute('version', '1.0');
        		$xml[$index]->setAttribute('encoding', 'UTF-8');
        		$xml[$index]->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        		
        		$parceiros[$index] = $associate->getSlug();
        		
        		$feedealFile[$index] = $container->get('kernel')->getRootDir().'/../web/feedeal-'.$associate->getSlug().'.xml';
        		$output->writeln("Gerando o feed das ofertas em <question>".$feedealFile[$index]."</question>");
        		$filesystem->touch($feedealFile[$index]);
        		
        		$elementDeals[$index] = $dom->createElement('deals');
        		
        		$index++;
        	}
        }
        
        // página das Ofertas
        
        $dealRepo = $dm->getRepository('ReurbanoDealBundle:Deal');
        $deals = $dealRepo->findBy(array('active' => true, 'checked' => true), array('createdAt'=>'desc')); // Todas as ofertas ativas ordenadas por data
        if($deals->count()){
            foreach($deals as $deal){
            	$index = 0;
            	foreach($parceiros as $parceiro){
            		$elementDeal = $dom->createElement('deal');
	            	$paramTracking = "?utm_source=".$parceiros[$index]."&utm_medium=agregador&utm_campaign=".$deal->getSource()->getCategory()->getSlug();
	            	
	            	// Link da oferta
	                $dealLink = $container->get('router')->generate('deal_deal_show', array('city'=>$deal->getSource()->getCity()->getSlug(), 'category' => $deal->getSource()->getCategory()->getSlug(), 'slug' => $deal->getSlug()), true).$paramTracking;
	                $dealLink = $dealLink.$paramTracking;
	                
	                // Título da oferta 
	                $dealTitle = preg_replace("'\s+'", ' ', $deal->getLabel());
	                $dealTitle = trim($dealTitle, ' -');
	                
	                // Imagem da oferta
	                $dealFilename = "http:".$container->get('mastop')->param('deal.all.dealurl').$deal->getSource()->getFilename();
	                
	                // Preço da oferta de venda
	                $dealPrice = $deal->getPrice();
	                	                
	                // Preço Original da oferta
	                $dealRegularPrice = $deal->getSource()->getPrice();
	                
	                // Quantidade disponível da oferta
	                $dealQuantity = $deal->getQuantity();
	                	                
	                // Categora da oferta
	                $dealCategory = preg_replace("'\s+'", ' ', $deal->getSource()->getCategory()->getName());
	                $dealCategory = trim($dealCategory, ' -');
	                
	                // Cidade da oferta
	                $dealCity = preg_replace("'\s+'", ' ', $deal->getSource()->getCity()->getName());
	                $dealCity = trim($dealCity, ' -');
	                
	                // Observação do Vendedor
	                $dealObs = preg_replace("'\s+'", ' ', $deal->getObs());
	                $dealObs = trim($dealObs, ' -');
	                
	                
	                // Adicionando os elementos ao XML
	                $title = $dom->createElement('title');
	                $title->appendChild($dom->createCDATASection($dealTitle));
	                $elementDeal->appendChild($title);
	                
	                $elementDeal->appendChild($dom->createElement('link', htmlentities($dealLink)));
	                $elementDeal->appendChild($dom->createElement('image', htmlentities($dealFilename)));
	                $elementDeal->appendChild($dom->createElement('price', $dealPrice));
	                $elementDeal->appendChild($dom->createElement('regularprice', $dealRegularPrice));
	                $elementDeal->appendChild($dom->createElement('quantity', $dealQuantity));
	                
	                $city = $dom->createElement('city');
	                $city->appendChild($dom->createCDATASection($dealCity));
	                $elementDeal->appendChild($city);
	                
	                $category = $dom->createElement('category');
	                $category->appendChild($dom->createCDATASection($dealCategory));
	                $elementDeal->appendChild($category);
	                
	                // Data de expiração da oferta
	                $elementDeal->appendChild($dom->createElement('expiresAt', $deal->getSource()->getExpiresAt()->format('Y-m-d')));
	                
	                $obs = $dom->createElement('observation');
	                $obs->appendChild($dom->createCDATASection($dealObs));
	                $elementDeal->appendChild($obs);
	                	                
	                $elementDeals[$index]->appendChild($elementDeal);
	                $index++;
            	}
            }
        }
        
        $index = 0;
        foreach($elementDeals as $element){
        	// Joga o XML criado no $dom
        	$xml[$index]->appendChild($element);
        	$index++;
        }
                
        $index = 0;
        $return = true;
        foreach($parceiros as $parceiro){
        	$dom->appendChild($xml[$index]);
        	$return = file_put_contents($feedealFile[$index], $dom->saveXml()); // Coloca o conteúdo do XML no arquivo e retorna
        	$dom->removeChild($xml[$index]);
        	$index++;
        }
        
        return $return; // Coloca o conteúdo do XML no arquivo e retorna
    }

}