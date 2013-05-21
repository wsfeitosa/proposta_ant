<?php
/**
 * Class Solicitacao Desbloqueio Taxa Model
 *
 * Esta classe cont�m as regras de neg�cio e os m�todos de banco de 
 * dados para manipular os desbloqueios
 *
 * @package Desbloqueios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 22/01/2013
 * @name Solicitacao_Desbloqueio_Taxa_Model
 * @version 1.0
 */
class Solicitacao_Desbloqueio_Taxa_Model extends DataMapper {
	
	public $db_params = "clientes";
	public $table = "desbloqueios_taxas";
	public $solicitacao;
	
	public function __construct()
	{				
		parent::__construct();		
	}
	
	/**
	 * Executa uma opera��o qualquer quando o objeto � criado
	 * @param boollean $from_cache
	 */
	
	public function post_model_init($from_cache = FALSE)
	{
		
	}
	
	public function setSolicitacao( Desbloqueios $solicitacao )
	{
		$this->solicitacao = $solicitacao;
	}
	
	/**
	  * Save
	  * 
	  * Salva a solicita��o no banco de dados
	  * 
	  * @name save
	  * @access public
	  * @param 
	  * @return int
	  */
	public function salvar()
	{
		
		if( empty($this->solicitacao) )
		{
			log_message('error','Nenhuma taxa informada para enviar a solicita��o');
			throw new RuntimeException('Nenhuma taxa informada para enviar a solicita��o');
		}	
		
		/** Obtem os dados do objeto taxa da solicta��o **/
		$taxa = $this->solicitacao->getItem();				
		$unidade = $taxa->getUnidade();			
		$moeda = $taxa->getMoeda();		
		$escalonamento = $this->solicitacao->getEscalonamento();
		
		$this->id_taxa_item = $taxa->getId();
		$this->id_unidade = $unidade->getId();
		$this->id_moeda = $moeda->getId();
		$this->valor = $taxa->getValor();
		$this->valor_minimo = $taxa->getValorMinimo();
		$this->valor_maximo = $taxa->getValorMaximo();
		//FIXME
		$this->id_usuario_solicitacao = 338;
		$this->data_solicitacao = date('Y-m-d H:i:s');
		$this->status = "P";
		
		return $this->save();
		
	}
	
}//END CLASS