<?php

namespace Explt13\Nosmi\interfaces;

interface ContainerInterface
{
    public function set(string $id, callable $callback): void;
    public function has(string $id): bool;
    public function get(string $id, bool $cacheDependency): object;
    public function remove(string $id): void;
}