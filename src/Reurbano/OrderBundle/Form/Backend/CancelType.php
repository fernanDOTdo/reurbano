<?php

namespace Reurbano\OrderBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CancelType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('msg', 'textarea', array(
                    'label' => 'Observação',
                    ))
                ->add('returnDeal', 'checkbox', array(
                    'label' => 'Voltar oferta para o site?',
                    'required' =>false,
                ))
                ->add('notifyBuyer', 'checkbox', array(
                    'label' => 'Notificar o comprador?',
                    'required' =>false,
                ))
                ->add('notifySeller', 'checkbox', array(
                    'label' => 'Notificar o vendedor?',
                    'required' =>false,
                ))
                ->add('control', 'hidden', array(
                    'value' => true,
                ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'intention' => 'cancel_creation',
        );
    }

    public function getName() {
        return 'cancel';
    }
}
