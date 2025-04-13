<?php

namespace Tests\Integration\ServiceProvider\fakeapp\src\something\custom;

use Explt13\Nosmi\Interfaces\ServiceProviderInterface;

class CustomFakeProvider implements ServiceProviderInterface
{
    public function boot(): void
    {
        echo self::class . " custom pathed provider has been booted";
    }
}