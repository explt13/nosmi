<?php

namespace Explt13\Nosmi\AppConfig;

use Explt13\Nosmi\Exceptions\ArrayNotAssocException;
use Explt13\Nosmi\Exceptions\ConfigAttributeException;
use Explt13\Nosmi\Exceptions\SetReadonlyException;

class ConfigValidator implements ConfigValidatorInterface
{
    /**
     * Checks if a config parameter is readonly.
     * @param array $parameter a parameter retrieved from the config
     * @return bool
     */
    public function isReadonly(array $parameter): bool
    {
        if (isset($parameter['readonly']) && $parameter['readonly'] === true) {
            return true;
        }
        return false;
    }

    /**
     * Checks if a config parameter is readonly, throws exception on failure
     * @param string $parameter_name a paramter name
     * @param array $parameter a parameter retrieved from the config
     * @throws SetReadonlyException rejects a paramater to be set if check fails
     * @return void
     */
    public function checkReadonly(string $parameter_name, array $parameter): void
    {
        if (array_key_exists('readonly', $parameter) && $parameter['readonly'] === true) {
            throw new SetReadonlyException($parameter_name);
        }
    }

    /**
     * Checks if a parameter is complex
     * @param mixed $parameter parameter parameter
     * @return bool
     */
    public function isComplexParameter($parameter): bool
    {
        return !is_primitive($parameter) && !array_is_list($parameter);
    }

    /**
     * Validates a parameter's attributes
     * @param string $parameter_name a parameter name
     * @param array $attributes attributes to check
     * @throws ArrayNotAssocException
     * @return void
     */
    public function validateAttributes(string $parameter_name, array $attributes): void
    {
        if (!empty($attributes) && !array_is_assoc($attributes)) {
            throw new ArrayNotAssocException('Provide an associative array for extra_attributes for parameter: ' . $parameter_name);
        }
    }

    /**
     * Validates a config parameter has value
     * @param string $name parameter name
     * @param mixed $parameter parameter body
     * @throws ConfigAttributeException
     * @return bool
     */
    public function validateParameterHasValue(string $name, mixed $parameter): void
    {
        if (!array_key_exists('value', $parameter)) {
            throw new ConfigAttributeException("`value` attribute has not been provided for complex parameter: $name");
        }
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
