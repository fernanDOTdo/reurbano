<?php
/**
 * Reurbano/DealBundle/Form/Backend/SourceEditType.php
 *
 * Form Type para site e categoria da oferta no /admin/ofertas/editar/
 *  
 * 
 * @copyright 2012 GUBN
 * @link http://www.gubn.com.br
 * @author Saulo Lima <saulo@gubn.com.br>
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

namespace Reurbano\DealBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SourceEditType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder
                ->add('category', 'document', 
                        array(
                            'label'=>'Categoria da Oferta', 
                            'class' => 'Reurbano\\DealBundle\\Document\\Category', 
                            'property'=>'name', 
                            'document_manager' => $options['document_manager'],
                            'required' => false,
                            'empty_value' => "Selecione",
                        )
                )
                ->add('site', 'document', 
                        array(
                            'label'=>'Site da Oferta', 
                            'class' => 'Reurbano\\DealBundle\\Document\\Site', 
                            'property'=>'name', 
                            'document_manager' => $options['document_manager'],
                            'required' => false,
                            'empty_value' => "Selecione",
                        )
                )
            ;
    }
    
    
    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Reurbano\DealBundle\Document\SourceEmbed',
            'document_manager' => 'default',
        );
    }

    public function getName() {
        return 'source';
    }
}