<?php

namespace Tests\Unit\helpers;

class SingletonReset
{
    public static function resetSingleton(string $singleton_class): void {
        $reflection = new \ReflectionClass($singleton_class);
        $instanceProperty = $reflection->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(null, null);
    }
}