<?php
/**
 * Classe que manipula e compõe a entidade envio do módulo de propostas
 * esta classe envia os emails do módulo de propostas
 * @author wsfall
 * @package Email
 * @name Envio
 */
class Envio {

	private $emails = Array();
	private $status = FALSE;
	
	public function __construct()
	{
		/** inclui a função do scoa que envia os emails **/
		include $_SERVER['DOCUMENT_ROOT']."/Libs/envia_msg.php";
	}
	
	/**
	 * Adiciona um novo email para ser enviado
	 * @name adicionarNovoEmail
	 * @access public
	 * @param Email $email
	 * @return Boolean
	 */
	public function adicionarNovoEmail( Email $email )
	{
		
		$this->emails[] = $email;
		
		end($this->emails);
		
		return key($this->emails);
		
	}
	
	/**
	  * Remove um email da classe
	  * @name removerEmail
	  * @access public
	  * @param index int
	  * @return Boolean
	  */
	public function removerEmail( $index )
	{
		
		unset($this->emails[$index]);
		
		return TRUE;
		
	}
	
	/**
	  * Retorna a quantidade de emails estão atualmente atribuidos a classe de envio
	  * @name obterQuantidadeEmails
	  * @access public
	  * @param 
	  * @return int
	  */
	public function obterQuantidadeEmails()
	{
		return count($this->emails);
	}
	
	/**
	  * Envia a mensagem contendo os emails que foram atribuidos a classe
	  * @name enviarmensagem
	  * @access public
	  * @param $corpoMensagem String
	  * @return boolean
	  */
	public function enviarMensagem( $corpoMensagem, $assunto ,$anexo = "", $nome_anexo = "")
	{
				
		/**
		 * Falta Implementar, terei de criar primeiro a classe usuario
		 * com os métodos para obter o usuario que está logado.
		 */
		
		$emails_serializados = $this->serializaEmails();
		
		$email_enviado = envia_email(
										"wellington.feitosa@allink.com.br",
										"Wellington", 
										$emails_serializados, "", "",
										"wellington.feitosa@allink.com.br",
										$assunto, $corpoMensagem, 
										$anexo, $nome_anexo 
		);
		
		return $email_enviado;
		
	}
	
	/**
	  * Serializa os email no formato correto para o envio
	  * @name serializarEmails
	  * @access protected
	  * @param  
	  * @return $emails_serializados String
	  */
	protected function serializaEmails()
	{
		
		if( ! is_array($this->emails) || count($this->emails) < 1 )
		{
			return FALSE;
		}	
		
		$emails_serializados = "";
		
		foreach( $this->emails as $email )
		{
			$emails_serializados .= $email->getEmail().";";
		}	
		
		/** Remove o ultimo caractere de ; do fim da String **/
		return substr($emails_serializados, 0, -1);
				
	}
		
}//END CLASS