<?php
interface Relatorio{
		
	public function adicionarNovoParametro(Array $parametro);
	public function obterParametros();
	public function gerar();
	public function obterNome();
		
}