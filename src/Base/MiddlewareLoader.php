<?php

namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Traits\SingletonTrait;

class MiddlewareLoader
{
    use SingletonTrait;
    private array $middlewares;
    
    public function add(callable $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function run()
    {

    }
}