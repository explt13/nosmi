<?php

namespace Explt13\Nosmi\Routing;

class RouteContext
{
    private array $route = [];
    private array $readOnlyProperties = [];
    private bool $canOverwrite = false;

    public function setRoute(array $route): void
    {
        $route = $this->prepareRoute($route);
        foreach ($route as $key => $value) {
            $this->route[$key] = $value;
            $this->readOnlyProperties[] = $key;
        }
    }

    private function prepareRoute(array $route): array
    {
        if (!isset($route['layout'])) {
            $route['layout'] = LAYOUT;
        }
        if (empty($route['action'])) {
            $route['action'] = 'index';
        }
        $route['controller'] = $this->upperCamelCase($route['controller']);
        return $route;
    }

    private function upperCamelCase(string $str): string
    {
        return str_replace('-', '', ucwords($str, '-'));
    }


    public function __get(string $name): mixed
    {
        return $this->route[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        if (in_array($name, $this->readOnlyProperties)) {
            throw new \Exception("Cannot set read-only property: $name");
        }
        if (!$this->canOverwrite && isset($this->route[$name])){
            throw new \Exception("Cannot overwrite existing property: $name");
        }
        $this->route[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->route[$name]);
    }

    public function toArray(): array
    {
        return $this->route;
    }
}