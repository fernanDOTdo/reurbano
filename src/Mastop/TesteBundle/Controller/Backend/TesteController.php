<?php

namespace Mastop\TesteBundle\Controller\Backend; // Confira o namespace!

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Mastop\TesteBundle\Entity\Teste;

class TesteController extends Controller {

    /**
     * @Route("/admin/", name="_teste_admin"),
     * @Route("/super/")
     * @Template()
     */
    public function indexAction() {
        $ret = array();
        $ret['data'] = date("d/m/Y H:i:s");
        $form = $this->createFormBuilder(new Teste())
            ->add('name', 'text', array('title'=>'class'))
            ->add('price', 'money', array('currency' => 'BRL'))
            ->getForm();
        
        $ret['form'] = $form->createView();
        return $ret;
    }


}