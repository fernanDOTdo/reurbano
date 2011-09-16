<?php
/**
 * Reurbano/UserBundle/Controller/Widget/MyDataController.php
 *
 * Widget dos dados do usuário
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

namespace Reurbano\UserBundle\Controller\Widget;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\UserBundle\Document\User;

/**
 * Controller que será os dados dentro da aba "Meus Dados" no minha-conta
 */

class MyDataController extends BaseController
{
    /**
     * Dashboard do MyData
     * @Template()
     */
    public function dashboardAction($userId = null){
        $user = $this->get('security.context')->getToken()->getUser();
        return array(
            'usuario' => $user,
        );
    }
}