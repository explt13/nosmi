<?php

namespace Explt13\Nosmi\interfaces;

interface ContainerInterface
{
    public function init(array $dependencies);
    public function set(string $id, callable $callback): bool;
    public function has(string $id): bool;
    public function get(string $id): object;
    public function autowire(string $service): object;
    public function remove(string $id): bool;
}