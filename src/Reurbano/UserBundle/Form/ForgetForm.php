<?php

namespace Reurbano\UserBundle\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

class ForgetForm extends AbstractType {

    protected $email;

    public function buildForm(FormBuilder $builder, array $options) {
        $builder
                ->add('email', 'email', array('label' => 'user.user.editarNovo.email'))
        ;
    }

    public function getName() {
        return 'Forgetform';
    }

}