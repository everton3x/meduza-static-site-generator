<?php
namespace Meduza\Config;

/**
 * Interface para loaders de configurações.
 * 
 * @author Everton
 */
interface ConfigLoaderInterface
{
    public function __construct();
    
    public function load(string $config, string $env): array;
}
