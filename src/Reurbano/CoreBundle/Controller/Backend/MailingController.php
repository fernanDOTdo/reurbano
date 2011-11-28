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
        $data = "E-mail,Cidade,Data\n";
        foreach($mailing as $mailing){
            $data .= $mailing->getMail() . 
                    "," . $mailing->getCity() . 
                    "," . date('d/m/Y', $mailing->getCreatedAt()->getTimestamp()) . "\n";
        }
        return new Response($data, 200, array(
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename= mailing_' . date('d_m_Y') . '.txt',
        ));
    }
    
    /**
     * Exibe um pre delete e deleta se for confirmado
     * 
     * @Route("/deletar/{id}", name="admin_core_mailing_delete")
     * @Template()
     */
    public function deleteAction($id)
    {
        $request = $this->get('request');
        $formResult = $request->request;
        $dm = $this->dm();
        $mail = $this->mongo('ReurbanoCoreBundle:Mailing')->find($id);
        if($request->getMethod() == 'POST'){
            if (!$mail) throw $this->createNotFoundException($this->trans('Nenhum e-mail encontrado com o ID %id%',array('%id%'=>$id)));
            $dm->remove($mail);
            $dm->flush();
            return $this->redirectFlash($this->generateUrl('admin_core_mailing_index'), $this->trans('E-mail Deletado'));
        }
        return $this->confirm($this->trans('Tem certeza que deseja deletar o e-mail %name%?', array("%name%" => $mail->getMail())), array('id' => $mail->getId()));

    }
}
