<?php
/**
 * Arquivo de construção do conteúdo.
 * 
 * Por enquanto é apenas para teste.
 */

require_once 'bootstrap.php';

$env = Meduza\Environment\Environment::DEVELOPMENT;
//$env = Meduza\Environment::PRODUTION;

$configLoader = new Meduza\Config\Loader();

$config = $configLoader->load('meduza.yml', $env);

print_r($config);

//$builder = new Meduza\Build\Builder($config);
//
//$builder->build();