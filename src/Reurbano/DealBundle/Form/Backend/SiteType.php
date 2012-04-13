<?php

namespace Reurbano\DealBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Description of SiteType
 *
 * @author Rafael Basquens <rafael@basquens.com>
 */
class SiteType extends AbstractType {
    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('name', 'text', array('label'=>'Nome'));
        $builder->add('url', 'text',  array('label'=>'Url'));
        $builder->add('logo', 'file', array (
            'label'         => "Logo",
            'required'      => false,
            'property_path' => false));
    }

    public function getDefaultOptions() {
        return array(
            'data_class' => 'Reurbano\DealBundle\Document\Site',
            'intention' => 'site_creation',
        );
    }

    public function getName() {
        return 'site';
    }
}