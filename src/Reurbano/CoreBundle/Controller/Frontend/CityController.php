<?php
namespace Reurbano\CoreBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CityController extends Controller
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
