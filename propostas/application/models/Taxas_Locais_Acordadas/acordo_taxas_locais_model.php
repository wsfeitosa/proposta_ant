<?php
/**
 * Acordo_Taxas_Locais_Model
 *
 * Classe que realiza às operações de banco de dados relativas à os
 * dados do acordo de taxas locais 
 *
 * @package models/Taxas_Locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 16/05/2013
 * @version  versao 1.0
*/
include_once APPPATH."models/Taxas_Locais_Acordadas/Interfaces/database_operations.php";

class Acordo_Taxas_Locais_Model extends CI_Model implements Database_Operations {
	
	public function __construct() {
		parent::__construct();
		$this->load->model("Taxas_Locais_Acordadas/gera_numero_acordo");
		$this->load->model("Taxas_Locais_Acordadas/taxa_acordo_model");
	}
	
	/**
	 * save
	 *
	 * Salva um acordo de taxas locais
	 *
	 * @name save
	 * @access public
	 * @param Bean $acordo
	 * @return boolean
	 */ 	
	public function save( Entity $acordo )
	{
		//TODO Validar se todos os dados da entidade estão preenchidos antes de salvar			
		$numero_gerador = new Gera_Numero_Acordo();
						
		$numero_acordo = $numero_gerador->gerarNumeroAcordo();
		
		$dados_para_salvar = Array(
									"numero" => $numero_acordo,
									"sentido" => $acordo->getSentido(),
									"observacoes_internas" => $acordo->getObservacao(),
									"data_inicial" => $acordo->getInicio()->format("Y-m-d"),
									"validade" => $acordo->getValidade()->format("Y-m-d"),
									"registro_ativo" => "S",
		);
		
		$acordo_salvo = $this->db->insert("CLIENTES.acordos_taxas_locais_globais", $dados_para_salvar);
						
		if( ! $acordo_salvo )
		{
			$message = "Não foi possível salvar o acordo de taxas locais";
			log_message('error',$message);
			throw new Exception($message);
		}	
		
		$id_acordo_salvo = $this->db->insert_id();
		$acordo->setId((int)$id_acordo_salvo);
		
		/** Salva os cliente do acordo de taxas **/
		$this->load->model("Taxas_Locais_Acordadas/clientes_acordos_taxas_model");
		$this->load->model("Taxas_Locais_Acordadas/cliente_acordo_entity");
		
		foreach( $acordo->getClientes() as $cliente )
		{			
			$acordo_cliente = new Cliente_Acordo_Entity();
			$acordo_cliente->setIdAcordo($id_acordo_salvo);
			$acordo_cliente->setIdCliente((int)$cliente->getId());
			
			$acordo_cliente_model = new Clientes_Acordos_Taxas_Model();
			$acordo_cliente_model->save($acordo_cliente);
		}	
		
		/** Salvar os portos dos acordos **/
		//TODO Criar às classes para salvar os portos dos acordos
		
		/** Salva às taxas do acordo **/
		$model_taxas = new Taxa_Acordo_Model();
		
		foreach( $acordo->getTaxas() as $taxa )
		{
			$taxa->setId((int)$id_acordo_salvo);
			$model_taxas->save($taxa);
		}	
		
		return $id_acordo_salvo;
		
	}
	
	public function findById( Entity $bean ){}
	public function update( Entity $bean ){}
	public function delete( Entity $bean ){}
		
}//END CLASS