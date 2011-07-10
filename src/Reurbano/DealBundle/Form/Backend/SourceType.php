<?php

namespace Reurbano\DealBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SourceType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('id', 'hidden')
                ->add('title', 'text', array('label'=>'Título'))
                ->add('filename', 'text', array('label'=>'Foto'))
                ->add('url', 'text', array('label'=>'Link'))
                ->add('price', 'text', array('label'=>'Valor Original'))
                ->add('priceOffer', 'text', array('label'=>'Valor com desconto'))
            ;
    }
    protected $price;
    
    /**
     * Preço com desconto da oferta
     *
     * @var float
     * @ODM\Float
     */
    protected $priceOffer;
    
    /**
     * Preço original da oferta
     *
     * @ODM\ReferenceOne(targetDocument="Reurbano\CoreBundle\Document\City")
     */
    protected $city;
    
    /**
     * Regulamento da oferta
     *
     * @var string
     * @ODM\String
     */
    protected $rules;
    
    /**
     * Descrição da oferta
     *
     * @var string
     * @ODM\String
     */
    protected $details;
    
    /**
     * Site da empresa
     *
     * @var string
     * @ODM\String
     */
    protected $businessUrl;
    
    /**
     * Nome da empresa
     *
     * @var string
     * @ODM\String
     */
    protected $businessName;
    
    /**
     * Endereço da empresa
     *
     * @var string
     * @ODM\String
     */
    protected $businessAddress;
    
    /**
     * Latitude da empresa
     *
     * @var int
     * @ODM\Int
     */
    protected $businessLatitude;
    
    /**
     * Longitude da empresa
     *
     * @var int
     * @ODM\Int
     */
    protected $businessLongitude;

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
