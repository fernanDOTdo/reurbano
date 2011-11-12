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
            'reurbano_get_categories' => 'getCategories',
            'reurbano_menu'           => 'menu',
        );

        $functions = array();
        foreach ($mappings as $twigFunction => $method) {
            $functions[$twigFunction] = new Twig_Function_Method($this, $method, array('is_safe' => array('html')));
        }

        return $functions;
    }

    public function selectCity($name = 'city', $id = null, $class = null, $style = null) {
        $repo = $this->container->get('mastop')->getDocumentManager()->getRepository('ReurbanoCoreBundle:City');
        $cities = $repo->findAll();
        $ret = null;
        if ($cities) {
            $current = $this->container->get('session')->get('reurbano.user.city');
            $ret = '<select name="' . $name . '"';
            $ret .= ($id) ? ' id="' . $id . '"' : '';
            $ret .= ($class) ? ' class="' . $class . '"' : '';
            $ret .= ($style) ? ' style="' . $style. '"' : '';
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

    public function getCategories() {
        $repo = $this->container->get('mastop')->getDocumentManager()->getRepository('ReurbanoDealBundle:Category');
        return $repo->findAllByOrder();
    }
    
    public function menu($menu, $item = null, $current = null, $depth = 0, $template = 'list', $attributes = array())
    {
        list($bundle, $code) = explode('-', $menu);
        $repo = $this->container->get('mastop')->getDocumentManager()->getRepository('MastopMenuBundle:Menu');
        $cache = $this->container->get('mastop')->getCache();
        if (!$item) {
            if ($cache->has($menu)) {
                $document = $cache->get($menu);
            } else {
                $menuMain = $repo->findByBundleCode($bundle, $code);
                if (!$menuMain) {
                    throw new \Exception("Menu " . $menu . " não encontrado.");
                }
                $document = $this->prepareLinks($menuMain);
                $cache->set($menu, $document, 604800); // Uma semana
            }
        } else {
            if ($cache->has($menu . '.' . $item)) {
                $document = $cache->get($menu . '.' . $item);
            } else {
                $menuMain = $repo->findByBundleCode($bundle, $code);
                $menuItem = $repo->getChildrenByCode($menuMain, $item);
                if (!$menuItem) {
                    throw new \Exception("Menu " . $item . " de " . $menu . " não encontrado.");
                }
                $document = $this->prepareLinks($menuItem);
                $cache->set($menu . '.' . $item, $document, 604800); // Uma semana
            }
        }
        return $this->container->get('templating')->render('ReurbanoCoreBundle:Templates:' . $template . '.html.twig', array('menu' => $document, 'current' => $current, 'attrs' => $attributes, 'root' => true, 'depth' => $depth));
    }
    private function prepareLinks($menu) {
        $childs = $menu->getChildren();
        $ret = array();
        if (count($childs) > 0) {
            $childs = $childs->toArray();
            usort($childs, function($a, $b) {
                        return $a->getOrder() > $b->getOrder() ? 1 : -1;
                    });
            foreach ($childs as $child) {
                $ret[$child->getCode()]['name'] = $child->getName();
                $ret[$child->getCode()]['title'] = $child->getTitle();
                $ret[$child->getCode()]['role'] = $child->getRole();
                $ret[$child->getCode()]['url'] = $child->getUrl();
                $ret[$child->getCode()]['newwindow'] = $child->getNewWindow();
                $ret[$child->getCode()]['route'] = $child->getRoute();
                if (count($child->getChildren()) > 0) {
                    $ret[$child->getCode()]['children'] = $this->prepareLinks($child);
                } else {
                    $ret[$child->getCode()]['children'] = null;
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
