<?php

namespace Reurbano\DealBundle\DataFixtures\MongoDB;

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
        if($menu){
            $menuItem = $repo->getChildrenByCode($menu, 'ofertas');
            if(!$menuItem){ // Só adiciona o novo menu se ele já não existir
                $child = new MenuItem();
                $child->setCode('ofertas');
                $child->setName('Ofertas');
                $child->setRole('ROLE_ADMIN');
                $child->setUrl('admin_deal_deal_index');
                $child->setRoute(true);
                $child->setOrder(6);
                $menu->addChildren($child);
                $manager->persist($menu);
                $manager->flush();
                
                $child2 = new MenuItem();
                $child2->setCode('ofertas.categorias');
                $child2->setName('Categorias');
                $child2->setRole('ROLE_ADMIN');
                $child2->setUrl('admin_deal_category_index');
                $child2->setRoute(true);
                $child->addChildren($child2);
                $manager->persist($menu);
                $manager->flush();
                
                $child3 = new MenuItem();
                $child3->setCode('ofertas.sites');
                $child3->setName('Sites');
                $child3->setRole('ROLE_ADMIN');
                $child3->setUrl('admin_deal_site_index');
                $child3->setRoute(true);
                $child->addChildren($child3);
                $manager->persist($menu);
                $manager->flush();
                
                $child4 = new MenuItem();
                $child4->setCode('ofertas.crawler');
                $child4->setName('Crawler');
                $child4->setRole('ROLE_ADMIN');
                $child4->setUrl('admin_deal_source_index');
                $child4->setRoute(true);
                $child->addChildren($child4);
                $manager->persist($menu);
                $manager->flush();
                
                $child5 = new MenuItem();
                $child5->setCode('ofertas.a-conferir');
                $child5->setName('A conferir');
                $child5->setRole('ROLE_ADMIN');
                $child5->setUrl('admin_deal_deal_checked');
                $child5->setRoute(true);
                $child->addChildren($child5);
                $manager->persist($menu);
                $manager->flush();
            }
        }
    }
    public function getOrder()
    {
        return 3;
    }
}