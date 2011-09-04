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
     * @Template()
     */
    public function renderAction($area = 'home', $opts = array()) {
        $session = $this->get('session');
        $ret = array();
        $c = 0;
        switch ($area) {
            case 'topL':
                $blockSpecial = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($session->get('reurbano.user.cityId'), null, true);
                $blockSpecialId = null;
                if ($blockSpecial) {
                    $blockSpecialId = $blockSpecial->getId();
                    $ret[$c]['title'] = 'Oferta em Destaque';
                    $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:blockTop';
                    $ret[$c]['opts'] = array('deal' => $blockSpecial);
                    $c++;
                }
                return array('blocks' => $ret, 'area' => $area);
                break;
            case 'topR':
                $blockSpecial = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($session->get('reurbano.user.cityId'), null, true);
                $blockSpecialId = null;
                if ($blockSpecial) {
                    $blockSpecialId = $blockSpecial->getId();
                }
                $blockCheap = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($session->get('reurbano.user.cityId'), null, false, 'price', 'asc', $blockSpecialId);
                if ($blockCheap) {
                    $ret[$c]['title'] = 'Mais Barato de '.$session->get('reurbano.user.cityName');
                    $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:blockTop';
                    $ret[$c]['opts'] = array('deal' => $blockCheap);
                    $c++;
                }
                return array('blocks' => $ret, 'area' => $area);
                break;
            case 'city': // Home de cada cidade
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
            case 'content': // Página de conteúdo
                break;
            case 'deal': // Página de oferta
                if (isset($opts['cat']) && isset ($opts['id'])) {
                    $catSpecial = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($session->get('reurbano.user.cityId'), $opts['cat']->getId(), true, 'special', 'desc', $opts['id']);
                    $catSpecialId = null;
                    if ($catSpecial) {
                        $catSpecialId = $catSpecial->getId();
                        $ret[$c]['title'] = 'Destaque de ' . $opts['cat']->getName();
                        $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:block';
                        $ret[$c]['opts'] = array('deal' => $catSpecial);
                        $c++;
                    }
                    $blockCheap = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($session->get('reurbano.user.cityId'), $opts['cat']->getId(), false, 'price', 'asc', $opts['id']);
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
            case 'sell': // Página de venda
                $ret[$c]['title'] = 'Não encontrou sua oferta?';
                $ret[$c]['content'] = "<a title='".$this->trans("Entre em contato se não encontrar a sua oferta em nosso site")."' class='button push_1 orange' href='".$this->generateUrl('core_html_contact')."'>Fale com ".$this->get('mastop')->param('system.site.name')."</a>";
                $c++;
                break;
            case 'home':
            default :
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
        $ret[$c]['widget'] = 'ReurbanoCoreBundle:Widget\\Block:facebookFans';
        $ret[$c]['opts'] = array('profile' => '188401247891549', 'url' => 'http://www.facebook.com/recompracoletiva', 'css' => 'http://www.mastop.com.br/fernando/css/facebook.css');
        //$ret[$c]['widget'] = 'ReurbanoCoreBundle:Widget\\Block:facebook';
        //$ret[$c]['opts'] = array('url' => 'http://www.facebook.com/recompracoletiva', 'width' => '292', 'border' => '#FFFFFF');
        return array('blocks' => $ret, 'area' => $area);
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
    /**
     * Widget que renderiza o Bloco do Facebook Fans
     * @param string $profile
     * @param int $width
     * @param int $height
     * @param int $connections
     * @param string $css
     * @param bool $stream
     * @param bool $header
     * @Template()
     */
    public function facebookFansAction($profile, $url, $width = 300, $height = 330, $connections = 36, $css = null, $stream = 'false', $header = 'false') {
        return array('profile' => $profile, 'url' => $url, 'width' => $width, 'height' => $height, 'connections' => $connections, 'css' => $css, 'stream' => $stream, 'header' => $header);
    }

}