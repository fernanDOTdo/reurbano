<?php

namespace Reurbano\CoreBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\CoreBundle\Form\ContactType;
use Reurbano\CoreBundle\Document\Contact;

/**
 * Controller que cadastrará o usuário na mailing list
 */
class HtmlController extends BaseController {

    /**
     * Action do Formulário de contato
     * 
     * @Route("/fale-conosco", name="core_html_contact")
     * @Template()
     */
    public function contactAction() {
        $dm = $this->dm();
        $title = $this->trans('Fale com o %sitename%',array("%sitename%"=>$this->get('mastop')->param('system.site.name')));
        $msg = $this->trans('Formulário enviado com sucesso!');
        $cont = new Contact();
        $form = $this->createForm(new ContactType());
        $request = $this->getRequest();
        $result = $request->request->get($form->getName());
        if($request->getMethod() == 'POST'){
            if($result['email'] || $result['coment']){
                return $this->redirect($this->generateUrl('_home'));
            }
            $cont->setName($result['name']);
            $cont->setPhone($result['phone']);
            $cont->setMail($result['mail']);
            $cont->setMsg($result['msg']);
            $cont->setIp($_SERVER['REMOTE_ADDR']);
            $dm->persist($cont);
            $dm->flush();
            
            $mail = $this->get('mastop.mailer');
            $mail->to($this->get('mastop')->param('system.site.adminmail'))
                    ->replyTo($cont->getMail())
                    ->subject($this->trans('Formulário de contato'))
                    ->template('contato', array('cont' => $cont, 'date' => date('d/m/y G:i:s'), 'title' => $this->trans('Novo Formulário de contato')))
                    ->send();
            return $this->redirectFlash($this->generateUrl('_home'), 'Mensagem enviada com sucesso.');
        }
        return array(
            'title' => $title,
            'form'  => $form->createView(),
        );
    }

}
