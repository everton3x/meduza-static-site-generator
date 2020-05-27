<?php
namespace Meduza\Config;

/**
 * Loader de configurações.
 *
 * @author everton
 */
class Loader
{
    public function __construct()
    {
        
    }
    
    public function load(string $configFilePath, string $environment): array
    {
        if(!file_exists($configFilePath)){
            throw new \Exception("Arquivo de configuração [$configFilePath] não encontrado.");
        }
        
        $loader = $this->detectConfigLoader(pathinfo($configFilePath, PATHINFO_EXTENSION));
        
        return $loader->load($configFilePath, $environment);
    }
    
    protected function detectConfigLoader(string $fileExtension): ConfigLoaderInterface
    {
        switch ($fileExtension){
            case 'yml':
                return new YamlConfigLoader();
                break;
            default:
                throw new \Exception("Configuração [$fileExtension] não suportada.");
        }
    }
}
