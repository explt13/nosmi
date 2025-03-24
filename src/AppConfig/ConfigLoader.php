<?php

namespace Explt13\Nosmi\AppConfig;

use DirectoryIterator;
use Dotenv\Dotenv;
use Explt13\Nosmi\Exceptions\ArrayNotAssocException;
use Explt13\Nosmi\Exceptions\ConfigAttributeException;
use Explt13\Nosmi\Exceptions\FileNotFoundException;
use Explt13\Nosmi\Exceptions\FileReadException;
use Explt13\Nosmi\Exceptions\ResourceNotFoundException;

class ConfigLoader
{
    /**
     * @var AppConfig $app_config App config instance
     */
    protected AppConfig $app_config;
    protected string $user_config_path;

    public function __construct(?string $destination = null)
    {
        $this->app_config = AppConfig::getInstance();
        $this->user_config_path = $destination ?? $this->getUserConfigPath();
    }
    /**
     * Inits config loader
     */
    public function init(): void
    {
        
        $this->loadDefaultConfig();
        $this->loadUserConfig($this->app_config->get('APP_ROOT') ?? __DIR__ . '/../../tests/unit/mockdata/AppConfig/user_config.json');
    }

    private function getUserConfigPath()
    {
        $assumed_root = dirname(__DIR__, 5);
        if (!empty(glob($assumed_root . '/composer.json'))) {
            $assumed_root .= '/config';
        }
        return $assumed_root;
    }

    /**
     * Loads framework's config
     */
    public function loadDefaultConfig()
    {
        $config = json_decode(file_get_contents($this->getConfigPath()), true);

        foreach($config as $name => $value)
        {
            if (!is_primitive($value) && !array_is_list($value)) {
                if (!isset($value['value'])) throw new ConfigAttributeException('`value` attribute has not been provided for complex parameter');
                if (!array_is_assoc($value)) throw new ArrayNotAssocException('Please provide an associative array for attributes for parameter: ' . $name);
                $this->app_config->set($name, $value['value'], false, $value);
                continue;
            }
            $this->app_config->set($name, $value);
        }
    }

    /**
     * Get a config path
     * @return string config path
     */
    private function getConfigPath(): string
    {
        return __DIR__ . '/../Config/default_config.json';
    }

    /**
     * Load an app config in .env, .json, .ini
     * @param string $dest destination to the config file \
     * Specify the full path, e.g
     * \_\_DIR\_\_ . '/config_folder/user_config.env;
     * @return void
     */
    public function loadUserConfig(string $dest): void
    {
        $extension = pathinfo($dest, PATHINFO_EXTENSION);
        switch ($extension) {
            case 'ini':
                $user_config = $this->loadIniConfig($dest);
                break;
            case 'env':
                $user_config = $this->loadEnvConfig($dest);
                break;
            case 'json':
                $user_config = $this->loadJsonConfig($dest);
                break;
            default:
                throw new ResourceNotFoundException('Failed to load config, cannot find: ' . $dest);
        }
        $this->mergeConfigs($user_config);
    }

    /**
     * Merges default config with the user config
     * @param array $user_config user configuration params array
     * @return void
     */
    private function mergeConfigs(array $user_config): void
    {
        foreach($user_config as $name => $value) {
            if (!is_primitive($value) && !array_is_list($value)) {
                if (!isset($value['value'])) throw new ConfigAttributeException('`value` attribute has not been provided for complex parameter');
                if (!array_is_assoc($value)) throw new ArrayNotAssocException('Please provide an associative array for attributes for parameter: ' . $name);
                $this->app_config->set($name, $value['value'], false, $value);
                continue;
            }
            $this->app_config->set($name, $value);
        }
    }

        /**
     * Loads .ini config
     * @param $dest the path to the .ini file
     * @return array
     */
    private function loadIniConfig($dest): array
    {
        $config = parse_ini_file($dest);
        if (!$config) throw new FileReadException("Cannot read the ini file: $dest");
        return $config;
    }

    /**
     * Loads .env config
     * @param $dest the path to the .env file
     * @return array
     */
    private function loadEnvConfig($dest): array
    {
        if (!is_file($dest)) throw new FileNotFoundException('Cannot find the file: ' . $dest);
    
        $dirname = dirname($dest);
        $dotenv = Dotenv::createImmutable($dirname);
        $dotenv->load();
        return $_ENV;
    }

    /**
     * Loads .json config
     * @param $dest the path to the .json file
     * @return array
     */
    private static function loadJsonConfig($dest): array
    {
        $data = file_get_contents($dest);
        if (!$data) throw new FileReadException("Cannot read the json file: $dest");
        return json_decode($data, true);
    }
}