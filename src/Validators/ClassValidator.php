<?php

namespace Explt13\Nosmi\Validators;

class ClassValidator
{
    public function isClassOrInterfaceExists(string $classname): bool
    {
        return interface_exists($classname) || class_exists($classname);
    }

    public function isClassExists(string $classname): bool
    {
        return class_exists($classname);
    }

    public function isInterfaceExists(string $classname): bool
    {
        return interface_exists($classname);
    }
}