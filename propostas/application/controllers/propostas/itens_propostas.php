<?php
if( ! isset($_SESSION) )
{    
    session_start();
}  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* @package  propostas
* @author Wellington Feitosa <wellington.feitosao@allink.com.br>
* @copyright Allink Transporte Internacionais LTDA. - 19/02/2013
* @version  1.0
* Classe que manupula a sessão do PHP para a inclusão de itens das propostas a serem salvos
*/
 

class Itens_Propostas extends CI_Controller {


	public function __construct()
	{

		parent::__construct();
		$this->load->library("Scoa/url");
        $this->load->model("Adaptadores/sessao");
	}
		
	public function incluirItemSessao($id_tarifario = "NULL", $mercadoria = "", $pp = NULL, $cc = NULL, $validade = NULL, $peso = NULL, $cubagem = NULL, $volumes = NULL, 
									  $origem = NULL, $embarque = NULL, $desembarque = NULL, $destino = NULL,
									  $un_origem = NULL, $un_embarque = NULL, $un_desembarque = NULL, $un_destino = NULL,
									  $frete_adicionais = NULL, $labels_fretes_adicionais = NULL, $taxas_locais = NULL, $labels_taxas_locais = NULL, 
									  $observacao_interna = "", $observacao_cliente = "", $id_item = NULL, $anti_cache = NULL)
	{
        
        $sessao  = new Sessao();
        
        if( $id_item == "null" || $id_item == "NULL" )
        {
            $id_item = NULL;
        }    
        
        $sessao
        ->setIdItem($id_item)        
        ->setCc($cc)
        ->setPp($pp)
        ->setPeso((float)$peso)
        ->setCubagem((float)$cubagem)
        ->setVolumes((int)$volumes)
        ->setOrigem($origem)
        ->setEmbarque($embarque)
        ->setDesembarque($desembarque)
        ->setDestino($destino)
        ->setUnOrigem($un_origem)
        ->setUnEmbarque($un_embarque)
        ->setUnDesembarque($un_desembarque)
        ->setUnDestino($un_destino)        
        ->setIdTarifario((int)$id_tarifario)
        ->setMercadoria($mercadoria)
        ->setObservacaoCliente($observacao_cliente)
        ->setObservacaoInterna($observacao_interna)
        ->setLabelsFretesAdicionais($labels_fretes_adicionais)
        ->setLabelsTaxasLocais($labels_taxas_locais)
        ->setFreteAdicionais($frete_adicionais)
        ->setTaxasLocais($taxas_locais)
        ->setValidade($validade)
        ->setAntiCache($anti_cache);        
        
        $id_item_sessao = $sessao->inserirItemNaSessao();
	
		$data["id_item_sessao"] = $id_item_sessao;
		
		$this->load->view("propostas/xml_item",$data);
		
	}//END FUNCTION
	
	public function excluirItemsessao( $itemIndex )
	{
		
		$sessao = new Sessao();
        
        $sessao->setIdItem($itemIndex);
        
        /** se o id do item estiver preenchido então exclui o item da base de dados **/
        if( ! is_null($_SESSION['itens_proposta'][$itemIndex]["id_item"]) )
        {
            $this->load->model("Propostas/item_proposta_model");
            $this->load->model("Propostas/item_proposta");
            
            $item = new Item_Proposta();
            $item->setId($_SESSION['itens_proposta'][$itemIndex]["id_item"]);
            
            $item_model = new Item_Proposta_Model();
            
            $item_model->excluirItemDaPropostaPeloIdDoItem($item);
            
        }    
        
        $sessao->excluirItemDaSessao();
        					
	}//END FUNCTION
	
	public function recuperarItemSessao( $itemIndex )
	{		
		
        $sessao = new Sessao();
        
        $sessao->setIdItem($itemIndex);
        
        $item_selecionado = $sessao->recuperarItemDaSessao($itemIndex);
		
        /** Encoda todos os itens que estavam na sessão de volta para iso **/
        foreach( $item_selecionado as $index=>$item )
        {
        	 $item_selecionado[$index] = utf8_decode($item);
        }	
		
		$data['id_item'] = $itemIndex;
		$data['item'] = $item_selecionado;
		$data['erro'] = $sessao->getErro();
		
		$this->load->view("propostas/recuperar_item_xml",$data);
		
	}
	
}//END CLASS