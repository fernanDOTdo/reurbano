<?php

namespace Reurbano\CoreBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mastop\SystemBundle\Document\Parameters;
use Mastop\SystemBundle\Document\Children;

class LoadParametersData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load($manager) {
        $param = new Parameters();
        $param->setName('banner');
        $param->setTitle('Banners');
        $param->setDesc('Configurações para os banners');
        $param->setBundle('core');
        $param->setRole('ROLE_ADMIN');
        $param->setOrder(5);

        $child = new Children();
        $child->setName('loadnum');
        $child->setTitle('Número de Banners');
        $child->setDesc('Digite o número de banners que o sistema carregará.');
        $child->setValue(5);
        $child->setOrder(0);
        $param->addChildren($child);
        $manager->persist($param);
        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }

}