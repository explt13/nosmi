<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Interfaces\LightMiddlewareInterface;
use Explt13\Nosmi\Traits\SingletonTrait;

class MiddlewareRegistry
{
    use SingletonTrait;
    private array $middlewares_list;
    
    public function add(string $middleware_class): void
    {
        $this->middlewares_list[] = $middleware_class;
    }

    public function remove(string $middleware_class): void
    {
        unset($this->middlewares_list[array_search($middleware_class, $this->middlewares_list)]);
        $this->middlewares_list = array_values(($this->middlewares_list));
    }

    public function addBulk(array $middleware) {
        foreach ($middleware as $middl) {
            $this->add($middl);
        }
    }

    public function getAll(): array
    {
        return $this->middlewares_list;
    }
}