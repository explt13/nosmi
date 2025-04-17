<?php

namespace Explt13\Nosmi\AppConfig;

use Dotenv\Dotenv;
use Explt13\Nosmi\Exceptions\ConfigParameterNotSetException;
use Explt13\Nosmi\Exceptions\InvalidFileExtensionException;
use Explt13\Nosmi\Exceptions\InvalidResourceException;
use Explt13\Nosmi\Exceptions\ResourceNotFoundException;
use Explt13\Nosmi\Exceptions\ResourceReadException;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\FileValidatorInterface;
use Explt13\Nosmi\Validators\FileValidator;

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
    }

    /**
     * Load a config in .env, .json, .ini
     * @param string $config_path a destination to the config file
     * @return void
     */
    public function loadConfig(?string $config_path = null): void
    {
        if (is_null($config_path)) {
            // LOG
            return;
        }
        $this->validateConfigFilePath($config_path);
        $config = $this->getConfig(pathinfo($config_path, PATHINFO_EXTENSION), $config_path);
        $this->app_config->bulkSet($config);
        $this->setRequiredMissingParams();
    }

    protected function setRequiredMissingParams(): void
    {
        if (!$this->app_config->has('APP_ROOT')) {
            throw new ConfigParameterNotSetException('APP_ROOT');
        }
        
        if (!$this->app_config->has('APP_PSR')) {
            throw new ConfigParameterNotSetException('APP_PSR');
        }

        $app_root = $this->app_config->get('APP_ROOT');
        $required_dirs = [
            'APP_SRC' => "$app_root/src",
            'APP_PROVIDERS' => "$app_root/src/providers",
            'APP_MIDDLEWARES' => "$app_root/src/middlewares",
            'APP_CONFIG' => "$app_root/config",
        ];

        foreach ($required_dirs as $key => $value) {
            if (!$this->app_config->has($key)) {
                if (!$this->file_validator->isReadableDir($value)) {
                    throw InvalidResourceException::withMessage('The specified folder is not a valid directory: ' . $value);
                }
                $this->app_config->set($key, $value);
            }
        }
    }

    /**
     * Validate the path of the config file
     * @param string $config_path the path to the config file
     * @return void
     * @throws InvalidFileExtensionException if a file has an unsupported extension
     */
    protected function validateConfigFilePath(string $config_path): void
    {
        if (!$this->file_validator->resourceExists($config_path)) {
            throw new ResourceNotFoundException('Cannot find a resource: ' . $config_path);
        }

        if (!$this->file_validator->isFile($config_path)) {
            throw new InvalidResourceException('directory', 'file', $config_path);
        }
        if (!$this->file_validator->isReadable($config_path)) {
            throw new ResourceReadException('Cannot read a file ' . $config_path . ', please make sure the file has appropriate permissions');
        }
        if (!$this->file_validator->isValidExtension($config_path, self::CONFIG_EXTENSTIONS)) {
            throw new InvalidFileExtensionException($config_path, self::CONFIG_EXTENSTIONS);
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
