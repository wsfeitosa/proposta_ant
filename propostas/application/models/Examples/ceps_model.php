<?php 
class Ceps_model extends CI_Model{
	
	// Class Variables
	var $cep = NULL;
	var $rua = NULL;
	var $bairro = NULL;
	var $cidade = NULL;
	var $estado = NULL;
	
	public function __construct(){
		
		parent::__construct();
		
	}//END FUNCTION
	
	public function findCep($cep){
		
		$sql = "SELECT 
					endereco.endereco_cep, endereco.endereco_logradouro,
					cidade.cidade_cep, cidade.cidade_descricao, uf_codigo,
					bairro.bairro_descricao, uf.Nome as 'estado'
				FROM
					CEPS.endereco
					INNER JOIN CEPS.bairro ON bairro.bairro_codigo = endereco.bairro_codigo
					INNER JOIN CEPS.cidade ON cidade.cidade_codigo = bairro.cidade_codigo
					INNER JOIN CEPS.uf ON uf.id_estado = cidade.uf_codigo
				WHERE
					endereco.endereco_cep LIKE '".$cep."%'";
		
		$result = $this->db->query($sql);
		
		if($result->num_rows() < 1)
		{
			return false;
		}
		
		return $result->result_array();		
		
	}
	
}// END CLASS
?>