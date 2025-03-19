<?php
namespace Explt13\Nosmi;

use Explt13\Nosmi\interfaces\ContainerInterface;

class Container implements ContainerInterface
{
    use SingletonTrait;

    protected array $bindings = array();
    protected array $services = array();

    /** 
    * @param string $id Interface Name
    * @param callable $callback, fn(ContainerInterface $container) => $container->autowire(Concrete::class);
    */
    public function set(string $id, callable $callback): void
    {
        if (!interface_exists($id) && !class_exists($id)) {
            throw new \Exception("Cannot bind non-existent interface or class: $id");
        }
        $this->bindings[$id] = $callback;
    }

    public function remove(string $id): void
    {
        if (!isset($this->bindings[$id]) && !isset($this->services[$id])) {
            throw new \Exception('Cannot unset unpresented service');
        }
        if (isset($this->bindings[$id])) {
            unset($this->bindings[$id]);
        }
        if (isset($this->services[$id])) {
            unset($this->services[$id]);
        }
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
    public function get(string $id, bool $cacheDependency): object
    {
        if ($cacheDependency && isset($this->services[$id])) {
            return $this->services[$id];
        }
        
        if (isset($this->bindings[$id]) && is_callable($this->bindings[$id])) {
            $dependency = $this->bindings[$id]($this);
            if ($cacheDependency) {
                $this->services[$id] = $dependency;
            }
            return $dependency;
        }
        $dependency = $this->autowire($id, $cacheDependency);
        if ($cacheDependency) {
            $this->services[$id] = $dependency;
        }
        return $dependency;
    }
    
    protected function autowire(string $service, $cacheDependency): object
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
            $dependencies[$arg->getName()] = $this->get($argType->getName(), $cacheDependency);
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