<?php

namespace Reurbano\DealBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SellType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('site', 'document',array(
                    'class' => 'Reurbano\\DealBundle\\Document\\Site',
                    'property'=>'name',
                    'label' =>"Site da Oferta",
                    'attr'    => array(
                        'class' => 'chzn-select',
                        'data-placeholder' => 'Escolha um site', 
                        'style'=> 'width: 200px;',
                    )
                ))
                ->add('cupom', 'text',array('label'=>"Digite o título ou link da oferta", 'attr' => array('class'=>'big')))
                ->add('cupomId', 'hidden');
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Reurbano\DealBundle\Document\Deal',
            'intention' => 'deal_creation',
        );
    }

    public function getName() {
        return 'sell';
    }

}
