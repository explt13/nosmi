<?php

namespace Tests\Unit\helpers;

use Explt13\Nosmi\Dependencies\Container;
use Explt13\Nosmi\Dependencies\DependencyManager;
use stdClass;

class Reset
{
    public static function resetSingleton(string $singleton_class): void
    {
        $reflection = new \ReflectionClass($singleton_class);
        $instanceProperty = $reflection->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(null, null);
    }

    public static function resetStaticProp(string $class, string $prop): void
    {
        $defaults = [
            "string" => "",
            "int" => 0,
            "array" => [],
            "callable" => function(){},
            "float" => 0.0,
            "null" => null,
            "object" => fn() => new stdClass(),
            "false" => false,
            "true" => true,
            "bool" => false,
            "mixed" => "",
        ];

        $reflection = new \ReflectionClass($class);
        $instanceProperty = $reflection->getProperty($prop);
        $instanceProperty->setAccessible(true);
        $propType = $instanceProperty->getType();
        $propTypeName = $propType->getName();
        
        if ($propType->isBuiltin()) {
            $hasDefaultValue = $instanceProperty->hasDefaultValue();
            $value = $hasDefaultValue ? $instanceProperty->getDefaultValue() : ($defaults[$propTypeName] ?? null);
            if (!is_null($value) && $propTypeName === 'object' && is_callable($value)) {
                $value = $value();
            }
            $instanceProperty->setValue(null, $value);
            return;
        }

        if (interface_exists($propTypeName)) {
            $dm = new DependencyManager(Container::getInstance());
            $concrete = $dm->getDependency($propTypeName, true);
            $instanceProperty->setValue(null, new $concrete());
            return;
        }
        
        if (class_exists($propTypeName)) {
            $instanceProperty->setValue(null, new $propTypeName());
            return;
        }
    }

    public static function resetStaticClass(string $class)
    {
        $reflectionClass = new \ReflectionClass($class);
        $staticProps = $reflectionClass->getStaticProperties();
        $defaultProps = $reflectionClass->getDefaultProperties(); // includes static + instance defaults

        foreach ($staticProps as $name => $value) {
            $defaultValue = $defaultProps[$name] ?? null;
            $reflectionClass->setStaticPropertyValue($name, $defaultValue);
        }
    }
}