<?php
namespace Explt13\Nosmi\AppConfig;

use Explt13\Nosmi\Exceptions\ConfigAttributeException;
use Explt13\Nosmi\Exceptions\SetReadonlyException;

class ConfigValidator implements ConfigValidatorInterface
{
    /**
     * Check if a config parameter is readonly.
     * @param string $parameter_name a name of a parameter to be set
     * @param array $parameter a parameter retrieved from the config
     * @throws SetReadonlyException rejects a paramater to be set if check fails
     * @return bool
     */
    public function readonlyCheck(string $parameter_name, array $parameter): bool
    {
        if (isset($parameter['readonly']) && $parameter['readonly'] === true) {
            throw new SetReadonlyException($parameter_name);
        }
        return true;
    }

    /**
     * Checks if a parameter is complex
     * @param mixed $value parameter value
     * @return bool
     */
    public function isComplexParameter($value): bool
    {
        return !is_primitive($value) && !array_is_list($value);
    }
    
    /**
     * Validates a config parameter
     * @param string $name parameter name
     * @param mixed $value parameter value
     * @return bool
     * @throws ConfigAttributeException
     */
    public function isValidConfigComplexParameter(string $name, mixed $value): bool
    {
        if (!isset($value['value'])) {
            throw new ConfigAttributeException("`value` attribute has not been provided for complex parameter: $name");
        }
        if (!array_is_assoc($value)) {
            throw new ConfigAttributeException("Please provide an associative array for attributes for parameter: $name");
        }
        return true;
    }

    // #todo
    public function validateParameter(mixed $parameter_to_set, array $parameter_from_config): bool
    {
        $declared_constraints = [
            "integer" => [
                "more",
                "less",
                "equals",
                "only_positive",
                "only_negative",
                "non_zero"
            ],
            "string" => [
                "maxlen",
                "minlen",
                "not_empty",
            ],
            "array" => [
                "only_int",
                "only_string",
                "only_bool"
            ]
        ];

        // if not constraints key presents
        if (!isset($parameter_from_config['constraints']) || empty($parameter_from_config['constraints'])) return true;
        
        $constraints = $parameter_from_config['constraints'];
        
        $type = gettype($parameter_to_set);
        $target_type = $constraints['type'];

        if ($type !== $target_type) return false;

        $validated = true;

        foreach ($constraints as $constraint => $expected) {
            if (!$validated) return false;
            
            if ($constraint === "more") {
                $validated = $parameter_to_set > $expected;
            }
        }
        return true;
    }
}