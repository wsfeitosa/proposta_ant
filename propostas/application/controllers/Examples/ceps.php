<?php 
class Ceps extends CI_Controller{
	
	public function __construct(){
		
		parent::__construct();
		$this->load->model('ceps_model','model',TRUE);
		
	}
	
	public function index(){
		
		$data = array(
					  'form_title' => "SCOA - ALLINK",
					  'form_name' => 'CONSULTA DE CEPS',
					  'msg' => '',
		);
		
		$this->load->view('ceps',$data);
		
	}//END FUNCTION
	
	
	public function find(){
		
		$results_cep = $this->model->findCep($this->input->post('cep'));
		
		if(!$results_cep)
		{
			$data = array(
						  'form_title' => "SCOA - ALLINK",
						  'form_name' => 'CONSULTA DE CEPS',
						  'msg' => 'Nenhum Cep Encontrado!',
			);
			
			$this->load->view('ceps',$data);
			return false;
		}
		
		$data = array(
					  'form_title' => "SCOA - ALLINK",
					  'form_name' => 'CONSULTA DE CEPS',
					  'msg' => '',
					  'results' => $results_cep,
		);
		
		$this->load->view('Padrao/header_view',$data);
		$this->load->view('show_ceps_result',$results_cep);
		$this->load->view('Padrao/footer_view');
		
		
		
	}//END FUNCTION
	
}//END CLASS
?>