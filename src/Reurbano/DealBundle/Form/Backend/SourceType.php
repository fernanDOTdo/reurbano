<?php

namespace Reurbano\DealBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SourceType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('id', 'hidden')
                ->add('title', 'text', array('label'=>'Título', 'attr' => array('class' => 'medium  LV_invalid_field')))
                ->add('filename', 'text', array('label'=>'Foto'))
                ->add('url', 'url', array('label'=>'Link'))
                ->add('price', 'money', array('label'=>'Valor Original', 'divisor' => 100, 'currency' => 'BRL'))
                ->add('priceOffer', 'money', array('label'=>'Valor com desconto', 'divisor' => 100, 'currency' => 'BRL'))
                ->add('site', 'document', array('label'=>'Site', 'class' => 'Reurbano\\DealBundle\\Document\\Site', 'property'=>'name', 'required' => false))
                ->add('city', 'document', array('label'=>'Cidade', 'class' => 'Reurbano\\CoreBundle\\Document\\City', 'property'=>'name' ))
                ->add('category', 'document', array('label'=>'Categoria', 'class' => 'Reurbano\\DealBundle\\Document\\Category', 'property'=>'name' ))
                ->add('rules', 'textarea', array('label'=>'Regulamento'))
                ->add('details', 'textarea', array('label'=>'Descrição'))
                ->add('businessUrl', 'url', array('label'=>'Site da empresa'))
                ->add('businessName', 'text', array('label'=>'Nome da empresa'))
                ->add('businessAddress', 'text', array('label'=>'Endereço da empresa'))
                ->add('businessCep', 'text', array('label'=>'CEP da empresa'))
                ->add('businessLatitude', 'text', array('label'=>'Latitude da empresa'))
                ->add('businessLongitude', 'text', array('label'=>'Longitude da empresa'))
                ->add('expiresAt', 'date', array('label'=>'Validade', 'format'=>'d/m/Y', 'widget'=>'single_text', 'attr'=>array('class'=>'datepicker')))
            ;
    }
    
    
    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Reurbano\DealBundle\Document\Source',
            'intention' => 'source_creation',
        );
    }

    public function getName() {
        return 'source';
    }

}
