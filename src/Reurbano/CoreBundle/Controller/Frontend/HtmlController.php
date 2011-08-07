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
     * @Route("/termos-e-condicoes-de-uso", name="core_html_terms")
     * @Template()
     */
    public function termsAction() {
        $request = $this->getRequest();
        return array(
            'titulo' => $this->trans('Termos e Codições de Uso %sitename%',array("%sitename%"=>$this->get('mastop')->param('system.site.name'))),
        );
    }

}
