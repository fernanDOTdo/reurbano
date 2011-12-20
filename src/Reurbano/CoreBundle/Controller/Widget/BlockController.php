<?php
/**
 *                                              ,d                              
 *                                              88                              
 * 88,dPYba,,adPYba,   ,adPPYYba,  ,adPPYba,  MM88MMM  ,adPPYba,   8b,dPPYba,   
 * 88P'   "88"    "8a  ""     `Y8  I8[    ""    88    a8"     "8a  88P'    "8a  
 * 88      88      88  ,adPPPPP88   `"Y8ba,     88    8b       d8  88       d8  
 * 88      88      88  88,    ,88  aa    ]8I    88,   "8a,   ,a8"  88b,   ,a8"  
 * 88      88      88  `"8bbdP"Y8  `"YbbdP"'    "Y888  `"YbbdP"'   88`YbbdP"'   
 *                                                                 88           
 *                                                                 88           
 * 
 * Reurbano/CoreBundle/Controller/Widget/BlockController.php
 *
 * Controller para os Widgets de Blocos
 *  
 * 
 * @copyright 2011 Mastop Internet Development.
 * @link http://www.mastop.com.br
 * @author Fernando Santos <o@fernan.do>
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */



namespace Reurbano\CoreBundle\Controller\Widget;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mastop\SystemBundle\Controller\BaseController;
use Reurbano\CoreBundle\Document\Banner;

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
            case 'city': // Home de cada cidade
                if ($session->get('reurbano.user.city') != 'oferta-nacional') {
                    $nacionalId = $session->get('reurbano.user.nacional');
                    if ($nacionalId) {
                        $blockSpecialNacional = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($nacionalId, null, true, 'source.totalcoupons');
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
                    $catSpecial = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($session->get('reurbano.user.cityId'), $opts['cat']->getId(), true, 'source.totalcoupons');
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
                        $ret[$c]['title'] = '+ Barato de '.$opts['cat']->getName();
                        $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:block';
                        $ret[$c]['opts'] = array('deal' => $blockCheap);
                        $c++;
                    }
                    if ($session->get('reurbano.user.city') != 'oferta-nacional') {
                        $nacionalId = $session->get('reurbano.user.nacional');
                        if ($nacionalId) {
                            $blockSpecialNacional = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($nacionalId, $opts['cat']->getId(), true, 'source.totalcoupons');
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
                    $catSpecial = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($session->get('reurbano.user.cityId'), $opts['cat']->getId(), true, 'source.totalcoupons', 'desc', $opts['id']);
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
                        $ret[$c]['title'] = '+ Barato de '.$opts['cat']->getName();
                        $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:block';
                        $ret[$c]['opts'] = array('deal' => $blockCheap);
                        $c++;
                    }
                    if ($session->get('reurbano.user.city') != 'oferta-nacional') {
                        $nacionalId = $session->get('reurbano.user.nacional');
                        if ($nacionalId) {
                            $blockSpecialNacional = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($nacionalId, $opts['cat']->getId(), true, 'source.totalcoupons');
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
                $ret[$c]['content'] = "<a title='".$this->trans("Entre em contato se não encontrar a sua oferta em nosso site")."' class='button push_1 orange' href='".$this->generateUrl('deal_sell_contact')."'>Fale com ".$this->get('mastop')->param('system.site.name')."</a>";
                $c++;
                break;
            case 'home':
            default :
                $blockCheap = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($session->get('reurbano.user.cityId'), null, false, 'price', 'asc');
                if ($blockCheap) {
                    $ret[$c]['title'] = '+ Barato de '.$session->get('reurbano.user.cityName');
                    $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:block';
                    $ret[$c]['opts'] = array('deal' => $blockCheap);
                    $c++;
                }
                if ($session->get('reurbano.user.city') != 'oferta-nacional') {
                    $nacionalId = $session->get('reurbano.user.nacional');
                    if ($nacionalId) {
                        $blockSpecialNacional = $this->mongo('ReurbanoDealBundle:Deal')->findOneByCityCat($nacionalId, null, true, 'source.totalcoupons');
                        if ($blockSpecialNacional) {
                            $ret[$c]['title'] = 'Oferta Nacional <span class="brFlag floatR"> &nbsp; </span>';
                            $ret[$c]['widget'] = 'ReurbanoDealBundle:Widget\\Deal:block';
                            $ret[$c]['opts'] = array('deal' => $blockSpecialNacional);
                            $c++;
                        }
                    }
                }
                //$ret[$c]['title'] = 'Bloco de Exemplo para a Home';
                //$ret[$c]['content'] = 'Conteúdo de Exemplo para a Home';
                //$c++;
                break;
        }
        $ret[$c]['title'] = 'Nosso Vídeo';
        $ret[$c]['content'] = '
        <p><a href="http://www.youtube.com/watch?v=ui_Hhm0Nfks" target="_blank"><img title="Nosso Vídeo" src="/themes/reurbano/images/video.jpg" alt="Nosso Vídeo" width="217" height="132" /></a></p>
        ';
        $c++;
        $ret[$c]['title'] = 'Garantia Reurbano';
        $ret[$c]['content'] = '
        <img class="floatR" title="Garantia Reurbano" src="//reurbano.s3.amazonaws.com/imagens/garantia-reurbano-home.png" alt="Garantia Reurbano" width="92" height="97" />&Eacute; uma forma de trazer seguran&ccedil;a ao COMPRADOR, garantindo que ele receber&aacute; um cupom ainda n&atilde;o utilizado, ou ter&aacute; seu dinheiro de volta.
        <p style="text-align: right;"><a href="http://www.reurbano.com.br/pg/garantia-reurbano">Saiba +</a></p>
        ';
        $c++;
        $ret[$c]['title'] = 'Compras Coletivas';
        $ret[$c]['content'] = '
        <p><a href="http://www.peixeurbano.com.br/" target="_blank"><img title="Peixe Urbano" src="/themes/reurbano/images/peixe-urbano.png" alt="Peixe Urbano" width="218" height="53" /></a></p>
<p><a href="http://www.groupon.com.br/" target="_blank"><img title="Groupon" src="/themes/reurbano/images/groupon.png" alt="Groupon" width="218" height="53" /></a></p>
<p><a href="http://br.groupalia.com/" target="_blank"><img title="Groupalia" src="/themes/reurbano/images/groupalia.png" alt="Groupalia" width="218" height="53" /></a></p>
<p><a href="http://www.clubedodesconto.com.br/" target="_blank"><img title="Clube do Desconto" src="/themes/reurbano/images/clube-do-desconto.png" alt="Clube do Desconto" width="218" height="53" /></a></p>
<p><a href="http://www.clickon.com.br/" target="_blank"><img title="ClickOn" src="/themes/reurbano/images/clickon.png" alt="ClickOn" width="218" height="53" /></a></p>    
        ';
        $c++;
        //$ret[$c]['content'] = '<a id="twitterBlock" href="http://twitter.com/ReurbanoBrasil" target="_blank" title="Siga o Reurbano no Twitter!">Siga o Reurbano no Twitter!</a>';
        //$c++;
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
    /**
     * Lista todas os Banners
     * 
     * @Template()
     */
    public function bannerAction()
    {
        $session = $this->get('session');
        $banner = $this->mongo('ReurbanoCoreBundle:Banner')->findByCity($this->mastop()->param('core.banner.loadnum'), $session->get('reurbano.user.cityId'));
        if($banner && count($banner) > 0){
            return array(
            'banner' => $banner,
            );
        }
        return array();
    }

}