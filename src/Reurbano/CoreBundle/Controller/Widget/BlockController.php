<?php

namespace Reurbano\CoreBundle\Controller\Widget;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mastop\SystemBundle\Controller\BaseController;

/*
 * Controller para os Widgets de Blocos
 */

class BlockController extends BaseController {

    /**
     * Widget que renderiza o bloco lateral
     * 
     */
    public function renderAction($area = 'home', $opts = array()) {
        $session = $this->get('session');
        $ret = array();
        $c = 0;
        switch ($area) {
            case 'city': // Home de cada cidade
                $blockSpecial = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($session->get('reurbano.user.cityId'), null, true);
                $blockSpecialId = null;
                if ($blockSpecial) {
                    $blockSpecialId = $blockSpecial->getId();
                    $ret[$c]['title'] = 'Oferta em Destaque';
                    $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:block';
                    $ret[$c]['opts'] = array('deal' => $blockSpecial);
                    $c++;
                }
                $blockCheap = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($session->get('reurbano.user.cityId'), null, false, 'price', 'asc');
                if ($blockCheap && $blockCheap->getId() != $blockSpecialId) { // Evita 2 ofertas iguais em blocos diferentes
                    $ret[$c]['title'] = 'Mais Barato de '.$session->get('reurbano.user.cityName');
                    $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:block';
                    $ret[$c]['opts'] = array('deal' => $blockCheap);
                    $c++;
                }
                if ($session->get('reurbano.user.city') != 'oferta-nacional') {
                    $nacionalId = $session->get('reurbano.user.nacional');
                    if ($nacionalId) {
                        $blockSpecialNacional = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($nacionalId, null, true);
                        if ($blockSpecialNacional) {
                            $ret[$c]['title'] = 'Oferta Nacional <span class="brFlag floatR"> &nbsp; </span>';
                            $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:block';
                            $ret[$c]['opts'] = array('deal' => $blockSpecialNacional);
                            $c++;
                        }
                    }
                }
                break;
            case 'category': // Home de cada categoria
                if (isset($opts['cat'])) {
                    $catSpecial = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($session->get('reurbano.user.cityId'), $opts['cat']->getId(), true);
                    $catSpecialId = null;
                    if ($catSpecial) {
                        $catSpecialId = $catSpecial->getId();
                        $ret[$c]['title'] = 'Destaque de ' . $opts['cat']->getName();
                        $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:block';
                        $ret[$c]['opts'] = array('deal' => $catSpecial);
                        $c++;
                    }
                    $blockCheap = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($session->get('reurbano.user.cityId'), $opts['cat']->getId(), false, 'price', 'asc');
                    if ($blockCheap && $blockCheap->getId() != $catSpecialId) { // Evita 2 ofertas iguais em blocos diferentes
                        $ret[$c]['title'] = 'Mais Barato de '.$opts['cat']->getName();
                        $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:block';
                        $ret[$c]['opts'] = array('deal' => $blockCheap);
                        $c++;
                    }
                    if ($session->get('reurbano.user.city') != 'oferta-nacional') {
                        $nacionalId = $session->get('reurbano.user.nacional');
                        if ($nacionalId) {
                            $blockSpecialNacional = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($nacionalId, $opts['cat']->getId(), true);
                            if ($blockSpecialNacional) {
                                $ret[$c]['title'] = $opts['cat']->getName().' em Oferta Nacional <span class="brFlag floatR"> &nbsp; </span>';
                                $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:block';
                                $ret[$c]['opts'] = array('deal' => $blockSpecialNacional);
                                $c++;
                            }
                        }
                    }
                    
                }
                break;
            case 'search': // Página de busca
                break;
            case 'deal': // Página de oferta
                break;
            case 'sell': // Página de venda
                break;
            case 'home':
            default :
                $blockSpecial = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($session->get('reurbano.user.cityId'), null, true);
                $blockSpecialId = null;
                if ($blockSpecial) {
                    $blockSpecialId = $blockSpecial->getId();
                    $ret[$c]['title'] = 'Oferta em Destaque';
                    $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:block';
                    $ret[$c]['opts'] = array('deal' => $blockSpecial);
                    $c++;
                }
                $blockCheap = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($session->get('reurbano.user.cityId'), null, false, 'price', 'asc');
                if ($blockCheap && $blockCheap->getId() != $blockSpecialId) { // Evita 2 ofertas iguais em blocos diferentes
                    $ret[$c]['title'] = 'Mais Barato de '.$session->get('reurbano.user.cityName');
                    $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:block';
                    $ret[$c]['opts'] = array('deal' => $blockCheap);
                    $c++;
                }
                if ($session->get('reurbano.user.city') != 'oferta-nacional') {
                    $nacionalId = $session->get('reurbano.user.nacional');
                    if ($nacionalId) {
                        $blockSpecialNacional = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($nacionalId, null, true);
                        if ($blockSpecialNacional) {
                            $ret[$c]['title'] = 'Oferta Nacional <span class="brFlag floatR"> &nbsp; </span>';
                            $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:block';
                            $ret[$c]['opts'] = array('deal' => $blockSpecialNacional);
                            $c++;
                        }
                    }
                }
                $ret[$c]['title'] = 'Bloco de Exemplo para a Home';
                $ret[$c]['content'] = 'Conteúdo de Exemplo para a Home';
                $c++;
                break;
        }
        $ret[$c]['widget'] = 'ReurbanoCoreBundle:Widget\\Block:facebook';
        $ret[$c]['opts'] = array('url' => 'http://www.facebook.com/pages/Reurbano/188401247891549', 'width' => '292', 'border' => '#FFFFFF');
        return $this->render(
                        'ReurbanoCoreBundle:Widget/Block:render.html.twig', array(
                    'blocks' => $ret
                        )
        );
    }

    /**
     * Widget que renderiza o Bloco do Facebook
     * @param string $url
     * @param string $width
     * @Template()
     */
    public function facebookAction($url, $width, $border = '#000000', $faces = 'true', $stream = 'false', $header = 'false') {
        return array('url' => $url, 'width' => $width, 'border' => $border, 'faces' => $faces, 'stream' => $stream, 'header' => $header);
    }

}