<?php

namespace Reurbano\OrderBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class StatusChangeType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('status', 'document',array(
                         'class' => 'Reurbano\\OrderBundle\\Document\\Status',
                         'property'=>'name',
                ))
                ->add('obs', 'text', array('label'=>'Observação'));
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
