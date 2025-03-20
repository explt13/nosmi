<?php
namespace Explt13\Nosmi;

use Explt13\Nosmi\interfaces\ContainerInterface;

class Container implements ContainerInterface
{
    use SingletonTrait;

    protected array $bindings = array();
    protected array $services = array();

    /** 
    * @param string $abstract Interface Name
    * @param callable $callback, fn(ContainerInterface $container) => $container->autowire(Concrete::class);
    */
    public function set(string $abstract, callable $callback): void
    {
        if (!interface_exists($abstract) && !class_exists($abstract)) {
            throw new \Exception("Cannot bind non-existent interface or class: $abstract");
        }
        $this->bindings[$abstract] = $callback;
    }

    public function remove(string $abstract): void
    {
        if (!isset($this->bindings[$abstract]) && !isset($this->services[$abstract])) {
            throw new \Exception('Cannot unset unpresented service');
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
    public function get(string $abstract, bool $getCached, bool $cacheService = true): object
    {
        if ($getCached && isset($this->services[$abstract])) {
            return $this->services[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            throw new \Exception("No binding found for $abstract");
        }

        $concrete = $this->bindings[$abstract]($this);
        if ($cacheService) {
            $this->cacheService($abstract, $concrete);
        }
        return $concrete;
    }

    protected function cacheService($abstract, $concrete)
    {
        $this->services[$abstract] = $concrete;
    }
    
    protected function autowire(string $service, $cacheDependency): object
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
                throw new \Exception("Unable to resolve argument '{$arg->getName()}' for service '$service'");
            }
            if (!class_exists($argType->getName()) && !interface_exists($argType->getName())) {
                throw new \Exception("Parameter '{$arg->getName()}' is not a class or interface");
            }
            $dependencies[$arg->getName()] = $this->get($argType->getName(), $cacheDependency);
        }
        return new $service(...$dependencies);
    }
}