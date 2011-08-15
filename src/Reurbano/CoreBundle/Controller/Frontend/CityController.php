<?php
namespace Reurbano\CoreBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\CoreBundle\Document\City;

/**
 * Controller que cuidará de setar a cidade escolhida na session do usuário.
 */

class CityController extends BaseController
{
    /**
     * @Route("/ofertas-em-{slug}", name="core_city_index")
     * @Template()
     */
    public function indexAction(City $city)
    {
        $this->get('session')->set('reurbano.user.city', $city->getSlug());
        $this->get('session')->set('reurbano.user.cityName', $city->getName());
        $this->get('session')->set('reurbano.user.cityId', $city->getId());
        return array();
    }
}
