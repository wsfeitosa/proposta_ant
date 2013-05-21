<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package  Taxas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 31/01/2013
 * @version  1.0
 * Classe que contém as regras de negócio da entidade moeda
 */

class Moeda_Model extends CI_Model{

	public function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
	/**
	  * findById
	  * 
	  * Preenche um objeto Moeda fazendo uma busca pelo id
	  * 
	  * @name findById
	  * @access public
	  * @param Moeda
	  * @return void
	  */
	public function findById( Moeda $moeda )
	{
		
		$this->db->
		select("moedas.*")->
		from("FINANCEIRO.moedas")->
		where("id_moeda",$moeda->getId());
		
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{
			log_message('error',"Não foi possivel encontrar a moeda solicitada!");
			throw new Exception("Não foi possivel encontrar a moeda solicitada!");
		}	
		
		$row = $rs->row();
		
		$moeda->setMoeda($row->moeda);
		$moeda->setSigla($row->sigla);
		
	}//END FUNCTION
	
	/**
	 *  retornaTodasAsMoedas
	 *
	 * Função que retorna todas às moedas que a allink utiliza
	 *
	 * @name  retornaTodasAsMoedas
	 * @access public	 
	 * @return Array $moedas
	 */ 	
	public function retornaTodasAsMoedas() 
	{
		$this->db->
				select("id_moeda, sigla as moeda")->
				from("FINANCEIRO.moedas")->
				or_where_in("id_moeda",113)->
				or_where_in("id_moeda",42)->
				or_where_in("id_moeda",88);
		
		$rs = $this->db->get();
		
		$moedas = Array();
		
		if( $rs->num_rows() < 1 )
		{
			return $moedas;
		}	
		
		foreach( $rs->result() as $moeda )
		{
			$moedas[$moeda->id_moeda] = $moeda->moeda;
		}

		return $moedas;
		
	}
	
}//END FILE