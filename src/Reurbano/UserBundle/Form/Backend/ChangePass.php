<?php

namespace Reurbano\UserBundle\Form\Backend;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

class ChangePass extends AbstractType {


    public function buildForm(FormBuilder $builder, array $options) {
        $builder
                ->add('id', 'hidden')
                ->add('password', 'password', array( 'label' => 'Senha'))
                ->add('password2', 'password', array('property_path' => false, 'label' => 'Repita a senha'))
        ;
    }

    public function getDefaultOptions() {
        return array(
            'data_class' => 'Reurbano\UserBundle\Document\User',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention' => 'novo_usuario',
        );
    }

    public function getName() {
        return 'Userform';
    }

}