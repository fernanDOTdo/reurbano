<?php

namespace Reurbano\DealBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\DealBundle\Document\Category;
use Reurbano\CoreBundle\Document\City;

class CategoryController extends BaseController {

    /**
     * @Route("/ofertas-em-{city}/{slug}", name="deal_category_index")
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
        return array('cat' => $cat, 'slug' => $cat->getSlug());
    }
}