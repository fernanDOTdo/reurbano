<?php

/**
 * Reurbano/AnalyticsBundle/Document/Coordinates.php
 *
 * Coordenadas Linha e Coluna
 *  
 * 
 * @copyright 2011 Reurbano.
 * @link http://www.reurbano.com.br
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

namespace Reurbano\AnalyticsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Coordinates
{
    /**
     * Linha
     *
	 * @var int
	 * @ODM\Int
     */
    protected $linha;
    
    /**
     * Coluna
     *
	 * @var int
	 * @ODM\Int
     */
    protected $coluna;

    /**
     * Set linha
     *
     * @param int $linha
     */
    public function setLinha($linha)
    {
        $this->linha = $linha;
    }

    /**
     * Get linha
     *
     * @return int $linha
     */
    public function getLinha()
    {
        return $this->linha;
    }

    /**
     * Set coluna
     *
     * @param int $coluna
     */
    public function setColuna($coluna)
    {
        $this->coluna = $coluna;
    }

    /**
     * Get coluna
     *
     * @return int $coluna
     */
    public function getColuna()
    {
        return $this->coluna;
    }
}
