<?php

namespace Reurbano\DealBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class StatusType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text', array('label'=>'Nome'));
        $builder->add('order', array('label'=>'Ordem'));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Reurbano\OrderBundle\Document\Status',
            'intention' => 'status_creation',
        );
    }

    public function getName() {
        return 'status';
    }

}
