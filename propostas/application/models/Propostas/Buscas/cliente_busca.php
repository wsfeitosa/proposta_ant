<?php
/**
 * Realiza a busca pelo nome do cliente
 *
 * Realiza a busca pelo nome do cliente e implementa a interface de buscas
 * 
 * @package Propostas/Buscas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - date
 * @name Cliente_Busca
 * @version 1.0
 */

class Cliente_Busca extends CI_Model implements Busca {
    
    public function __construct() {
        parent::__construct();
        $this->load->model("Clientes/cliente");
        $this->load->model("Clientes/cliente_model");
    }
    
    public function buscar($razao, $sentido)
    {   
        
        /** Seleciona todos os clientes que tem este trecho de texto no nome **/
        $cliente_model = new Cliente_Model();
        
        $clientes_encontrados = $cliente_model->findByName($razao);
        
        $propostas_encontradas = Array();
        
        if(count($clientes_encontrados) < 1 )
        {
            return $clientes_encontrados;
        }    
        
        $this->db->
                select("itens_proposta.*,propostas.sentido, propostas.tipo_proposta, propostas.id_proposta")->
                from("CLIENTES.itens_proposta")->
                join("CLIENTES.propostas", "propostas.id_proposta = itens_proposta.id_proposta")->
                join("CLIENTES.clientes_x_propostas", "clientes_x_propostas.id_proposta = propostas.id_proposta")->
                where_in("clientes_x_propostas.id_cliente", $this->converteObjetoClienteEmArray($clientes_encontrados))->
                group_by("propostas.id_proposta");
         
        $rs = $this->db->get();
                
        $proposta_model = new Proposta_Model();
        
        foreach ($rs->result() as $proposta) 
        {
            
            $proposta_encontrada = Proposta_Factory::factory($proposta->tipo_proposta);
            
            $proposta_encontrada->setId($proposta->id_proposta);
            
            $proposta_model->buscarPropostaPorId($proposta_encontrada);
            
            $propostas_encontradas[] = $proposta_encontrada;
            
        }
                        
        return $propostas_encontradas;
                       
    }        
    
    /**
     * converteObjetoClienteEmArray
     * 
     * Converte objetos do tipo cliente em um array formatado para
     * ser utilizado em clausula where_in() do active record
     * 
     * @name converteObjetoClienteEmArray
     * @access public
     * @param Array $clientes
     * @return Array $clientes_formatados
     */
    protected function converteObjetoClienteEmArray(Array $clientes)
    {
        
        if( !is_array($clientes) )
        {
            throw new InvalidArgumentException("Um Array de objetos do tipo cliente é esperado pela função para serializar os clientes!");
        }    
        
        $clientes_formatados = Array();
        
        foreach ($clientes as $cliente) 
        {
            
            $clientes_formatados[] = $cliente->getId();
            
        }
        
        return $clientes_formatados;
        
    }
    
}//END CLASS


