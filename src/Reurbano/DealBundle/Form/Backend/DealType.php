<?php

namespace Reurbano\DealBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DealType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('id', 'hidden');
        $builder->add('title', 'text', array('label'=>'Título'));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Reurbano\DealBundle\Document\Deal',
            'intention' => 'deal_creation',
        );
    }

    public function getName() {
        return 'deal';
    }

}
