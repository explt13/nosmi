<?php

namespace Explt13\Nosmi\AppConfig;

interface ConfigValidatorInterface
{
    /**
     * Checks if a config parameter is readonly.
     * @param array $parameter the parameter retrieved from the config
     * @return bool
     */
    public function isReadonly(array $parameter): bool;

    /**
     * Checks if a config parameter is readonly, throws exception on failure
     * @param string $parameter_name the name of the parameter
     * @param array $parameter the parameter retrieved from the config
     * @throws SetReadonlyException rejects a paramater to be set if check fails
     * @return void
     */
    public function checkReadonly(string $parameter_name, array $parameter): void;

    /**
     * Checks if a parameter is a complex parameter
     * @param mixed $parameter the parameter retrieved from the config
     * @return bool
     */
    public function isComplexParameter($parameter): bool;
   
    /**
     * Validates a parameter's attributes
     * @param string $parameter_name the name of the parameter
     * @param array $attributes the attributes to validate
     * @throws ArrayNotAssocException if the attributes are not an associative array
     * @return void
     */
    public function validateAttributes(string $parameter_name, array $attributes): void;
    
    /**
     * Validates if a config parameter has value
     * @param string $name the name of the parameter
     * @param mixed $parameter the parameter retrieved from the config
     * @throws ConfigAttributeException
     * @return bool
     */
    public function validateParameterHasValue(string $parameter_name, mixed $parameter): void;
    
    /**
     * Checks whether a parameter is removable
     * @param array $parameter the parameter retrieved from the config
     * @return bool returns true if parameter is removable, false otherwise
     */
    public function isRemovable(array $parameter): bool;
}