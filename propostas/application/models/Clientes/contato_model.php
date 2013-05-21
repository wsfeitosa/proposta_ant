<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Contato Model
 *
 * Classe que contém as regras de negócio da entidade contato
 *
 * @package Clientes
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 29/01/2013
 * @name Contato_Model
 * @version 1.0
 */
include_once "contato.php";
include_once APPPATH."/models/Email/email.php";

class Contato_Model extends DataMapper{
	
	public $db_params = "clientes";
	public $table = "contato";
	public $primary_key = "id_contato";
	/**
	public $has_one = array(
							'cliente' => array(			
												'class' => 'cliente_model',
												'other_field' => 'contato',
												'join_self_as' => 'id_cliente',
												'join_other_as' => 'id_cliente',
												'join_table' => 'clientes')	
	);
	**/
	public $has_one = array("cliente_model");
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Executa uma operação qualquer quando o objeto é criado
	 * @param boollean $from_cache
	 */
	public function post_model_init($from_cache = FALSE)
	{
	
	}
	
	public function findByIdCliente( $id_cliente = NULL )
	{
		
		if( empty($id_cliente) )
		{
			log_message("Id do cliente invalido para buscar o contato!");
			throw new RuntimeException("Id do cliente invalido para buscar o contato!");
		}	
		
		$this->where("id_cliente",$id_cliente)->get();
		
		$contatos = Array();
		
		foreach( $this as $row )
		{
			$contato = new Contato();
			
			$contato->setId($row->id_contato);
			$contato->setNome($row->contato);
			
			/** Cria um novo objeto do tipo email **/
			$email = new Email();
			$email->setEmail($row->email);
			
			$contato->setEmail($email);
			
			$contatos[] = $contato;
			
		}	
		
		return $contatos;
		
	}
    
    /**
     * findByIdContato
     * 
     * Faz a busca pelos dados do contato através do ID do contato
     * 
     * @name findByIdContato
     * @access public
     * @param Contato $contato
     * @return Contato
     */
    public function findById(Contato $contato)
    {
        
        $id = $contato->getId();
        
        if( empty($id) )
        {
            log_message("Contato inválido para realizar a busca!");
            show_error("Contato inválido para realizar a busca!");
        }    
        
        $this->where("id_contato",$contato->getId())->get();
                		
		foreach( $this as $row )
		{			
			$contato->setId($row->id_contato);
			$contato->setNome($row->contato);
			
			/** Cria um novo objeto do tipo email **/
			$email = new Email();
			$email->setEmail($row->email);
			
			$contato->setEmail($email);
					
		}	
		
		return $contato;
        
    }        
       
}//END CLASS