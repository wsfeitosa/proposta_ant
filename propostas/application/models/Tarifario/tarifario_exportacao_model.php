<?php
include_once APPPATH."/models/Tarifario/porto.php";
include_once APPPATH."/models/Tarifario/porto_exportacao_model.php";
include_once APPPATH."/models/Tarifario/tarifario_exportacao.php";
include_once APPPATH."/models/Taxas/taxa_tarifario_model.php";
include_once APPPATH."/models/Taxas/taxa_local_model.php";
include_once APPPATH."/models/Tarifario/interface_tarifario_model.php";

class Tarifario_Exportacao_Model extends CI_Model implements Interface_Tarifario_Model{
		
	const sentido = 'EXP';
		
	public function obterTarifarios(Rota $rota)
	{
		
		/** Converte o un_code do porto de destino **/
		$porto_model = new Porto_Exportacao_Model();			
		
		$this->db->
				select("tarifarios_pricing.*")->
				from("FINANCEIRO.tarifarios_pricing")->
				join("FINANCEIRO.tarifarios_taxas_pricing","tarifarios_taxas_pricing.id_tarifario_pricing = tarifarios_pricing.id_tarifario_pricing")->
				where("id_place_receipt",$rota->getPortoOrigem()->getId())->
				where("id_port_loading",$rota->getPortoEmbarque()->getId())->
				where("id_place_delivery",$rota->getPortoFinal()->getId())->
				where("modulo", self::sentido)->
				where("ativo","S")->
				group_by("tarifarios_pricing.id_tarifario_pricing");
				
		$rotas_encontradas = Array();
		
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{
			return $rotas_encontradas;
		}	
					
		foreach( $rs->result() as $tarifarios )
		{			
						
			$tarifario = new Tarifario_Exportacao();
			
			$tarifario->setId((int)$tarifarios->id_tarifario_pricing);
			$tarifario->setInicio(new DateTime());
			$tarifario->setValidade(new DateTime());
			$tarifario->setRota($rota);
			$tarifario->setSentido(self::sentido);
			
			/** Preenche o porto de desembarque (via) **/
			$porto_desembarque = $rota->getPortoDesembarque();
			$porto_desembarque->setId((int)$tarifarios->id_via);
						
			$porto_model->findById($porto_desembarque, 'desembarque');
			
			/** Obtem as taxas do tarifario **/
			$taxa_model = new Taxa_Tarifario_Model();
			
			$taxa_model->obterTaxasRotaTarifario($tarifario);
			     
			$rotas_encontradas[] = $tarifario;
		}	
		
		return $rotas_encontradas;
		
	}
	
	/**
	  * Find By Id
	  * 
	  * Busca o tarifário pelo id
	  * 
	  * @name findById
	  * @access public
	  * @param integer
	  * @return array
	  */
	public function findById( Tarifario $tarifario, $classificacao_cliente = "A" )
	{
				
		$this->db->
				select("tarifarios_pricing.*")->
				from("FINANCEIRO.tarifarios_pricing")->
				where("id_tarifario_pricing",$tarifario->getId())->		
				where("modulo",self::sentido)->
				where("ativo","S");

		$rs = $this->db->get();
				
		if( $rs->num_rows() < 1 )
		{
			return FALSE;
		}	

		$row = $rs->row();
		
		$tarifario->setId((int)$row->id_tarifario_pricing);
		$tarifario->setInicio(new DateTime());
		$tarifario->setValidade(new DateTime());		
		$tarifario->setSentido(self::sentido);
		
		/** Preenche os portos para criar a rota **/
		$porto_origem = new Porto();
		$porto_desembarque = new Porto();
		$porto_embarque = new Porto();
		$porto_destino = new Porto();
		$porto_model = new Porto_Exportacao_Model();
				
		$porto_origem->setId((int)$row->id_place_receipt);
		$porto_model->findById($porto_origem, "origem");
		
		$porto_embarque->setId((int)$row->id_port_loading);
		$porto_model->findById($porto_embarque, "embarque");
		
		$porto_desembarque->setId((int)$row->id_via);
		$porto_model->findById($porto_desembarque, "desembarque");
		
		$porto_destino->setId((int)$row->id_place_delivery);
		$porto_model->findById($porto_destino, "destino");
				
		/** Cria rota e atribui os portos **/
		$rota = new Rota();
		
		$rota->setPortoOrigem($porto_origem);
		$rota->setPortoEmbarque($porto_embarque);
		$rota->setPortoDesembarque($porto_desembarque);		
		$rota->setPortoFinal($porto_destino);
		
		$tarifario->setRota($rota);
		
		/** Formata às observações e cria o BreakDown do Tarifário **/
		$observacoes_tarifario = $this->concatenaObservacoesDoTarifario($row);
		
		$tarifario->setObservacao($observacoes_tarifario);

		/** Obtem as taxas do tarifario **/
		$taxa_model = new Taxa_Tarifario_Model();
		
		$taxa_model->obterTaxasRotaTarifario($tarifario);
		
		/** Busca às taxas locais da rota **/
		$taxas_locais_model = new Taxa_Local_Model();
		
		$taxas_locais = $taxas_locais_model->ObterTaxasLocais("EXP","LCL",$classificacao_cliente,$porto_origem->getId());
		
		/** Insere as taxas locais no tariário **/
		foreach( $taxas_locais as $taxa_local )
		{
			$tarifario->adicionarNovaTaxa($taxa_local);			
		}	
		                
		return $tarifario;
		
	}//END FUNCTION
	
	/**
	 * concatenaObservacoesDoTarifario
	 *
	 * Concatena às observações do tarifário para enviar a tela
	 *
	 * @name concatenaObservacoesDoTarifario
	 * @access public
	 * @param DataMapper $this
	 * @return string $observacao_concatenada;
	 */
	protected function concatenaObservacoesDoTarifario( stdClass $tarifario  )
	{
	
		$obs_tarifario = $tarifario->obs . "\n" . $tarifario->obs_origem . "\n" .
				$tarifario->obs_porto_embarque . "\n" . $tarifario->obs_via . "\n" .
				$tarifario->obs_pais_destino . "\n" . $tarifario->obs_destino . "\n" .
				$tarifario->obs_via_adicional;
		
		$disallowed_chars = Array("/", "/\/", "-","!");
		
		$obs_tarifario = str_replace($disallowed_chars, " ", utf8_encode($obs_tarifario));
		
		return $obs_tarifario;
	
	}
        
}//END CLASS