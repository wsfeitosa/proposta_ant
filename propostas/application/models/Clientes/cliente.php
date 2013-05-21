<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Cliente
 *
 * Classe que manipula os dados da entidade Cliente no sistema
 *
 * @package Clientes
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 16/01/2013
 * @name Clientes
 * @version 1.0
 */
include APPPATH."/models/Clientes/cnpj.php";

class Cliente {
	
	private $id_cliente = NULL;
	private $razao = NULL;
	private $cnpj = NULL;
	private $contatos = Array();
	private $endereco = NULL;
	private $numero = NULL;
	private $bairro = NULL;
	private $cidade = NULL;
	private $estado = NULL;
	private $classificacao = NULL;
	
	public function __construct()
	{
		
	}
	
	/**
	  * Set Id
	  * 
	  * Atribui um id ao cliente
	  * 
	  * @name setId
	  * @access public
	  * @param int 
	  * @return boolean
	  */
	public function setId( $id = NULL )
	{
		
		if( empty($id) || ! is_integer($id) )
		{
			return FALSE;
		}	
		
		$this->id_cliente = $id;
		
		return TRUE;
		
	}
	
	/**
	  * Get Id
	  * 
	  * Obtem o id do cliente
	  * 
	  * @name getId
	  * @access public
	  * @param  
	  * @return int
	  */
	public function getId()
	{
		return (int)$this->id_cliente;
	}
	
	/**
	  * Set Razao
	  * 
	  * Atribui uma razão ao cliente
	  * 
	  * @name setRazao
	  * @access public
	  * @param string
	  * @return boolean
	  */
	public function setRazao( $razao = NULL )
	{
		if( empty($razao) )
	 	{
	 		return FALSE;
	 	}

	 	$this->razao = $razao;
	 	
	 	return TRUE;
	 	
	}
	 
	/**
	  * Get Razao
	  * 
	  * Obtem uma razão para o usuário
	  * 
	  * @name setRazao
	  * @access public
	  * @param 
	  * @return string
	  */ 
	public function getRazao()
	{
		return (string)$this->razao;
	}
	
	/**
	  * Set CNPJ
	  * 
	  * Atribui um cnpj ao cliente
	  * 
	  * @name setCNPJ
	  * @access public
	  * @param string
	  * @return boolean
	  */
	public function setCNPJ( $cnpj = NULL )
	{
		if( empty($cnpj) )
		{
			return FALSE;
		}

		$objCnpj = new CNPJ($cnpj);
		
		$objCnpj->removerLetrasAcentos();
		
		if( ! $objCnpj->validarCNPJ() )
		{
			return FALSE;
		}	
		
		$this->cnpj = $objCnpj->getCNPJ();
		
		return TRUE;
	}

	/**
	  * getCNPJ
	  * 
	  * Obtem o cnpj do cliente
	  * 
	  * @name getCNPJ
	  * @access public
	  * @param 
	  * @return string
	  */
	public function getCNPJ()
	{
		return (string)$this->cnpj;
	}
	
	/**
	  * Set Contato
	  * 
	  * Atribui um contato para o cliente
	  * 
	  * @name setContato
	  * @access public
	  * @param Contato
	  * @return boolean
	  */
	public function setContatos( Contato $contato )
	{
		$this->contatos[] = $contato;
		return TRUE;
	}
	
	/**
	  * Get Contatos
	  * 
	  * Obtem os contatos do cliente
	  * 
	  * @name getContatos
	  * @access public 
	  * @param  
	  * @return Contato
	  */
	public function getContatos()
	{
		return $this->contatos;
	}
	
	public function setEndereco( $endereco = NULL )
	{
		
		if( empty($endereco) )
		{
			return FALSE;
		}
		
		$this->endereco = $endereco;
		
		return TRUE;
		
	}
	
	public function getEndereco()
	{
		return (string)$this->endereco;
	}
	
	public function setNumero( $numero = NULL )
	{
		
		if( empty($numero) || ! is_integer($numero) )
		{
			return FALSE;
		}	
		
		$this->numero = $numero;
		
		return TRUE;
		
	}
	
	public function getNumero()
	{
		return $this->numero;		
	}
	
	public function setBairro( $bairro = NULL )
	{
		
		if( empty($bairro) )
		{
			return FALSE;
		}	
		
		$this->bairro = $bairro;
		
		return TRUE;
		
	}
	
	public function getBairro()
	{
		return (string)$this->bairro;
	}
	
	public function setCidade( Cidade $cidade )
	{
		
		$this->cidade = $cidade;
		
		return TRUE;
		 
	}
	
	public function getCidade()
	{
		return $this->cidade;	
	}
	
	public function setEstado($estado = NULL)
	{
		
		if( empty($estado) )
		{
			return FALSE;
		}
		
		$this->estado = $estado;
		
		return TRUE;
		
	}

	public function getEstado()
	{
		return $this->estado;
	}
	
	public function setClassificacao( $classificacao = NULL )
	{
		
		if( empty($classificacao) )
		{
			return FALSE;
		}	
		
		$this->classificacao = $classificacao;
		
		return TRUE;
	}
	
	public function getClassificacao()
	{
		return $this->classificacao;
	}
	
}//END CLASS