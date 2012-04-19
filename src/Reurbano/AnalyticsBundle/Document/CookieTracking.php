<?php

/**
 * Reurbano/AnalyticsBundle/Document/CookieTracking.php
 *
 * Controla os cookies pra Tracking dos usuários de sites externos
 * 
 * Detalhe do conteúdo do cookie
 * 	O Cookie armazena uma lista dos dados listados abaixo
 * 		source, origem do dado
 * 		city, cidade de origem da deal
 * 		category, categoria da deal
 * 		deal, oferta clicada
 * 		click, quantos cliques essa deal teve
 * 		createdAt, quando ocorreu o primeiro clique à essa deal
 * 		access, controle se o acesso à deal já foi ou não registrado em banco de dados, 0 for false and 1 for true
 * 		sell, controle se a venda da deal já foi ou não registrada em banco de dados, 0 for false and 1 for true
 *
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
 * 
 * 
 */

namespace Reurbano\AnalyticsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations\String;

class CookieTracking implements \Serializable
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
	protected $name = "adTracking";
	
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
	public function hasCookie($name = null)
	{
		$name = ($name == null) ? $this->getName() : $name;
		return ($this->cookies->has($name)) ? true : false;
	}
	
	/**
	 * Verifica se existe o cookie e retorno o mesmo decodificado
	 *
	 * @param string $name, nome do cookie para ser verificado
	 *
	 * @return boolean
	 *
	 */
	public function getListCookie($name = null)
	{
		//Verifica se há o cookie na maquina do usuário
		if ($this->hasCookie($this->getName())){
			$cookie = $this->cookies->get($this->getName());
			return $this->dencodeBase64($cookie);
		}
		
		return null;
	}
	
	
	
	/**
	 * Transforma o conteúdo do cookie,$name, em array
	 *
	 * @param string $name, nome do cookie para ser transformado
	 *
	 * @return array
	 *
	 */
	public function explodeCookie($name = null)
	{
		$name = ($name == null) ? $this->getName() : $name;
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
	 * @param string $source, origem do dado
	 * @param string $city, cidade de origem da deal
	 * @param string $category, categoria da deal
	 *
	 * @return int, true ou false
	 */
	public function searchCookie($array, $source, $city, $category){
		if (is_array($array)){
			foreach($array as $key => $value) {
				if (is_array($value))
					foreach($value as $chave => $valor) {
					if (($array[$key]['source'] == $source) && ($array[$key]['city'] == $city) && ($array[$key]['category'] == $category) ){
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
     * @param array $array, lista que será buscada
     *
     * @return int, true ou false
     */
    public function array_search_i($array, $keySearch, $valueSearch){
    	if (is_array($array)){
		    foreach($array as $key => $value) {		    	
		    	if (is_array($value)){
		    		$this->array_search_i($value, $keySearch, $valueSearch);
		    	}else{		    		
		    		if (($keySearch == $key) && ($valueSearch == $value)){
		    			//echo $keySearch ." == ". $key ." || ". $valueSearch ." == ". $value."<br/>";
		    			return true;
		    		}
		    	}
		    }
    	}
    	return false;
	}
	
	/**
	 * Método para registar uma origem completa no cookie
	 *
	 * @param string $source, origem do dado
	 * @param string $city, cidade de origem da deal
	 * @param string $category, categoria da deal
	 * @param string $deal oferta clicada
	 * @param int $access registro de acesso na deal, 0 for false and 1 for true
	 * @param int $sell registro de venda na deal, 0 for false and 1 for true
	 *
	 * @return array
	 */
	public function addCookie($source, $city, $category, $deal, $access = 0, $sell = 0)
	{
		return array('source' => $source, 'city' => $city, 'category' => $category, 'deals' => array(0 => $this->addDealCookie($deal)), 'click' => 0);
	}
	
	/**
	 * Método para registar uma nova deal dentro de uma origem no cookie
	 *
	 * @param string $array lista completa que esta no cookie
	 * @param string $key chave que será adicionado a oferta
	 * @param string $deal identificação da oferta que foi clicada
	 *
	 * @return array
	 */
	public function addDealCookie($deal)
	{
		return array('deal' => $deal, 'createdAt' => date("Y-m-d H:i:s"), 'access' => 0, 'sell' => 0);
	}
		
	/**
	 * Registra os acesso em Coookir 
	 *
	 * @param string $source, origem do dado
	 * @param string $city, cidade de origem da deal
	 * @param string $category, categoria da deal 
	 * @param string $deal oferta clicada
	 * 
	 * @return array
	 * 
	 */
	public function recordsAccessCookie($source, $city, $category, $deal)
	{
		$key = -1;
		//Verifica se há o cookie na maquina do usuário		
		if ($this->hasCookie($this->getName())){
			$cookie = $this->cookies->get($this->getName());
			$list = $this->dencodeBase64($cookie);
		}else{
			$list = array(
					0 => $this->addCookie($source, $city, $category, $deal)
			);
		}
		
		// Verificar se existe no cookie a ORIGEM + CIDADE + CATEGORIA que foi clicada
		$key = $this->searchCookie($list, $source, $city, $category);
		if ($key >= 0){		
			//Adiona +1 nessa ORIGEM + CIDADE + CATEGORIA
			$list[$key]['click'] = $list[$key]['click']+1;
			
			//regista o clique da deal
			$list[$key]['deals'][] = $this->addDealCookie($deal);
		}else{
			$list[] = $this->addCookie($source, $city, $category, $deal);
		}

		//print_r($list);

		return array($this->getName(), $this->encodeBase64($list));
	}
	
	/**
	 * Registra os acesso em Cookie
	 *
	 * @param string $source, origem do dado
	 * @param string $city, cidade de origem da deal
	 * @param string $category, categoria da deal
	 * @param string $deal oferta clicada
	 *
	 * @return array
	 *
	 */
	public function recordsSellCookie($source, $city, $category, $deal)
	{
		$key = -1;
		
		//Verifica se há o cookie na maquina do usuário
		if ($this->hasCookie($this->getName())){
			$cookie = $this->cookies->get($this->getName());
			$list = $this->dencodeBase64($cookie);
		}else{
			$list = array(
					0 => $this->addCookie($source, $city, $category, $deal)
			);
		}
	
		// Verificar se existe no cookie a ORIGEM + CIDADE + CATEGORIA que foi clicada
		$key = $this->searchCookie($list, $source, $city, $category);
		if ($key >= 0){
			//Adiona +1 nessa ORIGEM + CIDADE + CATEGORIA
			$list[$key]['click'] = $list[$key]['click']+1;
				
			//regista a deal em cookie
			$list[$key]['deals'][] = $this->addDealCookie($deal);
		}else{
			$list[] = $this->addCookie($source, $city, $category, $deal, 1, 1);
		}
	
		//print_r($list);
	
		return array($this->getName(), $this->encodeBase64($list));
	}
	
	/**
	 * Método para codificar o Cookie
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
	 * Método para decodificar o Cookie
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
}