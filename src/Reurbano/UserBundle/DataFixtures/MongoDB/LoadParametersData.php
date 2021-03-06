<?php

namespace Reurbano\UserBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mastop\SystemBundle\Document\Parameters;
use Mastop\SystemBundle\Document\Children;
use Reurbano\UserBundle\Document\User;

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
        $param->setRole('ROLE_ADMIN');
        $param->setOrder(1);

        $child = new Children();
        $child->setName('allownew');
        $child->setTitle('Permitir novos cadastros');
        $child->setDesc('Permitir que novos usuários se cadastrem.');
        $child->setValue(1);
        $child->setFieldtype('checkbox');
        $child->setOrder(0);
        $param->addChildren($child);

        $child = new Children();
        $child->setName('mailnotify');
        $child->setTitle('Notificar Novos Cadastros');
        $child->setDesc('Digite endereços de e-mail, separados por vírgula, que serão notificados quando novos usuários se cadastrarem.');
        $child->setValue('meu@email.com');
        $child->setOrder(1);
        $param->addChildren($child);

        $child = new Children();
        $child->setName('autoactive');
        $child->setTitle('Ativação');
        $child->setDesc('Tipo de ativação de novos cadastros');
        $child->setValue('auto');
        $child->setFieldtype('choice');
        $child->setOpts(array('choices' => array('auto' => 'Automático', 'email' => 'E-mail', 'admin' => 'Aprovação Manual')));
        $child->setOrder(2);
        $param->addChildren($child);

        $child = new Children();
        $child->setName('selfdelete');
        $child->setTitle('Deletar Conta');
        $child->setDesc('Permitir que os usuários deletem o próprio cadastro.');
        $child->setValue('0');
        $child->setFieldtype('checkbox');
        $child->setOrder(3);
        $param->addChildren($child);
        
        $child = new Children();
        $child->setName('faceappid');
        $child->setTitle('ID do Aplicativo Facebook');
        $child->setDesc('Código do aplicativo criado no facebook developers');
        $child->setValue('108342115933418');
        $child->setOrder(4);
        $param->addChildren($child);
        
        $child = new Children();
        $child->setName('faceappsecret');
        $child->setTitle('Código Secret do App criado no Facebook');
        $child->setDesc('Código gerado no site de developers do Facebook.');
        $child->setValue('f3872f946c36c127f4effbb4e6a918b3');
        $child->setOrder(5);
        $param->addChildren($child);
        
        $child = new Children();
        $child->setName('twitterappid');
        $child->setTitle('ID Consumer key do Twitter');
        $child->setDesc('Código do aplicativo criado no twitter developers');
        $child->setValue('gfOuKj8MmG8Wc2wGyGyDCg');
        $child->setOrder(6);
        $param->addChildren($child);
        
        $child = new Children();
        $child->setName('twitterappsecret');
        $child->setTitle('Código Consumer secret no Twitter');
        $child->setDesc('Código gerado no site de developers do Twitter.');
        $child->setValue('rJCedVjsmmnED4ZLgHiyUPNNBQ6vtTzkObarciVhs');
        $child->setOrder(7);
        $param->addChildren($child);
        
        $manager->persist($param);
        $manager->flush();
    }

}