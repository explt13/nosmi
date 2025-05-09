<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\MiddlewareRegistryInterface;
use Explt13\Nosmi\Traits\SingletonTrait;
use Explt13\Nosmi\Utils\Utils;
use Psr\Http\Server\MiddlewareInterface;

class MiddlewareRegistry implements MiddlewareRegistryInterface
{
    use SingletonTrait;
    private array $middleware_list = [];
    private array $middleware_ignore = [];

    protected function __construct()
    {
        $this->middleware_list[ErrorHandlerMiddleware::class] = new ErrorHandlerMiddleware;
    }

    public function add(MiddlewareInterface $middleware, ?string $route_pattern = null): void
    {
        if ($route_pattern) {
            $this->middleware_list[$route_pattern][$middleware::class] = $middleware;
            return;
        }
        $this->middleware_list[$middleware::class] = $middleware;
    }

    public function remove(string $middleware_class, ?string $route_pattern = null): void
    {
        // if used via Route class Route::disableMiddleware
        if ($route_pattern) {
            $this->middleware_ignore[$route_pattern][] = $middleware_class;
            return;
        }
        $this->middleware_ignore[] = $middleware_class;
    }

    public function getForRoute(string $route): array
    {
        $route_middleware = [];
        $ignore_middleware = array_filter($this->middleware_ignore, fn($pat) => (is_int($pat) || preg_match("#$pat#", $route)), ARRAY_FILTER_USE_KEY);
        $ignore_middleware = Utils::flattenArray($ignore_middleware);
        foreach($this->middleware_list as $pattern => $middleware) {

            if (!is_array($middleware) && !in_array($middleware::class, $ignore_middleware)) {
                $route_middleware[$middleware::class] = $middleware;
                continue;
            }

            $pattern = str_replace('\\', '\\\\', $pattern);
            if (preg_match("#$pattern#", $route)) {
                $middleware = array_filter($middleware, fn($md_class) => !in_array($md_class, $ignore_middleware), ARRAY_FILTER_USE_KEY);
                $route_middleware = array_merge($route_middleware, $middleware);
            }
        }
        return $route_middleware;
    }


    // public function getForRoute(string $route): array
    // {
    //     $route_middleware = [];
    //     foreach($this->middleware_list as $pattern => $middleware) {
    //         if (preg_match("#$pattern#", $route)) {
    //             $route_middleware = array_merge($route_middleware, $middleware);
    //             continue;
    //         }
    //         if (!is_array($middleware) && !in_array($middleware, $this->middleware_ignore)) {
    //             $route_middleware[] = $middleware;
    //         }
    //     }
    //     return $route_middleware;
    // }

    // public function add(MiddlewareInterface $middleware, ?string $route = null): void
    // {
    //     if ($route) {
    //         $this->middleware_list[$route][$middleware::class] = $middleware;
    //         return;
    //     }
    //     $this->middleware_list[$middleware::class] = $middleware;
    // }

    // public function remove(string $middleware_class, ?string $route = null): void
    // {
    //     // if used via Route class Route::disableMiddleware
    //     if ($route) {
    //         if (isset($this->middleware_list[$route][$middleware_class])) {
    //             unset($this->middleware_list[$route][$middleware_class]);
    //         }
    //     }
    //     $this->middleware_ignore[] = $middleware_class;
    // }

    // public function getForRoute(string $route): array
    // {
    //     $route_middleware = [];
    //     foreach($this->middleware_list as $pattern => $middleware) {
    //         if (preg_match("#$pattern#", $route)) {
    //             $route_middleware = array_merge($route_middleware, $middleware);
    //         }
    //         if (!is_array($middleware) && !in_array($middleware, $this->middleware_ignore)) {
    //             $route_middleware[$middleware::class] = $middleware;
    //         }
    //     }
    //     return $route_middleware;
    // }


    public function getCommon(): array
    {
        return array_filter($this->middleware_list, fn($val) => !is_array($val));
    }

    public function getAll(): array
    {
        return $this->middleware_list;
    }
}