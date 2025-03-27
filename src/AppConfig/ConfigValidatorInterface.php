<?php

namespace Explt13\Nosmi\AppConfig;

interface ConfigValidatorInterface
{
    public function isReadonly(array $parameter): bool;
    public function checkReadonly(string $parameter_name, array $parameter): void;
    public function isComplexParameter($value): bool;
    public function validateAttributes(string $parameter_name, array $attributes): void;
    public function validateParameterHasValue(string $name, mixed $value): void;
    public function validateParameter(mixed $parameter_to_set, array $parameter_from_config): bool;
}