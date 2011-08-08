<?php

namespace Reurbano\OrderBundle\DataFixtures\MongoDB;

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
            // Pega menu filho "Cidades" dentro do City Admin
            $menuItem = $repo->getChildrenByCode($menu, 'vendas');
            if(!$menuItem){ // Só adiciona o novo menu se ele já não existir
                $child = new MenuItem();
                $child->setCode('vendas');
                $child->setName('Vendas');
                $child->setRole('ROLE_ADMIN');
                $child->setUrl('admin_order_order_index');
                $child->setRoute(true);
                $child->setOrder(2);
                
                $menu->addChildren($child);
                $manager->persist($menu);
                $manager->flush();
                
                $child2 = new MenuItem();
                $child2->setCode('vendas.status');
                $child2->setName('Status');
                $child2->setRole('ROLE_ADMIN');
                $child2->setUrl('admin_order_status_index');
                $child2->setRoute(true);
                $child->addChildren($child2);
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