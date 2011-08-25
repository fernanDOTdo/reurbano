<?php

/**
 * Reurbano/CoreBundle/Controller/Frontend
 *
 * Controller que exibirá a página de conteúdo estático
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


namespace Reurbano\CoreBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\CoreBundle\Document\Content;


class ContentController extends BaseController
{
    /**
     * @Route("/pg/{slug}", name="core_content_index")
     * @Template()
     */
    public function indexAction(Content $content)
    {
        $breadcrumbs[]['title'] = $content->getTitle();
        return array('breadcrumbs'=>$breadcrumbs, 'content' => $content, 'title' => $content->getSeoTitle());
    }
}
