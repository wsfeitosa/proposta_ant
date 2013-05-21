<?php
class CreateProject {

	protected $source;
	protected $destination;
	
	public function __construct( $destination = NULL ){
		
		if( is_null($destination) )
		{
			die("Você deve informar o local de destino da aplicação!");
		}	
		
		$this->destination = $destination;		
		$this->source = dirname(__FILE__) . "/propostas/";
		
	}
	
	public function gerar()
	{
		$this->gerarProjeto($this->source, $this->destination);
		system("chmod 777 -R ".$this->destination);
	}
	
	protected function gerarProjeto( $source, $dest )
	{
	
		// COPIA UM ARQUIVO
		if (is_file($source))
		{
			$copy = copy($source, $dest);
			return $copy;
		}
	
		// CRIA O DIRETÓRIO DE DESTINO
		if (!is_dir($dest))
		{
			mkdir($dest);
			echo "DIRET&Oacute;RIO $dest CRIADO<br />";
		}
	
		// FAZ LOOP DENTRO DA PASTA
		$dir = dir($source);
		while (false !== $entry = $dir->read()) 
		{
			// PULA "." e ".."
			if ($entry == '.' || $entry == '..') 
			{
				continue;
			}

			// COPIA TUDO DENTRO DOS DIRETÓRIOS
			if ($dest !== "$source/$entry") 
			{
				$this->gerarProjeto("$source/$entry", "$dest/$entry");
				echo "COPIANDO $entry de $source para $dest <br />";
			}
		}
						
		$dir->close();
		return true;
	
	}//END FUNCTION
	
}