<?php
include_once APPPATH . "/models/Adaptadores/conversor.php";

class Array_Conversor extends Conversor{
	
	public function converter(Array $array_de_objetos, Array $parametros)
	{
		
		if( count($array_de_objetos) < 1 || count($parametros) < 1 )
		{
			throw new InvalidArgumentException("Dados informados para a convesão do objeto em Array insulficientes!");
		}	
		
		$objetos_convertidos = Array();
		
		$value = "get".ucwords($parametros['value']);
		$label = "get".ucwords($parametros['label']);
						
		foreach( $array_de_objetos as $objeto )
		{
			
			/** Exceção para classe de clientes **/
			if( $objeto instanceof Cliente  )
			{
				$objetos_convertidos[$objeto->$value()] = $objeto->getCNPJ() . " - " . $objeto->getRazao();
			}
			else
			{
				$objetos_convertidos[$objeto->$value()] = $objeto->$label();
			}	
						
		}	
		
		return $objetos_convertidos;
		
	}//END FUNCTION
		
}//END CLASS