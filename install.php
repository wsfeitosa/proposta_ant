<?php
include_once "CleanUpTables.php";
include_once "CreateTables.php";

/** Cria �s tabelas do banco de dados **/
$createTables = new CreateTables();

/** Limpa �s tabelas do banco de dados **/
$cleanUp = new CleanUpTables();

echo "Instalado !";