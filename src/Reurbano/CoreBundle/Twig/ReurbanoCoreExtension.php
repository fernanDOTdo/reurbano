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
class ReurbanoCoreExtension extends Twig_Extension {

    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container Uma instância de Container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * Retorna uma lista com todas as funções globais para adicionar às existentes.
     *
     * @return array Um array de funções globais
     */
    public function getFunctions() {
        $mappings = array(
            'reurbano_select_city' => 'selectCity',
            'reurbano_get_cities' => 'getCities',
        );

        $functions = array();
        foreach ($mappings as $twigFunction => $method) {
            $functions[$twigFunction] = new Twig_Function_Method($this, $method, array('is_safe' => array('html')));
        }

        return $functions;
    }

    public function selectCity($name = 'city', $id = null, $class = null) {
        $repo = $this->container->get('mastop')->getDocumentManager()->getRepository('ReurbanoCoreBundle:City');
        $cities = $repo->findAll();
        $ret = null;
        if ($cities) {
            $current = $this->container->get('session')->get('reurbano.user.city');
            $ret = '<select name="' . $name . '"';
            $ret .= ($id) ? ' id="' . $id . '"' : '';
            $ret .= ($class) ? ' class="' . $class . '"' : '';
            $ret .= '>';
            foreach ($cities as $k => $v) {
                $ret .= '<option value="' . $v->getSlug() . '"' . ($current == $v->getSlug() ? ' selected="selected"' : '') . '>' . $v->getName() . '</option>';
            }
            $ret .= '</select>';
        }
        return $ret;
    }

    public function getCities() {
        $repo = $this->container->get('mastop')->getDocumentManager()->getRepository('ReurbanoCoreBundle:City');
        $cities = $repo->findAll();
        $ret = array('special' => array(), 'normal' => array());
        if ($cities) {
            foreach ($cities as $k => $v) {
                if ($v->getSpecial()) {
                    $ret['special'][] = $v;
                } else {
                    $ret['other'][] = $v;
                }
            }
        }
        return $ret;
    }

    /**
     * Retorna o nome canônico deste helper.
     *
     * @return string O nome canônico
     */
    public function getName() {
        return 'reurbano';
    }

}
