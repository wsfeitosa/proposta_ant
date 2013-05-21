<?php
/**
 * Acordos_Taxas_Facade
 *
 * Esta classe é Façade que fornece um conjuntos de métodos simplificados
 * para ser usados nos controllers da aplição 
 *
 * @package models/Taxas_Locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 20/05/2013
 * @version  versao 1.0
*/
class Acordos_Taxas_Facade extends CI_Model {
	
	public function __construct() 
	{
		parent::__construct();		
		$this->load->model("Taxas_Locais_Acordadas/acordo_taxas_locais_model");
		$this->load->model("Taxas_Locais_Acordadas/acordo_taxas_entity");
	}
	
	/**
	 * salvarAcordoTaxasLocais
	 *
	 * salva um acordo de taxas locais
	 *
	 * @name salvarAcordoTaxasLocais
	 * @access public
	 * @param Array $post 
	 * @return int $id_acordo
	 */ 	
	public function salvarAcordoTaxasLocais( Array $post )
	{
		$this->load->model("Clientes/cliente");
		$this->load->model("Tarifario/porto");
		$this->load->model("Taxas_Locais_Acordadas/conversor_taxas");
		
		$entity = new Acordo_Taxas_Entity();
		
		$entity->setSentido($post['sentido']);
		
		$entity->setObservacao($post['observacao_interna']);
		
		/** Data de inicio do acordo **/		
		$entity->setInicio(new DateTime($post['inicio']));
		/** Validade do acordo **/
		$entity->setValidade(new DateTime($post['validade']));	
		
		/** Cria os objetos do tipo porto que serão passados ao entity **/
		foreach( $post['portos_selecionados'] as $porto_selecionado )
		{
			$porto = new Porto();
			$porto->setId($porto_selecionado);
			$entity->setPortos($porto);
		}	
				
		/** Cria os objetos do tipo cliente para passar ao entity **/
		foreach( $post['clientes_selecionados'] as $cliente_selecionado )
		{
			$cliente = new Cliente();
			$cliente->setId($cliente_selecionado);
			$entity->setClientes($cliente);
		}	
		
		/** Deserializa e cria os objetos do tipo Taxa_Adicional **/
		$conversor = new Conversor_Taxas();
		
		foreach( $post['taxas_selecionadas'] as $taxa_selecionada )
		{			
			$taxa_adicional = $conversor->deserializaTaxa($taxa_selecionada);
			
			$entity->setTaxas($taxa_adicional);			
		}	
		
		$acordo_model = new Acordo_Taxas_Locais_Model();
		
		$id_acordo = $acordo_model->save($entity);
		
		$entity->setId((int)$id_acordo);
				
		return $id_acordo;		
	}
	
}//END CLASS