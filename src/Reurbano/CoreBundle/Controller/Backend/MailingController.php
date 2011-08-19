<?php
namespace Reurbano\CoreBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\CoreBundle\Document\Mailing;

/**
 * Controller backend dos e-mail cadastrados
 */

class MailingController extends BaseController
{
    /**
     * @Route("/", name="admin_core_mailing_index")
     * @Template()
     */
    public function indexAction()
    {
        $title = "Administração dos e-mails";
        $mailing = $this->mongo('ReurbanoCoreBundle:Mailing')->findAllByOrder();
        return array(
            'title'   => $title,
            'mailing' => $mailing,
            'current' => 'admin_core_mailing_index'
        );
    }
    
    /**
     * @Route("/export", name="admin_core_mailing_e")
     */
    public function exportAction()
    {
        return array();
    }
}
