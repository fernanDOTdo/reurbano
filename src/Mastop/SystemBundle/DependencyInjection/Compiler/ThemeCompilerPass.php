<?php

namespace Mastop\SystemBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class ThemeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Replace templating.
        /*$container->getDefinition('templating.locator.uncached')
            ->replaceArgument(0, new Reference('mastop.themes.file_locator'))
        ;*/

        $container->getDefinition('templating.locator')
            ->replaceArgument(0, new Reference('mastop.themes.file_locator'))
        ;
    }
}