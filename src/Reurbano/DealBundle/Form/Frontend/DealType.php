<?php

namespace Reurbano\DealBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DealType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('id', 'hidden')
                ->add('price', 'money', array('label'=>'Valor desejado', 'divisor' => 100, 'currency' => 'BRL'))
                ->add('quantity', 'integer', array('label'=>'Quantidade disponível'))
                ->add('voucher0', 'file', array ('label' => "Voucher" ,'required' => false, 'property_path' => false))
                ->add('voucher1', 'file', array ('label' => "Voucher" , 'required' => false, 'property_path' => false))
                ->add('voucher2', 'file', array ('label' => "Voucher" ,'required' => false, 'property_path' => false))
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
