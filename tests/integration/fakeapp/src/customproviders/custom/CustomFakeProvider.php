<?php

namespace Tests\Integration\fakeapp\src\customproviders\custom;

use Explt13\Nosmi\Interfaces\ServiceProviderInterface;

class CustomFakeProvider implements ServiceProviderInterface
{
    public function boot(): void
    {
        echo self::class . " custom pathed provider has been booted";
    }
}