<?php
namespace Reurbano\CoreBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controller que cadastrará o usuário na mailing list
 */

class MailingController extends BaseController
{
    /**
     * @Route("/new", name="core_mailing_new")
     * @Template()
     */
    public function newAction()
    {
        $request = $this->getRequest();
        exit(print_r($request->request));
    }
}
