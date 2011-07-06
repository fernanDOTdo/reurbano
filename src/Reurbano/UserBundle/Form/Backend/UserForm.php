<?php

namespace Reurbano\UserBundle\Form\Backend;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

class UserForm extends AbstractType {

    protected $name;
    protected $email;
    protected $password;
    protected $password2;
    protected $username;
    protected $group;
    protected $avatar;
    protected $status;

    public function buildForm(FormBuilder $builder, array $options) {
        $builder
                ->add('name', 'text', array('max_length' => 100, 'label' => 'user.user.editarNovo.name'))
                ->add('email', 'email', array('label' => 'user.user.editarNovo.email'))
                ->add('username', 'text', array('label' => 'user.user.editarNovo.username'))
                ->add('password', 'repeated', array('type' => 'password', 'first_name' => 'password', 'second_name' => 'password2'))
                ->add('group', 'choice', array('choices' => array('ROLE_USER' => 'Usuário', 'ROLE_ADMIN' => 'Administrador'), 'required' => true, 'label' => 'user.user.editarNovo.role',))
                ->add('status', 'choice', array(
                    'label' => '_ATIVO',
                    'expanded' => true,
                    'multiple' => false,
                    'choices' => array('1' => '_SIM', '0' => '_NAO')))

        ;
    }

    public function getDefaultOptions(array $options) {
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