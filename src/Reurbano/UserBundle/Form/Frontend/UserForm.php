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
                ->add('name', 'text', array('max_length' => 100, 'label' => 'Nome Completo'))
                ->add('email', 'email', array('label' => 'Email'))
                //->add('cpf', 'text', array('label' => 'CPF', 'attr' => array('class' => 'cpfMask')))
                ->add('password', 'password', array('label' => 'Senha'))
                ->add('password2', 'password', array('property_path' => false, 'label' => 'Repita a senha'))
                ->add('gender', 'choice', array(
                    'choices' => array(
                        'm' => 'Masculino',
                        'f' => 'Feminino'
                    ),
                    'label' => 'Sexo',
                    'required' => false,
                ))
                ->add('birth', 'birthday', array('label'=>'Data de nascimento', 'format' => 'd/M/y','required' => false))
                ->add('agree', 'checkbox', array('label' => 'Eu li e aceito os Termos e Condições de uso do site ', 'required' => true, 'property_path' => false))
                ->add('newsletters', 'checkbox', array('label' => 'Sim, quero receber notícias e promoções do Reurbano ', 'required' => false))
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