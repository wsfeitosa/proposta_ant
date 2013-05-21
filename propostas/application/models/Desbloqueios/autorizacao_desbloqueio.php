<?php
/**
 * Class Autorizacao_Desbloqueio
 *
 * Autoriza o desbloqueio de uma taxa ou de um periodo
 *
 * @package Desbloqueios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 22/01/2013
 * @name Autorizacao_Desbloqueio
 * @version 1.0
 */
class Autorizacao_Desbloqueio extends CI_Model{
	
	/**
	 * Armazena o status do desbloqueio
	 * @var boolean
	 */
	private $status = FALSE;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function autorizarDesbloqueio( Desbloqueios $desbloqueio, $decisao = FALSE )
	{
		
	}
	
}//END CLASS