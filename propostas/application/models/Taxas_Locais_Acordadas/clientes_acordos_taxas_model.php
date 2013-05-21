<?php
/**
 * Clientes_Acordos_Taxas_model
 *
 * Aplica às operações de banco de dados a os clientes dos acordos de taxas 
 *
 * @package models/Taxas_locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 20/05/2013
 * @version  versao 1.0
*/
include_once APPPATH."models/Taxas_Locais_Acordadas/Interfaces/database_operations.php";

class Clientes_Acordos_Taxas_Model extends CI_Model implements Database_Operations{
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function save( Entity $acordo_cliente )
	{
		
		$dados_para_salvar = Array(
									"id_cliente" => $acordo_cliente->getIdCliente(),
									"id_acordos_taxas_locais" => $acordo_cliente->getIdAcordo()
		);
		
		return $this->db->insert("CLIENTES.clientes_x_acordos_taxas_locais_globais",$dados_para_salvar);
		
	}
	
	public function findById( Entity $bean ){}
	public function update( Entity $bean ){}
	public function delete( Entity $bean ){}
	
}//END CLASS