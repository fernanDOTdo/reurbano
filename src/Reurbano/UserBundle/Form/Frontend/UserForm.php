<?php

namespace Reurbano\UserBundle\Form\Frontend;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

class UserForm extends AbstractType {

    protected $name;
    protected $email;
    protected $password;
    protected $password2;
    protected $username;

    public function buildForm(FormBuilder $builder, array $options) {
        $builder
                ->add('name', 'text', array('max_length' => 100, 'label' => 'user.user.novo.frontend.name'))
                ->add('email', 'email', array('label' => 'user.user.novo.frontend.email'))
                ->add('cpf', 'text', array('label' => 'user.user.novo.frontend.cpf'))
                ->add('password', 'repeated', array('type' => 'password', 'first_name' => 'Password', 'second_name' => 'Password2'))
                ->add('agree', 'checkbox', array('label' => 'user.user.novo.frontend.agree', 'required'  => true,'property_path' => false))
                ->add('newsletters', 'checkbox', array('label' => 'user.user.novo.frontend.newsletter', 'required'  => false))
                ->add('emailVerify', 'hidden', array('property_path' => false))

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