<?php
namespace Meduza\Config;

/**
 * Loader para configurações em arquivos yaml.
 *
 * @author Everton
 */
class YamlConfigLoader implements ConfigLoaderInterface
{

    public function __construct()
    {
        
    }

    public function load(string $config, string $env): array
    {
        // carrega as configurações do arquivo principal
        if (!($config = yaml_parse_file($config))) {
            throw new \Exception("Falha ao carregar dados de [$config]");
        }

        //carrega as configurações extras
        if (key_exists('extra-config', $config)) {
            $config = array_merge_recursive($config, $this->loadExtraConfig($config['extra-config']));
            unset($config['extra-config']);
        }

        $config = array_merge_recursive($config, $this->loadEnvConfig($env));
        $config['environment'] = $env;

        return $config;
    }

    /**
     * Carrega as configurações extras
     * 
     * @param array $extraConfig Lista com os caminhos para os arquivos de configuração extra.
     * @return array Retorna as configurações extras.
     * @throws \Exception
     */
    protected function loadExtraConfig(array $extraConfig): array
    {
        $return = [];
        foreach ($extraConfig as $fileConfig) {
            if (!($config = yaml_parse_file($fileConfig, 0))) {
                throw new \Exception("Falha ao ler configurações extras de [$fileConfig].");
            }

            //carrega configurações extras, se houverem.
            if (key_exists('extra-config', $config)) {
                $return = array_merge_recursive($return, $this->loadExtraConfig($config['extra-config']));
                unset($return['extra-config']);
            }
            
            $return = array_merge_recursive($return, $config);
        }
        

        return $return;
    }

    /**
     * Carrega as configurações do ambiente selecionado.
     * 
     * @param string $env
     * @return array Retorna as configurações específicas do ambiente.
     * @throws \Exception
     */
    protected function loadEnvConfig(string $env): array
    {
        $envConfigFile = MEDUZA_ENV_DIR . $env . DIRECTORY_SEPARATOR . 'main.yml';

        if (!($config = yaml_parse_file($envConfigFile, 0))) {
            throw new \Exception("Falha ao ler [$envConfigFile] para [$env].");
        }
        
        //carrega configurações extras se houver.
        if (key_exists('extra-config', $config)) {
            $config = array_merge_recursive($config, $this->loadExtraConfig($config['extra-config']));
            unset($config['extra-config']);
        }
        
        return $config;
    }
}
