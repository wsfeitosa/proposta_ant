<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Cliente Model
 *
 * Classe que contém as regras de negócio da entidade cliente
 *
 * @package Clientes
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 29/01/2013
 * @name Cliente_Model
 * @version 1.0
 */
include_once "cliente.php";
include_once "cidade.php";
include_once "cidade.php";
include_once APPPATH."/models/Email/email.php";
include_once "contato.php";

class Cliente_Model extends CI_Model{
			
	public function __construct()
	{
		parent::__construct();
	}
	
	public function findByName($name = NULL)
	{
				
		if( empty($name) )
		{
			throw new Exception("O Nome do cliente deve ser uma String Válida!");
		}	
		
		//$this->like("razao",urldecode($name))->where("ativo","S")->get();
		
        $this->db->select("clientes.*")->
                   from("CLIENTES.clientes")->
                   like("razao", utf8_decode(urldecode($name)))->
                   where("ativo", "S");
        
        $rs = $this->db->get();
        
		$clientes = Array();
		
		foreach( $rs->result() as $cliente )
		{
			try{
				$objCliente = New Cliente();
				
				$objCliente->setId((int)$cliente->id_cliente);			
				$objCliente->setRazao($cliente->razao);			
				$objCliente->setCNPJ($cliente->cnpj);
				$objCliente->setBairro($cliente->bairro);
				$objCliente->setEndereco($cliente->endereco);
				$objCliente->setNumero((int)$cliente->numero);
				$objCliente->setEstado($cliente->estado);
				
				$cidade = new Cidade();
				
				$cidade->setId((int)$cliente->cidade);
				
				if( ! $cidade->findById() )
				{	
					throw new RuntimeException("A cidade procurada não existe!");
				}
					
				$objCliente->setCidade($cidade);
				
				$clientes[] = $objCliente;	
								
			} catch (Exception $e) {				
				log_message('error',$e->getMessage());
				show_error($e->getMessage());
			}			
		}	
		
		return $clientes;
				
	}//END FUNCTION
    
    /**
     * findById
     * 
     * Busca os cliente baseado no id do cliente
     * 
     * @name findById
     * @access public
     * @param Cliente $cliente
     * @return void
     */
    public function findById(Cliente $cliente) 
    {
        
        $id_cliente = $cliente->getId();
        
        if( empty($id_cliente) )
        {
            throw new InvalidArgumentException("Id do cliente informado é invalido para realizar a consulta!");
        }    
        
        $this->db->select("clientes.*")->
                   from("CLIENTES.clientes")->
                   where("id_cliente", $cliente->getId())->
                   where("ativo", "S");
        
        $rs = $this->db->get();
        
        if( $rs->num_rows() < 1 )
        {
            throw new RuntimeException("Id do cliente não encontrado na base de dados");
        }    
        
        $cliente_encontrado = $rs->row();
        
        $cliente->setId((int) $cliente_encontrado->id_cliente);			
        $cliente->setRazao($cliente_encontrado->razao);			
        $cliente->setCNPJ($cliente_encontrado->cnpj);
        $cliente->setBairro($cliente_encontrado->bairro);
        $cliente->setEndereco($cliente_encontrado->endereco);
        $cliente->setNumero((int) $cliente_encontrado->numero);
        $cliente->setEstado($cliente_encontrado->estado);
        $cliente->setClassificacao($cliente_encontrado->id_classificacao);

        $cidade = new Cidade();

        $cidade->setId((int)$cliente_encontrado->cidade);

        if( ! $cidade->findById() )
        {	
            throw new RuntimeException("A cidade procurada não existe!");
        }

        $cliente->setCidade($cidade);        
        
    }//END FUNCTION
    
    /**
     * findByIdDaProposta
     * 
     * Busca os clientes que estão vinculados a uma proposta
     * 
     * @name findByIdDaProposta
     * @access public
     * @param Proposta $proposta
     * @return void
     */
    public function findByIdDaProposta(Proposta $proposta) 
    {
        
        $id_proposta = $proposta->getId();
        
        if( empty($id_proposta) )
        {
            throw new InvalidArgumentException("Id da proposta inválido para efetuar a busca pelos clientes!");
        }    
        
        $this->db->select("clientes_x_propostas.id_cliente")->
                   from("CLIENTES.clientes_x_propostas")->
                   where("clientes_x_propostas.id_proposta",$proposta->getId());
        
        $rs = $this->db->get();
        
        if( $rs->num_rows() < 1 )
        {
            throw new RuntimeException("Nenhum cliente encontrado para a Proposta :" . $proposta->getNumero());
        }    
        
        foreach ($rs->result() as $cliente_encontrado) 
        {
            
            $cliente = new Cliente();            
            $cliente->setId((int) $cliente_encontrado->id_cliente);
            $this->findById($cliente);
            
            $proposta->adicionarNovoCliente($cliente);
            
        }
        
        
    }//END FUNCTION
    
    /**
     * salvarCLienteProposta
     * 
     * Salva todos os cliente relacionados a uma proposta
     * 
     * @name salvaClienteProposta
     * @access public
     * @param Proposta $proposta
     * @return boolean
  */
    public function salvarClienteProposta( Proposta $proposta )
    {
        
        /** verifica se existem clientes para salvar **/
        if( count($proposta->getClientes()) < 1 )
        {
            log_message('error','Não existe cliente para ser salvo para está proposta!');
            show_error('Não existe cliente para ser salvo para está proposta!');
        }    
        
        /** verifica se o id da proposta está setado, ou seja se a proposta já foi salva **/
        $id_proposta = $proposta->getId();
        
        if( empty($id_proposta) )
        {
            log_message('error','A proposta ainda não foi salva, então não é possível relacionar os clientes!');
            show_error('A proposta ainda não foi salva, então não é possível relacionar os clientes!');
        }    
        
        foreach($proposta->getClientes() as $key => $cliente) 
        {
            $this->salvar($cliente, $id_proposta);
        }
        
    }        
	
    public function salvar( Cliente $cliente, $id_proposta = NULL )
    {
        
        $dados_para_salvar = Array(
                                   'id_cliente' => $cliente->getId(),
                                   'id_proposta' => $id_proposta            
        );
        
        $rs = $this->db->insert("CLIENTES.clientes_x_propostas",$dados_para_salvar);
        
        return $rs;
        
    }        
    
    
    /**
     * excluirClientesPeloIdDaProposta
     * 
     * Exclui todos clientes que estão vinculados a uma proposta, fazendo a busca
     * pelo id da proposta
     * 
     * @name excluiClientesPeloIdDaProposta
     * @access public
     * @param Proposta $proposta
     * @return boolean
     */
    public function excluiClientesPeloIdDaProposta(Proposta $proposta) 
    {
       
        $id_proposta = $proposta->getId();
        
        if( empty($id_proposta) )
        {
            throw new InvalidArgumentException("O id da proposta não foi definido para realizar a exclusão dos clientes relacionados!");
        }    
        
        /** Seleciona todos os clientes relacionados a aquela proposta **/
        $this->db->
                select("clientes_x_propostas.*")->
                from("CLIENTES.clientes_x_propostas")->
                where("clientes_x_propostas.id_proposta",$proposta->getId());
        
        $rs = $this->db->get();
        
        if( $rs->num_rows() < 1 )
        {
            return FALSE;
        } 
        
        $clientes_relacionados = $rs->result();
        
        /** Exclui todos os cliente relacionados **/
        foreach ($clientes_relacionados as $cliente_relacionado) 
        {
            $this->db->delete("CLIENTES.clientes_x_propostas", Array("id" => $cliente_relacionado->id));
        }
        
        return TRUE;
        
    }//END FUNCTION
    
    /** 
      * verificarModalidadeDosClientes
      * 
      * Verifica se todos os clientes informados tem a mesma modalidade (Direto ou Forwarder)
      * 
      * @name verificarModalidadeDosClientes
      * @access public
      * @param string $ids_dos_clientes
      * @return boolean
      * @throws InvalidArgumentException
      * @throws UnexpectedValueException
      */
    public function verificarModalidadeDosClientes( $ids_clientes_selecionados = "" )
    {
    	
    	if( empty( $ids_clientes_selecionados ) )
    	{
    		throw new InvalidArgumentException("Nenhum cliente foi informado para realizar a validação das modalidades");
    	}	
    	
    	/** explode os clientes que devem estar separados pelo caractere : **/
    	$pilha_clientes_informados = explode(":", $ids_clientes_selecionados);
		   	
    	if( ! is_array($pilha_clientes_informados) || count($pilha_clientes_informados) < 1 )
    	{
    		throw new UnexpectedValueException("Não foi possivel realizar a comparação entre às modalidades dos clientes!");
    	}	
    	    	
    	$clientes_para_comparacao = 0;
    	
    	foreach( $pilha_clientes_informados as $id_cliente )
    	{
    		
    		if( $id_cliente == "" )
    		{
    			continue;
    		}	
    		
    		$cliente = new Cliente();
    		
    		$cliente->setId((int)$id_cliente);
    		
    		$this->findById($cliente);

    		if( $clientes_para_comparacao == 0 )
    		{
    		 	$clientes_para_comparacao = $cliente->getClassificacao();
    		}	
    		else
    		{
    			
    			if( $clientes_para_comparacao != $cliente->getClassificacao() )
    			{
    				return FALSE;
    			}	
					    			
    		}	    		    		   		
    	}	
    	
    	return TRUE;
    	
    }
}//END CLASS