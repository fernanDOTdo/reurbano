<?php
/**
 * Reurbano/CoreBundle/Form/ContentType.php
 *
 * Formulário para páginas estáticas.
 *  
 * 
 * @copyright 2011 Mastop Internet Development.
 * @link http://www.mastop.com.br
 * @author Fernando Santos <o@fernan.do>
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

class ContentType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('id', 'hidden');
        $builder->add('title', 'text', array('label'=>'Título da Página'));
        $builder->add('seoTitle', 'text', array('label'=>'Título da Janela', 'required'=>false));
        $builder->add('seoKeywords', 'text', array('label'=>'Meta Keywords', 'required'=>false));
        $builder->add('seoDescription', 'textarea', array('label'=>'Meta Description', 'required'=>false));
        $builder->add('content', 'textarea', array('label'=>'Conteúdo', 'attr'=>array('class'=>'large')));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Reurbano\CoreBundle\Document\Content',
            'intention' => 'content_creation',
        );
    }

    public function getName() {
        return 'content';
    }
}