<?php

namespace Reurbano\UserBundle\Form\Frontend;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

class ReenviarForm extends AbstractType {

    protected $email;

    public function buildForm(FormBuilder $builder, array $options) {
        $builder
                ->add('email', 'email', array('label' => 'Informe seu email'))

        ;
    }


    public function getName() {
        return 'Reenviarform';
    }

}