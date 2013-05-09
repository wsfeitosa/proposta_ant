<?php
include_once dirname(dirname(__FILE__)) . "/Conexao/conecta.inc";

class CleanUpTables {
	
	protected $conn = NULL;
	
	public function __construct(){
		
		$this->conn = Zend_Conn();
		$this->cleanUp();
		
	}
	
	public function cleanUp(){
				
		$this->cleanUpPropostas();
		$this->cleanUpItensPropostas();
		$this->cleanUpTaxas();
		$this->cleanUpClientes();
		$this->cleanUpEmails();
		
	}
	
	protected function cleanUpPropostas(){
		
		$this->conn->query("ALTER TABLE CLIENTES.propostas AUTO_INCREMENT = 1");
		
		echo "Limpando à tabela propostas...<br />\r\n";
		
		return $this->conn->delete("CLIENTES.propostas");
	
	}
	
	protected function cleanUpItensPropostas(){
		
		$this->conn->query("ALTER TABLE CLIENTES.itens_proposta AUTO_INCREMENT = 1");
		
		echo "Limpando à tabela itens_propostas...<br />\r\n";
		
		return $this->conn->delete("CLIENTES.itens_proposta");
		
	}
	
	protected function cleanUpTaxas(){
		
		$this->conn->query("ALTER TABLE CLIENTES.taxas_item_proposta AUTO_INCREMENT = 1");
		
		echo "Limpando à tabela taxas_item_proposta...<br />\r\n";
		
		return $this->conn->delete("CLIENTES.taxas_item_proposta");
		
	}

	protected function cleanUpClientes(){

		$this->conn->query("ALTER TABLE CLIENTES.clientes_x_propostas AUTO_INCREMENT = 1");

		echo "Limpando à tabela clientes_x_propostas...<br />\r\n";

		return $this->conn->delete("CLIENTES.clientes_x_propostas");

	}

	protected functionn cleanUpEmails(){

		$this->conn->query("ALTER TABLE CLIENTES.emails_propostas AUTO_INCREMENT = 1");

		echo "Limpando à tabela de emails...<br />\r\n";

		return $this->conn->delete("CLIENTES.emails_propostas");

	}

			
}//END CLASS

