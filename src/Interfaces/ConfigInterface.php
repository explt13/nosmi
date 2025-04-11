<?php

namespace Explt13\Nosmi\Interfaces;

interface ConfigInterface
{


    /**
     * Check whether a config parameter exists
     * @param string $name parameter name to check
     * @return bool return true if exists else false
     */
    public function has(string $name): bool;

    /**
     * Get a configuration parameter
     * @param string $name retrieve a configuration parameter by its name
     * @param bool $getWithAttributes [optional] <p>
     * get a parameter with associated attributes if they present \
     * ["param" => ["value" => value , ...attributes], ...]
     * </p>
     * @return mixed returns null if a parameter is not present
     */
    public function get(string $name, bool $getWithAttributes=false): mixed;

    /**
     * Get all configuration parameters
     * @return array array of parameters
     */
    public function getAll(): array;

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
    public function set(string $name, mixed $value, bool $readonly = false, array $extra_attributes = []): void;
    
    /**
     * Set multiple config parameters at once
     * @param array $config_array a config array to set
     * @return void
     */
    public function bulkSet(array $config_array): void;

    /**
     * Removes configuration parameter with specified name
     * @param string $name a configuration parameter's name to remove
     * @return bool returns __false__ if the parameter is not found. If the parameter is found and __successfully__ removed, returns __true__
     * @throws RemoveConfigParameterException if parameter has been found but cannot be removed due some reason (e.g a not removable parameter)
     */
    public function remove(string $key): bool;
}