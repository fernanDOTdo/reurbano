<?php

namespace Reurbano\DealBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DealType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('id', 'hidden')
                ->add('quantity', 'integer', array('label'=>'Quantidade disponível','attr'=> array('class' => 'small')))
                ->add('price', 'money', array('label'=>'Valor desejado', 'currency' => 'BRL'))
                ->add('voucher0', 'file', array ('label' => "Voucher 1" , 'property_path' => false))
            ;
    
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
