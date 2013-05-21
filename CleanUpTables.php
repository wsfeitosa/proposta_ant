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
		$this->cleanUpAcordosTaxasLocaisGlobais();
		$this->cleanUpClientesAcordosTaxasLocais();
		$this->cleanUpDesbloqueioTaxas();
		$this->cleanUpDesbloqueioValidades();
		$this->cleanUpEnviosPropostas();
		$this->cleanUpLogPropostas();
		$this->cleanUpNotasExportacao();
		$this->cleanUpNotasImportacao();
		$this->cleanUpPortosAcordosTaxas();
		$this->cleanUpStatusItensPropostas();
		$this->cleanUpTaxasAcordosTaxasLocais();
		
	}
	
	protected function cleanUpPropostas(){
		
		$this->conn->query("ALTER TABLE CLIENTES.propostas AUTO_INCREMENT = 1");
		
		echo "Limpando à  tabela propostas...<br />\r\n";
		
		return $this->conn->delete("CLIENTES.propostas");
	
	}
	
	protected function cleanUpItensPropostas(){
		
		$this->conn->query("ALTER TABLE CLIENTES.itens_proposta AUTO_INCREMENT = 1");
		
		echo "Limpando à  tabela itens_propostas...<br />\r\n";
		
		return $this->conn->delete("CLIENTES.itens_proposta");
		
	}
	
	protected function cleanUpTaxas(){
		
		$this->conn->query("ALTER TABLE CLIENTES.taxas_item_proposta AUTO_INCREMENT = 1");
		
		echo "Limpando à  tabela taxas_item_proposta...<br />\r\n";
		
		return $this->conn->delete("CLIENTES.taxas_item_proposta");
		
	}

	protected function cleanUpClientes(){

		$this->conn->query("ALTER TABLE CLIENTES.clientes_x_propostas AUTO_INCREMENT = 1");

		echo "Limpando à  tabela clientes_x_propostas...<br />\r\n";

		return $this->conn->delete("CLIENTES.clientes_x_propostas");

	}

	protected function cleanUpEmails(){

		$this->conn->query("ALTER TABLE CLIENTES.emails_propostas AUTO_INCREMENT = 1");

		echo "Limpando à  tabela de emails...<br />\r\n";

		return $this->conn->delete("CLIENTES.emails_propostas");

	}

	protected function  cleanUpAcordosTaxasLocaisGlobais(){
		
		$this->conn->query("ALTER TABLE CLIENTES.acordos_taxas_locais_globais AUTO_INCREMENT = 1");

		echo "Limpando à  tabela de acordos das taxas locais...<br />\r\n";

		return $this->conn->delete("CLIENTES.acordos_taxas_locais_globais");

		
	}
	
	protected function cleanUpClientesAcordosTaxasLocais(){
				
		$this->conn->query("ALTER TABLE CLIENTES.clientes_x_acordos_taxas_locais_globais AUTO_INCREMENT = 1");

		echo "Limpando à  tabela de clientes dos acordos das taxas locais...<br />\r\n";

		return $this->conn->delete("CLIENTES.clientes_x_acordos_taxas_locais_globais");

	}

	protected function cleanUpDesbloqueioTaxas(){
				
		$this->conn->query("ALTER TABLE CLIENTES.desbloqueios_taxas AUTO_INCREMENT = 1");

		echo "Limpando à  tabela de desbloqueio de taxas..<br />\r\n";

		return $this->conn->delete("CLIENTES.desbloqueios_taxas");

	}

	protected function cleanUpDesbloqueioValidades(){
				
		$this->conn->query("ALTER TABLE CLIENTES.desbloqueios_validades AUTO_INCREMENT = 1");

		echo "Limpando à  tabela de desbloqueio de validades..<br />\r\n";

		return $this->conn->delete("CLIENTES.desbloqueios_validades");

	}

	protected function cleanUpEnviosPropostas(){
		
		$this->conn->query("ALTER TABLE CLIENTES.envios_propostas AUTO_INCREMENT = 1");

		echo "Limpando à  tabela de desbloqueio de envios das propostas...<br />\r\n";

		return $this->conn->delete("CLIENTES.envios_propostas");
		
	}
		
	protected function cleanUpLogPropostas(){
		
		$this->conn->query("ALTER TABLE CLIENTES.log_propostas AUTO_INCREMENT = 1");

		echo "Limpando à  tabela de desbloqueio de log das propostas...<br />\r\n";

		return $this->conn->delete("CLIENTES.log_propostas");
		
	}

	protected function cleanUpNotasExportacao(){
		
		$this->conn->query("ALTER TABLE CLIENTES.notas_exportacao AUTO_INCREMENT = 1");

		echo "Limpando à  tabela das notas de exportação...<br />\r\n";

		return $this->conn->delete("CLIENTES.notas_exportacao");
		
	}

	protected function cleanUpNotasImportacao(){
		
		$this->conn->query("ALTER TABLE CLIENTES.notas_importacao AUTO_INCREMENT = 1");

		echo "Limpando à  tabela das notas de importação...<br />\r\n";

		return $this->conn->delete("CLIENTES.notas_importacao");
		
	}

	protected function cleanUpPortosAcordosTaxas(){
		
		$this->conn->query("ALTER TABLE CLIENTES.portos_x_acordos_taxas_globais AUTO_INCREMENT = 1");

		echo "Limpando à  tabela dos portos dos acordos de taxas locais...<br />\r\n";

		return $this->conn->delete("CLIENTES.portos_x_acordos_taxas_globais");
		
	}

	protected function cleanUpStatusItensPropostas(){
		
		$this->conn->query("ALTER TABLE CLIENTES.status_itens_propostas AUTO_INCREMENT = 1");

		echo "Limpando à  tabela de status dos itens das propostas...<br />\r\n";

		return $this->conn->delete("CLIENTES.status_itens_propostas");
		
	}

	protected function cleanUpTaxasAcordosTaxasLocais(){
		
		$this->conn->query("ALTER TABLE CLIENTES.taxas_x_acordos_taxas_locais_globais AUTO_INCREMENT = 1");

		echo "Limpando à  tabela de taxas dos acordos de taxas locais...<br />\r\n";

		return $this->conn->delete("CLIENTES.taxas_x_acordos_taxas_locais_globais");
		
	}
	
}//END CLASS

