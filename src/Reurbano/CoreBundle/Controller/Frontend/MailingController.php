<?php
namespace Reurbano\CoreBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\CoreBundle\Document\Mailing;

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
        $dm = $this->dm();
        $city = $request->request->get('cityNL');
        $mailing = new Mailing();
        $mailing->setMail($request->request->get('email'));
        $mailing->setCity($request->request->get('cityNL'));
        $dm->persist($mailing);
        $dm->flush();
        $this->setCookie('hideNL', 1);
        return $this->redirect($this->generateUrl('_home'));
    }
}
