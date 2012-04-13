<?php

namespace Reurbano\OrderBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CancelType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('obs', 'textarea', array(
                    'label' => 'Observação',
                    'required' => false,
                    ));
    }

    public function getDefaultOptions() {
        return array(
            'intention' => 'cancel_creation',
        );
    }

    public function getName() {
        return 'cancel';
    }
}
