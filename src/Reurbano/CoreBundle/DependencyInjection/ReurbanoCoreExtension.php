<?php

namespace Reurbano\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class ReurbanoCoreExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('twig.xml');
        $loader->load('listener.xml');
        
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->process($configuration->getConfigTree(), $configs);

        if ($config['default_city']) {
            $container->setParameter('reurbano.default_city', $config['default_city']);
        }
        if ($config['quova']['apikey'] && $config['quova']['secret']) {
            $container->setParameter('reurbano.quova.apikey', $config['quova']['apikey']);
            $container->setParameter('reurbano.quova.secret', $config['quova']['secret']);
        }
        
    }
    public function getAlias()
    {
        return 'reurbano_core';
    }
}
