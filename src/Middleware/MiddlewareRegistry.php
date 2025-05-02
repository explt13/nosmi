<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\MiddlewareRegistryInterface;
use Explt13\Nosmi\Traits\SingletonTrait;

class MiddlewareRegistry implements MiddlewareRegistryInterface
{
    use SingletonTrait;
    private array $middleware_list = [];

    public function add(string $middleware_class, ?string $route = null): void
    {
        if ($route) {
            $this->middleware_list[$route][] = $middleware_class;
            return;
        }
        $this->middleware_list[] = $middleware_class;
    }

    public function remove(string $middleware_class): void
    {
        unset($this->middleware_list[array_search($middleware_class, $this->middleware_list)]);
    }

    public function addBulk(array $middleware) {
        foreach ($middleware as $middl) {
            $this->add($middl);
        }
    }

    public function getForRoute(string $route): array
    {
        $route_middleware = [];
        foreach($this->middleware_list as $pattern => $middleware) {
            if (is_int($pattern)) {
                $route_middleware[] = $middleware;
                continue;
            }
            if (preg_match("#$pattern#", $route)) {
                $route_middleware = array_merge($route_middleware, $middleware);
            }
        }
        return $route_middleware;
    }

    public function getCommon(): array
    {
        return array_filter($this->middleware_list, fn($key) => is_int($key), ARRAY_FILTER_USE_KEY);
    }

    public function getAll(): array
    {
        return $this->middleware_list;
    }
}