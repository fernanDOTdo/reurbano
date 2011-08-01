<?php

namespace Reurbano\UserBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SecurityController extends Controller {

    /**
     * @Route("/", name="_login")
     * @Template()
     */
    public function loginAction() {
        /* echo "<pre>";
          print_r($_SESSION);
          echo "<pre>";exit(); */
        // get the login error if there is one
        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        if($this->get('session')->has('_security.target_path')){
            $redir=$this->get('session')->get('_security.target_path');
        }else{
            $redir=$this->generateUrl("_home");
        }
        return array(
            // last username entered by the user
            'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
            'error' => $error,
            'redir' => $redir,
        );
    }

}