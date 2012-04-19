<?php

namespace Reurbano\AnalyticsBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Reurbano\AnalyticsBundle\Document\Zone;
use Reurbano\AnalyticsBundle\Document\ZoneType;

class LoadZoneData implements FixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load(ObjectManager $manager) {
    	$zoneTypeOferta = $manager->getRepository('ReurbanoAnalyticsBundle:ZoneType')->findBySlug('oferta');
    	$zoneTypeCategoria = $manager->getRepository('ReurbanoAnalyticsBundle:ZoneType')->findBySlug('categoria');
    	$zoneTypeSite = $manager->getRepository('ReurbanoAnalyticsBundle:ZoneType')->findBySlug('site');
    	$zoneTypeBanner = $manager->getRepository('ReurbanoAnalyticsBundle:ZoneType')->findBySlug('banner');
    	$zoneTypeIndefinida = $manager->getRepository('ReurbanoAnalyticsBundle:ZoneType')->findBySlug('indefinida');
    	
		$Zone = new Zone();
        $Zone->setName('Mais Clicados');
        $Zone->setDescription('Zona das ofertas mais clicados');
        $Zone->setRestricted(false);
        $Zone->setZoneType($zoneTypeOferta);
        $manager->persist($Zone);
        
        $Zone = new Zone();
        $Zone->setName('Mais Cupons para Revenda');
        $Zone->setDescription('Zona das ofertas para revenda');
        $Zone->setRestricted(false);
        $Zone->setZoneType($zoneTypeOferta);
        $manager->persist($Zone);
        
        $Zone = new Zone();
        $Zone->setName('Últimos Clicados');
        $Zone->setDescription('Zona das ofertas que foram as últimas clicadas');
        $Zone->setRestricted(false);
        $Zone->setZoneType($zoneTypeOferta);
        $manager->persist($Zone);
        
        $Zone = new Zone();
        $Zone->setName('Mais Ofertas');
        $Zone->setDescription('Zona das ofertas extras');
        $Zone->setRestricted(false);
        $Zone->setZoneType($zoneTypeOferta);
        $manager->persist($Zone);
        
        $Zone = new Zone();
        $Zone->setName('Outras Categorias');
        $Zone->setDescription('Zona de categorias extras');
        $Zone->setRestricted(false);
        $Zone->setZoneType($zoneTypeCategoria);
        $manager->persist($Zone);
        
        $Zone = new Zone();
        $Zone->setName('Os melhores sites');
        $Zone->setDescription('Zona dos melhores sites');
        $Zone->setRestricted(false);
        $Zone->setZoneType($zoneTypeSite);
        $manager->persist($Zone);
        
        $Zone = new Zone();
        $Zone->setName('Banner');
        $Zone->setDescription('Zona dos banners principais');
        $Zone->setRestricted(false);
        $Zone->setZoneType($zoneTypeBanner);
        $manager->persist($Zone);
        
        $Zone = new Zone();
        $Zone->setName('Indefinida');
        $Zone->setDescription('Zona indefinida no site');
        $Zone->setRestricted(false);
        $Zone->setZoneType($zoneTypeIndefinida);
        $manager->persist($Zone);
        
        $manager->flush();
    }

}