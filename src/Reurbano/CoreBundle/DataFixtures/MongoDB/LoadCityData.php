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
        $cidadesEspeciais = array('Oferta Nacional', 'São Paulo', 'Rio de Janeiro', 'Campinas', 'Curitiba', 'Belo Horizonte');
        $cidadesNormais = array(
            'Americana',
            'Aracaju',
            'Barueri', 
            'Belém', 
            'Blumenau', 
            'Boa Vista', 
            'Brasília', 
            'Campo Grande', 
            'Contagem', 
            'Criciúma', 
            'Cuiabá', 
            'Feira de Santana', 
            'Florianópolis', 
            'Goiânia', 
            'Guarulhos', 
            'Indaiatuba', 
            'Itajaí', 
            'Itatiba', 
            'Joinville', 
            'Jundiaí', 
            'Londrina', 
            'Maceió', 
            'Manaus', 
            'Natal', 
            'Niterói', 
            'Osasco', 
            'Palmas', 
            'Pelotas', 
            'Porto Alegre', 
            'Porto Seguro',
            'Recife',
            'Salvador',
            'Santos',
            'São Luís',
            'Sorocaba',
            'Teresina',
            'Uberaba',
            'Vitória',
            );
        foreach ($cidadesEspeciais as $cidade) {
            $City = new City();
            $City->setName($cidade);
            $City->setSpecial(1);
            if($cidade == 'Oferta Nacional'){
                $City->setOrder(0);
            }else{
                $City->setOrder(1);
            }
            $manager->persist($City);
        }
        foreach ($cidadesNormais as $cidade) {
            $City = new City();
            $City->setName($cidade);
            $City->setSpecial(0);
            $manager->persist($City);
        }
        $manager->flush();
    }

}