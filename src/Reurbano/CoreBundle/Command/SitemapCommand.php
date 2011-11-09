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
 * Reurbano/CoreBundle/Command/SitemapCommand.php
 *
 * Gerador de Sitemap
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

use Symfony\Component\HttpKernel\Util\Filesystem;

class SitemapCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('reurbano:sitemap')
                ->setDescription('Gera sitemap do sistema Reurbano');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();
        $dm = $container->get('mastop.dm');
        $filesystem = new Filesystem();
        $sitemapFile = $container->get('kernel')->getRootDir().'/../web/sitemap.xml';
        $output->writeln("Gerando sitemap em <question>".$sitemapFile."</question>");
        $filesystem->touch($sitemapFile);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $sitemap = $dom->createElement('urlset');
        $sitemap->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $sitemap->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        
        // Páginas de ofertas
        $dealRepo = $dm->getRepository('ReurbanoDealBundle:Deal');
        $deals = $dealRepo->findBy(array('active' => true), array('createdAt'=>'desc')); // Todas as ofertas ativas ordenadas por data
        
        if($deals->count()){
            foreach($deals as $deal){
                $dealLink = $container->get('router')->generate('deal_deal_show', array('city'=>$deal->getSource()->getCity()->getSlug(), 'category' => $deal->getSource()->getCategory()->getSlug(), 'slug' => $deal->getSlug()), true);
                $url = $dom->createElement('url');
                $url->appendChild($dom->createElement('loc', $dealLink));
                if($deal->getUpdatedAt()){
                    $url->appendChild($dom->createElement('lastmod', $deal->getUpdatedAt()->format('Y-m-d')));
                }
                $url->appendChild($dom->createElement('priority', '0.9'));
                $url->appendChild($dom->createElement('changefreq', 'weekly'));
                $sitemap->appendChild($url);
            }
        }
        
        // Cidades e Categorias
        
        $cityRepo = $dm->getRepository('ReurbanoCoreBundle:City');
        $catRepo = $dm->getRepository('ReurbanoDealBundle:Category');
        $cities = $cityRepo->findAll();
        $categories = $catRepo->findAllByOrder();
        if($cities->count()){
            foreach($cities as $city){
                $cityLink = $container->get('router')->generate('core_city_index', array('slug' => $city->getSlug()), true);
                $url = $dom->createElement('url');
                $url->appendChild($dom->createElement('loc', $cityLink));
                $url->appendChild($dom->createElement('priority', '0.7'));
                $url->appendChild($dom->createElement('changefreq', 'daily'));
                $sitemap->appendChild($url);
                // Categorias dentro das cidades
                foreach ($categories as $category) {
                    $categoryLink = $container->get('router')->generate('deal_category_index', array('city' => $city->getSlug(), 'slug' => $category->getSlug()), true);
                    $url = $dom->createElement('url');
                    $url->appendChild($dom->createElement('loc', $categoryLink));
                    $url->appendChild($dom->createElement('priority', '0.8'));
                    $url->appendChild($dom->createElement('changefreq', 'daily'));
                    $sitemap->appendChild($url);
                }
            }
        }
        
        // Páginas estáticas
        $conentRepo = $dm->getRepository('ReurbanoCoreBundle:Content');
        $contents = $conentRepo->findAllByCreated();
        
        if($contents->count()){
            foreach($contents as $content){
                $contentLink = $container->get('router')->generate('core_content_index', array('slug' => $content->getSlug()), true);
                $url = $dom->createElement('url');
                $url->appendChild($dom->createElement('loc', $contentLink));
                if($content->getUpdated()){
                    $url->appendChild($dom->createElement('lastmod', $content->getUpdated()->format('Y-m-d')));
                }
                $url->appendChild($dom->createElement('priority', '0.6'));
                $url->appendChild($dom->createElement('changefreq', 'monthly'));
                $sitemap->appendChild($url);
            }
        }
        
        // Contato
        $url = $dom->createElement('url');
        $url->appendChild($dom->createElement('loc', $container->get('router')->generate('core_html_contact', array(), true)));
        $url->appendChild($dom->createElement('priority', '0.8'));
        $url->appendChild($dom->createElement('changefreq', 'never'));
        
        $sitemap->appendChild($url);
        
        // Página de Novo Usuário
        $url = $dom->createElement('url');
        $url->appendChild($dom->createElement('loc', $container->get('router')->generate('user_user_new', array(), true)));
        $url->appendChild($dom->createElement('priority', '0.8'));
        $url->appendChild($dom->createElement('changefreq', 'never'));
        
        $sitemap->appendChild($url);
        
        // Joga o XML criado no $dom
        
        $dom->appendChild($sitemap);
        
        return file_put_contents($sitemapFile, $dom->saveXml()); // Coloca o conteúdo do XML no arquivo e retorna
    }

}