<?php
/**
 * Reurbano/AnalyticsBundle/Document/PublicationRepository.php
 *
 * Reposit�rio de zona
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

use Mastop\SystemBundle\Document\BaseRepository;

class PublicationRepository extends BaseRepository
{

    /**
     * Retorna todos os banners ordenados pelo parâmetro "order" e "name"
     *
     * @param string $id
     * @return bool
     */
    public function findAll()
    {
        return $this->createQueryBuilder()
                ->sort('name', 'asc')
                ->getQuery()->execute();
    }
    
    /**
     * Localiza uma publica��o�� partir area e a deal
     *
     * @param Area $area, area da publica��o
     * @param Deal $deal, deal publicada nesta �rea
     *
     * @return bool
     */
    public function findByAreaAndDeal($area, $deal){
    	return $this->createQueryBuilder()
		    	->field('area.$id')->equals((int) $area->getId())
		    	->field('deal.$id')->equals(new \MongoId($deal->getId()))
		    	->getQuery()
		    	->getSingleResult();
    }
    
    /**
     * Atualiza os cliques da publica��o�� partir area e a deal
     *
     * @param Area $area, area da publica��o
     * @param Deal $deal, deal publicada nesta �rea
     */
    public function updateClick($area, $deal){
    	return $this->createQueryBuilder()
		    	->update()
		    	->field('area.$id')->equals((int) $area->getId())
		    	->field('deal.$id')->equals(new \MongoId($deal->getId()))
		    	->field('click')->inc(1)
		    	->field('updatedAt')->set(new \DateTime)
		    	->getQuery()->execute();
    }
}