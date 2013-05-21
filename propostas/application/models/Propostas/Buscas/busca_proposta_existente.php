<?php
/**
 * Busca_Proposta_Existente
 *
 * Esta classe verifica se já existe algum item de proposta cadastrado para determinado cliente.
 *
 * @package Propostas/Buscas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 04/04/2013
 * @name Busca_Proposta_Existente
 * @version 1.0
 */
//include_once APPPATH . "/models/Propostas/Buscas/finder.php";
include_once "/var/www/html/allink/Clientes/propostas/application/models/Propostas/Buscas/finder.php";

class Busca_Proposta_Existente extends CI_Model implements Finder {
	
	public function __construct(){
		parent::__construct();		
	}
	
	/**
	  * verificarSeClienteJaPossuiPropostaValida
	  * 
	  * verifica se existe alguma proposta valida para um determinado cliente em uma
	  * determinada rota (Tarifário)
	  * 
	  * @name verificarSeClienteJaPossuiPropostaValida
	  * @access public
	  * @param Cliente $cliente
	  * @param Tarifario $tarifario
	  * @return boolean
	  */
	public function verificarSeClienteJaPossuiPropostaValida( Cliente $cliente, Tarifario $tarifario )
	{
		
		$this->db->
				select("clientes_x_propostas.*")->
				from("CLIENTES.clientes_x_propostas")->
				join("CLIENTES.itens_proposta","itens_proposta.id_proposta = clientes_x_propostas.id_proposta")->
				join("CLIENTES.propostas","itens_proposta.id_proposta = propostas.id_proposta")->
				where("id_tarifario_pricing",$tarifario->getId())->
				where("id_cliente",$cliente->getId());

		$rs = $this->db->get();

		if( $rs->num_rows() < 1 )
		{
			return FALSE;
		}	
		else
		{
			return TRUE;
		}	
				
	}
    
    /**
	  * verificarSeClienteJaPossuiPropostaValidaERetornaId
	  * 
	  * verifica se existe alguma proposta valida para um determinado cliente em uma
	  * determinada rota (Tarifário), e se houver retorna o id da proposta
	  * 
	  * @name verificarSeClienteJaPossuiPropostaValidaERetornaId
	  * @access public
	  * @param Cliente $cliente
	  * @param Tarifario $tarifario
	  * @return boolean
	  */
	public function verificarSeClienteJaPossuiPropostaValidaERetornaId( Cliente $cliente, Tarifario $tarifario )
	{
		
		$this->db->
				select("clientes_x_propostas.*, itens_proposta.id_item_proposta")->
				from("CLIENTES.clientes_x_propostas")->
				join("CLIENTES.itens_proposta","itens_proposta.id_proposta = clientes_x_propostas.id_proposta")->
				join("CLIENTES.propostas","itens_proposta.id_proposta = propostas.id_proposta")->
				where("id_tarifario_pricing",$tarifario->getId())->
				where("id_cliente",$cliente->getId());
        
		$rs = $this->db->get();
        
		if( $rs->num_rows() < 1 )
		{
			return FALSE;
		}	
		else
		{            
			return $rs->row()->id_item_proposta;
		}	
				
	}
	
    /**
     * buscaPorItensDuplicadosDeUmaNovaProposta
     * 
     * Busca por itens de uma proposta que já estejam cadastrados em outras propostas antes de salvar
     * 
     * @name buscaPorItensDuplicadosDeUmaNovaProposta
     * @access public
     * @param Proposta $proposta     
     * @return ArrayObject $itens_duplicados
     */
    public function buscaPorItensDuplicadosDeUmaNovaProposta(Proposta $proposta)
    {
        
        $clientes_da_proposta = $proposta->getClientes();
                        
        if(count($clientes_da_proposta) < 1)
        {
            $error_message = "Impossivel validar a proposta, nenhum cliente foi informado!";
            log_message('error',$error_message);
            throw new Exception($error_message);
        }
        
        $itens_da_proposta = $proposta->getItens();
        
        if(count($itens_da_proposta) < 1)
        {
            $error_message = "Impossivel validar a proposta, nenhum item informado!";
            log_message('error',$error_message);
            throw new Exception($error_message);
        }
        
        $itens_duplicados = new ArrayObject(Array());
        
        foreach ($clientes_da_proposta as $cliente)
        {
            
            foreach ($proposta->getItens() as $item)
            {
                $id_item_excluir = $this->verificarSeClienteJaPossuiPropostaValidaERetornaId($cliente, $item->getTarifario());
                
                if( $id_item_excluir !== FALSE && $item->getId() != $id_item_excluir )
                {
                    $itens_duplicados->append($id_item_excluir);
                }    
                
            }
            
        }
        
        return $itens_duplicados;
        
    }
    
}//END LASS