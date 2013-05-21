<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* @package  Controllers/Tarifarios
* @author Wellington Feitosa <wellington.feitosao@allink.com.br>
* @copyright Allink Transporte Internacionais LTDA. - 30/01/2013
* @version  1.0
* Controla o fluxo da aplica��o para os tarifarios de importa��o
*/
class Tarifarios extends CI_Controller {

	public function __construct()
	{
		
		parent::__construct();
		$this->load->helper(Array("html","form","url"));		
		$this->load->model("Tarifario/Facade/tarifario_facade");
		
		$this->output->enable_profiler(FALSE);
		
	}
	
	public function index()
	{
		$server = new SoapServer(null, Array('uri' => 'http://localhost/Clientes/propostas/index.php/tarifarios/tarifario/'));
		$server->setObject($this);
		$server->handle();
	}

	public function find($clientes = NULL, $origem = NULL, $embarque = NULL, $desembarque = NULL, $destino = NULL, $imo = "N", $modalidade = NULL, $modulo = "proposta", $sentido = NULL)
	{		
		
		if( is_null($sentido) )
		{
			log_message('error',"N�o foi poss�vel consultar o tarif�rio, pois o sentido (IMP ou EXP) n�o foi informado");
			show_error("N�o foi poss�vel consultar o tarif�rio, pois o sentido (IMP ou EXP) n�o foi informado");
		}	
		
		/** Chama o fa�ade que vai buscar os tarif�rios **/
        $dados_recebidos = new ArrayObject();
        
        $dados_recebidos->offsetSet("clientes", $clientes);
        $dados_recebidos->offsetSet("origem", $origem);
        $dados_recebidos->offsetSet("embarque",$embarque);
        $dados_recebidos->offsetSet("desembarque", $desembarque);
        $dados_recebidos->offsetSet("destino", $destino);
        $dados_recebidos->offsetSet("imo", $imo);
        $dados_recebidos->offsetSet("modalidade", $modalidade);
        $dados_recebidos->offsetSet("modulo", $modulo);
        $dados_recebidos->offsetSet("sentido", $sentido);
        
        $facade = new Tarifario_Facade();
        
        $tarifarios = $facade->ListarTarifarios($dados_recebidos);
		
		$header['form_title'] = 'Scoa - Tarif�rios';
		$header['form_name'] = 'SELECIONAR TARIF�RIO';
		$header['css'] = '';
		
		/** Decide qual o javascrip certo a carregar baseado no m�dulo **/
		switch($modulo)
		{
				
			case "proposta":
				$js_file  = "proposta.js";
			break;
					
			case "routing_order":
				$js_file = "routing_order.js";
			break;
				
			case "house":
				$js_file = "house.js";
			break;
					
			default:
				show_error("Impossivel determinar o modulo selecionado!");
					
		}
		
		$header['js'] = load_js(array('tarifarios/'.$js_file));
		
		$data["tarifarios"] = $tarifarios;
		
		$imagens = '';
			
		$footer['footer'] = $imagens;
		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("Tarifarios/find",$data);
		$this->load->view("Padrao/footer_view",$footer);
		
	}//END FUNCTION
	
	public function fill( $id_tarifario = NULL, $sentido = NULL, $clientes = NULL, $id_item_proposta = NULL )
	{		
		
		if( empty($id_tarifario) )
		{
			log_message('error','O id do tarif�rio n�o foi informado corretamente para efetuar a busca do tarif�rio');
			show_error('O id do tarif�rio n�o foi informado corretamente para efetuar a busca do tarif�rio');
		}	
							
		try{
			
            $facade = new Tarifario_Facade();
            
            $tarifario = $facade->BuscarTarifarioPeloId( $id_tarifario, $sentido, $clientes, $id_item_proposta );
			
			$this->load->view("Tarifarios/xml_tarifario",Array("tarifario" => $tarifario));
			
		} catch (Exception $e) {
			log_message('error',$e->getMessage());
			show_error($e->getMessage());
		}	
		
	}//END FUNCTION
	
}//END CLASS	