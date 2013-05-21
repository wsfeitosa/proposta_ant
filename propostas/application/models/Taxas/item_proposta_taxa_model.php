<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package  Taxas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 10/05/2013
 * @version  1.0
 * Classe que manipula às taxas de um item de proposta
 */
class Item_Proposta_Taxa_Model extends CI_Model{
	
	public function __construct(){
		parent::__construct();
		$this->load->model("Taxas/taxa_local");
		$this->load->model("Taxas/taxa_adicional");		
		$this->load->model("Taxas/taxa_model");
		$this->load->model("Taxas/moeda");
		$this->load->model("Taxas/moeda_model");
		$this->load->model("Taxas/unidade");
		$this->load->model("Taxas/unidade_model");
	}
	
	/**
	 * buscaTaxasDoItemDaProposta
	 *
	 * Busca às taxas cadastradas em um item de proposta no sistema
	 *
	 * @name buscaTaxasDoItemDaProposta
	 * @access public
	 * @param Item_Proposta $item
	 * @return void
	 */
	public function buscaTaxasDoItemDaProposta(Item_Proposta $item) {
		
		/** Verifica se o id do item foi definido **/
		$id_item = $item->getId();
		
		if( empty($id_item) )
		{
			throw new InvalidArgumentException("Id do item da proposta não definido para realizar a busca pelas taxas");
		}	
		
		$this->db->
				select("taxas_item_proposta.*")->
				from("CLIENTES.taxas_item_proposta")->
				where("taxas_item_proposta.id_item_proposta",$id_item);
		
		$rs = $this->db->get();
		
		if( $rs->num_rows() > 1 )
		{
									
			$item->getTarifario()->limparTaxasTarifario();
			
			/** Cria às taxas para substituir na proposta **/
			foreach( $rs->result() as $taxa )
			{
				
				/** Cria um objeto do tipo taxa de acordo com o tipo de taxa Taxa_Local ou Taxa_Adicional **/
				if( $taxa->taxa_portuaria == "S" )
				{
					$taxa_proposta = new Taxa_Local();
				}
				else
				{
					$taxa_proposta = new Taxa_Adicional();
				}		
				
				$taxa_model = new Taxa_Model();
				
				$taxa_proposta->setIdItem($id_item);
				$taxa_proposta->setId((int)$taxa->id_taxa_adicional);
				$taxa_proposta->setValor((float)$taxa->valor);
				$taxa_proposta->setValorMinimo((float)$taxa->valor_minimo);
				$taxa_proposta->setValorMaximo((float)$taxa->valor_maximo);
				
				/** Obtem o nome da taxa **/
				$taxa_model->obterNomeTaxaAdicional($taxa_proposta);
				
				/** Obtem a moeda **/
				$moeda = new Moeda();
				$moeda->setId((int)$taxa->id_moeda);
				
				$moeda_model = new Moeda_Model();
				$moeda_model->findById($moeda);
				
				$taxa_proposta->setMoeda($moeda);
				
				/** Obtém a unidade **/
				$unidade = new Unidade();
				$unidade->setId((int)$taxa->id_unidade);
				
				$unidade_model = new Unidade_Model();
				$unidade_model->findById($unidade);
				
				$taxa_proposta->setUnidade($unidade);
				
				$item->getTarifario()->adicionarNovaTaxa($taxa_proposta);
												
			}		
			
		}	
		
	}//END FUNCTION	 
	
}//END CLASS