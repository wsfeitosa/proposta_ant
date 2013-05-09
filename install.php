<?php
include_once "CleanUpTables.php";
include_once "CreateTables.php";

/** Cria às tabelas do banco de dados **/
$createTables = new CreateTables();

/** Limpa às tabelas do banco de dados **/
$cleanUp = new CleanUpTables();

echo "Instalado !";