<?php

namespace Explt13\Nosmi\AppConfig;

interface ConfigInerface
{
    public function has(string $name): bool;
    public function get(string $name, bool $getWithAttributes=false): mixed;
    public function getAll(): array;
    public function set(string $name, mixed $value, bool $readonly = false, array $extra_attributes = []): void;
    public function bulkSet(array $config_array): void;
    public function remove(string $key): void;
}