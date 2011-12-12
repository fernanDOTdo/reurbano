<?php

namespace Reurbano\UserBundle\Form\Frontend;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\AbstractType;

class UserFormEdit extends AbstractType {

    protected $name;
    protected $email;
    protected $username;

    public function buildForm(FormBuilder $builder, array $options) {
        $builder
                ->add('id', 'hidden')
                ->add('name', 'text', array('max_length' => 100, 'label' => 'Nome Completo'))
                ->add('email', 'email', array('label' => 'Email', 'attr' => array('disabled' => 'disabled')))
                ->add('cpf', 'text', array('label' => 'CPF', 'required' => false))
                ->add('gender', 'choice', array(
                    'choices' => array(
                        'm' => 'Masculino',
                        'f' => 'Feminino'
                    ),
                    'label' => 'Gênero'
                ))
                ->add('birth', 'birthday', array('label'=>'Data de nascimento', 'format' => 'd/M/y','required' => false))
                ->add('city', 'document', array('label'=>'Cidade', 'class' => 'Reurbano\\CoreBundle\\Document\\City', 'property'=>'name', 'required' => false, 'empty_value' => 'Todas' ))
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
        return 'Userformedit';
    }

}