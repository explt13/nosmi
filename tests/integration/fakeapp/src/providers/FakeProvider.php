<?php

namespace Tests\Integration\fakeapp\src\providers;

use Explt13\Nosmi\Interfaces\ServiceProviderInterface;

class FakeProvider implements ServiceProviderInterface
{
    public function boot(): void
    {
        echo self::class . ' has been booted';
    }
}