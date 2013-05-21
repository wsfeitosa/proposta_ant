<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if( ! isset($_SESSION) )
{    
    session_start();
} 
 
class Propostas extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->helper(Array("form","html","url"));
		$this->load->model("Propostas/proposta_model");
		$this->load->model("Adaptadores/array_conversor");     
        $this->load->model("Propostas/Factory/proposta_factory");
	}

	public function index()
	{
				
		$header['form_title'] = 'Scoa - Propostas';
		$header['form_name'] = 'NOVA PROPOSTA';
		$header['css'] = '';
		$header['js'] = load_js(array('nova_proposta/nova_proposta.js'));
		
		$data['tipos_propostas'] = Array(
											0 => "Selecione",
											"Proposta_Cotacao" => "Proposta Cotação",
											"Proposta_Tarifario" => "Proposta Tarifario",	
											"Proposta_Spot" => "Proposta Spot",
											"Proposta_Especial" => "Proposta Especial",
											"Proposta_NAC" => "Proposta NAC"	
		);
		
		$data['sentidos'] = Array(
									0 => "Selecione",
									"IMP" => "Importação",
									"EXP" => "Exportação"		
		);
		
		$footer['footer'] = ""; 
                		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("nova_proposta",$data);
		$this->load->view("Padrao/footer_view",$footer);		
	}

	public function nova_proposta()
	{
		/** 
		 * Destroi a variável de sessão armazena os itens das propostas,
		 * sempre que uma nova proposta é criada, com isso apenas uma proposta poderá 
		 * ser aberta por vez.
		 */	
		if( isset($_SESSION['itens_proposta']) )
		{
			unset($_SESSION['itens_proposta']);	
		}		
						
		$header['form_title'] = 'Scoa - Propostas';
		$header['css'] = '';
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/gravar.jpg', 'id' => 'salvar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
		
		$footer['footer'] = $imagens;
		
		$header['form_name'] = strtoupper(str_replace("_", " ", $this->input->post("tipo_proposta")));
		$header['js'] = load_js(array('propostas/'.strtolower($this->input->post("tipo_proposta")).'.js','jquery.price_format.1.7.js'));
						
		$data["sentido"] = $this->input->post("sentido");
			
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("propostas/".strtolower($this->input->post("tipo_proposta")),$data);
		$this->load->view("Padrao/footer_view",$footer);		
	}
    
    public function salvar()
    {
        /** valida os dados do form **/
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('sentido', 'Sentido', 'required');
        $this->form_validation->set_rules('clientes_selecionados', 'Clientes Selecionados', 'required');
        $this->form_validation->set_rules('rotas_adicionadas', 'Rotas Selecionadas', 'required');
        
        if ($this->form_validation->run() == FALSE)
		{
			show_error(validation_errors());
		}
		
        /* @var $proposta type Proposta */
        $proposta = $this->proposta_model->salvarProposta();   
                  
        redirect(base_url()."/index.php/propostas/propostas/consultar/". $proposta->getId() ."/" . strtolower(get_class($proposta)));
                     
    }        
	
    public function consultar( $id_proposta = NULL, $tipo_proposta = NULL )
    {
    	       
        if( is_null($id_proposta) || is_null($tipo_proposta) )
        {
            show_error("Id ou o tipo da proposta informado é invalido para efetuar a consulta!");
        }    
                                
        /** Carrega a classe correta de acordo com tipo de proposta **/  
        $nome_do_arquivo = strtolower($tipo_proposta);
        
        $proposta = Proposta_Factory::factory($nome_do_arquivo);
        
        $proposta->setId((int) $id_proposta);     
        
        $proposta_model = new Proposta_Model();
        
        try {
            
            $proposta_model->buscarPropostaPorId($proposta);
            
        } catch (UnexpectedValueException $uneExp) {
            
            log_message('error',$uneExp->getTraceAsString());
            show_error($uneExp->getMessage());
        
        } catch (InvalidArgumentException $e) {
            
            log_message('error',$e->getTraceAsString());
            show_error($e->getMessage());
            
        } catch (Exception $e) {
            
            log_message('error',$e->getTraceAsString());
            show_error($e->getMessage());
            
        }
        
        $header['form_title'] = 'Scoa - Propostas';
		$header['css'] = '';
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/alterar.gif', 'id' => 'alterar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
		
		$footer['footer'] = $imagens;
		
		/** Seleciona o tipo de tela que deverá ser carregado **/
		switch ($nome_do_arquivo)
		{
			case "proposta_cotacao":				
				$header['form_name'] = 'PROPOSTA COTAÇÃO';                
				$header['js'] = load_js(array('propostas/proposta_cotacao_consulta.js'));				
			break;

			case "proposta_tarifario":
				$header['form_name'] = 'PROPOSTA TARIFARIO';
				$header['js'] = load_js(array('propostas/proposta_tarifario_consulta.js'));				
			break;
			
			case "proposta_spot":
				$header['form_name'] = 'PROPOSTA SPOT';
				$header['js'] = load_js(array('propostas/proposta_spot_consulta.js'));				
			break;
			
			case "proposta_especial":
				$header['form_name'] = 'PROPOSTA ESPECIAL';
				$header['js'] = load_js(array('propostas/proposta_especial_consulta.js'));				
			break;
			
			case "proposta_nac":
				$header['form_name'] = 'PROPOSTA NAC';
				$header['js'] = load_js(array('propostas/proposta_nac_consulta.js'));				
			break;
        
            default :
                show_error("Não foi possível encontrar o arquivo javascript referente ao contexto atual!");
				 	
		}//END SWITCH		
		
		$data["sentido"] = $proposta->getSentido();
        $data["proposta"] = $proposta;
		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("propostas/".strtolower($tipo_proposta."_consulta"),$data);
		$this->load->view("Padrao/footer_view",$footer);     
                               
    }//END FUNCTION        
    
    public function alterar()
    {
    	
    	$id_proposta = $this->input->post('id_proposta');
    	$tipo_proposta = $this->input->post('tipo_proposta');
    	
    	if( empty($id_proposta) )
    	{
    		log_message('error','Não foi informado o Id da proposta para carregar o formulário de alteração!');
    		show_error('Não foi informado o Id da proposta para carregar o formulário de alteração!');
    	}	
    	
    	$nome_do_arquivo = strtolower($tipo_proposta);
    	$nome_da_classe = ucwords($tipo_proposta);
    	
    	/** Carrega a classe correta de acordo com tipo de proposta **/
    	$this->load->model("Propostas/" . $nome_do_arquivo);
    	
    	$proposta = new $nome_da_classe();
    	
    	$proposta->setId((int) $id_proposta);
    	
    	$proposta_model = new Proposta_Model();
        
        unset($_SESSION['itens_proposta']);
    	
    	try {
    	
    		$proposta_model->buscarPropostaPorId($proposta);
            $proposta_serializada = $proposta_model->serializaDadosDaPropostaParaView($proposta);
            
            /** Iclui os itens da proposta na sessão do PHP **/
            $this->load->model("Propostas/item_proposta_model");
            
            $item_proposta_model = new Item_Proposta_Model();
            
            foreach ($proposta->getItens() as $item) 
            {
                $item_proposta_model->incluirItemDaPropostaNaSessao($item);
            }            
    	
    	} catch (UnexpectedValueException $uneExp) {
    	
    		log_message('error',$uneExp->getTraceAsString());
    		show_error($uneExp->getMessage());
    	
    	} catch (InvalidArgumentException $e) {
    	
    		log_message('error',$e->getTraceAsString());
    		show_error($e->getMessage());
    	
    	} catch (Exception $e) {
    	
    		log_message('error',$e->getTraceAsString());
    		show_error($e->getMessage());
    	
    	}
    	
    	$header['form_title'] = 'Scoa - Propostas';
    	$header['css'] = '';
    	
    	$imagens = "";
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0)).'</a>';
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/salvar_registro.jpg', 'id' => 'salvar' , 'border' => 0)).'</a>';
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
    	
    	$footer['footer'] = $imagens;
    	
    	/** Seleciona o tipo de tela que deverá ser carregado **/
    	switch ($nome_do_arquivo)
    	{
    		case "proposta_cotacao":
    			$header['form_name'] = 'PROPOSTA COTAÇÃO';
    			$header['js'] = load_js(array('propostas/proposta_cotacao.js','jquery.price_format.1.7.js'));
    			break;
    	
    		case "proposta_tarifario":
    			$header['form_name'] = 'PROPOSTA TARIFARIO';
    			$header['js'] = load_js(array('nova_proposta/proposta_tarifario.js'));
    			break;
    				
    		case "proposta_spot":
    			$header['form_name'] = 'PROPOSTA SPOT';
    			$header['js'] = load_js(array('nova_proposta/proposta_spot.js'));
    			break;
    				
    		case "proposta_especial":
    			$header['form_name'] = 'PROPOSTA ESPECIAL';
    			$header['js'] = load_js(array('nova_proposta/proposta_especial.js'));
    			break;
    				
    		case "proposta_nac":
    			$header['form_name'] = 'PROPOSTA NAC';
    			$header['js'] = load_js(array('nova_proposta/proposta_nac.js'));
    			break;
    	
    		default :
    			show_error("Não foi possível encontrar o arquivo javascript referente ao contexto atual!");
    	
    	}//END SWITCH
        
    	$data["sentido"] = $proposta->getSentido();
    	$data["proposta"] = $proposta;
        $data["itens_serializados"] = $proposta_serializada;
    	
    	$this->load->view("Padrao/header_view",$header);
    	$this->load->view("propostas/".strtolower($tipo_proposta."_alteracao"),$data);
    	$this->load->view("Padrao/footer_view",$footer);
    	
    }//END FUNCTION
            
    public function alterar_proposta()
    {
        //pr($this->input->post());
        
          /** valida os dados do form **/
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('sentido', 'Sentido', 'required');
        $this->form_validation->set_rules('clientes_selecionados', 'Clientes Selecionados', 'required');
        $this->form_validation->set_rules('rotas_adicionadas', 'Rotas Selecionadas', 'required');
        $this->form_validation->set_rules('tipo_proposta','Tipo Proposta','required');
        
        if ($this->form_validation->run() == FALSE)
		{
			show_error(validation_errors());
		}
        
        $proposta_model = new Proposta_Model();
        
        try{        
            
            $proposta = $proposta_model->alterarProposta();     
            
        } catch ( InvalidArgumentException $e ) {
            log_message('error',$e->getTraceAsString());
            show_error($e->getMessage());
        } catch ( RuntimeException $e ) {
            log_message('error',$e->getTraceAsString());
            show_error($e->getMessage());                      
        } catch ( Exception $e ) {
            log_message('error',$e->getTraceAsString());
            show_error($e->getMessage());
        }
        
        redirect(base_url()."/index.php/propostas/propostas/consultar/". $proposta->getId() ."/" . strtolower(get_class($proposta)));
        
    }//END FUNCTION   

    public function realizar_busca()
    {
    	$header['form_title'] = 'Scoa - Propostas';
    	$header['form_name'] = 'REALIZAR BUSCA';
    	$header['css'] = '';
    	$header['js'] = load_js(array('propostas/realizar_busca.js'));
    	
    	$data['tipos_consultas'] = Array(
    			0 => "Selecione",
    			"numero" => "Número",
    			"cliente" => "Cliente",
    			"origem" => "Origem",
    			"destino" => "Destino",
    			"periodo" => "Período"
    	);
    	
    	$data['sentidos'] = Array(
    			0 => "Selecione",
    			"IMP" => "Importação",
    			"EXP" => "Exportação"
    	);
    	
    	$imagens = "";
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0)).'</a>';    	
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
    	 
    	$footer['footer'] = $imagens;
    	    	    	
    	$this->load->view("Padrao/header_view",$header);
    	$this->load->view("propostas/realizar_busca",$data);
    	$this->load->view("Padrao/footer_view",$footer);
    }
    
    public function listar_resultados_busca()
    {
        
        $this->load->model("Propostas/Buscas/search_factory");
        $this->load->library("Scoa/url");
        
        $url_library = new Url();
        
        $buscador = Search_Factory::factory($this->input->post('tipo_consulta'));
               
        $propostas_encontradas = $buscador->buscar(
                                                    $url_library->decodificarUrl($this->input->post('dado_para_busca')),
                                                    $this->input->post('sentido')
        );
        
        $header['form_title'] = 'Scoa - Propostas Econtradas';
    	$header['form_name'] = 'Propostas Encontradas';
    	$header['css'] = '';
    	$header['js'] = load_js(array('propostas/lista_propostas.js'));
        
        $imagens = "";    	
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
    	              
        $footer['footer'] = $imagens;
        
        $data['propostas'] = $propostas_encontradas;
    	    	    	
    	$this->load->view("Padrao/header_view",$header);
    	$this->load->view("propostas/lista_propostas",$data);
    	$this->load->view("Padrao/footer_view",$footer);
        
    }        
    
}//END CLASS

/* End of file propostas.php */
/* Location: ./application/controllers/propostas.php */

