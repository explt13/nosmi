<?php

namespace Tests\Integration\ServiceProvider\fakeapp\src\something\providers;


class InvalidProvider
{
    public function boot()
    {
        echo "will never reach";
    }
}