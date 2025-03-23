<?php

namespace Explt13\Nosmi\AppConfig;

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
    private static AppConfig $app_config;
   
    /**
     * Inits config loader
     */
    public static function init(): void
    {
        self::$app_config = AppConfig::getInstance();
        self::loadDefaultConfig();
        self::loadUserConfig(self::$app_config->get('APP_ROOT') ?? __DIR__ . '/../../tests/unit/mockdata/AppConfig/user_config.json');
    }

    /**
     * Loads framework's config
     */
    public static function loadDefaultConfig()
    {
        $config = json_decode(file_get_contents(self::getConfigPath()), true);

        foreach($config as $name => $value)
        {
            if (!is_primitive($value) && !array_is_list($value)) {
                if (!isset($value['value'])) throw new ConfigAttributeException('`value` attribute has not been provided for complex parameter');
                if (!array_is_assoc($value)) throw new ArrayNotAssocException('Please provide an associative array for attributes for parameter: ' . $name);
                self::$app_config->set($name, $value['value'], false, $value);
                continue;
            }
            self::$app_config->set($name, $value);
        }
    }

    /**
     * Get a config path
     * @return string config path
     */
    private static function getConfigPath(): string
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
    public static function loadUserConfig(string $dest): void
    {
        $extension = pathinfo($dest, PATHINFO_EXTENSION);
        switch ($extension) {
            case 'ini':
                $user_config = self::loadIniConfig($dest);
                break;
            case 'env':
                $user_config = self::loadEnvConfig($dest);
                break;
            case 'json':
                $user_config = self::loadJsonConfig($dest);
                break;
            default:
                throw new ResourceNotFoundException('Failed to load config, cannot find: ' . $dest);
        }
        self::mergeConfigs($user_config);
    }

    /**
     * Merges default config with the user config
     * @param array $user_config user configuration params array
     * @return void
     */
    private static function mergeConfigs(array $user_config): void
    {
        foreach($user_config as $name => $value) {
            if (!is_primitive($value) && !array_is_list($value)) {
                if (!isset($value['value'])) throw new ConfigAttributeException('`value` attribute has not been provided for complex parameter');
                if (!array_is_assoc($value)) throw new ArrayNotAssocException('Please provide an associative array for attributes for parameter: ' . $name);
                self::$app_config->set($name, $value['value'], false, $value);
                continue;
            }
            self::$app_config->set($name, $value);
        }
    }

        /**
     * Loads .ini config
     * @param $dest the path to the .ini file
     * @return array
     */
    private static function loadIniConfig($dest): array
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
    private static function loadEnvConfig($dest): array
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