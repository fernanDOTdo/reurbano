<?php

namespace Reurbano\UserBundle\Form\Backend;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

class UserForm extends AbstractType {


    public function buildForm(FormBuilder $builder, array $options) {
        $builder
                ->add('id', 'hidden')
                ->add('name', 'text', array('max_length' => 100, 'label' => 'Nome', 'required' => false))
                ->add('email', 'email', array('label' => 'Email'))
                ->add('cpf', 'text', array('label' => 'CPF', 'required' => false))
                ->add('password', 'password', array( 'label' => 'Senha'))
                ->add('password2', 'password', array('property_path' => false, 'label' => 'Repita a senha'))
                ->add('roles', 'choice', array('choices' => array('ROLE_USER' => 'Usuário', 'ROLE_ADMIN' => 'Administrador'), 'required' => true, 'label' => 'Grupo'))
                ->add('status', 'choice', array(
                    'label' => 'Ativo',
                    'required' => true,
                    'choices' => array('1' => 'Sim', '2' => 'Não')))
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