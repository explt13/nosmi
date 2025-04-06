<?php
namespace Explt13\Nosmi\Dependencies;

use Explt13\Nosmi\Exceptions\ClassNotFoundException;
use Explt13\Nosmi\Exceptions\DependencyNotSetException;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use Explt13\Nosmi\Interfaces\SingletonInterface;
use Explt13\Nosmi\SingletonTrait;

class Container implements ContainerInterface, SingletonInterface
{
    use SingletonTrait;

    protected array $bindings = array();
    protected array $services = array();

   
    public function set(string $abstract, string $dependency, bool $singleton = false): void
    {
        if (!interface_exists($abstract) && !class_exists($abstract)) {
            throw new ClassNotFoundException($abstract);
        }
        if (!interface_exists($dependency) && !class_exists($dependency)) {
            throw new ClassNotFoundException($dependency);
        }

        $this->bindings[$abstract] = [
            "dependency" => $dependency,
            "singleton" => $singleton,
        ];
    }

    public function remove(string $abstract): void
    {
        if (!isset($this->bindings[$abstract]) && !isset($this->services[$abstract])) {
            throw DependencyNotSetException::withMessage('Cannot unset non-existent dependency ' . $abstract);
        }
        if (isset($this->bindings[$abstract])) {
            unset($this->bindings[$abstract]);
        }
        if (isset($this->services[$abstract])) {
            unset($this->services[$abstract]);
        }
    }

    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]);
    }

    /**
     * @template T
     * @param class-string<T> $abstract
     * @return T
     */
    public function get(string $abstract, bool $getNew = false): object
    {
        if (!$getNew && isset($this->services[$abstract])) {
            return $this->services[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            throw new DependencyNotSetException($abstract);
        }

        $concrete = $this->autowire($this->bindings[$abstract]["dependency"]);
        if ($this->bindings[$abstract]["singleton"] ?? false) {
            $this->cacheService($abstract, $concrete);
        }
        return $concrete;
    }

    protected function cacheService(string $abstract, object $concrete)
    {
        $this->services[$abstract] = $concrete;
    }
    
    protected function autowire(string $service): object
    {
        
        $reflectorClass = new \ReflectionClass($service);
        $reflectorConstructor = $reflectorClass->getConstructor();

        if (is_null($reflectorConstructor)) {
            return new $service;
        }
        
        $constructorArgs = $reflectorConstructor->getParameters();
        if (empty($constructorArgs)){
            return new $service;
        }

        $dependencies = [];
        foreach ($constructorArgs as $arg) {
            $argType = $arg->getType();
            if ($argType === null) {
                throw new \Exception("Unable to resolve argument `{$arg->getName()}` for service `$service`", 1090);
            }
            $dependencies[$arg->getName()] = $this->get($argType->getName());
        }
        return new $service(...$dependencies);
    }
}