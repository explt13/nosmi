<?php

namespace Explt13\Nosmi;

use Dotenv\Dotenv;
use Explt13\Nosmi\Exceptions\ArrayNotAssocException;
use Explt13\Nosmi\Exceptions\ConfigAttributeException;
use Explt13\Nosmi\Exceptions\FileNotFoundException;
use Explt13\Nosmi\Exceptions\FileReadException;
use Explt13\Nosmi\Exceptions\ParameterValidationException;
use Explt13\Nosmi\Exceptions\ResourceNotFoundException;
use Explt13\Nosmi\Exceptions\SetReadonlyException;

class AppConfig
{
    use SingletonTrait;

    /**
     * @var array $config an array with parameters 
     */
    protected array $config;

    private function __construct()
    {
        require_once __DIR__ . '/Utils/functions.php';
        $this->loadDefaultConfig();
    }

    /**
     * Loads framework's config
     */
    private function loadDefaultConfig()
    {
        $this->config = json_decode(file_get_contents($this->getConfigPath()), true);
    }

    /**
     * Get a config path
     * @return string config path
     */
    private function getConfigPath(): string
    {
        return __DIR__ . '/Config/default_config.json';
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
            $parameter = $this->get($name, true);
            $this->validateParameter($name, $parameter);
            if (!is_primitive($value) && !array_is_list($value)) {
                if (!isset($value['value'])) throw new ConfigAttributeException('`value` attribute has not been provided for complex parameter');
                if (!array_is_assoc($value)) throw new ArrayNotAssocException('Please provide an associative array for attributes for parameter: ' . $name);
            }
            $this->config[$name] = $value;
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
    private function loadJsonConfig($dest): array
    {
        $data = file_get_contents($dest);
        if (!$data) throw new FileReadException("Cannot read the json file: $dest");
        return json_decode($data, true);
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
     * @param bool $getWithAttributes [optional] <p>
     * get a parameter with associated attributes if they present \
     * ["param" => ["value" => value , ...attributes], ...]
     * </p>
     * @return mixed returns null if a parameter is not present
     */
    public function get(string $name, bool $getWithAttributes=false): mixed
    {
        if ($this->has($name)) {
            $value = $this->config[$name];
            
            if (is_primitive($value) || array_is_list($value)) {
                return $value;
            }

            if (!isset($value['value'])) throw new ConfigAttributeException();
            
            if ($getWithAttributes) {
                return $value;
            }
            return $value['value'];
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
     * @param array $extra_attributes [optional] <p>
     * set extra attributes to the parameter. Must be an associative array.
     * </p>
     * @param bool $is_merging [optional] <p>
     * A parameter to specify whether merging set
     * </p>
     * @return void
     */
    public function set(string $name, mixed $value, bool $readonly = false, array $extra_attributes = []): void
    {
        $parameter = $this->get($name, true); // value
        $this->validateParameter($name, $parameter);       
    
        if (!empty($extra_attributes) && !array_is_assoc($extra_attributes)) {
            throw new ArrayNotAssocException('Please provide an associative array for extra_attributes for parameter: ' . $name);
        }
        
        $this->config[$name] = [
            'value' => $value,
            'readonly' => $readonly,
            ...$extra_attributes
        ];

    }

    protected function validateParameter($parameter_name, $parameter)
    {
        $mustbe_attributes = ["readonly" => false];
        if ($parameter !== null) {
            foreach($mustbe_attributes as $attr_name => $attr_value) {
                $a =  $parameter[$attr_name] === $attr_value;
                if (isset($parameter[$attr_name]) && !($parameter[$attr_name] === $attr_value)) {
                    throw new ParameterValidationException($parameter_name, $attr_name, $attr_value, $parameter[$attr_name]);
                }
            }
            
        }
    }

    /**
     * Reset configuration to default values
     */
    public function reset()
    {

    }
}