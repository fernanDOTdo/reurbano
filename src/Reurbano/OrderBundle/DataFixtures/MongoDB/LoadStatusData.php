<?php

namespace Reurbano\OrderBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Reurbano\OrderBundle\Document\Status;

class LoadStatusData implements FixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load($manager) {
        $status = array('Criado', 'Pendente', 'Processando', 'Aprovado', 'Com Problemas', 'Aguardando Informações', 'Finalizado');
        foreach ($status as $stat) {
            $Status = new Status();
            $Status->setName($stat);
            $manager->persist($Status);
        }
        $manager->flush();
    }
}