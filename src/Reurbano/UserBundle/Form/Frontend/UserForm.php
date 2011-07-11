<?php

namespace Reurbano\UserBundle\Form\Frontend;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\AbstractType;

class UserForm extends AbstractType {

    protected $name;
    protected $email;
    protected $password;
    protected $password2;
    protected $username;

    public function buildForm(FormBuilder $builder, array $options) {
        $builder
                ->add('id', 'hidden')
                ->add('name', 'text', array('max_length' => 100, 'label' => 'Nome'))
                ->add('email', 'email', array('label' => 'Email'))
                ->add('cpf', 'text', array('label' => 'CPF'))
                ->add('password', 'password', array('label' => 'Senha'))
                ->add('password2', 'password', array('property_path' => false, 'label' => 'Repita a senha'))
                ->add('agree', 'checkbox', array('label' => 'Eu li e aceito os Termos e Condições de uso do Reurbano', 'required' => true, 'property_path' => false))
                ->add('newsletters', 'checkbox', array('label' => 'Sim, quero receber notícias e promoções do Reurbano', 'required' => false))
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