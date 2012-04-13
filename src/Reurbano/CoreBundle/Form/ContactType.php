<?php

/**
 * Reurbano/Corebundle/Form/ContactType.php
 *
 * Cria o formulario para um novo contato
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

class ContactType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('name', 'text', array('label' => 'Nome:', 'attr' => array('style' => 'width: 450px;')));
        $builder->add('phone', 'text', array('required' => false, 'label' => 'Telefone:', 'attr' => array('style' => 'width: 450px;')));
        $builder->add('mail', 'email', array('label' => 'E-mail:', 'attr' => array('style' => 'width: 450px;')));
        $builder->add('msg', 'textarea', array('label' => 'Mensagem:', 'attr' => array('style' => 'width: 450px;', 'rows' => '8')));
        $builder->add('email', 'text', array(
            'required' => false,
            'attr' => array(
                'class' => 'hidden'
            )));
        $builder->add('coment', 'textarea', array(
            'required' => false,
            'attr' => array(
                'class' => 'hidden'
            )));
    }

    public function getDefaultOptions() {
        return array(
            'data_class' => 'Reurbano\CoreBundle\Document\Contact',
            'intention' => 'contact_creation',
        );
    }

    public function getName() {
        return 'contact';
    }

}
