<?php

namespace Reurbano\UserBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Reurbano\UserBundle\Document\User;

class LoadUserData implements FixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load($manager) {
        $citySP = $manager->getRepository('ReurbanoCoreBundle:City')->findBySlug('sao-paulo');
        $user = new User();
        $user->setName('Suporte Mastop');
        $user->setEmail('suporte@mastop.com.br');
        $user->setLang('pt_BR');
        $user->setCity($citySP);
        $user->setCreated(new \DateTime());
        $user->setTheme('');
        $user->setStatus(1);
        $user->setRoles('ROLE_ADMIN');
        $user->setMailOk(true);
        $user->setUsername('suportemastopcombr');
        $user->setPassword("QRcrz4q1+CMeIOSJe9qybVEL5agAMeWRc1ZpPj/wDlH8lbgaJetnRvz79I0WuPwjIcuVsU/cuF733/Ts1KHd1A==");
        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $user->setName('Mastop');
        $user->setEmail('mastop@mastop.com.br');
        $user->setLang('pt_BR');
        $user->setCity($citySP);
        $user->setCreated(new \DateTime());
        $user->setTheme('');
        $user->setStatus(1);
        $user->setRoles('ROLE_SUPERADMIN');
        $user->setMailOk(true);
        $user->setUsername('mastopmastopcombr');
        $user->setPassword("5wqjnkXHoGQ2ni1eOT8f83+uGjykKiVr35hfM90oSMX779xWoRxJQL6EYd8Mx4lV/bedVbWbQVhMBtMXoQC2JA==");
        $manager->persist($user);
        $manager->flush();
    }

}