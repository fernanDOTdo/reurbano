<?php
/**
 * Reurbano/UserBundle/Form/Frontend/BankDataType.php
 *
 * Formulário para Dados Bancários
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

namespace Reurbano\UserBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BankDataType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('name', 'text', array('label' => 'Banco'));
        $builder->add('agency', 'text', array('label' => 'Agência'));
        $builder->add('account', 'text', array('label' => 'Conta'));
        $builder->add('cpf', 'text', array('label' => 'CPF do Titular'));
        $builder->add('type', 'choice', array('label' => 'Tipo de Conta', 'choices'   => array('1' => 'Conta Corrente', '2' => 'Conta Poupança')));
        $builder->add('obs', 'textarea', array('label' => 'Observações', 'required' => false));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Reurbano\UserBundle\Document\BankData',
        );
    }

    public function getName() {
        return 'bankdata';
    }

}
