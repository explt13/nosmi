<?php

namespace Explt13\Nosmi\AppConfig;

use Explt13\Nosmi\Exceptions\RemoveConfigParameterException;
use Explt13\Nosmi\Interfaces\SingletonInterface;
use Explt13\Nosmi\SingletonTrait;

class AppConfig implements ConfigInterface, SingletonInterface
{
    use SingletonTrait;

    /**
     * @var array $config an array with parameters 
     */
    protected array $config = [];

    public const PARAMETER_NOT_SET = '__PARAMETER_NOT_SET__';

    /**
     * @var ConfigValidatorInterface a config validator
     */
    protected ConfigValidatorInterface $config_validator;

    private function __construct()
    {
        $this->config_validator = new ConfigValidator();
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->config);
    }

    public function get(string $name, bool $getWithAttributes=false): mixed
    {
        if ($this->has($name)) {
            $parameter = $this->config[$name];
            return $getWithAttributes ? $parameter : $parameter['value'];
        }
        return self::PARAMETER_NOT_SET;
    }

    public function getAll(): array
    {
        return $this->config;
    }

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

    public function remove(string $name): bool
    {
        if ($this->has($name)) {
            $parameter = $this->get($name, true);
            if (!$this->config_validator->isRemovable($parameter)) {
                throw new RemoveConfigParameterException($name, 'removable parameter');
            }
            unset($this->config[$name]);
            return true;
        }
        return false;
    }
}