<?php
/**
 * Reurbano/AnalyticsBundle/Document/DoubleClickRepository.php
 *
 * Repositório de zona
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

use Symfony\Component\Validator\Constraints\True;

use Mastop\SystemBundle\Document\BaseRepository;

class DoubleClickRepository extends BaseRepository
{

    /**
     * Retorna todos os DoubleClick ordenados pelo parÃ¢metro "click"
     *
     * @param string $id
     * @return bool
     */
    public function findAll()
    {
        return $this->createQueryBuilder()
                ->sort('click', 'asc')
                ->getQuery()->execute();
    }
    
    /**
     * Localiza um DoubleClick à partir user
     *
     * @param User $user, usuário da publicação
     *
     * @return bool
     */
    public function findByUser($user){
    	return $this->createQueryBuilder()
		    	->field('user.$id')->equals(new \MongoId($user->getId()))
		    	->getQuery()
		    	->getSingleResult();
    }
    
    /**
     * Atualiza os cliques do DoubleClick à partir user
     *
     * @param User $user, usuário da publicação
     * 
     */
    public function updateClick($user){
    	return $this->createQueryBuilder()
		    	->update()
		    	->field('user.$id')->equals(new \MongoId($user->getId()))
		    	->field('view')->inc(1)
		    	->field('updatedAt')->set(new \DateTime)
		    	->getQuery()->execute();
    }
    
    /**
     * Localiza um DoubleClick à partir publication
     *
     * @param User $user, usuário da publicação
     * @param Publication $publication, area da publicação
     *
     * @return bool
     */
    public function findByUserAndPublication($user, $publication){
    	$qb = $this->createQueryBuilder();
    	
    	return $this->createQueryBuilder()
    			->field('user.$id')->equals(new \MongoId($user->getId()))
		    
    			->getQuery()
    			->getSingleResult();
    }
    
    /**
     * Localiza um DoubleClick à partir publication
     *
     * @param User $user, usuário da publicação
     * @param Publication $publication, area da publicação
     *
     * @return bool
     */
    public function updateByUserAndPublication($user, $publication){
    	$qb = $this->createQueryBuilder();
    	 
    	return $this->createQueryBuilder()    	
    		->update()
	    	->field('user.$id')->equals(new \MongoId($user->getId()))
	    	->field('click')
	    	->elemMatch(
	    			$qb->expr()
	    			->field('publication.$id')->equals(new \MongoId($publication->getId()))
	    			->field('click')->inc(1)
	    	)
	    	->getQuery()
	    	->execute();
    }
    
    /**
     * Atualiza os cliques do DoubleClick à partir user
     *
     * @param User $user, usuário da publicação
     * @param Publication $publication, area da publicação
     * @param boolean $dcReset, true or false, zera ou não os clicks
     *
     */
	public function updateClickUserPublication($user, $publication){
    	$dm = $this->getDocumentManager();
    	
    	// Atualiza o total de clicks do usuario
    	$this->updateClick($user);
    	
    	$dcPublication = $this->findByUserAndPublication($user, $publication);
    	if($dcPublication){
    		$publications = $dcPublication->getPublication();
    		if($publications){
    			$publications = $publications->toArray();
	    		$pIndex = 0;
	    		foreach ($publications as $k => $public) {
	    			if($public->getId() == $publication->getId()){
	    				$public->setClickUnique($public->incClickUnique());
	    				$dcPublication->alterPublication($public, $pIndex);
	    				$dm->persist($dcPublication);
	    			}
	    			$pIndex++;
		    	}
    		}
    	}
    	$dm->flush();
	}
}