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
            // last username entered by the user
            'titulo' => 'teste',
            'content' => 'teste',
        );
    }

}
