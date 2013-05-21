<?php
/**
 * Taxa_Acordo_Model
 *
 * Aplica às operações de banco de dados às taxas dos acordos 
 *
 * @package models/Taxas_Locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 20/05/2013
 * @version  versao 1.0
*/
include_once APPPATH."models/Taxas_Locais_Acordadas/Interfaces/database_operations.php";

class Taxa_Acordo_Model extends CI_Model implements Database_Operations {
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	/**
	 * save
	 *
	 * Salva uma taxa local de um acordo de taxas locais globais
	 *
	 * @name save
	 * @access public
	 * @param $taxa Entity
	 * @return boolean
	 */ 	
	public function save(Entity $taxa)
	{
		
		$dados_para_salvar = Array(
									"id_taxa_adicional" => $taxa->getId(),
									"id_unidade" => $taxa->getUnidade()->getId(),
									"id_moeda" => $taxa->getMoeda()->getId(),
									"valor" => $taxa->getValor(),
									"valor_minimo" => $taxa->getValorMinimo(),
									"valor_maximo" => $taxa->getValorMaximo(),
									"id_acordos_taxas_locais" => $taxa->getIdItem()
		);
		
		return $this->db->insert("CLIENTES.taxas_x_acordos_taxas_locais_globais",$dados_para_salvar);
						
	}
	
	public function findById( Entity $taxa ){}
	public function update( Entity $taxa ){}
	public function delete( Entity $taxa ){}
	
}