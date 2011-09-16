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
        if($request->getMethod() == 'POST'){
            $result = $request->request->get($form->getName());
            var_dump($result['name']);
            $dm->persist($cont);
            $dm->flush();
            return $this->redirectFlash($this->generateUrl('_home'), 'Mensagem enviada com sucesso.');
        }
        return array(
            'title' => $title,
            'form'  => $form->createView(),
        );
    }

}
