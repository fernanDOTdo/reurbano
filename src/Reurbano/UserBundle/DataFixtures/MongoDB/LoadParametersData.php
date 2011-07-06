<?php

namespace Reurbano\UserBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mastop\SystemBundle\Document\Parameters;
use Mastop\SystemBundle\Document\Children;

class LoadParametersData implements FixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load($manager) {
        $param = new Parameters();
        $param->setName('all');
        $param->setTitle('Usuários');
        $param->setDesc('Configurações para usuários do site');
        $param->setBundle('user');
        $param->setOrder(1);
        
        $child = new Children();
        $child->setName('mailnotify');
        $child->setTitle('Notificar Novos Cadastros');
        $child->setDesc('Digite endereços de e-mail que serão notificados quando novos usuários se cadastrarem.');
        $child->setValue('meu@email.com');
        $param->addChildren($child);
        $child = new Children();
        $child->setName('autoactive');
        $child->setTitle('Ativação');
        $child->setDesc('Tipo de ativação de novos cadastros');
        $child->setValue('email');
        $child->setFieldtype('choice');
        $child->setOpts(array('choices' => array('auto'   => 'Automático', 'email' => 'E-mail', 'admin'   => 'Aprovação Manual')));
        $param->addChildren($child);
        $child = new Children();
        $child->setName('selfdelete');
        $child->setTitle('Deletar Conta');
        $child->setDesc('Permitir que os usuários deletem o próprio cadastro.');
        $child->setValue('0');
        $child->setFieldtype('checkbox');
        $param->addChildren($child);
        $manager->persist($param);
        $manager->flush();
    }
}