<?php
/**
 * Classe abstrata que representa a entidade Tarifario
 * 
 * Esta é uma classe abstrata que representa o tarifario e de onde as 
 * classes concretas Tarifario_Importacao e Tarifario_Exportacao herdam
 * o seus métodos e caracteristicas
 * 
 * @package Tarifario
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 14/01/2013
 * @name Tarifario
 * @version 1.0
 * @abstract
 */
abstract class Tarifario{
	
	private $id;
	private $inicio;
	private $validade;
	private $rota;
	private $sentido;
	private $taxas;
	private $observacao;
	
	public function __construct( Rota $rota = NULL )
	{
		$this->rota = $rota;
	}
	
	/**
	  * Adicionar Nova Rota
	  * 
	  * Esta função adiciona uma nova rota ao tarifário
	  * 
	  * @name setRota
	  * @access public
	  * @param Rota
	  * @return boolean
	  */
	public function setRota( Rota $rota )
	{
		
		if( empty($rota) )
		{
			return FALSE;
		}	
		
		$this->rota = $rota;
		
		return TRUE;
		
	}
	
	/**
	 * Obter Rota
	 *
	 * Esta obtem um objeto rota do tarifário
	 *
	 * @name getRota
	 * @access public
	 * @param 
	 * @return Rota
	 */
	public function getRota()
	{
		return $this->rota;
	}
	
	/**
	 * Set ID
	 *
	 * Atribui um ID para o tarifário
	 *
	 * @name setId
	 * @access public
	 * @param Int
	 * @return boolean
	 */
	public function setId( $id = NULL )
	{
		
		if( empty($id) || ! is_integer($id) )
		{
			return FALSE;
		}	
		
		$this->id = (int)$id;
		
		return TRUE;
	}
	
	/**
	 * Get ID
	 *
	 * Obtem o id do tarifario
	 *
	 * @name getId
	 * @access public
	 * @param 
	 * @return int
	 */
	public function getId()
	{
		return (int)$this->id;
	}
	
	/**
	 * Set Sentido
	 *
	 * Atribui um sentido (IMP ou EXP) para o tarifário
	 *
	 * @name setSentido
	 * @access public
	 * @param String
	 * @return boolean
	 */
	public function setSentido( $sentido )
	{
		
		if( $sentido != "IMP" && $sentido != "EXP" )
		{
			log_message('error','Modalidade invalida informada ao tarifario');
			throw new Exception("Modalidade invalida informada ao tarifario, precisa ser IMP ou EXP");
		}	
		
		$this->sentido = $sentido;
		
		return TRUE;
		
	}
	
	/**
	 * Get Sentido
	 *
	 * Obtem o sentido do tarifario
	 *
	 * @name getSentido
	 * @access public
	 * @param
	 * @return String
	 */
	public function getSentido()
	{
		return $this->sentido;
	}
	
	/**
	 * Set Inicio
	 *
	 * Atribui um valor (do tipo Date) de inicio para o tarifário
	 *
	 * @name setInicio
	 * @access public
	 * @param Date
	 * @return boolean
	 */
	public function setInicio( $inicio = NULL )
	{
		
		if( empty($inicio) )
		{
			return FALSE;
		}	
		
		$this->inicio = $inicio;
		
		return TRUE;
		
	}
	
	/**
	 * Get Inicio
	 *
	 * Obtem a data inicial do tarifario
	 *
	 * @name getInicio
	 * @access public
	 * @param
	 * @return Date
	 */
	public function getInicio()
	{
		return $this->inicio;
	}
	
	/**
	 * Set Validade
	 *
	 * Atribui um valor (do tipo Date) de validade para o tarifário
	 *
	 * @name setValidade
	 * @access public
	 * @param Date
	 * @return boolean
	 */
	public function setValidade( DateTime $validade = NULL )
	{
	
		if( empty($validade) )
		{
			return FALSE;
		}
	
		$this->validade = $validade;
	
		return TRUE;
	
	}
	
	/**
	 * Get Validade
	 *
	 * Obtem a data de validade do tarifario
	 *
	 * @name getValidade
	 * @access public
	 * @param
	 * @return Date
	 */
	public function getValidade()
	{
		return $this->validade;
	}
	
	/**
	  * Adicionar Nova Taxa
	  * 
	  * Adiciona uma nova taxa ao tarifário
	  * 
	  * @name adicionarNovaTaxa
	  * @access public
	  * @param Taxa
	  * @return int
	  */
	public function adicionarNovaTaxa( $taxa )
	{
		
		if( ! $taxa instanceof Taxa )
		{
			log_message('error',"Objeto incopativel com o tipo Taxa passado a classe tarifario");
			throw new Exception("Objeto incopativel com o tipo Taxa passado a classe tarifario");
		}

		$this->taxas[] = $taxa;
		
		end($this->taxas);
		
		return key($this->taxas);
		
	}
	
	/**
	 * Get Taxa
	 *
	 * Obtem as taxa do tarifario, se informado o indice,
	 * então retorna a taxa especifica, se não retorna um array com todas
	 * às taxas
	 *
	 * @name getTaxa
	 * @access public
	 * @param 
	 * @return Array
	 */
	public function getTaxa($index = NULL)
	{
		
		if( is_null($index) )
		{
			return $this->taxas;
		}

		return $this->taxas[$index];
		
	}
	
	/**
	  * Remover Taxa
	  * 
	  * Remove uma taxa do tarifario
	  * 
	  * @name removerTaxa
	  * @access public
	  * @param int
	  * @return boolean
	  */
	public function removerTaxa( $index )
	{
		
		if( ! is_integer($index) || ! array_key_exists($index, $this->taxas) )
		{
			return FALSE;
		}	
		
		unset($this->taxas[$index]);
		
		return TRUE;
		
	}
    
    /**
     * obterTodasAsTaxas
     * 
     * Retorna todas às taxas que estão atribuidas a ao item
     * 
     * @name obterTodasAsTaxas
     * @access public     
     * @return array
     */
    public function obterTodasAsTaxas() 
    {
        return $this->taxas;
    }
    
    /**
     * limparTaxasTarifario
     * 
     * Limpa todas às taxas que estão atribuidas à aquele objeto tarifário
     *  
     * @name limparTaxasTarifario
     * @access public
     * @param Tarifario $tarifario
     * @return void
     */
    public function limparTaxasTarifario()
    {
        $this->taxas = Array();        
    }   

    /**
      * setObservacao
      * 
      * Atribui uma observacao ao tarifario
      * 
      * @name setObservacao
      * @access public
      * @param string $observacao
      * @return Tarifario $this
      */
    public function setObservacao( $observacao )
    {
    	$this->observacao = $observacao;
    }
    
    /**
      * getObservacao
      * 
      * Obtem a observacao do tarifário que foi atribuida a classe
      * 
      * @name getObservacao
      * @access public     
      * @return string $observacao
      */
    public function getObservacao()
    {
    	return $this->observacao;
    }
	
}//END CLASS