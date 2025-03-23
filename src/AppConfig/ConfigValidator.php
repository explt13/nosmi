<?php
namespace Explt13\Nosmi\AppConfig;

use Explt13\Nosmi\Exceptions\SetReadonlyException;

class ConfigValidator
{
    /**
     * Check if a config parameter is readonly.
     * @param string $parameter_name a name of a parameter to be set
     * @param array $parameter a parameter retrieved from the config
     * @throws SetReadonlyException rejects a paramater to be set if check fails
     * @return void
     */
    public static function readonlyCheck(string $parameter_name, array $parameter)
    {
        if (isset($parameter['readonly']) && $parameter['readonly'] === true) {
            throw new SetReadonlyException($parameter_name);
        }
    }


    public static function validateParameter(mixed $parameter_to_set, array $parameter_from_config): bool
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