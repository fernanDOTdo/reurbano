<?php

namespace Reurbano\CoreBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Reurbano\CoreBundle\Document\City;

class LoadCityData implements FixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load($manager) {
        $cidades = array('Oferta Nacional','São Paulo', 'Rio de Janeiro', 'Campinas', 'Itatiba', 'Belo Horizonte', 'São Bernardo do Campo');
        foreach ($cidades as $cidade) {
            $City = new City();
            $City->setName($cidade);
            $City->setSpecial(rand(0, 1));
            $manager->persist($City);
        }
        $manager->flush();
    }
}