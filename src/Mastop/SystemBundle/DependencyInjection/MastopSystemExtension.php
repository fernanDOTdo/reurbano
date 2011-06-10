<?php

namespace Mastop\SystemBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Finder\Finder;

class MastopSystemExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        
        $config = $processor->process($configuration->getConfigTree(), $configs);

        if (empty($config['themes_dir']) || empty($config['active_theme'])) {
            throw new \RuntimeException('Mastop\SystemBundle está bichado.');
        }
        $finder = new Finder();
        $finder->directories()
          ->depth('== 0')
          ->in($config['themes_dir']);
        $themes = array();
        foreach($finder as $f){
            $themes[] = $f->getFilename();
        }
        $container->setParameter('mastop.themes.list', $themes);
        $container->setParameter('mastop.themes.active', $config['active_theme']);
        $container->setParameter('mastop.themes.themes_dir', $config['themes_dir']);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('templating.xml');
    }
}