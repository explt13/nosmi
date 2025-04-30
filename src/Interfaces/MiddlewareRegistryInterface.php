<?php

namespace Explt13\Nosmi\Interfaces;

interface MiddlewareRegistryInterface
{
    /**
     * Adds a middleware class to the registry.
     *
     * @param string $middleware_class The fully qualified class name of the middleware to add.
     * @return void
     */
    public function add(string $middleware_class): void;

    /**
     * Removes a middleware class from the registry.
     *
     * @param string $middleware_class The fully qualified class name of the middleware to remove.
     * @return void
     */
    public function remove(string $middleware_class): void;
     
    /**
     * Adds multiple middleware classes to the registry in bulk.
     *
     * @param array $middleware An array of fully qualified class names of the middleware to add.
     * @return void
     */
    public function addBulk(array $middleware);
     
    /**
     * Retrieves all middleware classes from the registry.
     *
     * @return array An array of fully qualified class names of the registered middleware.
     */
    public function getAll(): array;
}