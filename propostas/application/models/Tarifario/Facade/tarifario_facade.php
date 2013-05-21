<?php
/**
 * Façade para o subsistema de tarifario
 *
 * Esta classe fornece uma interface simplificada para todas às operações
 * das classes de tarifário, criando um subsistema tarifário, que é de 
 * utilização simplificada graças ao façade, assim os controllers podem 
 * ser mais enxutos e ter menos código, tornando-se mais genéricos e reaproveitáveis
 * 
 * @package Tarifario/Facade
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 04/04/2013
 * @name Tarifario_Facade
 * @version 1.0
 */
class Tarifario_Facade extends CI_Model {
  
    public function __construct() {
        parent::__construct();
        $this->load->model("Tarifario/porto");		
		$this->load->model("Tarifario/rota");		
		$this->load->model("Taxas/taxa_local");
		$this->load->model("Taxas/moeda");
        $this->load->model("Taxas/taxa_model");
		$this->load->model("Taxas/unidade");			
		$this->load->model("Taxas/taxa_local_model");
        $this->load->model("Clientes/cliente");
        $this->load->model("Clientes/cliente_model");
        $this->load->model("Clientes/define_classificacao");
        $this->load->model("Propostas/item_proposta");
		$this->load->model("Propostas/Buscas/busca_proposta_existente");
		$this->load->model("Tarifario/Factory/concrete_factory");
		$this->load->model("Tarifario/Factory/concrete_exportacao_factory");
		$this->load->model("Tarifario/Factory/concrete_importacao_factory");
		$this->load->model("Tarifario/Factory/factory");
    }
    
    /**
     * Listar Tarifários
     * 
     * Lista todas às rotas possiveis para uma determinada rota,
     * em um determinado sentido, importação ou exportação
     * 
     * @name ListarTarifarios
     * @access public
     * @param int
     * @return Array $tarifarios
     */    
    public function ListarTarifarios( ArrayObject $dados_enviados )
    {
          
        /** 
		 * Verifica se todos os clientes são do mesmo tipo direto ou forwarder 
		 * se forem de tipos diferentes então não pode prosseguir 
		 **/			
		$cliente_model = new Cliente_Model();
		
		if( ! $cliente_model->verificarModalidadeDosClientes($dados_enviados->offsetGet('clientes')) )
		{
			show_error("Existem Clientes com diferentes classificações (Direto e Forwarder) na mesma proposta e isso não é permitido!");
		}
        
        /** Cria os objetos dos portos **/
		$porto_origem = new Porto();
		$porto_desembarque = new Porto();
		$porto_embarque = new Porto();
		$porto_destino = new Porto();
        	
		$concrete_factory = Factory::factory($dados_enviados->offsetGet("sentido"));
		
		$factory = new Concrete_Factory();
						
		/** Model dos portos **/
		$porto_model = $factory->CreatePortoModel($concrete_factory);
        
        try{
			
			$porto_origem->setUnCode($dados_enviados->offsetGet("origem"));
			$porto_embarque->setUnCode($dados_enviados->offsetGet("embarque"));
			$porto_desembarque->setUnCode($dados_enviados->offsetGet("desembarque"));
			$porto_destino->setUnCode($dados_enviados->offsetGet("destino"));
			
			$porto_model->findByUnCode($porto_origem,'origem');
			$porto_model->findByUnCode($porto_embarque,'embarque');
			$porto_model->findByUnCode($porto_desembarque,'desembarque');
			$porto_model->findByUnCode($porto_destino,'destino');	 
			
			/** Cria a rota **/		
			$rota = new Rota();
			$rota->setPortoOrigem($porto_origem);
			$rota->setPortoEmbarque($porto_embarque);
			$rota->setPortoDesembarque($porto_desembarque);
			$rota->setPortoFinal($porto_destino);
			
			$tarifario_model = $factory->CreateTarifarioModel($concrete_factory);
            
            $tarifarios = $tarifario_model->obterTarifarios($rota);
            
            /**
             * Verifica se existe alguma proposta já cadastrada para algum dos clientes
             * na mesma rota que está sendo solicitada
             */
			$finder = new Busca_Proposta_Existente();
            
            /** Cria objetos com os clientes que vão ser verificados **/
            $clientes_para_verificacao = new ArrayObject(explode(":", $dados_enviados->offsetGet("clientes")));
            
            $iterator = $clientes_para_verificacao->getIterator();
            
            (bool) $proposta_ja_cadastrada = FALSE;
            
            $taxa_model = new Taxa_Model();
            
            $mensagem_tarifarios_duplicados_encontrados = "";
            $itens_duplicados_encontrados = FALSE;
            
            while( $iterator->valid() )
            {
                
                /** Cria um novo objeto do tipo cliente para verificação **/
                $cliente = new Cliente();
                $cliente->setId((int)$iterator->current());
                
                /** Testa todos os clientes com todos os tarifários encontrados **/
                foreach ($tarifarios as $tarifario) 
                {             
                    /** Adiciona a propriedade id_item_proposta dinamicamente a todos os tarifários encontrados **/
                    $tarifario->id_item_proposta = NULL;
                    
                    $proposta_ja_cadastrada = $finder->verificarSeClienteJaPossuiPropostaValida($cliente, $tarifario);

                    /** Se já existir uma proposta cadastrada para um dos clientes então emite uma mensagem **/
                    if( $proposta_ja_cadastrada == TRUE )
                    {
                    	$itens_duplicados_encontrados = TRUE;
                    	
                    	$cliente_model->findById($cliente);
                    	
                    	$mensagem_tarifarios_duplicados_encontrados .= " {$cliente->getCNPJ()} - {$cliente->getRazao()} {$cliente->getCidade()->getNome()}";                        
                    }
                    
                }
                                              
                $iterator->next();
            }
            
            /** 
             * Se algum dos tarifários foi encontrado já cadastrado em outra proposta do mesmo cliente
             * então emite a mensagem de alerta
             */
            if( $itens_duplicados_encontrados == TRUE )
            {
            	echo "<script language='javascript'>
		            	if( ! confirm('Já existe uma proposta(s) para o(s) cliente(s) {$mensagem_tarifarios_duplicados_encontrados} para esta rota, se você salvar esta proposta, a proposta anterior desse cliente nessa rota, será cancelada! Deseja Prosseguir?') )
		            	{
		            		window.parent.document.getElementById('pop').style.display = 'none';
		            	}
		               </script>";              	         	
            }	
            
            /** Sobrescreve os valores do tarifario encontrado com os valores da proposta encontrada caso houver **/
            $iterator->rewind();
            
            while( $iterator->valid() )
            {                
                
                /** Cria um novo objeto do tipo cliente para verificação **/
                $cliente = new Cliente();
                $cliente->setId((int)$iterator->current());
                
                foreach ($tarifarios as $tarifario) 
                {
                                              
                    $id_item_proposta = $finder->verificarSeClienteJaPossuiPropostaValidaERetornaId($cliente , $tarifario);
                    
                    if( $id_item_proposta != FALSE )
                    {
                        $tarifario_diferenciado = clone $tarifario;
                        
                        $tarifario_diferenciado->id_item_proposta = $id_item_proposta;
                        
                        $item = new Item_Proposta();
                        
                        $item->setId((int)$id_item_proposta);
                        
                        /** Obtem às taxas já cadastradas que vão se sobrepor às taxas trazidas pelo tarifáro **/
                        $taxas_cadastradas = $taxa_model->retornaTaxasDaProposta($item);
                        
                        $tarifario_diferenciado->limparTaxasTarifario();
                        
                        /** Insere às novas taxas no objeto tarifário **/                        
                        $taxas_cadastradas_iterador = $taxas_cadastradas->getIterator();
                        
                        while( $taxas_cadastradas_iterador->valid() )
                        {
                            $tarifario_diferenciado->adicionarNovaTaxa($taxas_cadastradas_iterador->current());
                            
                            $taxas_cadastradas_iterador->next();
                        }    
                        
                        $tarifarios[] = $tarifario_diferenciado;
                        
                    }    
                    
                }
                
                $iterator->next();                
            } 
                       
			return $tarifarios;
										
		} catch (Exception $e) {
			echo $e->getMessage();die();
		}
        
    }        
    
    /**
     * Encontrar um tarifário pelo Id
     *  
     * Encontra um tarifário especifico pelo id do tarifário
     * 
     * @name BuscarTarifarioPeloId
     * @access public
     * @param int
     * @return boolean
     */
    public function BuscarTarifarioPeloId( $id_tarifario, $sentido, $clientes, $id_item_proposta = NULL )
    {           
        
        /**
		 * Verifica se todos os clientes são do mesmo tipo direto ou forwarder
		 * se forem de tipos diferentes então não pode prosseguir
		 **/
		$cliente_model = new Cliente_Model();
		
		if( ! $cliente_model->verificarModalidadeDosClientes($clientes) )
		{
			show_error("Existem Clientes com diferentes classificações (Direto e Forwarder) na mesma proposta e isso não é permitido!");
		}
          
        /** obtem a classificação do cliente para passar ao tarifário **/
        $clientes_selecionados = new ArrayObject(explode(":", $clientes));
        
        $cliente = new Cliente();
        
        $cliente->setId( (int)$clientes_selecionados->offsetGet(0) ); 
                     
        $cliente_model->findById($cliente);
        
        $definidor_de_classificacao = new Define_Classificacao();
        
        $classificacao_cliente = $definidor_de_classificacao->ObterClassificacao($cliente);
                        
        if( $sentido == "Tarifario_Exportacao" )
        {
            $concrete_factory = new Concrete_Exportacao_Factory();
        }
        else
        {
            $concrete_factory = new Concrete_Importacao_Factory();
        }	 	

        $factory = new Concrete_Factory();

        $tarifario_model = $factory->CreateTarifarioModel($concrete_factory);

        $tarifario = $factory->CreateTarifarioObject($concrete_factory);

        $tarifario->setId((int)$id_tarifario);

        $tarifario_model->findById( $tarifario , $classificacao_cliente );
        
        /** 
         * Verifica se foi informado algum id de item de proposta,
         * em caso positivo busca às taxas desse item e sobreescreve às taxas padrões do tarifário.
         */        
        if( ! is_null($id_item_proposta) )
        {
            
            $item = new Item_Proposta();
                        
            $item->setId((int)$id_item_proposta);
                
            $taxa_model = new Taxa_Model();
            
            /** Obtem às taxas já cadastradas que vão se sobrepor às taxas trazidas pelo tarifáro **/
            $taxas_cadastradas = $taxa_model->retornaTaxasDaProposta($item);
            
            $tarifario->limparTaxasTarifario();

            /** Insere às novas taxas no objeto tarifário **/                        
            $taxas_cadastradas_iterador = $taxas_cadastradas->getIterator();

            while( $taxas_cadastradas_iterador->valid() )
            {
                $tarifario->adicionarNovaTaxa($taxas_cadastradas_iterador->current());

                $taxas_cadastradas_iterador->next();
            }
        }    
        
        return $tarifario;
        
    }        
    
    
    
}//END CLASS

