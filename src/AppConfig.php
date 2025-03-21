<?php

namespace Explt13\Nosmi;

use Dotenv\Dotenv;
use Explt13\Nosmi\Exceptions\FileReadException;
use Explt13\Nosmi\Exceptions\ResourceNotFoundException;

class AppConfig
{
    use SingletonTrait;

    /**
     * @var array $config an array with parameters 
     * ```php
     * ['name1' => ['value' => $value, ...], 'name2' => [...], ...];
     * ```
     */
    protected array $config;

    private function __construct()
    {
        $this->loadDefaultConfig();
    }

    private function loadDefaultConfig()
    {
        $dest = __DIR__ . '/config/default_config.json';
        $this->config = json_decode(file_get_contents($dest), true);
    }

    /**
     * Load an app config in .env, .json, .ini
     * 
     */
    public function loadUserConfig(?string $dest = null)
    {
        $extension = pathinfo($dest, PATHINFO_EXTENSION);
        switch ($extension) {
            case 'ini':
                $this->loadIniConfig($dest);
            case 'env':
                $this->loadEnvConfig($dest);
            case 'json':
                $this->loadJsonConfig($dest);
            default:
                throw new ResourceNotFoundException('Failed to load config, can not find: ' . $dest);
        }
    }
    
    private function loadIniConfig($dest)
    {
        $config = parse_ini_file($dest);
        if (!$config) throw new FileReadException("Cannot read the ini file: $dest");
        $this->config = array_merge($this->config, $config);
    }

    private function loadEnvConfig($dest)
    {
        if (!is_dir($dest) && !is_file($dest)) {
            throw new ResourceNotFoundException('Cannot find resource: ' . $dest);
        }
        if (is_dir($dest)) {
            $dotenv = Dotenv::createImmutable($dest);
        } else if (is_file($dest)) {
            $dirname = dirname($dest);
            $dotenv = Dotenv::createImmutable($dirname);
        }
        $dotenv->load();
        $this->config = array_merge($this->config, $_ENV);
    }

    private function loadJsonConfig($dest)
    {
        $data = file_get_contents($dest);
        if (!$data) throw new FileReadException("Cannot read the json file: $dest");
        $this->config = array_merge($this->config, json_decode($data, true));
    }

    /**
     * Check whether a config paramter exists
     * @param string $name paramter name to check
     * @return bool return true if exists else false
     */
    public function has(string $name): bool
    {
        return isset($this->config[$name]);
    }

    /**
     * Get a configuration parameter
     * @param string $name retrieve a configuration parameter by its name
     * @return array|null returns null if a parameter is not present
     */
    public function get(string $name): array | null
    {
        if ($this->has($name)) {
            return $this->config[$name]['value'];
        }
        return null;
    }

    /**
     * Get all configuration parameters
     * @return array array of parameters
     */
    public function getAll(): array
    {
        return $this->config;
    }

    /**
     * Set a configuration parameter
     * @param string $name a desired name for a parameter
     * @param mixed $value a value for the parameter
     * @param bool $readonly [optional] <p>
     * whether parameter is should be only set once
     * </p>
     */
    public function set(string $name, mixed $value, bool $readonly = false): void
    {
        $parameter = $this->get($name);
        if ($parameter !== null && $parameter['readonly']) {
            throw new \Exception('Can not set a readonly parameter');
        }
   
        $this->config[$name] = [
            'value' => $value,
            'readonly' => $readonly,
        ];
        
    }

    /**
     * Reset configuration to default values
     */
    public function reset()
    {

    }
}