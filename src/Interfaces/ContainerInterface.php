<?php

namespace Explt13\Nosmi\Interfaces;

interface ContainerInterface
{
    /**
     * Sets the dependency to the bindings
     * @param string $abstract an interface or class name ::class
     * @param string $concrete a class name ::class
     * @param bool $singleton if set to true the dependency will be cached
     * @return void
     */
    public function set(string $abstract, string $concrete, bool $singleton = false): void;
    public function has(string $abstract): bool;
    public function get(string $abstract, bool $getNew = false): object;
    public function remove(string $abstract): void;
}