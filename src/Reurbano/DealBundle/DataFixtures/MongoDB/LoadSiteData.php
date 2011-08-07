<?php

namespace Reurbano\DealBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Reurbano\DealBundle\Document\Site;

class LoadSiteData implements FixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load($manager) {
        
        $site = new Site();
        $site->setName('Groupon');
        $site->setUrl('www.groupon.com.br');
        $site->setFilename('20863374714e3efbd1e67e52.95655353.png');
        $site->setFilesize('11155');
        $site->setPath($this->container->get('kernel')->getRootDir() . "/../web/uploads/reurbanodeal");
        $manager->persist($site);
        $manager->flush();
        
        $site = new Site();
        $site->setName('Groupalia');
        $site->setUrl('www.groupalia.com.br');
        $site->setFilename('15417285174e3efbe17e6282.90236660.jpg');
        $site->setFilesize('4067');
        $site->setPath($this->container->get('kernel')->getRootDir() . "/../web/uploads/reurbanodeal");
        $manager->persist($site);
        $manager->flush();
        
        $site = new Site();
        $site->setName('Eoff');
        $site->setUrl('www.eoff.com.br');
        $site->setFilename('20106912844e3efbf277cf21.48011567.gif');
        $site->setFilesize('10456');
        $site->setPath($this->container->get('kernel')->getRootDir() . "/../web/uploads/reurbanodeal");
        $manager->persist($site);
        $manager->flush();
        
        $site = new Site();
        $site->setName('Peixe Urbano');
        $site->setUrl('www.peixeurbano.com.br');
        $site->setFilename('18002803524e3efc05cd2855.31639269.png');
        $site->setFilesize('5291');
        $site->setPath($this->container->get('kernel')->getRootDir() . "/../web/uploads/reurbanodeal");
        $manager->persist($site);
        $manager->flush();
    }
}