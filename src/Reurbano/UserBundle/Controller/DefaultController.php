<?php

namespace Reurbano\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ReurbanoUserBundle:Default:index.html.twig');
    }
}
