<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package  Controllers/taxas_locais
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 13/05/2013
 * @version  1.0
 * Controla o fluxo da aplicação para o cadastro de acordos de taxas locais
 */
class Taxas_Locais extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(Array("html","form","url"));
		$this->load->library('form_validation');		
		$this->load->model("Taxas_Locais_Acordadas/buscador_taxas_locais");
		$this->load->model("Taxas_Locais_Acordadas/acordo_taxas_locais_model");
		$this->load->model("Taxas_Locais_Acordadas/gera_numero_acordo");
	}
	
	public function index()
	{
		
		$header['form_title'] = 'Scoa - Propostas';
		$header['form_name'] = 'NOVO ACORDO TAXAS LOCAIS';
		$header['css'] = '';
		$header['js'] = load_js(array('taxas/cadastro_taxas_acordadas.js'));
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/gravar.jpg', 'id' => 'salvar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
		
		$footer['footer'] = $imagens;
		
		/** Busca os portos disponiveis para cadastramento das taxas **/
		$this->load->model("Taxas_Locais_Acordadas/portos_taxas");
		$portos_taxas = new Portos_Taxas();
		
		$data['portos'] = $portos_taxas->obterPortosDasTaxasLocais();
		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("Taxas/cadastro_taxas_acordadas",$data);
		$this->load->view("Padrao/footer_view",$footer);
		
	}
	
	public function find( $porto, $clientes, $sentido )
	{
							
		/** Valida às informações **/
		if( $sentido != "IMP" && $sentido != "EXP" )
		{
			$msg = "Sentido inválido informado, para o cadastro de taxas locais, precisa ser imp ou exp";
			log_message('error',$msg);
			show_error($msg);
		}		
		
		if( empty($clientes) )
		{
			$msg = "Nenhum cliente informado para a consulta";
			log_message('error',$msg);
			show_error($msg);
		}	
		
		if( empty($porto) )
		{
			$msg = "Nenhum cliente informado para a consulta";
			log_message('error',$msg);
			show_error($msg);
		}	
		
		$finder = new Buscador_Taxas_Locais();
		//TODO buscar às taxas já cadastradas e comparar com às taxas dos portos
		$taxas_locais_encontradas = $finder->buscarTaxasLocais( $porto, $clientes, $sentido );
						
		$data["taxas_locais"] = $taxas_locais_encontradas;
		
		$this->load->view("Taxas/xml_taxas_encontradas", $data);
		
	}
	
	public function add( $js_file )
	{
		
		$header['form_title'] = 'Scoa - Adicionar Taxa';
		$header['form_name'] = 'ADICIONAR TAXA';
		$header['css'] = '';
		$header['js'] = load_js(array('taxas/'.$js_file,"jquery.price_format.1.7.min.js","jquery.price_format.1.7.js"));
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/gravar.jpg', 'id' => 'salvar' , 'border' => 0)).'</a>';
		
		$footer['footer'] = $imagens;
		
		/** Busca às taxas disponiveis para adição na tela **/
		$this->load->model("Taxas/taxa_model");
		
		$taxa_model = new Taxa_Model();
		
		$data["taxas"] = $taxa_model->retornaTodasAsTaxas();
						
		/** Busca às unidades de cobrança **/
		$this->load->model("Taxas/unidade_model");
		
		$unidade_model = new Unidade_Model();
		
		$data["unidades"] = $unidade_model->retornaTodasAsUnidades();
				
		/** Busca às moedas **/
		$this->load->model("Taxas/moeda_model");
		
		$moeda_model = new Moeda_Model();
		
		$data["moedas"] = $moeda_model->retornaTodasAsMoedas();
				 	
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("Taxas/add",$data);
		$this->load->view("Padrao/footer_view",$footer);
		
	}
	
	public function save()
	{
		
		$this->form_validation->set_rules("sentido","Sentido","required");
		$this->form_validation->set_rules("inicio","Data Inicial","required");
		$this->form_validation->set_rules("validade","Data Final","required");
		$this->form_validation->set_rules("clientes_selecionados","Clientes","required");
		$this->form_validation->set_rules("portos_selecionados","Portos","required");
		$this->form_validation->set_rules("taxas_selecionadas","Taxas","required");
		
		if( ! $this->form_validation->run() )
		{
			show_error( validation_errors() );
		}	
		
		$model = new Acordo_Taxas_Locais_Model();
		
		$gerador = new Gera_Numero_Acordo();
		
		$numero = $gerador->gerarNumeroAcordo();
		
		pr($this->input->post());
			
	}
	
}//END CLASS