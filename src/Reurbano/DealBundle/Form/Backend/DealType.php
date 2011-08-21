<?php

namespace Reurbano\DealBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DealType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('id', 'hidden')
                ->add('label', 'text', array('label'=>'Título','attr'=>array('class'=>'large')))
                ->add('price', 'money', array('label'=>'Valor desejado', 'currency' => 'BRL'))
                ->add('quantity', 'integer', array('label'=>'Quantidade disponível'))
                ->add('active', 'choice', array('choices' => array('1' => 'Sim','0' => 'Não'), 'label'=>'Ativo'))
                ->add('special', 'choice', array('choices' => array('1' => 'Sim','0' => 'Não'), 'label'=>'Destaque'))
                ->add('checked', 'choice', array('choices' => array('1' => 'Sim','0' => 'Não'), 'label'=>'Conferido'))
//                ->add('voucher0', 'file', array ('label' => "Voucher" ,'required' => false, 'property_path' => false))
                //->add('voucher', 'document', array('label'=>'Voucher', 'class' => 'Reurbano\\DealBundle\\Document\\Voucher', 'property'=>'title' ))
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
