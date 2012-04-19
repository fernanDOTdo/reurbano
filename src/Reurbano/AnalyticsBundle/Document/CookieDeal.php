<?php

/**
 * Reurbano/AnalyticsBundle/Document/CookieDeal.php
 *
 * Controla os cookies de navegação nas ofertas
 *  
 * 
 * @copyright 2012 Reurbano.
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

use Doctrine\ODM\MongoDB\Mapping\Annotations\String;

class CookieDeal implements \Serializable
{
    /**
	 * Lista de cookies existante no navegador do usuário
	 *
	 * @var array
	 */
	protected $cookies;
	
	/**
	 * Requisições, detlhes do cabeçalho do navegador
	 *
	 * @var $request
	 */
	protected $request;
	
	/**
	 * Divisor dos valores contidos no cookie
	 *
	 * @var $request
	 */
	protected $divisor = "|";
	
	/**
	 * Nome do cookie
	 *
	 * @var string
	 */
	protected $name = "adDeal";
	
	public function __construct($request = null, $cookies = null)
	{
		$this->request = $request;
		$this->cookies = $cookies;
	}
	
	/**
	 * Verifica se existe o cookie $name
	 *
	 * @param string $name, nome do cookie para ser verificado
	 *
	 * @return boolean
	 *
	 */
	public function hasCookie($name)
	{
		return ($this->cookies->has($name)) ? true : false;
	}
	
	/**
	 * Transforma o conteúdo do cookie,$name, em array
	 *
	 * @param string $name, nome do cookie para ser transformado
	 *
	 * @return array
	 *
	 */
	public function explodeCookie($name)
	{
		return explode('|',$this->cookies->get($name));
	}
	
	/**
	 * Localiza um valor dentro de uma lista
	 *
	 * @param string $value, valor para ser localizado na lista
	 * @param array $list, lista de valores para ser varrida
	 *
	 * @return boolean
	 *
	 */
	public function findCookie($value, $list)
	{
		return (in_array($value, $list)) ? true : false;
	}
	
	/**
	 * Busca uma string dentro de uma array ou matriz
	 *
	 * @param array $str, string a ser buscada
	 * @param array $array, lista que serÃ¡ buscada
	 *
	 * @return int, true ou false
	 */
	public function searchCookie($array, $zone, $row, $column){
		if (is_array($array)){
			foreach($array as $key => $value) {
				if (is_array($value))
					foreach($value as $chave => $valor) {
					if (($array[$key]['zone'] == $zone) && ($array[$key]['row'] == $row) && ($array[$key]['column'] == $column)){
						return $key;
					}
				}
			}
		}
		return -1;
	}
	
	
	/**
     * Busca uma string dentro de uma array ou matriz
     *
     * @param array $str, string a ser buscada
     * @param array $array, lista que serÃ¡ buscada
     *
     * @return int, true ou false
     */
    public function array_search_i($array, $keySearch, $valueSearch){
    	if (is_array($array)){
		    foreach($array as $key => $value) {
		    	if (is_array($value))
			    	foreach($value as $chave => $valor) {
		        	if (($keySearch == $chave) && ($valueSearch == $valor)){	        		
		        		return $key;
		        	}
		        }
		    }
    	}
		return -1;
	}
	
	/**
	 * Método para criar o array que será incluido no cookie
	 *
	 * @param string $row linha clicada
	 * @param string $column coluna clicada
	 * @param string $zone zona clicada
	 * @param string $deal oferta clicada
	 *
	 * @return array
	 */
	public function addCookie($row, $column, $zone, $deal)
	{
		return array('zone' => $zone, 'row' => $row, 'column' => $column, 'deals' => array(0 => array('deal' => $deal, 'click' => 1)), 'click' => 1);
	}
	
	/**
	 * Método para criar o array que será incluido no cookie
	 *
	 * @param string $array lista completa que esta no cookie
	 * @param string $key chave que será adicionado a oferta
	 * @param string $deal identificação da oferta que foi clicada
	 *
	 * @return array
	 */
	public function addDealCookie($deal)
	{
		return array('deal' => $deal, 'click' => 1, 'createdAt' => date("Y-m-d H:i:s"), 'updatedAt' => date("Y-m-d H:i:s"));
	}
	
	/**
	 * Set name
	 *
	 * @param string $name
	 */
	public function setName($name)
	{
		return $this->name = $name;
	}
	
	/**
	 * Get name
	 *
	 * @return string $name
	 *
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Edita ou cria o cookie 
	 *
	 * @param string $value, valor do cookie a ser adicionado
	 * 
	 * @return array
	 * 
	 */
	public function checkCookie($row, $column, $zone, $deal)
	{
		$key = -1;
		//Verifica se há o cookie na maquina do usuário		
		if ($this->hasCookie($this->getName())){
			$cookie = $this->cookies->get($this->getName());
			$list = $this->dencodeBase64($cookie);
		}else{
			$list = array(
					0 => array('zone' => $zone, 'row' => $row, 'column' => $column, 'deals' => array(0 => array('deal' => $deal, 'click' => 0, 'createdAt' => date("Y-m-d H:i:s"), 'updatedAt' => date("Y-m-d H:i:s") )), 'click' => 0)
			);
		}
		
		// Verificar se existe no cookie a ZONA + LINHA + COLUNA que foi clicada
		$key = $this->searchCookie($list, $zone, $row, $column);
		if ($key >= 0){		
			//Adiona +1 nessa ZONA + LINHA + COLUNA
			$list[$key]['click'] = $list[$key]['click']+1;
			
			//Obtem as Deals do Cookie
			$listDeals = $list[$key]['deals'];
			$KeyDeal = $this->array_search_i($listDeals, 'deal', $deal);
			if ($KeyDeal >= 0){
				//adiciona +1 click para a deal
				$list[$key]['deals'][$KeyDeal]['click'] = $list[$key]['deals'][$KeyDeal]['click']+1;
				$list[$key]['deals'][$KeyDeal]['updatedAt'] = date("Y-m-d H:i:s");
			}else{
				//regista o clique da deal
				$list[$key]['deals'][] = $this->addDealCookie($deal);
			}
		}else{
			$list[] = $this->addCookie($row, $column, $zone, $deal);
		}

		//print_r($list);
		//echo "\n\n";

		return array($this->getName(), $this->encodeBase64($list));
	}
	
	/**
	 * Método para codificar os dados do cookie
	 * 
	 * @param string $value
	 * @return string
	 */	
	public function encodeBase64($value)
	{
		//return base64_encode($value);
		return strtr(base64_encode(addslashes(gzcompress($this->serialize($value),9))), '+/=', '-_,');
	}
	
	/**
	 * Método para decodificar os dados do cookie
	 * 
	 * @param string $value
	 * @return string
	 */	
	public function dencodeBase64($value)
	{
		//return base64_decode($value);
		return $this->unserialize(gzuncompress(stripslashes(base64_decode(strtr($value, '-_,', '+/=')))));
	}
	
	public function serialize($value)
	{
		return serialize($value);
	}
	
	public function unserialize($serialized)
	{
		return unserialize($serialized);
	}
}