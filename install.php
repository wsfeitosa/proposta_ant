<?php
include_once "CleanUpTables.php";
include_once "CreateTables.php";
include_once "CreateProject.php";

/** Coloque aqui o diret�rio onde deseja instalar o projeto **/
define("PROJECT_DIR","/var/www/");

/** Cria �s tabelas do banco de dados **/
$createTables = new CreateTables();

/** Limpa �s tabelas do banco de dados **/
$cleanUp = new CleanUpTables();

/** Cria a estrutura do projeto no diret�rio especificado **/
$project = new CreateProject(PROJECT_DIR);

$project->gerar();

echo "Instalado !";