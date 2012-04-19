<?php

namespace Reurbano\AnalyticsBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Reurbano\AnalyticsBundle\Document\ZoneType;

class LoadZoneTypeData implements FixtureInterface, ContainerAwareInterface {

	private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

   
    public function load(ObjectManager $manager) {
        $types = array("Indefinida", "Oferta", "Categoria", "Site", "Banner", "Minibanner", "Filtro", "Midia");
        foreach ($types as $type) {
            $ZoneType = new ZoneType();
            $ZoneType->setName($type);
            $manager->persist($ZoneType);
        }
        $manager->flush();
    }

}