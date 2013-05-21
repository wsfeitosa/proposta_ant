<?php
/**
 * Realiza a busca pelo número da proposta
 *
 * Realiza a busca pelo numero da proposta e implementa a interface de buscas
 * 
 * @package Tarifario
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - date
 * @name Tarifario
 * @version 1.0
 */

class Numero_Busca extends CI_Model implements Busca {
    
    public function __construct() {
        parent::__construct();
        $this->load->model("Propostas/proposta_factory");
        $this->load->model("Propostas/proposta_model");
    }
    
    public function buscar($numero, $sentido)
    {
        
        $this->db->
                select("itens_proposta.*,propostas.sentido, propostas.tipo_proposta, propostas.id_proposta")->
                from("CLIENTES.itens_proposta")->
                join("CLIENTES.propostas", "propostas.id_proposta = itens_proposta.id_proposta")->
                like("itens_proposta.numero_proposta", $numero)->
                group_by("propostas.id_proposta");
        
        $rs = $this->db->get();
        
        $propostas_encontradas = Array();
        
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
    
}


