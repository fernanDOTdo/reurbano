<?php

namespace Reurbano\OrderBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CommentType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('comment', 'textarea', array(
                    'label' => 'Comentário',
                    'attr'  => array(
                        'style' => 'width: 100%;',
                    )));
    }

    public function getDefaultOptions() {
        return array(
            'data_class' => 'Reurbano\OrderBundle\Document\Comment',
            'intention' => 'comment_creation',
        );
    }

    public function getName() {
        return 'comment';
    }
}
