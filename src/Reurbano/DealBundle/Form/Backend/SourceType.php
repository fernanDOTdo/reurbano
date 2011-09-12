<?php

namespace Reurbano\DealBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Reurbano\CoreBundle\Form\CoordinatesType;

class SourceType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('id', 'hidden')
                ->add('title', 'text', array('label'=>'Título', 'attr' => array('class' => 'medium  LV_invalid_field')))
                ->add('filename', 'text', array('label'=>'Foto'))
                ->add('url', 'url', array('label'=>'Link'))
                ->add('price', 'money', array('label'=>'Valor Original', 'currency' => 'BRL','attr'=>array('class'=>'small')))
                ->add('priceOffer', 'money', array('label'=>'Valor com desconto', 'currency' => 'BRL','attr'=>array('class'=>'small')))
                ->add('site', 'document', array('label'=>'Site', 'class' => 'Reurbano\\DealBundle\\Document\\Site', 'property'=>'name', 'required' => false))
                ->add('city', 'document', array('label'=>'Cidade', 'class' => 'Reurbano\\CoreBundle\\Document\\City', 'property'=>'name' ))
                ->add('category', 'document', array('label'=>'Categoria', 'class' => 'Reurbano\\DealBundle\\Document\\Category', 'property'=>'name' ))
                ->add('totalcoupons', 'integer', array('label'=>'Total de cupons vendidos','attr'=>array('class'=>'small')))
                ->add('totalsell', 'money', array('label'=>'Valor total faturado', 'currency' => 'BRL','attr'=>array('class'=>'small')))
                ->add('rules', 'textarea', array('label'=>'Regulamento'))
                ->add('details', 'textarea', array('label'=>'Descrição'))
                ->add('businessUrl', 'url', array('label'=>'Site da empresa'))
                ->add('businessName', 'text', array('label'=>'Nome da empresa'))
                ->add('businessAddress', 'text', array('label'=>'Endereço da empresa'))
                ->add('businessCep', 'text', array('label'=>'CEP da empresa'))
                ->add('expiresAt', 'date', array('label'=>'Validade', 'format'=>'d/m/Y', 'widget'=>'single_text', 'attr'=>array('class'=>'datepicker')))
                ->add('coordinates', new CoordinatesType(), array('label'=>'Coordenadas'))
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
