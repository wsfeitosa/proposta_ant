<?php
/**
 * Realiza a busca pelo destino 
 *
 * Realiza a busca pelo destino e implementa a interface de buscas
 * 
 * @package Propostas/Buscas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - date
 * @name Destino_Busca
 * @version 1.0
 */

class Destino_Busca extends CI_Model implements Busca {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function buscar($destino, $sentido)
    {
        echo get_class($this);
    }        
    
}


