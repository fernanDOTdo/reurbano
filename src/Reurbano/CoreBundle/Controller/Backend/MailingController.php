<?php
namespace Reurbano\CoreBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\CoreBundle\Document\Mailing;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/export", name="admin_core_mailing_export")
     */
    public function exportAction()
    {
        $mailing = $this->mongo('ReurbanoCoreBundle:Mailing')->findAllByOrder();
        $data = "E-mail,Cidade,Data<br />";
        foreach($mailing as $mailing){
            $data .= $mailing->getMail() . 
                    "," . $mailing->getCity() . 
                    "," . date('d/m/Y', $mailing->getCreatedAt()->getTimestamp()) . "<br />";
        }
        return new Response($data, 200, array(
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename= mailing_' . date('d_m_Y') . '.txt',
        ));
    }
}
