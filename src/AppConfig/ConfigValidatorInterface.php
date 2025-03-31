<?php

namespace Explt13\Nosmi\AppConfig;

interface ConfigValidatorInterface
{
    /**
     * Checks if a config parameter is readonly.
     * @param array $parameter a parameter retrieved from the config
     * @return bool
     */
    public function isReadonly(array $parameter): bool;

    /**
     * Checks if a config parameter is readonly, throws exception on failure
     * @param string $parameter_name a paramter name
     * @param array $parameter a parameter retrieved from the config
     * @throws SetReadonlyException rejects a paramater to be set if check fails
     * @return void
     */
    public function checkReadonly(string $parameter_name, array $parameter): void;

    /**
     * Checks if a parameter is complex
     * @param mixed $parameter parameter parameter
     * @return bool
     */
    public function isComplexParameter($value): bool;
   
    /**
     * Validates a parameter's attributes
     * @param string $parameter_name a parameter name
     * @param array $attributes attributes to check
     * @throws ArrayNotAssocException
     * @return void
     */
    public function validateAttributes(string $parameter_name, array $attributes): void;
    
    /**
     * Validates a config parameter has value
     * @param string $name parameter name
     * @param mixed $parameter parameter body
     * @throws ConfigAttributeException
     * @return bool
     */
    public function validateParameterHasValue(string $name, mixed $value): void;
    
    /**
     * Checks whether a parameter is removable
     * @param array $parameter a config parameter
     * @return bool returns true if parameter is removable, false otherwise
     */
    public function isRemovable(array $parameter): bool;
}