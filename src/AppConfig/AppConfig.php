<?php

namespace Explt13\Nosmi\AppConfig;

use Explt13\Nosmi\AppConfig\ConfigValidator;
use Explt13\Nosmi\Exceptions\ArrayNotAssocException;
use Explt13\Nosmi\Exceptions\ConfigAttributeException;
use Explt13\Nosmi\SingletonTrait;

class AppConfig implements ConfigInerface
{
    use SingletonTrait;

    /**
     * @var array $config an array with parameters 
     */
    protected array $config = [];

    /**
     * @var ConfigValidatorInterface a config validator
     */
    protected ConfigValidatorInterface $config_validator;

    private function __construct(ConfigValidatorInterface $config_validator)
    {
        $this->config_validator = $config_validator;
    }

    /**
     * Check whether a config parameter exists
     * @param string $name parameter name to check
     * @return bool return true if exists else false
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->config);
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
     * @return void
     */
    public function set(string $name, mixed $value, bool $readonly = false, array $extra_attributes = []): void
    {
        $parameter = $this->get($name, true); // value
        if ($parameter !== null) {
            $this->config_validator->readonlyCheck($name, $parameter);
        }
    
        if (!empty($extra_attributes) && !array_is_assoc($extra_attributes)) {
            throw new ArrayNotAssocException('Provide an associative array for extra_attributes for parameter: ' . $name);
        }
        
        $this->config[$name] = [
            'value' => $value,
            'readonly' => $readonly,
            ...$extra_attributes
        ];

    }

    /**
     * Bulk set config parameters
     * @param array $config_array a config array to set
     * @return void
     * @internal
     */
    public function bulkSet(array $config_array): void
    {
        foreach ($config_array as $name => $value) {
            if ($this->config_validator->isComplexParameter($value) && $this->config_validator->isValidConfigComplexParameter($name, $value)) {
                $this->set($name, $value['value'], false, $value);
                continue;
            }
            $this->set($name, $value);
        }
    }

    /**
     * Removes configuration parameter using a key # todo
     */
    public function remove(string $key): void
    {

    }

     /**
     * Reset configuration to default values # todo
     */
    public function reset()
    {

    }
}