<?php
namespace Mastop\SystemBundle; // Confira o namespace!

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Mastop\SystemBundle\DependencyInjection\Compiler\ThemeCompilerPass;

class MastopSystemBundle extends Bundle 
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ThemeCompilerPass());
    }
}