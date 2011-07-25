<?php

namespace Reurbano\DealBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DealType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('id', 'hidden')
                ->add('label', 'text', array('label'=>'Título'))
                ->add('user', 'document', array('label'=>'Usuário', 'class' => 'Reurbano\\UserBundle\\Document\\User', 'property'=>'name', 'required' => false))
                ->add('source', 'document', array('label'=>'Oferta', 'class' => 'Reurbano\\DealBundle\\Document\\Source', 'property'=>'title', 'property_path' => false ))
                ->add('price', 'money', array('label'=>'Valor desejado', 'divisor' => 100, 'currency' => 'BRL'))
                ->add('quantity', 'integer', array('label'=>'Quantidade disponível'))
                ->add('active', 'choice', array('choices' => array('sim' => 'Sim','nao' => 'Não'), 'label'=>'Ativo'))
                ->add('special', 'choice', array('choices' => array('sim' => 'Sim','nao' => 'Não'), 'label'=>'Destaque'))
                ->add('voucher0', 'file', array ('label' => "Voucher" ,'required' => false, 'property_path' => false))
                ->add('voucher1', 'file', array ('label' => "Voucher" , 'required' => false, 'property_path' => false))
                ->add('voucher2', 'file', array ('label' => "Voucher" ,'required' => false, 'property_path' => false))
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
