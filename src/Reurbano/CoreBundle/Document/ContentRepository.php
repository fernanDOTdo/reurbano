<?php

/**
 * Reurbano/CoreBundle/Document/ContentRepository.php
 *
 * Repositório de conteúdo estático.
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

namespace Reurbano\CoreBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class ContentRepository extends BaseRepository
{
    public function findBySlug($slug){
        return $this->findOneBy(array('slug' => $slug), array());
    }
    
    /**
     * Pega todos ordenado por CREATED
     *
     * @return Content ou null
     **/
    public function findAllByCreated()
    {
        return $this->findBy(array(), array('created'=>'desc'));
    }
}