<?php
namespace Reurbano\CoreBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controller que cuidará de setar a cidade escolhida na session do usuário.
 */

class CityController extends BaseController
{
    /**
     * @Route("/{name}", name="core_city_index")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }
}
