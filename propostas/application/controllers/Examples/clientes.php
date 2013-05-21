<?php
class Clientes extends CI_Controller{
    
    public function __construct(){
    
        parent::__construct();
        
        $this->load->model('clientes_model','model',TRUE);
        
    }   
    
    public function index(){
                    
          $data = $this->model->findCliente('cep','058');
          
          print"<pre>";var_dump($data);  
              
    }
    
    public function listClientes($cep){
                    
          $data = $this->model->findCliente('cep',$cep);
          
          print"<pre>";var_dump($data);  
              
    }   
    
}//END CLASS    
?>
