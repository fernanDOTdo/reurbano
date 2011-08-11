<?php
namespace Reurbano\UserBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controller que será o DashBoard do usúario.
 */

class DashboardController extends BaseController
{
    /**
     * Index do Dasboard
     * 
     * @Route("/", name="user_dashboard_index")
     * @Template()
     */
    public function indexAction(){
        $title = 'titulo';
        return array();
    }
}