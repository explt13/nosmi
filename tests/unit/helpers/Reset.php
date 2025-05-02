<?php

namespace Tests\Unit\helpers;

use Explt13\Nosmi\Dependencies\Container;
use Explt13\Nosmi\Dependencies\DependencyManager;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use stdClass;

class Reset
{
    private static bool $deps_loaded = false;
    public static function resetSingleton(string $singleton_class): void
    {
        $reflection = new \ReflectionClass($singleton_class);
        $instanceProperty = $reflection->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(null, null);
    }

    public static function resetStaticProp(string $class, string $prop): void
    {
        self::setDeps();
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
            $dm = new DependencyManager();
            $concrete = $dm->getDependency($propTypeName, true);
            $instanceProperty->setValue(null, $concrete);
            return;
        }
        
        if (class_exists($propTypeName)) {
            $instanceProperty->setValue(null, $propTypeName);
            return;
        }
    }

    public static function resetStaticClass(string $class)
    {
        self::setDeps();
        $reflectionClass = new \ReflectionClass($class);
        $staticProps = $reflectionClass->getStaticProperties();
        $defaultProps = $reflectionClass->getDefaultProperties(); // includes static + instance defaults

        foreach ($staticProps as $name => $value) {
            $prop = $reflectionClass->getProperty($name);
            $propTypeName = $prop->getType()->getName();
            if (interface_exists($propTypeName)) {
                $dm = new DependencyManager();
                $concrete = $dm->getDependency($propTypeName, true);
                $reflectionClass->setStaticPropertyValue($name, $concrete);
                return;
            }
            
            if (class_exists($propTypeName)) {
                $reflectionClass->setStaticPropertyValue($name, $propTypeName);
                return;
            }
            $defaultValue = $defaultProps[$name] ?? null;
            $reflectionClass->setStaticPropertyValue($name, $defaultValue);
        }
    }

    private static function setDeps()
    {
        if (self::$deps_loaded === false) {
            $dependency_manager = new DependencyManager();
            if ($dependency_manager->hasDependency(ConfigInterface::class) === false) {
                $dependency_manager->loadFrameworkDependencies(dirname(__DIR__, 3) . '/src/Dependencies/dependencies.php');
            }
            self::$deps_loaded = true;            
        }
    }
}