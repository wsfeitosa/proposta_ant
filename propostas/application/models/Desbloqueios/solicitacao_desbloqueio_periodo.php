<?php
/**
 * Class Solicitacao_Desbloqueio_Peiodo
 *
 * Implementa os m�todos da interface Desbloqueios, possibilita a 
 * cria��o de solicita��es de desbloqueios para per�odos de validade.
 *
 * @package Desbloqueios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 22/01/2013
 * @name Solicitacao_Desbloqueio_Periodo
 * @version 1.0
 */
include_once "desbloqueios.php";

class Solicitacao_Desbloqueio_Periodo extends Desbloqueios {
	
	public function solicitarDesbloqueio()
	{
		/** aqui vai salvar na tabela no banco de dados **/
		return TRUE;
	}
	
}//END CLASS