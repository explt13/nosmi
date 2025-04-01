<?php

namespace Explt13\Nosmi\AppConfig;

use Dotenv\Dotenv;
use Explt13\Nosmi\Exceptions\FileNotFoundException;
use Explt13\Nosmi\Exceptions\FileReadException;
use Explt13\Nosmi\Exceptions\InvalidFileExtensionException;
use Explt13\Nosmi\Exceptions\ResourceNotFoundException;

class ConfigLoader
{
    /**
     * @var ConfigInterface $app_config App config instance
     */
    protected ConfigInterface $app_config;

    /**
     * @var array{0: 'env', 1: 'json', 2: 'ini'} CONFIG_EXTENSTIONS available extensions for the config file
     */
    private const CONFIG_EXTENSTIONS = ['env', 'json', 'ini'];

    /**
     * @param ConfigInterface $app_config An app config object
     */
    public function __construct(ConfigInterface $app_config)
    {
        $this->app_config = $app_config;
        $this->loadFrameworkConfig($this->getFrameworkConfigPath());
    }

    /**
     * Get a config path
     * @return string config path
     */
    private function getFrameworkConfigPath(): string
    {
        return __DIR__ . '/../Config/default_config.json';
    }

    /**
     * Loads framework's config
     * @return void
     */
    private function loadFrameworkConfig(string $dest): void
    {
        $config = json_decode(file_get_contents($dest), true);
        $this->app_config->bulkSet($config);
    }

    /**
     * Attempts to detect a config path
     * @return string a resolved config path
     * @throws ResourceNotFoundException
     */
    private function detectConfigFile(): string
    {
        $assumed_root = dirname(__DIR__, 5);
        if (!empty(glob($assumed_root . '/composer.json'))) {
            $assumed_root .= '/config';
            return $assumed_root;
        }
        throw new ResourceNotFoundException("Cannot find a config file. Set the path explicitly or set it to false if config file is not supposed to present");
    }

    /**
     * Load an app config in .env, .json, .ini
     * @param null|string $config_path [optional] <p> a destination to the config file \
     * Set to null by default which will try to autodeteced the path to the config file
     * Pass a full path, e.g
     * \_\_DIR\_\_ . '/config_folder/user_config.env;
     * </p>
     * 
     * @return void
     * @throws InvalidFileExtensionException if a file has an unsupported extension
     */
    public function loadUserConfig(null|string $config_path = null): void
    {
        if (is_null($config_path)) {
            $config_path = $this->detectConfigFile();
        }
        if (!is_file($config_path) || !is_readable($config_path)) {
            throw new FileNotFoundException('Cannot find a file: ' . $config_path);
        }
        
        $extension = pathinfo($config_path, PATHINFO_EXTENSION);
        if (!in_array($extension, self::CONFIG_EXTENSTIONS, true)) {
            throw new InvalidFileExtensionException("Invalid file extension $extension.", self::CONFIG_EXTENSTIONS);
        }
        $user_config = $this->getConfig($extension, $config_path);
        $this->app_config->bulkSet($user_config);
    }

    /**
     * Get config array based on extension
     * @return array a config array
     */
    private function getConfig($extension, $config_path): array
    {
        return match($extension) {
            "ini" => $this->loadIniConfig($config_path),
            "env" => $this->loadEnvConfig($config_path),
            "json" => $this->loadJsonConfig($config_path)
        };
    }
    
    /**
     * Loads .ini config
     * @param $dest the path to the .ini file
     * @return array
     * @throws FileReadException
     */
    private function loadIniConfig(string $dest): array
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
    private function loadEnvConfig(string $dest): array
    {
        $dirname = dirname($dest);
        $dotenv = Dotenv::createImmutable($dirname);
        $dotenv->load();
        return $_ENV;
    }

    /**
     * Loads .json config
     * @param $dest the path to the .json file
     * @return array
     * @throws FileReadException
     */
    private function loadJsonConfig(string $dest): array
    {
        $data = file_get_contents($dest);
        if (!$data) throw new FileReadException("Cannot read the json file: $dest");
        return json_decode($data, true);
    }
}