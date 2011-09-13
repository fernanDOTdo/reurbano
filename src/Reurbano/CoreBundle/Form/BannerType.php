<?php

/**
 * Reurbano/Corebundle/Form/BannerType.php
 *
 * Cria o formulario para um novo banner
 *  
 * 
 * @copyright 2011 Mastop Internet Development.
 * @link http://www.mastop.com.br
 * @author Rafael Basquens <rafael@basquens.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */



namespace Reurbano\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BannerType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('id', 'hidden');
        $builder->add('city', 'document', array('label'=>'Cidade', 'class' => 'Reurbano\\CoreBundle\\Document\\City', 'property'=>'name', 'required' => false, 'empty_value' => 'Todas' ));
        $builder->add('title', 'text', array('label' => 'Título'));
        $builder->add('url', 'url');
        $builder->add('active', 'checkbox', array(
            'label'    => 'Ativo?',
            'required' => false,
        ));
        $builder->add('newWindow', 'checkbox', array(
            'label'    => 'Abrir em nova janela?',
            'required' => false,
        ));
        $builder->add('order', 'integer', array('label' => 'Ordem'));
        $builder->add('logo', 'file', array (
            'label'         => "Imagem",
            'required'      => false,
            'property_path' => false
            ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Reurbano\CoreBundle\Document\Banner',
            'intention' => 'banner_creation',
        );
    }

    public function getName() {
        return 'banner';
    }

}
