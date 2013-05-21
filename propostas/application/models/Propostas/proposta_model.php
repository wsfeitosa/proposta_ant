<?php
if( ! isset($_SESSION['matriz']) )
{    
    //session_start(); //FIXME descomentar o session start quando estiver pronto, pois está dando problema no phpunit
}    
/**
* @package  propostas
* @author Wellington Feitosa <wellington.feitosao@allink.com.br>
* @copyright Allink Transporte Internacionais LTDA. - 19/02/2013
* @version  1.0
* Classe que controla às regras de negócio das propostas cotação no sistema
*/
class Proposta_Model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->model("Clientes/cliente");
        $this->load->model("Clientes/contato");
        $this->load->model("Clientes/cliente_model");
        $this->load->model("Clientes/contato_model");
        $this->load->model("Propostas/proposta_cotacao");
        include_once APPPATH.'/models/Propostas/item_proposta.php';
        include_once APPPATH.'/models/Propostas/item_proposta_model.php';
        $this->load->model("Tarifario/porto");
        $this->load->model("Tarifario/rota");               
        $this->load->model("Taxas/taxa_local");
        $this->load->model("Taxas/moeda");
        $this->load->model("Taxas/unidade");
        $this->load->model("Taxas/serializa_taxas");  
        $this->load->model("Email/email_model");
        $this->load->model("Tarifario/Factory/concrete_factory");
        $this->load->model("Tarifario/Factory/concrete_importacao_factory");
        $this->load->model("Tarifario/Factory/concrete_exportacao_factory");
        $this->load->model("Propostas/Factory/proposta_factory");
    }
    
    //TODO quando salvar às propostas verificar às datas de validade dos itens
    public function salvarProposta()
    {
    	try{
            $proposta = Proposta_Factory::factory($this->input->post('tipo_proposta'));
    	}catch (Exception $e) {
    		log_message('error',$e->getMessage());
    		show_error($e->getTraceAsString());
    	}
    	
        $proposta->setNumero($this->gerarNumero($this->input->post('tipo_proposta')));
        
        $proposta->setSentido($this->input->post('sentido'));
        
        $proposta->setTipoProposta($this->input->post('tipo_proposta'));
        
        $cliente_model = new Cliente_Model();
                
        /** cria os objetos do tipo cliente **/
        foreach ( $this->input->post("clientes_selecionados") as $cliente_selecionado )
        {
            $cliente = new Cliente();
            
            $cliente->setId((int)$cliente_selecionado);
            
            /** Adiciona o cliente a proposta **/
            $proposta->adicionarNovoCliente($cliente);
        }    
        
        /** Cria os objetos do tipo contato **/
        $contatos_da_proposta = Array();
        $contato_model = new Contato_Model();
        
        if( is_array($this->input->post('contatos_para_selecionados')) )
        {    
        
            foreach ($this->input->post('contatos_para_selecionados') as $contato_selecionado) 
            {
                $contato = new Contato();

                $contato->setId((int)$contato_selecionado);

                /** O obtem o email do contato **/
                $contato_model->findById($contato);

                $proposta->adicionarNovoEmail($contato->getEmail());

            }
        
        }
        
        /** Salva os emails avulsos inseridos na proposta **/
        if( is_array($this->input->post('contatos_cc_selecionados')) )
        {    
            foreach ($this->input->post('contatos_cc_selecionados') as $email_cc)
            {
                $email = new Email();
                $email->setEmail($email_cc);
                $proposta->adicionarNovoEmail($email);
            }
        }
        
        /** Inclui os itens na proposta, recuperados da sessão **/
        if( count($_SESSION['itens_proposta']) < 1 )
        {
            log_message('error','Não foi possível recuperar os itens da sessão');
            show_error('Não foi possível salvar os itens da proposta!');
        }
        
        $factory = new Concrete_Factory();
		
        $concrete_factory = new Concrete_Exportacao_Factory();
        
        if( $proposta->getSentido() == "IMP" )
        {
        	$concrete_factory = new Concrete_Importacao_Factory();
        }	
        
        $tarifario_model = $factory->CreateTarifarioModel($concrete_factory);
        
        $serializador_de_taxas = new Serializa_Taxas();
        
        if( ! isset($_SESSION['itens_proposta']) || count($_SESSION['itens_proposta']) < 1 )
        {
            $msg = "Impossível recuperar os itens da sessão no momento de salvar. Proposta{$proposta->getNumero()}";
            log_message('error',$msg);
            show_error($msg);exit;
        }    
        
        foreach( $_SESSION['itens_proposta'] as $item )
        {
            
            $tarifario = $factory->CreateTarifarioObject($concrete_factory);
            $tarifario->setId((int)$item['id_tarifario']);
                        
            /** Carrega às informações do tarifário selecionado na proposta **/
            $tarifario_model->findById($tarifario);
            
            /** 
             * Remove às taxas padrões trazidas pelo model do tarifário,
             * para preencher com os dados da taxas informadas na proposta  
             */
            $tarifario->limparTaxasTarifario();
            
            /** converte a string da sessão que contém às taxas em objetos **/
            $taxas_locais = $serializador_de_taxas->deserializaTaxasProposta($item['taxas_locais'], "Taxa_Local");
            
            $frete_adicionais = $serializador_de_taxas->deserializaTaxasProposta($item['frete_adicionais'], "Taxa_Adicional");
            
            /** Atribui às taxas a cada um dos respctivos itens **/
            foreach( $taxas_locais as $taxa_local )
            {
                $tarifario->adicionarNovaTaxa($taxa_local);
            }
            
            foreach ($frete_adicionais as $taxa_adicional) 
            {
                $tarifario->adicionarNovaTaxa($taxa_adicional);
            }
            
            $data_inicio_acordo = new DateTime(date('Y-m-d'));//FIXME verificar se a data de inicio será realmente essa
            $validade_acordo = new DateTime($item['validade']);
            
            $item_proposta = new Item_Proposta($tarifario);//
            
            
            $item_proposta->setInicio(date('Y-m-d'));
            $item_proposta->setValidade($validade_acordo->format('Y-m-d'));
            $item_proposta->setCc((bool)$item['cc']);
            $item_proposta->setPp((bool)$item['pp']);
            $item_proposta->setCubagem($item['cubagem']);
            $item_proposta->setPeso($item['peso']);
            $item_proposta->setVolumes($item['volumes']);
            $item_proposta->setMercadoria($item['mercadoria']);
            $item_proposta->setObservacaoCliente($item['observacao_cliente']);
            $item_proposta->setObservacaoInterna($item['observacao_interna']);
                       
            $proposta->adicionarNovoItem($item_proposta);
        }  
        
        /** Salva a proposta **/
        $this->salvar($proposta);
        
        /** Salva os clientes da proposta **/
        $cliente_model->salvarClienteProposta($proposta);
        
        /** Salva os itens da proposta **/
        $item_proposta_model = new Item_Proposta_Model();
        $item_proposta_model->salvarItens($proposta);
        
        /** Salva os emails da proposta **/
        $email_model = new Email_Model();        
        $email_model->salvarEmail($proposta);
        
        /** Destroi os itens que estão salvos na sessão do PHP **/
        unset($_SESSION['itens_proposta']);
        
        return $proposta;
        
    }//END FUNCTION        
    
    /**
     * salvar
     * 
     * Salva os dados referentes a proposta na tabela CLIENTES.propostas 
     * 
     * @name salvar
     * @access private
     * @param $proposta Proposta
     * @return boolean
     */
    protected function salvar( Proposta $proposta )
    {
       
        $dados_para_salvar = Array(
                                    'numero_proposta' => $proposta->getNumero(),
                                    'sentido' => $proposta->getSentido(),
                                    'enviada' => FALSE,
                                    'id_usuario_inclusao' => $_SESSION['matriz'][7],
                                    'tipo_proposta' => $proposta->getTipoProposta(),
                                    'data_inclusao' => date('Y-m-d H:i:s')
        );
        
       $rs = $this->db->insert("CLIENTES.propostas",$dados_para_salvar);
       
       $proposta->setId((int)$this->db->insert_id());
       
       return $rs;
       
    }        
    
    /**
     * gerarNumero
     * 
     * Gera o numero da proposta
     * 
     * @name gerarNumero
     * @access private     
     * @return string
     */
    public function gerarNumero( $tipo_proposta )
    {
        
        $this->db->
        		select("propostas.numero_proposta")->
                from("CLIENTES.propostas")->
                order_by("propostas.id_proposta","desc")->
                limit(1);
        
        $rs = $this->db->get();
        
        if( $rs->num_rows() < 1 )
        {
            //log_message('error',"Impossivel gerar a numeração da proposta");
            //show_error("Impossivel gerar a numeração da proposta");
            $referencia = "PC051310SP00000";
        }
        else
       { 	
       		$referencia = $rs->row()->numero_proposta;
       	}
       	
        //ANO DA ULTIMA COTACAO
        $ano = substr($referencia,4,2);
        //ANO ATUAL
        $ano_atual = date("y");
        
        if($ano == $ano_atual)
        {
            $numero_base = substr($referencia,8,5);
        }
        else
        {
            $numero_base = "00000";
        }               
                
        $sigla = $this->retornaSiglaDaNumeracaoDaProposta($tipo_proposta);
        
        $numero_referencia = $sigla.date("my").$_SESSION["matriz"][1].sprintf("%05d",($numero_base + "1"))."00";      
        
        return $numero_referencia;
        
    }//END FUNCTION      
    
    /**
     * buscarPropostaPorId
     * 
     * busca uma proposta cadastrada pelo id da proposta
     * 
     * @name buscarPropostaPorId
     * @access public
     * @param Proposta $proposta
     * @return Proposta
     */
    
    public function buscarPropostaPorId(Proposta $proposta)
    {
    	
        /** verifica se o id da proposta foi informado **/
        $id_proposta = $proposta->getId();
        
        if( empty($id_proposta) )
        {
            log_message('error',"Impossivel realizar a consulta, o id da proposta não foi definido corretamente!");
            show_error("Impossivel realizar a consulta, o id da proposta não foi definido corretamente!");
        }    
        
        $this->db->
                select("propostas.*")->
                from("CLIENTES.propostas")->
                where("propostas.id_proposta", $proposta->getId());
        
        $rs = $this->db->get();        
        
        if( $rs->num_rows() < 1 )
        {
            log_message('error',"A proposta com o Id : ".$proposta->getId()." não pode ser recuperada!");
            throw new UnexpectedValueException("A proposta com o Id : ".$proposta->getId()." não pode ser recuperada!");
        }    
        
        $proposta_encontrada = $rs->row();
        
        $proposta->setId( (int) $proposta_encontrada->id_proposta );
        $proposta->setSentido( (string) $proposta_encontrada->sentido );
        $proposta->setNumero( (string) $proposta_encontrada->numero_proposta );
        $proposta->setTipoProposta( (string) $proposta_encontrada->tipo_proposta );
        
        /** Recupera os itens das propostas **/
        $item_model = new Item_Proposta_Model();
        
        $item_model->buscarItensPorIdDaProposta($proposta);
        
        /** Busca os clientes **/
        $cliente_model = new Cliente_Model();
        
        $cliente_model->findByIdDaProposta($proposta);        
       
        /** Busca os emails adicionados a proposta **/
        $this->load->model("Email/email_model");
        
        $email_model = new Email_Model();
        
        $email_model->buscaEmailPeloIdDaProposta($proposta);
        
        return $proposta;
    }
     
    /**
      * preparaDadosDaPropostaParaView
      * 
      * Tranforma os dados da proposta para enviar a view
      * 
      * @name preparaDadosDaPropostaParaView
      * @access public
      * @param Proposta $proposta 
      * @return array $proposta_formatada
      */ 
    public function serializaDadosDaPropostaParaView(Proposta $proposta)
    {
        
        $dados_serializados = Array();
        
        foreach ($proposta->getItens() as $item) 
        {
            
            $dados_serializados[$item->getId()] = $item->getTarifario()->getRota()->getPortoOrigem()->getNome() . " - " . 
                                                  $item->getTarifario()->getRota()->getPortoEmbarque()->getNome() . " - " .
                                                  $item->getTarifario()->getRota()->getPortoDesembarque()->getNome() . " - " .
                                                  $item->getTarifario()->getRota()->getPortoFinal()->getNome();
        }
    	
        return $dados_serializados;
        
    }//END FUNCTION
    
    /**
     * alterarProposta
     * 
     * Altera od dados de uma proposta já existente
     * 
     * @name alterarProposta
     * @access public
     * @param Proposta $proposta
     * @return boolean
     */
    public function alterarProposta() 
    {
        
        /** Cria o objeto necessario para atender a requisicao **/
        $this->load->model("Propostas/".$this->input->post('tipo_proposta'));
        
        $class_name = ucwords($this->input->post('tipo_proposta'));
        
        $proposta = new $class_name();
               
        $proposta->setId($this->input->post('id_proposta'));    
        $proposta->setSentido($this->input->post('sentido'));
        $proposta->setNumero($this->buscarNumeroDaPropostaPeloId($proposta));        
        
        $dados_para_alteracao['enviada'] = FALSE; //FIXME aki tera de ser informado se a proposta ja foi ou não enviada
        $dados_para_alteracao['id_usuario_alteracao'] = $_SESSION['matriz'][7];
        $dados_para_alteracao['data_alteracao'] = date('Y-m-d H:i:s');
        
        $this->db->where("propostas.id_proposta",$proposta->getId());
        $this->db->update("CLIENTES.propostas",$dados_para_alteracao);
                        
        $cliente_model = new Cliente_Model();
                
        /** cria os objetos do tipo cliente **/
        foreach ( $this->input->post("clientes_selecionados") as $cliente_selecionado )
        {
            $cliente = new Cliente();
            
            $cliente->setId((int)$cliente_selecionado);
            
            /** Adiciona o cliente a proposta **/
            $proposta->adicionarNovoCliente($cliente);
        }    
        
        /** Cria os objetos do tipo contato **/
        $contatos_da_proposta = Array();
        $contato_model = new Contato_Model();
        
        if( is_array($this->input->post('contatos_para_selecionados')) )
        {    
        
            foreach ($this->input->post('contatos_para_selecionados') as $contato_selecionado) 
            {
                $contato = new Contato();

                $contato->setId((int)$contato_selecionado);

                /** O obtem o email do contato **/
                $contato_model->findById($contato);

                $proposta->adicionarNovoEmail($contato->getEmail());

            }
        
        }
        
        /** Salva os emails avulsos inseridos na proposta **/
        if( is_array($this->input->post('contatos_cc_selecionados')) )
        {    
            foreach ($this->input->post('contatos_cc_selecionados') as $email_cc)
            {
                $email = new Email();
                $email->setEmail($email_cc);
                $proposta->adicionarNovoEmail($email);
            }
        }
        
        /** Inclui os itens na proposta, recuperados da sessão **/        
        if( ! isset($_SESSION['itens_proposta']) )
        {
            log_message('error','Não foi possivel recuperar os itens da proposta para realizar a alteração! Proposta: '.$proposta->getId());
            show_error('Não foi possivel recuperar os itens da proposta para realizar a alteração!');
        }         
        
        $class_tarifario_model = "Tarifario_Exportacao_Model";
        $class_tarifario = "Tarifario_Exportacao";
        
        if( $proposta->getSentido() == "IMP" )
        {
            $class_tarifario_model = "Tarifario_Importacao_Model";
            $class_tarifario = "Tarifario_Importacao";
        }    
        
        $tarifario_model = new $class_tarifario_model();
                        
        $serializador_de_taxas = new Serializa_Taxas();
        
        foreach( $_SESSION['itens_proposta'] as $item )
        {
            
            $tarifario = new $class_tarifario();
            $tarifario->setId((int)$item['id_tarifario']);
                        
            /** Carrega às informações do tarifário selecionado na proposta **/
            $tarifario_model->findById($tarifario);
            
            /** 
             * Remove às taxas padrões trazidas pelo model do tarifário,
             * para preencher com os dados da taxas informadas na proposta  
             */
            $tarifario->limparTaxasTarifario();
            
            /** converte a string da sessão que contém às taxas em objetos **/
            $taxas_locais = $serializador_de_taxas->deserializaTaxasProposta($item['taxas_locais'], "Taxa_Local");
            
            $frete_adicionais = $serializador_de_taxas->deserializaTaxasProposta($item['frete_adicionais'], "Taxa_Adicional");
            
            /** Atribui às taxas a cada um dos respctivos itens **/
            foreach( $taxas_locais as $taxa_local )
            {
                $tarifario->adicionarNovaTaxa($taxa_local);
            }
            
            foreach ($frete_adicionais as $taxa_adicional) 
            {
                $tarifario->adicionarNovaTaxa($taxa_adicional);
            }
            
            $item_proposta = new Item_Proposta($tarifario);
            $item_proposta->setInicio(date('Y-m-d'));
            $item_proposta->setValidade($item['validade']);
            $item_proposta->setCc((bool)$item['cc']);
            $item_proposta->setPp((bool)$item['pp']);
            $item_proposta->setCubagem($item['cubagem']);
            $item_proposta->setPeso($item['peso']);
            $item_proposta->setVolumes($item['volumes']);
            $item_proposta->setMercadoria($item['mercadoria']);
            $item_proposta->setObservacaoCliente($item['observacao_cliente']);
            $item_proposta->setObservacaoInterna($item['observacao_interna']);
            
            /** Adiciona o id do item da proposta se for um item já existente **/
            if( ! is_null($item['id_item']) )
            {
                 $item_proposta->setId($item['id_item']);
            }    
            
            $proposta->adicionarNovoItem($item_proposta);
        }
                      
        /** Exclui os clientes que estão relacionados na proposta antes de salva-la **/
        $cliente_model->excluiClientesPeloIdDaProposta($proposta);
        
        /** Salva os clientes da proposta **/
        $cliente_model->salvarClienteProposta($proposta);
        
        /** Salva os itens da proposta **/
        $item_proposta_model = new Item_Proposta_Model();
        $item_proposta_model->salvarItens($proposta);
                        
        /** Salva os emails da proposta **/
        $email_model = new Email_Model(); 
        
        /** Exclui os emails da proposta antes de adicionar os novos emails após a alteração **/
        $email_model->excluirEmailsDaPropostaPeloIdDaProposta($proposta);
        
        $email_model->salvarEmail($proposta);
        
        /** Destroi os itens que estão salvos na sessão do PHP **/
        unset($_SESSION['itens_proposta']);
        
        return $proposta;       
                      
    }//END FUNCTION
    
    /**
     * buscarNumeroDaPropostaPeloId
     * 
     * Retorna o numero da proposta fazendo a busca pelo Id
     * 
     * @name buscarNumeroDaPropostaPeloId
     * @access public
     * @param Proposta $proposta
     * @return string $numero
     */
    public function buscarNumeroDaPropostaPeloId(Proposta $proposta)
    {
        
         /** verifica se o id da propostan foi informado **/
        $id_proposta = $proposta->getId();
        
        if( empty($id_proposta) )
        {
            log_message('error',"Impossivel realizar a consulta, o id da proposta não foi definido corretamente!");
            show_error("Impossivel realizar a consulta, o id da proposta não foi definido corretamente!");
        }
        
        $this->db->
                select("propostas.numero_proposta")->
                from("CLIENTES.propostas")->
                where("propostas.id_proposta",$id_proposta);
        
        $rs = $this->db->get();
                        
        if( $rs->num_rows() < 1 )
        {
            throw new RuntimeException("Impossivel encontrar o número da proposta solicitada");
        }    
        
        $numero = $rs->row()->numero_proposta;
        
        return $numero;
        
    }//END FUNCTION
    
    /**
     * retornaSiglaDaNumeracaoDaProposta
     *
     * Retorna a sigla correta para ser utilizada na geração do número da proposta.
     *
     * @name retornaSiglaDaNumeracaoDaProposta
     * @access protected
     * @param string $tipo_de_proposta
     * @return string $sigla
     */
    protected function retornaSiglaDaNumeracaoDaProposta( $tipo_de_proposta )
    {
    	
    	switch( $tipo_de_proposta )
    	{
    		case "proposta_cotacao":
    			return "PC";
    		break;

    		case "proposta_tarifario":
    			return "PT";
    		break;
    		
    		case "proposta_especial":
    			return "PE";
    		break;
    		
    		case "proposta_spot":
    			return "PS";
    		break;
    		
    		case "proposta_nac":
    			return "NC";
    		break;
    		
    		default:
    			$error_message = "Impossível gerar o número da proposta, tipo de proposta desconhecido!";
    			log_message('erros',$error_message);
    			show_error($error_message);
    		
    	}
    	
    }
    
}//END CLASS


