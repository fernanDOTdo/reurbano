<?php
namespace Reurbano\CoreBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controller que cuidará de setar a cidade escolhida na session do usuário.
 */

class CoreController extends BaseController
{
    /**
     * @Route("/", name="_home", requirements={"_scheme" = "http"})
     * @Template()
     */
    public function indexAction()
    {
        return array('Teste' => 'Teste');
    }
}
