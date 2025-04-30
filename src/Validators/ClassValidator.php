<?php

namespace Explt13\Nosmi\Validators;

class ClassValidator
{
    public static function isClassOrInterfaceExists(string $classname): bool
    {
        return interface_exists($classname) || class_exists($classname);
    }

    public static function isClassExists(string $classname): bool
    {
        return class_exists($classname);
    }

    public static function isInterfaceExists(string $classname): bool
    {
        return interface_exists($classname);
    }
}