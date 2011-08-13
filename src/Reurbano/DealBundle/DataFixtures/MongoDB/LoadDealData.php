<?php

namespace Reurbano\DealBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Reurbano\DealBundle\Document\Deal;

class LoadDealData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load($manager) {
        $userMastop = $manager->getRepository('ReurbanoUserBundle:User')->findByEmail('mastop@mastop.com.br');
        $userSuporte = $manager->getRepository('ReurbanoUserBundle:User')->findByEmail('suporte@mastop.com.br');
        $sources = $manager->getRepository('ReurbanoDealBundle:Source')->findAll();
        if($sources){
            foreach ($sources as $source){
                // Mastop
                $deal = new Deal();
                $deal->setUser($userMastop);
                $deal->setSource($source);
                $deal->setPrice(number_format($source->getPriceOffer() / 2, 2)); // Metade do Preço
                $deal->setQuantity(2);
                $deal->setActive(true);
                $deal->setLabel($source->getTitle());
                $deal->setSpecial(true);
                $deal->setCreatedAt(new \DateTime);
                $manager->persist($deal);
                $manager->flush();
                // Suporte
                $deal = new Deal();
                $deal->setUser($userSuporte);
                $deal->setSource($source);
                $deal->setPrice(number_format($source->getPriceOffer() / 3, 2)); // Um terço do Preço
                $deal->setQuantity(1);
                $deal->setActive(true);
                $deal->setLabel($source->getTitle());
                $deal->setSpecial(false);
                $deal->setCreatedAt(new \DateTime);
                $manager->persist($deal);
                $manager->flush();
            }
        }
    }
    public function getOrder()
    {
        return 2;
    }
}