<?php

namespace Reurbano\DealBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Reurbano\DealBundle\Document\Category;
use Reurbano\DealBundle\Document\SubCategory;

class LoadSubCategoryData implements FixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load(ObjectManager $manager) {
    	$categoryOutros = $manager->getRepository('ReurbanoDealBundle:Category')->findBySlug('outros');
    	$categoryLazer = $manager->getRepository('ReurbanoDealBundle:Category')->findBySlug('lazer');
    	
    	// Bar e Restaurantes
    	$category = $manager->getRepository('ReurbanoDealBundle:Category')->findBySlug('bares-restaurantes');
        $subcategorias = array('Bares', 'Baladas', 'Pizza', 'Churrasco', 'Massas', 'Japonês', 'Mexicano', 'Feijoada', 'Sanduíches', 'Salgadinhos', 'Doces');
        foreach ($subcategorias as $sub) {
            $SubCategory = new SubCategory();
            $SubCategory->setName($sub);
            $SubCategory->setCategory($category);
            $manager->persist($SubCategory);
        }

        // Cursos e Aulas
        $category = $categoryOutros; //$manager->getRepository('ReurbanoDealBundle:Category')->findBySlug('cursos-aulas');
        $subcategorias = array('Dança de Salão', 'Curso de Idiomas', 'Curso de Informática', 'Cursos em geral');
        foreach ($subcategorias as $sub) {
        	$SubCategory = new SubCategory();
        	$SubCategory->setName($sub);
        	$SubCategory->setCategory($category);
        	$manager->persist($SubCategory);
        }
        
        // Entretenimento
        $category = $categoryOutros; //$manager->getRepository('ReurbanoDealBundle:Category')->findBySlug('entretenimento');
        $subcategorias = array('Paintball', 'Kart', 'Boliche', 'Show', 'Teatro', 'Eventos', 'Passeios', 'Vinhos', 'Museus');
        foreach ($subcategorias as $sub) {
        	$SubCategory = new SubCategory();
        	$SubCategory->setName($sub);
        	$SubCategory->setCategory($category);
        	$manager->persist($SubCategory);
        }
        
        // Esportes
        $category = $categoryLazer; //$manager->getRepository('ReurbanoDealBundle:Category')->findBySlug('esportes');
        $subcategorias = array('Academia', 'Pilates', 'Yoga', 'Personal', 'Corrida', 'Bike', 'Lutas', 'Radicais');
        foreach ($subcategorias as $sub) {
        	$SubCategory = new SubCategory();
        	$SubCategory->setName($sub);
        	$SubCategory->setCategory($category);
        	$manager->persist($SubCategory);
        }
        
        // Saúde e Beleza
        $category = $manager->getRepository('ReurbanoDealBundle:Category')->findBySlug('beleza-saude');
        $subcategorias = array('Dentistas', 'Olhos', 'Maquiagem', 'Corte de cabelo', 'Escova progresiva', 'Hidratação', 'Limpeza de Pele', 'Massagem', 'SPA', 'Pedicure e Manicure', 'Depilação', 'Bronzeamento', 'Facial', 'Dermatológico');
        foreach ($subcategorias as $sub) {
        	$SubCategory = new SubCategory();
        	$SubCategory->setName($sub);
        	$SubCategory->setCategory($category);
        	$manager->persist($SubCategory);
        }
        
        // Serviços Locais
        $category = $manager->getRepository('ReurbanoDealBundle:Category')->findBySlug('servicos');
        $subcategorias = array('Automotivos', 'Book fotográfico', 'Fotos', 'Domésticos', 'Pet Shop', 'Para crianças', 'Casamento');
        foreach ($subcategorias as $sub) {
        	$SubCategory = new SubCategory();
        	$SubCategory->setName($sub);
        	$SubCategory->setCategory($category);
        	$manager->persist($SubCategory);
        }
        
        // Produtos
        $category = $manager->getRepository('ReurbanoDealBundle:Category')->findBySlug('produtos');
        $subcategorias = array('Celular', 'Máquinas fotográficas', 'MP3', 'Acessórios', 'Perfumes', 'GPS', 'Roupas', 'Fotos', 'Mobiliário', 'Utilidades', 'Para crianças', 'Casamento');
        foreach ($subcategorias as $sub) {
        	$SubCategory = new SubCategory();
        	$SubCategory->setName($sub);
        	$SubCategory->setCategory($category);
        	$manager->persist($SubCategory);
        }
        
        // Viagens
        $category = $manager->getRepository('ReurbanoDealBundle:Category')->findBySlug('hoteis-viagens');
        $subcategorias = array('Internacionais', 'Praias', 'Interior', 'Capitais', 'Cruzeiros');
        foreach ($subcategorias as $sub) {
        	$SubCategory = new SubCategory();
        	$SubCategory->setName($sub);
        	$SubCategory->setCategory($category);
        	$manager->persist($SubCategory);
        }
        
        $manager->flush();
    }
}