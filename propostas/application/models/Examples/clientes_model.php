<?php
class Clientes_model extends CI_Model{
    
    public function __construct(){
        
        parent::__construct();
        
    }//END FUNCTION
    
    // Class Variables
    public $_id_cliente;
    public $_id_usuario;
    public $_data_cadastro;
    public $_id_usuario_alt;
    public $_data_alt;
    public $_razao;
    public $_endereco;
    public $_id_classificacao;
    public $_numero;
    public $_complemento;
    public $_bairro;
    public $_cidade;    
    public $_estado;
    public $_cep;
    public $_localizacao;
    public $_vendedor_exp;
    public $_vendedor_imp;
    public $_grupo;
    public $_tfclassif;
    public $_fax;
    public $_ddi;
    public $_ddd;
    public $_telefone;
    public $_cnpj;
    public $_ativo;
    public $_tipo_pessoa;
    public $_nome_fantasia;
    public $_key_account;
    public $_customer_exp;
    public $_customer_imp;
    public $_logradouro;
    public $_apelido;
    public $_cod_atividade_principal;
    public $_atividade_principal;
    public $_cod_atividade_secundaria;
    public $_atividade_secundaria;
    public $_id_grupo_cnpj;
    public $_id_grupo_comercial;
    public $_id_coordenador_imp;
    public $_nome_cidade;
    public $_id_sugar;
    
    public function findCliente($tipo_pesquisa,$parametro){
               
        $this->db->select('*')->from('CLIENTES.clientes')->like($tipo_pesquisa,$parametro,'after');
        
        $query = $this->db->get();
        
        return $query->result_array();
        
    }//END FUNCTION 
    
    
}//END CLASS    
?>
