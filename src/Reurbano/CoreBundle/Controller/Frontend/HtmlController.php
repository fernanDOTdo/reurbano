<?php

namespace Reurbano\CoreBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controller que cadastrará o usuário na mailing list
 */
class HtmlController extends BaseController {

    /**
     * Action do Formulário de contato
     * @Route("/fale-conosco", name="core_html_contact")
     * @Template()
     */
    public function contactAction() {
        $request = $this->getRequest();
        return array(
            'title' => $this->trans('Fale com o %sitename%',array("%sitename%"=>$this->get('mastop')->param('system.site.name'))),
        );
    }

}
