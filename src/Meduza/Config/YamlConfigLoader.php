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
        if (!($config = yaml_parse_file($config))) {
            throw new \Exception("Falha ao carregar dados de [$config]");
        }

        if (key_exists('extra-config', $config)) {
            $config = array_merge_recursive($config, $this->loadExtraConfig($config['extra-config']));
            unset($config['extra-config']);
        }

        $config = array_merge_recursive($config, $this->loadEnvConfig($env));
        $config['environment'] = $env;

        return $config;
    }

    protected function loadExtraConfig(array $extraConfig): array
    {
        $return = [];
        foreach ($extraConfig as $fileConfig) {
            if (!($config = yaml_parse_file($fileConfig, 0))) {
                throw new \Exception("Falha ao ler configurações extras de [$fileConfig].");
            }

            if (key_exists('extra-config', $config)) {
                $return = array_merge_recursive($return, $this->loadExtraConfig($config['extra-config']));
                unset($return['extra-config']);
            }
            
            $return = array_merge_recursive($return, $config);
        }
        

        return $return;
    }

    protected function loadEnvConfig(string $env): array
    {
        $envConfigFile = MEDUZA_ENV_DIR . $env . DIRECTORY_SEPARATOR . 'main.yml';

        if (!($config = yaml_parse_file($envConfigFile, 0))) {
            throw new \Exception("Falha ao ler [$envConfigFile] para [$env].");
        }
        
        if (key_exists('extra-config', $config)) {
            $config = array_merge_recursive($config, $this->loadExtraConfig($config['extra-config']));
            unset($config['extra-config']);
        }
        
        return $config;
    }
}
