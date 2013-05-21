<?php
Header("Content-type: application/xml; charset=ISO-8859-1");
$dom = new DOMDocument();

$dom->formatOutput = TRUE;

$t = $dom->createElement("tarifario");
/** Id do tarifario **/
$id = $dom->createElement("id_tarifario",$tarifario->getId());
$t->appendChild($id);

/** Data de Inicio **/
$data_inicio = $dom->createElement("data_inicio",$tarifario->getInicio()->format('d/m/Y'));
$t->appendChild($data_inicio);

/** Validade do tarifario **/
$validade = $dom->createElement("validade",$tarifario->getValidade()->format('d/m/Y'));
$t->appendChild($validade);

/** Sentido (IMP OU EXP) **/
$sentido = $dom->createElement("sentido",$tarifario->getSentido());
$t->appendChild($sentido);

/** Rota do tarifario **/
$rota = $dom->createElement("rota");

/** Porto de origem **/
$porto_origem = $dom->createElement("origem");
$id_porto_origem = $dom->createElement("id",$tarifario->getRota()->getPortoOrigem()->getId());
$nome_porto_origem = $dom->createElement("nome",$tarifario->getRota()->getPortoOrigem()->getNome());
$uncode_origem = $dom->createElement("uncode",$tarifario->getRota()->getPortoOrigem()->getUnCode());

$porto_origem->appendChild($id_porto_origem);
$porto_origem->appendChild($nome_porto_origem);
$porto_origem->appendChild($uncode_origem);

$rota->appendChild($porto_origem);

/** Porto de embarque **/
$porto_embarque = $dom->createElement("embarque");
$id_porto_embarque = $dom->createElement("id",$tarifario->getRota()->getPortoembarque()->getId());
$nome_porto_embarque = $dom->createElement("nome",utf8_encode($tarifario->getRota()->getPortoEmbarque()->getNome()));
$uncode_embarque = $dom->createElement("uncode",$tarifario->getRota()->getPortoEmbarque()->getUnCode());

$porto_embarque->appendChild($id_porto_embarque);
$porto_embarque->appendChild($nome_porto_embarque);
$porto_embarque->appendChild($uncode_embarque);

$rota->appendChild($porto_embarque);

/** Porto de desembarque **/
$porto_desembarque = $dom->createElement("desembarque");
$id_porto_desembarque = $dom->createElement("id",$tarifario->getRota()->getPortoDesembarque()->getId());
$nome_porto_desembarque = $dom->createElement("nome",utf8_encode($tarifario->getRota()->getPortoDesembarque()->getNome()));
$uncode_desembarque = $dom->createElement("uncode",$tarifario->getRota()->getPortoDesembarque()->getUnCode());

$porto_desembarque->appendChild($id_porto_desembarque);
$porto_desembarque->appendChild($nome_porto_desembarque);
$porto_desembarque->appendChild($uncode_desembarque);

$rota->appendChild($porto_desembarque);

/** Porto de destino **/
$porto_destino = $dom->createElement("destino");
$id_porto_destino = $dom->createElement("id",$tarifario->getRota()->getPortoFinal()->getId());
$nome_porto_destino = $dom->createElement("nome",utf8_encode($tarifario->getRota()->getPortoFinal()->getNome()));
$uncode_destino = $dom->createElement("uncode",$tarifario->getRota()->getPortoFinal()->getUnCode());

$porto_destino->appendChild($id_porto_destino);
$porto_destino->appendChild($nome_porto_destino);
$porto_destino->appendChild($uncode_destino);

$rota->appendChild($porto_destino);

$t->appendChild($rota);

$observacao = $dom->createElement("observacao",$tarifario->getObservacao());

$t->appendChild($observacao);

/** Taxas adicionais **/
$taxas_adicionais = $dom->createElement("taxas_adicionais");
$taxas_locais = $dom->createElement("taxas_locais");

foreach($tarifario->getTaxa() as $taxa):

if( $taxa instanceof Taxa_Adicional )
{
	$taxa_adicional = $dom->createElement("taxa");
	$taxa_adicional->appendChild($dom->createElement("id_taxa",$taxa->getId()));
	$taxa_adicional->appendChild($dom->createElement("nome",($taxa->getNome())));
	$taxa_adicional->appendChild($dom->createElement("id_unidade",$taxa->getUnidade()->getId()));
	$taxa_adicional->appendChild($dom->createElement("unidade",$taxa->getUnidade()->getUnidade()));
	$taxa_adicional->appendChild($dom->createElement("id_moeda",$taxa->getMoeda()->getId()));
	$taxa_adicional->appendChild($dom->createElement("moeda",$taxa->getMoeda()->getSigla()));
	$taxa_adicional->appendChild($dom->createElement("valor",number_format($taxa->getValor(),2,".",",")));
	$taxa_adicional->appendChild($dom->createElement("valor_minimo",number_format($taxa->getValorMinimo(),2,".",",")));
	$taxa_adicional->appendChild($dom->createElement("valor_maximo",number_format($taxa->getValorMaximo(),2,".",",")));

	$taxas_adicionais->appendChild($taxa_adicional);
}
else
{
	$taxa_local = $dom->createElement("taxa");
	$taxa_local->appendChild($dom->createElement("id_taxa",$taxa->getId()));
	$taxa_local->appendChild($dom->createElement("nome",($taxa->getNome())));
	$taxa_local->appendChild($dom->createElement("id_unidade",$taxa->getUnidade()->getId()));
	$taxa_local->appendChild($dom->createElement("unidade",$taxa->getUnidade()->getUnidade()));
	$taxa_local->appendChild($dom->createElement("id_moeda",$taxa->getMoeda()->getId()));
	$taxa_local->appendChild($dom->createElement("moeda",$taxa->getMoeda()->getSigla()));
	$taxa_local->appendChild($dom->createElement("valor",number_format($taxa->getValor(),2,".",",")));
	$taxa_local->appendChild($dom->createElement("valor_minimo",number_format($taxa->getValorMinimo(),2,".",",")));
	$taxa_local->appendChild($dom->createElement("valor_maximo",number_format($taxa->getValorMaximo(),2,".",",")));

	$taxas_locais->appendChild($taxa_local);
}

endforeach;

$t->appendChild($taxas_adicionais);

$t->appendChild($taxas_locais);

$dom->appendChild($t);

echo $dom->saveXML();