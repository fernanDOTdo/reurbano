<?php

namespace Reurbano\DealBundle\DataFixtures\MongoDB;

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
        $param->setName('all');
        $param->setTitle('Ofertas');
        $param->setDesc('Configurações para o módulo de ofertas');
        $param->setBundle('deal');
        $param->setOrder(2);

        $child = new Children();
        $child->setName('perpage');
        $child->setTitle('Número de Ofertas por Página');
        $child->setDesc('O número de ofertas por página em todo o site é definido neste campo.');
        $child->setValue(10);
        $child->setOrder(0);
        $param->addChildren($child);
        
        $child = new Children();
        $child->setName('dealsort');
        $child->setTitle('Ordenação Padrão');
        $child->setDesc('Selecione a ordenação padrão das ofertas');
        $child->setValue('sortNew');
        $child->setFieldtype('choice');
        $child->setOpts(array('choices' => array('sortNew' => 'Mais Novos', 'sortCheap' => 'Mais Baratos', 'sortExpires' => 'Vencimento', 'sortDiscount' => 'Maior Desconto')));
        $child->setOrder(1);
        $param->addChildren($child);
        
        $manager->persist($param);
        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }

}