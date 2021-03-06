<?php

namespace Reurbano\DealBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Reurbano\DealBundle\Form\Backend\ComissionType;
use Reurbano\DealBundle\Form\Backend\SourceEditType;

class DealType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('id', 'hidden')
                ->add('label', 'text', array('label'=>'Título','attr'=>array('class'=>'large')))
                ->add('price', 'money', array('label'=>'Valor desejado pelo vendedor', 'currency' => 'BRL','attr'=>array('class'=>'small')))
                
              	->add('source', new SourceEditType(), array('label'=>'', 'em' => $options['em']))
              
                //->add('source', 'document', array('label'=>'Oferta', 'class' => 'Reurbano\\DealBundle\\Document\\SourceEmbed', 'property'=>'titleFormat', 'property_path' => false, 'attr' => array('style' => 'width: 400px;') ))
                //->add('quantity', 'integer', array('label'=>'Quantidade disponível','attr'=>array('class'=>'small')))
                ->add('active', 'choice', array('choices' => array('1' => 'Sim','0' => 'Não'), 'label'=>'Ativo'))
                ->add('special', 'choice', array('choices' => array('1' => 'Sim','0' => 'Não'), 'label'=>'Destaque'))
                ->add('checked', 'choice', array('choices' => array('1' => 'Sim','0' => 'Não'), 'label'=>'Conferido'))
                //->add('expiresAt', 'date', array('label'=>'Validade', 'format'=>'dd/M/Y', 'widget'=>'single_text', 'attr'=>array('class'=>'datepicker')))
                ->add('comission', new ComissionType(), array('label'=>'Comissão'))
                ->add('obs', 'textarea', array('label' => 'Observação', 'required' => false, 'attr'  => array('style' => 'width: 100%;')))
                //->add('tags', 'collection', array('label' => 'SEO da Oferta', 'required' => false, 'attr'  => array('style' => 'width: 100%;')))
                //->add('voucher0', 'file', array ('label' => "Voucher" , 'property_path' => false))
                //->add('voucher', 'document', array('label'=>'Voucher', 'class' => 'Reurbano\\DealBundle\\Document\\Voucher', 'property'=>'title' ))
            ;
    
    }

    public function getDefaultOptions() {
        return array(
            'data_class' => 'Reurbano\DealBundle\Document\Deal',
            'intention' => 'deal_creation',
            'em' => 'default',
        );
    }

    public function getName() {
        return 'deal';
    }

}
