<?php

namespace Reurbano\CoreBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mastop\MenuBundle\Document\Menu;
use Mastop\MenuBundle\Document\MenuItem;

class LoadMenuData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load($manager) {
        $repo = $manager->getRepository('MastopMenuBundle:Menu');
        // Pega menu Pai System-Admin
        $menu = $repo->findByBundleCode('system', 'admin');
        if ($menu) {
            // Pega menu filho "Cidades" dentro do City Admin
            $menuItem = $repo->getChildrenByCode($menu, 'cidades');
            if (!$menuItem) { // Só adiciona o novo menu se ele já não existir
                $child = new MenuItem();
                $child->setCode('cidades');
                $child->setName('Cidades');
                $child->setRole('ROLE_ADMIN');
                $child->setUrl('admin_core_city_index');
                $child->setRoute(true);
                $child->setOrder(1);
                $menu->addChildren($child);
                $manager->persist($menu);
                $manager->flush();

                $child2 = new MenuItem();
                $child2->setCode('city.novo');
                $child2->setName('Adicionar');
                $child2->setRole('ROLE_ADMIN');
                $child2->setUrl('admin_core_city_form');
                $child2->setRoute(true);
                $child->addChildren($child2);
                $manager->persist($menu);
                $manager->flush();
            }
            $menuItem2 = $repo->getChildrenByCode($menu, 'email');
            if(!$menuItem2){
                $child = new MenuItem();
                $child->setCode('email');
                $child->setName('E-mails');
                $child->setRole('ROLE_ADMIN');
                $child->setUrl('admin_core_mailing_index');
                $child->setRoute(true);
                $child->setOrder(4);
                $menu->addChildren($child);
                $manager->persist($menu);
                $manager->flush(); 
            }
            $menuItem3 = $repo->getChildrenByCode($menu, 'banner');
            if(!$menuItem3){
                $child = new MenuItem();
                $child->setCode('banner');
                $child->setName('Banners');
                $child->setRole('ROLE_ADMIN');
                $child->setUrl('admin_core_banner_index');
                $child->setRoute(true);
                $child->setOrder(5);
                $menu->addChildren($child);
                $manager->persist($menu);
                $manager->flush(); 
            }
            $menuItem4 = $repo->getChildrenByCode($menu, 'content');
            if(!$menuItem4){
                $child = new MenuItem();
                $child->setCode('content');
                $child->setName('Páginas');
                $child->setRole('ROLE_ADMIN');
                $child->setUrl('admin_core_content_index');
                $child->setRoute(true);
                $child->setOrder(6);
                $menu->addChildren($child);
                $manager->persist($menu);
                $manager->flush();
                
                $child2 = new MenuItem();
                $child2->setCode('content.novo');
                $child2->setName('Adicionar');
                $child2->setRole('ROLE_ADMIN');
                $child2->setUrl('admin_core_content_form');
                $child2->setRoute(true);
                $child->addChildren($child2);
                $manager->persist($menu);
                $manager->flush();
            }
        }
        // Pega menu Pai System-Foot
        $menu = $repo->findByBundleCode('system', 'foot');
        if ($menu) {
            // Pega menu filho "Empresa" dentro do Foot
            $menuItem = $repo->getChildrenByCode($menu, 'empresa');
            if (!$menuItem) { // Só adiciona o novo menu se ele já não existir
                $child = new MenuItem();
                $child->setCode('empresa');
                $child->setName('Empresa');
                $menu->addChildren($child);
                $manager->persist($menu);
                $manager->flush();
                    $child2 = new MenuItem();
                    $child2->setCode('empresa.sobre');
                    $child2->setName('Sobre');
                    $child2->setUrl('/pg/sobre-o-reurbano');
                $child->addChildren($child2);
                $manager->persist($menu);
                $manager->flush();
                    $child2 = new MenuItem();
                    $child2->setCode('empresa.contato');
                    $child2->setName('Contato');
                    $child2->setUrl('/fale-conosco');
                    $child2->setOrder(1);
                $child->addChildren($child2);
                $manager->persist($menu);
                $manager->flush();
                    $child2 = new MenuItem();
                    $child2->setCode('empresa.privacidade');
                    $child2->setName('Privacidade');
                    $child2->setUrl('/pg/privacidade');
                    $child2->setOrder(2);
                $child->addChildren($child2);
                $manager->persist($menu);
                $manager->flush();
                    $child2 = new MenuItem();
                    $child2->setCode('empresa.termos-e-condicoes');
                    $child2->setName('Termos e Condições');
                    $child2->setUrl('/pg/termos-e-condicoes');
                    $child2->setOrder(3);
                $child->addChildren($child2);
                $manager->persist($menu);
                $manager->flush();
            }
            $menuItem2 = $repo->getChildrenByCode($menu, 'saiba-mais');
            if(!$menuItem2){
                $child = new MenuItem();
                $child->setCode('saiba-mais');
                $child->setName('Saiba Mais');
                $menu->addChildren($child);
                $manager->persist($menu);
                $manager->flush();
                    $child2 = new MenuItem();
                    $child2->setCode('saiba-mais.faq');
                    $child2->setName('FAQ');
                    $child2->setUrl('/pg/faq');
                $child->addChildren($child2);
                $manager->persist($menu);
                $manager->flush();
                    $child2 = new MenuItem();
                    $child2->setCode('saiba-mais.como-comprar');
                    $child2->setName('Como Comprar');
                    $child2->setUrl('/pg/como-comprar');
                    $child2->setOrder(1);
                $child->addChildren($child2);
                $manager->persist($menu);
                $manager->flush();
                    $child2 = new MenuItem();
                    $child2->setCode('saiba-mais.como-vender');
                    $child2->setName('Como Vender');
                    $child2->setUrl('/pg/como-vender');
                    $child2->setOrder(2);
                $child->addChildren($child2);
                $manager->persist($menu);
                $manager->flush(); 
            }
        }
    }

    public function getOrder() {
        return 2;
    }

}