<?php

namespace Explt13\Nosmi\AppConfig;

interface ConfigValidatorInterface
{
    public function readonlyCheck(string $parameter_name, array $parameter): bool;
    public function isComplexParameter($value): bool;
    public function isValidConfigComplexParameter(string $name, mixed $value): bool;
    public function validateParameter(mixed $parameter_to_set, array $parameter_from_config): bool;

}