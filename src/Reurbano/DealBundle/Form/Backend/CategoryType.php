<?php

namespace Reurbano\DealBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CategoryType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text', array('label'=>'Nome'));
        $builder->add('order', 'text',  array('label'=>'Ordem'));
    }

    public function getDefaultOptions() {
        return array(
            'data_class' => 'Reurbano\DealBundle\Document\Category',
            'intention' => 'category_creation',
        );
    }

    public function getName() {
        return 'category';
    }

}
