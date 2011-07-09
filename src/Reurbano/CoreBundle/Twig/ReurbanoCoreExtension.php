<?php

namespace Reurbano\CoreBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;
use Twig_Function_Method;
use Twig_Filter_Method;

/**
 * Classe para extensão do Twig.
 * Adicionar nesta classe todas as funções necessárias para o Twig no projeto.
 */

class ReurbanoCoreExtension extends Twig_Extension
{
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container Uma instância de Container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Retorna uma lista com todas as funções globais para adicionar às existentes.
     *
     * @return array Um array de funções globais
     */
    public function getFunctions()
    {
        $mappings = array(
            'reurbano_hello'           => 'hello',
        );

        $functions = array();
        foreach($mappings as $twigFunction => $method) {
            $functions[$twigFunction] = new Twig_Function_Method($this, $method, array('is_safe' => array('html')));
        }

        return $functions;
    }
    /**
     * Exemplo de função para extensão do Twig.
     *
     * @param string $world
     * @return string
     */

    public function hello($world)
    {
        return "Oi $world";
    }

    /**
     * Retorna o nome canônico deste helper.
     *
     * @return string O nome canônico
     */
    public function getName()
    {
        return 'reurbano';
    }

}
