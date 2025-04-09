<?php

namespace Explt13\Nosmi\AppConfig;

use Explt13\Nosmi\Exceptions\ArrayNotAssocException;
use Explt13\Nosmi\Exceptions\MissingAssocArrayKeyException;
use Explt13\Nosmi\Exceptions\SetReadonlyException;
use Explt13\Nosmi\Interfaces\ConfigValidatorInterface;

class ConfigValidator implements ConfigValidatorInterface
{
    
    public function isReadonly(array $parameter): bool
    {
        if (isset($parameter['readonly']) && $parameter['readonly'] === true) {
            return true;
        }
        return false;
    }

    public function checkReadonly(string $parameter_name, array $parameter): void
    {
        if (array_key_exists('readonly', $parameter) && $parameter['readonly'] === true) {
            throw new SetReadonlyException($parameter_name);
        }
    }

    public function isComplexParameter($parameter): bool
    {
        return !is_primitive($parameter) && !array_is_list($parameter);
    }

    public function validateAttributes(string $parameter_name, array $attributes): void
    {
        if (!empty($attributes) && !array_is_assoc($attributes)) {
            throw new ArrayNotAssocException('Provide an associative array for extra_attributes for parameter: ' . $parameter_name);
        }
    }

    public function validateParameterHasRequiredAttribute(string $parameter_name, array $parameter, string $required): void
    {
        if (!array_key_exists($required, $parameter)) {
            throw new MissingAssocArrayKeyException($parameter_name, $required);
        }
    }

    public function isRemovable(array $parameter): bool
    {
        return (!isset($parameter['removable']) || $parameter['removable'] === true);
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
