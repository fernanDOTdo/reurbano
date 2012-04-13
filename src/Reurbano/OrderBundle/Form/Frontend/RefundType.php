<?php
/**
 *                                              ,d                              
 *                                              88                              
 * 88,dPYba,,adPYba,   ,adPPYYba,  ,adPPYba,  MM88MMM  ,adPPYba,   8b,dPPYba,   
 * 88P'   "88"    "8a  ""     `Y8  I8[    ""    88    a8"     "8a  88P'    "8a  
 * 88      88      88  ,adPPPPP88   `"Y8ba,     88    8b       d8  88       d8  
 * 88      88      88  88,    ,88  aa    ]8I    88,   "8a,   ,a8"  88b,   ,a8"  
 * 88      88      88  `"8bbdP"Y8  `"YbbdP"'    "Y888  `"YbbdP"'   88`YbbdP"'   
 *                                                                 88           
 *                                                                 88           
 * 
 * Reurbano/OrderBundle/Form/Frontend/RefundType.php
 *
 * Formulário de Solicitação de Reembolso
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

namespace Reurbano\OrderBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RefundType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('reason', 'textarea', array(
                    'label' => 'Motivo para o Reembolso',
                    'attr'  => array(
                        'style' => 'width: 80%;',
                        'class' => 'm10 mT0',
                    )));
    }

    public function getDefaultOptions() {
        return array(
            'data_class' => 'Reurbano\OrderBundle\Document\Refund',
            'intention' => 'refund_creation',
        );
    }

    public function getName() {
        return 'refund';
    }
}
