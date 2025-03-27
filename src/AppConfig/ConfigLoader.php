<?php

namespace Explt13\Nosmi\AppConfig;

use Dotenv\Dotenv;
use Explt13\Nosmi\Exceptions\FileReadException;
use Explt13\Nosmi\Exceptions\InvalidFileExtensionException;
use Explt13\Nosmi\Exceptions\InvalidResourceException;
use Explt13\Nosmi\Exceptions\ResourceNotFoundException;
use SplFileInfo;

class ConfigLoader
{
    /**
     * @var AppConfig $app_config App config instance
     */
    protected AppConfig $app_config;

    /**
     * @var array{0: 'env', 1: 'json', 2: 'ini'} CONFIG_EXTENSTIONS available extensions for the config file
     */
    private const CONFIG_EXTENSTIONS = ['env', 'json', 'ini'];

    /**
     * @param string $config_path [optional] <p> \
     * A path to the app's config file.
     * Setting path explicitly is recommended \
     * Set it to __null__ to try resolve a path to the config automatically \
     * Set it to __false__ to indicate that app does not use config 
     * </p>
     */
    public function __construct(null|false|string $config_path = null)
    {
        $this->app_config = AppConfig::getInstance();
        $this->loadFrameworkConfig($this->getFrameworkConfigPath());
        if ($this->validateConfigPath($config_path) !== false) {
            $this->loadUserConfig($this->app_config->get('APP_ROOT') ?? $this->getUserConfigPath($config_path));
        }
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
     * Gets user's config path;
     * @param null|string $config_path a path to the user's config
     */
    private function getUserConfigPath(null|string $config_path): string
    {
        if (!is_null($config_path)) {
            return $config_path;
        }
        return $this->detectConfigFile();
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
     * Validates a config path
     * @param null|false|string $config path a path to the config
     * @return bool returns __false__ if path is set to false meaning app does not have a config \
     * returns __true__ if either a path is a null meaning left to autodetection or set to string that will be checked for validity
     * @throws ResourceNotFoundException|InvalidResourceException
     */
    private function validateConfigPath(null|false|string $config_path): bool
    {
        if ($config_path === false) return false;
        if (is_null($config_path)) return true;

        if (!file_exists($config_path)) {
            throw new ResourceNotFoundException("Cannot find the path to the config file: $config_path");
        }
        if (!is_file($config_path)) {
            $file_info = new \SplFileInfo($config_path);
            throw new InvalidResourceException($file_info->getType(), 'file');
        }

        return true;
    }

    /**
     * Load an app config in .env, .json, .ini
     * @param string $dest destination to the config file \
     * Specify the full path, e.g
     * \_\_DIR\_\_ . '/config_folder/user_config.env;
     * @return void
     * @throws InvalidFileExtensionException
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
                throw new InvalidFileExtensionException("Invalid file extension $extension.", self::CONFIG_EXTENSTIONS);
        };
        $this->app_config->bulkSet($user_config);
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
    private static function loadJsonConfig(string $dest): array
    {
        $data = file_get_contents($dest);
        if (!$data) throw new FileReadException("Cannot read the json file: $dest");
        return json_decode($data, true);
    }
}