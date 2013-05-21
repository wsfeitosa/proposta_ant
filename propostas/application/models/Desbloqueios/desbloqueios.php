<?php
/**
 * Interface Desbloqueios
 *
 * Interface que define métodos comuns para todos os objetos de desbloqueio do sistema
 *
 * @package Desbloqueios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 22/01/2013
 * @name Desbloqueios
 * @version 1.0
 */
abstract class Desbloqueios{
			
	/**
	 * Armazena o item a ser desbloqueado
	 * @var object
	 */
	private $item = NULL;
	/**
	 * Armazena o concorrente informado no desbloqueio
	 * @var Concorrente
	 */
	private $concorrente = NULL ;
	/**
	 * Aramazena o escalonamento informado no momento do desbloqueio
	 * @var Escalonamento
	 */
	private $escalonamento = NULL;
	
	public function __construct()
	{
	
	}
	
	/**
	 * Set Item
	 *
	 * Adicionar um item de desbloqueio à classe
	 *
	 * @name setItem
	 * @access public
	 * @param object
	 * @return boolean
	 */
	public function setItem( $item = NULL )
	{
	
		if( empty($item) || ! is_object($item) )
		{
			log_message('error',"Não foi possivel atribuir o item!");
			throw new Exception("Não foi possivel atribuir o item ao desbloqueio");
		}
	
		$this->item = $item;
	
		return TRUE;
	
	}
	
	/**
	 * Get Item
	 *
	 * Obtem o de desbloqueio que foi informado na classe
	 *
	 * @name getItem
	 * @access public
	 * @param
	 * @return object
	 */
	public function getItem()
	{
		return $this->item;
	}
	
	/**
	 * Set Concorrente
	 * 
	 * Adiciona um concorrente ao desbloqueio
	 * 
	 * @name setConcorrente
	 * @access public
	 * @param Concorrente
	 * @return boolean
	 */
	public function setConcorrente( Concorrente $concorrente )
	{
		$this->concorrente = $concorrente;
		
		return TRUE;
	}
	
	/**
	  * Get Concorrente
	  * 
	  * Obtém o concorrente informado no desbloqueio
	  * 
	  * @name getConcorrente
	  * @access public
	  * @param 
	  * @return Concorrente
	  */
	public function getConcorrente()
	{
		return $this->concorrente;
	}
	
	/**
	  * Set Escalonamento
	  *  
	  * Atribui uma nota de escalonamento para o desbloqueio 
	  *  
	  * @name setEscalonamento
	  * @access public
	  * @param Escalonamento
	  * @return boolean 
	  */
	public function setEscalonamento( Escalonamento $escalonamento )
	{
  	
		$this->escalonamento = $escalonamento;

		return TRUE;
	  	
	}
	
	/**
	  * Get Escalonamento
	  * 
	  * Obtém o valor do escalonamento
	  * 
	  * @name getEscalonamento
	  * @access public
	  * @param 
	  * @return Escalonamento
	  */
	public function getEscalonamento()
	{
		return $this->escalonamento;
	}
	
	/**
	  * Solicitar Desbloqueio
	  * 
	  * Faz uma solicitação de desbloqueio
	  * 
	  * @name solicitarDesbloqueio
	  * @access public
	  * @param 
	  * @return boolean
	  */
	public abstract function solicitarDesbloqueio();
		
}//END CLASS