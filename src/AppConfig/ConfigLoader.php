<?php

namespace Explt13\Nosmi\AppConfig;

use Dotenv\Dotenv;
use Explt13\Nosmi\Exceptions\InvalidFileExtensionException;
use Explt13\Nosmi\Exceptions\InvalidResourceException;
use Explt13\Nosmi\Exceptions\ResourceNotFoundException;
use Explt13\Nosmi\Exceptions\ResourceReadException;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\FileValidatorInterface;

class ConfigLoader
{
    /**
     * @var ConfigInterface $app_config App config instance
     */
    protected ConfigInterface $app_config;

    protected FileValidatorInterface $file_validator;

    /**
     * @var array{0: 'env', 1: 'json', 2: 'ini'} CONFIG_EXTENSTIONS available extensions for the config file
     */
    private const CONFIG_EXTENSTIONS = ['env', 'json', 'ini'];

    /**
     * @param ConfigInterface $app_config An app config object
     */
    public function __construct(ConfigInterface $app_config, FileValidatorInterface $file_validator)
    {
        $this->app_config = $app_config;
        $this->file_validator = $file_validator;
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
        $this->validateConfigFilePath($config_path);
        $extension = pathinfo($config_path, PATHINFO_EXTENSION);
        $user_config = $this->getConfig($extension, $config_path);
        $this->app_config->bulkSet($user_config);
    }

    /**
     * Validate the path of the config file
     * @param string $config_path the path to the config file
     * @return void
     */
    protected function validateConfigFilePath(string $config_path): void
    {
        if (!$this->file_validator->fileExists($config_path)) {
            throw new ResourceNotFoundException('Cannot find a resource: ' . $config_path);
        }

        if (!$this->file_validator->isFile($config_path)) {
            throw new InvalidResourceException('directory', 'file');
        }
        if (!$this->file_validator->isReadable($config_path)) {
            throw new ResourceReadException('Cannot read a file ' . $config_path . ', please make sure the file has appropriate permissions');
        }
        if (!$this->file_validator->isValidExtension(pathinfo($config_path, PATHINFO_EXTENSION), self::CONFIG_EXTENSTIONS)) {
            throw new InvalidFileExtensionException(self::CONFIG_EXTENSTIONS);
        }
    }

    /**
     * Get config array based on extension
     * @param string $extension an extension to call the right loader
     * @param
     * @return array returns a config array
     */
    private function getConfig(string $extension, string $config_path): array
    {
        return match ($extension) {
            "ini" => $this->loadIniConfig($config_path),
            "env" => $this->loadEnvConfig($config_path),
            "json" => $this->loadJsonConfig($config_path)
        };
    }

    /**
     * Loads .ini config
     * @param $dest the path to the .ini file
     * @return array
     * @throws ResourceReadException
     */
    private function loadIniConfig(string $dest): array
    {
        $config = parse_ini_file($dest);
        if (!$config) throw new ResourceReadException("Cannot read the ini file: $dest");
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
     * @throws ResourceReadException
     */
    private function loadJsonConfig(string $dest): array
    {
        $data = file_get_contents($dest);
        if (!$data) throw new ResourceReadException("Cannot read the json file: $dest");
        return json_decode($data, true);
    }
}
