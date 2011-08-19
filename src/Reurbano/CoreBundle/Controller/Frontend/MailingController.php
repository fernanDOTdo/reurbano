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
        $regex = "/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD";
        $result = preg_match($regex, $request->request->get('email'));
        if(!$result){
            $this->get('session')->setFlash('error', $this->trans('E-mail invalido'));
            return $this->redirect($this->generateUrl('_home'));
        }
        $mailing->setMail($request->request->get('email'));
        $mailing->setCity($request->request->get('cityNL'));
        $dm->persist($mailing);
        $dm->flush();
        $this->setCookie('hideNL', 1);
        return $this->redirect($this->generateUrl('_home'));
    }
}
