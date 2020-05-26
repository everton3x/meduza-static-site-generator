<?php
/**
 * Arquivo de construção do conteúdo.
 * 
 * Por enquanto é apenas para teste.
 */

require_once 'vendor/autoload.php';

$config = new Meduza\Config\Loader('meduza.yml');

$env = Meduza\Environment\Environment::DEVELOPMENT;
//$env = Meduza\Environment::PRODUTION;

$builder = new Meduza\Build\Builder($config);

$builder->build($env);