<?php

namespace Explt13\Nosmi\Validators;

class ContainerValidator
{
    public static function isDependencyInBindings(array $bindings, string $abstract): bool
    {
        return isset($bindings[$abstract]);
    }

    public static function isDependencyInServices(array $services, string $abstract): bool
    {
        return isset($services[$abstract]);
    }
}