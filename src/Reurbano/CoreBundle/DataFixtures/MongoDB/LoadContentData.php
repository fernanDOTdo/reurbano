<?php
/**
 * Reurbano/CoreBundle/DataFixtures/MongoDB/LoadContentData.php
 *
 * Carrega páginas padrão.
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

namespace Reurbano\CoreBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Reurbano\CoreBundle\Document\Content;

class LoadContentData implements FixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load($manager) {
        $paginas = array('FAQ', 'Como Comprar', 'Como Vender', 'Sobre o Reurbano', 'Privacidade', 'Termos e Condições');
        foreach ($paginas as $pg) {
            $Content = new Content();
            $Content->setTitle($pg);
            $Content->setSeoTitle($pg);
            $Content->setContent('Página <strong>'.$pg.'</strong>');
            $manager->persist($Content);
        }
        $manager->flush();
    }
}