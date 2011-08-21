<?php
/**
 * Reurbano/DealBundle/Form/ComissionType.php
 *
 * Formulário para Comissão
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

namespace Reurbano\DealBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ComissionType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('sellerpercent', 'text', array('label'=>'Comissão a cobrar do vendedor %'));
        $builder->add('sellerreal', 'text', array('label'=>'Comissão a cobrar do vendedor R$'));
        $builder->add('buyerpercent', 'text', array('label'=>'Comissão a cobrar do comprador %'));
        $builder->add('buyerreal', 'text', array('label'=>'Comissão a cobrar do comprador R$'));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Reurbano\DealBundle\Document\Comission',
        );
    }

    public function getName() {
        return 'comission';
    }

}
