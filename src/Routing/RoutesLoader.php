<?php

namespace Explt13\Nosmi\Routing;

class RoutesLoader
{
    public static function load(string $path): void
    {
        require $path;
    }
}