<?php

namespace Tests\Integration\fakeapp\src\customproviders\providers;


class InvalidProvider
{
    public function boot()
    {
        echo "will never reach";
    }
}