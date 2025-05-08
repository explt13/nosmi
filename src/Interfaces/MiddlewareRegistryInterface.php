<?php

namespace Explt13\Nosmi\Interfaces;

use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareRegistryInterface
{
    /**
     * Adds a middleware class to the registry.
     *
     * @param MiddlewareInterface $middleware the middleware to add.
     * @param string|null $route_pattern add middleware for __specific__ route
     * @return void
     */
    public function add(MiddlewareInterface $middleware, ?string $route_pattern = null): void;

    /**
     * Removes a middleware class from the registry.
     *
     * @param string $middleware_class The fully qualified class name of the middleware to remove.
     * @param string|null $route_pattern remove middleware for __specific__ route
     * @return void
     */
    public function remove(string $middleware_class, ?string $route_pattern = null): void;
     
    /**
     * Retrieves all middleware classes from the registry.
     *
     * @return array An array of fully qualified class names of the registered middleware.
     */
    public function getAll(): array;
    
    /**
     * Retrieves the middleware associated with a specific route.
     *
     * @param string $route The route for which to retrieve middleware.
     * @return array An array of middleware associated with the given route.
     */
    public function getForRoute(string $route): array;
     
    /**
     * Retrieves the common middleware that applies to all routes.
     *
     * @return array An array of common middleware.
     */

    public function getCommon(): array;
}