<?php

namespace Explt13\Nosmi\Dependencies;

class ContainerValidator
{
    public function isDependencyInBindings(array $bindings, string $abstract): bool
    {
        return isset($bindings[$abstract]);
    }

    public function isDependencyInServices(array $services, string $abstract): bool
    {
        return isset($services[$abstract]);
    }
}