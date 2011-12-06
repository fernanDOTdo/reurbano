<?php

namespace Reurbano\DealBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ContactType extends AbstractType {

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
                ->add('url', 'text', array('label'=>'Link para a oferta'))
                ->add('quantity', 'integer', array('label'=>'Quantidade de cupons','attr'=> array('class' => 'small')))
                ->add('price', 'money', array('label'=>'Valor desejado', 'currency' => 'BRL'))
                ->add('voucher0', 'file', array ('label' => "Voucher 1" , 'property_path' => false))
                ->add('obs', 'textarea', array ('label' => 'Observações', 'required' => false));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'intention' => 'deal_creation',
        );
    }

    public function getName() {
        return 'contact';
    }

}
