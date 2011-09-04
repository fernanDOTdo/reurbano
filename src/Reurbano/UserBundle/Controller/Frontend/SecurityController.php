<?php

namespace Reurbano\UserBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\UserBundle\Form\Frontend\UserForm;

/**
 * @Route("", requirements={"_scheme" = "https"})
 */
class SecurityController extends BaseController {

    /**
     * @Route("/login", name="_login")
     * @Template()
     */
    public function loginAction() {
        // get the login error if there is one
        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        if($error){
            $this->get('session')->setFlash('error', $error->getMessage());
        }
        $factory = $this->get('form.factory');
        $titulo = $this->trans("Cadastre-se");
        $form = $factory->create(new UserForm());
        
        return array(
            // last username entered by the user
            'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
            'form' => $form->createView(), 'titulo' => $titulo
        );
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function securityCheckAction() {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_logout")
     */
    public function logoutAction() {
        // The security layer will intercept this request
    }

}