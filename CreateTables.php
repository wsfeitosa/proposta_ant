<?php
include_once dirname(dirname(__FILE__)) . "/Conexao/conecta.inc";

class CreateTables {
	
	protected $conn = NULL;
	
	public function __construct(){
		
		$this->conn = Zend_Conn();
		$this->Main();
		
	}
	
	public function Main(){

		//$this->CreateDatabase();		
		$this->CreateAcordosTaxasLocaisGlobaisTable();
		$this->CreateClientesAcordosTaxasLocais();
		$this->CreateClientesPropostasTable();
		$this->CreateDsbloqueioTaxasTable();
		$this->CreateDesbloqueioValidadesTable();
		$this->CreateEmails();
		$this->CreateEnviosPropostasTable();		
		$this->CreateItensPropostasTables();
		$this->CreateLogPropostasTable();
		$this->CreateNotasExportacaoTable();
		$this->CreateNotasImportacaoTable();
		$this->CreatePortosAcordosTaxas();
		$this->CreatePropostasTable();
		$this->CreateStatusItensPropostasTable();		
		$this->CreateItemTaxas();		
		$this->CreateClientesPropostas();
		$this->CreateTaxasAcordosTaxasLocais();
		
	}
	
	protected function CreateDatabase(){

		echo "Criando a base de dados...<br />\r\n";

		$stmt = "CREATE DATABASE CLIENTES IF NOT EXISTS";

		return $this->conn->exec($stmt);

	}
	
	protected function CreateAcordosTaxasLocaisGlobaisTable()
	{
		echo "Criando a tabela acordos_taxas_locais_globais...<br />\r\n";
		
		$stmt = "DROP TABLE IF EXISTS `acordos_taxas_locais_globais`;
				CREATE TABLE IF NOT EXISTS `acordos_taxas_locais_globais` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `numero` varchar(13) NOT NULL,
				  `sentido` varchar(3) NOT NULL,
				  `observacoes_internas` text,
				  `data_inicial` date DEFAULT NULL,
				  `validade` date DEFAULT NULL,
				  `id_usuario_cadastro` int(11) NOT NULL,
				  `data_cadastro` datetime NOT NULL,
				  `id_usuario_ultima_alteracao` int(11) NOT NULL,
				  `data_ultima_alteracao` datetime NOT NULL,
				  `registro_ativo` char(1) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  KEY `id_usuario_cadastro` (`id_usuario_cadastro`,`id_usuario_ultima_alteracao`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		
		return $this->conn->exec($stmt);
	}
	
	protected function CreateClientesAcordosTaxasLocais(){
	
		echo "Criando a tabela clientes_x_acordos_taxas_locais_globais...<br />\r\n";
	
		$stmt = "DROP TABLE IF EXISTS `clientes_x_acordos_taxas_locais_globais`;
				CREATE TABLE IF NOT EXISTS `clientes_x_acordos_taxas_locais_globais` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `id_cliente` int(11) DEFAULT NULL,
				  `id_acordos_taxas_locais` int(11) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  KEY `id_cliente` (`id_cliente`),
				  KEY `id_acordos_taxas_locais` (`id_acordos_taxas_locais`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
		return $this->conn->exec($stmt);
	
	}
	
	protected function CreateClientesPropostasTable(){
	
		echo "Criando a tabela clientes_x_propostas...<br />\r\n";
	
		$stmt = "DROP TABLE IF EXISTS `clientes_x_propostas`;
				CREATE TABLE IF NOT EXISTS `clientes_x_propostas` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `id_cliente` int(11) NOT NULL,
				  `id_proposta` int(11) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `id_proposta` (`id_proposta`),
				  KEY `fk_clientes_x_propostas_propostas1` (`id_proposta`),
				  KEY `fk_clientes_x_propostas_clientes1` (`id_cliente`),
				  KEY `id_cliente` (`id_cliente`,`id_proposta`),
				  KEY `id_cliente_2` (`id_cliente`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
		return $this->conn->exec($stmt);
	
	}
	
	protected function CreateDsbloqueioTaxasTable(){
	
		echo "Criando a tabela desbloqueios_taxas...<br />\r\n";
	
		$stmt = "DROP TABLE IF EXISTS `desbloqueios_taxas`;
				CREATE TABLE IF NOT EXISTS `desbloqueios_taxas` (
				  `id_desbloqueio_taxa` int(11) NOT NULL AUTO_INCREMENT,
				  `id_taxa_item` int(11) NOT NULL,
				  `id_unidade` int(3) unsigned NOT NULL,
				  `id_moeda` int(5) unsigned NOT NULL,
				  `valor` float(10,2) NOT NULL,
				  `valor_minimo` float(10,2) NOT NULL,
				  `valor_maximo` float(10,2) NOT NULL,
				  `id_usuario_solicitacao` int(11) NOT NULL,
				  `data_solicitacao` datetime NOT NULL,
				  `id_usuario_desbloqueio` int(11) DEFAULT NULL,
				  `data_desbloqueio` datetime DEFAULT NULL,
				  `status` char(1) DEFAULT NULL,
				  `id_nota_exportacao` int(11) NOT NULL,
				  `id_nota_importacao` int(11) NOT NULL,
				  PRIMARY KEY (`id_desbloqueio_taxa`),
				  KEY `fk_debloqueios_taxas_taxas_item_proposta1` (`id_taxa_item`),
				  KEY `fk_debloqueios_taxas_unidades1` (`id_unidade`),
				  KEY `fk_debloqueios_taxas_moedas1` (`id_moeda`),
				  KEY `fk_debloqueios_taxas_notas_exportacao1` (`id_nota_exportacao`),
				  KEY `id_nota_importacao` (`id_nota_importacao`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
		return $this->conn->exec($stmt);
	
	}
	
	protected function CreateDesbloqueioValidadesTable(){
	
		echo "Criando a tabela desbloqueios_validades...<br />\r\n";
	
		$stmt = "DROP TABLE IF EXISTS `desbloqueios_validades`;
				CREATE TABLE IF NOT EXISTS `desbloqueios_validades` (
				  `id_desbloqueio_validade` int(11) NOT NULL AUTO_INCREMENT,
				  `id_item_proposta` int(11) NOT NULL,
				  `validade` date NOT NULL,
				  `status` char(1) DEFAULT NULL,
				  `id_usuario_solicitacao` int(11) NOT NULL,
				  `data_solicitacao` datetime NOT NULL,
				  `id_usuario_desbloqueio` int(11) DEFAULT NULL,
				  `data_desbloqueio` datetime DEFAULT NULL,
				  `id_nota_exportacao` int(11) NOT NULL,
				  `id_nota_importacao` int(11) NOT NULL,
				  PRIMARY KEY (`id_desbloqueio_validade`),
				  KEY `fk_desbloqueios_validades_itens_proposta1` (`id_item_proposta`),
				  KEY `fk_desbloqueios_validades_notas_exportacao1` (`id_nota_exportacao`),
				  KEY `fk_desbloqueios_validades_notas_importacao1` (`id_nota_importacao`),
				  KEY `id_usuario_solicitacao` (`id_usuario_solicitacao`),
				  KEY `id_usuario_desbloqueio` (`id_usuario_desbloqueio`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
		return $this->conn->exec($stmt);
	
	}
	
	protected function CreateEmails(){
	
		echo "Criando à tabela emails_propostas...<br />\r\n";
	
		$stmt = "CREATE TABLE IF NOT EXISTS `emails_propostas` (
				  `id_email_proposta` int(11) NOT NULL AUTO_INCREMENT,
				  `email` varchar(255) DEFAULT NULL,
				  `ativo` char(1) NOT NULL,
				  `id_proposta` int(11) NOT NULL,
				  PRIMARY KEY (`id_email_proposta`),
				  KEY `fk_emails_propostas_propostas` (`id_proposta`),
				  KEY `id_proposta` (`id_proposta`)
				) ENGINE=MyISAM AUTO_INCREMENT=1549 DEFAULT CHARSET=latin1 COMMENT='latin1_swedish_ci'";
	
		return $this->conn->exec($stmt);
	
	}
	
	protected function CreateEnviosPropostasTable(){
	
		echo "Criando a tabela envios_propostas...<br />\r\n";
	
		$stmt = "DROP TABLE IF EXISTS `envios_propostas`;
				CREATE TABLE IF NOT EXISTS `envios_propostas` (
				  `id_envio_proposta` int(11) NOT NULL AUTO_INCREMENT,
				  `id_proposta` int(11) NOT NULL,
				  `id_usuario_envio` int(3) unsigned NOT NULL,
				  `data_envio` datetime NOT NULL,
				  `emails_para` varchar(255) DEFAULT NULL,
				  `emails_cc` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id_envio_proposta`),
				  KEY `fk_envios_propostas_propostas1` (`id_proposta`),
				  KEY `fk_envios_propostas_usuarios1` (`id_usuario_envio`),
				  KEY `id_proposta` (`id_proposta`,`id_usuario_envio`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
		return $this->conn->exec($stmt);
	
	}
	
	protected function CreateItensPropostasTables(){
	
		echo "Criando à tabela itens_proposta...<br />\r\n";
	
		$stmt = "DROP TABLE IF EXISTS `itens_proposta`;
				CREATE TABLE IF NOT EXISTS `itens_proposta` (
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
				  `id_usuario_inclusao` int(11) NOT NULL,
				  `data_inclusao` datetime NOT NULL,
				  `id_usuario_ultima_alteracao` int(11) NOT NULL,
				  `data_ultima_alteracao` datetime NOT NULL,
				  PRIMARY KEY (`id_item_proposta`),
				  KEY `fk_itens_proposta_propostas1` (`id_proposta`),
				  KEY `fk_itens_proposta_tarifarios_pricing1` (`id_tarifario_pricing`),
				  KEY `fk_itens_proposta_status_itens_propostas1` (`id_status_item`),
				  KEY `id_usuario_inclusao` (`id_usuario_inclusao`,`id_usuario_ultima_alteracao`),
				  KEY `id_usuario_inclusao_2` (`id_usuario_inclusao`),
				  KEY `id_usuario_ultima_alteracao` (`id_usuario_ultima_alteracao`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
		return $this->conn->exec($stmt);
	
	}
	
	protected function CreateLogPropostasTable(){
	
		echo "Criando a tabela log_ptopostas...<br />\r\n";
	
		$stmt = "DROP TABLE IF EXISTS `log_propostas`;
				CREATE TABLE IF NOT EXISTS `log_propostas` (
				  `id_log` int(11) NOT NULL AUTO_INCREMENT,
				  `propostas_id_proposta` int(11) NOT NULL,
				  `dados_log` text,
				  PRIMARY KEY (`id_log`),
				  KEY `fk_log_propostas_propostas1` (`propostas_id_proposta`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
	
		return $this->conn->exec($stmt);
	
	}
	
	protected function CreateNotasExportacaoTable(){
	
		echo "Criando a tabela notas_exportacao...<br />\r\n";
	
		$stmt = "DROP TABLE IF EXISTS `notas_exportacao`;
				CREATE TABLE IF NOT EXISTS `notas_exportacao` (
				  `id_nota_exportacao` int(11) NOT NULL AUTO_INCREMENT,
				  `nota` char(1) NOT NULL,
				  `valor_minimo` float(10,2) NOT NULL,
				  `valor_maximo` float(10,2) NOT NULL,
				  PRIMARY KEY (`id_nota_exportacao`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
		return $this->conn->exec($stmt);
	
	}
	
	protected function CreateNotasImportacaoTable(){
	
		echo "Criando a tabela notas_importacao...<br />\r\n";
	
		$stmt = "DROP TABLE IF EXISTS `notas_importacao`;
				CREATE TABLE IF NOT EXISTS `notas_importacao` (
				  `id_nota_importacao` int(11) NOT NULL AUTO_INCREMENT,
				  `nota` char(1) NOT NULL,
				  `valor_minimo` float(10,2) NOT NULL,
				  `valor_maximo` float(10,2) NOT NULL,
				  PRIMARY KEY (`id_nota_importacao`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
		return $this->conn->exec($stmt);
	
	}
	
	protected function CreatePortosAcordosTaxas(){
	
		echo "Criando a tabela portos_x_acordos_taxas_globais...<br />\r\n";
	
		$stmt = "DROP TABLE IF EXISTS `portos_x_acordos_taxas_globais`;
				CREATE TABLE IF NOT EXISTS `portos_x_acordos_taxas_globais` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `id_porto` int(11) NOT NULL,
				  `id_acordo` int(11) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `id_porto` (`id_porto`,`id_acordo`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
		return $this->conn->exec($stmt);
	
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
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;";
	
		return $this->conn->exec($stmt);
	
	}
	
	protected function CreateStatusItensPropostasTable(){
	
		echo "Criando a tabela status_itens_propostas...<br />\r\n";
	
		$stmt = "DROP TABLE IF EXISTS `status_itens_propostas`;
				CREATE TABLE IF NOT EXISTS `status_itens_propostas` (
				  `id_status_item` int(11) NOT NULL AUTO_INCREMENT,
				  `status` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id_status_item`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
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
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;";
	
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
				) ENGINE=MyISAM AUTO_INCREMENT=417 DEFAULT CHARSET=latin1 ;";
	
		return $this->conn->exec($stmt);
	
	}
	
	protected function CreateTaxasAcordosTaxasLocais(){
	
		echo "Criando a tabela taxas_x_acordos_taxas_locais_globais...<br />\r\n";
	
		$stmt = "DROP TABLE IF EXISTS `taxas_x_acordos_taxas_locais_globais`;
				CREATE TABLE IF NOT EXISTS `taxas_x_acordos_taxas_locais_globais` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `id_taxa_adicional` int(11) DEFAULT NULL,
				  `id_unidade` int(11) DEFAULT NULL,
				  `id_moeda` int(11) DEFAULT NULL,
				  `valor` float(10,2) DEFAULT NULL,
				  `valor_minimo` float(10,2) DEFAULT NULL,
				  `valor_maximo` float(10,2) DEFAULT NULL,
				  `id_acordos_taxas_locais` int(11) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  KEY `id_taxa_adicional` (`id_taxa_adicional`,`id_unidade`,`id_moeda`,`id_acordos_taxas_locais`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
				SET FOREIGN_KEY_CHECKS=1;";
	
		return $this->conn->exec($stmt);
	
	}	
	
}//END CLASS