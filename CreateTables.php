<?php
include_once dirname(dirname(__FILE__)) . "/Conexao/conecta.inc";

class CreateTables {
	
	protected $conn = NULL;
	
	public function __construct(){
		
		$this->conn = Zend_Conn();
		$this->Main();
		
	}
	
	public function Main(){

		$this->CreatePropostasTable();
		$this->CreateItensPropostasTables();
		$this->CreateItemTaxas();
		$this->CreateClientesPropostas();
		
	}
	
	protected function CreatePropostasTable(){
		
		echo "Criando a tabela propostas...<br />\r\n";
		
		$stmt = "CREATE TABLE IF NOT EXISTS `propostas` (
				  `id_proposta` int(11) NOT NULL AUTO_INCREMENT,
				  `numero_proposta` varchar(15) NOT NULL,
				  `tipo_proposta` varchar(45) NOT NULL,
				  `sentido` char(3) DEFAULT NULL,
				  `enviada` tinyint(1) DEFAULT NULL,
				  `id_usuario_inclusao` int(11) DEFAULT NULL,
				  `id_usuario_alteracao` int(11) DEFAULT NULL,
				  `data_inclusao` datetime DEFAULT NULL,
				  `data_alteracao` datetime DEFAULT NULL,
				  PRIMARY KEY (`id_proposta`),
				  KEY `id_usuario_inclusao` (`id_usuario_inclusao`,`id_usuario_alteracao`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		
		return $this->conn->exec($stmt);
		
	}
	
	protected function CreateItensPropostasTables(){
		
		echo "Criando à tabela itens_proposta...<br />\r\n";
		
		$stmt = "CREATE TABLE IF NOT EXISTS `itens_proposta` (
				  `id_item_proposta` int(11) NOT NULL AUTO_INCREMENT,
				  `id_proposta` int(11) NOT NULL,
				  `id_tarifario_pricing` bigint(20) NOT NULL,
				  `numero_proposta` varchar(15) NOT NULL,
				  `mercadoria` varchar(255) NOT NULL,
				  `pp` tinyint(1) NOT NULL,
				  `cc` tinyint(1) NOT NULL,
				  `peso` float(10,3) NOT NULL,
				  `cubagem` float(10,3) NOT NULL,
				  `volumes` int(11) NOT NULL,
				  `observacao_cliente` text NOT NULL,
				  `observacao_interna` text NOT NULL,
				  `id_status_item` int(11) NOT NULL,
				  `data_inicial` date NOT NULL,
				  `validade` date NOT NULL,
				  PRIMARY KEY (`id_item_proposta`),
				  KEY `fk_itens_proposta_propostas1` (`id_proposta`),
				  KEY `fk_itens_proposta_tarifarios_pricing1` (`id_tarifario_pricing`),
				  KEY `fk_itens_proposta_status_itens_propostas1` (`id_status_item`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		
		return $this->conn->exec($stmt);
		
	}
	
	protected function CreateItemTaxas(){
		
		echo "Criando à tabela taxas_item_proposta...<br />\r\n";
		
		$stmt = "CREATE TABLE IF NOT EXISTS `taxas_item_proposta` (
				  `id_taxa_item` int(11) NOT NULL AUTO_INCREMENT,
				  `id_item_proposta` int(11) NOT NULL,
				  `id_taxa_adicional` int(3) unsigned NOT NULL,
				  `id_unidade` int(3) unsigned NOT NULL,
				  `id_moeda` int(5) unsigned NOT NULL,
				  `valor` float(10,2) NOT NULL DEFAULT '0.00',
				  `valor_minimo` float(10,2) NOT NULL DEFAULT '0.00',
				  `valor_maximo` float(10,2) NOT NULL DEFAULT '0.00',
				  `taxa_portuaria` char(1) NOT NULL,
				  PRIMARY KEY (`id_taxa_item`),
				  KEY `fk_taxas_item_proposta_itens_proposta1` (`id_item_proposta`),
				  KEY `fk_taxas_item_proposta_taxas_adicionais1` (`id_taxa_adicional`),
				  KEY `fk_taxas_item_proposta_unidades1` (`id_unidade`),
				  KEY `fk_taxas_item_proposta_moedas1` (`id_moeda`),
				  KEY `id_item_proposta` (`id_item_proposta`,`id_taxa_adicional`,`id_unidade`,`id_moeda`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		
		return $this->conn->exec($stmt);
		
	}
	
	protected function CreateClientesPropostas(){

		echo "Criando à tabela clientes_x_propostas...<br />\r\n";

		$stmt = "CREATE TABLE IF NOT EXISTS `clientes_x_propostas` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `id_cliente` int(11) NOT NULL,
				  `id_proposta` int(11) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `fk_clientes_x_propostas_propostas1` (`id_proposta`),
				  KEY `fk_clientes_x_propostas_clientes1` (`id_cliente`),
				  KEY `id_cliente` (`id_cliente`,`id_proposta`)
				) ENGINE=MyISAM AUTO_INCREMENT=417 DEFAULT CHARSET=latin1";

		return $this->conn->exec($stmt);

	}
	
	
}//END CLASS