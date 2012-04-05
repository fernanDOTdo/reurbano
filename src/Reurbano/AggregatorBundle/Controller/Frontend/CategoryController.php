<?php

namespace Reurbano\AggregatorBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\DealBundle\Document\Category;
use Reurbano\CoreBundle\Document\City;

class CategoryController extends BaseController {

    /**
     * @Route("/agregador/ofertas-em-{city}/{slug}", name="aggregator_deal_category_index", requirements={"_scheme" = "http"})
     * @Template()
     */
    public function indexAction($city, Category $cat)
    {
        if($city != $this->get('session')->get('reurbano.user.city')){ // A cidade acessada não é a cidade do usuário
            $cidade = $this->mongo('ReurbanoCoreBundle:City')->findBySlug($city);
            $this->get('session')->set('reurbano.user.city', $cidade->getSlug());
            $this->get('session')->set('reurbano.user.cityName', $cidade->getName());
            $this->get('session')->set('reurbano.user.cityId', $cidade->getId());
        }
        $breadcrumbs[] = array('title'=>$this->get('session')->get('reurbano.user.cityName'), 'url' => $this->generateUrl('core_city_index', array('slug' => $this->get('session')->get('reurbano.user.city'))));
        $breadcrumbs[] = array('title'=>$cat->getName());
        return array('cat' => $cat, 'slug' => $cat->getSlug(), 'breadcrumbs' => $breadcrumbs);
    }
}