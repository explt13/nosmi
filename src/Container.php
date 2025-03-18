<?php
namespace Explt13\Nosmi;

use Explt13\Nosmi\interfaces\ContainerInterface;

class Container implements ContainerInterface
{
    use SingletonTrait;

    protected array $bindings = array();
    protected array $services = array();

    public function init(array $dependencies)
    {
        $this->bindings = $dependencies;
    }

    /** 
    * @param string $id Interface Name
    * @param callable $callback, fn(ContainerInterface $container) => new Concrete;
    */
    public function set(string $id, callable $callback): bool
    {
        if (!interface_exists($id) && !class_exists($id)) {
            throw new \Exception("Cannot bind non-existent interface or class: $id");
        }
        $this->bindings[$id] = $callback;
        return true;
    }

    public function remove(string $id): bool
    {
        $deleted = false;
        if (isset($this->bindings[$id])) {
            unset($this->bindings[$id]);
            $deleted = true;
        }
        if (isset($this->services[$id])) {
            unset($this->services[$id]);
            $deleted = true;
        }
        return $deleted;
    }

    public function has(string $id): bool
    {
        return isset($this->bindings[$id]);
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @return T
     */
    public function get(string $id): object
    {
        if (isset($this->services[$id])) {
            return $this->services[$id];
        }
        
        if (isset($this->bindings[$id]) && is_callable($this->bindings[$id])) {
            $this->services[$id] = $this->bindings[$id]($this);
            return $this->services[$id];
        }
        $this->services[$id] = $this->autowire($id);
        return $this->services[$id];
    }
    
    public function autowire(string $service): object
    {
        
        $reflectorClass = $this->getReflectorClass($service);
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
            $dependencies[$arg->getName()] = $this->get($argType->getName());
        }
        return new $service(...$dependencies);
    }

    protected function getReflectorClass(string &$service): \ReflectionClass
    {
        $reflectorClass = new \ReflectionClass($service);
        
        if ($reflectorClass->isInterface()) {
            $service = preg_replace('/(interfaces\\\\|Interface)/', '', $service);
            if (!class_exists($service)) {
                throw new \Exception("Class $service not found");
            }
            $reflectorClass = new \ReflectionClass($service);
        }

        if ($reflectorClass->isAbstract()) {
            throw new \Exception("Cannot instantiate abstract class: $service");
        }

        return $reflectorClass;
    }
}