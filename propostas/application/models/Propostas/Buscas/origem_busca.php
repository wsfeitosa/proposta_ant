<?php
/**
 * Realiza a busca pela origem da proposta
 *
 * Realiza a busca pela origem da proposta e implementa a interface de buscas
 * 
 * @package Propostas/Buscas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - date
 * @name Origem_Busca
 * @version 1.0
 */

class Origem_Busca extends CI_Model implements Busca {
    
    public function __construct() {
        parent::__construct();
        $this->load->model("Tarifario/Factory/factory");
        $this->load->model("Tarifario/Factory/concrete_factory");
    }
    
    public function buscar($origem, $sentido)
    {
        
    	$fabrica = Factory::factory(strtolower($sentido));
    	
    	$fabrica_concreta = new Concrete_Factory();
    	
    	$porto_model = $fabrica_concreta->CreatePortoModel($fabrica);
    	
    	$tarifario = $fabrica_concreta->CreateTarifarioObject($fabrica);
    	
    	$tarifario_model = $fabrica_concreta->CreateTarifarioModel($fabrica);
    	
    	pr($porto_model);
    	
    	pr($tarifario);
    	
    	pr($tarifario_model);    	
    	
    }        
    
}


