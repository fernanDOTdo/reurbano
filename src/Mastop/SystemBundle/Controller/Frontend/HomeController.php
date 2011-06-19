<?php

namespace Mastop\SystemBundle\Controller\Frontend; // Confira o namespace!

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class HomeController extends Controller {

    public function indexAction() {
        return $this->render('MastopSystemBundle:Frontend\Home:index.html.twig');
    }

}