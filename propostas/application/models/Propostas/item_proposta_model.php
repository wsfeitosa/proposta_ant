<?php
if( ! isset($_SESSION) )
{    
    session_start();
} 
 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* @package  propostas
* @author Wellington Feitosa <wellington.feitosao@allink.com.br>
* @copyright Allink Transporte Internacionais LTDA. - 27/02/2013
* @version  1.0
* Classe que manupula a sessão do PHP para a inclusão de itens das propostas a serem salvos
*/

class Item_Proposta_Model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->model("Taxas/taxa_model");
        $this->load->model("Clientes/cliente");
        $this->load->model("Propostas/status_item");        
        $this->load->model("Tarifario/Factory/concrete_factory");
        $this->load->model("Tarifario/Factory/concrete_importacao_factory");
        $this->load->model("Tarifario/Factory/concrete_exportacao_factory");
        $this->load->model("Tarifario/Factory/factory");
        $this->load->model("Propostas/Buscas/busca_proposta_existente");
    }
      
    /**
     * salvarItens
     * 
     * Salva os itens de uma proposta
     * 
     * @name salvarItem
     * @access public
     * @param $proposta Proposta
     * @return boolean
     */
     public function salvarItens( Proposta $proposta )
     {
         
         $model = new Taxa_Model();
         
         $finder = new Busca_Proposta_Existente();
         
         /** 
           * Verifica se já existe outra proposta para este cliente nesta rota,
           * caso houver, cancela o item da proposta encontrada antes de salvar o novo
           * para que não existam dois ativos ao mesmo tempo 
           */
         $itens_para_excluir = $finder->buscaPorItensDuplicadosDeUmaNovaProposta($proposta);           

         if( $itens_para_excluir->count() > 0 )
         {
             $iterator = $itens_para_excluir->getIterator();

             while( $iterator->valid() )
             {
                 $item_exclusao = new Item_Proposta();

                 $item_exclusao->setId($iterator->current());

                 $this->excluirItemDaPropostaPeloIdDoItem($item_exclusao);

                 $iterator->next();
             }    

         }    
         
         /** aplica a função a cada item da proposta **/
         foreach ($proposta->getItens() as $item) 
         {             
             $dados_para_salvar = Array(
                                        "id_proposta" => $proposta->getId(),
                                        "id_tarifario_pricing" => $item->getTarifario()->getId(),                                        
                                        "mercadoria" => strtoupper($item->getMercadoria()),
                                        "pp" => $item->getPp(),
                                        "cc" => $item->getCc(),
                                        "peso" => $item->getPeso(),
                                        "cubagem" => $item->getCubagem(),
                                        "volumes" => $item->getVolumes(),
                                        "observacao_interna" => strtoupper($item->getObservacaoInterna()),
                                        "observacao_cliente" => strtoupper($item->getObservacaoCliente()),
                                        "id_status_item" => 0, //FIXME aqui será informado o status do item                                                         
                                        "data_inicial" => $item->getInicio(),
                                        "validade" => $item->getValidade()
             );
                      
             /** Se o id da proposta estiver definido executa uma alteração, caso contrário executa a inclusão do item **/
             $id_item = $item->getId();
             
             if( empty($id_item) )
             {
                 //pr($proposta);die();
                 $dados_para_salvar["numero_proposta"] = $this->geraNovoNumeroDeItemDeProposta($proposta);
                 
                 $rs = $this->db->insert("CLIENTES.itens_proposta",$dados_para_salvar);
             
                 $id_do_item_salvo = $this->db->insert_id();
                 
                 $item->setId((int)$id_do_item_salvo);
                 
             }
             else
             {                 
                 $this->db->where("itens_proposta.id_item_proposta",$item->getId());
                 
                 $rs = $this->db->update("CLIENTES.itens_proposta",$dados_para_salvar);
                 
                 $id_do_item_salvo = $item->getId();       
                 
                 /** Exclui às taxas antigas antes da salvar às taxas novas **/
                 $model->exluirTaxasPorItemDeProposta($item);
             }    
                  
             /** Salva às taxas referentes à este item da proposta **/             
             foreach ($item->getTarifario()->obterTodasAsTaxas() as $taxa) 
             {
                 $model->salvarTaxa($taxa, $id_do_item_salvo);
             }
                          
         }         
                  
     }//END FUNCTION    
     
    /**
     * buscarItensPorIdDaProposta
     * 
     * Busca todos os itens de uma proposta pelo id da proposta
     * 
     * @name buscarItensPorIdDaProposta
     * @access public
     * @param Proposta $proposta
     * @return array
     */
     public function buscarItensPorIdDaProposta( Proposta $proposta ) 
     {
         
         /** Verifica se o id da proposta já foi definido **/
         $id_proposta = $proposta->getId();
         
         if( empty($id_proposta) )
         {
             throw new InvalidArgumentException("O id da proposta ainda não foi definido, não é possivel realizar a busca pelos itens da proposta!");
         }    
        
         $this->db->
                 select("itens_proposta.*")->
                 from("CLIENTES.itens_proposta")->
                 where("itens_proposta.id_proposta",$proposta->getId());
         
         $rs = $this->db->get();
         
         $itens_encontrados = Array();
         
         if( $rs->num_rows() < 1 )
         {
             return $itens_encontrados;
         }    
         
         /** Cria os objetos de tarifario necessários baseado no tipode proposta **/
         $factory = new Concrete_Factory();         
         
         $concrete_factory = Factory::factory($proposta->getSentido());
         
         foreach ( $rs->result() as $item_encontrado ) 
         {
             
             $tarifario = $factory->CreateTarifarioObject($concrete_factory);
             $tarifario_model = $factory->CreateTarifarioModel($concrete_factory);
             
             $tarifario->setId((int) $item_encontrado->id_tarifario_pricing);
             
             $tarifario_model->findById($tarifario);
                                       
             $item = new Item_Proposta($tarifario);
             
             $data_inicial = new DateTime($item_encontrado->data_inicial);
             $validade = new DateTime($item_encontrado->validade);
             
             $item->setId((int) $item_encontrado->id_item_proposta);
             $item->setNumero($item_encontrado->numero_proposta);
             $item->setMercadoria($item_encontrado->mercadoria);             
             $item->setPp($item_encontrado->pp);             
             $item->setCc($item_encontrado->cc);
             $item->setPeso((float) $item_encontrado->peso);
             $item->setCubagem((float) $item_encontrado->cubagem);
             $item->setVolumes($item_encontrado->volumes);
             $item->setInicio($data_inicial);
             $item->setValidade($validade);
             $item->setObservacaoCliente($item_encontrado->observacao_cliente);
             $item->setObservacaoInterna($item_encontrado->observacao_interna);
             
             /** Define o status do item **/
             $status = new Status_Item();
             $status->setId(0); //FIXME trazer o status real da proposta quando implementado
             $status->setStatus("Ativo");             
             $item->setStatus($status);
                          
             $this->load->model("Taxas/item_proposta_taxa_model");
             
             $item_proposta_taxa_model = new Item_Proposta_Taxa_Model();

             $item_proposta_taxa_model->buscaTaxasDoItemDaProposta($item);
             
             $proposta->adicionarNovoItem($item);
                  
         }
         
     }//END FUNCTION
     
    /**
     * verificaUltimoNumeroDeItemGerado
     * 
     * Verifica qual foi o último número de item gerado para um número de proposta
     * 
     * @name verificaUltimoNumeroDeItemGerado
     * @access protected
     * @param Proposta $proposta
     * @return string $numero
     */
     protected function verificaUltimoNumeroDeItemGerado(Proposta $proposta) 
     {
         
         $numero_proposta = $proposta->getNumero();
         
         if( empty($numero_proposta) )
         {
             throw new InvalidArgumentException("Id da proposta não definido, impossivel gerar a numeração do item");
         }    
         
         $this->db->select("itens_proposta.*")->
                    from("CLIENTES.itens_proposta")->
                    like("numero_proposta",  substr($proposta->getNumero(), 0, -2));
         
         $rs = $this->db->get();
                           
         $maior_sequencial_encontrado = '00';
         
         foreach( $rs->result() as $numero_encotrado )
         {             
             $sequencial_encontrado = substr($numero_encotrado->numero_proposta, 13);
             
             if( intval($sequencial_encontrado) > intval($maior_sequencial_encontrado) )
             {           
                 $maior_sequencial_encontrado = $sequencial_encontrado;
             }    
         }    
         
         return $maior_sequencial_encontrado;
         
     }//END FUNCTION
         
    /**
     * geraNovoNumeroDeItemDeProposta
     * 
     * Gera um novo número para umitem de proposta baseado na numeração da proposta
     * 
     * @name geraNovoNumeroDeItemDeProposta
     * @access protected
     * @param Proposta $proposta
     * @return string $numero
     */     
    protected function geraNovoNumeroDeItemDeProposta(Proposta $proposta) 
    {
        
        $numero_proposta = $proposta->getNumero();
         
        if( empty($numero_proposta) )
        {
            throw new InvalidArgumentException("Id da proposta não definido, impossivel gerar a numeração do item");
        }
        
        $ultimo_sequencial_gerado = $this->verificaUltimoNumeroDeItemGerado($proposta);
        
        $sequencial_do_novo_numero = intval($ultimo_sequencial_gerado) + 1;
        
        $sequencial_do_novo_numero = sprintf("%02d", $sequencial_do_novo_numero);
        
        $numero_base = substr($proposta->getNumero(), 0, -2);
        
        $novo_numero_gerado = $numero_base . $sequencial_do_novo_numero;
        
        return $novo_numero_gerado;
        
    }//END FUNCTION
    
    /**
     * incluirItemDaPropostaNaSessao
     *  
     * inclui um item da proposta na sessão do PHP
     * 
     * @name incluirItemDaPropostaNaSessao
     * @access public
     * @param Item_Propostas
     * @return boolean
     */
    public function incluirItemDaPropostaNaSessao(Item_Proposta $item) 
    {
        
        $this->load->model("Adaptadores/sessao");
        $this->load->model("Taxas/serializa_taxas");
        
        /** Serializa às taxas da proposta para inclusão na sessão **/
        $serializador_de_taxas = new Serializa_Taxas();
        
        $taxas_serializadas = $serializador_de_taxas->serializaTaxasProposta($item);
        
        $sessao = new Sessao();
        
        $sessao
        ->setIdItem($item->getId())        
        ->setCc($item->getCc())
        ->setPp($item->getPp())
        ->setPeso((float)$item->getPeso())
        ->setCubagem((float)$item->getCubagem())
        ->setVolumes((int)$item->getVolumes())
        ->setOrigem($item->getTarifario()->getRota()->getPortoOrigem()->getNome())
        ->setEmbarque($item->getTarifario()->getRota()->getPortoEmbarque()->getNome())
        ->setDesembarque($item->getTarifario()->getRota()->getPortoDesembarque()->getNome())
        ->setDestino($item->getTarifario()->getRota()->getPortoFinal()->getNome())
        ->setUnOrigem($item->getTarifario()->getRota()->getPortoOrigem()->getUnCode())
        ->setUnEmbarque($item->getTarifario()->getRota()->getPortoEmbarque()->getUnCode())
        ->setUnDesembarque($item->getTarifario()->getRota()->getPortoDesembarque()->getUnCode())
        ->setUnDestino($item->getTarifario()->getRota()->getPortoFinal()->getUnCode())        
        ->setIdTarifario((int)$item->getTarifario()->getId())
        ->setMercadoria($item->getMercadoria())
        ->setObservacaoCliente($item->getObservacaoCliente())
        ->setObservacaoInterna($item->getObservacaoInterna())
        ->setLabelsFretesAdicionais($taxas_serializadas['label_taxas_adicionais'])
        ->setLabelsTaxasLocais($taxas_serializadas['label_taxas_locais'])
        ->setFreteAdicionais($taxas_serializadas['value_taxas_adicionais'])
        ->setTaxasLocais($taxas_serializadas['value_taxas_locais'])
        ->setValidade($item->getValidade()->format("d-m-Y"))
        ->setAntiCache(time());        
        
        return $sessao->inserirItemNaSessao();
        
    }//END FUNCTION
    
    /**
     * excluirItemDaPropostaPeloIdDoItem
     * 
     * Exlui um item de uma proposta pelo id do item
     * 
     * @name excluirItemDaPropostaPeloIdDoItem 
     * @access public
     * @param Item_Proposta $item
     * @return boolean
     */
    public function excluirItemDaPropostaPeloIdDoItem(Item_Proposta $item) 
    {
        
        /** Exclui às taxas que estão relacionadas a o item **/
        $taxa_model = new Taxa_Model();
        
        $taxa_model->exluirTaxasPorItemDeProposta($item);
        
        return $this->db->delete("CLIENTES.itens_proposta",Array("id_item_proposta" => $item->getId()));
                
    }//END FUNCTION
    
    /**
     * excluirItensDaPropostaPeloIdDaProposta
     * 
     * Exlui todas os item de uma proposta pelo id da proposta
     * 
     * @name excluirItemDaPropostaPeloIdDoItem 
     * @access public
     * @param Item_Proposta $item
     * @return boolean
     */
    public function excluirItensDaPropostaPeloIdDaProposta(Proposta $proposta) 
    {
        
        return $this->db->delete("CLIENTES.itens_proposta", Array("id_proposta" => $proposta->getId()));        
        
    }//END FUNCTION
       
}//END CLASS

