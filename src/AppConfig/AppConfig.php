<?php

namespace Explt13\Nosmi\AppConfig;

use Explt13\Nosmi\SingletonTrait;

class AppConfig implements ConfigInerface
{
    use SingletonTrait;

    /**
     * @var array $config an array with parameters 
     */
    protected array $config = [];

    /**
     * @var string PARAMETER_NOT_SET a marker for unset parameters
     */
    public const PARAMETER_NOT_SET = '__PARAMETER_NOT_SET__';

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
     * @return mixed returns self::PARAMETER_NOT_SET if a parameter is not present
     */
    public function get(string $name, bool $getWithAttributes=false): mixed
    {
        if ($this->has($name)) {
            $parameter = $this->config[$name];
            return $getWithAttributes ? $parameter : $parameter['value'];
        }
        return self::PARAMETER_NOT_SET;
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
        $parameter = $this->get($name, true);
        if ($parameter !== self::PARAMETER_NOT_SET) {
            $this->config_validator->checkReadonly($name, $parameter);
        }
        $this->config_validator->validateAttributes($name, $extra_attributes);

        $this->config[$name] = [
            'value' => $value,
            'readonly' => $readonly,
            ...$extra_attributes
        ];
    }

    /**
     * Set multiple config parameters at once
     * @param array $config_array a config array to set
     * @return void
     */
    public function bulkSet(array $config_array): void
    {
        foreach ($config_array as $name => $parameter) {
            if ($this->config_validator->isComplexParameter($parameter)) {
                $this->config_validator->validateParameterHasValue($name, $parameter);
                $this->set($name, $parameter['value'], false, $parameter);
                continue;
            }
            $this->set($name, $parameter);
        }
    }

    /**
     * Removes configuration parameter using a key # todo
     */
    public function remove(string $key): void
    {
        $parameter = $this->get($key);

    }
}